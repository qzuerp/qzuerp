@section('content')

<div class="container-fluid">

    {{-- ÜST SEÇİM --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Stok Kodu Seç</label>
                    <select id="stok_kodu" class="form-select">
                        <option value="">Stok seçiniz</option>
                        @foreach($stoklar as $stok)
                            <option value="{{ $stok->STOK_KODU }}">
                                {{ $stok->STOK_KODU }} - {{ $stok->STOK_ADI }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="btnGetir" class="btn btn-primary w-100">
                        Geçmişi Getir
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- GEÇMİŞ TABLOSU --}}
    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            Stok İzlenebilirlik Geçmişi
        </div>
        <div class="card-body">
            <table class="table table-hover table-sm" id="gecmisTable">
                <thead class="table-light">
                    <tr>
                        <th>Tarih</th>
                        <th>Kaynak</th>
                        <th>Belge No</th>
                        <th>Açıklama</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Stok seçiniz
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    $('#btnGetir').on('click', function () {
        let stok = $('#stok_kodu').val();

        if (!stok) {
            alert('Stok seç');
            return;
        }

        $.ajax({
            url: '{{ route("stok.gecmisi.getir") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                stok_kodu: stok
            },
            success: function (data) {
                let tbody = '';

                if (data.length === 0) {
                    tbody = `
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Kayıt bulunamadı
                            </td>
                        </tr>`;
                } else {
                    data.forEach(row => {
                        tbody += `
                            <tr>
                                <td>${row.tarih}</td>
                                <td><span class="badge bg-secondary">${row.kaynak}</span></td>
                                <td>${row.no}</td>
                                <td>${row.aciklama}</td>
                            </tr>`;
                    });
                }

                $('#gecmisTable tbody').html(tbody);
            }
        });
    });
</script>
@endsection
