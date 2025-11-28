<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\stok00_controller;
use App\Http\Controllers\calisma_bildirimi_controller;
use App\Http\Controllers\cari00_controller;
use App\Http\Controllers\gdef00_controller;
use App\Http\Controllers\imlt00_controller;
use App\Http\Controllers\imlt00_kalibrasyon_controller;
use App\Http\Controllers\imlt01_controller;
use App\Http\Controllers\pers00_pers_controller;
use App\Http\Controllers\pers00_opt_controller;
use App\Http\Controllers\stok29_controller;
use App\Http\Controllers\stok40_controller;
use App\Http\Controllers\stok46_controller;
use App\Http\Controllers\stok47_controller;
use App\Http\Controllers\stok60_controller;
use App\Http\Controllers\gecous_controller;
use App\Http\Controllers\KullaniciIslemleri;
use App\Http\Controllers\stok01_controller;
use App\Http\Controllers\stok69_controller;
use App\Http\Controllers\stok20_controller;
use App\Http\Controllers\stok21_controller;
use App\Http\Controllers\stok26_controller;
use App\Http\Controllers\stok25_controller;
use App\Http\Controllers\bomu01_controller;
use App\Http\Controllers\mmps10_controller;
use App\Http\Controllers\kontakt00_controller;
use App\Http\Controllers\stok48_controller;
use App\Http\Controllers\opbild_controller;
use App\Http\Controllers\main_controller;
use App\Http\Controllers\dosyalar00_controller;
use App\Http\Controllers\stok63_controller;
use App\Http\Controllers\stok68_controller;
use App\Http\Controllers\kalip00_controller;
use App\Http\Controllers\dys_controller;
use App\Http\Controllers\toplumps_controller;
use App\Http\Controllers\tezgah_is_planlama_controller;
use App\Http\Controllers\barcode_controller;
use App\Http\Controllers\siparisraporlari_controller;
use App\Http\Controllers\MusteriForm_Controller;
use App\Http\Controllers\DovizKuruController;
use App\Http\Controllers\Etiket_Karti_controller;
use App\Http\Controllers\Maliyet;
use App\Http\Controllers\Teklif_fiyat_analiz;
use App\Http\Controllers\qval10_controller;
use App\Http\Controllers\qval02_controller;
use App\Http\Controllers\fkk_controller;
use App\Http\Controllers\pers00_controller;
use App\Http\Controllers\Issiralama;
use App\Http\Controllers\RaporlamaController;
use App\Http\Controllers\deneme1;
use App\Http\Controllers\uretim_gazetesi;
use App\Http\Controllers\tmustr_controller;
use App\Http\Controllers\info_controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\efn_controler;
use App\Http\Controllers\cgc70_controller;
use App\Http\Controllers\takip_controller;


use Illuminate\Http\Request;
use League\CommonMark\Extension\TaskList\TaskListItemMarkerParser;

// eposta
Route::post('/kontrol-email', function (\Illuminate\Http\Request $request) {
    $email = $request->input('email');
    $exists = \App\Models\User::where('email', $email)->exists();
    return response()->json(['exists' => $exists]);
})->name('kontrol-email');




Route::get('/', function () {
  return redirect('/index');
});

Route::get('/api/data', [efn_controler::class, 'index']);

Route::get('/raporlama', [RaporlamaController::class, 'index'])->name('raporlama.index');
Route::post('/raporlama/run', [RaporlamaController::class, 'run'])->name('raporlama.run');
Route::post('/raporlama/kriter', [RaporlamaController::class, 'kriter'])->name('raporlama.kriter');
Route::post('/raporlama/alanlar', [RaporlamaController::class, 'alanlar'])->name('raporlama.alanlar');
Route::post('/raporlama/template/save', [RaporlamaController::class, 'saveTemplate'])->name('raporlama.template.save');
// routes/web.php
Route::get('/raporlama/templates', [RaporlamaController::class, 'listTemplates'])->name('raporlama.template.list');
Route::get('/raporlama/template/{id}', [RaporlamaController::class, 'loadTemplate'])->name('raporlama.template.load');
Route::get('/raporlama/template/delete/{id}', [RaporlamaController::class, 'deleteTemplate'])->name('raporlama.template.delete');
Route::get('/raporlama/template/edit/{id}', [RaporlamaController::class, 'editTemplate'])->name('raporlama.template.edit');
Route::post('/raporlama/template/update/{id}', [RaporlamaController::class, 'updateTemplate'])->name('raporlama.template.update');


Route::get('/uretim_gazetesi', [uretim_gazetesi::class, 'index'])->name('uretim_gazetesi.index');

Route::get('/musteri_sikayet', [cgc70_controller::class, 'index'])->name('musteri_sikayet');
Route::post('/cgc70_islemler', [cgc70_controller::class, 'islemler']);
Route::post('/cgc702_islemler', [takip_controller::class, 'islemler']);

Route::get('/takip_listeleri', [takip_controller::class, 'index'])->name('takip_listeleri');

Route::view('/erisim_engeli', 'erisim_engeli');


Auth::routes(['password.request' => false]);

  // POST'LAR
  Route::get('/loglar/datatables', [main_controller::class, 'getLoglar'])->name('loglar.ajax');
  Route::post('/hizli_islem_verileri', [main_controller::class, 'hizli_islem_verileri']);
  Route::post('/seri_no_uret', [main_controller::class, 'seri_no_uret']);
  Route::post('/stokKartinaGit', [main_controller::class, 'StokKartinaGit']);

  Route::post('kullanici_olustur',[KullaniciIslemleri::class,'kullaniciOlustur']);
  Route::post('kullanici_islemleri',[KullaniciIslemleri::class,'kullaniciIslemleri']);
  Route::post('kullanici_sifre_degistir',[KullaniciIslemleri::class,'sifreDegistir']);


  Route::get('musteri_form',[MusteriForm_Controller::class,'index']);
  Route::post('musteri_form_islemler', [MusteriForm_Controller::class, 'islemler']);
  Route::post('musteri_form_musteri', [MusteriForm_Controller::class, 'musetiGetir']);

  Route::post('cari00_islemler',[cari00_controller::class,'islemler']);
  Route::post('stok00_islemler',[stok00_controller::class,'islemler']);
  Route::post('dys_islemler',[dys_controller::class,'islemler']);
  Route::post('pers00_islemler',[pers00_pers_controller::class,'islemler']);
  Route::post('imlt00_islemler',[imlt00_controller::class,'islemler']);
  Route::post('imlt00_kalibrasyon_islemler',[imlt00_kalibrasyon_controller::class,'islemler']);

  Route::post('gdef00_islemler',[gdef00_controller::class,'islemler']);
  Route::post('calisma_bildirimi_islemler',[calisma_bildirimi_controller::class,'islemler']);
  Route::post('jobno_degerleri',[calisma_bildirimi_controller::class,'jobno_degerleri']);
  Route::get('sirali_isleri_getir',[calisma_bildirimi_controller::class,'sirali_isleri_getir']);
  Route::get('surec_kontrolu',[calisma_bildirimi_controller::class,'surec_kontrolu']);
  Route::post('imlt01_islemler',[imlt01_controller::class,'islemler']);
  Route::post('stok29_islemler',[stok29_controller::class,'islemler']);
  Route::post('stok40_islemler',[stok40_controller::class,'islemler']);
  Route::post('siparisten_talep_olustur',[stok40_controller::class,'siparisten_talep_olustur']);
  Route::post('stok46_islemler',[stok46_controller::class,'islemler']);
  Route::post('stok47_islemler',[stok47_controller::class,'islemler']);
  Route::post('stok60_islemler',[stok60_controller::class,'islemler']);
  Route::post('gecous_islemler',[gecous_controller::class,'islemler']);
  Route::post('stok69_islemler',[stok69_controller::class,'islemler']);
  Route::post('stok21_islemler',[stok21_controller::class,'islemler']);
  Route::post('stok20_islemler',[stok20_controller::class,'islemler']);
  Route::post('stok26_islemler',[stok26_controller::class,'islemler']);
  Route::post('stok25_islemler',[stok25_controller::class,'islemler']);
  Route::post('bomu01_islemler',[bomu01_controller::class,'islemler']);
  Route::post('mmps10_islemler',[mmps10_controller::class,'islemler']);
  Route::post('mps_olustur',[mmps10_controller::class,'mps_olustur']);
  Route::post('mmps10_fetchData',[mmps10_controller::class,'fetchData']);
  Route::post('chartVeri',[mmps10_controller::class,'chartVeri']);
  Route::post('chartVeri2',[mmps10_controller::class,'chartVeri2']);
  Route::post('verimlilikHesapla',[mmps10_controller::class,'verimlilikHesapla']);
  Route::post('kontakt00_islemler',[kontakt00_controller::class,'islemler']);
  Route::post('stok48_islemler',[stok48_controller::class,'islemler']);
  Route::post('stok63_islemler',[stok63_controller::class,'islemler']);
  Route::post('stok68_islemler',[stok68_controller::class,'islemler']);
  Route::post('/fason/getir',[stok68_controller::class,'fason_getir']);
  Route::post('kalip00_islemler',[kalip00_controller::class,'islemler']);
  Route::post('sfdc31_e_islemler',[calisma_bildirimi_controller::class,'islemler']);
  Route::post('toplumps_islemler',[toplumps_controller::class,'islemler']);
  Route::post('tezgah_is_planlama_islemler',[tezgah_is_planlama_controller::class,'islemler']);
  Route::post('is_atama',[tezgah_is_planlama_controller::class,'is_atama']);
  Route::post('isleri_sifirla',[tezgah_is_planlama_controller::class,'isleri_sifirla']);

  Route::post('etiketKarti_islemler',[Etiket_Karti_controller::class,'islemler'])->name('etiket_Karti_islemler');

  Route::post('/islemler',[Maliyet::class,'islemler'])->name("islemler");

  Route::post('cari00_kartGetir',[cari00_controller::class,'kartGetir']);
  Route::post('calisma_bildirimi_kartGetir',[calisma_bildirimi_controller::class,'kartGetir']);
  Route::post('stok00_kartGetir',[stok00_controller::class,'kartGetir']);
  Route::get('stok-kodu-ara', [stok00_controller::class, 'stokKoduAra']);
  Route::get('/evraklar-veri', [stok00_controller::class, 'getEvraklarAjax']);
  Route::get('stok-kodu-custom-select', [stok00_controller::class, 'stokKoduCustomSelect']);
  Route::post('dys00_kartGetir',[dys_controller::class,'kartGetir']);
  Route::post('pers00_kartGetir',[pers00_controller::class,'kartGetir']);
  Route::post('imlt00_kartGetir',[imlt00_controller::class,'kartGetir']);
  Route::post('gdef00_kartGetir',[gdef00_controller::class,'kartGetir']);
  Route::post('imlt01_kartGetir',[imlt01_controller::class,'kartGetir']);
  Route::post('stok29_kartGetir',[stok29_controller::class,'kartGetir']);
  Route::post('stok29_kalite_kontrolu',[stok29_controller::class,'kalite_kontrolu']);
  Route::post('stok40_kartGetir',[stok40_controller::class,'kartGetir']);
  Route::post('stok46_kartGetir',[stok46_controller::class,'kartGetir']);
  Route::post('stok60_kartGetir',[stok60_controller::class,'kartGetir']);
  Route::post('tezgah_is_planlama_kartGetir',[tezgah_is_planlama_controller::class,'kartGetir']);

  Route::post('stok29_yeniEvrakNo',[stok29_controller::class,'yeniEvrakNo']);
  Route::post('stok40_yeniEvrakNo',[stok40_controller::class,'yeniEvrakNo']);
  Route::post('stok46_yeniEvrakNo',[stok46_controller::class,'yeniEvrakNo']);
  Route::post('stok60_yeniEvrakNo',[stok60_controller::class,'yeniEvrakNo']);

  Route::post('stok29_siparisGetir',[stok29_controller::class,'siparisGetir']);
  Route::post('stok29_siparisGetirETable',[stok29_controller::class,'siparisGetirETable']);
  Route::post('stok60_siparisGetir',[stok60_controller::class,'siparisGetir']);
  Route::post('stok60_siparisGetirETable',[stok60_controller::class,'siparisGetirETable']);
  Route::post('tezgah_is_planlama_operasyonGetir',[tezgah_is_planlama_controller::class,'operasyonGetir']);
  Route::post('tezgah_is_planlama_operasyonGetirETable',[tezgah_is_planlama_controller::class,'operasyonGetirETable']);
  Route::post('tezgah_is_planlama_tezgahAdiGetir',[tezgah_is_planlama_controller::class,'tezgahAdiGetir']);

  Route::post('stok26_createLocationSelect',[stok26_controller::class,'createLocationSelect']);
  Route::post('stok21_createLocationSelect',[stok21_controller::class,'createLocationSelect']);
  Route::post('mmps10_createKaynakKodSelect',[mmps10_controller::class,'createKaynakKodSelect']);
  Route::post('bomu01_createKaynakKodSelect',[bomu01_controller::class,'createKaynakKodSelect']);
  Route::post('/maliyet_createKaynakKodSelect',[Maliyet::class,'createKaynakKodSelect']);

  Route::post('mmps10_getStok10aToTable',[mmps10_controller::class,'getStok10aToTable']);
  Route::post('mmps10_getSipToEvrak',[mmps10_controller::class,'getSipToEvrak']);
  Route::post('sfdc31_getMPSToEvrak',[calisma_bildirimi_controller::class,'getMPSToEvrak']);
  // Route::get('/get-tezgah-plan', [calisma_bildirimi_controller::class, 'getTezgahPlan']);
  Route::post('stok01_getStok01',[stok01_controller::class,'getStok01']);
  Route::post('siparisraporlari_getStok01',[siparisraporlari_controller::class,'getStok01']);
  Route::post('siparisraporlari_getStok40',[siparisraporlari_controller::class,'getStok40']);

  Route::post('main_getIlceler',[main_controller::class,'getIlceler']);

  Route::post('dosyalar00_dosyaEkle',[dosyalar00_controller::class,'dosyaEkle']);
  Route::post('dosyalar00_dosyalariGetir',[dosyalar00_controller::class,'dosyalariGetir']);
  Route::post('dosyalar00_dosyaSil',[dosyalar00_controller::class,'dosyaSil']);

  Route::get('/teklif_fiyat_analiz',[Teklif_fiyat_analiz::class,'index']);
  Route::post('/maliyetlendire_islemler',[Teklif_fiyat_analiz::class,'islemler'])->name('maliyetlendire_islemler');
  Route::post('/maliyetlendire_createKaynakKodSelect',[Teklif_fiyat_analiz::class,'createKaynakKodSelect']);
  Route::post('/maliyet_hesapla',[Teklif_fiyat_analiz::class,'maliyet_hesapla'])->name('maliyet_hesapla');
  Route::post('/doviz_kur_getir',[Teklif_fiyat_analiz::class,'doviz_kur_getir'])->name('doviz_kur_getir');
  Route::post('/recetedenHesapla',[Teklif_fiyat_analiz::class,'recetedenHesapla'])->name('recetedenHesapla');
  Route::post('/evrakNoGetir',[Teklif_fiyat_analiz::class,'evrakNoGetir'])->name('evrakNoGetir');

  Route::post('/is_siralama_islemler',[Issiralama::class,'islemler']);
  Route::post('/is_sirala',[Issiralama::class,'is_sirala']);

    // GET'LER

  Route::get('/index', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

  // dikkat her sayfa için bir route yönlendirmesi olur controllers içerisinden view e yönlendirme yapılıyor
  Route::get('/kart_stokk',[stok00_controller::class,'getstok00']);

  Route::group(['middleware' => ['auth']], function() {

  Route::get('zorunlu_alan',[tmustr_controller::class,'index'])->name('zorunlu_alan');
  Route::get('/tmustr/alanlar/{tablo}', [tmustr_controller::class, 'getAlanlar']);
  Route::post('/tmustr_islemler', [tmustr_controller::class, 'islemler']);

  Route::get('info',[info_controller::class,'index'])->name('info');
  Route::post('info_islemler',[info_controller::class,'islemler'])->name('info_islemler');

  Route::get('is_siralama',[Issiralama::class,'index'])->name('is_siralama');
  Route::get('kart_cari',[cari00_controller::class,'index'])->name('kart_cari');
  Route::get('kart_tezgah',[imlt00_controller::class,'index'])->name('kart_tezgah');
  Route::get('kart_kalibrasyon',[imlt00_kalibrasyon_controller::class,'index'])->name('kart_kalibrasyon');
  Route::get('calisma_bildirimi',[calisma_bildirimi_controller::class,'index'])->name('calisma_bildirimi');
  Route::get('calisma_bildirimi_oprt',[calisma_bildirimi_controller::class,'index_oprt'])->name('calisma_bildirimi_oprt');
  Route::get('kart_stok',[stok00_controller::class,'index'])->name('kart_stok');
  Route::get('kart_depo',[gdef00_controller::class,'index'])->name('kart_depo');
  Route::get('kart_operasyon',[imlt01_controller::class,'index'])->name('kart_operasyon');
  Route::get('kart_personel',[pers00_pers_controller::class,'index'])->name('kart_personel');
  Route::get('kart_operator',[pers00_opt_controller::class,'index'])->name('kart_operator');
  Route::post('pers00_opt_islemler',[pers00_opt_controller::class,'islemler'])->name('islemler');
  Route::get('kart_kontakt',[kontakt00_controller::class,'index'])->name('kart_kontakt');
  Route::get('kart_kalip',[kalip00_controller::class,'index'])->name('kart_kalip');

  Route::get('satinalmairsaliyesi',[stok29_controller::class,'index'])->name('satinalmairsaliyesi');
  Route::get('satinalmasiparisi',[stok46_controller::class,'index'])->name('satinalmasiparisi');
  Route::get('satinalmaTalepleri',[stok47_controller::class,'index'])->name('satinalmaTalepleri');
  Route::post('bakiyeHesapla2',[stok46_controller::class,'bakiyeHesapla'])->name('bakiyeHesapla2');
  Route::get('sevkirsaliyesi',[stok60_controller::class,'index'])->name('sevkirsaliyesi');
  Route::get('mevcutVeriler',[stok60_controller::class,'mevcutVeriler'])->name('mevcutVerileriGetir');
  Route::get('stok_harketleri',[main_controller::class,'stok_harketleri'])->name('stok_harketleri');
  Route::post('sevkirsaliyesi_stokAdiGetir',[stok60_controller::class,'stokAdiGetir']);
  Route::get('satissiparisi',[stok40_controller::class,'index'])->name('satissiparisi');
  Route::post('bakiyeHesapla',[stok40_controller::class,'bakiyeHesapla'])->name('bakiyeHesapla');
  Route::get('urunagaci',[bomu01_controller::class,'index'])->name('urunagaci');

  Route::post('/import', [App\Http\Controllers\dosyalar00_controller::class, 'import'])->name('import');

  Route::get('gecerlilokasyonlar',[stok69_controller::class,'index'])->name('gecerlilokasyonlar');
  Route::get('stokgiriscikis',[stok21_controller::class,'index'])->name('stokgiriscikis');
  Route::get('uretim_fisi',[stok20_controller::class,'index'])->name('uretim_fisi');
  Route::post('receteden-hesapla', [stok20_controller::class, 'hesapla'])->name('receteden-hesapla');
  Route::post('mpsden-hesapla', [stok20_controller::class, 'hesaplaMPS'])->name('mpsden-hesapla');
  Route::get('depodandepoyatransfer',[stok26_controller::class,'index'])->name('depodandepoyatransfer');
  Route::get('etiket_bolme',[stok25_controller::class,'index'])->name('etiket_bolme');
  Route::get('mpsgiriskarti',[mmps10_controller::class,'index'])->name('mpsgiriskarti');

  Route::get('gk_tanimlari',[gecous_controller::class,'index'])->name('gk_tanimlari');
  Route::get('doviz_kuru',[DovizKuruController::class,'index']);

  Route::get('stok_tv',[stok01_controller::class,'index'])->name('stok_tv');
  Route::get('stok_hareketleri',[stok01_controller::class,'index2'])->name('stok_hareketleri');
  Route::get('siparis_raporlari',[siparisraporlari_controller::class,'index'])->name('siparis_raporlari');

  Route::get('fiyat_listesi',[stok48_controller::class,'index'])->name('fiyat_listesi');
  Route::get('operasyon_bildirimi',[opbild_controller::class,'index'])->name('operasyon_bildirimi');

  Route::get('fasonsevkirsaliyesi',[stok63_controller::class,'index'])->name('fasonsevkirsaliyesi');
  Route::get('fasongelisirsaliyesi',[stok68_controller::class,'index'])->name('fasongelisirsaliyesi');
  Route::get('dys',[dys_controller::class,'index'])->name('dys');
  Route::get('QLT',[qval10_controller::class,'index'])->name('QLT');
  Route::post('qval10_islemler',[qval10_controller::class,'islemler']);

  Route::get('giris_kalite_kontrol', [qval02_controller::class, 'index'])->name('giris_kalite_kontrol');

  Route::post('qval02_islemler',[qval02_controller::class,'islemler']);
  Route::post('sablonGetir',[qval02_controller::class,'sablonGetir']);

  Route::get('final_kalite_kontrol', [fkk_controller::class, 'index'])->name('final_kalite_kontrol');
  Route::post('fkk_islemler',[fkk_controller::class,'islemler']);
  Route::post('sablonGetir',[fkk_controller::class,'sablonGetir']);

  Route::get('user',[KullaniciIslemleri::class,'index'])->name('user');
  Route::get('logout_user', [KullaniciIslemleri::class, 'logout_user']);
  Route::get('toplu_mps_girisi',[toplumps_controller::class,'index'])->name('toplu_mps_girisi');

  Route::get('tezgahisplanlama',[tezgah_is_planlama_controller::class,'index'])->name('tezgahisplanlama');
  Route::get('planlanmis_isler',[tezgah_is_planlama_controller::class,'p_isler'])->name('p_isler');

  Route::get('barcode',[barcode_controller::class,'index'])->name('barcode');

  Route::get('etiketKarti',[Etiket_Karti_controller::class,'index'])->name('etiket_Karti');

  Route::get('/maliyet',[Maliyet::class,'index']);


  Route::get('/check-session', function () {
    if (auth()->check()) {
        return response()->json(['status' => 'active']);
    }
    return response()->json(['status' => 'expired']);
  });

  // Route::get('/sayfa', [YourController::class, 'metodAdi']);

  //Route::view('satinalmairsaliyesi','satinalmairsaliyesi');
  //Route::view('satinalmasiparisi','satinalmasiparisi');
  //Route::view('sevkirsaliyesi','sevkirsaliyesi');
  //Route::view('satissiparisi','satissiparisi');
  //Route::view('urunagaci','urunagaci');
  Route::view('change_password','change_password');

  Route::get('/notifications/poll', [NotificationController::class, 'poll']); // Yeni
  Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead']);
});