function DepoMevcutlari(kod) {
    Swal.fire({
        text: 'Lütfen bekleyin',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        url: '/mevcutVeriler',
        type: 'get',
        data: { KOD: kod },
        success: function (res) {
            let htmlCode = res.map(row => {
                let criticalClass = (parseFloat(row.MIKTAR) <= 0) ? 'table-danger' : '';
                return `
                    <tr class="${criticalClass}">
                        <td>${row.KOD || ''}</td>
                        <td>${row.STOK_ADI || ''}</td>
                        <td>${row.MIKTAR || ''}</td>
                        <td>${row.SF_SF_UNIT || ''}</td>
                        <td>${row.LOTNUMBER || ''}</td>
                        <td>${row.SERINO || ''}</td>
                        <td>${row.AMBCODE || ''} - ${row.AD || ''}</td>
                        <td>${row.TEXT1 || ''}</td>
                        <td>${row.TEXT2 || ''}</td>
                        <td>${row.TEXT3 || ''}</td>
                        <td>${row.TEXT4 || ''}</td>
                        <td>${row.NUM1 || ''}</td>
                        <td>${row.NUM2 || ''}</td>
                        <td>${row.NUM3 || ''}</td>
                        <td>${row.NUM4 || ''}</td>
                        <td>${row.LOCATION1 || ''}</td>
                        <td>${row.LOCATION2 || ''}</td>
                        <td>${row.LOCATION3 || ''}</td>
                        <td>${row.LOCATION4 || ''}</td>
                    </tr>
                `;
            }).join('');

            if ($.fn.DataTable.isDataTable('#DepoMevcutlari')) {
                $('#DepoMevcutlari').DataTable().clear().destroy();
            }

            $("#DepoMevcutlari > tbody").html(htmlCode);

            $('#DepoMevcutlari').DataTable({
                paging: true,
                info: true,
                ordering: true,
                lengthChange: false,
                searching: true,
                language: {
                    url: 'http://213.159.6.42:8000/tr.json'
                }
            });
        },
        error: function (error) {
            console.error('Ajax hatası:', error);
        },complete: function () {
            Swal.close();
            $("#modal_depo_mevcutlari").modal('show');
        }
    });
}

function StokHareketleri(kod) {
    Swal.fire({
        text: 'Lütfen bekleyin',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        url: '/stok_harketleri',
        type: 'get',
        data: { KOD: kod },
        success: function (res) {
            let htmlCode = '';

            res.forEach((row) => {
                htmlCode += `
                <tr>
                    <td>${row.EVRAKNO || ''}</td>
                    <td>${row.KOD || ''}</td>
                    <td>${row.STOK_ADI || ''}</td>
                    <td>${row.SF_MIKTAR || ''}</td>
                    <td>${row.SF_SF_UNIT || ''}</td>
                    <td>${row.LOTNUMBER || ''}</td>
                    <td>${row.SERINO || ''}</td>
                    <td>${row.AMBCODE || ''} - ${row.DEPO_AD || ''}</td>
                    <td>${row.TEXT1 || ''}</td>
                    <td>${row.TEXT2 || ''}</td>
                    <td>${row.TEXT3 || ''}</td>
                    <td>${row.TEXT4 || ''}</td>
                    <td>${row.NUM1 || ''}</td>
                    <td>${row.NUM2 || ''}</td>
                    <td>${row.NUM3 || ''}</td>
                    <td>${row.NUM4 || ''}</td>
                    <td>${row.LOCATION1 || ''}</td>
                    <td>${row.LOCATION2 || ''}</td>
                    <td>${row.LOCATION3 || ''}</td>
                    <td>${row.LOCATION4 || ''}</td>
                    <td>${row.EVRAKTIPI || ''}</td>
                    <td>${row.created_at}</td>
                </tr>`;
            });

            if ($.fn.DataTable.isDataTable('#stokHarketleri')) {
                $('#stokHarketleri').DataTable().clear().destroy();
            }

            $("#stokHarketleri > tbody").html(htmlCode);

            $('#stokHarketleri').DataTable({
                paging: true,
                info: true,
                ordering: true,
                lengthChange:false,
                searching: true,
                language: {
                    url: 'http://213.159.6.42:8000/tr.json'
                }
            });
        },
        error: function (error) {
            console.error('Ajax hatası:', error);
        },complete: function () {
            Swal.close();
            $("#modal_stok_hareketleri").modal('show');
        }
    });
}

function SatirKopyala(button) {
    const satir = $(button).closest('tr');

    satir.find('input, select, textarea').each(function () {
        const name = $(this).attr('name')?.replace(/\[\]$/, '');
        if (!name) return;

        const yukariInput = $(`[data-name="${name}"]`);

        if (yukariInput.length) {
            if (yukariInput.is(':checkbox')) {
                yukariInput.prop('checked', $(this).is(':checked'));
            } else if (yukariInput.hasClass('select2-hidden-accessible')) {
                const fullValue = $(this).val();
                const sadeceKod = fullValue?.split('|||')[0];
                let bulundu = false;
                yukariInput.find('option').each(function () {
                    const val = $(this).val();
                    if (val?.startsWith(sadeceKod + '|||')) {
                        yukariInput.val(val).trigger('change.select2');
                        bulundu = true;
                        return false;
                    }
                });

                if (!bulundu) {
                    console.warn(`Kod eşleşmedi: ${sadeceKod}`);
                }
            } else {
                yukariInput.val($(this).val());
            }
        }
    });

    mesaj("Veriler yukarı alındı, düzenle sonra ekle", "success");
}

function SatirYazdir(button)
{
    if(!evrakDegisti)
    {
        let row = $(button).closest('tr');
        let table = row.closest('table');

        table.find('tbody tr').not(row).remove();
    }
}

function StokKartinaGit(KOD) {
    
    $.ajax({
        type: 'POST',
        url: '/stokKartinaGit',
        data: { KOD: KOD },
        success: function(res) {
            console.log(res);
            if (res) {
                window.open('kart_stok?ID=' + res, '_blank');
            } else {
                alert('Bir sorun oluştu lütfen tekrar deneyin.');
            }
        },
        error: function(xhr, status, err) {
            $('#loader').hide();
            console.error('Hata:', err);
            alert('Stok kartına gidilemedi. Lütfen tekrar deneyin.');
        }
    });
}

