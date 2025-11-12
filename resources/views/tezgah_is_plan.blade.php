@extends('layout.mainlayout')
@php
  if (Auth::check()) {
    $user = Auth::user();
  }

  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "TZGHISPLNLM";
  $ekranRumuz = "PLAN_E";
  $ekranAdi = "Tezgah İş Planlama";
  $ekranLink = "tezgahisplanlama";
  $ekranTableE = $database."plan_e";
  $ekranTableT = $database."plan_t";
  $ekranKayitSatirKontrol = "false";

  $kullanici_read_yetkilrei = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);
  $evrakno = null;
  if(isset($_GET['evrakno'])) {
    $evrakno = $_GET['evrakno'];
  }
  if(isset($_GET['ID'])) {
    $sonID = $_GET['ID'];
  }
  else {
    $sonID = DB::table($ekranTableE)->min('id');
  }
  $kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();
  $t_kart_veri=DB::table($ekranTableT)->orderBy('id', 'ASC')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();
  $evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();
  $mmps_evraklar=DB::table($database.'mmps10t')->orderBy('id', 'ASC')->get();
  $imlt00_evraklar=DB::table($database.'imlt00')->orderBy('KOD', 'ASC')->get();
  if (isset($kart_veri)) {
    $ilkEvrak=DB::table($ekranTableE)->min('id');
    $sonEvrak=DB::table($ekranTableE)->max('id');
    $sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');
  }
@endphp
<style>
  .extra-tools {
        position: fixed;
        bottom: 45px;
        right: 20px;
        display: flex;
        gap: 8px;
        z-index: 1000;
    }
    
    .extra-tools .btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        color
    }
    
    .extra-tools .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    .btn-stats {
        background: #fff;
    }
    
    .btn-auto {
        background: #fff;
    }
    
    .btn-export {
        background: #fff;
    }
  .board { 
    display: grid; 
    grid-template-columns: 320px 1fr; 
    gap: 16px;
    margin-top: 10px;
  }
  
  .panel { 
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  }
  
  .panel-header {
    padding: 14px 16px;
    border-bottom: 1px solid #ddd;
    font-weight: 600;
    font-size: 14px;
    /* color: #fff; */
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .panel-header .badge {
    background: rgba(255,255,255,0.2);
    color: #fff;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }
  
  .panel-body {
    padding: 12px;
    max-height: 600px;
    overflow-y: auto;
  }
  .job-card { 
    background: #fff;
    border: 1px solid #e0e0e0;
    border-left: 4px solid #667eea;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 8px;
    cursor: move;
    position: relative;
  }
  
  .job-card:hover {
    border-left-color: #764ba2;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
  }
  
  .job-card.dragging { 
    opacity: 0.6;
    transform: rotate(3deg);
  }

  .job-card .job-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #667eea;
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
  }
  
  .job-title {
    font-weight: 600;
    font-size: 13px;
    color: #333;
    margin-bottom: 6px;
    padding-right: 50px;
  }
  
  .job-info {
    font-size: 12px;
    color: #666;
    margin-bottom: 6px;
  }
  
  .job-meta {
    display: flex;
    gap: 12px;
    margin-top: 8px;
    font-size: 11px;
    color: #888;
    border-top: 1px solid #f0f0f0;
    padding-top: 8px;
  }

  .job-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .job-meta i {
    color: #667eea;
  }
  .workcenter {
    background: #fafafa;
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 12px;
    transition: all 0.2s ease;
  }

  .workcenter:hover {
    border-color: #667eea;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
  }
  
  .wc-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;
    margin-bottom: 10px;
    border-bottom: 2px solid #e0e0e0;
  }
  
  .wc-title {
    font-weight: 600;
    font-size: 13px;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .wc-title i {
    color: #667eea;
  }
  
  .wc-metric {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
  }
  
  .wc-metric.warn {
    background: linear-gradient(135deg, #f44336 0%, #da190b 100%);
    box-shadow: 0 2px 4px rgba(244, 67, 54, 0.3);
  }
  
  .list {
    min-height: 80px;
    border-radius: 6px;
    padding: 6px;
    background: #fff;
    border: 2px dashed #e0e0e0;
  }
  
  .list.hover {
    background: #f0f4ff;
    border-color: #667eea;
  }
  
  .list:empty::after {
    content: 'İşleri buraya sürükleyin';
    display: block;
    text-align: center;
    color: #bbb;
    padding: 30px 10px;
    font-size: 12px;
    font-style: italic;
  }
  
  .ui-state-highlight {
    height: 80px;
    background: linear-gradient(135deg, #f0f4ff 0%, #f0f4ff 100%);
    border: 2px dashed #667eea;
    border-radius: 6px;
    margin-bottom: 8px;
  }
  .panel-body::-webkit-scrollbar {
    width: 8px;
  }
  
  .panel-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
  }
  
  .panel-body::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 4px;
  }

  .panel-body::-webkit-scrollbar-thumb:hover {
    background: #764ba2;
  }

  .action-buttons {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
  }

  .action-buttons .btn {
    flex: 1;
    font-weight: 600;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    transition: all 0.2s ease;
  }

  .btn-save-plan {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
  }

  .btn-save-plan:hover {
    box-shadow: 0 4px 8px rgba(76, 175, 80, 0.4);
    transform: translateY(-2px);
  }

  .btn-reset-plan {
    background: linear-gradient(135deg, #ff9800 0%, #fb8c00 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(255, 152, 0, 0.3);
  }

  .btn-reset-plan:hover {
    box-shadow: 0 4px 8px rgba(255, 152, 0, 0.4);
    transform: translateY(-2px);
  }

  .changes-badge {
    position: fixed;
    bottom: 15px;
    left: 35px;
    background: linear-gradient(135deg, #ff9800 0%, #fb8c00 100%);
    color: white;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(255, 152, 0, 0.4);
    z-index: 1000;
    display: none;
    animation: slideIn 0.3s ease;
  }

  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  .loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
  }

  .loading-spinner {
    background: white;
    padding: 30px 40px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
  }

  .loading-spinner .spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    padding: 16px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 10000;
    display: none;
    min-width: 300px;
    animation: slideIn 0.3s ease;
  }

  .toast-notification.success {
    border-left: 4px solid #4CAF50;
  }

  .toast-notification.error {
    border-left: 4px solid #f44336;
  }

  .toast-notification.warning {
    border-left: 4px solid #ff9800;
  }
  
  @media (max-width: 768px) {
    .board {
      grid-template-columns: 1fr;
    }

    .changes-badge {
      top: auto;
      bottom: 80px;
      right: 10px;
      left: 10px;
      text-align: center;
    }
  }

  /* Empty State */
  .empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #999;
  }

  .empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.3;
  }

  .empty-state p {
    font-size: 14px;
    margin: 0;
  }
</style>

@section('content')
<div class="content-wrapper">
    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal',['EVRAKTYPE' => 'PLAN','EVRAKNO'=>@$kart_veri->EVRAKNO])

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <div>İşleminiz gerçekleştiriliyor...</div>
        </div>
    </div>

    <div class="toast-notification" id="toastNotification">
        <span id="toastMessage"></span>
    </div>

    <div class="sync-status" id="syncStatus">
        <i class="fa fa-check-circle"></i>
        <span id="syncStatusText">Tüm değişiklikler kaydedildi</span>
    </div>

    <section class="content">
        <form action="tezgah_is_planlama_islemler" method="POST" name="verilerForm" id="verilerForm">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="box box-danger">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-2 col-xs-2">
                                    <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value, '{{ $ekranLink }}')">
                                        @php
                                        foreach ($evraklar as $key => $veri) {
                                            if($veri->id == @$kart_veri->id){
                                            echo "<option value='".$veri->id."' selected>".$veri->EVRAKNO."</option>";
                                            }
                                            else {
                                            echo "<option value='".$veri->id."'>".$veri->EVRAKNO."</option>";
                                            }
                                        }
                                        @endphp
                                    </select>
                                    <input type="hidden" value="{{ @$kart_veri->id }}" name="ID_TO_REDIRECT" id="ID_TO_REDIRECT">
                                </div>
                                <div class="col-md-2 col-sm-3 col-xs-6">
                                    <input type="date" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" name="TARIH" id="TARIH"  value="{{ @$kart_veri->TARIH }}">
                                </div>
                                <div class="col-md-3 col-xs-2">
                                    <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i class="fa fa-filter" style="color: white;"></i></a>
                                    <input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}">
                                </div>
                                <div class="col-md-5 col-xs-5">
                                    @include('layout.util.evrakIslemleri')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="board">
                                <div class="panel">
                                    <div class="panel-header">
                                        <span><i class="fa fa-inbox"></i> Atanmamış İşler</span>
                                        <span class="badge" id="unassignedCount">0</span>
                                    </div>
                                    <div class="panel-body">
                                        <div id="unassigned" class="list connected" data-area="unassigned">
                                            @php
                                                // Önce atanmış işleri al
                                                $atanmisIsler = DB::table($ekranTableT)
                                                    ->where('EVRAKNO', @$kart_veri->EVRAKNO)
                                                    ->pluck('JOBNO')
                                                    ->toArray();
                                                
                                                // Tüm işleri al
                                                $JOBS = DB::table($database.'mmps10t')
                                                    ->where('R_KAYNAKTYPE','I')
                                                    ->get();
                                            @endphp
                                            @if($JOBS->count() > 0)
                                                @foreach($JOBS as $JOB)
                                                    @if(!in_array($JOB->JOBNO, $atanmisIsler))
                                                    <div class="job-card" 
                                                         data-isno="{{ $JOB->JOBNO }}" 
                                                         data-rsira="{{ $JOB->R_SIRANO }}" 
                                                         data-sure="{{ $JOB->R_MIKTART }}" 
                                                         data-evrakno="{{ $JOB->EVRAKNO }}" 
                                                         data-operasyon="{{ $JOB->R_OPERASYON }}" 
                                                         data-hedef="{{ $JOB->R_YMAMULMIKTAR }}"
                                                         data-planid="">
                                                        <span class="job-badge">{{ $JOB->R_SIRANO }}</span>
                                                        <span class="save-indicator"><i class="fa fa-spinner fa-spin"></i></span>
                                                        <div class="job-title">{{ $JOB->JOBNO }}</div>
                                                        <div class="job-info">{{ $JOB->R_OPERASYON }} · Evrak: {{ $JOB->EVRAKNO }}</div>
                                                        <div class="job-meta">
                                                            <span><i class="fa fa-clock-o"></i> {{ $JOB->R_MIKTART }}s</span>
                                                            <span><i class="fa fa-bullseye"></i> {{ $JOB->R_YMAMULMIKTAR }}</span>
                                                        </div>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <div class="empty-state">
                                                    <i class="fa fa-check-circle"></i>
                                                    <p>Hiç iş bulunamadı!</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="panel">
                                    <div class="panel-header">
                                        <span><i class="fa fa-cogs"></i> Tezgahlar</span>
                                    </div>
                                    <div class="panel-body">
                                        <div class="pool row" data-pool="PRODUCTION">
                                            @if($imlt00_evraklar->count() > 0)
                                                @foreach($imlt00_evraklar as $imlt00)
                                                    <div class="col-4 workcenter" data-wc="{{ $imlt00->KOD }}" data-cap="24">
                                                        <div class="wc-head">
                                                            <div class="wc-title">
                                                                <i class="fa fa-wrench"></i>
                                                                <span>{{ $imlt00->KOD }} - {{ $imlt00->AD }}</span>
                                                            </div>
                                                            <div class="wc-metric">0/24s</div>
                                                        </div>
                                                        <div class="list connected droppable" data-area="{{ $imlt00->KOD }}">
                                                            @php
                                                                // Bu tezgaha atanmış işleri getir
                                                                $tezgahIsleri = DB::table($ekranTableT)
                                                                    ->where('EVRAKNO', @$kart_veri->EVRAKNO)
                                                                    ->where('TEZGAH_KODU', $imlt00->KOD)
                                                                    ->orderBy('id', 'ASC')
                                                                    ->get();
                                                            @endphp
                                                            @foreach($tezgahIsleri as $planIs)
                                                                @php
                                                                    $jobDetay = DB::table($database.'mmps10t')
                                                                        ->where('JOBNO', $planIs->JOBNO)
                                                                        ->first();
                                                                @endphp
                                                                @if($jobDetay)
                                                                <div class="job-card" 
                                                                     data-isno="{{ $jobDetay->JOBNO }}" 
                                                                     data-rsira="{{ $jobDetay->R_SIRANO }}" 
                                                                     data-sure="{{ $jobDetay->R_MIKTART }}" 
                                                                     data-evrakno="{{ $jobDetay->EVRAKNO }}" 
                                                                     data-operasyon="{{ $jobDetay->R_OPERASYON }}" 
                                                                     data-hedef="{{ $jobDetay->R_YMAMULMIKTAR }}"
                                                                     data-planid="{{ $planIs->id }}">
                                                                    <span class="job-badge">{{ $jobDetay->R_SIRANO }}</span>
                                                                    <span class="save-indicator"><i class="fa fa-spinner fa-spin"></i></span>
                                                                    <div class="job-title">{{ $jobDetay->JOBNO }}</div>
                                                                    <div class="job-info">{{ $jobDetay->R_OPERASYON }} · Evrak: {{ $jobDetay->EVRAKNO }}</div>
                                                                    <div class="job-meta">
                                                                        <span><i class="fa fa-clock-o"></i> {{ $jobDetay->R_MIKTART }}s</span>
                                                                        <span><i class="fa fa-bullseye"></i> {{ $jobDetay->R_YMAMULMIKTAR }}</span>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="empty-state">
                                                    <i class="fa fa-info-circle"></i>
                                                    <p>Tanımlı tezgah bulunamadı</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-save-plan" onclick="topluKaydet()" style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white;">
                                                <i class="fa fa-save"></i> Toplu Kaydet
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10" style="font-size: 0.8em">
                        <thead>
                            <tr class="bg-primary">
                                <th>Evrak No</th>
                                <th>Tarih</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-info">
                                <th>Evrak No</th>
                                <th>Tarih</th>
                                <th>#</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @php
                            $evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();
                            foreach ($evraklar as $key => $suzVeri) {
                                echo "<tr>";
                                echo "<td>".$suzVeri->EVRAKNO."</td>";
                                echo "<td>".$suzVeri->TARIH."</td>";
                                echo "<td>"."<a class='btn btn-info' href='tezgahisplanlama?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
                                echo "</tr>";
                            }
                            @endphp
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin-top: 15px;">Kapat</button>
            </div>
        </div>
    </div>
</div>

<div class="extra-tools">
    <button type="button" class="btn btn-stats" onclick="showStats()" data-bs-toggle="tooltip" data-bs-placement="auto" data-bs-title="İstatistikler">
        <i class="fa fa-bar-chart"></i>
    </button>
    <button type="button" class="btn btn-auto" onclick="autoDistribute()" data-bs-toggle="tooltip" data-bs-placement="auto" data-bs-title="Otomatik Dağıt">
        <i class="fa fa-magic"></i>
    </button>
    <button type="button" class="btn btn-export" onclick="exportPlan()" data-bs-toggle="tooltip" data-bs-placement="auto" data-bs-title="Dışa Aktar">
        <i class="fa fa-download"></i>
    </button>
    <button type="button" class="btn btn-export" onclick="tumunuSil()" data-bs-toggle="tooltip" data-bs-placement="auto" data-bs-title="Tümünü Sil">
        <i class="fa fa-trash"></i>
    </button>
</div>

<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>
<script>
    const EVRAKNO = "{{ @$kart_veri->EVRAKNO }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";
    let saveQueue = [];
    let isSaving = false;

    $(function(){
        initializeSortable();
        refreshUtilization();
        updateUnassignedCount();
        updateSyncStatus('synced', 'Tüm değişiklikler kaydedildi');
    });

    function initializeSortable() {
        $(".connected").sortable({
            connectWith: ".connected",
            placeholder: "ui-state-highlight",
            revert: 150,
            tolerance: "pointer",
            cursor: "move",
            start: function(e, ui){
                ui.item.addClass("dragging");
            },
            stop: function(e, ui){
                ui.item.removeClass("dragging");
                
                // Hedef alan bilgisini al
                const targetArea = ui.item.closest('.list').data('area');
                const targetWc = ui.item.closest('.workcenter').data('wc');
                
                // Sıra numarasını hesapla
                const newIndex = ui.item.index() + 1;
                
                // AJAX ile kaydet
                saveJobPosition(ui.item, targetArea, targetWc, newIndex);
            },
            receive: function(e, ui){
                refreshUtilization();
                updateUnassignedCount();
            },
            update: function(e, ui){
                if (this === ui.item.parent()[0] && !ui.sender) {
                    refreshUtilization();
                    
                    // Sıra değişikliğinde tüm kartları güncelle
                    updateAllPositionsInContainer($(this));
                }
            },
            over: function(){ $(this).addClass("hover"); },
            out: function(){ $(this).removeClass("hover"); }
        }).disableSelection();
    }

    // İş pozisyonunu AJAX ile kaydet
    function saveJobPosition($card, targetArea, targetWc, sira) {
        const jobData = {
            _token: CSRF_TOKEN,
            action: 'save_position',
            evrakno: EVRAKNO,
            jobno: $card.data('isno'),
            rsira: $card.data('rsira'),
            sure: $card.data('sure'),
            operasyon: $card.data('operasyon'),
            hedef: $card.data('hedef'),
            tezgah: targetWc || null,
            havuz: targetArea === 'unassigned' ? null : 'PRODUCTION',
            sira: sira,
            planid: $card.data('planid') || null
        };

        // Kayıt göstergesi
        $card.find('.save-indicator').fadeIn();
        $card.addClass('saving');
        updateSyncStatus('syncing', 'Kaydediliyor...');

        $.ajax({
            url: '{{ url("tezgah_is_planlama_ajax") }}',
            type: 'POST',
            data: jobData,
            success: function(response) {
                if(response.success) {
                    // Plan ID'yi güncelle
                    $card.data('planid', response.planid);
                    $card.attr('data-planid', response.planid);
                    
                    // Kayıt başarılı göstergesi
                    setTimeout(function() {
                        $card.find('.save-indicator').fadeOut();
                        $card.removeClass('saving');
                        updateSyncStatus('synced', 'Kaydedildi');
                        
                        // 2 saniye sonra durumu gizle
                        setTimeout(function() {
                            if(!isSaving && saveQueue.length === 0) {
                                updateSyncStatus('synced', 'Tüm değişiklikler kaydedildi');
                            }
                        }, 2000);
                    }, 500);
                } else {
                    handleSaveError($card, response.message);
                }
            },
            error: function(xhr, status, error) {
                handleSaveError($card, 'Bağlantı hatası: ' + error);
            }
        });
    }

    // Bir container içindeki tüm pozisyonları güncelle
    function updateAllPositionsInContainer($container) {
        const targetArea = $container.data('area');
        const targetWc = $container.closest('.workcenter').data('wc');
        
        $container.find('.job-card').each(function(index) {
            const $card = $(this);
            const newSira = index + 1;
            
            // Sadece sıra değiştiyse kaydet
            if($card.data('current-sira') !== newSira) {
                $card.data('current-sira', newSira);
                saveJobPosition($card, targetArea, targetWc, newSira);
            }
        });
    }

    // Hata durumunda
    function handleSaveError($card, message) {
        $card.find('.save-indicator').fadeOut();
        $card.removeClass('saving');
        updateSyncStatus('error', 'Kayıt hatası!');
        showToast(message || 'Kayıt sırasında bir hata oluştu', 'error');
        
        // 3 saniye sonra durumu gizle
        setTimeout(function() {
            updateSyncStatus('synced', 'Tüm değişiklikler kaydedildi');
        }, 3000);
    }

    // Sync durumu güncelle
    function updateSyncStatus(status, text) {
        const $syncStatus = $('#syncStatus');
        $syncStatus.removeClass('syncing synced error').addClass(status);
        
        let icon = 'fa-check-circle';
        if(status === 'syncing') icon = 'fa-spinner fa-spin';
        if(status === 'error') icon = 'fa-exclamation-circle';
        
        $syncStatus.find('i').attr('class', 'fa ' + icon);
        $('#syncStatusText').text(text);
        
        $syncStatus.fadeIn();
    }

    function refreshUtilization() {
        $(".workcenter").each(function(){
            let cap = parseFloat($(this).data("cap")) || 0;
            let total = 0;
            let count = 0;
            
            $(this).find(".job-card").each(function(){
                total += parseFloat($(this).data("sure")) || 0;
                count++;
            });
            
            const metric = $(this).find(".wc-metric");
            metric.text(total.toFixed(1) + "/" + cap + "s (" + count + ")");
            metric.toggleClass("warn", total > cap);
        });
    }

    function updateUnassignedCount() {
        const count = $("#unassigned .job-card").length;
        $("#unassignedCount").text(count);
    }

    function showLoading(show) {
        if (show) {
            $("#loadingOverlay").css('display', 'flex').hide().fadeIn(200);
        } else {
            $("#loadingOverlay").fadeOut(200);
        }
    }

    function showToast(message, type = 'success') {
        const $toast = $("#toastNotification");
        const $message = $("#toastMessage");
        
        $toast.removeClass('success error warning').addClass(type);
        $message.html('<i class="fa fa-' + getToastIcon(type) + '"></i> ' + message);
        
        $toast.fadeIn(300);
        
        setTimeout(function() {
            $toast.fadeOut(300);
        }, 3000);
    }

    function getToastIcon(type) {
        switch(type) {
            case 'success': return 'check-circle';
            case 'error': return 'times-circle';
            case 'warning': return 'exclamation-triangle';
            default: return 'info-circle';
        }
    }

    // Toplu kaydetme fonksiyonu (tüm plan'ı bir seferde kaydet)
    function topluKaydet() {
        Swal.fire({
            title: 'Planı Toplu Kaydet',
            text: 'Tüm atamalar kaydedilecek. Devam edilsin mi?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4CAF50',
            cancelButtonColor: '#999',
            confirmButtonText: 'Evet, Kaydet',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading(true);
                updateSyncStatus('syncing', 'Toplu kaydediliyor...');
                
                const planData = [];
                
                $(".workcenter").each(function(){
                    const wc = $(this).data("wc");
                    const pool = $(this).closest(".pool").data("pool");
                    let sira = 1;
                    
                    $(this).find(".job-card").each(function(){
                        planData.push({
                            evrakno: EVRAKNO,
                            jobno: $(this).data("isno"),
                            rsira: $(this).data("rsira"),
                            sure: parseFloat($(this).data("sure")) || 0,
                            operasyon: $(this).data("operasyon"),
                            hedef: $(this).data("hedef"),
                            tezgah: wc,
                            havuz: pool,
                            sira: sira++,
                            planid: $(this).data("planid") || null
                        });
                    });
                });

                $.ajax({
                    url: '{{ url("tezgah_is_planlama_ajax") }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        action: 'save_bulk',
                        evrakno: EVRAKNO,
                        plan_data: JSON.stringify(planData)
                    },
                    success: function(response) {
                        showLoading(false);
                        if(response.success) {
                            updateSyncStatus('synced', 'Toplu kayıt başarılı!');
                            showToast('Plan başarıyla kaydedildi!', 'success');
                            
                            // Plan ID'leri güncelle
                            if(response.planids) {
                                response.planids.forEach(function(item) {
                                    $('.job-card[data-isno="' + item.jobno + '"]').attr('data-planid', item.planid).data('planid', item.planid);
                                });
                            }
                            
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            updateSyncStatus('error', 'Kayıt hatası!');
                            showToast(response.message || 'Kayıt hatası', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        showLoading(false);
                        updateSyncStatus('error', 'Bağlantı hatası!');
                        showToast('Bağlantı hatası: ' + error, 'error');
                    }
                });
            }
        });
    }

    // Tümünü sil
    function tumunuSil() {
        Swal.fire({
            title: 'Tüm Planı Sil',
            text: 'Bu evraktaki tüm atamalar silinecek. Emin misiniz?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f44336',
            cancelButtonColor: '#999',
            confirmButtonText: 'Evet, Sil',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading(true);
                updateSyncStatus('syncing', 'Siliniyor...');

                $.ajax({
                    url: '{{ url("tezgah_is_planlama_ajax") }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        action: 'delete_all',
                        evrakno: EVRAKNO
                    },
                    success: function(response) {
                        showLoading(false);
                        if(response.success) {
                            updateSyncStatus('synced', 'Plan silindi!');
                            showToast('Tüm plan başarıyla silindi!', 'success');
                            
                            // Tüm kartları atanmamış havuzuna taşı
                            $(".workcenter .job-card").each(function(){
                                $(this).attr('data-planid', '').data('planid', '');
                                $("#unassigned").append($(this));
                            });
                            
                            refreshUtilization();
                            updateUnassignedCount();
                            
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            updateSyncStatus('error', 'Silme hatası!');
                            showToast(response.message || 'Silme hatası', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        showLoading(false);
                        updateSyncStatus('error', 'Bağlantı hatası!');
                        showToast('Bağlantı hatası: ' + error, 'error');
                    }
                });
            }
        });
    }

    // Tek bir işi sil
    function deleteJob(jobno, planid) {
        if(!planid) {
            showToast('Bu iş henüz kaydedilmemiş!', 'warning');
            return;
        }

        Swal.fire({
            title: 'İşi Sil',
            text: 'Bu iş atanmamış havuzuna gönderilecek. Devam edilsin mi?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f44336',
            cancelButtonColor: '#999',
            confirmButtonText: 'Evet, Sil',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                const $card = $('.job-card[data-isno="' + jobno + '"]');
                $card.find('.save-indicator').fadeIn();
                $card.addClass('saving');
                updateSyncStatus('syncing', 'Siliniyor...');

                $.ajax({
                    url: '{{ url("tezgah_is_planlama_ajax") }}',
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        action: 'delete_job',
                        planid: planid
                    },
                    success: function(response) {
                        if(response.success) {
                            $card.attr('data-planid', '').data('planid', '');
                            $("#unassigned").append($card);
                            $card.find('.save-indicator').fadeOut();
                            $card.removeClass('saving');
                            
                            refreshUtilization();
                            updateUnassignedCount();
                            updateSyncStatus('synced', 'İş silindi!');
                            showToast('İş başarıyla silindi!', 'success');
                        } else {
                            handleSaveError($card, response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        handleSaveError($card, 'Bağlantı hatası: ' + error);
                    }
                });
            }
        });
    }

    // İstatistikler
    function calculateStats() {
        let totalJobs = 0;
        let assignedJobs = 0;
        let totalTime = 0;
        let overCapacityCount = 0;
        
        $(".job-card").each(function() {
            totalJobs++;
            totalTime += parseFloat($(this).data("sure")) || 0;
        });
        
        assignedJobs = totalJobs - $("#unassigned .job-card").length;
        
        $(".workcenter").each(function() {
            const cap = parseFloat($(this).data("cap")) || 0;
            let wcTime = 0;
            
            $(this).find(".job-card").each(function() {
                wcTime += parseFloat($(this).data("sure")) || 0;
            });
            
            if (wcTime > cap) {
                overCapacityCount++;
            }
        });
        
        return {
            totalJobs: totalJobs,
            assignedJobs: assignedJobs,
            unassignedJobs: totalJobs - assignedJobs,
            totalTime: totalTime.toFixed(1),
            overCapacityCount: overCapacityCount
        };
    }

    function showStats() {
        const stats = calculateStats();
        const message = `
            <div style="text-align: left;">
                <strong>Plan İstatistikleri:</strong><br><br>
                📊 Toplam İş: <strong>${stats.totalJobs}</strong><br>
                ✅ Atanan: <strong>${stats.assignedJobs}</strong><br>
                ⏳ Atanmayan: <strong>${stats.unassignedJobs}</strong><br>
                ⏱️ Toplam Süre: <strong>${stats.totalTime}s</strong><br>
                ⚠️ Kapasite Aşan Tezgah: <strong>${stats.overCapacityCount}</strong>
            </div>
        `;
        
        Swal.fire({
            title: 'İstatistikler',
            html: message,
            icon: 'info',
            confirmButtonText: 'Tamam'
        });
    }

    // Otomatik dağıt
    function autoDistribute() {
        Swal.fire({
            title: 'Otomatik Dağıt',
            text: 'İşler otomatik olarak tezgahlara dağıtılacak. Devam edilsin mi?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#00c6ff',
            cancelButtonColor: '#999',
            confirmButtonText: 'Evet, Dağıt',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading(true);
                updateSyncStatus('syncing', 'Otomatik dağıtılıyor...');
                
                // Basit round-robin dağıtım
                const $workcenters = $(".workcenter .list");
                let currentIndex = 0;
                
                $("#unassigned .job-card").each(function(){
                    if ($workcenters.length > 0) {
                        const $target = $workcenters.eq(currentIndex);
                        $target.append($(this));
                        
                        // Pozisyonu kaydet
                        const targetArea = $target.data('area');
                        const targetWc = $target.closest('.workcenter').data('wc');
                        const newSira = $target.find('.job-card').length;
                        
                        saveJobPosition($(this), targetArea, targetWc, newSira);
                        
                        currentIndex = (currentIndex + 1) % $workcenters.length;
                    }
                });
                
                setTimeout(function() {
                    refreshUtilization();
                    updateUnassignedCount();
                    showLoading(false);
                    updateSyncStatus('synced', 'Otomatik dağıtım tamamlandı!');
                    showToast('İşler otomatik olarak dağıtıldı!', 'success');
                }, 1000);
            }
        });
    }

    // Planı dışa aktar
    function exportPlan() {
        const data = [];
        
        $(".workcenter").each(function(){
            const wc = $(this).data("wc");
            const pool = $(this).closest(".pool").data("pool");
            let sira = 1;
            
            $(this).find(".job-card").each(function(){
                data.push({
                    isNo: $(this).data("isno"),
                    rSiraNo: $(this).data("rsira"),
                    evrakNo: $(this).data("evrakno"),
                    operasyon: $(this).data("operasyon"),
                    sure: parseFloat($(this).data("sure")) || 0,
                    hedef: $(this).data("hedef"),
                    tezgah: wc,
                    havuz: pool,
                    sira: sira++
                });
            });
        });
        
        const stats = calculateStats();
        
        const exportData = {
            plan: data,
            stats: stats,
            date: new Date().toISOString(),
            evrak: EVRAKNO
        };
        
        const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(exportData, null, 2));
        const downloadAnchor = document.createElement('a');
        downloadAnchor.setAttribute("href", dataStr);
        downloadAnchor.setAttribute("download", "plan_" + EVRAKNO + "_" + new Date().getTime() + ".json");
        document.body.appendChild(downloadAnchor);
        downloadAnchor.click();
        downloadAnchor.remove();
        
        showToast('Plan dışa aktarıldı!', 'success');
    }

    // Klavye kısayolları
    $(document).on('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            topluKaydet();
        }
        if (e.ctrlKey && e.key === 'i') {
            e.preventDefault();
            showStats();
        }
    });

    // İş kartına tıklama - detayları göster
    $(document).on('click', '.job-card', function(e) {
        if (!$(this).hasClass('ui-sortable-helper') && !$(this).hasClass('dragging')) {
            const jobData = {
                isNo: $(this).data('isno'),
                evrakNo: $(this).data('evrakno'),
                operasyon: $(this).data('operasyon'),
                sure: $(this).data('sure'),
                hedef: $(this).data('hedef'),
                rsira: $(this).data('rsira'),
                planid: $(this).data('planid')
            };
            
            const detailHtml = `
                <div style="text-align: left;">
                    <strong>İş Detayları:</strong><br><br>
                    🔖 İş No: <strong>${jobData.isNo}</strong><br>
                    📄 Evrak: <strong>${jobData.evrakNo}</strong><br>
                    🔧 Operasyon: <strong>${jobData.operasyon}</strong><br>
                    ⏱️ Süre: <strong>${jobData.sure}s</strong><br>
                    🎯 Hedef: <strong>${jobData.hedef}</strong><br>
                    📊 Sıra: <strong>${jobData.rsira}</strong><br>
                    ${jobData.planid ? '💾 Plan ID: <strong>' + jobData.planid + '</strong>' : '⚠️ Henüz kaydedilmemiş'}
                </div>
            `;
            
            Swal.fire({
                title: 'İş Bilgileri',
                html: detailHtml,
                icon: 'info',
                showCancelButton: jobData.planid ? true : false,
                confirmButtonText: 'Tamam',
                cancelButtonText: 'Sil',
                cancelButtonColor: '#f44336'
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel && jobData.planid) {
                    deleteJob(jobData.isNo, jobData.planid);
                }
            });
        }
    });

    // Sayfa yüklendiğinde tooltip'leri etkinleştir
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endsection