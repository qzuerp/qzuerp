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
  // StokController.php içine ekleyin

  public function stokTvData(Request $request)
  {
      $user = Auth::user();
      $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
      $database = trim($kullanici_veri->firma).".dbo.";

      $evraklar = DB::table($database.'stok10a as s10')
          ->leftJoin($database.'stok00 as s0', 's10.KOD', '=', 's0.KOD')
          ->leftJoin($database.'gdef00 as g', 'g.KOD', '=', 's10.AMBCODE')
          ->selectRaw('
              s10.KOD,
              s10.STOK_ADI,
              SUM(s10.SF_MIKTAR) AS MIKTAR,
              s0.IUNIT AS SF_SF_UNIT,
              s10.LOTNUMBER,
              s10.SERINO,
              s10.AMBCODE,
              g.AD AS DEPO_ADI,
              s10.TEXT1,
              s10.TEXT2,
              s10.TEXT3,
              s10.TEXT4,
              s10.NUM1,
              s10.NUM2,
              s10.NUM3,
              s10.NUM4,
              s10.LOCATION1,
              s10.LOCATION2,
              s10.LOCATION3,
              s10.LOCATION4,
              s0.NAME2,
              s0.id,
              s0.REVNO
          ')
          ->groupBy(
              's10.KOD','s10.STOK_ADI','s0.IUNIT','s10.LOTNUMBER',
              's10.SERINO','s10.AMBCODE','g.AD',
              's10.TEXT1','s10.TEXT2','s10.TEXT3','s10.TEXT4',
              's10.NUM1','s10.NUM2','s10.NUM3','s10.NUM4',
              's10.LOCATION1','s10.LOCATION2','s10.LOCATION3','s10.LOCATION4',
              's0.NAME2','s0.id','s0.REVNO'
          )
          ->havingRaw('SUM(s10.SF_MIKTAR) <> 0')
          ->get();

      // Görselleri toplu al
      $kodlar = $evraklar->pluck('KOD')->toArray();
      $gorseller = collect();
      
      foreach (array_chunk($kodlar, 2000) as $chunk) {
          $part = DB::table($database.'dosyalar00')
              ->whereIn('EVRAKNO', $chunk)
              ->where('EVRAKTYPE', 'STOK00')
              ->where('DOSYATURU', 'GORSEL')
              ->get();
          $gorseller = $gorseller->merge($part);
      }
      
      $gorseller = $gorseller->keyBy('EVRAKNO');

      // DataTable formatında hazırla
      $data = [];
      foreach ($evraklar as $item) {
          $img = $gorseller[$item->KOD] ?? null;
          $imgSrc = $img ? asset('dosyalar/'.$img->DOSYA) : '';
          
          $data[] = [
              'KOD' => $item->KOD,
              'STOK_ADI' => $item->STOK_ADI,
              'NAME2' => $item->NAME2,
              'REVNO' => $item->REVNO,
              'MIKTAR' => $item->MIKTAR,
              'SF_SF_UNIT' => $item->SF_SF_UNIT,
              'LOTNUMBER' => $item->LOTNUMBER,
              'SERINO' => $item->SERINO,
              'AMBCODE' => $item->AMBCODE,
              'DEPO_ADI' => $item->DEPO_ADI,
              'TEXT1' => $item->TEXT1,
              'TEXT2' => $item->TEXT2,
              'TEXT3' => $item->TEXT3,
              'TEXT4' => $item->TEXT4,
              'NUM1' => $item->NUM1,
              'NUM2' => $item->NUM2,
              'NUM3' => $item->NUM3,
              'NUM4' => $item->NUM4,
              'LOCATION1' => $item->LOCATION1,
              'LOCATION2' => $item->LOCATION2,
              'LOCATION3' => $item->LOCATION3,
              'LOCATION4' => $item->LOCATION4,
              'imgSrc' => $imgSrc,
              'id' => $item->id
          ];
      }

      return response()->json(['data' => $data]);
  }

  public function getStokHareketleriData(Request $request)
  {
      $user = Auth::user();
      $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
      $database = trim($kullanici_veri->firma) . ".dbo.";

      $query = DB::table($database . 'stok10a as s10')
          ->leftJoin($database . 'stok00 as s0', 's10.KOD', '=', 's0.KOD')
          ->leftJoin($database . 'gdef00 as g', 'g.KOD', '=', 's10.AMBCODE')
          ->leftJoin($database . 'table00 as t0', 't0.tablo', '=', 's10.EVRAKTIPI')
          ->select(
              's10.*',
              's0.NAME2',
              's0.id as STOK_ID',
              'g.AD as DEPO_ADI',
              't0.BLADE AS EKRAN_ADI',
              't0.baslik AS BASLIK'
          );

      $totalData = $query->count();
      
      if ($request->has('search') && !empty($request->search['value'])) {
          $search = $request->search['value'];
          $query->where(function($q) use ($search) {
              $q->where('s10.KOD', 'like', "%{$search}%")
                ->orWhere('s10.STOK_ADI', 'like', "%{$search}%")
                ->orWhere('s10.EVRAKNO', 'like', "%{$search}%");
          });
      }

      $totalFiltered = $query->count();

      if ($request->has('order')) {
          $orderColumn = $request->order[0]['column'];
          $orderDir = $request->order[0]['dir'];
          $query->orderBy('s10.created_at', $orderDir);
      } else {
          $query->orderBy('s10.created_at', 'desc');
      }

      $start = $request->start ?? 0;
      $length = $request->length ?? 10;
      $evraklar = $query->skip($start)->take($length)->get();

      $data = [];
      foreach ($evraklar as $suzVeri) {
          $data[] = [
              'created_at' => $suzVeri->created_at,
              'KOD' => $suzVeri->KOD,
              'STOK_ADI' => $suzVeri->STOK_ADI,
              'NAME2' => $suzVeri->NAME2,
              'SF_MIKTAR' => $suzVeri->SF_MIKTAR,
              'SF_SF_UNIT' => $suzVeri->SF_SF_UNIT,
              'BASLIK' => $suzVeri->BASLIK,
              'EVRAKNO' => $suzVeri->EVRAKNO,
              'LOTNUMBER' => $suzVeri->LOTNUMBER,
              'SERINO' => $suzVeri->SERINO,
              'AMBCODE' => $suzVeri->AMBCODE . ' - ' . $suzVeri->DEPO_ADI,
              'TEXT1' => $suzVeri->TEXT1,
              'TEXT2' => $suzVeri->TEXT2,
              'TEXT3' => $suzVeri->TEXT3,
              'TEXT4' => $suzVeri->TEXT4,
              'NUM1' => $suzVeri->NUM1,
              'NUM2' => $suzVeri->NUM2,
              'NUM3' => $suzVeri->NUM3,
              'NUM4' => $suzVeri->NUM4,
              'LOCATION1' => $suzVeri->LOCATION1,
              'LOCATION2' => $suzVeri->LOCATION2,
              'LOCATION3' => $suzVeri->LOCATION3,
              'LOCATION4' => $suzVeri->LOCATION4,
              'action' => '<a class="btn btn-info" href="' . trim($suzVeri->EKRAN_ADI) . '?ID=' . $suzVeri->EVRAKNO . '" target="_blank"><i class="fa fa-chevron-circle-right" style="color: white"></i></a>'
          ];
      }

      return response()->json([
          'draw' => intval($request->draw),
          'recordsTotal' => $totalData,
          'recordsFiltered' => $totalFiltered,
          'data' => $data
      ]);
  }
}
