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

    public static function KodKontrol($KOD,$table = [])
    {
        if(!Auth::check()) return null;

        $p = Auth::user();
        $firma = trim($p->firma).'.dbo.';

        $kontroller = [
            ['table'=>'mmps10e', 'column'=>'MAMULSTOKKODU', 'evrak'=>'EVRAKNO', 'msg'=>'MPS bağlantısı bulundu'],
            ['table'=>'bomu01e', 'column'=>'MAMULCODE', 'evrak'=>'EVRAKNO', 'msg'=>'Ürün ağacı bağlantısı bulundu'],
            ['table'=>'bomu01t', 'column'=>'BOMREC_KAYNAKCODE', 'evrak'=>'EVRAKNO', 'msg'=>'Ürün ağacı (Satır) bağlantısı bulundu'],
            ['table'=>'stok40t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Satış Sipariş (Satır) bağlantısı bulundu'],
            ['table'=>'stok63t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Fason sevk irsaliyesi (Satır) bağlantısı bulundu'],
            ['table'=>'stok68t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Fason geliş irsaliyesi (Satır) bağlantısı bulundu'],
            ['table'=>'sfdc31e', 'column'=>'STOK_CODE', 'evrak'=>'EVRAKNO', 'msg'=>'Çalışma bildirimi bağlantısı bulundu'],
            ['table'=>'stok20t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Üretim fişi bağlantısı bulundu'],
            ['table'=>'stok60t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Sevk irsaliyesi (Satır) bağlantısı bulundu'],
            ['table'=>'stok48t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Fiyat listesi bağlantısı bulundu'],
            ['table'=>'tekl20tı', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Teklif fiyat analiz bağlantısı bulundu'],
            ['table'=>'stdm10e', 'column'=>'TEZGAH_KODU', 'evrak'=>'EVRAKNO', 'msg'=>'Maliyet tanımı bağlantısı bulundu'],
            ['table'=>'stok46t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Satın alma sipariş bağlantısı bulundu'],
            ['table'=>'stok29t', 'column'=>'KOD', 'evrak'=>'EVRAKNO', 'msg'=>'Satın alma irsaliyesi bağlantısı bulundu'],
        ];

        $messages = [];

        foreach($kontroller as $kontrol) {
            if(in_array($kontrol['table'],$table))
                continue;
            $rows = DB::table($firma.$kontrol['table'])
                ->where($kontrol['column'], $KOD)
                ->pluck($kontrol['evrak']);

            if($rows->count() > 0) {
                $evraklar = $rows->take(3)->implode(', ');
                $extra = $rows->count() > 3 ? " (+".($rows->count()-3)." tane daha)" : "";
                $messages[] = [
                    'tip' => $kontrol['msg'],
                    'evraklar' => $evraklar.$extra
                ];
            }
        }

        return count($messages) ? $messages : null;
    }


}
