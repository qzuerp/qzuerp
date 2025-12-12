<style>
    .form-container {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .card {
        border: 1px solid #dee2e6;
        margin-bottom: 20px;
    }

    .card-header {
        font-weight: 600;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #495057;
    }

    .form-check-label {
        font-size: 0.9rem;
    }

    .radio-group,
    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
</style>

<div class="form" style="display:none;" id="SAPMA">
    <!-- 1. GENEL BİLGİLER -->
    <div class="card">
        <div class="card-header">1. Genel Bilgiler</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label ">Talep Eden Kurum</label>
                    <!-- <input type="text" name="sapma_talep_eden" class="form-control" 
                           value="{{ @$kart_veri->sapma_talep_eden ?? '' }}"> -->

                           <select class="form-control js-example-basic-single" style="width: 100%;" name="sapma_talep_eden">

                                @php
                                    $evraklar=DB::table($database.'cari00')->orderBy('id', 'ASC')->get();

                                    foreach ($evraklar as $key => $veri) {
                                        if ($veri->KOD == @$kart_veri->sapma_talep_eden) {
                                            echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                            else {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                        }
                                @endphp

                            </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label ">Rapor No</label>
                    <input type="text" name="sapma_rapor_no" class="form-control" 
                           value="{{ @$kart_veri->sapma_rapor_no ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label ">Oluşturma Tarih</label>
                    <input type="date" name="sapma_tarih" class="form-control" 
                           value="{{ @$kart_veri->sapma_tarih ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Devreye Giriş Tarihi</label>
                    <input type="date" name="sapma_devre_tarihi" class="form-control"
                           value="{{ @$kart_veri->sapma_devre_tarihi ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label ">Değişikliğin Tanımı</label>
                    <input type="text" name="sapma_degisim_tanimi" class="form-control" 
                           value="{{ @$kart_veri->sapma_degisim_tanimi ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label ">Parça no</label>
                    <select name="sapma_parca_no" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="sapma_parca_no" id="sapma_parca_no" class="form-control select2">
                        <option value="">Seç</option>

                        @php
                            $evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->get();

                            foreach ($evraklar as $key => $veri) {
                                if ($veri->KOD == @$kart_veri->sapma_parca_no) {
                                    echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                }
                                else {
                                    echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                }
                            }
                        @endphp
                    </select>
                </div>
                <div class="col-2">
                    <img width="150" height="100" style="object-fit: contain; border-radius: 8px;" src="https://community.softr.io/uploads/db9110/original/2X/7/74e6e7e382d0ff5d7773ca9a87e6f6f8817a68a6.jpeg" alt="" id="stok_gorsel">
                </div>
            </div>
        </div>
    </div>

    <!-- 2. DEĞİŞİKLİK/SAPMA DETAYLARI -->
    <div class="card">
        <div class="card-header">2. Değişiklik / Sapma Detayları</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label ">Teklif Tarifi</label>
                <textarea name="sapma_teklif_tarifi" rows="3" class="form-control">{{ @$kart_veri->sapma_teklif_tarifi ?? '' }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label ">Mevcut Durum (Öncesi)</label>
                <textarea name="sapma_mevcut_durum" rows="3" class="form-control">{{ @$kart_veri->sapma_mevcut_durum ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <!-- 3. TEKLİF EDEN BİLGİLERİ -->
    <div class="card">
        <div class="card-header">3. Teklif Eden Bilgileri</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label ">İsim Soyad</label>
                    <!-- <input type="text" name="sapma_teklif_eden_isim" class="form-control" 
                           value="{{ @$kart_veri->sapma_teklif_eden_isim ?? '' }}"> -->
                    <select class="form-control select2 js-example-basic-single" style="width: 100%;" name="sapma_teklif_eden_isim">
                        <option value="" selected></option>
                        @php
                            $pers00_evraklar=DB::table($database.'pers00')->orderBy('id', 'ASC')->get();

                            foreach ($pers00_evraklar as $key => $veri) {

                                if ($veri->KOD == @$kart_veri->sapma_teklif_eden_isim) {
                                    echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                                }
                                else {
                                    echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                                }
                            }
                        @endphp
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label ">Bölüm</label>
                    <input type="text" name="sapma_teklif_eden_bolum" class="form-control" 
                           value="{{ @$kart_veri->sapma_teklif_eden_bolum ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label ">Görevi</label>
                    <input type="text" name="sapma_teklif_eden_gorev" class="form-control" 
                           value="{{ @$kart_veri->sapma_teklif_eden_gorev ?? '' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- 4. DEĞİŞİKLİK/SAPMA SEBEBİ -->
    <div class="card">
        <div class="card-header">4. Değişiklik / Sapma Sebebi</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep1" id="sebep1" class="form-check-input"
                               {{ !empty(@$kart_veri->sapma_sebep_iade) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sebep1">Müşteri İadesi</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep2" id="sebep2" class="form-check-input"
                               {{ !empty(@$kart_veri->sapma_sebep_yansanayi) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sebep2">Yan Sanayi Talebi</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep3" id="sebep3" class="form-check-input"
                               {{ !empty(@$kart_veri->sapma_sebep_imalat) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sebep3">İmalat / Montaj Zorluğu</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep4" id="sebep4" class="form-check-input"
                               {{ !empty(@$kart_veri->sapma_sebep_iyilestirme) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sebep4">İyileştirme / Öneri</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep5" id="sebep5" class="form-check-input"
                               {{ !empty(@$kart_veri->sapma_sebep_kalite) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sebep5">Kalite Problemi Çözümü</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep6" id="sebep6" class="form-check-input"
                               {{ @$kart_veri->sapma_sebep_musteri == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="sebep6">Müşteri Talebi</label>
                    </div>
                    <input type="text" name="sapma_musteri_adi" class="form-control form-control-sm mb-3"
                        placeholder="Müşteri Adı" value="{{ @$kart_veri->sapma_sebep_musteri_adi ?? '' }}">

                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep7" id="sebep7" class="form-check-input"
                               {{ @$kart_veri->sapma_sebep_lisansor == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="sebep7">Lisansör Talebi</label>
                    </div>
                    <input type="text" name="sapma_lisansor_adi" class="form-control form-control-sm"
                        placeholder="Lisansör Adı" value="{{ @$kart_veri->sapma_sebep_lisansor_adi ?? '' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- 5. ETKİLENEN ALANLAR -->
    <div class="card">
        <div class="card-header">5. Değişiklik / Sapmanın Etkilediği Alanlar</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sapma_etki_urun" id="etki1" class="form-check-input"
                               {{ !empty(@$kart_veri->sapma_etki_urun) ? 'checked' : '' }}>
                        <label class="form-check-label" for="etki1">Ürün</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sapma_etki_proses" id="etki2" class="form-check-input"
                               {{ !empty(@$kart_veri->sapma_etki_proses) ? 'checked' : '' }}>
                        <label class="form-check-label" for="etki2">Proses</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sapma_etki_teminyeri" id="etki3" class="form-check-input"
                               {{ !empty(@$kart_veri->sapma_etki_teminyeri) ? 'checked' : '' }}>
                        <label class="form-check-label" for="etki3">Temin Yeri</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sapma_etki_malzeme" id="etki4" class="form-check-input"
                               {{ !empty(@$kart_veri->sapma_etki_malzeme) ? 'checked' : '' }}>
                        <label class="form-check-label" for="etki4">Hammadde/Malzeme</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sapma_etki_ambalaj" id="etki5" class="form-check-input"
                               {{ !empty(@$kart_veri->sapma_etki_ambalaj) ? 'checked' : '' }}>
                        <label class="form-check-label" for="etki5">Sevkiyat/Ambalaj</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sapma_etki_prosedur" id="etki6" class="form-check-input"
                               {{ !empty(@$kart_veri->sapma_etki_prosedur) ? 'checked' : '' }}>
                        <label class="form-check-label" for="etki6">Prosedür</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <input type="text" name="sapma_etki_diger" class="form-control" 
                           placeholder="Diğer (belirtiniz)" value="{{ @$kart_veri->sapma_etki_diger ?? '' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- 6. SINIFLANDIRMA VE KARAR -->
    <div class="card">
        <div class="card-header">6. Sınıflandırma ve Karar</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label ">Değişiklik / Sapma Sınıfı</label>
                    <div class="radio-group">
                        <div class="form-check">
                            <input type="radio" name="sapma_sinif" value="minor" id="sinif1" class="form-check-input"
                                {{ (@$kart_veri->sapma_sinif ?? '') == 'minor' ? 'checked' : '' }}>
                            <label class="form-check-label" for="sinif1">Minor</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="sapma_sinif" value="major" id="sinif2" class="form-check-input"
                                {{ (@$kart_veri->sapma_sinif ?? '') == 'major' ? 'checked' : '' }}>
                            <label class="form-check-label" for="sinif2">Major</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="section-title">Değişiklik</div>
                    <div class="radio-group mb-3">
                        <div class="form-check">
                            <input type="radio" name="sapma_degisiklik_durum" value="kullanilabilir" id="deg1"
                                class="form-check-input"
                                {{ (@$kart_veri->sapma_deg_parca ?? '') == 'kullanilabilir' ? 'checked' : '' }}>
                            <label class="form-check-label" for="deg1">Kullanılabilir</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="sapma_degisiklik_durum" value="kullanilamaz" id="deg2"
                                class="form-check-input"
                                {{ (@$kart_veri->sapma_deg_parca ?? '') == 'kullanilamaz' ? 'checked' : '' }}>
                            <label class="form-check-label" for="deg2">Kullanılamaz</label>
                        </div>
                    </div>
                    <input type="text" name="sapma_degisiklik_sure" class="form-control mb-2"
                        placeholder="... tarihine kadar kullanılabilir"
                        value="{{ @$kart_veri->sapma_deg_tarihine_kadar ?? '' }}">
                </div>

                <div class="col-md-6">
                    <div class="section-title">Sapma</div>
                    <div class="radio-group mb-3">
                        <div class="form-check">
                            <input type="radio" name="sapma_durum" value="gecici" id="sap1" class="form-check-input"
                                {{ (@$kart_veri->sapma_durum ?? '') == 'gecici' ? 'checked' : '' }}>
                            <label class="form-check-label" for="sap1">Geçici</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="sapma_durum" value="kalici" id="sap2" class="form-check-input"
                                {{ (@$kart_veri->sapma_durum ?? '') == 'kalici' ? 'checked' : '' }}>
                            <label class="form-check-label" for="sap2">Kalıcı</label>
                        </div>
                    </div>
                    <input type="text" name="sapma_gecerlilik_tarih" class="form-control mb-2"
                        placeholder="... tarihine kadar geçerlidir"
                        value="{{ @$kart_veri->sapma_gec_tarih ?? '' }}">
                    <input type="number" name="sapma_gecerlilik_adet" class="form-control"
                        placeholder="... adet kadar geçerlidir"
                        value="{{ @$kart_veri->sapma_gec_adet ?? '' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- 7. SORUMLU VE MALİYET BİLGİLERİ -->
    <div class="card">
        <div class="card-header">7. Sorumlu ve Maliyet Bilgileri</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label ">Sorumlu Ad Soyad</label>
                    <!-- <input type="text" name="sapma_sorumlu_ad" class="form-control" 
                           value="{{ @$kart_veri->sapma_sorumlu_ad ?? '' }}"> -->

                    <select class="form-control select2 js-example-basic-single" style="width: 100%;" name="sapma_sorumlu_ad">
                        <option value="" selected></option>
                        @php
                            $pers00_evraklar=DB::table($database.'pers00')->orderBy('id', 'ASC')->get();

                            foreach ($pers00_evraklar as $key => $veri) {

                                if ($veri->KOD == @$kart_veri->sapma_sorumlu_ad) {
                                    echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                                }
                                else {
                                    echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                                }
                            }
                        @endphp
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label ">Bölüm</label>
                    <input type="text" name="sapma_sorumlu_bolum" class="form-control" 
                           value="{{ @$kart_veri->sapma_sorumlu_bolum ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gerekli Süre (gün)</label>
                    <input type="number" name="sapma_gerekli_sure" class="form-control" min="0"
                           value="{{ @$kart_veri->sapma_gerekli_sure ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kalıp Değişimi Gerekli mi?</label>
                    <select name="sapma_kalip_degisim" class="form-select">
                        <option value="">Seçiniz</option>
                        <option value="evet" {{ (@$kart_veri->sapma_kalip_degisim ?? '') == 'evet' ? 'selected' : '' }}>Evet</option>
                        <option value="hayir" {{ (@$kart_veri->sapma_kalip_degisim ?? '') == 'hayir' ? 'selected' : '' }}>Hayır</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kalıp Maliyeti (₺)</label>
                    <input type="number" name="sapma_kalip_maliyet" class="form-control" min="0" step="0.01"
                           value="{{ @$kart_veri->sapma_kalip_maliyet ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Parça Fiyatı Değişiyor mu?</label>
                    <select name="sapma_fiyat_degisim" class="form-select">
                        <option value="">Seçiniz</option>
                        <option value="evet" {{ (@$kart_veri->sapma_fiyat_degisim ?? '') == 'evet' ? 'selected' : '' }}>Evet</option>
                        <option value="hayir" {{ (@$kart_veri->sapma_fiyat_degisim ?? '') == 'hayir' ? 'selected' : '' }}>Hayır</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Yeni Parça Fiyatı (₺)</label>
                    <input type="number" name="sapma_yeni_fiyat" class="form-control" min="0" step="0.01"
                           value="{{ @$kart_veri->sapma_yeni_fiyat ?? '' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- 8. STOK YÖNETİMİ -->
    <div class="card">
        <div class="card-header">8. Stok Yönetimi</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Eldeki Stok Miktarı</label>
                    <input type="number" name="sapma_stok_miktar" class="form-control" min="0"
                           value="{{ @$kart_veri->sapma_stok ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Stok Yönetimi</label>
                    <select name="sapma_stok_yonetim" class="form-select">
                        <option value="tuketilecek" {{ (@$kart_veri->sapma_stok_yonetim ?? '') == 'tuketilecek' ? 'selected' : '' }}>Tüketilecek</option>
                        <option value="iskarta" {{ (@$kart_veri->sapma_stok_yonetim ?? '') == 'iskarta' ? 'selected' : '' }}>İskarta</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- 9. ANA SANAYİ ONAYI -->
    <div class="card">
        <div class="card-header">9. Ana Sanayi Onayı</div>
        <div class="card-body">
            <div class="form-check mb-2">
                <input type="radio" name="sapma_resim_deg_gerekli" id="resim1" class="form-check-input" {{ @$kart_veri->sapma_resim_deg_gerekli == 1 ? 'checked' : '' }} value="1">
                <label class="form-check-label" for="resim1">Resim değişikliği gerekli</label>
            </div>
            <div class="form-check">
                <input type="radio" name="sapma_resim_deg_gerekli" id="resim2" class="form-check-input" {{ @$kart_veri->sapma_resim_deg_gerekli == 0 ? 'checked' : '' }} value="0">
                <label class="form-check-label" for="resim2">Resim değişikliği gerekli değil</label>
            </div>
        </div>
    </div>

    <!-- 10. AÇIKLAMA -->
    <div class="card">
        <div class="card-header">10. Açıklama</div>
        <div class="card-body">
            <textarea name="sapma_aciklama" rows="4" class="form-control"
                placeholder="Ek açıklamalar ve notlar...">{{ @$kart_veri->sapma_aciklama ?? '' }}</textarea>
        </div>
    </div>

    <!-- 11. ONAYLAR -->
    <div class="card">
        <div class="card-header">11. Onaylar</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="section-title">Müşteri</div>
                    <label class="form-label">Bilgilendirme</label>
                    <input type="text" name="sapma_musteri_bilgi" class="form-control mb-2"
                           value="{{ @$kart_veri->sapma_musteri_bilgi ?? '' }}">
                    <label class="form-label">Onay / İmza</label>
                    <input type="text" name="sapma_musteri_onay" class="form-control"
                           value="{{ @$kart_veri->sapma_musteri_onay ?? '' }}">
                </div>
                <div class="col-md-6">
                    <div class="section-title">Lisansör</div>
                    <label class="form-label">Bilgilendirme</label>
                    <input type="text" name="sapma_lisansor_bilgi" class="form-control mb-2"
                           value="{{ @$kart_veri->sapma_lisansor_bilgi ?? '' }}">
                    <label class="form-label">Onay / İmza</label>
                    <input type="text" name="sapma_lisansor_onay" class="form-control"
                           value="{{ @$kart_veri->sapma_lisansor_onay ?? '' }}">
                </div>
                <div class="col-md-4">
                    <div class="section-title">Karar Veren Bölüm</div>
                    <label class="form-label">Bölüm Adı</label>
                    <input type="text" name="sapma_karar_bolum" class="form-control mb-2"
                           value="{{ @$kart_veri->sapma_karar_bolum ?? '' }}">
                    <label class="form-label">İsim / İmza</label>
                    <input type="text" name="sapma_karar_imza" class="form-control mb-2"
                           value="{{ @$kart_veri->sapma_karar_imza ?? '' }}">
                    <label class="form-label">Tarih</label>
                    <input type="date" name="sapma_karar_tarih" class="form-control"
                           value="{{ @$kart_veri->sapma_karar_tarih ?? '' }}">
                </div>
                <div class="col-md-4">
                    <div class="section-title">Genel Müdür</div>
                    <label class="form-label">Onay / İmza</label>
                    <input type="text" name="sapma_gm_onay" class="form-control"
                           value="{{ @$kart_veri->sapma_gm_onay ?? '' }}">
                </div>
                <div class="col-md-4">
                    <div class="section-title">Müşteri Temsilcisi</div>
                    <label class="form-label">Onay / İmza</label>
                    <input type="text" name="sapma_musteri_tem_onay" class="form-control"
                           value="{{ @$kart_veri->sapma_musteri_tem_onay ?? '' }}">
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#sapma_parca_no').trigger('change');
    });

    $('#sapma_parca_no').on('change',function(){
        var kod = $(this).val();
        $.ajax({
            'url':'sapma/kod_gorsel',
            'type':'post',
            'data':{KOD:kod},
            'success':function(res){
                $('#stok_gorsel').attr('src',res ?? 'https://community.softr.io/uploads/db9110/original/2X/7/74e6e7e382d0ff5d7773ca9a87e6f6f8817a68a6.jpeg');
            }
        })
    });
</script>