@section('evrakIslemleri')
<style>
  .evrak-toolbar {
    /* background: linear-gradient(to bottom, #ffffff, #f8f9fa); */
  }
  
  .evrak-actions {
    display: flex;
    align-items: center;
    gap: 5px;
  }
  
  .action-group {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 0 12px;
    border-right: 1px solid #dee2e6;
  }
  
  .action-group:first-child {
    padding-left: 0;
  }
  
  .action-group:last-child {
    border-right: none;
    padding-right: 0;
  }
  
  .evrak-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 6px;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    text-decoration: none;
  }
  
  .evrak-btn:hover {
    background: rgba(0,0,0,0.05);
    transform: translateY(-1px);
  }
  
  .evrak-btn:active {
    transform: translateY(0);
  }
  
  .evrak-btn i {
    font-size: 20px;
  }
  
  .evrak-btn.nav-btn i {
    color: #0d6efd;
  }
  
  .evrak-btn.save-btn i {
    color: #198754;
  }
  
  .evrak-btn.cancel-btn i {
    color: #fd7e14;
  }
  
  .evrak-btn.new-btn i {
    color: #6f42c1;
  }
  
  .evrak-btn.delete-btn i {
    color: #dc3545;
  }
  
  .evrak-btn.info-btn i {
    color: #0dcaf0;
  }
  
  .evrak-btn.log-btn i {
    color: #6c757d;
  }
  
  .evrak-btn.print-btn i {
    color: #198754;
  }
  
  .evrak-btn:disabled,
  .evrak-btn[href="#"] {
    opacity: 0.3;
    cursor: not-allowed;
  }
  
  .evrak-btn:disabled:hover,
  .evrak-btn[href="#"]:hover {
    background: transparent;
    transform: none;
  }
  
  /* Tooltip */
  .evrak-btn::after {
    content: attr(title);
    position: absolute;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.85);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    z-index: 10000;
  }
  
  .evrak-btn:hover::after {
    opacity: 1;
  }
  
  /* Hidden elements için display kontrolü */
  [style*="display: none"] {
    display: none !important;
  }
</style>

<div class="evrak-toolbar">
  <div class="evrak-actions">
    <!-- Navigasyon Butonları -->
    <div class="action-group">
      <a data-evrak-kontrol 
         href="@php if (isset($ilkEvrak)) { echo $ekranLink.'?ID='.$ilkEvrak; } else { echo '#'; } @endphp" 
         class="evrak-btn nav-btn" 
         title="İlk Kart">
        <i class="fa fa-angle-double-left"></i>
      </a>
      
      <a data-evrak-kontrol 
         href="@php if (isset($oncekiEvrak)) { echo $ekranLink.'?ID='.$oncekiEvrak; } else { echo '#'; } @endphp" 
         class="evrak-btn nav-btn" 
         title="Önceki Kart">
        <i class="fa fa-angle-left"></i>
      </a>
      
      <a data-evrak-kontrol 
         href="@php if (isset($sonrakiEvrak)) { echo $ekranLink.'?ID='.$sonrakiEvrak; } else { echo '#'; } @endphp" 
         class="evrak-btn nav-btn" 
         title="Sonraki Kart">
        <i class="fa fa-angle-right"></i>
      </a>
      
      <a data-evrak-kontrol 
         href="@php if (isset($sonEvrak)) { echo $ekranLink.'?ID='.$sonEvrak; } else { echo '#'; } @endphp" 
         class="evrak-btn nav-btn" 
         title="Son Kart">
        <i class="fa fa-angle-double-right"></i>
      </a>
    </div>

    @if(in_array($ekran, $kullanici_write_yetkileri))
      <!-- Yeni Kart Oluşturma Butonları -->
      <div class="action-group">
        <div id="kartOlustur" name="kartOlustur" style="display: none;">
          <a href="javascript:void(0)" 
             class="evrak-btn save-btn" 
             id="kartOlusturBtn" 
             title="Kaydet"
             data-bs-toggle="modal" 
             onclick="evrakIslemleri('evrakKaydet',{{ $ekranKayitSatirKontrol }})">
            <i class="fa fa-save"></i>
          </a>
        </div>
        
        <div id="kartOlustur2" name="kartOlustur2" style="display: none;">
          <a href="javascript:void(0)" 
             class="evrak-btn cancel-btn" 
             title="Vazgeç"
             onclick="buttonRollback2('{{ $ekranLink }}')">
            <i class="fa-solid fa-square-xmark"></i>
          </a>
        </div>
      </div>

      <!-- Düzenleme ve Kaydetme Butonları -->
      <div class="action-group">
        <div id="kartDuzenleme" name="kartDuzenle">
          <a href="javascript:void(0)" 
            id="kartDuzenle" 
            class="evrak-btn new-btn" 
            title="Yeni Kart"
            onclick="inputTemizle2(); if (typeof ozelInput === 'function') { ozelInput(); }">
            <i class="fa-solid fa-file-circle-plus"></i>
          </a>
        </div>
        
        <div id="kartKopyalama" name="kartDuzenle">
          <a href="javascript:void(0)"
            id="kartKopyala"
            class="evrak-btn new-btn"
            title="Kopyasını Oluştur"
            onclick="kartKopyala(); if (typeof ozelInput === 'function') { ozelInput(); }">
            <i class="fa-solid fa-copy"></i>
          </a>
        </div>

        <div id="kartDuzenle2" name="kartDuzenle2">
          <a href="javascript:void(0)" 
             class="evrak-btn save-btn" 
             id="kartDuzenle2Btn" 
             title="Güncelle"
             data-bs-toggle="modal" 
             onclick="evrakIslemleri('evrakDuzenle',{{ $ekranKayitSatirKontrol }})">
            <i class="fa fa-save"></i>
          </a>
        </div>
      </div>
    @endif

    @if(in_array($ekran, $kullanici_delete_yetkileri))
      <!-- Silme Butonu -->
      <div class="action-group">
        <div id="kartDuzenle3" name="kartDuzenle3">
          <a href="javascript:void(0)" 
             class="evrak-btn delete-btn" 
             title="Sil"
             data-bs-toggle="modal" 
             onclick="evrakIslemleri('evrakSil')">
            <i class="fa fa-trash"></i>
          </a>
        </div>
      </div>
    @endif

    <!-- Bilgi ve Log Butonları -->
    <div class="action-group">
      <div id="log" name="log">
        <a class="evrak-btn info-btn" 
           data-bs-toggle="modal" 
           data-bs-target="#info"
           title="Bilgi">
          <i class="fa-solid fa-circle-info"></i>
        </a>
      </div>
      
      <div id="log" name="log">
        <a class="evrak-btn log-btn" 
           data-bs-toggle="modal" 
           data-bs-target="#log"
           title="Geçmiş">
          <i class="fa-solid fa-clock-rotate-left"></i>
        </a>
      </div>
      
      <div id="yazdir" name="yazdir" style="display: none;">
        <button style="border:none; outline:none; background: transparent; padding: 0;" 
                type="submit" 
                class="evrak-btn print-btn smbButton" 
                title="Yazdır" 
                name="kart_islemleri" 
                id="smbButton" 
                value="yazdir">
          <i class="fa fa-print"></i>
        </button>
      </div>

      <div id="mail" style="display: none;">
        <button style="border:none; outline:none; background: transparent; padding: 0;" 
                type="submit" 
                class="evrak-btn log-btn smbButton" 
                title="Mail Gönder" 
                name="kart_islemleri" 
                id="mailButton" 
                value="send_mail">
              <i class="fa-solid fa-envelope-circle-check"></i>
        </button>
        <button style="border:none; outline:none; background: transparent; padding: 0;" 
                type="submit" 
                class="evrak-btn log-btn smbButton" 
                title="PDF indir" 
                name="kart_islemleri" 
                id="mailButton" 
                value="download_btn">
              <i class="fa-solid fa-download"></i>
        </button>
      </div>

      <!-- <div id="" name="">
        <a class="evrak-btn log-btn" 
           data-bs-toggle="modal" 
           data-bs-target="#"
           title="Geri Dönüşüm Kutusu">
           <i class="fa-solid fa-recycle"></i>
        </a>
      </div> -->

    </div>
  </div>
</div>

<!-- Gizli Form Butonları -->
<div style="display: none">
  <button type="submit" class="btn btn-outline" name="kart_islemleri" id="evrakKaydet" value="kart_olustur">Kaydet</button>
  <button type="submit" class="btn btn-outline" name="kart_islemleri" id="evrakDuzenle" value="kart_duzenle">Kaydet</button>
  <button type="submit" class="btn btn-outline" name="kart_islemleri" id="evrakSil" value="kart_sil">Sil</button>
</div>

<!-- Hidden Input Fields -->
@if($ekranKayitSatirKontrol == "true")
  <input type="hidden" name="LAST_TRNUM" id="LAST_TRNUM" value="@php if(isset($kart_veri->LAST_TRNUM)) { echo @$kart_veri->LAST_TRNUM; } else { echo '000000'; } @endphp">
  <input type="hidden" name="LAST_TRNUM2" id="LAST_TRNUM2" value="@php if(isset($kart_veri->LAST_TRNUM2)) { echo @$kart_veri->LAST_TRNUM2; } else { echo '000000'; } @endphp">
  <input type="hidden" name="LAST_TRNUM3" id="LAST_TRNUM3" value="@php if(isset($kart_veri->LAST_TRNUM3)) { echo @$kart_veri->LAST_TRNUM3; } else { echo '000000'; } @endphp">
@endif

@show