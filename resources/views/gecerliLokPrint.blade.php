<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barkod Etiketleri</title>
    <style>
        /* ── PREVIEW ──────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #e8e8e8;
            font-family: 'Courier New', monospace;
            padding: 32px;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
            align-items: flex-start;
        }

        .label-card {
            width:  188px;   /* 50mm @ 96dpi */
            height: 113px;   /* 30mm @ 96dpi */
            background: #fff;
            border: 1px solid #c0c0c0;
            border-radius: 3px;
            box-shadow: 0 1px 4px rgba(0,0,0,.14);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4px 6px 3px;
            gap: 1px;
            overflow: hidden;
        }

        .label-card svg.barcode {
            width: 100%;
            height: 72px;
            display: block;
        }

        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: .5px;
            color: #111;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            line-height: 1.2;
        }

        /* ── PRINT ────────────────────────────────── */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
                display: block;
            }

            @page {
                size: 50mm 30mm;
                margin: 0;
            }

            .label-card {
                width:  50mm;
                height: 30mm;
                border: none;
                border-radius: 0;
                box-shadow: none;
                padding: 1.5mm 2mm 1mm;
                gap: 0;
                page-break-after: always;
                break-after: page;
            }

            .label-card svg.barcode {
                width: 100%;
                height: 20mm;
            }

            .barcode-text {
                font-size: 6.5pt;
                letter-spacing: .4px;
            }
        }

        /* loading */
        #loading {
            width: 100%;
            text-align: center;
            padding: 40px;
            font-family: sans-serif;
            color: #555;
        }
        .hidden { display: none !important; }
    </style>
</head>
<body>

    <div id="loading">Barkodlar oluşturuluyor…</div>

    {{-- ── LABEL LOOP ─────────────────────── --}}
    @for ($i = 0; $i < count($AMBCODE); $i++)
        @php
            $loc = collect([$LOCATION1[$i] ?? '', $LOCATION2[$i] ?? '', $LOCATION3[$i] ?? '', $LOCATION4[$i] ?? ''])
                       ->filter()->implode('-');
            $barcodeValue = '--' . $AMBCODE[$i] . ($loc ? '-' . $loc : '');
            $displayText  = $AMBCODE[$i] . ($loc ? ' ' . str_replace('-', ' ', $loc) : '');
        @endphp

        <div class="label-card hidden">
            <svg class="barcode" id="bc-{{ $i }}" data-value="{{ $barcodeValue }}"></svg>
            <div class="barcode-text">{{ $displayText }}</div>
        </div>
    @endfor

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loading = document.getElementById('loading');
            const cards   = document.querySelectorAll('.label-card');

            cards.forEach((card, i) => {
                const svg = card.querySelector('svg.barcode');
                try {
                    JsBarcode(svg, svg.dataset.value, {
                        format:       'CODE128',
                        width:        1.05,
                        height:       52,
                        displayValue: false,
                        background:   'transparent',
                        lineColor:    '#000',
                        margin:       0,
                    });
                } catch (e) {
                    svg.innerHTML = `<text x="50%" y="50%" text-anchor="middle"
                        fill="#c00" font-size="10">ERR: ${e.message}</text>`;
                }
                card.classList.remove('hidden');
            });

            loading.style.display = 'none';

            if (cards.length === 0) {
                loading.textContent = 'Barkod verisi bulunamadı.';
                loading.style.display = 'block';
                return;
            }

            /* 1.5s sonra yazdır */
            setTimeout(() => window.print(), 1500);
        });
    </script>
</body>
</html>