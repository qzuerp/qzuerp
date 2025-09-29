<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
class FunctionHelpers
{
    public static function stokKontrol(
        $KOD, $AD, $LOTNO, $SERINO, $SF_SF_UNIT, $AMBCODE,
        $NUM1, $NUM2, $NUM3, $NUM4,
        $TEXT1, $TEXT2, $TEXT3, $TEXT4,
        $LOCATION1, $LOCATION2, $LOCATION3, $LOCATION4
        ) {
        return DB::table('stok10a')
            ->selectRaw('KOD, STOK_ADI, LOTNUMBER, SERINO, SUM(SF_MIKTAR) AS MIKTAR, SF_SF_UNIT, AMBCODE, 
                        NUM1, NUM2, NUM3, NUM4, TEXT1, TEXT2, TEXT3, TEXT4, 
                        LOCATION1, LOCATION2, LOCATION3, LOCATION4')
            ->where('KOD', $KOD)
            ->where('STOK_ADI', $AD)
            ->where('LOTNUMBER', $LOTNO)
            ->where('SERINO', $SERINO)
            ->where('SF_SF_UNIT', $SF_SF_UNIT)
            ->where('AMBCODE', $AMBCODE)
            ->where('NUM1', $NUM1)
            ->where('NUM2', $NUM2)
            ->where('NUM3', $NUM3)
            ->where('NUM4', $NUM4)
            ->where('TEXT1', $TEXT1)
            ->where('TEXT2', $TEXT2)
            ->where('TEXT3', $TEXT3)
            ->where('TEXT4', $TEXT4)
            ->where('LOCATION1', $LOCATION1)
            ->where('LOCATION2', $LOCATION2)
            ->where('LOCATION3', $LOCATION3)
            ->where('LOCATION4', $LOCATION4)
            ->first();
    }

    public static function Logla($EVRAKTYPE,$EVRAKNO,$ISLEM,$TARIH = '')
    {
        if(Auth::check())
        {
            $p = Auth::user();
        }
        $logTarih = Carbon::now()->format('Y-m-d');
        $logTime = Carbon::now()->format('H:i:s');
        DB::table(trim($p->firma).'.dbo.'.'ULOG00')->insert([
            'EVRAKTYPE' => $EVRAKTYPE,
            'EVRAKNO' => $EVRAKNO,
            'PROCESS' => $ISLEM,
            'LOGTARIH' => $logTarih,
            'LOGTIME' => $logTime,
            'USERNAME' => $p->email,
            'OTARIH' => $TARIH
        ]);
    }

    public static function KodKontrol($KOD)
    {
        if(Auth::check())
        {
            $p = Auth::user();
            $firma = trim($p->firma).'.dbo.';
        }
        if(DB::table($firma.'mmps10e')->where('MAMULSTOKKODU',$KOD)->exists())
            return 'Bu kodun MPS bağlantısı bulunmakta';
        else if(DB::table($firma.'bomu01e')->where('MAMULCODE',$KOD)->exists())
            return 'Bu kodun ürün ağacı bağlantısı bulunmakta';
        else if(DB::table($firma.'bomu01t')->where('BOMREC_KAYNAKCODE',$KOD)->exists())
            return 'Bu kodun ürün ağacı(Satır) bağlantısı bulunmakta';
        else if(DB::table($firma.'stok40t')->where('KOD',$KOD)->exists())
            return 'Bu kodun ürün ağacı(Satır) bağlantısı bulunmakta';
        else if(DB::table($firma.'stok26t')->where('KOD',$KOD)->exists())
            return 'Bu kodun depordan depoya transfer(Satır) bağlantısı bulunmakta';
        else if(DB::table($firma.'stok25t')->where('KOD',$KOD)->exists())
            return 'Bu kodun etiket bölme(Satır) bağlantısı bulunmakta';
        else if(DB::table($firma.'stok63t')->where('KOD',$KOD)->exists())
            return 'Bu kodun fason sevk irsaliyesi(Satır) bağlantısı bulunmakta';
        else if(DB::table($firma.'stok68t')->where('KOD',$KOD)->exists())
            return 'Bu kodun fason geliş irsaliyesi(Satır) bağlantısı bulunmakta';
        else if(DB::table($firma.'stok21t')->where('KOD',$KOD)->exists())
            return 'Bu kodun stok giriş-çıkış(Satır) bağlantısı bulunmakta';
        else if(DB::table($firma.'sfdc31e')->where('KOD',$KOD)->exists())
            return 'Bu kodun çalışma bildirimi bağlantısı bulunmakta';
        else if(DB::table($firma.'stok20t')->where('KOD',$KOD)->exists())
            return 'Bu kodun üretim fişi bağlantısı bulunmakta';
        else if(DB::table($firma.'stok60t')->where('KOD',$KOD)->exists())
            return 'Bu kodun sevk irsaliyesi bağlantısı bulunmakta';
        else if(DB::table($firma.'stok48t')->where('KOD',$KOD)->exists())
            return 'Bu kodun fiyat listesi bağlantısı bulunmakta';
        else if(DB::table($firma.'tekl20tı')->where('KOD',$KOD)->exists())
            return 'Bu kodun teklif fiyat analiz bağlantısı bulunmakta';
        else if(DB::table($firma.'stdm10e')->where('TEZGAH_KODU',$KOD)->exists())
            return 'Bu kodun maliyet tanımı bağlantısı bulunmakta';
        else if(DB::table($firma.'stok46t')->where('KOD',$KOD)->exists())
            return 'Bu kodun satın alma sipariş bağlantısı bulunmakta';
        else if(DB::table($firma.'stok29t')->where('KOD',$KOD)->exists())
            return 'Bu kodun satın alma irsaliyesi bağlantısı bulunmakta';
        else if(DB::table($firma.'stok29t')->where('KOD',$KOD)->exists())
            return 'Bu kodun satın alma irsaliyesi bağlantısı bulunmakta';
    }
}
