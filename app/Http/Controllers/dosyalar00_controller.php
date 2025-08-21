<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
class dosyalar00_controller extends Controller
{

  private function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
  }

public function dosyaEkle(Request $request)
{
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';
    $f = trim($u->firma);
    $dosya = $request->file('dosyaFile');
    $dosyaAdi = time() . '_' . $dosya->getClientOriginalName();

    $hedefKlasor = public_path('dosyalar/' . trim($u->firma));
    $kaydedilecekYol = "{$f}/{$dosyaAdi}";
    
    if (!file_exists($hedefKlasor)) {
        mkdir($hedefKlasor, 0777, true);
    }

    $dosya->move($hedefKlasor, $dosyaAdi);

    DB::table($firma . 'dosyalar00')->insert([
        'EVRAKNO' => $request->input('dosyaEvrakNo'),
        'DOSYATURU' => $request->input('dosyaTuruKodu'),
        'EVRAKTYPE' => $request->input('dosyaEvrakType'),
        'ACIKLAMA' => $request->input('dosyaAciklama'),
        'DOSYA' => $kaydedilecekYol
    ]);

    $veri = DB::table($firma . 'dosyalar00')->where('DOSYA', $kaydedilecekYol)->first();
    return $veri->id . '|*|*|*|' . $veri->DOSYA . '|*|*|*|' . $veri->created_at;
}


  public function dosyalariGetir(Request $request) {

    $dosyaEvrakType = $request->input('dosyaEvrakType');
    $dosyaEvrakNo = $request->input('dosyaEvrakNo');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'dosyalar00')->where('EVRAKNO',$dosyaEvrakNo)->where('EVRAKTYPE',$dosyaEvrakType)->get();

    return json_encode($veri);
  }

  public function dosyaSil(Request $request) {
    $firma = $request->input('firma').'.dbo.';
    $dosyaID = $request->input('dosyaID');
    $firma =substr($dosyaID,strcspn($dosyaID,',')+1,10).'.dbo.';
    $dosyaID = substr($dosyaID,0,strcspn($dosyaID,','));
    
    //$veri=DB::table($firma.'dosyalar00')->where('id',$dosyaID)->first();

   // $fileUrl = asset('asset/' . $veri->DOSYA);

   // File::delete($fileUrl);
    DB::table('logx')->insert(['KOMUT' => 'delete ' .$firma.'dosyalar00'.'where id = '.$dosyaID  ]);
    DB::table($firma.'dosyalar00')->where('id',$dosyaID)->delete();
    //DB::table('modulsan.dbo.dosyalar00')->where('DOSYA',$dosyaID)->delete();

    

    //return "OK";
  }

}
