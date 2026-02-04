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
	$ekranKayitSatirKontrol = "false";

	$kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
	$kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
	$kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

	// $evrakno = null;

	// if (isset($_GET['evrakno'])) {
	// 	$evrakno = $_GET['evrakno'];
	// }

	// if (isset($_GET['ID'])) {
	// 	$sonID = $_GET['ID'];
	// } else {
	// 	$sonID = DB::table($database . $ekranTableE)->min('id');
	// }
	// $kart_veri = DB::table($database . $ekranTableE)->where('id', $sonID)->first();

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
            padding: 24px 30px;
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
            padding: 30px;
        }

        .nav-tabs {
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 30px;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            padding: 12px 20px;
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
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
        }

        .work-mode-toggle label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 12px;
            display: block;
            font-size: 0.95rem;
        }

        .btn-group-toggle .btn {
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-check:checked + .btn-outline-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-check:checked + .btn-outline-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .time-selection-area {
            background: white;
            padding: 25px;
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
            margin-bottom: 20px;
        }

        .time-input-wrapper {
            flex: 1;
        }

        .time-input-wrapper label {
            display: block;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .visual-timeline {
            margin-top: 25px;
            padding: 20px;
            background: linear-gradient(to bottom, #f8f9fa 0%, #fff 100%);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .visual-timeline h6 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
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
            margin-top: 8px;
            font-size: 0.75rem;
            color: #6c757d;
        }

        .action-buttons {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-primary {
            padding: 10px 30px;
            font-weight: 600;
        }

        .status-badge {
            padding: 6px 12px;
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
            margin-top: 10px;
        }

        .quick-presets {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed var(--border-color);
        }

        .quick-presets small {
            color: #6c757d;
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .preset-btn {
            font-size: 0.8rem;
            padding: 4px 12px;
            margin-right: 8px;
            margin-bottom: 8px;
        }
</style>
    <div class="content-wrapper">
        @include('layout.util.evrakContentHeader')
		@include('layout.util.logModal', ['EVRAKTYPE' => 'TAKVIM0', 'EVRAKNO' => @$kart_veri->EVRAKNO])

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
            </ul>

            <!-- Tab İçerikleri -->
            <div class="tab-content" id="dayTabsContent">
                <!-- Pazartesi -->
                <div class="tab-pane fade show active" id="monday" role="tabpanel">
                    <div class="work-mode-toggle">
                        <label>Çalışma Durumu</label>
                        <div class="row mb-1">
                            <div class="col-6">
                                <label for="">EVRAKNO</label>
                                <input type="text" class="form-control" id="EVRAKNO">
                            </div>
                            <div class="col-6">
                                <label for="">AÇIKLAMA</label>
                                <input type="text" class="form-control" id="ACIKLAMA">
                            </div>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="monday-mode" id="monday-working" value="working" checked>
                            <label class="btn btn-outline-success" for="monday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="monday-mode" id="monday-full" value="full">
                            <label class="btn btn-outline-success" for="monday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="monday-mode" id="monday-off" value="off">
                            <label class="btn btn-outline-danger" for="monday-off">
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
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('monday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('monday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('monday', '08:30', '17:30')">08:30 - 17:30</button>
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
                            <label class="btn btn-outline-success" for="tuesday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="tuesday-mode" id="tuesday-full" value="full">
                            <label class="btn btn-outline-success" for="tuesday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="tuesday-mode" id="tuesday-off" value="off">
                            <label class="btn btn-outline-danger" for="tuesday-off">
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
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('tuesday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('tuesday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('tuesday', '08:30', '17:30')">08:30 - 17:30</button>
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
                            <label class="btn btn-outline-success" for="wednesday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="wednesday-mode" id="wednesday-full" value="full">
                            <label class="btn btn-outline-success" for="wednesday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="wednesday-mode" id="wednesday-off" value="off">
                            <label class="btn btn-outline-danger" for="wednesday-off">
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
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('wednesday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('wednesday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('wednesday', '08:30', '17:30')">08:30 - 17:30</button>
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
                            <label class="btn btn-outline-success" for="thursday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="thursday-mode" id="thursday-full" value="full">
                            <label class="btn btn-outline-success" for="thursday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="thursday-mode" id="thursday-off" value="off">
                            <label class="btn btn-outline-danger" for="thursday-off">
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
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('thursday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('thursday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('thursday', '08:30', '17:30')">08:30 - 17:30</button>
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
                            <label class="btn btn-outline-success" for="friday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="friday-mode" id="friday-full" value="full">
                            <label class="btn btn-outline-success" for="friday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="friday-mode" id="friday-off" value="off">
                            <label class="btn btn-outline-danger" for="friday-off">
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
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('friday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('friday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('friday', '08:30', '17:30')">08:30 - 17:30</button>
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
                            <label class="btn btn-outline-success" for="saturday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="saturday-mode" id="saturday-full" value="full">
                            <label class="btn btn-outline-success" for="saturday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="saturday-mode" id="saturday-off" value="off" checked>
                            <label class="btn btn-outline-danger" for="saturday-off">
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
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('saturday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('saturday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('saturday', '08:30', '17:30')">08:30 - 17:30</button>
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
                            <label class="btn btn-outline-success" for="sunday-working">
                                <i class="bi bi-check-circle"></i> Çalışma Günü
                            </label>
                            
                            <input type="radio" class="btn-check" name="sunday-mode" id="sunday-full" value="full">
                            <label class="btn btn-outline-success" for="sunday-full">
                                <i class="bi bi-clock"></i> 24 Saat Çalışıyor
                            </label>
                            
                            <input type="radio" class="btn-check" name="sunday-mode" id="sunday-off" value="off" checked>
                            <label class="btn btn-outline-danger" for="sunday-off">
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
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('sunday', '08:00', '17:00')">08:00 - 17:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('sunday', '09:00', '18:00')">09:00 - 18:00</button>
                            <button class="btn btn-sm btn-outline-secondary preset-btn" onclick="setTime('sunday', '08:30', '17:30')">08:30 - 17:30</button>
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

            <!-- Action Buttons -->
            <div class="action-buttons">
                <div>
                    <button class="btn btn-outline-secondary" onclick="copyFromPrevious()">
                        <i class="bi bi-clipboard"></i> Önceki Günden Kopyala
                    </button>
                    <button class="btn btn-outline-secondary ms-2" onclick="applyToAllDays()">
                        <i class="bi bi-arrow-repeat"></i> Tüm Günlere Uygula
                    </button>
                </div>
                <div>
                    <button class="btn btn-outline-secondary me-2">İptal</button>
                    <button class="btn btn-primary" onclick="saveCalendar()">
                        <i class="bi bi-check-lg"></i> Kaydet
                    </button>
                </div>
            </div>
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

            // Her gün için event listener'ları ekle
            days.forEach(function(day) {
                // Mode değişikliği
                $(`input[name="${day}-mode"]`).on('change', function() {
                    updateDayMode(day, $(this).val());
                });

                // Saat değişikliği
                $(`#${day}-start, #${day}-end`).on('change', function() {
                    updateVisualTimeline(day);
                });
            });

            function updateDayMode(day, mode) {
                const $timeArea = $(`#${day}-time-area`);
                
                if (mode === 'off') {
                    $timeArea.addClass('disabled');
                    updateVisualTimeline(day, true);
                } else if (mode === 'full') {
                    $timeArea.addClass('disabled');
                    updateVisualTimeline(day, false, true);
                } else {
                    $timeArea.removeClass('disabled');
                    updateVisualTimeline(day);
                }
            }

            function updateVisualTimeline(day, isOff = false, isFull = false) {
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

                const [startHour, startMin] = startValue.split(':').map(Number);
                const [endHour, endMin] = endValue.split(':').map(Number);

                const startMinutes = startHour * 60 + startMin;
                const endMinutes = endHour * 60 + endMin;

                const leftPercent = (startMinutes / 1440) * 100;
                const widthPercent = ((endMinutes - startMinutes) / 1440) * 100;

                $visual.css({
                    'left': leftPercent + '%',
                    'width': widthPercent + '%'
                }).text(`${startValue} - ${endValue}`);
            }

            window.setTime = function(day, start, end) {
                $(`#${day}-start`).val(start);
                $(`#${day}-end`).val(end);
                updateVisualTimeline(day);
            };

            function generateBinaryString(start, end, mode) {
                if (mode === 'off') {
                    return '0'.repeat(96);
                }
                
                if (mode === 'full') {
                    return '1'.repeat(96);
                }

                const [startHour, startMin] = start.split(':').map(Number);
                const [endHour, endMin] = end.split(':').map(Number);

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

                days.forEach(function(day, index) {
                    const mode = $(`input[name="${day}-mode"]:checked`).val();
                    const start = $(`#${day}-start`).val();
                    const end = $(`#${day}-end`).val();
                    const dbField = dayDbFields[day]; // D01, D02, ... D07

                    calendarData[dbField] = generateBinaryString(start, end, mode);
                });

                console.log('Veritabanına gönderilecek veri:', calendarData);

                // Backend'e gönder
                $.ajax({
                    url: '{{ route("calismaTakvimi.kaydet") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        EVRAKNO: $('#EVRAKNO').val(),
                        ACIKLAMA: $('#ACIKLAMA').val(),
                        D01: calendarData.D01,
                        D02: calendarData.D02,
                        D03: calendarData.D03,
                        D04: calendarData.D04,
                        D05: calendarData.D05,
                        D06: calendarData.D06,
                        D07: calendarData.D07
                    },
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: 'Çalışma takvimi başarıyla kaydedildi.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        console.log('Kayıt Sonucu:', response);
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
                updateVisualTimeline(activeTab);

                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı',
                    text: `${dayNames[previousDay]} günündeki ayarlar kopyalandı.`,
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
                                updateVisualTimeline(day);
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

            // Sayfa yüklendiğinde tüm görselleri güncelle
            days.forEach(function(day) {
                const mode = $(`input[name="${day}-mode"]:checked`).val();
                updateDayMode(day, mode);
            });

            // Mevcut verileri yükle (eğer düzenleme modundaysa)
            @if(isset($kart_veri))
                loadExistingData();
            @endif

            function loadExistingData() {
                // Backend'den gelen binary string'leri parse et
                const existingData = @json($kart_veri ?? null);
                
                if (existingData) {
                    days.forEach(function(day, index) {
                        const dbField = dayDbFields[day];
                        const binaryString = existingData[dbField];
                        
                        if (binaryString) {
                            // Binary string'den mode, start ve end'i çıkar
                            const parsedData = parseBinaryString(binaryString);
                            
                            $(`#${day}-${parsedData.mode}`).prop('checked', true).trigger('change');
                            
                            if (parsedData.mode === 'working') {
                                $(`#${day}-start`).val(parsedData.start);
                                $(`#${day}-end`).val(parsedData.end);
                            }
                            
                            updateVisualTimeline(day);
                        }
                    });
                }
            }

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
                
                const start = `${String(startHour).padStart(2, '0')}:${String(startMin).padStart(2, '0')}`;
                const end = `${String(endHour).padStart(2, '0')}:${String(endMin).padStart(2, '0')}`;
                
                return { mode: 'working', start: start, end: end };
            }
        });
    </script>
@endsection