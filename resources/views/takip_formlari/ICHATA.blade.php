<div class="form" style="display:none;" id="ICHATA">

    @php
        $ich_fault_types = json_decode(@$kart_veri->ich_fault_types ?? '[]', true);
    @endphp

    <div class="row g-3">

        {{-- DOKÜMAN NO --}}
        <div class="col-md-3">
            <label class="form-label">Doküman No</label>
            <input type="text"
                   class="form-control ich_doc_no"
                   data-bs-toggle="tooltip"
                   data-bs-placement="bottom"
                   data-bs-title="ich_doc_no"
                   name="ich_doc_no"
                   value="{{ @$kart_veri->ich_doc_no ?? '' }}">
        </div>

        {{-- TARİH --}}
        <div class="col-md-3">
            <label class="form-label">Tarih</label>
            <input type="date"
                   class="form-control ich_date"
                   data-bs-toggle="tooltip"
                   data-bs-placement="bottom"
                   data-bs-title="ich_date"
                   name="ich_date"
                   value="{{ @$kart_veri->ich_date ?? '' }}">
        </div>

        {{-- İŞ EMRİ --}}
        <div class="col-md-3">
            <label class="form-label">İş Emri No</label>
            <input type="text"
                   class="form-control ich_jobno"
                   data-bs-toggle="tooltip"
                   data-bs-placement="bottom"
                   data-bs-title="ich_jobno"
                   name="ich_jobno"
                   value="{{ @$kart_veri->ich_jobno ?? '' }}">
        </div>

        {{-- SİPARİŞ NO --}}
        <div class="col-md-3">
            <label class="form-label">Sipariş No</label>
            <input type="text"
                   class="form-control ich_order_no"
                   data-bs-toggle="tooltip"
                   data-bs-placement="bottom"
                   data-bs-title="ich_order_no"
                   name="ich_order_no"
                   value="{{ @$kart_veri->ich_order_no ?? '' }}">
        </div>

        <hr class="my-4">

        <h4>Hata Türü / Departman Bilgileri</h4>

        <div class="col-md-12 d-flex flex-wrap gap-3">

            @php
                $faultList = [
                    "Üretim","Kalite","Müşteri Red","Proje Parçası","G-Hurda",
                    "GKK","Satın Alma","Red","Tashih","Hurda"
                ];
            @endphp

            @foreach($faultList as $f)
                <div>
                    <input type="checkbox"
                           class="ich_fault_types"
                           data-bs-toggle="tooltip"
                           data-bs-placement="bottom"
                           data-bs-title="ich_fault_types"
                           name="ich_fault_types[]"
                           value="{{ $f }}"
                           {{ in_array($f, $ich_fault_types) ? 'checked' : '' }}>
                    {{ $f }}
                </div>
            @endforeach
        </div>

        <hr class="my-4">

        {{-- PARÇA KODU --}}
        <div class="col-md-6">
            <label class="form-label">Parça Kodu</label>
            <select class="form-control select2 ich_part_code"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    data-bs-title="ich_part_code"
                    onchange="stokAdiGetir3(this.value)">

                <option value="">Seç</option>

                @php
                    if (!empty(@$kart_veri->ich_part_code)) {
                        echo "<option value='".@$kart_veri->ich_part_code."' selected>" .
                             @$kart_veri->ich_part_code . " - " . @$kart_veri->ich_part_name . "</option>";
                    }
                    else
                    {
                        DB::table($database.'stok00')->select('KOD', 'AD')->get()->each(function ($item) {
                            echo "<option value='" . $item->KOD . "|||" . $item->AD . "'>" . $item->KOD . " - " . $item->AD . "</option>";
                        });
                    }
                @endphp
                <input type="hidden" name="ich_part_code" id="ich_part_code">
            </select>
        </div>

        {{-- PARÇA ADI --}}
        <div class="col-md-6">
            <label class="form-label">Parça Adı</label>
            <input type="text"
                   class="form-control ich_part_name"
                   data-bs-toggle="tooltip"
                   data-bs-placement="bottom"
                   data-bs-title="ich_part_name"
                   name="ich_part_name"
                   id="STOK_ADI_SHOW"
                   value="{{ @$kart_veri->ich_part_name ?? '' }}"
                   readonly>
        </div>

        {{-- İŞ EMRİ --}}
        <div class="col-md-4">
            <label class="form-label">İş Emri</label>
            <input type="text"
                   class="form-control ich_workorder"
                   data-bs-toggle="tooltip"
                   data-bs-placement="bottom"
                   data-bs-title="ich_workorder"
                   name="ich_workorder"
                   value="{{ @$kart_veri->ich_workorder ?? '' }}">
        </div>

        {{-- KONUM --}}
        <div class="col-md-4">
            <label class="form-label">Konum</label>
            <input type="text"
                   class="form-control ich_location"
                   data-bs-toggle="tooltip"
                   data-bs-placement="bottom"
                   data-bs-title="ich_location"
                   name="ich_location"
                   value="{{ @$kart_veri->ich_location ?? '' }}">
        </div>

        {{-- TEZGAH --}}
        <div class="col-md-4">
            <label class="form-label">Tezgah</label>
            <select class="form-control js-example-basic-single" style="width: 100%;" name="ich_machine" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="ich_machine">
                <option value="">Tezgah Seç</option>
                @php
                    $evraklar=DB::table($database.'imlt00')->orderBy('id', 'ASC')->get();

                    foreach ($evraklar as $key => $veri) {
                        if ($veri->KOD == @$kart_veri->ich_machine) {
                            echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                        }
                        else {
                            echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                        }
                    }
                @endphp
            </select>
        </div>

        {{-- HATA KODU --}}
        <div class="col-md-3">
            <label class="form-label">Hata Kodu</label>
            <input type="text"
                   class="form-control ich_fault_code"
                   data-bs-toggle="tooltip"
                   data-bs-placement="bottom"
                   data-bs-title="ich_fault_code"
                   name="ich_fault_code"
                   value="{{ @$kart_veri->ich_fault_code ?? '' }}">
        </div>

        {{-- MİKTAR --}}
        <div class="col-md-3">
            <label class="form-label">Miktar</label>
            <input type="number"
                   class="form-control ich_quantity"
                   data-bs-toggle="tooltip"
                   data-bs-placement="bottom"
                   data-bs-title="ich_quantity"
                   name="ich_quantity"
                   value="{{ @$kart_veri->ich_quantity ?? '' }}">
        </div>

        {{-- OPERATÖR --}}
        <div class="col-md-6">
            <label class="form-label">Operatör İsmi</label>
            <select class="form-control select2 js-example-basic-single ich_operator"
                    style="width: 100%;"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    data-bs-title="ich_operator"
                    name="ich_operator">

                <option value=""></option>

                @php
                    $pers00 = DB::table($database.'pers00')->orderBy('id')->get();
                @endphp

                @foreach($pers00 as $p)
                    <option value="{{ $p->KOD }}"
                        {{ (@$kart_veri->ich_operator ?? '') == $p->KOD ? 'selected' : '' }}>
                        {{ $p->KOD }} | {{ $p->AD }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- PROBLEM TANIMI --}}
        <div class="col-md-12">
            <label class="form-label">Problem Tanımı</label>
            <textarea class="form-control ich_problem"
                      data-bs-toggle="tooltip"
                      data-bs-placement="bottom"
                      data-bs-title="ich_problem"
                      rows="3"
                      name="ich_problem">{{ @$kart_veri->ich_problem ?? '' }}</textarea>
        </div>

        {{-- KÖK NEDEN --}}
        <div class="col-md-12">
            <label class="form-label">Kök Neden</label>
            <textarea class="form-control ich_rootcause"
                      data-bs-toggle="tooltip"
                      data-bs-placement="bottom"
                      data-bs-title="ich_rootcause"
                      rows="3"
                      name="ich_rootcause">{{ @$kart_veri->ich_rootcause ?? '' }}</textarea>
        </div>

        {{-- DÜZELTİCİ FAALİYET --}}
        <div class="col-md-12">
            <label class="form-label">Düzeltici Faaliyet</label>
            <textarea class="form-control ich_corrective"
                      data-bs-toggle="tooltip"
                      data-bs-placement="bottom"
                      data-bs-title="ich_corrective"
                      rows="3"
                      name="ich_corrective">{{ @$kart_veri->ich_corrective ?? '' }}</textarea>
        </div>

        {{-- AÇIKLAMA --}}
        <div class="col-md-12">
            <label class="form-label">Açıklama</label>
            <textarea class="form-control ich_description"
                      data-bs-toggle="tooltip"
                      data-bs-placement="bottom"
                      data-bs-title="ich_description"
                      rows="3"
                      name="ich_description">{{ @$kart_veri->ich_description ?? '' }}</textarea>
        </div>

    </div>
</div>
<script>
    function stokAdiGetir3(kod) {
        var stokAdi = kod.split("|||");
        $("#ich_part_code").val(stokAdi[0]);
        $("#STOK_ADI_SHOW").val(stokAdi[1]);
    }
</script>