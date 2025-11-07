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
  $ekranKayitSatirKontrol = "true";

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
  .board { 
    display: grid; 
    grid-template-columns: 300px 1fr; 
    gap: 16px;
  }
  
  .panel { 
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
  }
  
  .panel-header {
    padding: 12px 16px;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
    font-weight: 600;
    font-size: 14px;
    color: #333;
  }
  
  .panel-body {
    padding: 12px;
    max-height: 600px;
    overflow-y: auto;
  }
  
  /* İş Kartı */
  .job-card { 
    background: #fff;
    border: 1px solid #ddd;
    border-left: 3px solid #2196F3;
    border-radius: 8px;
    padding: 10px 12px;
    margin-bottom: 8px;
    cursor: move;
    /* transition: all 0.08s; */
  }
  
  .job-card:hover {
    border-color: #2196F3;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .job-card.dragging { 
    opacity: 0.5;
  }
  
  .job-title {
    font-weight: 600;
    font-size: 13px;
    color: #333;
    margin-bottom: 4px;
  }
  
  .job-info {
    font-size: 12px;
    color: #666;
  }
  
  .job-meta {
    display: flex;
    gap: 12px;
    margin-top: 6px;
    font-size: 11px;
    color: #888;
  }
  
  /* Tezgah */
  .workcenter {
    background: #fafafa;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 10px;
  }
  
  .wc-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 8px;
    margin-bottom: 8px;
    border-bottom: 1px solid #e0e0e0;
  }
  
  .wc-title {
    font-weight: 600;
    font-size: 13px;
    color: #333;
  }
  
  .wc-metric {
    background: #4CAF50;
    color: white;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
  }
  
  .wc-metric.warn {
    background: #f44336;
  }
  
  .list {
    min-height: 60px;
    border-radius: 8px;
    padding: 4px;
  }
  
  .list.hover {
    background: #e3f2fd;
  }
  
  .list:empty::after {
    content: 'Boş';
    display: block;
    text-align: center;
    color: #bbb;
    padding: 20px;
    font-size: 12px;
  }
  
  /* Havuz Başlık */
  .pool-header {
    font-weight: 600;
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    padding: 8px 10px;
    background: #f5f5f5;
    margin: 16px -12px 12px;
    border-top: 1px solid #e0e0e0;
    border-bottom: 1px solid #e0e0e0;
  }
  
  .pool-header:first-child {
    margin-top: 0;
  }
  
  /* Sortable placeholder */
  .ui-state-highlight {
    height: 60px;
    background: #e3f2fd;
    border: 2px dashed #2196F3;
    border-radius: 8px;
    margin-bottom: 8px;
  }
  
  /* Scrollbar */
  .panel-body::-webkit-scrollbar {
    width: 6px;
  }
  
  .panel-body::-webkit-scrollbar-track {
    background: #f1f1f1;
  }
  
  .panel-body::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
  }
  
  @media (max-width: 768px) {
    .board {
      grid-template-columns: 1fr;
    }
  }
</style>

@section('content')
<div class="content-wrapper">
    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal',['EVRAKTYPE' => 'PLAN','EVRAKNO'=>@$kart_veri->EVRAKNO])

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
                                <!-- Sol: Atanmamış İşler -->
                                <div class="panel">
                                    <div class="panel-header">Atanmamış İşler</div>
                                    <div class="panel-body">
                                        <div id="unassigned" class="list connected">
                                            @php
                                                $JOBS = DB::table($database.'mmps10t')->where('R_KAYNAKTYPE','I')->get();
                                            @endphp
                                            @foreach($JOBS as $JOB)
                                                <div class="job-card" data-isno="{{ $JOB->JOBNO }}" data-rsira="{{ $JOB->R_SIRANO }}" data-sure="{{ $JOB->R_MIKTART }}">
                                                    <div class="job-title">{{ $JOB->JOBNO }} - {{ $JOB->EVRAKNO }}</div>
                                                    <div class="job-info">{{ $JOB->R_OPERASYON }} · Sıra: {{ $JOB->R_SIRANO }}</div>
                                                    <div class="job-meta">
                                                        <span>Süre: {{ $JOB->R_MIKTART }}</span>
                                                        <span>Hedef: {{ $JOB->R_YMAMULMIKTAR }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Sağ: Tezgahlar -->
                                <div class="panel">
                                    <div class="panel-header">Tezgahlar</div>
                                    <div class="panel-body">
                                        <!-- <div class="pool-header">FREZE HAVUZU</div> -->
                                        
                                        <div class="pool" data-pool="FREZE">
                                            @foreach($imlt00_evraklar as $imlt00)
                                                <div class="workcenter" data-wc="{{ $imlt00->KOD }}" data-cap="24">
                                                    <div class="wc-head">
                                                        <div class="wc-title">{{ $imlt00->KOD }} - {{ $imlt00->AD }}</div>
                                                        <div class="wc-metric">0/24s</div>
                                                    </div>
                                                    <div class="list connected droppable"></div>
                                                </div>
                                            @endforeach
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
<div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz"  >
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
                    {{-- <th>Operasyon Kodu</th> --}}
                    <th>#</th>
                </tr>
                </thead>

                <tfoot>
                <tr class="bg-info">
                    <th>Evrak No</th>
                    <th>Tarih</th>
                    {{-- <th>Operasyon Kodu</th> --}}
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
                    // echo "";
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
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>
<script>
    function refreshUtilization() {
        $(".workcenter").each(function(){
            let cap = parseFloat($(this).data("cap")) || 0;
            let total = 0;
            $(this).find(".job-card").each(function(){
                total += parseFloat($(this).data("sure")) || 0;
            });
            const metric = $(this).find(".wc-metric");
            metric.text(total.toFixed(1) + "/" + cap + "s");
            metric.toggleClass("warn", total > cap);
        });
    }

    function apiAssign(payload){
        console.log("assign", payload);
        // return $.post("/api/assign", payload);
    }
    
    function apiReorder(payload){
        console.log("reorder", payload);
        // return $.ajax({url:"/api/assign/"+payload.id, method:"PATCH", data:payload});
    }
    
    function buildAssignmentPayload($card, $targetList, index){
        const isNo = $card.data("isno");
        const rSira = $card.data("rsira");
        const sure = parseFloat($card.data("sure")) || null;
        const wc = $targetList.closest(".workcenter").data("wc") || null;
        const pool = $targetList.closest(".pool").data("pool") || null;
        return {
            isNo: isNo, 
            rSiraNo: rSira,
            hedef: { havuz: pool, tezgah: wc },
            planliSaat: sure,
            sira: index
        };
    }

    $(function(){
        $(".connected").sortable({
            connectWith: ".connected",
            placeholder: "ui-state-highlight",
            revert: 100,
            tolerance: "pointer",
            start: function(e, ui){
                ui.item.addClass("dragging");
            },
            stop: function(e, ui){
                ui.item.removeClass("dragging");
            },
            receive: function(e, ui){
                const $target = $(this);
                const idx = $target.children(".job-card").index(ui.item);
                const payload = buildAssignmentPayload(ui.item, $target, idx);
                apiAssign(payload);
                refreshUtilization();
            },
            update: function(e, ui){
                if (this === ui.item.parent()[0] && !ui.sender) {
                    const $list = $(this);
                    const idx = $list.children(".job-card").index(ui.item);
                    const payload = buildAssignmentPayload(ui.item, $list, idx);
                    apiReorder(payload);
                    refreshUtilization();
                }
            },
            over: function(){ $(this).addClass("hover"); },
            out: function(){ $(this).removeClass("hover"); }
        }).disableSelection();

        refreshUtilization();
    });
</script>
@endsection