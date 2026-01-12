<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Final Kalite Kontrol</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f6f8;
        }
        .report-box {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 20px;
        }
        .report-title {
            font-weight: 700;
            font-size: 18px;
            text-align: center;
        }
        .report-subtitle {
            font-size: 14px;
            text-align: center;
            color: #555;
        }
        .label-title {
            font-size: 12px;
            font-weight: 600;
        }
        .label-sub {
            font-size: 11px;
            color: #666;
        }
        .value-box {
            border: 1px solid #ced4da;
            padding: 6px 8px;
            min-height: 34px;
            background: #fff;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container my-4">
    <div class="report-box">

        <!-- HEADER -->
        <div class="row align-items-center mb-3">
            <div class="col-md-3" style="max-height:60px !important;">
                @php
                    if(Auth::check()) {
                        $u = Auth::user();
                    }

                    $FIRMA = DB::table('FIRMA_TANIMLARI')->where('FIRMA',trim($u->firma))->first();
                @endphp
                <img src="{{ asset($FIRMA->LOGO_URL) }}" style="max-height:60px !important; object-fit:cover;" alt="{{ $FIRMA->FIRMA_ADI }}" class="w-100">
            </div>
            <div class="col-md-6">
                <div class="report-title">
                    FINAL KALİTE KONTROL TALİMATI VE RAPORU
                </div>
                <div class="report-subtitle">
                    Final Quality Control Instruction and Report
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>

        <hr>

        <!-- FORM GRID -->
        <div class="row g-3">

            <!-- SOL -->
            <div class="col-md-6">
                <div class="row g-2">

                    <div class="col-6">
                        <div class="label-title">MÜŞTERİ</div>
                        <div class="label-sub">Customer</div>
                        <div class="value-box">{{ $data->KRITERCODE_3 }}</div>
                    </div>

                    <div class="col-6">
                        <div class="label-title">SİPARİŞ NO</div>
                        <div class="label-sub">Order No</div>
                        <div class="value-box">{{ $data->order_no }}</div>
                    </div>

                    <div class="col-6">
                        <div class="label-title">PARÇA NUMARASI</div>
                        <div class="label-sub">Part Number</div>
                        <div class="value-box">{{ $data->KOD }}</div>
                    </div>

                    <div class="col-6">
                        <div class="label-title">İŞ EMRİ NO</div>
                        <div class="label-sub">Work Order No</div>
                        <div class="value-box">{{ $data->work_order_no }}</div>
                    </div>

                    <div class="col-6">
                        <div class="label-title">TEKNİK RESİM NO</div>
                        <div class="label-sub">Technical Drawing No</div>
                        <div class="value-box">{{ $data->work_order_no }}</div>
                    </div>

                    <div class="col-6">
                        <div class="label-title">REV. NO</div>
                        <div class="label-sub">Rev. No</div>
                        <div class="value-box">{{ $data->rev_no }}</div>
                    </div>

                </div>
            </div>

            <!-- SAĞ -->
            <div class="col-md-6">
                <div class="row g-2">

                    <div class="col-6">
                        <div class="label-title">RAPOR NO</div>
                        <div class="label-sub">Report No</div>
                        <div class="value-box">{{ $data->report_no }}</div>
                    </div>

                    <div class="col-6">
                        <div class="label-title">TARİH</div>
                        <div class="label-sub">Date</div>
                        <div class="value-box">{{ $data->date }}</div>
                    </div>

                    <div class="col-6">
                        <div class="label-title">PARTİ / FİŞ NO</div>
                        <div class="label-sub">Batch Nr</div>
                        <div class="value-box">{{ $data->batch_no }}</div>
                    </div>

                    <div class="col-6">
                        <div class="label-title">NUMUNE ADEDİ</div>
                        <div class="label-sub">Sample Qty.</div>
                        <div class="value-box">{{ $data->sample_qty }}</div>
                    </div>

                    <div class="col-12">
                        <div class="label-title">PARÇA ADI</div>
                        <div class="label-sub">Part Name</div>
                        <div class="value-box">
                            
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="label-title">SİPARİŞ ADEDİ</div>
                        <div class="label-sub">Batch Qty</div>
                        <div class="value-box">{{ $data->order_qty }}</div>
                    </div>

                    <div class="col-6">
                        <div class="label-title">SEVK EDİLEN ÜRÜN SAYISI</div>
                        <div class="label-sub">Number of products</div>
                        <div class="value-box">{{ $data->shipped_qty }}</div>
                    </div>

                </div>
            </div>

        </div>
        <hr class="my-4">
        <div class="row g-3">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Ölçüm No</th>
                                <th>Kriter Kodu</th>
                                <th>Min</th>
                                <th>Max</th>
                                <th>Cihaz Kodu</th>
                                <th>N1</th>
                                @php
                                    // İlk satırdaki numune sayısını al (tüm satırlar için geçerli olacak)
                                    $maxNumuneSayisi = 0;
                                    if(Auth::check()) {
                                        $u = Auth::user();
                                    }
                                    $firma = trim($u->firma).'.dbo.';
                                    
                                    foreach ($data->satirlar->TRNUM as $trnum) {
                                        $numuneSayisi = DB::table($firma.'FKKTI')->where('OR_TRNUM', $trnum)->count();
                                        if ($numuneSayisi > $maxNumuneSayisi) {
                                            $maxNumuneSayisi = $numuneSayisi;
                                        }
                                    }
                                    
                                    for ($n = 2; $n <= $maxNumuneSayisi + 1; $n++) {
                                        echo "<th>N{$n}</th>";
                                    }
                                @endphp
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < count($data->satirlar->TRNUM); $i++)
                                @php
                                    if(Auth::check()) {
                                        $u = Auth::user();
                                    }
                                    $firma = trim($u->firma).'.dbo.';
                                    $NUMUNELER = DB::table($firma.'FKKTI')->where('OR_TRNUM', $data->satirlar->TRNUM[$i])->get();
                                @endphp
                                <tr>
                                    <td>{{ $data->satirlar->QS_VARINDEX[$i] ?? '' }}</td>
                                    <td>{{ $data->satirlar->QS_VARCODE[$i] ?? '' }}</td>
                                    <td>{{ $data->satirlar->VERIFIKASYONNUM1[$i] ?? '' }}</td>
                                    <td>{{ $data->satirlar->VERIFIKASYONNUM2[$i] ?? '' }}</td>
                                    <td>{{ $data->satirlar->CIHAZKODU[$i] ?? '' }}</td>
                                    <td>{{ $data->satirlar->QS_VALUE[$i] ?? '' }}</td>
                                    @foreach ($NUMUNELER as $numune)
                                        <td>{{ $numune->QS_VALUE ?? '' }}</td>
                                    @endforeach
                                    @php
                                        // Eksik sütunları boş hücrelerle doldur
                                        $eksikSutun = $maxNumuneSayisi - count($NUMUNELER);
                                        for ($k = 0; $k < $eksikSutun; $k++) {
                                            echo "<td></td>";
                                        }
                                    @endphp
                                </tr>
                            @endfor

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>