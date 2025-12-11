<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class stok00_controller extends Controller
{
  public function index()
  {
    $sonID=DB::table('stok00')->min('id');

    return view('kart_stok')->with('sonID', $sonID);
  }
  public function getEvraklarAjax(Request $request)
  {
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';
    $veriler = DB::table($firma.'stok00')
      ->select('id', 'KOD', 'AD', 'IUNIT')
      ->get();
    return DataTables::of($veriler)->make(true);
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok00')->where('id',$id)->first();

    return json_encode($veri);
  }
  public function stokKoduAra(Request $request)
  {
      if(Auth::check()) {
        $u = Auth::user();
      }
      $firma = trim($u->firma).'.dbo.';
      $q = $request->input('q');
      $results = DB::table($firma.'stok00')
        ->where(function($query) use ($q) {
            $query->where('KOD', 'like', "%$q%")
                  ->orWhere('AD', 'like', "%$q%");
        })
        ->selectRaw("id, CONCAT(KOD, ' - ', AD) as text")
        ->limit(30)
        ->get();


      return response()->json($results);
  }
  public function stokKoduCustomSelect(Request $request)
  {
      if(Auth::check()) {
        $u = Auth::user();
      }
      $firma = trim($u->firma).'.dbo.';
      $q = $request->input('q');

      $results = DB::table($firma.'stok00')
          ->where(function($query) use ($q) {
              $query->where('KOD', 'like', "%$q%")
                    ->orWhere('AD', 'like', "%$q%");
          })
          ->select('KOD', 'AD', 'IUNIT','NAME2','B_AGIRLIK')
          ->limit(30)
          ->get();

      $formatted = $results->map(function ($veri) {
          return [
              'id' => $veri->KOD . '|||' . $veri->AD . '|||' . $veri->IUNIT . '|||' . $veri->NAME2 . '|||' . $veri->B_AGIRLIK,
              'text' => $veri->KOD . ' - ' . $veri->AD
          ];
      });

      return response()->json(['results' => $formatted]);
  }


  public function islemler(Request $request) {

    //dd(request()->all());

    //if (Auth::check()) {
    //  $user = Auth::user();
    //}

    //$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
    $firma = $request->input('firma').'.dbo.';
    $islem_turu = $request->kart_islemleri;
    $KOD = $request->input('KOD');
    $AD = $request->input('AD');
    $AP10 = $request->AP10;
    $KALIPMI = $request->input('KALIPMI');
    $IUNIT = $request->input('IUNIT');
    $GK_1 = $request->input('GK_1');
    $GK_2 = $request->input('GK_2');
    $GK_3 = $request->input('GK_3');
    $GK_4 = $request->input('GK_4');
    $GK_5 = $request->input('GK_5');
    $GK_6 = $request->input('GK_6');
    $GK_7 = $request->input('GK_7');
    $GK_8 = $request->input('GK_8');
    $GK_9 = $request->input('GK_9');
    $GK_10 = $request->input('GK_10');
    $B_EN = $request->input('B_EN');
    $B_BOY = $request->input('B_BOY');
    $B_YUKSEKLIK = $request->input('B_YUKSEKLIK');
    $B_HACIM = $request->input('B_HACIM');
    $B_AGIRLIK = $request->input('B_AGIRLIK');
    $B_ICCAP = $request->input('B_ICCAP');
    $B_CAP = $request->input('B_CAP');
    $B_YOGUNLUK = $request->input('B_YOGUNLUK');
    $NORM = $request->input('NORM');
    $DEFAULT_LOCATION1 = $request->input('DEFAULT_LOCATION1');
    $DEFAULT_LOCATION2 = $request->input('DEFAULT_LOCATION2');
    $DEFAULT_LOCATION3 = $request->input('DEFAULT_LOCATION3');
    $DEFAULT_LOCATION4 = $request->input('DEFAULT_LOCATION4');
    $B_OR_FIYAT_1 = $request->input('B_OR_FIYAT_1');
    $B_OR_FIYAT_2 = $request->input('B_OR_FIYAT_2');
    $B_OR_FIYAT_3 = $request->input('B_OR_FIYAT_3');
    $B_OR_FIYAT_4 = $request->input('B_OR_FIYAT_4');
    $B_GUVENLIKSTOGU = $request->input('B_GUVENLIKSTOGU');
    $B_MAXENVANTERMIKTARI = $request->input('B_MAXENVANTERMIKTARI');
    $B_MINIMUMSIPMIKTARI = $request->input('B_MINIMUMSIPMIKTARI');
    $B_MINPARTIBUYUKLUGU = $request->input('B_MINPARTIBUYUKLUGU');
    $B_ORTTEMINSURES = $request->input('B_ORTTEMINSURES');
    $B_URETICI_GRNT_SURE = $request->input('B_URETICI_GRNT_SURE');
    $B_SATIS_GRNT_SURE = $request->input('B_SATIS_GRNT_SURE');
    $UNITS_1 = $request->input('UNITS_1');
    $UNITS_2 = $request->input('UNITS_2');
    $UNITS_3 = $request->input('UNITS_3');
    $TBAC_TIPI = $request->input('TBAC_TIPI');
    $B_TBAC_COEF = $request->input('B_TBAC_COEF');
    $CONVUNITS_COEF_1 = $request->input('CONVUNITS_COEF_1');
    $CONVUNITS_COEF_2 = $request->input('CONVUNITS_COEF_2');
    $CONVUNITS_COEF_3 = $request->input('CONVUNITS_COEF_3');
    $CONVUNITS_1 = $request->input('CONVUNITS_1');
    $CONVUNITS_2 = $request->input('CONVUNITS_2');
    $CONVUNITS_3 = $request->input('CONVUNITS_3');
    $NAME2 = $request->input('NAME2');
    $SUPPLIERCODE = $request->input('SUPPLIERCODE');
    $REVNO = $request->input('REVNO');
    $REVTAR = $request->input('REVTAR');
    $ILKNUMTAR = $request->input('ILKNUMTAR');
    $SERIGECTAR = $request->input('SERIGECTAR');
    $L1 = $request->input('L1');
    $L2 = $request->input('L2');
    $L3 = $request->input('L3');
    $L4 = $request->input('L4');
    $L5 = $request->input('L5');
    $L6 = $request->input('L6');
    $L7 = $request->input('L7');
    $L8 = $request->input('L8');
    $L9 = $request->input('L9');
    $L10 = $request->input('L10');
    $L11 = $request->input('L11');
    $L12 = $request->input('L12');
    $L13 = $request->input('L13');
    $L14 = $request->input('L14');
    $L15 = $request->input('L15');
    $L16 = $request->input('L16');
    $L17 = $request->input('L17');
    $L18 = $request->input('L18');
    $L19 = $request->input('L19');
    $L20 = $request->input('L20');

    switch($islem_turu) {

      case 'listele':

        $firma = $request->input('firma').'.dbo.';
        $KOD_E = $request->input('KOD_E');
        $KOD_B = $request->input('KOD_B');
        $GK_1_B = $request->input('GK_1_B');
        $GK_1_E = $request->input('GK_1_E');
        $GK_2_B = $request->input('GK_2_B');
        $GK_2_E = $request->input('GK_2_E');
        $GK_3_B = $request->input('GK_3_B');
        $GK_3_E = $request->input('GK_3_E');
        $GK_4_B = $request->input('GK_4_B');
        $GK_4_E = $request->input('GK_4_E');
        $GK_5_B = $request->input('GK_5_B');
        $GK_5_E = $request->input('GK_5_E');
        $GK_6_B = $request->input('GK_6_B');
        $GK_6_E = $request->input('GK_6_E');
        $GK_7_B = $request->input('GK_7_B');
        $GK_7_E = $request->input('GK_7_E');
        $GK_8_B = $request->input('GK_8_B');
        $GK_8_E = $request->input('GK_8_E');
        $GK_9_B = $request->input('GK_9_B');
        $GK_9_E = $request->input('GK_9_E');
        $GK_10_B = $request->input('GK_10_B');
        $GK_10_E = $request->input('GK_10_E');


        return redirect()->route('kart_stok', [
          'SUZ' => 'SUZ',
          'KOD_B' => $KOD_B, 
          'KOD_E' => $KOD_E, 
          'GK_1_B' => $GK_1_B, 
          'GK_1_E' => $GK_1_E, 
          'GK_2_B' => $GK_2_B, 
          'GK_2_E' => $GK_2_E, 
          'GK_3_B' => $GK_3_B, 
          'GK_3_E' => $GK_3_E, 
          'GK_4_B' => $GK_4_B, 
          'GK_4_E' => $GK_4_E, 
          'GK_5_B' => $GK_5_B, 
          'GK_5_E' => $GK_5_E, 
          'GK_6_B' => $GK_6_B, 
          'GK_6_E' => $GK_6_E, 
          'GK_7_B' => $GK_7_B, 
          'GK_7_E' => $GK_7_E, 
          'GK_8_B' => $GK_8_B, 
          'GK_8_E' => $GK_8_E, 
          'GK_9_B' => $GK_9_B, 
          'GK_9_E' => $GK_9_E, 
          'GK_10_B' => $GK_10_B, 
          'GK_10_E' => $GK_10_E,
          'firma' => $firma
        ]);

        // print_r("mesaj mesaj");
        break;

      case 'kart_sil':
        FunctionHelpers::Logla('STOK00',$KOD,'D');

        $msg = FunctionHelpers::KodKontrol($KOD);

        if($msg) {
          return redirect()->back()->with('error_swal', $msg);
        }

        DB::table($firma.'stok00')->where('KOD',$KOD)->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'stok00')->min('id');
        return redirect()->route('kart_stok', ['ID' => $sonID, 'silme' => 'ok']);

        // break;

      case 'kart_olustur':
        
        
        $check = DB::table($firma.'stok00')->where('KOD', $KOD)->first();
        
        if ($check) {
          return redirect()->back()->with('error', 'Bu kod zaten var.');
        }
        FunctionHelpers::Logla('STOK00',$KOD,'C');

        DB::table($firma.'stok00')->insert([
          'KOD' => $KOD,
          'AD' => $AD,
          'IUNIT' => $IUNIT,
          'AP10' => $AP10,
          'GK_1' => $GK_1,
          'GK_2' => $GK_2,
          'GK_3' => $GK_3,
          'GK_4' => $GK_4,
          'GK_5' => $GK_5,
          'GK_6' => $GK_6,
          'GK_7' => $GK_7,
          'GK_8' => $GK_8,
          'GK_9' => $GK_9,
          'GK_10' => $GK_10,
          'B_EN' => $B_EN,
          'B_BOY' => $B_BOY,
          'B_YUKSEKLIK' => $B_YUKSEKLIK,
          'B_HACIM' => $B_HACIM,
          'B_AGIRLIK' => $B_AGIRLIK,
          'B_ICCAP' => $B_ICCAP,
          'B_CAP' => $B_CAP,
          'B_YOGUNLUK' => $B_YOGUNLUK,
          'NORM' => $NORM,
          'DEFAULT_LOCATION1' => $DEFAULT_LOCATION1,
          'DEFAULT_LOCATION2' => $DEFAULT_LOCATION2,
          'DEFAULT_LOCATION3' => $DEFAULT_LOCATION3,
          'DEFAULT_LOCATION4' => $DEFAULT_LOCATION4,
          'B_OR_FIYAT_1' => $B_OR_FIYAT_1,
          'B_OR_FIYAT_2' => $B_OR_FIYAT_2,
          'B_OR_FIYAT_3' => $B_OR_FIYAT_3,
          'B_OR_FIYAT_4' => $B_OR_FIYAT_4,
          'B_GUVENLIKSTOGU' => $B_GUVENLIKSTOGU,
          'B_MAXENVANTERMIKTARI' => $B_MAXENVANTERMIKTARI,
          'B_MINIMUMSIPMIKTARI' => $B_MINIMUMSIPMIKTARI,
          'B_MINPARTIBUYUKLUGU' => $B_MINPARTIBUYUKLUGU,
          'B_ORTTEMINSURES' => $B_ORTTEMINSURES,
          'B_URETICI_GRNT_SURE' => $B_URETICI_GRNT_SURE,
          'B_SATIS_GRNT_SURE' => $B_SATIS_GRNT_SURE,
          'UNITS_1' => $UNITS_1,
          'UNITS_2' => $UNITS_2,
          'UNITS_3' => $UNITS_3,
          'TBAC_TIPI' => $TBAC_TIPI,
          'B_TBAC_COEF' => $B_TBAC_COEF,
          'CONVUNITS_COEF_1' => $CONVUNITS_COEF_1,
          'CONVUNITS_COEF_2' => $CONVUNITS_COEF_2,
          'CONVUNITS_COEF_3' => $CONVUNITS_COEF_3,
          'CONVUNITS_1' => $CONVUNITS_1,
          'CONVUNITS_2' => $CONVUNITS_2,
          'CONVUNITS_3' => $CONVUNITS_3,
          'NAME2' => $NAME2,
          'SUPPLIERCODE' => $SUPPLIERCODE,
          'REVNO' => $REVNO,
          'REVTAR' => $REVTAR,
          'ILKNUMTAR' => $ILKNUMTAR,
          'SERIGECTAR' => $SERIGECTAR,
          'L1' => $L1,
          'L2' => $L2,
          'L3' => $L3,
          'L4' => $L4,
          'L5' => $L5,
          'L6' => $L6,
          'L7' => $L7,
          'L8' => $L8,
          'L9' => $L9,
          'L10' => $L10,
          'L11' => $L11,
          'L12' => $L12,
          'L13' => $L13,
          'L14' => $L14,
          'L15' => $L15,
          'L16' => $L16,
          'L17' => $L17,
          'L18' => $L18,
          'L19' => $L19,
          'L20' => $L20,
          'created_at' => date('Y-m-d H:i:s'),
        ]);

        print_r("Kayıt işlemi başarılı.");

        $sonID=DB::table($firma.'stok00')->max('id');
        return redirect()->route('kart_stok', ['ID' => $sonID, 'kayit' => 'ok']);

        // break;

      case 'kart_duzenle':
        FunctionHelpers::Logla('STOK00',$KOD,'W');

        DB::table($firma.'stok00')->where('KOD',$KOD)->update([
          'KOD' => $KOD,
          'AD' => $AD,
          'IUNIT' => $IUNIT,
          'AP10' => $AP10,
          'GK_1' => $GK_1,
          'GK_2' => $GK_2,
          'GK_3' => $GK_3,
          'GK_4' => $GK_4,
          'GK_5' => $GK_5,
          'GK_6' => $GK_6,
          'GK_7' => $GK_7,
          'GK_8' => $GK_8,
          'GK_9' => $GK_9,
          'GK_10' => $GK_10,
          'B_EN' => $B_EN,
          'B_BOY' => $B_BOY,
          'B_YUKSEKLIK' => $B_YUKSEKLIK,
          'B_HACIM' => $B_HACIM,
          'B_AGIRLIK' => $B_AGIRLIK,
          'B_ICCAP' => $B_ICCAP,
          'B_CAP' => $B_CAP,
          'B_YOGUNLUK' => $B_YOGUNLUK,
          'NORM' => $NORM,
          'DEFAULT_LOCATION1' => $DEFAULT_LOCATION1,
          'DEFAULT_LOCATION2' => $DEFAULT_LOCATION2,
          'DEFAULT_LOCATION3' => $DEFAULT_LOCATION3,
          'DEFAULT_LOCATION4' => $DEFAULT_LOCATION4,
          'B_OR_FIYAT_1' => $B_OR_FIYAT_1,
          'B_OR_FIYAT_2' => $B_OR_FIYAT_2,
          'B_OR_FIYAT_3' => $B_OR_FIYAT_3,
          'B_OR_FIYAT_4' => $B_OR_FIYAT_4,
          'B_GUVENLIKSTOGU' => $B_GUVENLIKSTOGU,
          'B_MAXENVANTERMIKTARI' => $B_MAXENVANTERMIKTARI,
          'B_MINIMUMSIPMIKTARI' => $B_MINIMUMSIPMIKTARI,
          'B_MINPARTIBUYUKLUGU' => $B_MINPARTIBUYUKLUGU,
          'B_ORTTEMINSURES' => $B_ORTTEMINSURES,
          'B_URETICI_GRNT_SURE' => $B_URETICI_GRNT_SURE,
          'B_SATIS_GRNT_SURE' => $B_SATIS_GRNT_SURE,
          'UNITS_1' => $UNITS_1,
          'UNITS_2' => $UNITS_2,
          'UNITS_3' => $UNITS_3,
          'TBAC_TIPI' => $TBAC_TIPI,
          'B_TBAC_COEF' => $B_TBAC_COEF,
          'CONVUNITS_COEF_1' => $CONVUNITS_COEF_1,
          'CONVUNITS_COEF_2' => $CONVUNITS_COEF_2,
          'CONVUNITS_COEF_3' => $CONVUNITS_COEF_3,
          'CONVUNITS_1' => $CONVUNITS_1,
          'CONVUNITS_2' => $CONVUNITS_2,
          'CONVUNITS_3' => $CONVUNITS_3,
          'NAME2' => $NAME2,
          'SUPPLIERCODE' => $SUPPLIERCODE,
          'REVNO' => $REVNO,
          'REVTAR' => $REVTAR,
          'ILKNUMTAR' => $ILKNUMTAR,
          'SERIGECTAR' => $SERIGECTAR,
          'L1' => $L1,
          'L2' => $L2,
          'L3' => $L3,
          'L4' => $L4,
          'L5' => $L5,
          'L6' => $L6,
          'L7' => $L7,
          'L8' => $L8,
          'L9' => $L9,
          'L10' => $L10,
          'L11' => $L11,
          'L12' => $L12,
          'L13' => $L13,
          'L14' => $L14,
          'L15' => $L15,
          'L16' => $L16,
          'L17' => $L17,
          'L18' => $L18,
          'L19' => $L19,
          'L20' => $L20,
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        print_r("Düzenleme işlemi başarılı.");

        $veri=DB::table($firma.'stok00')->where('KOD',$KOD)->first();

        return redirect()->route('kart_stok', ['ID' => $veri->id, 'duzenleme' => 'ok']);

        // break;

    }
    
  }

}
