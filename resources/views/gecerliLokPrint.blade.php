<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barkod Etiketleri</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .labels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }


        .label-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f3f4;
        }

        .label-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }

        .label-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3748;
        }

        .barcode-container {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
        }

        .barcode {
            max-width: 100%;
            height: auto;
        }

        .barcode-info {
            /* margin-top: 15px; */
            /* padding: 10px;
            background: linear-gradient(135deg, #667eea10, #764ba210);
            border-radius: 8px;
            border-left: 4px solid #667eea; */
        }

        .barcode-text {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            /* color: #4a5568; */
            /* font-size: 45px; */
            word-break: break-all;
        }

        .control-panel {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .print-btn {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(72, 187, 120, 0.4);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.6);
        }

        .print-btn:active {
            transform: translateY(0);
        }

        .loading {
            display: block;
            text-align: center;
            margin: 40px 0;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255,255,255,0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .labels-grid.hidden {
            display: none;
        }

        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
                overflow: visible;
            }
            
            .header, .control-panel, .loading {
                display: none !important;
            }
            
            .container {
                max-width: none;
                margin: 0;
                padding: 0;
            }
            
            .labels-grid {
                display: block !important; /* Grid'i komple kaldır, block yap */
            }
            
            .label-card {
                /* transform: rotate(90deg); */
                width: 30mm !important;   /* ⬅️ ters */
                height: 15mm !important;  /* ⬅️ ters */
                border: 1px solid #ddd;
                box-sizing: border-box;
                page-break-after: always;
                break-after: page;
            }
            @page {
                size: 50mm 30mm;   /* ETİKETİN GERÇEK BOYUTU */
                margin: 0;         /* ÇOK KRİTİK */
            }

            body {
                margin: 0;
                padding: 0;
            }
            
            .label-card:first-child {
                page-break-before: avoid; /* İlk etikette sayfa başı koyma */
                break-before: auto; /* Modern tarayıcılar için */
            }
            
            .barcode-container {
                background: white;
                border: 1px solid #ddd;
                width: 100%;
                text-align: center;
                padding: 10px;
            }
            
            .barcode {
                max-width: 100%;
                height: auto;
            }
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .labels-grid {
                grid-template-columns: 1fr;
            }
            
            .control-panel {
                position: relative;
                top: auto;
                right: auto;
                text-align: center;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="control-panel">
        <button class="print-btn" onclick="window.print()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
            </svg>
            Yazdır
        </button>
    </div>

    <div class="container">
        <div class="header">
            <h1>Barkod Etiketleri</h1>
        </div>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Barkodlar oluşturuluyor...</p>
        </div>

        <div class="labels-grid hidden" id="labelsGrid">
            @for ($i = 0; $i < count($AMBCODE); $i++)
                <div class="label-card" style="page-break-before: always; text-align: center; break-before: page; page-break-after: always; break-after: page;">
                    <svg class="barcode" id="barcode-{{ $i }}"
                        data-value="--{{ 
                            $AMBCODE[$i]
                            . (!empty($LOCATION1[$i]) ? '-' . $LOCATION1[$i] : '')
                            . (!empty($LOCATION2[$i]) ? '-' . $LOCATION2[$i] : '')
                            . (!empty($LOCATION3[$i]) ? '-' . $LOCATION3[$i] : '')
                            . (!empty($LOCATION4[$i]) ? '-' . $LOCATION4[$i] : '')
                        }}">
                    </svg>
                    <div class="barcode-info">
                        <div class="barcode-text">{{ 
                            $AMBCODE[$i]
                            . (!empty($LOCATION1[$i]) ? ' ' . trim($LOCATION1[$i]) : '')
                            . (!empty($LOCATION2[$i]) ? ' ' . trim($LOCATION2[$i]) : '')
                            . (!empty($LOCATION3[$i]) ? ' ' . trim($LOCATION3[$i]) : '')
                            . (!empty($LOCATION4[$i]) ? ' ' . trim($LOCATION4[$i]) : '')
                        }}</div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loading = document.getElementById('loading');
            const grid = document.getElementById('labelsGrid');
            
            // Barkodları oluştur
            setTimeout(() => {
                const barcodes = document.querySelectorAll(".barcode");
                let processedCount = 0;
                
                barcodes.forEach((barcode, index) => {
                    const value = barcode.dataset.value;
                    
                    try {
                        JsBarcode(barcode, value, {
                            format: "CODE128",
                            width: 0.8,        // ⬅️ kritik
                            height: 22,        // ⬅️ kritik          
                            displayValue: false,
                            background: "transparent",
                            lineColor: "#2d3748",
                            fontSize: 14,
                            textMargin: 8,
                            textAlign: "center"
                        });
                    } catch (error) {
                        console.error(`Barkod oluşturma hatası ${index}:`, error);
                        barcode.innerHTML = `<text x="50%" y="50%" text-anchor="middle" fill="#dc3545">Hata: ${error.message}</text>`;
                    }
                    
                    processedCount++;
                    
                    // Tüm barkodlar işlendiğinde
                    if (processedCount === barcodes.length) {
                        loading.style.display = 'none';
                        grid.classList.remove('hidden');
                        
                        // 2 saniye sonra otomatik yazdırma
                        setTimeout(() => {
                            window.print();
                        }, 2000);
                    }
                });
                
                // Eğer hiç barkod yoksa
                if (barcodes.length === 0) {
                    loading.style.display = 'none';
                    grid.innerHTML = `
                        <div style="text-align: center; color: white; grid-column: 1/-1; padding: 40px;">
                            <h2>Barkod verisi bulunamadı</h2>
                            <p>Lütfen veri dizilerini kontrol edin.</p>
                        </div>
                    `;
                    grid.classList.remove('hidden');
                }
            }, 500);
        });
        window.addEventListener('beforeprint', () => {
            document.body.style.overflow = 'visible';
        });

        window.addEventListener('afterprint', () => {
            document.body.style.overflow = 'auto';
        });

        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>