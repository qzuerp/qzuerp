@php
  $kullanici_read_yetkileri = explode("|", $user->read_perm);
  $kullanici_write_yetkileri = explode("|", $user->write_perm);
  $kullanici_delete_yetkileri = explode("|", $user->delete_perm);
@endphp

<div class="sidebar">
  <div class="logo-details">
    @if(trim($user->firma) == 'yukselcnc')
      <img src="{{URL::asset('/assets/img/yukselcnc_LOGO.jpeg')}}" class="" width='50' style='object-fit:contain width:100px !important; transform:scale(0.80); height:50px; border 1px solid white; border-radius: 50%;' alt="User Image">
    @else
      <img src="{{URL::asset('/assets/img/qzu_logo.png')}}" class="img-circle" width='50' alt="User Image">
    @endif
    <div class="logo_name ms-3"><b>QZU</b>ERP</div>
  </div>
  <ul class="nav-list">
    <li>
      <i class='bx bx-search'></i>
      <input type="text" id="menu-search" placeholder="Ara..." data-skip-tracking="true">
      <span class="tooltip">Ara</span>
    </li>
    <li>
      <a href="index">
        <i class='bx bx-home'></i>
        <span class="links_name">Anasayfa</span>
      </a>
      <span class="tooltip">Anasayfa</span>
    </li>

    <li class="treeview">
      <a href="#">
        <i class='bx bx-box'></i>
        <span class="links_name">Stok</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Stok</span>
      <ul class="treeview-menu">
        @if (in_array('STOKKART', $kullanici_read_yetkileri))
          <li><a href="kart_stok"><i class='bx bx-package'></i>Stok Kartı</a></li>
        @endif
        @if (in_array('DEPOKART', $kullanici_read_yetkileri))
          <li><a href="kart_depo"><i class='bx bx-store'></i>Depo Kartı</a></li>
        @endif
        @if (in_array('LOKASYONLAR', $kullanici_read_yetkileri))
          <li><a href="gecerlilokasyonlar"><i class='bx bx-map'></i>Geçerli Lokasyonlar</a></li>
        @endif
        @if (in_array('DDTRANSFER', $kullanici_read_yetkileri))
          <li><a href="depodandepoyatransfer"><i class='bx bx-transfer'></i>Depodan Depoya Transfer</a></li>
        @endif
        @if (in_array('ETIKETBOL', $kullanici_read_yetkileri))
          <li><a href="etiket_bolme"><i class='bx bx-shuffle'></i>Etiket Bölme</a></li>
        @endif
        @if (in_array('ETKTKART', $kullanici_read_yetkileri))
          <li><a href="etiketKarti"><i class='bx bx-purchase-tag'></i>Etiket Kartı</a></li>
        @endif
        @if (in_array('STKGRSCKS', $kullanici_read_yetkileri))
          <li><a href="stokgiriscikis"><i class='bx bx-import'></i>Stok Giriş-Çıkış</a></li>
        @endif
        @if (in_array('STOKTV', $kullanici_read_yetkileri))
          <li><a href="stok_tv"><i class='bx bx-list-check'></i>Depo Mevcutları</a></li>
        @endif
        @if (in_array('STKHRKT', $kullanici_read_yetkileri))
          <li><a href="stok_hareketleri"><i class='bx bx-history'></i>Stok Hareketleri</a></li>
        @endif
        @if (in_array('GECMIS', $kullanici_read_yetkileri))
          <li><a href="stok_gecmisi"><i class='bx bx-git-branch'></i>Stok Geçmişi / İzlenebilirlik</a></li>
        @endif
      </ul>
    </li>
    
    <li class="treeview">
      <a href="#">
        <i class='bx bx-calendar'></i>
        <span class="links_name">Üretim / Plan</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Üretim / Plan</span>
      <ul class="treeview-menu">
        @if (in_array('TEZGAHKART', $kullanici_read_yetkileri))
          <li><a href="kart_tezgah"><i class='bx bx-cog'></i>Tezgah Kartı</a></li>
        @endif
        @if (in_array('OPTKART', $kullanici_read_yetkileri))
          <li><a href="kart_operator"><i class='bx bx-user-check'></i>Operatör Kartı</a></li>
        @endif
        @if (in_array('OPERSYNKART', $kullanici_read_yetkileri))
          <li><a href="kart_operasyon"><i class='bx bx-task'></i>Operasyon Kartı</a></li>
        @endif
        @if (in_array('URUNAGACI', $kullanici_read_yetkileri))
          <li><a href="urunagaci"><i class='bx bx-sitemap'></i>Ürün Ağacı</a></li>
        @endif
        @if (in_array('MPSGRS', $kullanici_read_yetkileri))
          <li><a href="mpsgiriskarti"><i class='bx bx-clipboard'></i>MPS Giriş Kartı</a></li>
        @endif
        @if (in_array('TPLMPSGRS', $kullanici_read_yetkileri))
          <li><a href="toplu_mps_girisi"><i class='bx bx-list-plus'></i>Toplu MPS Girişi</a></li>
        @endif
        @if (in_array('TZGHISPLNLM', $kullanici_read_yetkileri))
          <li><a href="tezgahisplanlama"><i class='bx bx-calendar-check'></i>Tezgah İş Planlama</a></li>
        @endif
        @if (in_array('CLSMBLDRM', $kullanici_read_yetkileri))
          <li><a href="calisma_bildirimi"><i class='bx bx-notification'></i>Çalışma Bildirimi</a></li>
        @endif
        @if (in_array('CLSMBLDRMOPRT', $kullanici_read_yetkileri))
          <li><a href="calisma_bildirimi_oprt"><i class='bx bx-notification'></i>Çalışma Bildirimi</a></li>
        @endif
        @if (in_array('AKTIFIS', $kullanici_read_yetkileri))
          <li><a href="atif_isler"><i class='bx bx-cog'></i></i>Aktif İşler</a></li>
        @endif
        @if (in_array('is_siralama', $kullanici_read_yetkileri))
          <li><a href="is_siralama"><i class='bx bx-sort'></i>İş Sıralama</a></li>
        @endif
        @if (in_array('URTFISI', $kullanici_read_yetkileri))
          <li><a href="uretim_fisi"><i class='fa-solid fa-industry'></i>Üretim Fişi</a></li>
        @endif
        @if (in_array('URETIM_GAZETESI', $kullanici_read_yetkileri))
          <li><a href="uretim_gazetesi"><i class='fa-solid fa-newspaper'></i>Üretim Gazetesi</a></li>
        @endif
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class='bx bx-basket'></i>
        <span class="links_name">Satış</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Satış</span>
      <ul class="treeview-menu">
        @if (in_array('CARIKART', $kullanici_read_yetkileri))
          <li><a href="kart_cari"><i class='bx bx-id-card'></i>Cari Kartı</a></li>
        @endif
        @if (in_array('KNTKKART', $kullanici_read_yetkileri))
          <li><a href="kart_kontakt"><i class='bx bx-user-pin'></i>Kontakt Kartı</a></li>
        @endif
        @if (in_array('SATISSIP', $kullanici_read_yetkileri))
          <li><a href="satissiparisi"><i class='bx bx-cart-alt'></i>Satış Siparişi</a></li>
        @endif
        @if (in_array('SEVKIRS', $kullanici_read_yetkileri))
          <li><a href="sevkirsaliyesi"><i class="fa-solid fa-truck-fast"></i>Sevk İrsaliyesi</a></li>
        @endif
        @if (in_array('FYTLST', $kullanici_read_yetkileri))
          <li><a href="fiyat_listesi"><i class='bx bx-money'></i>Fiyat Listeleri</a></li>
        @endif
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class='bx bx-dollar-circle'></i>
        <span class="links_name">Teklif</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Teklif</span>
      <ul class="treeview-menu">
        @if (in_array('teklif_fiyat_analiz', $kullanici_read_yetkileri))
          <li><a href="teklif_fiyat_analiz"><i class='bx bx-bar-chart-alt'></i>Teklif Fiyat Analiz</a></li>
        @endif
        @if (in_array('maliyet', $kullanici_read_yetkileri))
          <li><a href="maliyet"><i class='bx bx-calculator'></i>Maliyet Tanımı</a></li>
        @endif
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class='bx bx-cart'></i>
        <span class="links_name">Satın Alma</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Satın Alma</span>
      <ul class="treeview-menu">
        @if (in_array('SATINALMSIP', $kullanici_read_yetkileri))
          <li><a href="satinalmasiparisi"><i class='bx bx-credit-card'></i>Satın Alma Siparişi</a></li>
        @endif
        @if (in_array('SATALMIRS', $kullanici_read_yetkileri))
          <li><a href="satinalmairsaliyesi"><i class='bx bx-receipt'></i>Satın Alma İrsaliyesi</a></li>
        @endif
        @if (in_array('SATINALMTALEP', $kullanici_read_yetkileri))
          <li><a href="satinalmaTalepleri"><i class='bx bx-shopping-bag'></i>Satın Alma Talepleri</a></li>
        @endif
        @if (in_array('FSNGLSIRS', $kullanici_read_yetkileri))
          <li><a href="fasongelisirsaliyesi"><i class='bx bx-arrow-to-right'></i>Fason Geliş İrsaliyesi</a></li>
        @endif
        @if (in_array('FSNSEVKIRS', $kullanici_read_yetkileri))
          <li><a href="fasonsevkirsaliyesi"><i class='bx bx-arrow-from-left'></i>Fason Sevk İrsaliyesi</a></li>
        @endif
        @if (in_array('FSNTKB', $kullanici_read_yetkileri))
          <li><a href="fason_takibi"><i class='bx bx-arrow-from-left'></i>Fason Takibi</a></li>
        @endif
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class='bx bx-check-shield'></i> {{-- Kalite ikonu --}}
        <span class="links_name">Kalite</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Kalite</span>
      <ul class="treeview-menu">
        @if (in_array('QLT', $kullanici_read_yetkileri))
          <li><a href="QLT"><i class='bx bx-edit-alt'></i>Kalite Şablonu</a></li> {{-- Şablon = Düzenleme --}}
        @endif
        @if (in_array('QLT02', $kullanici_read_yetkileri))
          <li><a href="giris_kalite_kontrol"><i class='fa-solid fa-clipboard-check'></i>Giriş Kalite Kontrol</a></li>
        @endif
        @if (in_array('FKK', $kullanici_read_yetkileri))
          <li><a href="final_kalite_kontrol"><i class='fa-solid fa-check-double'></i>Final Kalite Kontrol</a></li>
        @endif
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class="fa-solid fa-screwdriver-wrench"></i>
        <span class="links_name">Bakım</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Bakım</span>
      <ul class="treeview-menu">
        @if (in_array('KALIPKART', $kullanici_read_yetkileri))
          <li><a href="kart_kalip"><i class='bx bx-shape-square'></i>Kalıp Kartı</a></li>
        @endif
        @if (in_array('KLBRSYNKARTI', $kullanici_read_yetkileri))
          <li><li><a href="kart_kalibrasyon"><i class='bx bx-ruler'></i>Bakım ve Kalibrasyon Kartı</a></li>
        @endif
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class="bx bx-folder"></i>
        <span class="links_name">Doküman Yön.</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Bakım</span>
      <ul class="treeview-menu">
        @if (in_array('DYS', $kullanici_read_yetkileri))
          <li><a href="dys"><i class='bx bx-file'></i>Doküman Yönetim Kartı</a></li>
        @endif
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class='bx bx-cog'></i>
        <span class="links_name">Tanımlar</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Tanımlar</span>
      <ul class="treeview-menu">
        @if (in_array('TAKIPLISTE', $kullanici_read_yetkileri))
          <li><a href="takip_listeleri"><i class='bx bx-list-check'></i>Takip Listeleri</a></li>
        @endif
        @if (in_array('GKTNM', $kullanici_read_yetkileri))
          <li><a href="gk_tanimlari"><i class='bx bx-list-ul'></i>Grup Kodu Tanımları</a></li>
        @endif
        @if (in_array('CGC70', $kullanici_read_yetkileri))
          <li><a href="musteri_sikayet"><i class='bx bx-message-error'></i> Müşteri Şikayetleri</a></li>
        @endif
        @if (in_array('DVZKUR', $kullanici_read_yetkileri))
          <li><a href="doviz_kuru"><i class='bx bx-money'></i>Günlük Döviz Kuru</a></li>
        @endif
        @if (in_array('musteri_form', $kullanici_read_yetkileri))
          <li><a href="musteri_form"><i class="fa-solid fa-file"></i>Müşteri Formu</a></li>
        @endif
        @if (in_array('PRMTR', $kullanici_read_yetkileri))
          <li><a href="parametreler"><i class='bx bx-slider'></i>Parametreler</a></li>
        @endif
        <!-- @if (in_array('PERSKART', $kullanici_read_yetkileri))
          <li><a href="kart_personel"><i class='bx bx-user'></i>Personel Kartı</a></li>
        @endif -->
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class='bx bx-chart'></i>
        <span class="links_name">Raporlama</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Raporlama</span>
      <ul class="treeview-menu">
        <li><a href="{{ route('raporlama.template.list') }}"><i class='bx bx-bar-chart-square'></i> Raporlarım</a></li>
        <li><a href="{{ route('raporlama.index') }}"><i class='bx bx-edit'></i> Rapor Tanımları</a></li>
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class='bx bx-server'></i>
        <span class="links_name">Sistem</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Sistem</span>
      <ul class="treeview-menu">
        @if (in_array('TMUSTR', $kullanici_read_yetkileri))
          <li><a href="{{ route('zorunlu_alan') }}"><i class='bx bx-spreadsheet'></i> Zorunlu Alan Paneli</a></li>
        @endif
        @if (in_array('INFO', $kullanici_read_yetkileri))
          <li><a href="{{ route('info') }}"><i class="fa-solid fa-info"></i> Ekran Tanıtım Kart</a></li>
        @endif
         @if ($user->perm == "ADMIN")
          <li><a href="user"><i class='fa fa-users'></i>Kullanıcılar</a></li>
        @endif
        <li><a href="change_password"><i class='bx bx-key'></i>Şifre Değiştir</a></li>
      </ul>
    </li>

    <!-- <li class="treeview">
      <a href="#">
        <i class='bx bx-key'></i>
        <span class="links_name">Kullanıcılar</span>
        <span class="pull-right-container">
          <i class='bx bx-chevron-down'></i>
        </span>
      </a>
      <span class="tooltip">Kullanıcılar</span>
      <ul class="treeview-menu">
        @if ($user->perm == "ADMIN")
          <li><a href="user"><i class='fa fa-users'></i>Kullanıcılar</a></li>
        @endif
        <li><a href="change_password"><i class='bx bx-key'></i>Şifre Değiştir</a></li>
      </ul>
    </li> -->

    <li class="treeview">
  </ul>
</div>

<script>
  let sidebar = document.querySelector(".sidebar");
let closeBtn = document.querySelector("#toggle-btn-sidebar");
let searchBtn = document.querySelector(".bx-search");
let searchInput = document.querySelector("#menu-search");
let body = document.body;
let isPinned = false;

// Sayfa ilk yüklendiğinde sidebar kapalı
if (sidebar) {
  sidebar.classList.remove("open");
}
if (body) {
  body.classList.add("sidebar-collapse");
}

// Dropdownları sıfırlayan fonksiyon
function resetDropdowns() {
  document.querySelectorAll(".treeview-menu").forEach((submenu) => {
    submenu.style.display = "none";
    const chevron = submenu.parentElement.querySelector(".bx-chevron-down");
    if (chevron) chevron.classList.remove("rotate");
    submenu.parentElement.classList.remove("active");
  });
}

// Menü ikonu değiştir ve dropdownları sıfırla
function menuBtnChange() {
  if (!closeBtn) return;
  if (sidebar.classList.contains("open")) {
    document.getElementById('icon').classList.replace("bx-menu", "bx-menu-alt-right");
  } else {
    document.getElementById('icon').classList.replace("bx-menu-alt-right", "bx-menu");
  }
  resetDropdowns();
}

// Hover olayları
if (sidebar) {
  sidebar.addEventListener("mouseenter", () => {
    if (body.classList.contains("sidebar-collapse") && !isPinned && !sidebar.classList.contains("open")) {
      sidebar.classList.add("open");
      menuBtnChange();
    }
  });

  sidebar.addEventListener("mouseleave", () => {
    if (body.classList.contains("sidebar-collapse") && !isPinned && sidebar.classList.contains("open")) {
      sidebar.classList.remove("open");
      menuBtnChange();
    }
  });
}

// Toggle butonu
if (closeBtn) {
  closeBtn.addEventListener("click", () => {
    isPinned = !isPinned;
    sidebar.classList.toggle("open");

    if (sidebar.classList.contains("open")) {
      body.classList.remove("sidebar-collapse");
    } else {
      body.classList.add("sidebar-collapse");
    }

    menuBtnChange();
  });
}

// Arama butonu
if (searchBtn && searchInput) {
  searchBtn.addEventListener("click", () => {
    searchInput.focus();
  });
}

// Dropdown menüler açma-kapama
document.querySelectorAll(".treeview > a").forEach((menu) => {
  menu.addEventListener("click", (e) => {
    e.preventDefault();
    const treeviewMenu = menu.parentElement.querySelector(".treeview-menu");
    if (!treeviewMenu) return;

    const isOpen = treeviewMenu.style.display === "block";

    resetDropdowns();

    if (!isOpen) {
      treeviewMenu.style.display = "block";
      const chevron = menu.querySelector(".bx-chevron-down");
      if (chevron) chevron.classList.add("rotate");
      menu.parentElement.classList.add("active");
    }
  });
});

// Arama özelliği
if (searchInput) {
  searchInput.addEventListener("input", (e) => {
    const searchTerm = e.target.value.toLowerCase().trim();
    const allMenuItems = document.querySelectorAll(".nav-list > li");

    if (searchTerm === "") {
      allMenuItems.forEach((item) => {
        item.style.display = "block";
        const treeviewMenu = item.querySelector(".treeview-menu");
        if (treeviewMenu) {
          treeviewMenu.style.display = "none";
          const chevron = item.querySelector(".bx-chevron-down");
          if (chevron) chevron.classList.remove("rotate");
          item.classList.remove("active");
          item.querySelectorAll(".treeview-menu li").forEach((subItem) => {
            subItem.style.display = "flex";
          });
        }
      });
      return;
    }

    allMenuItems.forEach((item) => {
      if (item.querySelector("#menu-search")) return;

      const isTreeview = item.classList.contains("treeview");

      if (isTreeview) {
        const mainMenuText = item.querySelector("a .links_name")?.textContent.toLowerCase() || "";
        const subMenus = item.querySelectorAll(".treeview-menu li");
        let hasMatchingSubMenu = false;

        subMenus.forEach((subItem) => {
          const subMenuText = subItem.textContent.toLowerCase();
          if (subMenuText.includes(searchTerm)) {
            subItem.style.display = "flex";
            hasMatchingSubMenu = true;
          } else {
            subItem.style.display = "none";
          }
        });

        if (mainMenuText.includes(searchTerm) || hasMatchingSubMenu) {
          item.style.display = "block";
          if (hasMatchingSubMenu) {
            const treeviewMenu = item.querySelector(".treeview-menu");
            const chevron = item.querySelector(".bx-chevron-down");
            if (treeviewMenu && chevron) {
              treeviewMenu.style.display = "block";
              chevron.classList.add("rotate");
              item.classList.add("active");
            }
          }
        } else {
          item.style.display = "none";
        }
      } else {
        const menuText = item.textContent.toLowerCase();
        if (menuText.includes(searchTerm)) {
          item.style.display = "block";
        } else {
          item.style.display = "none";
        }
      }
    });
  });

  // ESC ile aramayı temizle
  searchInput.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      searchInput.value = "";
      const allMenuItems = document.querySelectorAll(".nav-list > li");
      allMenuItems.forEach((item) => {
        item.style.display = "block";
        const treeviewMenu = item.querySelector(".treeview-menu");
        if (treeviewMenu) {
          treeviewMenu.style.display = "none";
          const chevron = item.querySelector(".bx-chevron-down");
          if (chevron) chevron.classList.remove("rotate");
          item.classList.remove("active");
          item.querySelectorAll(".treeview-menu li").forEach((subItem) => {
            subItem.style.display = "flex";
          });
        }
      });
    }
  });
}

</script>