<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>MPS Etiketi</title>
    <style>
        @page {
            size: 5cm 3cm;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
        }
        .etiket {
            width: 5cm;
            height: 3cm;
            box-sizing: border-box;
            border: 1px dashed #000;
            padding: 0.25cm;
            font-family: Arial, sans-serif;
            font-size: 10px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .etiket div {
            height: 0.4cm; /* her alan sabit yükseklikte */
            line-height: 0.4cm;
            margin-bottom: 0.1cm; /* alanlar arası boşluk */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    @php
        $user = Auth::user();
        $firma = trim($user->firma) . '.dbo.';
        $veri = DB::table($firma.'mmps10e')->where('EVRAKNO', $EVRAKNO)->first();
        $alanlar = [
            'MUSTERIKODU' => $veri->MUSTERIKODU ?? '',
            'PROJEKODU' => $veri->PROJEKODU ?? '',
            'EVRAKNO' => $veri->EVRAKNO ?? '',
            'SIPARTNO' => $veri->SIPARTNO ?? '',
            'SF_TOPLAMMIKTAR' => $veri->SF_TOPLAMMIKTAR ?? '',
            'MAMULSTOKADI' => $veri->MAMULSTOKADI ?? '',
            'MAMULSTOKKODU' => $veri->MAMULSTOKKODU ?? ''
        ];
        $ilk_yarisi = array_slice($alanlar, 0, ceil(count($alanlar)/2), true);
        $ikinci_yarisi = array_slice($alanlar, ceil(count($alanlar)/2), null, true);
    @endphp

    <div class="etiket">
        @foreach($ilk_yarisi as $key => $value)
            <div>{{ $value }}</div>
        @endforeach
    </div>

    <div class="etiket" style="justify-content: center;">
        @foreach($ikinci_yarisi as $key => $value)
            <div>{{ $value }}</div>
        @endforeach
    </div>
</body>
</html>
