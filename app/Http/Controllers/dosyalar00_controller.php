<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
class dosyalar00_controller extends Controller
{
    public function import(Request $request)
    {
        if(Auth::check()) {
         $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'table' => 'required|string',
        ]);

        $unallowedTables = ['stok','urun','musteri'];
        $maxRows = 100;
        $chunkSize = 500;
        $blacklistColumns = ['id','created_at','updated_at'];

        $table = $firma.$request->input('table');
        $EVRAKNO = $request->input('EVRAKNO');

        if (in_array($table, $unallowedTables)) {
            return response()->json(['error' => 'Bu tabloya yükleme iznin yok.'], 422);
        }

        $tableColumns = Schema::getColumnListing($table);
        $tableColumnsLowerMap = [];
        foreach ($tableColumns as $col) {
            $tableColumnsLowerMap[strtolower($col)] = $col;
        }

        foreach ($blacklistColumns as $b) {
            unset($tableColumnsLowerMap[strtolower($b)]);
        }

        $collection = Excel::toCollection(null, $request->file('file'))->first();
        if (!$collection || $collection->count() == 0) {
            return response()->json(['error' => 'Excel dosyası boş veya okunamadı.'], 422);
        }

        $headerRow = $collection->first();
        $dataRows = $collection->slice(1);

        if ($dataRows->count() > $maxRows) {
            return response()->json(['error' => "Satır sayısı çok fazla. Maks {$maxRows} satır izinli."], 422);
        }

        $indexToColumn = [];
        foreach ($headerRow as $idx => $head) {
            $headNormalized = strtolower(trim((string)$head));
            if ($headNormalized === '') continue;
            if (isset($tableColumnsLowerMap[$headNormalized])) {
                $indexToColumn[$idx] = $tableColumnsLowerMap[$headNormalized];
            }
        }

        if (count($indexToColumn) === 0) {
            return response()->json(['error' => 'Excel başlıkları ile tablo sütunları eşleşmedi.','excel ' => $headerRow,'table' => $tableColumnsLowerMap], 422);
        }

        $insertCount = 0;
        $failed = [];
        $batch = [];

        $specialTables = [
            "tekl20tı",
            "stdm10t",
            "stok48t",
            "stok40t",
            "MMPS10S_T",
            "bomu01t",
            "mmos10t",
            "stok60ti",
            "sfdc31t",
            "stok20t",
            "mmps10t",
            "stok21t",
            "plan_t",
            "stok26t",
            "stok29t",
            "stok46t",
            "stok63t",
            "QVAL10T",
            "stok68t",
            "stok69t",
            "SRVKC0",
            "stok25t"
        ];
        if(in_array($table, $specialTables))
        {
            $trNum = DB::table($table)->where('EVRAKNO',$EVRAKNO)->max('TRNUM');
            $trNum = $trNum ? (int)$trNum + 1 : 1;
        }
           

        foreach ($dataRows as $rIndex => $row) {
            $rowData = [];
            foreach ($indexToColumn as $idx => $colName) {
                $val = $row->get($idx);
                if (is_string($val)) $val = trim($val);
                $rowData[$colName] = $val;
            }

            $allNull = true;
            foreach ($rowData as $v) { if ($v !== null && $v !== '') { $allNull = false; break; } }
            if ($allNull) continue;

            if (in_array($table, $specialTables)) {
                $rowData['TRNUM'] = str_pad($trNum, 6, '0', STR_PAD_LEFT);
                $trNum++;
                if ($EVRAKNO) {
                    $rowData['EVRAKNO'] = $EVRAKNO;
                }
            }

            $batch[] = $rowData;

            if (count($batch) >= $chunkSize) {
                try {
                    DB::table($table)->insert($batch);
                    $insertCount += count($batch);
                } catch (\Exception $e) {
                    $failed[] = "Chunk insert failed at excel row ".($rIndex+2).": ".$e->getMessage();
                }
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            try {
                DB::table($table)->insert($batch);
                $insertCount += count($batch);
            } catch (\Exception $e) {
                $failed[] = "Final chunk failed: ".$e->getMessage();
            }
        }

        $msg = "{$insertCount} kayıt eklendi.";
        if (count($failed) > 0) {
            $msg .= " Hatalar: ".count($failed);
            \Log::error('Import errors', $failed);
        }

        return response()->json(['success' => $msg]);
    }



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
