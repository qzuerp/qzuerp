<div class="form" style="display:none;" id="TOPLANTI">
    <!-- ===== TOPLANTI BİLGİLERİ ===== -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <label>Tarih</label>
            <input type="date" name="tarih" value="{{ @$kart_veri->tarih }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label>Başlangıç</label>
            <input type="time" name="baslangic" value="{{ @$kart_veri->baslangic ? substr($kart_veri->baslangic, 0, 5) : '' }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label>Bitiş</label>
            <input type="time" name="bitis" value="{{ @$kart_veri->bitis ? substr($kart_veri->bitis, 0, 5) : '' }}" class="form-control">
        </div>
        <div class="col-md-5">
            <label>Toplantı Yeri</label>
            <input type="text" name="yer" value="{{ @$kart_veri->yer }}" class="form-control">
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label>Toplantı Konusu</label>
            <input type="text" name="konu" value="{{ @$kart_veri->konu }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label>Toplantı Türü</label>
            <select name="tur" class="form-select select2">
                <option {{ @$kart_veri->tur == 'Firma İçi' ? 'selected' : '' }}>Firma İçi</option>
                <option {{ @$kart_veri->tur == 'Müşteri' ? 'selected' : '' }}>Müşteri</option>
                <option {{ @$kart_veri->tur == 'Tedarikçi' ? 'selected' : '' }}>Tedarikçi</option>
                <option {{ @$kart_veri->tur == 'Diğer' ? 'selected' : '' }}>Diğer</option>
            </select>
        </div>
    </div>

    <hr>

    <!-- ===== KARARLAR ===== -->
    <h6>Toplantı Kararları</h6>

    <table class="table table-bordered">
        <thead>
            <tr>
            <th style="width:50px">#</th>
            <th>Karar</th>
            <th>Sorumlu</th>
            <th>Bitiş Tarihi</th>
            <th style="width:60px"></th>
            </tr>
        </thead>
        <tbody id="kararTable"></tbody>
    </table>

    <button type="button" id="addKarar" class="btn btn-primary mb-4">
        + Karar Ekle
    </button>

    <hr>

    <!-- ===== KATILIMCILAR ===== -->
    <h6>Katılımcılar</h6>

    <table class="table table-bordered">
        <thead>
            <tr>
            <th style="width:50px">#</th>
            <th>Ad Soyad</th>
            <th style="width:60px"></th>
            </tr>
        </thead>
        <tbody id="katilimciTable"></tbody>
    </table>

    <button type="button" id="addKatilimci" class="btn btn-primary">
        + Katılımcı Ekle
    </button>
</div>
<!-- ===== VERİLERİ JS'E AKTAR ===== -->
<script>
    const kararlar     = @json(json_decode(@$kart_veri->karar ?? '[]', true));
    const sorumlular   = @json(json_decode(@$kart_veri->sorumlu ?? '[]', true));
    const kararBitis   = @json(json_decode(@$kart_veri->karar_bitis ?? '[]', true));
    const katilimcilar = @json(json_decode(@$kart_veri->katilimci ?? '[]', true));
</script>

<!-- ===== JS ===== -->
<script>
$(function(){

/* ========= KARAR ========= */
function reIndexKarar(){
  $('#kararTable tr').each(function(i){
    $(this).find('td:first').text(i + 1)
  })
}

function addKarar(karar = '', sorumlu = '', bitis = ''){
  $('#kararTable').append(`
    <tr>
      <td></td>
      <td><input name="karar[]" class="form-control" value="${karar}"></td>
      <td><input name="sorumlu[]" class="form-control" value="${sorumlu}"></td>
      <td><input type="date" name="karar_bitis[]" class="form-control" value="${bitis ?? ''}"></td>
      <td>
        <button type="button" class="btn btn-danger removeKarar">✕</button>
      </td>
    </tr>
  `)
  reIndexKarar()
}

$('#addKarar').on('click', function(){
  addKarar()
})

$(document).on('click','.removeKarar',function(){
  $(this).closest('tr').remove()
  reIndexKarar()
})

/* ========= KATILIMCI ========= */
function reIndexKatilimci(){
  $('#katilimciTable tr').each(function(i){
    $(this).find('td:first').text(i + 1)
  })
}

function addKatilimci(ad = ''){
  $('#katilimciTable').append(`
    <tr>
      <td></td>
      <td><input name="katilimci[]" class="form-control" value="${ad}" placeholder="Ad Soyad"></td>
      <td>
        <button type="button" class="btn btn-danger removeKatilimci">✕</button>
      </td>
    </tr>
  `)
  reIndexKatilimci()
}

$('#addKatilimci').on('click', function(){
  addKatilimci()
})

$(document).on('click','.removeKatilimci',function(){
  $(this).closest('tr').remove()
  reIndexKatilimci()
})

/* ========= SAYFA AÇILIŞI ========= */
if (kararlar.length) {
  kararlar.forEach((k, i) => {
    addKarar(
      k,
      sorumlular[i] ?? '',
      kararBitis[i] ?? ''
    )
  })
}

if (katilimcilar.length) {
  katilimcilar.forEach(ad => addKatilimci(ad))
} else {
  addKatilimci()
}

});
</script>
