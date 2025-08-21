<div class="dropup popup">
    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-bars"></i>
    </button>
    <ul class="dropdown-menu shadow">
        <li><button type="button" class="dropdown-item" onclick="DepoMevcutlari('{{ $KOD }}')">Depo Mevcutları</button></li>
        <li><button type="button" class="dropdown-item" onclick="StokHareketleri('{{ $KOD }}')">Stok Hareketleri</button></li>
        <li><button type="button" class="dropdown-item" onclick="StokKartinaGit('{{ $KOD }}')">Stok Kartına Git</button></li>
        <li><button type="submit" name='kart_islemleri' value='yazdir' class="dropdown-item smbButton" onclick="SatirYazdir(this)">Satırı yazdır</button></li>
        <li><button type="button" class="dropdown-item delete-row" id="deleteSingleRow">Satırı Sil</button></li>
        <li><button type="button" class="dropdown-item" onclick='SatirKopyala(this)'>Satırı Kopyala</button></li>
    </ul>
</div>
