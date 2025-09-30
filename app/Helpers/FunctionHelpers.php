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
        if(!Auth::check()) return null;

        $p = Auth::user();
        $firma = trim($p->firma).'.dbo.';

        $kontroller = [
            ['table'=>'mmps10e', 'column'=>'MAMULSTOKKODU', 'evrak'=>'EVRAKNO', 'msg'=>'MPS bağlantısı'],
            ['table'=>'bomu01e', 'column'=>'MAMULCODE', 'evrak'=>'EVRAKNO', 'msg'=>'Ürün ağacı bağlantısı'],
            ['table'=>'bomu01t', 'column'=>'BOMREC_KAYNAKCODE', 'evrak'=>'EVRAKNO', 'msg'=>'Ürün ağacı (Satır) bağlantısı'],
            ['table'=>'stok40t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Ürün ağacı (Satır) bağlantısı'],
            ['table'=>'stok26t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Depodan depoya transfer (Satır) bağlantısı'],
            ['table'=>'stok25t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Etiket bölme (Satır) bağlantısı'],
            ['table'=>'stok63t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Fason sevk irsaliyesi (Satır) bağlantısı'],
            ['table'=>'stok68t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Fason geliş irsaliyesi (Satır) bağlantısı'],
            ['table'=>'stok21t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Stok giriş-çıkış (Satır) bağlantısı'],
            ['table'=>'sfdc31e', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Çalışma bildirimi bağlantısı'],
            ['table'=>'stok20t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Üretim fişi bağlantısı'],
            ['table'=>'stok60t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Sevk irsaliyesi bağlantısı'],
            ['table'=>'stok48t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Fiyat listesi bağlantısı'],
            ['table'=>'tekl20tı', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Teklif fiyat analiz bağlantısı'],
            ['table'=>'stdm10e', 'column'=>'TEZGAH_KODU', 'evrak'=>'EVRAKNO', 'msg'=>'Maliyet tanımı bağlantısı'],
            ['table'=>'stok46t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Satın alma sipariş bağlantısı'],
            ['table'=>'stok29t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Satın alma irsaliyesi bağlantısı'],
        ];

        foreach($kontroller as $kontrol) {
            $row = DB::table($firma.$kontrol['table'])
                ->where($kontrol['column'], $KOD)
                ->first();

            if($row) {
                $evrakNo = $row->{$kontrol['evrak']} ?? 'Bilgi yok';
                return "Bu kodun {$kontrol['msg']} bulunmakta. Evrak No: {$evrakNo}";
            }
        }

        return null;
    }
}
