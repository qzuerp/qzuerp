@php
    $ic_gain_categories = json_decode($kart_veri->ic_gain_categories ?? '[]', true);
    $ic_functions       = json_decode($kart_veri->ic_functions ?? '[]', true);
@endphp

<div class="form" style="display:none;" id="IC">
    <!-- Document information -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Doküman No</label>
            <input type="text" class="form-control" name="ic_doc_no"
                   value="{{ $kart_veri->ic_doc_no ?? '' }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Yayın Tarihi</label>
            <input type="date" class="form-control" name="ic_publish_date"
                   value="{{ $kart_veri->ic_publish_date ?? '' }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Rev No/Tarihi</label>
            <input type="text" class="form-control" name="ic_rev_info"
                   value="{{ $kart_veri->ic_rev_info ?? '' }}">
        </div>
    </div>

    <h4 class="mb-3">İyileştirme Bilgileri</h4>
    <div class="row mb-3">
        <div class="col-md-2">
            <label class="form-label">No</label>
            <input type="number" class="form-control" name="ic_no"
                   value="{{ $kart_veri->ic_no ?? '' }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Tarih</label>
            <input type="date" class="form-control" name="ic_date"
                   value="{{ $kart_veri->ic_date ?? '' }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">İyileştirme Türü</label>
            <select name="ic_type" class="form-select">
                <option disabled>Seçiniz...</option>
                @foreach(["Kaizen","KYS İyileştirme","5S","Diğer"] as $item)
                    <option value="{{ $item }}" 
                        {{ @$kart_veri->ic_type == $item ? 'selected' : '' }}>{{ $item }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Bölüm</label>
            <select name="ic_department" class="form-select">
                <option disabled>Seçiniz...</option>
                @foreach(["Talaşlı İmalat","Kalite","Ambalajlama","Planlama","Proje","Satın alma","Ar-Ge","Diğer"] as $dep)
                    <option value="{{ $dep }}" 
                        {{ @$kart_veri->ic_department == $dep ? 'selected' : '' }}>{{ $dep }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Kişi</label>
            <input type="text" class="form-control" name="ic_person"
                   value="{{ $kart_veri->ic_person ?? '' }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Parça Kodu / Adı</label>
            <input type="text" class="form-control" name="ic_part"
                   value="{{ $kart_veri->ic_part ?? '' }}">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <label class="form-label">İlgili Proses Adı</label>
            <input type="text" class="form-control" name="ic_process"
                   value="{{ $kart_veri->ic_process ?? '' }}">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Mevcut Durum</label>
        <textarea class="form-control" rows="3" name="ic_current_status">{{ $kart_veri->ic_current_status ?? '' }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Yeni Durum</label>
        <textarea class="form-control" rows="3" name="ic_new_status">{{ $kart_veri->ic_new_status ?? '' }}</textarea>
    </div>

    <h4 class="mb-3">İyileştirmeden Kazançlar</h4>
    <div class="row mb-3">

        <!-- Kazanç Kategorileri -->
        <div class="col-md-8">
            <label class="form-label">Kazanç Kategorileri</label>

            @php
                $gainList = [
                    "1"  => "Maliyet Tasarrufu",
                    "2"  => "Zaman Tasarrufu",
                    "3"  => "Enerji Tasarrufu",
                    "4"  => "Ergonomi",
                    "5"  => "İş Güvenliği",
                    "6"  => "Yangın",
                    "7"  => "5S",
                    "8"  => "Çevre/Sağlık",
                    "9"  => "Verimlilik",
                    "10" => "İş Kolaylığı",
                    "11" => "Kalite",
                    "12" => "Diğer"
                ];
            @endphp

            <div class="row">

                @foreach($gainList as $key => $text)
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input"
                            type="checkbox"
                            name="ic_gain_categories[]"
                            value="{{ $key }}"
                            {{ in_array((string)$key, $ic_gain_categories) ? 'checked' : '' }}>

                        <label class="form-check-label">
                            {{ $key }}- {{ $text }}
                        </label>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        <!-- Fonksiyonlar -->
        <div class="col-md-4">
            <label class="form-label">Fonksiyonlar</label>

            @php
                $functionList = [
                    "Üretim",
                    "Kalite",
                    "Planlama",
                    "Proje",
                    "Satın alma",
                    "Ar-Ge"
                ];
            @endphp

            @foreach($functionList as $func)
            <div class="form-check">
                <input class="form-check-input"
                    type="checkbox"
                    name="ic_functions[]"
                    value="{{ $func }}"
                    {{ in_array($func, $ic_functions) ? 'checked' : '' }}>
                <label class="form-check-label">{{ $func }}</label>
            </div>
            @endforeach
        </div>

    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label class="form-label">Sonuç</label>
            <div class="form-check">
                <input type="radio" class="form-check-input"
                       name="ic_result" value="Olumlu"
                       {{ @$kart_veri->ic_result == 'Olumlu' ? 'checked' : '' }}>
                <label class="form-check-label">Olumlu</label>
            </div>

            <div class="form-check">
                <input type="radio" class="form-check-input"
                       name="ic_result" value="Olumsuz"
                       {{ @$kart_veri->ic_result == 'Olumsuz' ? 'checked' : '' }}>
                <label class="form-check-label">Olumsuz</label>
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label">Bitiş Tarihi</label>
            <input type="date" class="form-control" name="ic_finish_date"
                   value="{{ $kart_veri->ic_finish_date ?? '' }}">
        </div>
    </div>
</div>
