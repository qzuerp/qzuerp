<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\ImageManagerStatic as Image;
class dosyalar00_controller extends Controller
{
    public function import(Request $request)
    {
        if (Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'table' => 'required|string',
        ]);

        $unallowedTables = ['stok', 'urun', 'musteri'];
        $maxRows = 1020;
        
        $blacklistColumns = ['id', 'created_at', 'updated_at'];

        $tableName = $request->input('table');
        $table = $firma . '.dbo.' . $tableName;
        $EVRAKNO = $request->input('EVRAKNO', null); // Default null

        if (in_array($tableName, $unallowedTables)) {
            return response()->json(['error' => 'Bu tabloya yükleme iznin yok.'], 422);
        }

        // ---- TABLO SÜTUNLARINI AL
        $tableColumns = DB::table('INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_NAME', $tableName)
            ->where('TABLE_SCHEMA', 'dbo')
            ->pluck('COLUMN_NAME')
            ->toArray();

        $tableColumnsLowerMap = [];
        foreach ($tableColumns as $col) {
            $tableColumnsLowerMap[strtolower($col)] = $col;
        }

        foreach ($blacklistColumns as $b) {
            unset($tableColumnsLowerMap[strtolower($b)]);
        }

        // ---- EXCEL OKU
        try {
            $collection = Excel::toCollection(null, $request->file('file'))->first();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Excel dosyası okunamadı: ' . $e->getMessage()], 422);
        }

        if (!$collection || $collection->count() == 0) {
            return response()->json(['error' => 'Excel dosyası boş.'], 422);
        }

        $headerRow = $collection->first();
        $dataRows = $collection->slice(1);

        if ($dataRows->count() > $maxRows) {
            return response()->json(['error' => "Satır sayısı çok fazla. Maks {$maxRows} satır izinli."], 422);
        }

        // ---- EXCEL → TABLO SÜTUN EŞLEŞME
        $indexToColumn = [];
        $usedColumns = [];

        foreach ($headerRow as $idx => $head) {
            $headNormalized = strtolower(trim((string) $head));
            if ($headNormalized === '')
                continue;

            // Duplicate başlık kontrolü
            if (isset($usedColumns[$headNormalized])) {
                return response()->json([
                    'error' => "Excel'de duplicate başlık bulundu: {$head}"
                ], 422);
            }

            if (isset($tableColumnsLowerMap[$headNormalized])) {
                $indexToColumn[$idx] = $tableColumnsLowerMap[$headNormalized];
                $usedColumns[$headNormalized] = true;
            }
        }

        if (count($indexToColumn) === 0) {
            return response()->json([
                'error' => 'Excel başlıkları ile tablo sütunları eşleşmedi.',
                'excel_headers' => array_values($headerRow->toArray()),
                'table_columns' => array_values($tableColumnsLowerMap)
            ], 422);
        }
        $chunkSize = floor(2000 / count($indexToColumn));
        // ------------------------------------------------------------------------------------
        // *** STOK KODU KONTROL ENTEGRASYONU ***
        $possibleCodeFields = ['kod', 'stok_kodu', 'stok_kod']; // lowercase
        $codeColumn = null;

        foreach ($possibleCodeFields as $field) {
            if (isset($tableColumnsLowerMap[$field])) {
                $codeColumn = $tableColumnsLowerMap[$field]; // gerçek kolon ismi
                break;
            }
        }

        // Eğer kod alanı varsa → stok00'daki tüm kodları RAM'e çek
        $existingCodes = [];
        if ($codeColumn) {
            try {
                $existingCodes = DB::table($firma . '.dbo.stok00')
                    ->pluck('KOD')
                    ->map(fn($x) => strtolower(trim($x)))
                    ->filter(fn($x) => $x !== '')
                    ->flip() // Daha hızlı arama için array key'e çevir
                    ->toArray();
            } catch (\Exception $e) {
                \Log::error('stok00 kontrolü başarısız: ' . $e->getMessage());
                return response()->json(['error' => 'Stok kontrolü sırasında hata oluştu.'], 500);
            }
        }
        // ------------------------------------------------------------------------------------

        $insertCount = 0;
        $skippedCount = 0;
        $failed = [];
        $batch = [];

        // TRNUM gereken tablolar
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

        $trNum = null;
        if (in_array($tableName, $specialTables)) {
            if (!$EVRAKNO) {
                return response()->json(['error' => 'Bu tablo için EVRAKNO gereklidir.'], 422);
            }

            $trNum = DB::table($table)->where('EVRAKNO', $EVRAKNO)->max('TRNUM');
            $trNum = $trNum ? (int) $trNum + 1 : 1;
        }

        // ---- VERİ SATIRLARINI İŞLE
        foreach ($dataRows as $rIndex => $row) {
            $rowData = [];

            foreach ($indexToColumn as $idx => $colName) {
                $val = $row->get($idx);
                if (is_string($val))
                    $val = trim($val);
                $rowData[$colName] = $val;
            }

            // Tamamen boş satırı atla
            $allNull = true;
            foreach ($rowData as $v) {
                if ($v !== null && $v !== '') {
                    $allNull = false;
                    break;
                }
            }
            if ($allNull) {
                continue;
            }

            // ------------------------------------------------------------------------------------
            // *** STOK00 KONTROLÜ: kod yoksa satırı SKIP ***
            if ($codeColumn && $tableName !== 'stok00') {
                $excelKod = isset($rowData[$codeColumn]) ? strtolower(trim($rowData[$codeColumn])) : '';

                if ($excelKod === '' || !isset($existingCodes[$excelKod])) {
                    // stok00'da karşılığı yok → satırı atla
                    $skippedCount++;
                    continue;
                }
            }
            // ------------------------------------------------------------------------------------

            // TRNUM gerekiyorsa ekle
            if (in_array($tableName, $specialTables)) {
                $rowData['TRNUM'] = str_pad($trNum, 6, '0', STR_PAD_LEFT);
                $rowData['EVRAKNO'] = $EVRAKNO;
                $trNum++;
            }

            // Batch'e ekle
            $batch[] = $rowData;

            // 500'e ulaştıysa insert yap
            if (count($batch) >= $chunkSize) {
                try {
                    DB::table($table)->insert($batch);
                    $insertCount += count($batch);
                } catch (\Exception $e) {
                    $failed[] = [
                        'excel_row_range' => ($rIndex - count($batch) + 2) . '-' . ($rIndex + 2),
                        'error' => $e->getMessage()
                    ];
                    \Log::error('Batch insert failed', [
                        'table' => $tableName,
                        'row_range' => ($rIndex - count($batch) + 2) . '-' . ($rIndex + 2),
                        'error' => $e->getMessage()
                    ]);
                }
                $batch = [];
            }
        }

        // Kalan satırları ekle
        if (count($batch) > 0) {
            try {
                DB::table($table)->insert($batch);
                $insertCount += count($batch);
            } catch (\Exception $e) {
                $failed[] = [
                    'excel_row_range' => 'final_chunk',
                    'error' => $e->getMessage()
                ];
                \Log::error('Final chunk failed', [
                    'table' => $tableName,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $msg = "{$insertCount} kayıt eklendi.";
        if ($skippedCount > 0) {
            $msg .= " {$skippedCount} kayıt stok kartı bulunamadığı için atlandı.";
        }
        if (count($failed) > 0) {
            $msg .= " " . count($failed) . " batch'de hata oluştu.";
        }

        return response()->json([
            'success' => $msg,
            'inserted' => $insertCount,
            'skipped' => $skippedCount,
            'failed_batches' => count($failed),
            'errors' => $failed
        ]);
    }



    private function generateRandomString($length = 10)
    {
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
        $u = Auth::user();
        $firmaDb = trim($u->firma) . '.dbo.';
        $firma   = trim($u->firma);
    
        $dosya = $request->file('dosyaFile');
        $ext   = strtolower($dosya->getClientOriginalExtension());
    
        $hedefKlasor = public_path('dosyalar/' . $firma);
        if (!file_exists($hedefKlasor)) {
            mkdir($hedefKlasor, 0777, true);
        }
    
        // Resim mi?
        $resimExt = ['jpg','jpeg','png','gif','bmp','webp'];
    
        if (in_array($ext, $resimExt)) {
            $dosyaAdi = time() . '.webp';
    
            Image::make($dosya)
                ->encode('webp', 80) // kalite ayarı
                ->save($hedefKlasor . '/' . $dosyaAdi);
        } else {
            // Normal dosya
            $dosyaAdi = time() . '_' . $dosya->getClientOriginalName();
            $dosya->move($hedefKlasor, $dosyaAdi);
        }
    
        $kaydedilecekYol = "{$firma}/{$dosyaAdi}";
    
        DB::table($firmaDb . 'dosyalar00')->insert([
            'EVRAKNO'   => $request->dosyaEvrakNo,
            'DOSYATURU' => $request->dosyaTuruKodu,
            'EVRAKTYPE' => $request->dosyaEvrakType,
            'ACIKLAMA'  => $request->dosyaAciklama,
            'TEMP_ID'   => $request->dosyaTempID,
            'DOSYA'     => $kaydedilecekYol
        ]);
    
        $veri = DB::table($firmaDb . 'dosyalar00')
            ->where('DOSYA', $kaydedilecekYol)
            ->first();
    
        return $veri->id . '|*|*|*|' . $veri->DOSYA . '|*|*|*|' . $veri->created_at;
    }

    public function dosyalariGetir(Request $request)
    {

        $dosyaEvrakType = $request->input('dosyaEvrakType');
        $dosyaEvrakNo = $request->input('dosyaEvrakNo');
        $firma = $request->input('firma') . '.dbo.';
        $veri = DB::table($firma . 'dosyalar00')->where('EVRAKNO', $dosyaEvrakNo)->where('EVRAKTYPE', $dosyaEvrakType)->get();

        return json_encode($veri);
    }

    public function dosyaSil(Request $request)
    {
        $firma = $request->input('firma') . '.dbo.';
        $dosyaID = $request->input('dosyaID');
        $firma = substr($dosyaID, strcspn($dosyaID, ',') + 1, 10) . '.dbo.';
        $dosyaID = substr($dosyaID, 0, strcspn($dosyaID, ','));

        //$veri=DB::table($firma.'dosyalar00')->where('id',$dosyaID)->first();

        // $fileUrl = asset('asset/' . $veri->DOSYA);

        // File::delete($fileUrl);
        DB::table('logx')->insert(['KOMUT' => 'delete ' . $firma . 'dosyalar00' . 'where id = ' . $dosyaID]);
        DB::table($firma . 'dosyalar00')->where('id', $dosyaID)->delete();
        //DB::table('modulsan.dbo.dosyalar00')->where('DOSYA',$dosyaID)->delete();



        //return "OK";
    }
}
