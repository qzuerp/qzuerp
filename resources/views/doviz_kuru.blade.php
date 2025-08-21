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
        table {
            width: 60%;
            margin: auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #3c8dbc;
            color: white;
        }
    </style>

	<div class="content-wrapper">
		
    	@include('layout.util.evrakContentHeader')

		@if (isset($currencies) && count($currencies) > 0)
			<table style="width: 90%; margin-top: 20px; border-collapse: collapse;">
				<thead>
					<tr>
						<th>Kur Kodu</th>
						<th>Kur Adı</th>
						<th>Alış Kuru</th>
						<th>Satış Kuru</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($currencies as $currency)
						<tr>
							<td>{{ $currency['CurrencyCode'] }}</td>
							<td>{{ $currency['CurrencyName'] }}</td>
							<td>{{ $currency['BanknoteBuying'] }}</td>
							<td>{{ $currency['BanknoteSelling'] }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		@else
			<p style="text-align: center; color: red;">Veri bulunamadı veya çekilemedi.</p>
		@endif
	</div>
@endsection