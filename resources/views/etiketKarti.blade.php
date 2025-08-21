@extends('layout.mainlayout')

@php
  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";
  
  $ekran = "ETKTKART";
  $ekranRumuz = "ETIKETKARTI";
  $ekranAdi = "Etiket Kartı";
  $ekranLink = "etiketKarti";
  $ekranTableE = "D7KIDSLB";
  // $data = session('etiket_data');
  // dd($data);
@endphp

@section('content')
  <style>
    /* Genel Stil */
    .content-wrapper {
      background-color: #f4f6f9;
      padding: 20px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Butonlar */
    .action-buttons {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .btn-custom {
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
      color: white;
    }

    .btn-primary:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }

    /* Kart Stilleri */
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, 350px);
      gap: 20px;
      padding: 10px;
      justify-content: center;
    }

    .dynamic-label {
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .dynamic-label:hover {
      transform: scale(1.02);
    }

    .selected-label .card {
      box-shadow: 0 0 15px rgba(60, 141, 188, 0.5), inset 0 0 10px rgba(60, 141, 188, 0.2);
      background-color: #e9f7ff;
      border: 2px solid #3c8dbc;
    }

    .card {
      width: 350px;
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      border: 1px solid #dee2e6;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .card-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 15px;
    }

    .card-title {
      text-align: center;
      font-size: 1.5rem;
      font-weight: 700;
      color: #343a40;
      margin: 10px 0;
    }

    .divider {
      height: 3px;
      background-color: #343a40;
      margin: 10px 0;
    }

    .card-content {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      font-size: 0.95rem;
      color: #495057;
    }

    /* Mobil Uyumluluk */
    @media (max-width: 768px) {
      .card-grid {
        grid-template-columns: 350px;
      }

      .action-buttons {
        flex-direction: column;
        align-items: stretch;
      }

      .btn-custom {
        width: 100%;
        text-align: center;
      }
    }
  </style>

  <div class="content-wrapper">
    @include('layout.util.evrakContentHeader')
    <section class="content">
      <div class="action-buttons">
        <a href="{{ $data['ID'] ?? $ID }}" type="button" class="btn btn-custom btn-primary" ><i class="fa-solid fa-arrow-left"></i> Geri Dön</a>
        <button type="button" class="btn btn-custom btn-primary" id="yazdir">Yazdır</button>
      </div>
      <div class="card-grid" id="yazdirilicak">
        @php
          $firma_bilgileri = DB::table('FIRMA_TANIMLARI')
          ->where('FIRMA',$kullanici_veri->firma)
          ->first();
          $count = isset($data['KOD']) && is_array($data['KOD']) ? count($data['KOD']) : 0;
          for ($i = 0; $i < $count; $i++) {
        @endphp
          <input type="checkbox" name="secilenler[]" id="card-{{ $i }}" value="{{ $i }}" class="hidden-checkbox" style="display: none">
          <label for="card-{{ $i }}" class="dynamic-label">
            <div class="card">
              <div class="card-header">
                <img src="{{ asset($firma_bilgileri->LOGO_URL ?? 'assets/img/qzu_logo.png') }}" alt="Üntel Logo" width="50px">
                <b>{{ $firma_bilgileri->FIRMA_ADI ?? 'ETİKET KARTI' }}</b>
              </div>
              <h3 class="card-title">Hammadde Tanıtım Kartı</h3>
              <div class="divider"></div>
              <div style="display: grid; grid-template-columns: max-content 1fr; gap: 8px 20px; max-width: 600px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #222;">

                <div style="font-weight: 700; color: #555;">Kod:</div>
                <div style="overflow: hidden; text-overflow: ellipsis;">{{ $data['KOD'][$i] ?? '' }}</div>

                <div style="font-weight: 700; color: #555;">Stok Adı:</div>
                <div style="overflow: hidden; text-overflow: ellipsis;">{{ $data['STOK_ADI'][$i] ?? '' }}</div>

                <div style="font-weight: 700; color: #555;" class="{{ $data['MPS_BILGISI'][$i]->MUSTERIKODU ?? 'd-none' }}">Müşteri:</div>
                <div style="overflow: hidden; text-overflow: ellipsis;" class="{{ $data['MPS_BILGISI'][$i]->MUSTERIKODU ?? 'd-none' }}">
                  {{ ($data['MPS_BILGISI'][$i]->MUSTERIKODU ?? '') . ' - ' . ($data['MPS_BILGISI'][$i]->AD ?? '') }}
                </div>

                <div style="font-weight: 700; color: #555;" class="{{ $data['MPS_BILGISI'][$i]->SIPNO ?? 'd-none' }}">Sipariş No:</div>
                <div style="overflow: hidden; text-overflow: ellipsis;" class="{{ $data['MPS_BILGISI'][$i]->SIPNO ?? 'd-none' }}">{{ $data['MPS_BILGISI'][$i]->SIPNO ?? '' }}</div>

                <div style="font-weight: 700; color: #555;">Miktar:</div>
                <div>{{ $data['MIKTAR'][$i] ?? '' }}</div>

                <div style="font-weight: 700; color: #555;">Tarih:</div>
                <div>{{ $data['TARIH'] ?? '' }}</div>

              </div>

              @if($data['SERINO'][$i] == NULL)
              @php
                $NEWSERINO = DB::table($database.'D7KIDSLB')->max('id');
                $NEWSERINO++;
              @endphp
              <svg class="barcode" style="margin:auto;" data-value="{{ str_pad($NEWSERINO, 12, '0', STR_PAD_LEFT) ?? '' }}"></svg>
              @else
              <svg class="barcode" style="margin:auto;" data-value="{{ str_pad($data['SERINO'][$i], 12, '0', STR_PAD_LEFT) ?? '' }}"></svg>
              @endif
            </div>
          </label>
        @php
          }
        @endphp
      </div>
    </section>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

  <!-- Barkod Oluşturma -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      document.querySelectorAll(".barcode").forEach(barcode => {
        JsBarcode(barcode, barcode.dataset.value, {
          format: "CODE128",
          width: 2.5,
          height: 40,
          displayValue: true,
          background: "#ffffff",
          lineColor: "#343a40"
        });
      });
    });
  </script>

  <!-- Yazdırma İşlemi -->
  <script>
    document.getElementById("yazdir").addEventListener("click", function () {
      const checkboxes = document.querySelectorAll('.hidden-checkbox:checked');
      const allLabels = document.querySelectorAll('.dynamic-label');
      let labelsToPrint = checkboxes.length > 0 ? Array.from(checkboxes).map(cb => cb.nextElementSibling) : Array.from(allLabels);

      const printWindow = window.open("", "_blank");
      printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
          <title>Etiket Yazdır</title>
          <style>
            body {
              margin: 0;
              padding: 10px;
              font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .card-grid {
              display: flex;
              flex-wrap:wrap;
              gap: 20px;
              justify-content: center;
            }
            .card {
              width: 350px;
              background: white;
              border-radius: 10px;
              padding: 20px;
              border: 1px solid #dee2e6;
              box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
              page-break-inside: avoid;
            }
            .card-header {
              display: flex;
              align-items: center;
              gap: 10px;
              margin-bottom: 15px;
            }
            .card-title {
              text-align: center;
              font-size: 1.5rem;
              font-weight: 700;
              color: #343a40;
              margin: 10px 0;
            }
            .divider {
              height: 3px;
              background: #343a40;
              margin: 10px 0;
            }
            .card-content {
              display: grid;
              grid-template-columns: 1fr 1fr;
              gap: 10px;
              font-size: 0.95rem;
              color: #495057;
            }
          </style>
        </head>
        <body>
          <div class="card-grid">
            ${labelsToPrint.map(label => label.outerHTML).join('')}
          </div>
        </body>
        </html>
      `);
      printWindow.document.close();
      printWindow.print();
      printWindow.onafterprint = () => printWindow.close();
    });
  </script>

  <!-- Seçme İşlemi -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const checkboxes = document.querySelectorAll('.hidden-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
          const label = this.nextElementSibling;
          label.classList.toggle('selected-label', this.checked);
        });
      });
    });
  </script>
@endsection