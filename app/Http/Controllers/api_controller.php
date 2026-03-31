<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class api_controller extends Controller
{
    public function index()
    {
        return view('api');
    }

    public function islemler(Request $request)
    {
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        $data = [
            'APP_TYPE' => $request->APP_TYPE,
            'CLIENT_ID' => $request->CLIENT_ID,
            'CLIENT_SECRET' => $request->CLIENT_SECRET,
            'username' => $request->USERNAME,
            'password' => $request->PASSWORD,
            'company_id' => $request->company_id,
            'firma' => trim($u->firma)
        ];

        DB::table('tabl91t')->updateOrInsert(
            ['firma' => trim($u->firma)],
            $data
        );

        return redirect()->back()->with('success', 'API ayarları başarıyla güncellendi.');
    }
}
