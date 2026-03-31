<?php

namespace App\Services;

use App\Interfaces\AccountingInterface;
use App\Services\ParasutService;
// use App\Services\MakroService; // Yarın buraya eklenecek
use Exception;
use Illuminate\Support\Facades\DB;

class AccountingManager
{
    public function getProvider($firmaId): AccountingInterface
    {
        $config = DB::table('tabl91t')->where('firma', $firmaId)->first();

        if (!$config) {
            throw new Exception("Firma için muhasebe ayarları bulunamadı (ID: $firmaId)");
        }

        $service = match (strtolower($config->APP_TYPE)) {
            'parasut' => new ParasutService(),
            // 'makro' => new MakroService(),
            default   => throw new Exception("Desteklenmeyen uygulama tipi: " . $config->APP_TYPE),
        };

        if (!$service->authenticate((array)$config)) {
            throw new Exception("Muhasebe programına giriş başarısız! Ayarları kontrol et.");
        }

        return $service;
    }
}