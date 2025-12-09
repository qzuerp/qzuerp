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
                    <label class="form-label ">Talep Eden</label>
                    <input type="text" name="talep_eden" class="form-control" >
                </div>
                <div class="col-md-3">
                    <label class="form-label ">Rapor No</label>
                    <input type="text" name="rapor_no" class="form-control" >
                </div>
                <div class="col-md-3">
                    <label class="form-label ">Tarih</label>
                    <input type="date" name="tarih" class="form-control" >
                </div>
                <div class="col-md-3">
                    <label class="form-label">Devreye Giriş Tarihi</label>
                    <input type="date" name="devre_tarihi" class="form-control">
                </div>
                <div class="col-md-12">
                    <label class="form-label ">Değişikliğin Tanımı</label>
                    <input type="text" name="degisim_tanimi" class="form-control" >
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
                <textarea name="teklif_tarifi" rows="3" class="form-control" ></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label ">Mevcut Durum (Öncesi)</label>
                <textarea name="mevcut_durum" rows="3" class="form-control" ></textarea>
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
                    <input type="text" name="teklif_eden_isim" class="form-control" >
                </div>
                <div class="col-md-4">
                    <label class="form-label ">Bölüm</label>
                    <input type="text" name="teklif_eden_bolum" class="form-control" >
                </div>
                <div class="col-md-4">
                    <label class="form-label ">Görevi</label>
                    <input type="text" name="teklif_eden_gorev" class="form-control" >
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
                        <input type="checkbox" name="sebep_musteri_iade" id="sebep1" class="form-check-input">
                        <label class="form-check-label" for="sebep1">Müşteri İadesi</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep_yansanayi" id="sebep2" class="form-check-input">
                        <label class="form-check-label" for="sebep2">Yan Sanayi Talebi</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep_imalat" id="sebep3" class="form-check-input">
                        <label class="form-check-label" for="sebep3">İmalat / Montaj Zorluğu</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep_iyilestirme" id="sebep4" class="form-check-input">
                        <label class="form-check-label" for="sebep4">İyileştirme / Öneri</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep_kalite" id="sebep5" class="form-check-input">
                        <label class="form-check-label" for="sebep5">Kalite Problemi Çözümü</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep_musteri_talep" id="sebep6" class="form-check-input">
                        <label class="form-check-label" for="sebep6">Müşteri Talebi</label>
                    </div>
                    <input type="text" name="musteri_adi" class="form-control form-control-sm mb-3"
                        placeholder="Müşteri Adı">

                    <div class="form-check mb-2">
                        <input type="checkbox" name="sebep_lisansor" id="sebep7" class="form-check-input">
                        <label class="form-check-label" for="sebep7">Lisansör Talebi</label>
                    </div>
                    <input type="text" name="lisansor_adi" class="form-control form-control-sm"
                        placeholder="Lisansör Adı">
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
                        <input type="checkbox" name="etki_urun" id="etki1" class="form-check-input">
                        <label class="form-check-label" for="etki1">Ürün</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="etki_proses" id="etki2" class="form-check-input">
                        <label class="form-check-label" for="etki2">Proses</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="etki_teminyeri" id="etki3" class="form-check-input">
                        <label class="form-check-label" for="etki3">Temin Yeri</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="etki_malzeme" id="etki4" class="form-check-input">
                        <label class="form-check-label" for="etki4">Hammadde/Malzeme</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="etki_ambalaj" id="etki5" class="form-check-input">
                        <label class="form-check-label" for="etki5">Sevkiyat/Ambalaj</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="etki_prosedur" id="etki6" class="form-check-input">
                        <label class="form-check-label" for="etki6">Prosedür</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <input type="text" name="etki_diger" class="form-control" placeholder="Diğer (belirtiniz)">
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
                            <input type="radio" name="sinif" value="minor" id="sinif1" class="form-check-input"
                                >
                            <label class="form-check-label" for="sinif1">Minor</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="sinif" value="major" id="sinif2" class="form-check-input"
                                >
                            <label class="form-check-label" for="sinif2">Major</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="section-title">Değişiklik</div>
                    <div class="radio-group mb-3">
                        <div class="form-check">
                            <input type="radio" name="degisiklik_durum" value="kullanilabilir" id="deg1"
                                class="form-check-input">
                            <label class="form-check-label" for="deg1">Kullanılabilir</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="degisiklik_durum" value="kullanilamaz" id="deg2"
                                class="form-check-input">
                            <label class="form-check-label" for="deg2">Kullanılamaz</label>
                        </div>
                    </div>
                    <input type="text" name="degisiklik_sure" class="form-control mb-2"
                        placeholder="... tarihine kadar kullanılabilir">
                </div>

                <div class="col-md-6">
                    <div class="section-title">Sapma</div>
                    <div class="radio-group mb-3">
                        <div class="form-check">
                            <input type="radio" name="sapma_durum" value="gecici" id="sap1" class="form-check-input">
                            <label class="form-check-label" for="sap1">Geçici</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="sapma_durum" value="kalici" id="sap2" class="form-check-input">
                            <label class="form-check-label" for="sap2">Kalıcı</label>
                        </div>
                    </div>
                    <input type="text" name="sapma_gecerlilik_tarih" class="form-control mb-2"
                        placeholder="... tarihine kadar geçerlidir">
                    <input type="number" name="sapma_gecerlilik_adet" class="form-control"
                        placeholder="... adet kadar geçerlidir">
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
                    <input type="text" name="sorumlu_ad" class="form-control" >
                </div>
                <div class="col-md-4">
                    <label class="form-label ">Bölüm</label>
                    <input type="text" name="sorumlu_bolum" class="form-control" >
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gerekli Süre (gün)</label>
                    <input type="number" name="gerekli_sure" class="form-control" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kalıp Değişimi Gerekli mi?</label>
                    <select name="kalip_degisim" class="form-select">
                        <option value="">Seçiniz</option>
                        <option value="evet">Evet</option>
                        <option value="hayir">Hayır</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kalıp Maliyeti (₺)</label>
                    <input type="number" name="kalip_maliyet" class="form-control" min="0" step="0.01">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Parça Fiyatı Değişiyor mu?</label>
                    <select name="fiyat_degisim" class="form-select">
                        <option value="">Seçiniz</option>
                        <option value="evet">Evet</option>
                        <option value="hayir">Hayır</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Yeni Parça Fiyatı (₺)</label>
                    <input type="number" name="yeni_fiyat" class="form-control" min="0" step="0.01">
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
                    <input type="number" name="stok_miktar" class="form-control" min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Stok Yönetimi</label>
                    <select name="stok_yonetim" class="form-select">
                        <option value="tuketilecek">Tüketilecek</option>
                        <option value="iskarta">İskarta</option>
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
                <input type="checkbox" name="resim_deg_gerekli" id="resim1" class="form-check-input">
                <label class="form-check-label" for="resim1">Resim değişikliği gerekli</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="resim_deg_gerekli_degil" id="resim2" class="form-check-input">
                <label class="form-check-label" for="resim2">Resim değişikliği gerekli değil</label>
            </div>
        </div>
    </div>

    <!-- 10. AÇIKLAMA -->
    <div class="card">
        <div class="card-header">10. Açıklama</div>
        <div class="card-body">
            <textarea name="aciklama" rows="4" class="form-control"
                placeholder="Ek açıklamalar ve notlar..."></textarea>
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
                    <input type="text" name="musteri_bilgi" class="form-control mb-2">
                    <label class="form-label">Onay / İmza</label>
                    <input type="text" name="musteri_onay" class="form-control">
                </div>
                <div class="col-md-6">
                    <div class="section-title">Lisansör</div>
                    <label class="form-label">Bilgilendirme</label>
                    <input type="text" name="lisansor_bilgi" class="form-control mb-2">
                    <label class="form-label">Onay / İmza</label>
                    <input type="text" name="lisansor_onay" class="form-control">
                </div>
                <div class="col-md-4">
                    <div class="section-title">Karar Veren Bölüm</div>
                    <label class="form-label">Bölüm Adı</label>
                    <input type="text" name="karar_bolum" class="form-control mb-2">
                    <label class="form-label">İsim / İmza</label>
                    <input type="text" name="karar_imza" class="form-control mb-2">
                    <label class="form-label">Tarih</label>
                    <input type="date" name="karar_tarih" class="form-control">
                </div>
                <div class="col-md-4">
                    <div class="section-title">Genel Müdür</div>
                    <label class="form-label">Onay / İmza</label>
                    <input type="text" name="gm_onay" class="form-control">
                </div>
                <div class="col-md-4">
                    <div class="section-title">Müşteri Temsilcisi</div>
                    <label class="form-label">Onay / İmza</label>
                    <input type="text" name="musteri_tem_onay" class="form-control">
                </div>
            </div>
        </div>
    </div>
</div>