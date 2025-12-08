<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DovizKaydetme extends Command
{
    protected $signature = 'doviz:kaydet';
    protected $description = 'TCMB den günlük döviz kurlarını çeker ve veritabanına kaydeder';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // TCMB'den son geçerli günü otomatik bulan fonksiyon
        $xml = $this->getLatestTCMBData();

        if (!$xml) {
            $this->error('TCMB’den veri çekilemedi (3 güne kadar denendi).');
            return 1;
        }

        $data = json_decode(json_encode($xml), true);

        $tarih = Carbon::now()->format('Y/m/d');
        $trnum = '000001';

        foreach ($data['Currency'] as $currency) {

            $currencyCode = $currency['@attributes']['CurrencyCode'] ?? null;
            $buyingRate   = isset($currency['BanknoteBuying']) 
                                ? str_replace(',', '.', $currency['BanknoteBuying']) : null;

            if (!empty($buyingRate) && !is_array($buyingRate)) {

                DB::table('excratt')->insert([
                    'EVRAKNOTARIH' => $tarih,
                    'TRNUM'        => $trnum,
                    'SRNUM'        => $trnum,
                    'CODEFROM'     => $currencyCode,
                    'CODETO'       => 'TRY',
                    'KURS_1'       => $buyingRate,
                    'KURTYPE'      => 'ALIS'
                ]);

                // TRNUM arttır
                $trnum = str_pad((int)$trnum + 1, 6, '0', STR_PAD_LEFT);
            }
        }

        $this->info('Döviz kurları başarıyla güncellendi.');
        return 0;
    }

    /**
     * TCMB bugün veri vermediyse 1–3 gün geri gider
     */
    private function getLatestTCMBData()
    {
        // TCMB hafta sonu veri yayınlamıyor → 3 gün geriye bakmak ideal
        for ($i = 0; $i < 3; $i++) {

            // Geri gittiğimiz gün
            $date = Carbon::now()->subDays($i)->format('dmY'); // Ör: 21012025
            $folder = Carbon::now()->subDays($i)->format('Ym'); // Ör: 202501

            $url = "https://www.tcmb.gov.tr/kurlar/{$folder}/{$date}.xml";

            $response = Http::withoutVerifying()->get($url);

            if ($response->successful()) {
                return simplexml_load_string($response->body());
            }
        }

        return null;
    }
}