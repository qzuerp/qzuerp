<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class DovizKuruController extends Controller
{
    public function index()
    {
        $response = Http::withoutVerifying()->get('https://www.tcmb.gov.tr/kurlar/today.xml');

        if ($response->successful()) {
            $xml = simplexml_load_string($response->body());
            $data = json_decode(json_encode($xml), true);

            $currencies = collect($data['Currency'])->map(function ($item) {
                return [
                    'CurrencyCode' => $item['@attributes']['CurrencyCode'] ?? '',
                    'CurrencyName' => $item['CurrencyName'] ?? '',
                    'ForexBuying' => is_array($item['ForexBuying']) ? '' : $item['ForexBuying'],
                    'ForexSelling' => is_array($item['ForexSelling']) ? '' : $item['ForexSelling'],
                ];
            });

            return view('doviz_kuru', ['currencies' => $currencies]);
        }

        return view('exchange-rates', ['error' => 'Veri çekilemedi']);
    }
}
