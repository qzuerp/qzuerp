<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class main_controller extends Controller
{

  public function getIlceler(Request $request)
  {
    $ilSecimi = $request->input('ilSecimi');

    $ilce_veri = DB::table('ilceler')->select('*')->leftJoin('iller','iller.id','=','ilceler.sehirid')->where('sehiradi',@$ilSecimi)->get();

    return json_encode($ilce_veri);
  }
  public function getLoglar(Request $request)
  {
    if(Auth::check())
      $userp1 = Auth::user();

    $veri = DB::table(trim($userp1->firma).'.dbo.'.'ULOG00')
    ->where('EVRAKTYPE', $request->EVRAKTYPE)
    ->where('EVRAKNO', $request->EVRAKNO);

    return DataTables::of($veri)
        ->make(true);
  }
  public function seri_no_uret()
  {
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';
    $id = DB::table($firma.'D7KIDSLB')->max('id');
    $serino = str_pad($id, 12, '0', STR_PAD_LEFT);
    return $serino++;
  }
  public function hizli_islem_verileri(Request $request) {
    $DEPO = $request->veriler[2];
    $LOK1 = $request->veriler[3] ?? NULL;
    $LOK2 = $request->veriler[4] ?? NULL;
    $LOK3 = $request->veriler[5] ?? NULL;
    $LOK4 = $request->veriler[6] ?? NULL;
    
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';
    $data = DB::table($firma.'stok10a')
    ->selectRaw('KOD, STOK_ADI, LOTNUMBER, SERINO, SUM(SF_MIKTAR) AS MIKTAR, SF_SF_UNIT, AMBCODE, 
                  NUM1, NUM2, NUM3, NUM4, TEXT1, TEXT2, TEXT3, TEXT4, 
                  LOCATION1, LOCATION2, LOCATION3, LOCATION4')
    ->where('AMBCODE', $DEPO)
    ->where('LOCATION1', $LOK1)
    ->where('LOCATION2', $LOK2)
    ->where('LOCATION3', $LOK3)
    ->where('LOCATION4', $LOK4)
    ->groupBy(
        'KOD', 'STOK_ADI', 'LOTNUMBER', 'SERINO', 'SF_SF_UNIT', 'AMBCODE', 
        'NUM1', 'NUM2', 'NUM3', 'NUM4', 
        'TEXT1', 'TEXT2', 'TEXT3', 'TEXT4', 
        'LOCATION1', 'LOCATION2', 'LOCATION3', 'LOCATION4'
    )
    ->get();

    
    return $data;
  }
  public function stok_harketleri(Request $request)
  {
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';
    $KOD = $request->KOD;

    return DB::table($firma.'stok10a as s')
        ->leftJoin($firma.'gdef00 as g', 'g.KOD', '=', 's.AMBCODE')
        ->select(
            's.*',
            'g.AD as DEPO_AD'
        )
        ->where('s.KOD', $KOD)
        ->orderBy('s.created_at', 'desc')
        ->get();
    
  }
  public function StokKartinaGit(Request $request)
  {
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';
    return DB::table($firma.'stok00')
    ->where('KOD', $request->KOD)
    ->value('id');
  }
}
