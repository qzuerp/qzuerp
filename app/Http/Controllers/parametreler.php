<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class parametreler extends Controller
{
    public function index()
    {
        if(Auth::check()) {
            $u = Auth::user();
        }
        $db = trim($u->firma);
        $firma = DB::table('FIRMA_TANIMLARI')->where('FIRMA', $db)->first();
        return view('parametreler', compact('firma'));
    }

    public function islemler(Request $request)
    {
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma);
        $data = $request->except(['_token', 'LOGO_URL']);

        $folder = public_path('firma_Logolari/'.$firma);

        // klasör yoksa oluştur
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        if ($request->hasFile('LOGO_URL')) {
            $file = $request->file('LOGO_URL');

            $filename = 'logo.'.$file->getClientOriginalExtension();

            $file->move($folder, $filename);

            $data['LOGO_URL'] = 'firma_logolari/'.$firma.'/'.$filename;
        }

        DB::table('FIRMA_TANIMLARI')->updateOrInsert(
            ['FIRMA' => $firma],
            $data
        );

        return back()->with('success', 'Firma parametreleri güncellendi.');
    }

}
