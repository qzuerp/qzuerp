<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{
    public function poll(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $user = Auth::user();
        $firma = trim($user->firma) . '.dbo.';
        $lastId = (int) $request->query('lastId', 0);
        $EVRAKNO =  $request->query('EVRAKNO', 0);
        $EVRAKTYPE =  $request->query('EVRAKTYPE', 0);
    
        $notifications = DB::table($firma . 'notifications')
            ->select('*')
            ->where('target_user_id', $user->id)
            ->where('read', 0)
            ->where('id', '>', $lastId)
            ->orderBy('id', 'desc')
                    ->limit(10)
            ->get();
    
        

        return response()->json([
            'status' => 'active',
            'notifications' => $notifications,
            'lastId' => $notifications->first()->id ?? $lastId,
            'count' => $notifications->count(),
            'salt' => $this->salt($EVRAKNO,$EVRAKTYPE),
        ]);
    }

    public function salt($EVRAKNO,$EVRAKTYPE)
    {
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        
        $durum = true;
        $name = '';

        if($EVRAKNO == 'pass' || $EVRAKTYPE == 'pass')
        {
            return [
                'durum' => 'false',
            ];
        }

        DB::statement("DELETE FROM ".$firma."D7WLOCK WHERE ACTIVE_TIME < DATEADD(second, -45, GETDATE())");

        $salt = DB::table($firma.'D7WLOCK')->where('EVRAKNO', $EVRAKNO)->where('EVRAKTYPE', $EVRAKTYPE)->first();

        if (!$salt) {
            DB::table($firma.'D7WLOCK')->insert([
                'EVRAKNO' => $EVRAKNO,
                'EVRAKTYPE' => $EVRAKTYPE,
                'ACTIVE_TIME' => now(),
                'USER_ID' => $u->id,
                'USER_NAME' => $u->name
            ]);
            $durum = false;
        }
        else
        {
            if($salt->USER_ID != $u->id)
            {
                $durum = true;
                $name = $salt->USER_NAME;
            }
            else
            {
                DB::table($firma.'D7WLOCK')->where('ID', $salt->ID)->update(['ACTIVE_TIME' => now()]);
                $durum = false;
            }
        }

        return [
            'durum' => $durum,
            'name' => $name
        ];
    }

    public function empty_modul(Request $request)
    {
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        DB::table($firma.'D7WLOCK')
        ->where('EVRAKNO',$request->EVRKANO)
        ->where('EVRAKTYPE',$request->EVRKTYPE)
        ->where('USER_ID', $u->id)
        ->delete();

        return response()->json(['success' => true]);
    }

    public function markAsRead(Request $request)
    {
        $user = auth()->user();
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        DB::table($firma.'notifications')
            ->where('target_user_id', $user->id)
            ->whereIn('id', $request->input('ids', []))
            ->update(['read' => 1]);

        return response()->json(['success' => true]);
    }
}

















// Gelişmiş hali
/*
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function poll(Request $request)
    {
        $user = auth()->user();
        $lastId = (int) $request->query('lastId', 0);

        $notifications = DB::table('notifications')
            ->where('read', 0)
            ->where('id', '>', $lastId)
            ->where(function($query) use ($user) {
                // Kullanıcıya özel VEYA genel bildirimler
                $query->where('target_user_id', $user->id)
                      ->orWhereNull('target_user_id');
            })
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'message', 'type', 'target_user_id', 'created_at']);

        return response()->json([
            'notifications' => $notifications,
            'lastId' => $notifications->first()->id ?? $lastId,
            'count' => $notifications->count()
        ]);
    }

    public function markAsRead(Request $request)
    {
        $user = auth()->user();
        $ids = $request->input('ids', []);

        DB::table('notifications')
            ->whereIn('id', $ids)
            ->where(function($query) use ($user) {
                // Sadece kullanıcının görebildiği bildirimleri işaretle
                $query->where('target_user_id', $user->id)
                      ->orWhereNull('target_user_id');
            })
            ->update(['read' => 1]);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $user = auth()->user();

        $count = DB::table('notifications')
            ->where('read', 0)
            ->where(function($query) use ($user) {
                $query->where('target_user_id', $user->id)
                      ->orWhereNull('target_user_id');
            })
            ->count();

        return response()->json(['count' => $count]);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('per_page', 20);

        $notifications = DB::table('notifications')
            ->where(function($query) use ($user) {
                $query->where('target_user_id', $user->id)
                      ->orWhereNull('target_user_id');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($notifications);
    }

    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|in:info,success,warning,error'
        ]);

        $notificationId = DB::table('notifications')->insertGetId([
            'target_user_id' => $request->user_id,
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type ?? 'info',
            'read' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'notification_id' => $notificationId,
            'message' => 'Bildirim gönderildi'
        ]);
    }

    public function sendToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|in:info,success,warning,error'
        ]);

        $notificationId = DB::table('notifications')->insertGetId([
            'target_user_id' => null, // NULL = genel bildirim
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type ?? 'info',
            'read' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'notification_id' => $notificationId,
            'message' => 'Genel bildirim gönderildi'
        ]);
    }

    public function sendToMultiple(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|in:info,success,warning,error'
        ]);

        $notifications = [];
        $now = Carbon::now();

        foreach ($request->user_ids as $userId) {
            $notifications[] = [
                'target_user_id' => $userId,
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type ?? 'info',
                'read' => 0,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        DB::table('notifications')->insert($notifications);

        return response()->json([
            'success' => true,
            'sent_count' => count($notifications),
            'message' => count($notifications) . ' kullanıcıya bildirim gönderildi'
        ]);
    }

    public function markAllAsRead()
    {
        $user = auth()->user();

        $updated = DB::table('notifications')
            ->where('read', 0)
            ->where(function($query) use ($user) {
                $query->where('target_user_id', $user->id)
                      ->orWhereNull('target_user_id');
            })
            ->update(['read' => 1]);

        return response()->json([
            'success' => true,
            'updated_count' => $updated
        ]);
    }

    public function delete(Request $request)
    {
        $user = auth()->user();
        $ids = $request->input('ids', []);

        // Sadece kullanıcının görebildiği bildirimleri sil
        $deleted = DB::table('notifications')
            ->whereIn('id', $ids)
            ->where(function($query) use ($user) {
                $query->where('target_user_id', $user->id)
                      ->orWhereNull('target_user_id');
            })
            ->delete();

        return response()->json([
            'success' => true,
            'deleted_count' => $deleted
        ]);
    }

    public function cleanup()
    {
        // 30 günden eski okunmuş bildirimleri sil
        $deleted = DB::table('notifications')
            ->where('read', 1)
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->delete();

        return response()->json([
            'success' => true,
            'deleted_count' => $deleted,
            'message' => '30 günden eski bildirimler temizlendi'
        ]);
    }
}
*/