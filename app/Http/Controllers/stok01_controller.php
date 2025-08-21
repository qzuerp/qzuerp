<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class stok01_controller extends Controller
{

  public function index()
  {
    return view('stok_tv');
  }

  public function index2()
  {
    return view('stok_hareketleri');
  }

  public function getStok01(Request $request)
  {
    $islem = $request->input('islem');
    $KOD = $request->input('KOD');
    $tabledata = "";
    $firma = $request->input('firma').'.dbo.';
    switch($islem) {

    case 'liveStock':

      $STOK01_VERILER=DB::table($firma.'stok10a')
        ->selectRaw('KOD, STOK_ADI, SUM(SF_MIKTAR) MIKTAR, SF_SF_UNIT, LOTNUMBER, SERINO, AMBCODE, TEXT1, TEXT2, TEXT3, TEXT4, NUM1, NUM2, NUM3, NUM4, LOCATION1, LOCATION2, LOCATION3, LOCATION4')
        ->groupBy('KOD', 'STOK_ADI', 'SF_SF_UNIT', 'LOTNUMBER','SERINO', 'AMBCODE', 'TEXT1', 'TEXT2', 'TEXT3', 'TEXT4', 'NUM1', 'NUM2', 'NUM3', 'NUM4', 'LOCATION1', 'LOCATION2', 'LOCATION3', 'LOCATION4')
        ->get();

      foreach ($STOK01_VERILER as $key => $veri) {

        $tabledata .= "<tr>";
        $tabledata .= "<td>".$veri->KOD."</td>";
        $tabledata .= "<td>".$veri->STOK_ADI."</td>";
        $tabledata .= "<td style='color:blue;font-weight:bold'>".$veri->MIKTAR."</td>";
        $tabledata .= "<td>".$veri->SF_SF_UNIT."</td>";
        $tabledata .= "<td>".$veri->LOTNUMBER."</td>";
        $tabledata .= "<td>".$veri->SERINO."</td>";
        $tabledata .= "<td>".$veri->AMBCODE."</td>";
        $tabledata .= "<td>".$veri->LOCATION1."</td>";
        $tabledata .= "<td>".$veri->LOCATION2."</td>";
        $tabledata .= "<td>".$veri->LOCATION3."</td>";
        $tabledata .= "<td>".$veri->LOCATION4."</td>";
        $tabledata .= "<td>".$veri->TEXT1."</td>";
        $tabledata .= "<td>".$veri->TEXT2."</td>";
        $tabledata .= "<td>".$veri->TEXT3."</td>";
        $tabledata .= "<td>".$veri->TEXT4."</td>";
        $tabledata .= "<td>".$veri->NUM1."</td>";
        $tabledata .= "<td>".$veri->NUM2."</td>";
        $tabledata .= "<td>".$veri->NUM3."</td>";
        $tabledata .= "<td>".$veri->NUM4."</td>";
        $tabledata .= "</tr>";

      }

      return $tabledata;

    break;

    case 'cardStock':

      $STOK01_VERILER=DB::table($firma.'stok00')
            ->whereNull('AP10')
            ->orWhere('AP10','!=','0')
            ->get();

      foreach ($STOK01_VERILER as $key => $veri) {

        $tabledata .= "<tr>";
        $tabledata .= "<td>".$veri->KOD."</td>";
        $tabledata .= "<td>".$veri->AD."</td>";
        $tabledata .= "<td>".$veri->MIKTAR."</td>";
        $tabledata .= "<td>".$veri->SF_SF_UNIT."</td>";
        $tabledata .= "<td>".$veri->LOTNUMBER."</td>";
        $tabledata .= "<td>".$veri->SERINO."</td>";
        $tabledata .= "<td>".$veri->AMBCODE."</td>";
        $tabledata .= "<td>".$veri->LOCATION1."</td>";
        $tabledata .= "<td>".$veri->LOCATION2."</td>";
        $tabledata .= "<td>".$veri->LOCATION3."</td>";
        $tabledata .= "<td>".$veri->LOCATION4."</td>";
        $tabledata .= "<td>".$veri->TEXT1."</td>";
        $tabledata .= "<td>".$veri->TEXT2."</td>";
        $tabledata .= "<td>".$veri->TEXT3."</td>";
        $tabledata .= "<td>".$veri->TEXT4."</td>";
        $tabledata .= "<td>".$veri->NUM1."</td>";
        $tabledata .= "<td>".$veri->NUM2."</td>";
        $tabledata .= "<td>".$veri->NUM3."</td>";
        $tabledata .= "<td>".$veri->NUM4."</td>";
        $tabledata .= "</tr>";

      }

      return $tabledata;

      break;
  }

  }

}
