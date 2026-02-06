@extends('layout.mainlayout')

@php
	if (Auth::check()) {
		$user = Auth::user();
	}
	$kullanici_veri = DB::table('users')->where('id', $user->id)->first();
	$database = trim($kullanici_veri->firma) . '.dbo.';

	$ekran = "SATALMIRS";
	$ekranRumuz = "TAKVIM0";
	$ekranAdi = "Çalışma Takvimi";
	$ekranLink = "calismaTakvimi";
	$ekranTableE = "TAKVM0E";
	$ekranTableT = "TAKVM0T";
    $ekranKayitSatirKontrol = "true";

	$kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
	$kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
	$kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

	// $evrakno = null;
    if (isset($_GET['ID'])) {
        $sonID = $_GET['ID'];
    } else {
        $lastRow = DB::table($database . $ekranTableE)
            ->orderBy('ID', 'desc')
            ->first();

        $sonID = $lastRow ? $lastRow->EVRAKNO : null;
    }

    $kart_veri = null;
    if ($sonID) {
        $kart_veri = DB::table($database . $ekranTableE)
            ->where('EVRAKNO', $sonID)
            ->first();
    }

    $t_veri = DB::table($database . $ekranTableT)->where('EVRAKNO',$sonID)->get();
    
    if (isset($kart_veri)) {
        $ilkEvrak=DB::table($ekranTableE)->orderBy('ID','asc')->value('EVRAKNO');
        $sonEvrak=DB::table($ekranTableE)->orderBy('ID','desc')->value('EVRAKNO');
        $sonrakiEvrak=DB::table($ekranTableE)->where('ID', '>', $kart_veri->ID)->min('EVRAKNO');
        $oncekiEvrak=DB::table($ekranTableE)->where('ID', '<', $kart_veri->ID)->max('EVRAKNO');
    }
@endphp
@section('content')
<style>
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --danger-color: #dc3545;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .page-header {
            padding: 12px 15px;
            border-bottom: 2px solid var(--border-color);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px 8px 0 0;
        }

        .page-header h4 {
            margin: 0;
            color: white;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .page-header p {
            margin: 5px 0 0 0;
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
        }

        .content-area {
            padding: 15px;
        }

        .nav-tabs {
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 15px;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            padding: 6px 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary-color);
            border-color: transparent;
            background-color: rgba(13, 110, 253, 0.05);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background-color: transparent;
            border-color: transparent transparent var(--primary-color);
            font-weight: 600;
        }

        .work-mode-toggle {
            background: var(--light-bg);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 17.5px;
            border: 1px solid var(--border-color);
        }

        .work-mode-toggle label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
            display: block;
            font-size: 0.95rem;
        }

        .btn-group-toggle .btn {
            padding: 5px 12px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-check:checked + .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-check:checked + .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .time-selection-area {
            background: white;
            padding: 12.5px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .time-selection-area.disabled {
            opacity: 0.5;
            pointer-events: none;
            background: #f8f9fa;
        }

        .time-input-group {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .time-input-wrapper {
            flex: 1;
        }

        .time-input-wrapper label {
            display: block;
            font-weight: 600;
            color: #495057;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .visual-timeline {
            margin-top: 12.5px;
            padding: 10px;
            background: linear-gradient(to bottom, #f8f9fa 0%, #fff 100%);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .visual-timeline h6 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 7.5px;
        }

        .timeline-bar {
            height: 40px;
            background: #e9ecef;
            border-radius: 6px;
            position: relative;
            overflow: hidden;
        }

        .timeline-work-block {
            position: absolute;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .timeline-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 4px;
            font-size: 0.75rem;
            color: #6c757d;
        }

        .action-buttons {
            margin: 5px 0px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-primary {
            padding: 5px 15px;
            font-weight: 600;
        }

        .status-badge {
            padding: 3px 6px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-badge.active {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-badge.inactive {
            background-color: #f8d7da;
            color: #842029;
        }

        .info-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .quick-presets {
            margin-top: 7.5px;
            padding-top: 7.5px;
            border-top: 1px dashed var(--border-color);
        }

        .quick-presets small {
            color: #6c757d;
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .preset-btn {
            font-size: 0.8rem;
            padding: 2px 6px;
            margin-right: 4px;
            margin-bottom: 4px;
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
        .action-group {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 0 6px;
            border-right: 1px solid #dee2e6;
        }
        
        .action-group:first-child {
            padding-left: 0;
        }
        
        .action-group:last-child {
            border-right: none;
            padding-right: 0;
        }
</style>
    <div class="content-wrapper">
        @include('layout.util.evrakContentHeader')
		@include('layout.util.logModal', ['EVRAKTYPE' => 'TAKVIM0', 'EVRAKNO' => @$kart_veri->EVRAKNO])
        @if($ekranKayitSatirKontrol == "true")
            <input type="hidden" name="LAST_TRNUM" id="LAST_TRNUM" value="@php if(isset($kart_veri->LAST_TRNUM)) { echo @$kart_veri->LAST_TRNUM; } else { echo '000000'; } @endphp">
            <input type="hidden" name="LAST_TRNUM2" id="LAST_TRNUM2" value="@php if(isset($kart_veri->LAST_TRNUM2)) { echo @$kart_veri->LAST_TRNUM2; } else { echo '000000'; } @endphp">
            <input type="hidden" name="LAST_TRNUM3" id="LAST_TRNUM3" value="@php if(isset($kart_veri->LAST_TRNUM3)) { echo @$kart_veri->LAST_TRNUM3; } else { echo '000000'; } @endphp">
        @endif
        <div class="content">
            <!-- Gün Sekmeleri -->
            <ul class="nav nav-tabs" id="dayTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="monday-tab" data-bs-toggle="tab" data-bs-target="#monday" type="button" role="tab">
                        Pazartesi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tuesday-tab" data-bs-toggle="tab" data-bs-target="#tuesday" type="button" role="tab">
                        Salı
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="wednesday-tab" data-bs-toggle="tab" data-bs-target="#wednesday" type="button" role="tab">
                        Çarşamba
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="thursday-tab" data-bs-toggle="tab" data-bs-target="#thursday" type="button" role="tab">
                        Perşembe
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="friday-tab" data-bs-toggle="tab" data-bs-target="#friday" type="button" role="tab">
                        Cuma
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="saturday-tab" data-bs-toggle="tab" data-bs-target="#saturday" type="button" role="tab">
                        Cumartesi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sunday-tab" data-bs-toggle="tab" data-bs-target="#sunday" type="button" role="tab">
                        Pazar
                    </button>
                </li>
                <li class="nav-item ms-auto d-flex justify-content-center align-items-center gap-3" role="presentation">
                    <div class="action-group">
                        <a  
                            href="@php if (isset($ilkEvrak)) { echo $ekranLink.'?ID='.$ilkEvrak; } else { echo '#'; } @endphp" 
                            class="evrak-btn nav-btn" 
                            title="İlk Kart">
                            <i class="fa fa-angle-double-left"></i>
                        </a>
                        
                        <a  
                            href="@php if (isset($oncekiEvrak)) { echo $ekranLink.'?ID='.$oncekiEvrak; } else { echo '#'; } @endphp" 
                            class="evrak-btn nav-btn" 
                            title="Önceki Kart">
                            <i class="fa fa-angle-left"></i>
                        </a>
                        
                        <a  
                            href="@php if (isset($sonrakiEvrak)) { echo $ekranLink.'?ID='.$sonrakiEvrak; } else { echo '#'; } @endphp" 
                            class="evrak-btn nav-btn" 
                            title="Sonraki Kart">
                            <i class="fa fa-angle-right"></i>
                        </a>
                        
                        <a  
                            href="@php if (isset($sonEvrak)) { echo $ekranLink.'?ID='.$sonEvrak; } else { echo '#'; } @endphp" 
                            class="evrak-btn nav-btn" 
                            title="Son Kart">
                            <i class="fa fa-angle-double-right"></i>
                        </a>
                    </div>
                    <div class="d-flex">
                        <button class="evrak-btn new-btn">
                            <i class="fa-solid fa-file-circle-plus"></i>
                        </button>
                        <button class="evrak-btn save-btn" style="color: #198754;" onclick="saveCalendar()">
                            <i class="fa fa-save"></i>
                        </button>
                        <form action="{{ route('calismaTakvimi.sil')  }}" method="POST">
                            @csrf
                            <input type="hidden" name="EVRAKNO" value="{{ @$kart_veri->EVRAKNO }}">
                            <button class="evrak-btn delete-btn" style="color: #198754;" >
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </li>
            </ul>

            <div class="row mb-1" id="verilerForm">
                <div class="col-6">
                    <label for="">EVRAKNO</label>
                    <input type="text" class="form-control" id="EVRAKNO" value="{{ @$kart_veri->EVRAKNO }}">
                </div>
                <div class="col-6">
                    <label for="">AÇIKLAMA</label>
                    <input type="text" class="form-control" id="ACIKLAMA" value="{{ @$kart_veri->ACIKLAMA }}">
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <div>
                    <button class="btn btn-secondary" onclick="copyFromPrevious()">
                        <i class="bi bi-clipboard"></i> Önceki Günden Kopyala
                    </button>
                    <button class="btn btn-secondary ms-2" onclick="applyToAllDays()">
                        <i class="bi bi-arrow-repeat"></i> Tüm Günlere Uygula
                    </button>
                </div>
            </div>

            <!-- Tab İçerikleri -->
            <div class="tab-content" id="dayTabsContent">
                <!-- Pazartesi -->
                <div class="tab-pane fade show active" id="monday" role="tabpanel">
                    <div class="work-mode-toggle">
                        <label>Çalışma Durumu</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="monday-mode" id="monday-working" value="working" checked>
                            <label class="btn btn-success" for="monday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="monday-mode" id="monday-full" value="full">
                            <label class="btn btn-success" for="monday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="monday-mode" id="monday-off" value="off">
                            <label class="btn btn-danger" for="monday-off">
                                <i class="bi bi-x-circle"></i> Tatil
                            </label>
                        </div>
                    </div>

                    <div class="time-selection-area" id="monday-time-area">
                        <div class="time-input-group">
                            <div class="time-input-wrapper">
                                <label for="monday-start">Başlangıç Saati</label>
                                <input type="time" class="form-control form-control-lg" id="monday-start" value="08:00" step="900">
                            </div>
                            <div class="d-flex align-items-end pb-2">
                                <i class="bi bi-arrow-right fs-4 text-muted"></i>
                            </div>
                            <div class="time-input-wrapper">
                                <label for="monday-end">Bitiş Saati</label>
                                <input type="time" class="form-control form-control-lg" id="monday-end" value="18:00" step="900">
                            </div>
                        </div>

                        <div class="info-text">
                            <i class="bi bi-info-circle"></i> Saatler 15 dakika aralıklarla seçilebilir
                        </div>

                        <div class="quick-presets">
                            <small>Hızlı Seçenekler:</small>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('monday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('monday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('monday', '08:30', '17:30')">08:30 - 17:30</button>
                        </div>

                        <div class="visual-timeline">
                            <h6><i class="bi bi-graph-up"></i> Görsel Özet</h6>
                            <div class="timeline-bar">
                                <div class="timeline-work-block" id="monday-visual" style="left: 33.33%; width: 41.67%">
                                    08:00 - 18:00
                                </div>
                            </div>
                            <div class="timeline-labels">
                                <span>00:00</span>
                                <span>06:00</span>
                                <span>12:00</span>
                                <span>18:00</span>
                                <span>24:00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diğer günler için benzer yapı -->
                <div class="tab-pane fade" id="tuesday" role="tabpanel">
                    <div class="work-mode-toggle">
                        <label>Çalışma Durumu</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="tuesday-mode" id="tuesday-working" value="working" checked>
                            <label class="btn btn-success" for="tuesday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="tuesday-mode" id="tuesday-full" value="full">
                            <label class="btn btn-success" for="tuesday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="tuesday-mode" id="tuesday-off" value="off">
                            <label class="btn btn-danger" for="tuesday-off">
                                <i class="bi bi-x-circle"></i> Tatil
                            </label>
                        </div>
                    </div>

                    <div class="time-selection-area" id="tuesday-time-area">
                        <div class="time-input-group">
                            <div class="time-input-wrapper">
                                <label for="tuesday-start">Başlangıç Saati</label>
                                <input type="time" class="form-control form-control-lg" id="tuesday-start" value="08:00" step="900">
                            </div>
                            <div class="d-flex align-items-end pb-2">
                                <i class="bi bi-arrow-right fs-4 text-muted"></i>
                            </div>
                            <div class="time-input-wrapper">
                                <label for="tuesday-end">Bitiş Saati</label>
                                <input type="time" class="form-control form-control-lg" id="tuesday-end" value="18:00" step="900">
                            </div>
                        </div>

                        <div class="info-text">
                            <i class="bi bi-info-circle"></i> Saatler 15 dakika aralıklarla seçilebilir
                        </div>

                        <div class="quick-presets">
                            <small>Hızlı Seçenekler:</small>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('tuesday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('tuesday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('tuesday', '08:30', '17:30')">08:30 - 17:30</button>
                        </div>

                        <div class="visual-timeline">
                            <h6><i class="bi bi-graph-up"></i> Görsel Özet</h6>
                            <div class="timeline-bar">
                                <div class="timeline-work-block" id="tuesday-visual" style="left: 33.33%; width: 41.67%">
                                    08:00 - 18:00
                                </div>
                            </div>
                            <div class="timeline-labels">
                                <span>00:00</span>
                                <span>06:00</span>
                                <span>12:00</span>
                                <span>18:00</span>
                                <span>24:00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Çarşamba -->
                <div class="tab-pane fade" id="wednesday" role="tabpanel">
                    <div class="work-mode-toggle">
                        <label>Çalışma Durumu</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="wednesday-mode" id="wednesday-working" value="working" checked>
                            <label class="btn btn-success" for="wednesday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="wednesday-mode" id="wednesday-full" value="full">
                            <label class="btn btn-success" for="wednesday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="wednesday-mode" id="wednesday-off" value="off">
                            <label class="btn btn-danger" for="wednesday-off">
                                <i class="bi bi-x-circle"></i> Tatil
                            </label>
                        </div>
                    </div>

                    <div class="time-selection-area" id="wednesday-time-area">
                        <div class="time-input-group">
                            <div class="time-input-wrapper">
                                <label for="wednesday-start">Başlangıç Saati</label>
                                <input type="time" class="form-control form-control-lg" id="wednesday-start" value="08:00" step="900">
                            </div>
                            <div class="d-flex align-items-end pb-2">
                                <i class="bi bi-arrow-right fs-4 text-muted"></i>
                            </div>
                            <div class="time-input-wrapper">
                                <label for="wednesday-end">Bitiş Saati</label>
                                <input type="time" class="form-control form-control-lg" id="wednesday-end" value="18:00" step="900">
                            </div>
                        </div>

                        <div class="info-text">
                            <i class="bi bi-info-circle"></i> Saatler 15 dakika aralıklarla seçilebilir
                        </div>

                        <div class="quick-presets">
                            <small>Hızlı Seçenekler:</small>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('wednesday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('wednesday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('wednesday', '08:30', '17:30')">08:30 - 17:30</button>
                        </div>

                        <div class="visual-timeline">
                            <h6><i class="bi bi-graph-up"></i> Görsel Özet</h6>
                            <div class="timeline-bar">
                                <div class="timeline-work-block" id="wednesday-visual" style="left: 33.33%; width: 41.67%">
                                    08:00 - 18:00
                                </div>
                            </div>
                            <div class="timeline-labels">
                                <span>00:00</span>
                                <span>06:00</span>
                                <span>12:00</span>
                                <span>18:00</span>
                                <span>24:00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Perşembe -->
                <div class="tab-pane fade" id="thursday" role="tabpanel">
                    <div class="work-mode-toggle">
                        <label>Çalışma Durumu</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="thursday-mode" id="thursday-working" value="working" checked>
                            <label class="btn btn-success" for="thursday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="thursday-mode" id="thursday-full" value="full">
                            <label class="btn btn-success" for="thursday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="thursday-mode" id="thursday-off" value="off">
                            <label class="btn btn-danger" for="thursday-off">
                                <i class="bi bi-x-circle"></i> Tatil
                            </label>
                        </div>
                    </div>

                    <div class="time-selection-area" id="thursday-time-area">
                        <div class="time-input-group">
                            <div class="time-input-wrapper">
                                <label for="thursday-start">Başlangıç Saati</label>
                                <input type="time" class="form-control form-control-lg" id="thursday-start" value="08:00" step="900">
                            </div>
                            <div class="d-flex align-items-end pb-2">
                                <i class="bi bi-arrow-right fs-4 text-muted"></i>
                            </div>
                            <div class="time-input-wrapper">
                                <label for="thursday-end">Bitiş Saati</label>
                                <input type="time" class="form-control form-control-lg" id="thursday-end" value="18:00" step="900">
                            </div>
                        </div>

                        <div class="info-text">
                            <i class="bi bi-info-circle"></i> Saatler 15 dakika aralıklarla seçilebilir
                        </div>

                        <div class="quick-presets">
                            <small>Hızlı Seçenekler:</small>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('thursday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('thursday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('thursday', '08:30', '17:30')">08:30 - 17:30</button>
                        </div>

                        <div class="visual-timeline">
                            <h6><i class="bi bi-graph-up"></i> Görsel Özet</h6>
                            <div class="timeline-bar">
                                <div class="timeline-work-block" id="thursday-visual" style="left: 33.33%; width: 41.67%">
                                    08:00 - 18:00
                                </div>
                            </div>
                            <div class="timeline-labels">
                                <span>00:00</span>
                                <span>06:00</span>
                                <span>12:00</span>
                                <span>18:00</span>
                                <span>24:00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cuma -->
                <div class="tab-pane fade" id="friday" role="tabpanel">
                    <div class="work-mode-toggle">
                        <label>Çalışma Durumu</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="friday-mode" id="friday-working" value="working" checked>
                            <label class="btn btn-success" for="friday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="friday-mode" id="friday-full" value="full">
                            <label class="btn btn-success" for="friday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="friday-mode" id="friday-off" value="off">
                            <label class="btn btn-danger" for="friday-off">
                                <i class="bi bi-x-circle"></i> Tatil
                            </label>
                        </div>
                    </div>

                    <div class="time-selection-area" id="friday-time-area">
                        <div class="time-input-group">
                            <div class="time-input-wrapper">
                                <label for="friday-start">Başlangıç Saati</label>
                                <input type="time" class="form-control form-control-lg" id="friday-start" value="08:00" step="900">
                            </div>
                            <div class="d-flex align-items-end pb-2">
                                <i class="bi bi-arrow-right fs-4 text-muted"></i>
                            </div>
                            <div class="time-input-wrapper">
                                <label for="friday-end">Bitiş Saati</label>
                                <input type="time" class="form-control form-control-lg" id="friday-end" value="18:00" step="900">
                            </div>
                        </div>

                        <div class="info-text">
                            <i class="bi bi-info-circle"></i> Saatler 15 dakika aralıklarla seçilebilir
                        </div>

                        <div class="quick-presets">
                            <small>Hızlı Seçenekler:</small>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('friday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('friday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('friday', '08:30', '17:30')">08:30 - 17:30</button>
                        </div>

                        <div class="visual-timeline">
                            <h6><i class="bi bi-graph-up"></i> Görsel Özet</h6>
                            <div class="timeline-bar">
                                <div class="timeline-work-block" id="friday-visual" style="left: 33.33%; width: 41.67%">
                                    08:00 - 18:00
                                </div>
                            </div>
                            <div class="timeline-labels">
                                <span>00:00</span>
                                <span>06:00</span>
                                <span>12:00</span>
                                <span>18:00</span>
                                <span>24:00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cumartesi -->
                <div class="tab-pane fade" id="saturday" role="tabpanel">
                    <div class="work-mode-toggle">
                        <label>Çalışma Durumu</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="saturday-mode" id="saturday-working" value="working">
                            <label class="btn btn-success" for="saturday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="saturday-mode" id="saturday-full" value="full">
                            <label class="btn btn-success" for="saturday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="saturday-mode" id="saturday-off" value="off" checked>
                            <label class="btn btn-danger" for="saturday-off">
                                <i class="bi bi-x-circle"></i> Tatil
                            </label>
                        </div>
                    </div>

                    <div class="time-selection-area disabled" id="saturday-time-area">
                        <div class="time-input-group">
                            <div class="time-input-wrapper">
                                <label for="saturday-start">Başlangıç Saati</label>
                                <input type="time" class="form-control form-control-lg" id="saturday-start" value="08:00" step="900">
                            </div>
                            <div class="d-flex align-items-end pb-2">
                                <i class="bi bi-arrow-right fs-4 text-muted"></i>
                            </div>
                            <div class="time-input-wrapper">
                                <label for="saturday-end">Bitiş Saati</label>
                                <input type="time" class="form-control form-control-lg" id="saturday-end" value="18:00" step="900">
                            </div>
                        </div>

                        <div class="info-text">
                            <i class="bi bi-info-circle"></i> Saatler 15 dakika aralıklarla seçilebilir
                        </div>

                        <div class="quick-presets">
                            <small>Hızlı Seçenekler:</small>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('saturday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('saturday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('saturday', '08:30', '17:30')">08:30 - 17:30</button>
                        </div>

                        <div class="visual-timeline">
                            <h6><i class="bi bi-graph-up"></i> Görsel Özet</h6>
                            <div class="timeline-bar">
                                <div class="timeline-work-block" id="saturday-visual" style="left: 0%; width: 0%">
                                </div>
                            </div>
                            <div class="timeline-labels">
                                <span>00:00</span>
                                <span>06:00</span>
                                <span>12:00</span>
                                <span>18:00</span>
                                <span>24:00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pazar -->
                <div class="tab-pane fade" id="sunday" role="tabpanel">
                    <div class="work-mode-toggle">
                        <label>Çalışma Durumu</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="sunday-mode" id="sunday-working" value="working">
                            <label class="btn btn-success" for="sunday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="sunday-mode" id="sunday-full" value="full">
                            <label class="btn btn-success" for="sunday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="sunday-mode" id="sunday-off" value="off" checked>
                            <label class="btn btn-danger" for="sunday-off">
                                <i class="bi bi-x-circle"></i> Tatil
                            </label>
                        </div>
                    </div>

                    <div class="time-selection-area disabled" id="sunday-time-area">
                        <div class="time-input-group">
                            <div class="time-input-wrapper">
                                <label for="sunday-start">Başlangıç Saati</label>
                                <input type="time" class="form-control form-control-lg" id="sunday-start" value="08:00" step="900">
                            </div>
                            <div class="d-flex align-items-end pb-2">
                                <i class="bi bi-arrow-right fs-4 text-muted"></i>
                            </div>
                            <div class="time-input-wrapper">
                                <label for="sunday-end">Bitiş Saati</label>
                                <input type="time" class="form-control form-control-lg" id="sunday-end" value="18:00" step="900">
                            </div>
                        </div>

                        <div class="info-text">
                            <i class="bi bi-info-circle"></i> Saatler 15 dakika aralıklarla seçilebilir
                        </div>

                        <div class="quick-presets">
                            <small>Hızlı Seçenekler:</small>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('sunday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('sunday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-secondary preset-btn" onclick="setTime('sunday', '08:30', '17:30')">08:30 - 17:30</button>
                        </div>

                        <div class="visual-timeline">
                            <h6><i class="bi bi-graph-up"></i> Görsel Özet</h6>
                            <div class="timeline-bar">
                                <div class="timeline-work-block" id="sunday-visual" style="left: 0%; width: 0%">
                                </div>
                            </div>
                            <div class="timeline-labels">
                                <span>00:00</span>
                                <span>06:00</span>
                                <span>12:00</span>
                                <span>18:00</span>
                                <span>24:00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form id="takvimForm">
                <table class="table table-bordered text-center mt-2" id="veriTable">

                    <thead>
                        <tr>
                            <th></th>
                            <th>Başlangıç Tarihi</th>
                            <th>Başlangıç Saati</th>
                            <th>Bitiş Tarihi</th>
                            <th>Bitiş Saati</th>
                            <th>Çalışma / Tatil</th>
                            <th>Açıklama</th>
                        </tr>

                        <tr class="satirEkle" style="background-color:#3c8dbc">
                            <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button>
                            <td><input type='date' class='form-control' id='B_GUN' ></td>
                            <td><input type='time' class='form-control' id='B_SAAT'></td>
                            <td><input type='date' class='form-control' id='E_GUN' ></td>
                            <td><input type='time' class='form-control' id='E_SAAT'></td>
                            <td>
                                <select class='form-control select2' id='IS1_TATIL2'>
                                    <option value='IS'>Çalışma</option>
                                    <option value='TATIL'>Tatil</option>
                                </select>
                            </td>
                        <td><input type='text' class='form-control' id='ACIKLAMA'></td>
                    </thead>

                    <tbody>
                        @foreach($t_veri as $veri)
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-default delete-row">
                                        <i class="fa fa-minus" style="color:red"></i>
                                    </button>
                                    <input type="hidden" name="TRNUM[]" value="{{ $veri->TRNUM }}">
                                </td>

                                <td>
                                    <input type="date" class="form-control" name="B_GUN[]" value="{{ $veri->B_GUN }}">
                                </td>

                                <td>
                                    <input type="time" class="form-control" name="B_SAAT[]" value="{{ substr($veri->B_SAAT, 0, 5) }}">
                                </td>

                                <td>
                                    <input type="date" class="form-control" name="E_GUN[]" value="{{ $veri->E_GUN }}">
                                </td>

                                <td>
                                    <input type="time" class="form-control" name="E_SAAT[]" value="{{ substr($veri->E_SAAT, 0, 5) }}">
                                </td>

                                <td>
                                    <select class="form-control" name="IS1_TATIL2[]">
                                        <option value="IS" {{ $veri->IS1_TATIL2 == 'IS' ? 'selected' : '' }}>Çalışma</option>
                                        <option value="TATIL" {{ $veri->IS1_TATIL2 == 'TATIL' ? 'selected' : '' }}>Tatil</option>
                                    </select>
                                </td>

                                <td>
                                    <input type="text" class="form-control" name="ACIKLAMA[]" value="{{ $veri->ACIKLAMA }}">
                                </td>
                            </tr>

                        @endforeach
                    </tbody>

                </table>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            const dayNames = {
                'monday': 'Pazartesi',
                'tuesday': 'Salı',
                'wednesday': 'Çarşamba',
                'thursday': 'Perşembe',
                'friday': 'Cuma',
                'saturday': 'Cumartesi',
                'sunday': 'Pazar'
            };
            const dayDbFields = {
                'monday': 'D01',
                'tuesday': 'D02',
                'wednesday': 'D03',
                'thursday': 'D04',
                'friday': 'D05',
                'saturday': 'D06',
                'sunday': 'D07'
            };

            function updateDayMode(day, mode) {
                const $timeArea = $(`#${day}-time-area`);
                
                if (mode === 'off') {
                    $timeArea.addClass('disabled');
                    updateVisualTimeline(day, true, false);
                } else if (mode === 'full') {
                    $timeArea.addClass('disabled');
                    updateVisualTimeline(day, false, true);
                } else {
                    $timeArea.removeClass('disabled');
                    updateVisualTimeline(day, false, false);
                }
            }

            function updateVisualTimeline(day, isOff, isFull) {
                isOff = isOff || false;
                isFull = isFull || false;
                
                const $visual = $(`#${day}-visual`);
                const startValue = $(`#${day}-start`).val();
                const endValue = $(`#${day}-end`).val();

                if (isOff) {
                    $visual.css({
                        'left': '0%',
                        'width': '0%'
                    }).text('');
                    return;
                }

                if (isFull) {
                    $visual.css({
                        'left': '0%',
                        'width': '100%'
                    }).text('00:00 - 24:00 (Tüm Gün)');
                    return;
                }

                if (!startValue || !endValue) return;

                const startParts = startValue.split(':');
                const endParts = endValue.split(':');
                
                const startHour = parseInt(startParts[0]);
                const startMin = parseInt(startParts[1]);
                const endHour = parseInt(endParts[0]);
                const endMin = parseInt(endParts[1]);

                const startMinutes = startHour * 60 + startMin;
                const endMinutes = endHour * 60 + endMin;

                const leftPercent = (startMinutes / 1440) * 100;
                const widthPercent = ((endMinutes - startMinutes) / 1440) * 100;

                $visual.css({
                    'left': leftPercent + '%',
                    'width': widthPercent + '%'
                }).text(startValue + ' - ' + endValue);
            }

            window.setTime = function(day, start, end) {
                $(`#${day}-start`).val(start);
                $(`#${day}-end`).val(end);
                updateVisualTimeline(day, false, false);
            };

            function generateBinaryString(start, end, mode) {
                if (mode === 'off') {
                    return '0'.repeat(96);
                }
                
                if (mode === 'full') {
                    return '1'.repeat(96);
                }

                const startParts = start.split(':');
                const endParts = end.split(':');
                
                const startHour = parseInt(startParts[0]);
                const startMin = parseInt(startParts[1]);
                const endHour = parseInt(endParts[0]);
                const endMin = parseInt(endParts[1]);

                const startSlot = Math.floor((startHour * 60 + startMin) / 15);
                const endSlot = Math.floor((endHour * 60 + endMin) / 15);

                let binary = '';
                for (let i = 0; i < 96; i++) {
                    binary += (i >= startSlot && i < endSlot) ? '1' : '0';
                }

                return binary;
            }

            window.saveCalendar = function() {
                // Veritabanı kolonlarına göre veri hazırla
                const calendarData = {};

                days.forEach(function(day) {
                    const mode = $(`input[name="${day}-mode"]:checked`).val();
                    const start = $(`#${day}-start`).val();
                    const end = $(`#${day}-end`).val();
                    const dbField = dayDbFields[day];

                    calendarData[dbField] = generateBinaryString(start, end, mode);
                });

                let formData = new FormData($('#takvimForm')[0]);

                formData.append('_token', '{{ csrf_token() }}');
                formData.append('EVRAKNO', $('#EVRAKNO').val());
                formData.append('ACIKLAMA_GENEL', $('#ACIKLAMA').val());
                formData.append('D01', calendarData.D01);
                formData.append('D02', calendarData.D02);
                formData.append('D03', calendarData.D03);
                formData.append('D04', calendarData.D04);
                formData.append('D05', calendarData.D05);
                formData.append('D06', calendarData.D06);
                formData.append('D07', calendarData.D07);
                // Backend'e gönder
                $.ajax({
                    url: '{{ route("calismaTakvimi.kaydet") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Kaydediliyor...',
                            text: 'Lütfen bekleyiniz',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                    console.log(response)
                        if(response.success = true)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Çalışma takvimi başarıyla kaydedildi.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                        else
                        {
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata!',
                                text: response.message,
                                confirmButtonText: 'Tamam'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Kayıt sırasında bir hata oluştu: ' + error,
                            confirmButtonText: 'Tamam'
                        });
                        console.error('Hata:', xhr.responseText);
                    }
                });
            };

            window.copyFromPrevious = function() {
                const activeTab = $('.nav-link.active').attr('id').replace('-tab', '');
                const currentIndex = days.indexOf(activeTab);
                
                if (currentIndex === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Uyarı',
                        text: 'İlk gün için önceki gün bulunmuyor.',
                        confirmButtonText: 'Tamam'
                    });
                    return;
                }

                const previousDay = days[currentIndex - 1];
                const mode = $(`input[name="${previousDay}-mode"]:checked`).val();
                const start = $(`#${previousDay}-start`).val();
                const end = $(`#${previousDay}-end`).val();

                $(`#${activeTab}-${mode}`).prop('checked', true).trigger('change');
                $(`#${activeTab}-start`).val(start);
                $(`#${activeTab}-end`).val(end);
                updateVisualTimeline(activeTab, false, false);

                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı',
                    text: dayNames[previousDay] + ' günündeki ayarlar kopyalandı.',
                    timer: 1500,
                    showConfirmButton: false
                });
            };

            window.applyToAllDays = function() {
                const activeTab = $('.nav-link.active').attr('id').replace('-tab', '');
                const mode = $(`input[name="${activeTab}-mode"]:checked`).val();
                const start = $(`#${activeTab}-start`).val();
                const end = $(`#${activeTab}-end`).val();

                Swal.fire({
                    title: 'Emin misiniz?',
                    text: 'Bu günün ayarları tüm günlere uygulanacak. Devam etmek istiyor musunuz?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Evet, uygula',
                    cancelButtonText: 'İptal',
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        days.forEach(function(day) {
                            if (day !== activeTab) {
                                $(`#${day}-${mode}`).prop('checked', true).trigger('change');
                                $(`#${day}-start`).val(start);
                                $(`#${day}-end`).val(end);
                                updateVisualTimeline(day, false, false);
                            }
                        });
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı',
                            text: 'Ayarlar tüm günlere başarıyla uygulandı.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            };

            function parseBinaryString(binaryString) {
                // Tüm 0'lar ise tatil
                if (binaryString === '0'.repeat(96)) {
                    return { mode: 'off', start: '08:00', end: '18:00' };
                }
                
                // Tüm 1'ler ise 24 saat
                if (binaryString === '1'.repeat(96)) {
                    return { mode: 'full', start: '00:00', end: '24:00' };
                }
                
                // İlk 1'i bul (başlangıç)
                const startSlot = binaryString.indexOf('1');
                // Son 1'i bul (bitiş)
                const endSlot = binaryString.lastIndexOf('1') + 1;
                
                if (startSlot === -1) {
                    return { mode: 'off', start: '08:00', end: '18:00' };
                }
                
                // Slot'tan saate çevir
                const startMinutes = startSlot * 15;
                const endMinutes = endSlot * 15;
                
                const startHour = Math.floor(startMinutes / 60);
                const startMin = startMinutes % 60;
                const endHour = Math.floor(endMinutes / 60);
                const endMin = endMinutes % 60;
                
                const start = String(startHour).padStart(2, '0') + ':' + String(startMin).padStart(2, '0');
                const end = String(endHour).padStart(2, '0') + ':' + String(endMin).padStart(2, '0');
                
                return { mode: 'working', start: start, end: end };
            }

            function loadExistingData() {
                // Backend'den gelen binary string'leri parse et
                const existingData = {
                    D01: '{{ $kart_veri->D01 ?? "" }}',
                    D02: '{{ $kart_veri->D02 ?? "" }}',
                    D03: '{{ $kart_veri->D03 ?? "" }}',
                    D04: '{{ $kart_veri->D04 ?? "" }}',
                    D05: '{{ $kart_veri->D05 ?? "" }}',
                    D06: '{{ $kart_veri->D06 ?? "" }}',
                    D07: '{{ $kart_veri->D07 ?? "" }}'
                };
                
                days.forEach(function(day) {
                    const dbField = dayDbFields[day];
                    const binaryString = existingData[dbField];
                    
                    console.log(day + ' için binary:', binaryString);
                    
                    if (binaryString && binaryString.length === 96) {
                        // Binary string'den mode, start ve end'i çıkar
                        const parsedData = parseBinaryString(binaryString);
                        
                        console.log(day + ' parsed data:', parsedData);
                        
                        $(`#${day}-${parsedData.mode}`).prop('checked', true);
                        
                        if (parsedData.mode === 'working') {
                            $(`#${day}-start`).val(parsedData.start);
                            $(`#${day}-end`).val(parsedData.end);
                        }
                        
                        updateDayMode(day, parsedData.mode);
                    }
                });
            }

            // Her gün için event listener'ları ekle
            days.forEach(function(day) {
                // Mode değişikliği
                $(`input[name="${day}-mode"]`).on('change', function() {
                    updateDayMode(day, $(this).val());
                });

                // Saat değişikliği
                $(`#${day}-start, #${day}-end`).on('change', function() {
                    updateVisualTimeline(day, false, false);
                });
            });

            // Sayfa yüklendiğinde tüm görselleri güncelle
            days.forEach(function(day) {
                const mode = $(`input[name="${day}-mode"]:checked`).val();
                updateDayMode(day, mode);
            });

            // Mevcut verileri yükle (eğer düzenleme modundaysa)
            @if(isset($kart_veri) && $kart_veri)
                loadExistingData();
            @endif

            $("#addRow").on('click', function () {

                var TRNUM_FILL = getTRNUM();

                var satirEkleInputs = getInputs('satirEkle');
                var htmlCode = "";
                htmlCode += "<tr>";
                htmlCode += "<td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='" + TRNUM_FILL + "'></td>";
                htmlCode += "<td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td>";
                htmlCode += "<td><input type='date' class='form-control' name='B_GUN[]' value='" + satirEkleInputs.B_GUN + "'></td>";
                htmlCode += "<td><input type='time' class='form-control' name='B_SAAT[]' value='" + satirEkleInputs.B_SAAT + "'></td>";
                htmlCode += "<td><input type='date' class='form-control' name='E_GUN[]' value='" + satirEkleInputs.E_GUN + "'></td>";
                htmlCode += "<td><input type='time' class='form-control' name='E_SAAT[]' value='" + satirEkleInputs.E_SAAT + "'></td>";
                htmlCode += "<td>"
                htmlCode += "<select class='form-control' name='IS1_TATIL2[]'>";
                htmlCode += "<option value='IS' " + (satirEkleInputs.IS1_TATIL2 === 'IS' ? 'selected' : '') + ">Çalışma</option>";
                htmlCode += "<option value='TATIL' " + (satirEkleInputs.IS1_TATIL2 === 'TATIL' ? 'selected' : '') + ">Tatil</option>";
                htmlCode += "</select>";
                htmlCode += "</td>";
                htmlCode += "<td><input type='text' class='form-control' name='ACIKLAMA[]' value='" + satirEkleInputs.ACIKLAMA + "'></td>";
                htmlCode += "</tr>";

                if (satirEkleInputs.B_GUN == null || satirEkleInputs.B_GUN == " " || satirEkleInputs.B_GUN == "" || satirEkleInputs.B_SAAT == null || satirEkleInputs.B_SAAT == "" || satirEkleInputs.B_SAAT == " " || satirEkleInputs.E_GUN == null || satirEkleInputs.E_GUN == "" || satirEkleInputs.E_GUN == " " || satirEkleInputs.E_SAAT == null || satirEkleInputs.E_SAAT == "" || satirEkleInputs.E_SAAT == " ") {
                    eksikAlanHataAlert2();
                }
                else {
                    $("#veriTable > tbody").append(htmlCode);
                    updateLastTRNUM(TRNUM_FILL);
                    emptyInputs('satirEkle');
                }
            });

            $('.new-btn').on('click', function() {
                $('#veriTable tbody').empty();
                $('#baglantiliDokumanlarTable tbody').empty();

                $('.nav-tabs-custom input[type="text"]').val('');
                $('.nav-tabs-custom input[type="number"]').val('');
                $('#REVTAR').val('');
                $('select:not(#evrakSec)').val('').trigger('change');
                $('select:not(#evrakSec)').val(' ').trigger('change');


                $(':input', '#verilerForm')
                    .not(':button, :submit, :reset, :hidden, :checkbox, :radio')
                    .val('')
                    .prop('checked', false)
                    .prop('selected', false);

                $(':input', '#verilerForm').prop('checked', false)
            });
            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();

                let form = $(this).closest('form');

                Swal.fire({
                    title: 'Emin misin?',
                    text: 'Bu işlem geri alınamaz!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet',
                    cancelButtonText: 'Hayır'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection