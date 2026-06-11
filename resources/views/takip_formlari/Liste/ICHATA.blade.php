@php
    $toplamHataSayisi = count($veri);
    $hataSayilari = $veri->groupBy('ich_fault_code')->map->count();
    $toplamAdet = $veri->sum('ich_quantity');
    $benzersizHataKoduSayisi = $hataSayilari->count();
@endphp

<style>
.ich-card { background: var(--bs-white); border: 1px solid #e9ecef; border-radius: 12px; overflow: hidden; }
.ich-card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-bottom: 1px solid #e9ecef; }
.ich-head-icon { width: 36px; height: 36px; background: #FCEBEB; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #A32D2D; flex-shrink: 0; }
.ich-kpis { display: grid; grid-template-columns: repeat(4, 1fr); border-bottom: 1px solid #e9ecef; }
.ich-kpi { padding: 14px 16px; text-align: center; border-right: 1px solid #e9ecef; }
.ich-kpi:last-child { border-right: none; }
.ich-kpi-val { font-size: 1.5rem; font-weight: 700; line-height: 1.2; }
.ich-kpi-lbl { font-size: 10px; color: #6c757d; margin-top: 3px; text-transform: uppercase; letter-spacing: .05em; }
.ich-table thead th { padding: 10px 14px; font-size: 11px; font-weight: 600; color: #6c757d; background: #f8f9fa; border-bottom: 1px solid #e9ecef; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }
.ich-table td { padding: 10px 14px; vertical-align: middle; font-size: 12px; border-bottom: 1px solid #f3f3f3; }
.ich-table tbody tr:last-child td { border-bottom: none; }
.ich-table tbody tr:hover { background: rgba(0,0,0,.015); }
.ich-part-code { font-family: monospace; background: #f1f3f5; border: 1px solid #dee2e6; border-radius: 5px; padding: 2px 7px; font-size: 11px; white-space: nowrap; }
.ich-avatar { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; flex-shrink: 0; background: #EEEDFE; color: #3C3489; }
.ich-trunc { max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #495057; display: block; }
.ich-fault-badge { display: inline-block; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 5px; white-space: nowrap; }
.ich-pbar-bg { flex: 1; height: 5px; background: #e9ecef; border-radius: 3px; overflow: hidden; min-width: 50px; }
.ich-pbar-fill { height: 100%; border-radius: 3px; }
.ich-pct-text { font-size: 11px; font-weight: 700; min-width: 45px; text-align: right; white-space: nowrap; }
.ich-pct-ratio { font-size: 10px; color: #adb5bd; }
</style>

<div class="ich-card shadow-sm">

    {{-- Başlık --}}
    <div class="ich-card-head">
        <div class="d-flex align-items-center gap-2">
            <div class="ich-head-icon">
                <i class="fas fa-exclamation-triangle" style="font-size:15px;"></i>
            </div>
            <div>
                <div class="fw-bold" style="font-size:14px;">İç Hata Kayıtları</div>
                <div class="text-muted" style="font-size:12px;">{{ $toplamHataSayisi }} kayıt bulundu</div>
            </div>
        </div>
        <button class="btn btn-sm btn-success d-flex align-items-center gap-2 px-3"
                onclick="exportTableToExcel('example2')"
                style="font-size:12px; border-radius:8px;">
            <i class="fas fa-file-excel"></i> Excel'e Aktar
        </button>
    </div>

    {{-- KPI Kartlar --}}
    <div class="ich-kpis">
        <div class="ich-kpi">
            <div class="ich-kpi-val" style="color:#E24B4A;">{{ $toplamHataSayisi }}</div>
            <div class="ich-kpi-lbl"><i class="fas fa-list-ul me-1"></i>Toplam Kayıt</div>
        </div>
        <div class="ich-kpi">
            <div class="ich-kpi-val" style="color:#BA7517;">{{ $toplamAdet }}</div>
            <div class="ich-kpi-lbl"><i class="fas fa-boxes me-1"></i>Hatalı Adet</div>
        </div>
        <div class="ich-kpi">
            <div class="ich-kpi-val" style="color:#185FA5;">{{ $benzersizHataKoduSayisi }}</div>
            <div class="ich-kpi-lbl"><i class="fas fa-tags me-1"></i>Hata Kodu Çeşidi</div>
        </div>
        @if(in_array('SSF', $kullanici_read_yetkileri))
        <div class="ich-kpi">
            <div class="ich-kpi-val" style="color:#3B6D11;" id="ich-kpi-maliyet">—</div>
            <div class="ich-kpi-lbl"><i class="fas fa-lira-sign me-1"></i>Toplam Maliyet</div>
        </div>
        @endif
    </div>

    {{-- Tablo --}}
    <div class="table-responsive">
        <table id="example2" class="table table-hover ich-table mb-0" data-page-length="500">
            <thead>
                <tr>
                    <th>Parça No</th>
                    <th>Operatör</th>
                    <th>Problem Tanımı</th>
                    <th>Kök Neden</th>
                    <th>Düzeltici Faaliyet</th>
                    <th>Açıklama</th>
                    <th class="text-center">Adet</th>
                    <th class="text-center">Tarih</th>
                    @if(in_array('SSF', $kullanici_read_yetkileri))
                        <th class="text-end">Fiyat</th>
                    @endif
                    <th class="text-center">Hata Kodu</th>
                    <th>Yüzdelik Oran</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($veri as $item)
                @php
                    $name = DB::table($database.'pers00')->where('KOD', $item->ich_operator)->value('AD');

                    $fiyat_listesi = DB::table($database.'stok48t')
                        ->where('KOD', $item->ich_part_code)
                        ->where('GECERLILIK_TAR', '<=', \Carbon\Carbon::parse($item->ich_date))
                        ->orderBy('GECERLILIK_TAR', 'desc')
                        ->first();

                    $fiyat = 0;
                    if (isset($fiyat_listesi)) {
                        $tarih = date('Y/m/d', strtotime($item->ich_date));
                        $kur = DB::table($database.'excratt')
                            ->where('CODEFROM', $fiyat_listesi->PRICE_UNIT ?? 'TL')
                            ->where('EVRAKNOTARIH', $tarih)
                            ->first();
                        if (isset($fiyat_listesi->PRICE_UNIT) && $fiyat_listesi->PRICE_UNIT == 'TL') {
                            $fiyat = $fiyat_listesi->PRICE * $item->ich_quantity;
                        } else {
                            $fiyat = ($fiyat_listesi->PRICE ?? 0) * $item->ich_quantity * ($kur->KURS_1 ?? 1);
                        }
                    }

                    $buHataninAdedi = $hataSayilari[$item->ich_fault_code] ?? 0;
                    $yuzde = $toplamHataSayisi > 0 ? round(($buHataninAdedi / $toplamHataSayisi) * 100, 2) : 0;

                    // Renge göre yüzde eşikleri: %30+ kırmızı, %15+ sarı, %5+ mavi, altı gri
                    if ($yuzde >= 30) {
                        $badgeStyle  = 'background:#FCEBEB;color:#A32D2D;border:1px solid #F7C1C1;';
                        $pbarColor   = '#E24B4A';
                        $pctColor    = '#A32D2D';
                    } elseif ($yuzde >= 15) {
                        $badgeStyle  = 'background:#FAEEDA;color:#854F0B;border:1px solid #FAC775;';
                        $pbarColor   = '#EF9F27';
                        $pctColor    = '#854F0B';
                    } elseif ($yuzde >= 5) {
                        $badgeStyle  = 'background:#E6F1FB;color:#185FA5;border:1px solid #B5D4F4;';
                        $pbarColor   = '#378ADD';
                        $pctColor    = '#185FA5';
                    } else {
                        $badgeStyle  = 'background:#f1f3f5;color:#495057;border:1px solid #dee2e6;';
                        $pbarColor   = '#adb5bd';
                        $pctColor    = '#6c757d';
                    }

                    $initials = strtoupper(substr($item->ich_operator, 0, 2));
                @endphp
                <tr>
                    <td>
                        <span class="ich-part-code">{{ $item->ich_part_code }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="ich-avatar">{{ $initials }}</div>
                            <div>
                                <div class="fw-semibold" style="font-size:12px;">{{ $name ?? '—' }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $item->ich_operator }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="ich-trunc" title="{{ $item->ich_problem }}">{{ $item->ich_problem }}</span>
                    </td>
                    <td>
                        <span class="ich-trunc" title="{{ $item->ich_rootcause }}">{{ $item->ich_rootcause }}</span>
                    </td>
                    <td>
                        <span class="ich-trunc" title="{{ $item->ich_corrective }}">{{ $item->ich_corrective }}</span>
                    </td>
                    <td>
                        <span class="ich-trunc" title="{{ $item->ich_description }}">{{ $item->ich_description }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill bg-secondary bg-opacity-10 text-dark fw-semibold"
                              style="font-size:11px;">{{ $item->ich_quantity }}</span>
                    </td>
                    <td class="text-center text-nowrap" style="color:#495057;">
                        {{ \Carbon\Carbon::parse($item->ich_date)->format('d.m.Y') }}
                    </td>
                    @if(in_array('SSF', $kullanici_read_yetkileri))
                    <td class="text-end text-nowrap" data-fiyat="{{ $fiyat ?? 0 }}">
                        <span style="font-weight:600; color:#3B6D11; font-size:12px;">
                            {{ number_format($fiyat ?? 0, 2, ',', '.') }} ₺
                        </span>
                    </td>
                    @endif
                    <td class="text-center">
                        <span class="ich-fault-badge" style="{{ $badgeStyle }}">
                            {{ $item->ich_fault_code }}
                        </span>
                    </td>
                    <td style="min-width:180px;" data-order="{{ $yuzde }}">
                        <div class="d-flex align-items-center gap-2">
                            <div class="ich-pbar-bg">
                                <div class="ich-pbar-fill"
                                     style="width:{{ min($yuzde, 100) }}%; background:{{ $pbarColor }};"></div>
                            </div>
                            <div class="ich-pct-text" style="color:{{ $pctColor }};">
                                %{{ $yuzde }}
                            </div>
                            <span class="ich-pct-ratio">({{ $buHataninAdedi }}/{{ $toplamHataSayisi }})</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if(in_array('SSF', $kullanici_read_yetkileri))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cells = document.querySelectorAll('[data-fiyat]');
        let toplam = 0;
        cells.forEach(c => { toplam += parseFloat(c.dataset.fiyat) || 0; });
        const el = document.getElementById('ich-kpi-maliyet');
        if (el) el.textContent = toplam.toLocaleString('tr-TR', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        }) + ' ₺';
    });
</script>
@endif