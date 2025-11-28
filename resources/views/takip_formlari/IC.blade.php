<div class="form" style="display:none;" id="IC">
    <!-- Document information -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="dokumanNo" class="form-label">Doküman No</label>
            <input type="text" id="dokumanNo" class="form-control" placeholder="F.94">
        </div>
        <div class="col-md-4">
            <label for="yayinTarihi" class="form-label">Yayın Tarihi</label>
            <input type="date" id="yayinTarihi" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="revNo" class="form-label">Rev No/Tarihi</label>
            <input type="text" id="revNo" class="form-control" placeholder="00 / ...">
        </div>
    </div>

    <!-- Improvement details -->
    <h4 class="mb-3">İyileştirme Bilgileri</h4>
    <div class="row mb-3">
        <div class="col-md-2">
            <label for="no" class="form-label">No</label>
            <input type="number" id="no" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="tarih" class="form-label">Tarih</label>
            <input type="date" id="tarih" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="iyilestirmeTuru" class="form-label">İyileştirme Türü</label>
            <select id="iyilestirmeTuru" class="form-select">
                <option selected disabled>Seçiniz...</option>
                <option value="Kaizen">Kaizen</option>
                <option value="KYS İyileştirme">KYS İyileştirme</option>
                <option value="5S">5S</option>
                <option value="Diğer">Diğer</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="bolum" class="form-label">Bölüm</label>
            <select id="bolum" class="form-select">
                <option selected disabled>Seçiniz...</option>
                <option value="Talaşlı İmalat">Talaşlı İmalat</option>
                <option value="Kalite">Kalite</option>
                <option value="Ambalajlama">Ambalajlama</option>
                <option value="Planlama">Planlama</option>
                <option value="Proje">Proje</option>
                <option value="Satın alma">Satın alma</option>
                <option value="Ar-Ge">Ar-Ge</option>
                <option value="Diğer">Diğer</option>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="kisi" class="form-label">Kişi</label>
            <input type="text" id="kisi" class="form-control" placeholder="İlgili kişi">
        </div>
        <div class="col-md-6">
            <label for="parcaKodu" class="form-label">Parça Kodu / Adı</label>
            <input type="text" id="parcaKodu" class="form-control" placeholder="123456 / Parça Adı">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="prosesAdi" class="form-label">İlgili Proses Adı</label>
            <input type="text" id="prosesAdi" class="form-control">
        </div>
        <div class="col-md-6">
            <label for="gorsel" class="form-label">Görsel</label>
            <input type="file" id="gorsel" class="form-control">
        </div>
    </div>
    <div class="mb-3">
        <label for="mevcutDurum" class="form-label">Mevcut Durum</label>
        <textarea id="mevcutDurum" class="form-control" rows="3"></textarea>
    </div>
    <div class="mb-3">
        <label for="yeniDurum" class="form-label">Yeni Durum</label>
        <textarea id="yeniDurum" class="form-control" rows="3"></textarea>
    </div>

    <!-- Gains section -->
    <h4 class="mb-3">İyileştirmeden Kazançlar</h4>
    <div class="row mb-3">
        <!-- Gain categories -->
        <div class="col-md-4">
            <label class="form-label">Kazanç Kategorileri</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc1" value="1">
                <label class="form-check-label" for="kazanc1">1- Maliyet
                    Tasarrufu</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc2" value="2">
                <label class="form-check-label" for="kazanc2">2- Zaman
                    Tasarrufu</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc3" value="3">
                <label class="form-check-label" for="kazanc3">3- Enerji
                    Tasarrufu</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc4" value="4">
                <label class="form-check-label" for="kazanc4">4- Ergonomi</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc5" value="5">
                <label class="form-check-label" for="kazanc5">5- İş
                    Güvenliği</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc6" value="6">
                <label class="form-check-label" for="kazanc6">6- Yangın</label>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Additional categories in second column -->
            <label class="form-label d-none d-md-block">&nbsp;</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc7" value="7">
                <label class="form-check-label" for="kazanc7">7- 5S</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc8" value="8">
                <label class="form-check-label" for="kazanc8">8-
                    Çevre/Sağlık</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc9" value="9">
                <label class="form-check-label" for="kazanc9">9- Verimlilik</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc10" value="10">
                <label class="form-check-label" for="kazanc10">10- İş
                    Kolaylığı</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc11" value="11">
                <label class="form-check-label" for="kazanc11">11- Kalite</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kazanc12" value="12">
                <label class="form-check-label" for="kazanc12">12- Diğer</label>
            </div>
        </div>
        <!-- Functional areas -->
        <div class="col-md-4">
            <label class="form-label">Fonksiyonlar</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="uretim" value="Üretim">
                <label class="form-check-label" for="uretim">Üretim</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="kalite" value="Kalite">
                <label class="form-check-label" for="kalite">Kalite</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="planlama" value="Planlama">
                <label class="form-check-label" for="planlama">Planlama</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="proje" value="Proje">
                <label class="form-check-label" for="proje">Proje</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="satinalma" value="Satın alma">
                <label class="form-check-label" for="satinalma">Satın alma</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="arge" value="Ar-Ge">
                <label class="form-check-label" for="arge">Ar-Ge</label>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3">
            <label class="form-label">Sonuç</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="sonuc" id="olumlu" value="Olumlu">
                <label class="form-check-label" for="olumlu">Olumlu</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="sonuc" id="olumsuz" value="Olumsuz">
                <label class="form-check-label" for="olumsuz">Olumsuz</label>
            </div>
        </div>
        <div class="col-md-3">
            <label for="bitisTarihi" class="form-label">Bitiş Tarihi</label>
            <input type="date" id="bitisTarihi" class="form-control">
        </div>
    </div>
</div>