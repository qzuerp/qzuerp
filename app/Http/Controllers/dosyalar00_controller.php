<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class dosyalar00_controller extends Controller
{
    public function import(Request $request)
    {
        if (Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma);

        $request->validate([
            'file'  => 'required|file|mimes:xlsx,xls,csv',
            'table' => 'required|string',
        ]);

        $unallowedTables  = ['stok', 'urun', 'musteri'];
        $blacklistColumns = ['id', 'created_at', 'updated_at'];
        $maxRows          = 3000;

        $tableName = $request->input('table');
        $table     = $firma . '.dbo.' . $tableName;
        $EVRAKNO   = $request->input('EVRAKNO');

        if (in_array($tableName, $unallowedTables)) {
            return response()->json(['error' => 'Bu tabloya yükleme iznin yok.'], 422);
        }

        // Tablo sütunlarını çek
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

        // Tüm dosyayı oku
        $collection = Excel::toCollection(null, $request->file('file'))->first();
        if (!$collection || $collection->isEmpty()) {
            return response()->json(['error' => 'Excel dosyası boş veya okunamadı.'], 422);
        }

        $headerRow = $collection->first();
        $dataRows  = $collection->slice(1);

        if ($dataRows->count() > $maxRows) {
            return response()->json(['error' => "Satır sayısı çok fazla. Maks {$maxRows} satır izinli."], 422);
        }

        // Header → kolon eşleştirmesi
        $indexToColumn = [];
        foreach ($headerRow as $idx => $head) {
            $headNormalized = strtolower(trim((string)$head));
            if ($headNormalized === '') continue;
            if (isset($tableColumnsLowerMap[$headNormalized])) {
                $indexToColumn[$idx] = $tableColumnsLowerMap[$headNormalized];
            }
        }

        if (empty($indexToColumn)) {
            return response()->json([
                'error' => 'Excel başlıkları ile tablo sütunları eşleşmedi.',
                'excel' => $headerRow,
                'table' => $tableColumnsLowerMap,
            ], 422);
        }

        // BURAYA DIKKAT: sym10t listeye eklendi!
        $specialTables = [
            "sym10t", "tekl20tı","stdm10t","stok48t","stok40t","MMPS10S_T","bomu01t","mmos10t",
            "stok60ti","sfdc31t","stok20t","mmps10t","stok21t","plan_t","stok26t",
            "stok29t","stok46t","stok63t","QVAL10T","stok68t","stok69t","stok25t",
        ];

        $columnCount = count($indexToColumn);
        $effectiveColumnCount = in_array($tableName, $specialTables) ? $columnCount + 2 : $columnCount;
        
        // SQL Server garantili safe chunk boyutu
        $chunkSize = max(1, (int) floor(2000 / $effectiveColumnCount));

        // TRNUM başlangıcı
        $trNum = null;
        if (in_array($tableName, $specialTables)) {
            $max   = DB::table($table)->where('EVRAKNO', $EVRAKNO)->max('TRNUM');
            $trNum = $max ? (int)$max + 1 : 1;
        }

        $insertCount = 0;
        $allRowsData = [];

        // Önce tüm veriyi hafızada toplayıp temizliyoruz
        foreach ($dataRows as $rIndex => $row) {
            $rowData = [];
            foreach ($indexToColumn as $idx => $colName) {
                $val = $row->get($idx);
                if (is_string($val)) $val = trim($val);
                $rowData[$colName] = $val;
            }

            // Tamamen boş satırı atla
            $allNull = true;
            foreach ($rowData as $v) {
                if ($v !== null && $v !== '') { $allNull = false; break; }
            }
            if ($allNull) continue;

            if (in_array($tableName, $specialTables)) {
                $rowData['TRNUM'] = str_pad($trNum, 6, '0', STR_PAD_LEFT);
                $trNum++;
                if ($EVRAKNO) {
                    $rowData['EVRAKNO'] = $EVRAKNO;
                }
            }

            $allRowsData[] = $rowData;
        }

        // Şimdi güvenli transaction ve chunk insert aşaması
        if (!empty($allRowsData)) {
            try {
                DB::transaction(function () use ($allRowsData, $chunkSize, $table, &$insertCount) {
                    $chunks = array_chunk($allRowsData, $chunkSize);
                    foreach ($chunks as $chunk) {
                        DB::table($table)->insert($chunk);
                        $insertCount += count($chunk);
                    }
                });
            } catch (\Exception $e) {
                \Log::error('Import fatal error: ' . $e->getMessage());
                return response()->json(['error' => 'Veritabanına yazılırken bir hata oluştu: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['success' => "{$insertCount} kayıt başarıyla eklendi."]);
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

        $resimExt = ['jpg','jpeg','png','gif','bmp','webp'];

        if (in_array($ext, $resimExt)) {

            $dosyaAdi = time() . '.webp';

            $manager = new ImageManager(new Driver());

            $image = $manager->read($dosya);
            $image->toWebp(80)
                ->save($hedefKlasor . '/' . $dosyaAdi);

        } else {

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
