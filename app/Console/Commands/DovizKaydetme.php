<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DovizKaydetme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doviz:kaydet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TCMB den günlük döviz kurlarını çeker ve veritabanına kaydeder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::withoutVerifying()->get('https://www.tcmb.gov.tr/kurlar/today.xml');

        if ($response->successful()) {
            $xml = simplexml_load_string($response->body());
            $data = json_decode(json_encode($xml), true);
            
            $tarih = Carbon::now()->format('Y/m/d');
            $trnum = '000001';
            
            foreach ($data['Currency'] as $currency) {
                $currencyCode = $currency['@attributes']['CurrencyCode'];
                $buyingRate = str_replace(',', '.', $currency['BanknoteBuying']);
                $sellingRate = str_replace(',', '.', $currency['BanknoteSelling']);
                
                // Alış kuru kaydı
                if (!empty($buyingRate) && !is_array($buyingRate)) {
                    DB::table('excratt')->insert([
                        'EVRAKNOTARIH' => $tarih,
                        'TRNUM' => $trnum,
                        'SRNUM' => $trnum,
                        'CODEFROM' => $currencyCode,
                        'CODETO' => 'TRY',
                        'KURS_1' => $buyingRate,
                        'KURTYPE' => 'ALIS'
                    ]);
                    
                    $trnum = str_pad((int)$trnum + 1, 6, '0', STR_PAD_LEFT);
                }
            }
            
            $this->info('Döviz kurları başarıyla güncellendi.');
        } else {
            $this->error('TCMB servisine bağlanırken bir hata oluştu.');
        }
    }

}
