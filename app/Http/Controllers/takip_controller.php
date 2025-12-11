<?php
namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class takip_controller extends Controller
{
    public function index()
    {
        return view('takip_listeleri');
    }

    public function islemler(Request $request)
    {
        // dd($request->all());
        $firma = $request->input('firma') . '.dbo.';
        $islem_turu = $request->kart_islemleri;
        $FORM = $request->FORM;
        $EVRAKNO = $request->evrakSec;

        // 8D
        $report_no = $request->report_no;
        $report_date = $request->report_date;
        $team = $request->team;

        // D0
        $d0_short = $request->d0_short;
        $d0_containment = $request->d0_containment;

        // D1
        $d1_team = $request->d1_team;

        // D2
        $d2_description = $request->d2_description;
        $d2_area = $request->d2_area;
        $d2_frequency = $request->d2_frequency;
        $d2_priority = $request->d2_priority;

        // D3 (JSON array)
        $d3_containment = $request->d3_containment;

        // D4
        $d4_rootcause = $request->d4_rootcause;
        $d4_method = $request->d4_method;

        // D5 (JSON array)
        $d5_actions = $request->d5_action_desc;

        // D6
        $d6_results = $request->d6_results;
        $d6_verified_at = $request->d6_verified_at;

        // D7
        $d7_preventive = $request->d7_preventive;

        // D8
        $d8_closure = $request->d8_closure;
        $d8_approved_by = $request->d8_approved_by;
        $d8_approved_at = $request->d8_approved_at;

        // Ekler
        $attachments = $request->attachments;
        $notes = $request->notes;


        // İç Hata Formu
        $ich_doc_no = $request->ich_doc_no;

        $ich_date = $request->ich_date;
        $ich_jobno = $request->ich_jobno;
        $ich_order_no = $request->ich_order_no;

        $ich_fault_types = $request->ich_fault_types;

        $ich_part_name = $request->ich_part_name;
        $ich_part_code = $request->ich_part_code;

        $ich_workorder = $request->ich_workorder;
        $ich_location = $request->ich_location;
        $ich_machine = $request->ich_machine;

        $ich_fault_code = $request->ich_fault_code;
        $ich_quantity = $request->ich_quantity;
        $ich_operator = $request->ich_operator;

        $ich_problem = $request->ich_problem;
        $ich_rootcause = $request->ich_rootcause;
        $ich_corrective = $request->ich_corrective;
        $ich_description = $request->ich_description;

        $ich_remarks = $request->ich_remarks;


        // iyileştirme çalışmaları
        $ic_doc_no = $request->ic_doc_no;
        $ic_publish_date = $request->ic_publish_date;
        $ic_rev_info = $request->ic_rev_info;

        $ic_no = $request->ic_no;
        $ic_date = $request->ic_date;
        $ic_type = $request->ic_type;
        $ic_department = $request->ic_department;
        $ic_person = $request->ic_person;
        $ic_part = $request->ic_part;
        $ic_process = $request->ic_process;

        $ic_current_status = $request->ic_current_status;
        $ic_new_status = $request->ic_new_status;

        $ic_gain_categories = $request->ic_gain_categories ?? [];
        $ic_functions = $request->ic_functions ?? [];

        $ic_result = $request->ic_result;
        $ic_finish_date = $request->ic_finish_date;


        // SAPMA
        // GENEL
        $sapma_talep_eden = $request->sapma_talep_eden;
        $sapma_rapor_no = $request->sapma_rapor_no;
        $sapma_tarih = $request->sapma_tarih;
        $sapma_devre_tarihi = $request->sapma_devre_tarihi;
        $sapma_degisim_tanimi = $request->sapma_degisim_tanimi;

        // DETAYLAR
        $sapma_teklif_tarifi = $request->sapma_teklif_tarifi;
        $sapma_mevcut_durum = $request->sapma_mevcut_durum;

        // TEKLİF EDEN
        $sapma_teklif_eden_isim = $request->sapma_teklif_eden_isim;
        $sapma_teklif_eden_bolum = $request->sapma_teklif_eden_bolum;
        $sapma_teklif_eden_gorev = $request->sapma_teklif_eden_gorev;

        // SEBEPLER
        $sapma_sebep_iade = $request->has('sebep1') ? 1 : 0;
        $sapma_sebep_yansanayi = $request->has('sebep2') ? 1 : 0;
        $sapma_sebep_imalat = $request->has('sebep3') ? 1 : 0;
        $sapma_sebep_iyilestirme = $request->has('sebep4') ? 1 : 0;
        $sapma_sebep_musteri = $request->has('sebep5') ? 1 : 0;
        $sapma_sebep_lisansor = $request->has('sebep6') ? 1 : 0;
        $sapma_sebep_kalite = $request->has('sebep7') ? 1 : 0;

        $sapma_musteri_adi = $request->sapma_musteri_adi;
        $sapma_lisansor_adi = $request->sapma_lisansor_adi;

        // ETKİLER
        $sapma_etki_urun = $request->has('sapma_etki_urun') ? 1 : 0;
        $sapma_etki_proses = $request->has('sapma_etki_proses') ? 1 : 0;
        $sapma_etki_teminyeri = $request->has('sapma_etki_teminyeri') ? 1 : 0;
        $sapma_etki_malzeme = $request->has('sapma_etki_malzeme') ? 1 : 0;
        $sapma_etki_ambalaj = $request->has('sapma_etki_ambalaj') ? 1 : 0;
        $sapma_etki_prosedur = $request->has('sapma_etki_prosedur') ? 1 : 0;
        $sapma_etki_diger = $request->sapma_etki_diger;

        // SINIF
        $sapma_sinif = $request->sapma_sinif;

        // KARAR / DEĞİŞİKLİK & SAPMA
        $sapma_degisiklik_durum = $request->sapma_degisiklik_durum;
        $sapma_degisiklik_sure = $request->sapma_degisiklik_sure;

        $sapma_durum = $request->sapma_durum;
        $sapma_gecerlilik_tarih = $request->sapma_gecerlilik_tarih;
        $sapma_gecerlilik_adet = $request->sapma_gecerlilik_adet;

        // SORUMLU & MALİYET
        $sapma_sorumlu_ad = $request->sapma_sorumlu_ad;
        $sapma_sorumlu_bolum = $request->sapma_sorumlu_bolum;
        $sapma_gerekli_sure = $request->sapma_gerekli_sure;
        $sapma_kalip_degisim = $request->sapma_kalip_degisim;
        $sapma_kalip_maliyet = $request->sapma_kalip_maliyet;
        $sapma_fiyat_degisim = $request->sapma_fiyat_degisim;
        $sapma_yeni_fiyat = $request->sapma_yeni_fiyat;

        // STOK
        $sapma_stok_miktar = $request->sapma_stok_miktar;
        $sapma_stok_yonetim = $request->sapma_stok_yonetim;

        // ANA SANAYİ
        $sapma_resim_deg_gerekli = $request->has('sapma_resim_deg_gerekli') ? 1 : 0;
        $sapma_resim_deg_gerekli_deg = $request->has('sapma_resim_deg_gerekli_degil') ? 1 : 0;

        // AÇIKLAMA
        $sapma_aciklama = $request->sapma_aciklama;

        // ONAYLAR
        $sapma_musteri_bilgi = $request->sapma_musteri_bilgi;
        $sapma_musteri_onay = $request->sapma_musteri_onay;
        $sapma_lisansor_bilgi = $request->sapma_lisansor_bilgi;
        $sapma_lisansor_onay = $request->sapma_lisansor_onay;

        $sapma_karar_bolum = $request->sapma_karar_bolum;
        $sapma_karar_imza = $request->sapma_karar_imza;
        $sapma_karar_tarih = $request->sapma_karar_tarih;

        $sapma_gm_onay = $request->sapma_gm_onay;
        $sapma_musteri_tem_onay = $request->sapma_musteri_tem_onay;
        $sapma_parca_no = $request->sapma_parca_no;

        switch ($islem_turu) {
            case 'kart_olustur':
                $SON_EVRAK = DB::table($firma . 'cgc70')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
                $SON_ID = $SON_EVRAK->EVRAKNO;

                $SON_ID = (int) $SON_ID;
                if ($SON_ID == NULL) {
                    $EVRAKNO = 1;
                } else {
                    $EVRAKNO = $SON_ID + 1;
                }

                $data = [
                    // GENEL
                    'd8_report_no' => $report_no,
                    'd8_report_date' => $report_date,
                    'd8_team' => $team,
                    'FORM' => $FORM,
                    'EVRAKNO' => $EVRAKNO,

                    // D0
                    'd8_d0_short' => $d0_short,
                    'd8_d0_containment' => $d0_containment,

                    // D1
                    'd8_d1_team' => $d1_team,

                    // D2
                    'd8_d2_description' => $d2_description,
                    'd8_d2_area' => $d2_area,
                    'd8_d2_frequency' => $d2_frequency,
                    'd8_d2_priority' => $d2_priority,

                    // D3 (JSON)
                    'd8_d3_containment' => is_array($d3_containment)
                        ? json_encode($d3_containment, JSON_UNESCAPED_UNICODE)
                        : $d3_containment,

                    // D4
                    'd8_d4_rootcause' => $d4_rootcause,
                    'd8_d4_method' => $d4_method,

                    // D5 (JSON)
                    'd8_d5_actions' => is_array($d5_actions)
                        ? json_encode($d5_actions, JSON_UNESCAPED_UNICODE)
                        : $d5_actions,

                    // D6
                    'd8_d6_results' => $d6_results,
                    'd8_d6_verified_at' => $d6_verified_at,

                    // D7
                    'd8_d7_preventive' => $d7_preventive,

                    // D8
                    'd8_d8_closure' => $d8_closure,
                    'd8_d8_approved_by' => $d8_approved_by,
                    'd8_d8_approved_at' => $d8_approved_at,

                    // EKLER
                    'd8_attachments' => is_array($attachments)
                        ? json_encode($attachments, JSON_UNESCAPED_UNICODE)
                        : $attachments,

                    'd8_notes' => $notes,




                    'ich_doc_no' => $ich_doc_no,

                    'ich_date' => $ich_date,
                    'ich_jobno' => $ich_jobno,
                    'ich_order_no' => $ich_order_no,

                    // MULTI SELECTION
                    'ich_fault_types' => is_array($ich_fault_types)
                        ? json_encode($ich_fault_types, JSON_UNESCAPED_UNICODE)
                        : $ich_fault_types,

                    'ich_part_name' => $ich_part_name,
                    'ich_part_code' => $ich_part_code,

                    'ich_workorder' => $ich_workorder,
                    'ich_location' => $ich_location,
                    'ich_machine' => $ich_machine,

                    'ich_fault_code' => $ich_fault_code,
                    'ich_quantity' => $ich_quantity,
                    'ich_operator' => $ich_operator,

                    'ich_problem' => $ich_problem,
                    'ich_rootcause' => $ich_rootcause,
                    'ich_corrective' => $ich_corrective,
                    'ich_description' => $ich_description,


                    // iyileştirme çalışmaları
                    'ic_doc_no' => $ic_doc_no,
                    'ic_publish_date' => $ic_publish_date,
                    'ic_rev_info' => $ic_rev_info,

                    'ic_no' => $ic_no,
                    'ic_date' => $ic_date,
                    'ic_type' => $ic_type,
                    'ic_department' => $ic_department,
                    'ic_person' => $ic_person,
                    'ic_part' => $ic_part,
                    'ic_process' => $ic_process,

                    'ic_current_status' => $ic_current_status,
                    'ic_new_status' => $ic_new_status,

                    'ic_gain_categories' => json_encode($ic_gain_categories, JSON_UNESCAPED_UNICODE),
                    'ic_functions' => json_encode($ic_functions, JSON_UNESCAPED_UNICODE),

                    'ic_result' => $ic_result,
                    'ic_finish_date' => $ic_finish_date,


                    // SAPMA

                    // GENEL
                    'sapma_talep_eden' => $sapma_talep_eden,
                    'sapma_rapor_no' => $sapma_rapor_no,
                    'sapma_tarih' => $sapma_tarih,
                    'sapma_devre_tarihi' => $sapma_devre_tarihi,
                    'sapma_degisim_tanimi' => $sapma_degisim_tanimi,

                    // DETAYLAR
                    'sapma_teklif_tarifi' => $sapma_teklif_tarifi,
                    'sapma_mevcut_durum' => $sapma_mevcut_durum,

                    // TEKLİF EDEN
                    'sapma_teklif_eden_isim' => $sapma_teklif_eden_isim,
                    'sapma_teklif_eden_bolum' => $sapma_teklif_eden_bolum,
                    'sapma_teklif_eden_gorev' => $sapma_teklif_eden_gorev,

                    // SEBEPLER (TINYINT)
                    'sapma_sebep_iade' => $sapma_sebep_iade,
                    'sapma_sebep_yansanayi' => $sapma_sebep_yansanayi,
                    'sapma_sebep_imalat' => $sapma_sebep_imalat,
                    'sapma_sebep_iyilestirme' => $sapma_sebep_iyilestirme,
                    'sapma_sebep_musteri' => $sapma_sebep_musteri,
                    'sapma_sebep_lisansor' => $sapma_sebep_lisansor,
                    'sapma_sebep_kalite' => $sapma_sebep_kalite,

                    'sapma_sebep_musteri_adi' => $sapma_musteri_adi,
                    'sapma_sebep_lisansor_adi' => $sapma_lisansor_adi,

                    // ETKİLER (TINYINT)
                    'sapma_etki_urun' => $sapma_etki_urun,
                    'sapma_etki_proses' => $sapma_etki_proses,
                    'sapma_etki_teminyeri' => $sapma_etki_teminyeri,
                    'sapma_etki_malzeme' => $sapma_etki_malzeme,
                    'sapma_etki_ambalaj' => $sapma_etki_ambalaj,
                    'sapma_etki_prosedur' => $sapma_etki_prosedur,
                    'sapma_etki_diger' => $sapma_etki_diger,

                    // SINIF
                    'sapma_sinif' => $sapma_sinif,

                    // KARAR / DEĞİŞİKLİK
                    'sapma_deg_parca' => $sapma_degisiklik_durum,
                    'sapma_deg_tarihine_kadar' => $sapma_degisiklik_sure,

                    // SAPMA DURUM
                    'sapma_durum' => $sapma_durum,
                    'sapma_gec_tarih' => $sapma_gecerlilik_tarih,
                    'sapma_gec_adet' => $sapma_gecerlilik_adet,

                    // SORUMLU & MALİYET
                    'sapma_sorumlu_ad' => $sapma_sorumlu_ad,
                    'sapma_sorumlu_bolum' => $sapma_sorumlu_bolum,
                    'sapma_gerekli_sure' => $sapma_gerekli_sure,
                    'sapma_kalip_degisim' => $sapma_kalip_degisim,
                    'sapma_kalip_maliyet' => $sapma_kalip_maliyet,
                    'sapma_fiyat_degisim' => $sapma_fiyat_degisim,
                    'sapma_yeni_fiyat' => $sapma_yeni_fiyat,

                    // STOK
                    'sapma_stok' => $sapma_stok_miktar,
                    'sapma_stok_yonetimi' => $sapma_stok_yonetim,

                    // ANA SANAYİ (TINYINT)
                    'sapma_resim_deg_gerekli' => $sapma_resim_deg_gerekli,
                    'sapma_resim_deg_gerekli_deg' => $sapma_resim_deg_gerekli_deg,

                    // AÇIKLAMA
                    'sapma_aciklama' => $sapma_aciklama,

                    // ONAYLAR
                    'sapma_musteri_bilgi' => $sapma_musteri_bilgi,
                    'sapma_musteri_onay' => $sapma_musteri_onay,
                    'sapma_lisansor_bilgi' => $sapma_lisansor_bilgi,
                    'sapma_lisansor_onay' => $sapma_lisansor_onay,
                    'sapma_karar_bolum' => $sapma_karar_bolum,
                    'sapma_karar_imza' => $sapma_karar_imza,
                    'sapma_karar_tarih' => $sapma_karar_tarih,
                    'sapma_gm_onay' => $sapma_gm_onay,
                    'sapma_musteri_tem_onay' => $sapma_musteri_tem_onay,
                    'sapma_parca_no' => $sapma_parca_no,
                ];


                DB::table($firma . 'cgc70')->insert($data);
                $sonID = DB::table($firma . 'cgc70')->max('ID');
                return redirect()->route('takip_listeleri', ['ID' => $sonID, 'kayit' => 'ok']);

                break;

            case 'kart_duzenle':
                $data = [
                    // GENEL
                    'd8_report_no' => $report_no,
                    'd8_report_date' => $report_date,
                    'd8_team' => $team,
                    'FORM' => $FORM,

                    // D0
                    'd8_d0_short' => $d0_short,
                    'd8_d0_containment' => $d0_containment,

                    // D1
                    'd8_d1_team' => $d1_team,

                    // D2
                    'd8_d2_description' => $d2_description,
                    'd8_d2_area' => $d2_area,
                    'd8_d2_frequency' => $d2_frequency,
                    'd8_d2_priority' => $d2_priority,

                    // D3 (JSON)
                    'd8_d3_containment' => is_array($d3_containment)
                        ? json_encode($d3_containment, JSON_UNESCAPED_UNICODE)
                        : $d3_containment,

                    // D4
                    'd8_d4_rootcause' => $d4_rootcause,
                    'd8_d4_method' => $d4_method,

                    // D5 (JSON)
                    'd8_d5_actions' => is_array($d5_actions)
                        ? json_encode($d5_actions, JSON_UNESCAPED_UNICODE)
                        : $d5_actions,

                    // D6
                    'd8_d6_results' => $d6_results,
                    'd8_d6_verified_at' => $d6_verified_at,

                    // D7
                    'd8_d7_preventive' => $d7_preventive,

                    // D8
                    'd8_d8_closure' => $d8_closure,
                    'd8_d8_approved_by' => $d8_approved_by,
                    'd8_d8_approved_at' => $d8_approved_at,

                    // EKLER
                    'd8_attachments' => is_array($attachments)
                        ? json_encode($attachments, JSON_UNESCAPED_UNICODE)
                        : $attachments,

                    'd8_notes' => $notes,





                    'ich_doc_no' => $ich_doc_no,

                    'ich_date' => $ich_date,
                    'ich_jobno' => $ich_jobno,
                    'ich_order_no' => $ich_order_no,

                    // MULTI SELECTION
                    'ich_fault_types' => is_array($ich_fault_types)
                        ? json_encode($ich_fault_types, JSON_UNESCAPED_UNICODE)
                        : $ich_fault_types,

                    'ich_part_name' => $ich_part_name,
                    'ich_part_code' => $ich_part_code,

                    'ich_workorder' => $ich_workorder,
                    'ich_location' => $ich_location,
                    'ich_machine' => $ich_machine,

                    'ich_fault_code' => $ich_fault_code,
                    'ich_quantity' => $ich_quantity,
                    'ich_operator' => $ich_operator,

                    'ich_problem' => $ich_problem,
                    'ich_rootcause' => $ich_rootcause,
                    'ich_corrective' => $ich_corrective,
                    'ich_description' => $ich_description,


                    // iyileştirme çalışmaları
                    'ic_doc_no' => $ic_doc_no,
                    'ic_publish_date' => $ic_publish_date,
                    'ic_rev_info' => $ic_rev_info,

                    'ic_no' => $ic_no,
                    'ic_date' => $ic_date,
                    'ic_type' => $ic_type,
                    'ic_department' => $ic_department,
                    'ic_person' => $ic_person,
                    'ic_part' => $ic_part,
                    'ic_process' => $ic_process,

                    'ic_current_status' => $ic_current_status,
                    'ic_new_status' => $ic_new_status,

                    'ic_gain_categories' => json_encode($ic_gain_categories, JSON_UNESCAPED_UNICODE),
                    'ic_functions' => json_encode($ic_functions, JSON_UNESCAPED_UNICODE),

                    'ic_result' => $ic_result,
                    'ic_finish_date' => $ic_finish_date,


                    // SAPMA

                    // GENEL
                    'sapma_talep_eden'          => $sapma_talep_eden,
                    'sapma_rapor_no'            => $sapma_rapor_no,
                    'sapma_tarih'               => $sapma_tarih,
                    'sapma_devre_tarihi'        => $sapma_devre_tarihi,
                    'sapma_degisim_tanimi'      => $sapma_degisim_tanimi,

                    // DETAYLAR
                    'sapma_teklif_tarifi'       => $sapma_teklif_tarifi,
                    'sapma_mevcut_durum'        => $sapma_mevcut_durum,

                    // TEKLİF EDEN
                    'sapma_teklif_eden_isim'    => $sapma_teklif_eden_isim,
                    'sapma_teklif_eden_bolum'   => $sapma_teklif_eden_bolum,
                    'sapma_teklif_eden_gorev'   => $sapma_teklif_eden_gorev,

                    // SEBEPLER (TINYINT)
                    'sapma_sebep_iade'          => $sapma_sebep_iade,
                    'sapma_sebep_yansanayi'     => $sapma_sebep_yansanayi,
                    'sapma_sebep_imalat'        => $sapma_sebep_imalat,
                    'sapma_sebep_iyilestirme'   => $sapma_sebep_iyilestirme,
                    'sapma_sebep_musteri'       => $sapma_sebep_musteri,
                    'sapma_sebep_lisansor'      => $sapma_sebep_lisansor,
                    'sapma_sebep_kalite'        => $sapma_sebep_kalite,

                    'sapma_sebep_musteri_adi'   => $sapma_musteri_adi,
                    'sapma_sebep_lisansor_adi'  => $sapma_lisansor_adi,

                    // ETKİLER (TINYINT)
                    'sapma_etki_urun'           => $sapma_etki_urun,
                    'sapma_etki_proses'         => $sapma_etki_proses,
                    'sapma_etki_teminyeri'      => $sapma_etki_teminyeri,
                    'sapma_etki_malzeme'        => $sapma_etki_malzeme,
                    'sapma_etki_ambalaj'        => $sapma_etki_ambalaj,
                    'sapma_etki_prosedur'       => $sapma_etki_prosedur,
                    'sapma_etki_diger' => $sapma_etki_diger,

                    // SINIF
                    'sapma_sinif'               => $sapma_sinif,

                    // KARAR / DEĞİŞİKLİK
                    'sapma_deg_parca'           => $sapma_degisiklik_durum,
                    'sapma_deg_tarihine_kadar'  => $sapma_degisiklik_sure,

                    // SAPMA DURUM
                    'sapma_durum'               => $sapma_durum,
                    'sapma_gec_tarih'           => $sapma_gecerlilik_tarih,
                    'sapma_gec_adet'            => $sapma_gecerlilik_adet,

                    // SORUMLU & MALİYET
                    'sapma_sorumlu_ad'          => $sapma_sorumlu_ad,
                    'sapma_sorumlu_bolum'       => $sapma_sorumlu_bolum,
                    'sapma_gerekli_sure'        => $sapma_gerekli_sure,
                    'sapma_kalip_degisim'       => $sapma_kalip_degisim,
                    'sapma_kalip_maliyet'       => $sapma_kalip_maliyet,
                    'sapma_fiyat_degisim'       => $sapma_fiyat_degisim,
                    'sapma_yeni_fiyat'          => $sapma_yeni_fiyat,

                    // STOK
                    'sapma_stok'                => $sapma_stok_miktar,
                    'sapma_stok_yonetimi'       => $sapma_stok_yonetim,

                    // ANA SANAYİ (TINYINT)
                    'sapma_resim_deg_gerekli'       => $sapma_resim_deg_gerekli,
                    'sapma_resim_deg_gerekli_deg'   => $sapma_resim_deg_gerekli_deg,

                    // AÇIKLAMA
                    'sapma_aciklama'            => $sapma_aciklama,

                    // ONAYLAR
                    'sapma_musteri_bilgi'       => $sapma_musteri_bilgi,
                    'sapma_musteri_onay'        => $sapma_musteri_onay,
                    'sapma_lisansor_bilgi'      => $sapma_lisansor_bilgi,
                    'sapma_lisansor_onay'       => $sapma_lisansor_onay,
                    'sapma_karar_bolum'         => $sapma_karar_bolum,
                    'sapma_karar_imza'          => $sapma_karar_imza,
                    'sapma_karar_tarih'         => $sapma_karar_tarih,
                    'sapma_gm_onay'             => $sapma_gm_onay,
                    'sapma_musteri_tem_onay'    => $sapma_musteri_tem_onay,
                    'sapma_parca_no' => $sapma_parca_no,
                ];

                DB::table($firma . 'cgc70')->where('ID', $EVRAKNO)->update($data);
                return redirect()->route('takip_listeleri', ['ID' => $request->ID_TO_REDIRECT, 'duzenleme' => 'ok']);
                break;

            case 'kart_sil':
                DB::table($firma . 'cgc70')->where('ID', $EVRAKNO)->delete();
                return redirect()->route('takip_listeleri', ['ID' => $request->ID_TO_REDIRECT, 'silme' => 'ok']);
        }

    }

    function gorsel(Request $request)
    {
        if(Auth::check()) {
          $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        $KOD = $request->input('KOD');
        $img = DB::table($firma.'dosyalar00')
            ->where('EVRAKNO',$KOD)
            ->where('EVRAKTYPE','STOK00')
            ->where('DOSYATURU','GORSEL')
            ->first();

        return isset($img->DOSYA) ? asset('dosyalar/'.$img->DOSYA) : 'https://community.softr.io/uploads/db9110/original/2X/7/74e6e7e382d0ff5d7773ca9a87e6f6f8817a68a6.jpeg';
    }
}
