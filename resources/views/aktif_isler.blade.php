@php
    $isler = DB::table('imlt00 as I00')
    ->leftJoin('sfdc31e as S31E', 'I00.KOD', '=', 'S31E.TO_ISMERKEZI')
    ->leftJoin('sfdc31t as S31T', 'S31E.EVRAKNO', '=', 'S31T.EVRAKNO')
    ->get();
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <title>Tezgah Durum Ekranı</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-800">

  <div class="p-6 grid grid-cols-4 gap-6">
    @foreach ($isler as $is)
    <!-- CARD -->
    <div class="bg-white rounded-xl shadow-md p-6 flex flex-col justify-between">
      <div class="text-2xl font-bold">{{ $is->TO_ISMERKEZI }} - {{ $is->AD }}</div>
      <div class="text-lg text-slate-600 mt-2">{{ $is->JOBNO }}</div>
      <div class="mt-4 text-center text-xl font-bold py-3 rounded-lg bg-green-500 text-white">
        @php
            $is->BITIS_SAATI;
            if($is->BITIS_SAATI == null && $is->BITIS_TARIHI == null && $is->ISLEM_TURU == 'A'){
                echo 'Ayar';
            }
            else if($is->BITIS_SAATI == null && $is->BITIS_TARIHI == null && $is->ISLEM_TURU == 'U')
            {
                echo 'Üretim';
            }
            else if($is->BITIS_SAATI == null && $is->BITIS_TARIHI == null && $is->ISLEM_TURU == 'D')
            {
                echo 'Duruş';
            }
        @endphp
      </div>
    </div>
    @endforeach
  </div>

</body>
</html>