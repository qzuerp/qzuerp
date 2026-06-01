@extends('layout.mainlayout')

@php
  if (Auth::check()) { $user = Auth::user(); }
  $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran              = "STKSYM";
  $ekranRumuz         = "STOK21";
  $ekranAdi           = "Stok Mukayesesi";
  $ekranLink          = "stokSayim";
  $ekranTableE        = $database."sym10e";
  $ekranTableT        = $database."sym10t";
  $ekranKayitSatirKontrol = "true";

  $kullanici_read_yetkileri   = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri  = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $sayimListesi = DB::table($database.'sym10e')->get();
  $depo         = DB::table($database.'gdef00')->orderBy('id','ASC')->get();
@endphp

@section('content')
<style>
  /* Google Font - IBM Plex Sans (compact, professional) */
  @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap');

  .muk-scope {
    font-family: 'IBM Plex Sans', sans-serif;
    padding: 20px;
  }
  .muk-scope * { box-sizing: border-box; }

  /* ── Filter Card ─────────────────────────── */
  .muk-filter-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 18px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
  }

  .muk-section-title {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: #94a3b8;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f1f5f9;
  }

  .muk-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 4px;
  }
  @media(max-width:900px){ .muk-grid { grid-template-columns: repeat(2,1fr); } }
  @media(max-width:600px){ .muk-grid { grid-template-columns: 1fr; } }

  .muk-field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 5px;
  }
  .muk-field select {
    width: 100%;
    border: 1.5px solid #e2e8f0;
    border-radius: 7px;
    padding: 7px 10px;
    font-size: 13px;
    color: #1e293b;
    background: #f8fafc;
    transition: border-color .15s, box-shadow .15s;
    font-family: 'IBM Plex Sans', sans-serif;
  }
  .muk-field select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
    background: #fff;
  }

  /* select2 uyum */
  .muk-scope .select2-container { width: 100% !important; }
  .muk-scope .select2-container--default .select2-selection--single {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 7px !important;
    height: 36px !important;
    background: #f8fafc !important;
    font-family: 'IBM Plex Sans', sans-serif;
  }
  .muk-scope .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 34px !important;
    font-size: 13px !important;
    color: #1e293b !important;
    padding-left: 10px !important;
  }
  .muk-scope .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 34px !important;
  }
  .muk-scope .select2-container--default.select2-container--open .select2-selection--single {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
  }
  .select2-dropdown { border: 1.5px solid #e2e8f0 !important; border-radius: 8px !important; box-shadow: 0 8px 24px rgba(0,0,0,.1) !important; }
  .select2-results__option { font-size: 13px !important; }
  .select2-container--default .select2-results__option--highlighted { background: #3b82f6 !important; }

  .muk-hr { border: none; border-top: 1px solid #f1f5f9; margin: 18px 0; }

  /* ── Action row ─────────────────────────── */
  .muk-action-row {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 18px;
    flex-wrap: wrap;
  }

  /* ── Buttons ─────────────────────────────── */
  .muk-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    border-radius: 8px;
    border: none;
    font-family: 'IBM Plex Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s, box-shadow .15s, transform .1s;
    white-space: nowrap;
  }
  .muk-btn:active:not(:disabled) { transform: scale(.97); }
  .muk-btn:disabled { opacity: .45; cursor: not-allowed; }

  .muk-btn-blue   { background: #2563eb; color: #fff; }
  .muk-btn-blue:hover:not(:disabled) { background: #1d4ed8; box-shadow: 0 4px 12px rgba(37,99,235,.3); }

  .muk-btn-red    { background: #dc2626; color: #fff; }
  .muk-btn-red:hover:not(:disabled) { background: #b91c1c; box-shadow: 0 4px 12px rgba(220,38,38,.3); }

  .muk-btn-amber  { background: #d97706; color: #fff; }
  .muk-btn-amber:hover:not(:disabled) { background: #b45309; box-shadow: 0 4px 12px rgba(217,119,6,.3); }

  /* ── Stats bar ───────────────────────────── */
  .muk-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 18px;
  }
  @media(max-width:900px){ .muk-stats { grid-template-columns: repeat(2,1fr); } }
  @media(max-width:500px){ .muk-stats { grid-template-columns: 1fr; } }

  .muk-stat {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 22px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
  }
  .muk-stat-val { font-size: 30px; font-weight: 700; line-height: 1; }
  .muk-stat-lbl { font-size: 10.5px; font-weight: 600; letter-spacing: .8px; text-transform: uppercase; color: #94a3b8; margin-top: 5px; }

  .muk-stat-all { border-top: 3px solid #64748b; }
  .muk-stat-all .muk-stat-val { color: #1e293b; }
  .muk-stat-neg { border-top: 3px solid #dc2626; }
  .muk-stat-neg .muk-stat-val { color: #dc2626; }
  .muk-stat-pos { border-top: 3px solid #d97706; }
  .muk-stat-pos .muk-stat-val { color: #d97706; }

  /* Doğruluk kartı */
  .muk-stat-acc { border-top: 3px solid #e2e8f0; padding: 14px 20px; }
  .muk-stat-acc-inner { display: flex; align-items: center; gap: 16px; }

  .muk-ring-wrap { position: relative; width: 64px; height: 64px; flex-shrink: 0; }
  .muk-ring-wrap svg { transform: rotate(-90deg); }
  .muk-ring-bg   { fill: none; stroke: #f1f5f9; stroke-width: 6; }
  .muk-ring-fill { fill: none; stroke-width: 6; stroke-linecap: round;
                   transition: stroke-dashoffset 1s cubic-bezier(.4,0,.2,1), stroke .4s; }
  .muk-ring-pct  {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #1e293b;
    transform: rotate(0deg); /* counter-rotate back */
  }

  .muk-acc-info {}
  .muk-acc-val   { font-size: 24px; font-weight: 700; line-height: 1; }
  .muk-acc-state { font-size: 11px; font-weight: 600; margin-top: 3px; letter-spacing: .3px; }
  .muk-acc-lbl   { font-size: 10.5px; font-weight: 600; letter-spacing: .8px;
                   text-transform: uppercase; color: #94a3b8; margin-top: 4px; }

  /* ── Result card ─────────────────────────── */
  .muk-result-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    position: relative;
  }

  /* ── Loading overlay ─────────────────────── */
  .muk-loading {
    display: none;
    position: absolute; inset: 0;
    background: rgba(255,255,255,.82);
    border-radius: 12px;
    z-index: 20;
    align-items: center; justify-content: center;
    flex-direction: column; gap: 12px;
  }
  .muk-loading.active { display: flex; }
  .muk-spinner {
    width: 38px; height: 38px;
    border: 3px solid #e2e8f0;
    border-top-color: #2563eb;
    border-radius: 50%;
    animation: muk-spin .65s linear infinite;
  }
  .muk-loading-txt { font-size: 13px; font-weight: 600; color: #475569; }
  @keyframes muk-spin { to { transform: rotate(360deg); } }

  /* ── Table ───────────────────────────────── */
  #veriTable { font-family: 'IBM Plex Mono', monospace; font-size: 12px; width: 100%; }

  #veriTable thead th {
    background: #f8fafc;
    color: #64748b;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
    white-space: nowrap;
    padding: 10px 12px;
    border-top: none;
    border-bottom: 2px solid #e2e8f0;
  }
  #veriTable tbody td {
    padding: 8px 12px;
    border-top: 1px solid #f1f5f9;
    color: #334155;
    white-space: nowrap;
  }
  #veriTable tbody tr:hover td { background: #f8fafc; }

  /* row colors */
  #veriTable tbody tr.muk-neg td { background: #fff5f5; }
  #veriTable tbody tr.muk-pos td { background: #fffbeb; }
  #veriTable tbody tr.muk-neg:hover td { background: #fee2e2; }
  #veriTable tbody tr.muk-pos:hover td { background: #fef3c7; }

  /* fark badge */
  .fark-badge {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 8px; border-radius: 20px;
    font-size: 11.5px; font-weight: 700;
  }
  .fark-neg { background: #fee2e2; color: #b91c1c; }
  .fark-pos { background: #fef3c7; color: #92400e; }
  .fark-zero { color: #94a3b8; }

  /* DT overrides */
  div.dataTables_wrapper div.dataTables_filter input {
    border: 1.5px solid #e2e8f0; border-radius: 7px;
    padding: 5px 10px; font-size: 12px; color: #1e293b;
    font-family: 'IBM Plex Sans', sans-serif;
  }
  div.dataTables_wrapper div.dataTables_filter input:focus {
    outline: none; border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
  }
  div.dataTables_wrapper div.dataTables_length select {
    border: 1.5px solid #e2e8f0; border-radius: 7px;
    padding: 4px 8px; font-size: 12px;
    font-family: 'IBM Plex Sans', sans-serif;
  }
  div.dataTables_wrapper div.dataTables_info,
  div.dataTables_wrapper div.dataTables_length label,
  div.dataTables_wrapper div.dataTables_filter label { color: #64748b; font-size: 12px; }

  .dataTables_paginate .paginate_button {
    border-radius: 6px !important; border: none !important;
    font-size: 12px !important; color: #475569 !important;
  }
  .dataTables_paginate .paginate_button.current,
  .dataTables_paginate .paginate_button.current:hover {
    background: #2563eb !important; color: #fff !important; border: none !important;
  }
  .dataTables_paginate .paginate_button:hover:not(.current) {
    background: #f1f5f9 !important; color: #1e293b !important;
  }

  /* tfoot search */
  #veriTable tfoot th { padding: 6px 6px; background: #f8fafc; border-top: 1px solid #e2e8f0; }
  #veriTable tfoot input {
    width: 100%; border: 1px solid #e2e8f0; border-radius: 5px;
    padding: 4px 7px; font-size: 11px; color: #334155;
    font-family: 'IBM Plex Mono', monospace; background: #fff;
  }
  #veriTable tfoot input:focus { outline: none; border-color: #3b82f6; }
</style>

<div class="content-wrapper">
  @include('layout.util.evrakContentHeader')
  <section class="content">
    <div class="muk-scope">
      <form id="verilerForm" name="verilerForm">
        @csrf
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

        {{-- ── FİLTRE KARTI ────────────────────── --}}
        <div class="muk-filter-card">

          <div class="muk-section-title">Sayım Listeleri</div>
          <div class="muk-grid">
            @for ($i = 1; $i <= 6; $i++)
              <div class="muk-field">
                <label>Liste {{ $i }}</label>
                <select class="select2" name="MUKAYESE{{ $i }}" id="MUKAYESE{{ $i }}">
                  <option value="">— Seçiniz —</option>
                  @foreach ($sayimListesi as $s)
                    <option value="{{ $s->EVRAKNO }}">{{ $s->EVRAKNO }} · {{ $s->AMBCODE }} · {{ $s->NOT }}</option>
                  @endforeach
                </select>
              </div>
            @endfor
          </div>

          <div class="muk-hr"></div>

          <div class="muk-section-title">Depolar</div>
          <div class="muk-grid">
            @for ($i = 1; $i <= 6; $i++)
              <div class="muk-field">
                <label>Depo {{ $i }}</label>
                <select class="select2" name="DEPO{{ $i }}" id="DEPO{{ $i }}">
                  <option value="">— Seçiniz —</option>
                  @foreach ($depo as $d)
                    <option value="{{ $d->KOD }}">{{ $d->KOD }} · {{ $d->AD }}</option>
                  @endforeach
                </select>
              </div>
            @endfor
          </div>

          <div class="muk-action-row">
            <button type="button" id="btnEksiler" class="muk-btn muk-btn-red" disabled>
              Stoğu Düzelt
            </button>
            
            <button type="button" id="btnHesapla" class="muk-btn muk-btn-blue">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
              Hesapla
            </button>
          </div>
        </div>

        {{-- ── İSTATİSTİKLER ───────────────────── --}}
        <div class="muk-stats" id="statsBar" style="display:none">

          <div class="muk-stat muk-stat-all">
            <div class="muk-stat-val" id="statTotal">0</div>
            <div class="muk-stat-lbl">Toplam Satır</div>
          </div>

          <div class="muk-stat muk-stat-neg">
            <div class="muk-stat-val" id="statNeg">0</div>
            <div class="muk-stat-lbl">Eksik (Fark &lt; 0)</div>
          </div>

          <div class="muk-stat muk-stat-pos">
            <div class="muk-stat-val" id="statPos">0</div>
            <div class="muk-stat-lbl">Fazla (Fark &gt; 0)</div>
          </div>

          {{-- Doğruluk Oranı --}}
          <div class="muk-stat muk-stat-acc" id="statAccCard">
            <div class="muk-stat-acc-inner">
              <div class="muk-ring-wrap">
                <svg viewBox="0 0 64 64" width="64" height="64">
                  <circle class="muk-ring-bg"   cx="32" cy="32" r="28"/>
                  <circle class="muk-ring-fill" id="ringFill" cx="32" cy="32" r="28"
                    stroke="#22c55e"
                    stroke-dasharray="175.93"
                    stroke-dashoffset="175.93"/>
                </svg>
                <div class="muk-ring-pct" id="ringPct">–</div>
              </div>
              <div class="muk-acc-info">
                <div class="muk-acc-val" id="accVal">–</div>
                <div class="muk-acc-state" id="accState" style="color:#94a3b8">Bekleniyor</div>
                <div class="muk-acc-lbl">Doğruluk Oranı</div>
              </div>
            </div>
          </div>

        </div>

        {{-- ── SONUÇ TABLOSU ────────────────────── --}}
        <div class="muk-result-card">
          <div class="muk-loading" id="loadingOverlay">
            <div class="muk-spinner"></div>
            <div class="muk-loading-txt">Hesaplanıyor…</div>
          </div>

          <div style="overflow-x:auto">
            <table id="veriTable" class="table" style="width:100%">
              <thead>
                <tr>
                  <th>Ürün Kodu</th>
                  <th>Depo</th>
                  <th>S. Lot No</th><th>S. Seri No</th>
                  <th>S. Lokasyon 1</th>
                  <th>S. Lokasyon 2</th>
                  <th>S. Lokasyon 3</th>
                  <th>S. Lokasyon 4</th>
                  <th>S. Text1</th><th>S. Text2</th><th>S. Text3</th><th>S. Text4</th>
                  <th>S. Num1</th><th>S. Num2</th><th>S. Num3</th><th>S. Num4</th>
                  <th>SYS Lot No</th><th>SYS Seri No</th>
                  <th>SYS Lokasyon 1</th>
                  <th>SYS Lokasyon 2</th>
                  <th>SYS Lokasyon 3</th>
                  <th>SYS Lokasyon 4</th>
                  <th>SYS Text1</th><th>SYS Text2</th><th>SYS Text3</th><th>SYS Text4</th>
                  <th>SYS Num1</th><th>SYS Num2</th><th>SYS Num3</th><th>SYS Num4</th>
                  <th>Sayım Mik.</th><th>Sistem Mik.</th><th>Fark</th>
                </tr>
              </thead>
              <tfoot>
                <tr>@for($i=0;$i<33;$i++)<th></th>@endfor</tr>
              </tfoot>
              <tbody></tbody>
            </table>
          </div>
        </div>

      </form>
    </div>
  </section>
</div>

<script>
$(document).ready(function () {

  /* tfoot search inputları */
  var headers = [];
  $('#veriTable thead th').each(function(){ headers.push($(this).text()); });
  $('#veriTable tfoot th').each(function(i){
    $(this).html('<input type="text" placeholder="' + headers[i] + '" />');
  });

  /* DataTable */
  var table = $('#veriTable').DataTable({
    dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rtip',
    language: { url: '{{ asset("tr.json") }}' },
    data: [],
    pageLength: 25,
    columns: [
      { data: 'KOD'            },
      { data: 'AMBCODE'        },
      { data: 'LOTNUMBER'      }, { data: 'SERINO' }, 
      { data: 'LOCATION1' },
      { data: 'LOCATION2' },
      { data: 'LOCATION3' },
      { data: 'LOCATION4' },
      { data: 'TEXT1' }, { data: 'TEXT2' }, { data: 'TEXT3' }, { data: 'TEXT4' },
      { data: 'NUM1'  }, { data: 'NUM2'  }, { data: 'NUM3'  }, { data: 'NUM4'  },
      { data: 'OLD_LOTNUMBER'  }, { data: 'OLD_SERINO' }, 
      { data: 'OLD_LOCATION1' },
      { data: 'OLD_LOCATION2' },
      { data: 'OLD_LOCATION3' },
      { data: 'OLD_LOCATION4' },
      { data: 'OLD_TEXT1' }, { data: 'OLD_TEXT2' }, { data: 'OLD_TEXT3' }, { data: 'OLD_TEXT4' },
      { data: 'OLD_NUM1'  }, { data: 'OLD_NUM2'  }, { data: 'OLD_NUM3'  }, { data: 'OLD_NUM4'  },
      { data: 'SAYILAN_MIKTAR' },
      { data: 'SISTEM_MIKTAR'  },
      {
        data: 'FARK',
        render: function(v) {
          var f = parseFloat(v);
          if (f < 0) return '<span class="fark-badge fark-neg">▼ ' + f + '</span>';
          if (f > 0) return '<span class="fark-badge fark-pos">▲ ' + f + '</span>';
          return '<span class="fark-zero">0</span>';
        }
      }
    ],
    createdRow: function(row, data) {
      var f = parseFloat(data.FARK);
      if (f < 0) $(row).addClass('muk-neg');
      else if (f > 0) $(row).addClass('muk-pos');
    },
    initComplete: function() {
      this.api().columns().every(function() {
        var col = this;
        $('input', this.footer()).on('keyup change clear', function() {
          if (col.search() !== this.value) col.search(this.value).draw();
        });
      });
    }
  });

  /* Veri cache */
  var tumVeri = [];

  /* İstatistik */
  function istatistikGuncelle(data) {
    var neg = data.filter(function(r){ return parseFloat(r.FARK) < 0; }).length;
    var pos = data.filter(function(r){ return parseFloat(r.FARK) > 0; }).length;

    $('#statTotal').text(data.length);
    $('#statNeg').text(neg);
    $('#statPos').text(pos);

    /* Doğruluk oranı hesapla */
    var tamam    = (neg === 0 && pos === 0);
    var circumf  = 175.93; /* 2 * π * r = 2 * 3.14159 * 28 */
    var pct, renk, durum;

    if (data.length === 0) {
      /* Sonuç yok — veri bulunamadı anlamı */
      pct = 0; renk = '#94a3b8'; durum = 'Veri Yok';
    } else if (tamam) {
      pct = 100; renk = '#22c55e'; durum = '✓ Tam Doğru';
    } else {
        hataOran = Math.abs((neg - pos) / data.length * 100)
        pct      = Math.round(hataOran);
        renk = '#ef4444'; durum = '✗ Uyuşmazlık Var';
    }

    /* Ring animasyonu */
    var offset = circumf - (pct / 100) * circumf;
    $('#ringFill').css('stroke', renk).css('stroke-dashoffset', offset);
    $('#ringPct').text(pct + '%').css('color', renk);

    /* Kart üst border rengi */
    $('#statAccCard').css('border-top-color', renk);

    /* Büyük değer + durum yazısı */
    $('#accVal').text(pct + '%').css('color', renk);
    $('#accState').text(durum).css('color', renk);

    $('#statsBar').fadeIn(200);
    $('#btnEksiler').prop('disabled', neg === 0);
    $('#btnArtilar').prop('disabled', pos === 0);
  }

  /* HESAPLA */
  $('#btnHesapla').on('click', function() {
    var formData = {
      _token:    $('input[name="_token"]').val(),
      MUKAYESE1: $('#MUKAYESE1').val(), MUKAYESE2: $('#MUKAYESE2').val(),
      MUKAYESE3: $('#MUKAYESE3').val(), MUKAYESE4: $('#MUKAYESE4').val(),
      MUKAYESE5: $('#MUKAYESE5').val(), MUKAYESE6: $('#MUKAYESE6').val(),
      DEPO1: $('#DEPO1').val(), DEPO2: $('#DEPO2').val(),
      DEPO3: $('#DEPO3').val(), DEPO4: $('#DEPO4').val(),
      DEPO5: $('#DEPO5').val(), DEPO6: $('#DEPO6').val(),
    };

    $.ajax({
      url:  '{{ url("sym10_mukayese") }}',
      type: 'POST',
      data: formData,
      beforeSend: function() {
        $('#loadingOverlay').addClass('active');
        $('#btnHesapla').prop('disabled', true);
        $('#btnEksiler, #btnArtilar').prop('disabled', true);
        table.clear().draw();
        tumVeri = [];
        $('#statsBar').hide();
      },
      success: function(response) {
        tumVeri = response;
        table.clear().rows.add(response).draw();
        istatistikGuncelle(response);
      },
      error: function(xhr) {
        var msg = xhr.responseJSON?.error ?? 'Bir hata oluştu.';
        alert('Hata: ' + msg);
      },
      complete: function() {
        $('#loadingOverlay').removeClass('active');
        $('#btnHesapla').prop('disabled', false);
      }
    });
  });

  $('#btnEksiler').on('click', function() {
    backendGonder(tumVeri, 'eksiler');
  });

  $('#btnArtilar').on('click', function() {
    var filtreli = tumVeri.filter(function(r){ return parseFloat(r.FARK) > 0; });
    backendGonder(filtreli, 'artilar');
  });

  function backendGonder(data, tip) {
    if (!data.length) { alert('Gönderilecek satır bulunamadı.'); return; }
    $.ajax({
      url:         '{{ url("sym10_mukayese_duzenle") }}',
      type:        'POST',
      contentType: 'application/json',
      data:        JSON.stringify({
        _token:   $('input[name="_token"]').val(),
        tip:      tip,
        satirlar: data
      }),
      success: function(res) {
        swal.fire('Bilgi', 'İşlem başarıyla gönderildi.', 'success');
        setTimeout(() => {
          location.reload();
        }, 1000);
      },
      error: function(xhr) {
        var msg = xhr.responseJSON?.error ?? 'Bir hata oluştu.';
        alert('Hata: ' + msg);
      }
    });
  }

});
</script>
@endsection