@extends('layout.mainlayout')

@section('content')

    @php
        if (Auth::check()) {
            $user = Auth::user();
        }

        $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
        $database = trim($kullanici_veri->firma).".dbo.";

        $ekran = "DVZKUR";
        $ekranRumuz = "doviz_kuru";
        $ekranAdi = "Günlük Döviz Kuru";
        $ekranLink = "doviz_kuru";
        $ekranTableE = $database."EXCRATE";
        $ekranTableT = $database."EXCRATT";
        $ekranKayitSatirKontrol = "false";
    @endphp

    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        /* Üst kısım */
        .top-bar {
            justify-content: space-between;
            align-items: center;
            width: 95%;
            max-width: 1200px;
            margin: 0px auto 10px auto;
        }

        .date-picker {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .date-picker label {
            font-weight: 600;
            color: #3c8dbc;
            font-size: 15px;
        }

        
        .page-header {
            text-align: center;
            margin: 10px 0 30px 0;
            flex: 1;
        }

        .page-header h1 {
            font-size: 26px;
            font-weight: 600;
            color: #3c8dbc;
        }

        /* Kart tasarımı */
        .currency-container {
            width: 95%;
            max-width: 1200px;
            margin: 0 auto 40px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .currency-card {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .currency-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 22px rgba(0,0,0,0.12);
        }

        .currency-code {
            font-size: 20px;
            font-weight: 700;
            color: #3c8dbc;
            margin-bottom: 8px;
        }

        .currency-name {
            font-size: 15px;
            font-weight: 500;
            color: #555;
            margin-bottom: 15px;
        }

        .currency-values {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: 600;
        }

        .currency-values div {
            background: #eef6fb;
            padding: 10px;
            border-radius: 8px;
            flex: 1;
            text-align: center;
            margin: 0 5px;
            color: #333;
        }

        .currency-values div span {
            display: block;
            font-size: 12px;
            color: #3c8dbc;
            margin-bottom: 4px;
        }

        .no-data {
            text-align: center;
            font-size: 16px;
            color: red;
            margin: 40px 0;
        }
    </style>

    <div class="content-wrapper">

        <!-- Sol üstte tarih, ortada başlık -->
        <div class="top-bar">
           
            <div class="page-header">
                <h1>Günlük Döviz Kuru</h1>
            </div>
			 <form class="date-picker" onsubmit="return false;">
                <label for="tarihSec">📅 Tarih Seç:</label>
                <input type="date" id="tarihSec" max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
            </form>
        </div>

        <!-- Döviz kartları -->
        @if (isset($currencies) && count($currencies) > 0)
            <div class="currency-container">
                @foreach ($currencies as $currency)
                    <div class="currency-card">
                        <div class="currency-code">{{ $currency['CurrencyCode'] }}</div>
                        <div class="currency-name">{{ $currency['CurrencyName'] }}</div>
                        <div class="currency-values">
                            <div>
                                <span>Alış</span>
                                {{ $currency['BanknoteBuying'] }}
                            </div>
                            <div>
                                <span>Satış</span>
                                {{ $currency['BanknoteSelling'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="no-data">Veri bulunamadı veya çekilemedi.</p>
        @endif
    </div>

@endsection
