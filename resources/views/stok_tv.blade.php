@extends('layout.mainlayout')

@php
if (Auth::check()) {
	$user = Auth::user();
}
$kullanici_veri = DB::table('users')->where('id',$user->id)->first();

$ekran = "STOKTV";
$ekranAdi = "Depo Mevcutları";
$ekranLink = "stok_tv";

$kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
$kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
$kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);
@endphp

@section('content')
<style>
	#overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0,0,0,0.8);
		display: none;
		justify-content: center;
		align-items: center;
		z-index: 99999;
	}
	#overlay img {
		max-width: 100vw;
		max-height: 100vh;
		transform: scale(3);
		transition: transform 0.25s ease;
	}
	#overlay.show img {
		transform: scale(1);
	}
	.refresh-icon.spinning {
		animation: spin 1s linear infinite;
	}
	@keyframes spin {
		100% { transform: rotate(360deg); }
	}
	.last-update {
		display: inline-block;
		padding: 5px 10px;
		background: #f8f9fa;
		border-radius: 5px;
		font-size: 12px;
		margin-top: 10px;
	}
	.auto-refresh-badge {
		display: inline-block;
		padding: 3px 8px;
		background: #28a745;
		color: white;
		border-radius: 3px;
		font-size: 11px;
		margin-left: 10px;
		animation: pulse 2s infinite;
	}
	@keyframes pulse {
		0%, 100% { opacity: 1; }
		50% { opacity: 0.7; }
	}
</style>

<div id="overlay"></div>

<div class="content-wrapper" style="min-height: 822px;">
	@include('layout.util.evrakContentHeader')

	<section class="content">
		<div class="row">
			<div class="col">
				<div class="box box-info">
					<div class="box-body">
						<div class="row align-items-end">
							<div class="col-md-12">
								<label class="form-label fw-bold">İşlemler</label>
								<div class="action-btn-group flex gap-2 flex-wrap">
									<button type="button" class="action-btn btn btn-primary" onclick="refreshData()">
										<i class="fas fa-sync refresh-icon"></i> Verileri Yenile
									</button>
									<button type="button" data-evrak-kontrol class="action-btn btn btn-success" onclick="exportTableToExcel('table')">
										<i class="fas fa-file-excel"></i> Excel'e Aktar
									</button>
									<button type="button" class="action-btn btn btn-success" onclick="exportAllTableToExcel('table')">
										<i class="fas fa-file-excel"></i> Tümünü Excel'e Aktar
									</button>
									<button type="button" data-evrak-kontrol class="action-btn btn btn-danger" onclick="exportTableToWord('table')">
										<i class="fas fa-file-word"></i> Word'e Aktar
									</button>
								</div>
								<div class="last-update">
									<i class="fas fa-clock"></i> Son Güncelleme: <span id="lastUpdate">Yükleniyor...</span>
									<!-- <span class="auto-refresh-badge"><i class="fas fa-sync-alt"></i> Otomatik Yenileme Aktif</span> -->
								</div>
							</div>
						</div>

						<div class="row mt-3" style="overflow: auto">
							<table id="table" class="table table-hover text-center" data-page-length="10">
								<thead>
									<tr class="bg-primary">
										<th style="min-width: 150px">Kod</th>
										<th style="min-width: 200px">Ad</th>
										<th style="min-width: 200px">Ad 2</th>
										<th style="min-width: 100px">Revizyon No</th>
										<th style="min-width: 100px">Miktar</th>
										<th style="min-width: 100px">Birim</th>
										<th style="min-width: 100px">Lot</th>
										<th style="min-width: 100px">Seri No</th>
										<th style="min-width: 100px">Depo</th>
										<th style="min-width: 100px">Varyant Text 1</th>
										<th style="min-width: 100px">Varyant Text 2</th>
										<th style="min-width: 100px">Varyant Text 3</th>
										<th style="min-width: 100px">Varyant Text 4</th>
										<th style="min-width: 100px">Ölçü 1</th>
										<th style="min-width: 100px">Ölçü 2</th>
										<th style="min-width: 100px">Ölçü 3</th>
										<th style="min-width: 100px">Ölçü 4</th>
										<th style="min-width: 100px">Lok 1</th>
										<th style="min-width: 100px">Lok 2</th>
										<th style="min-width: 100px">Lok 3</th>
										<th style="min-width: 100px">Lok 4</th>
										<th style="min-width: 100px">Görsel</th>
										<th>#</th>
									</tr>
								</thead>
								<tfoot>
									<tr class="bg-info">
										<th>Kod</th>
										<th>Ad</th>
										<th>Ad 2</th>
										<th>Revizyon No</th>
										<th>Miktar</th>
										<th>Birim</th>
										<th>Lot</th>
										<th>Seri No</th>
										<th>Depo</th>
										<th>Varyant Text 1</th>
										<th>Varyant Text 2</th>
										<th>Varyant Text 3</th>
										<th>Varyant Text 4</th>
										<th>Ölçü 1</th>
										<th>Ölçü 2</th>
										<th>Ölçü 3</th>
										<th>Ölçü 4</th>
										<th>Lok 1</th>
										<th>Lok 2</th>
										<th>Lok 3</th>
										<th>Lok 4</th>
										<th>Görsel</th>
										<th>#</th>
									</tr>
								</tfoot>
								<tbody>
									<!-- DataTable otomatik dolduracak -->
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
	<script>
		let table;
		let searchDebounceTimer;
		let autoRefreshInterval;

		// Görsel overlay için click handler
		document.addEventListener('click', e => {
			const overlay = document.getElementById('overlay');
			if (e.target.tagName === 'IMG' && e.target.classList.contains('kart_img')) {
				const clone = e.target.cloneNode(true);
				overlay.innerHTML = '';
				overlay.appendChild(clone);
				overlay.style.display = 'flex';
			}
			else if (e.target.id === 'overlay') {
				overlay.style.display = 'none';
				overlay.innerHTML = '';
			}
		});

		// Debounce fonksiyonu - arama için
		function debounce(func, wait) {
			return function executedFunction(...args) {
				const later = () => {
					clearTimeout(searchDebounceTimer);
					func(...args);
				};
				clearTimeout(searchDebounceTimer);
				searchDebounceTimer = setTimeout(later, wait);
			};
		}

		// Verileri yenileme fonksiyonu
		function refreshData(showAnimation = true) {
			const icon = document.querySelector('.refresh-icon');
			if (showAnimation && icon) {
				icon.classList.add('spinning');
			}
			
			table.ajax.reload(function() {
				if (icon) {
					icon.classList.remove('spinning');
				}
				updateLastRefreshTime();
			}, false); // false = mevcut sayfada kal, filtreler korunsun
		}

		// Son güncelleme zamanını göster
		function updateLastRefreshTime() {
			const now = new Date();
			const timeStr = now.toLocaleTimeString('tr-TR', { 
				hour: '2-digit', 
				minute: '2-digit', 
				second: '2-digit' 
			});
			document.getElementById('lastUpdate').textContent = timeStr;
		}

		// Otomatik yenileme başlat
		function startAutoRefresh(intervalSeconds = 30) {
			// Önce varsa eski interval'ı temizle
			if (autoRefreshInterval) {
				clearInterval(autoRefreshInterval);
			}
			
			// Yeni interval başlat
			autoRefreshInterval = setInterval(function() {
				refreshData(false); // false = animasyon gösterme (sessiz yenileme)
			}, intervalSeconds * 1000);
			
			console.log(`Otomatik yenileme başlatıldı: Her ${intervalSeconds} saniyede bir`);
		}

		// Otomatik yenilemeyi durdur
		function stopAutoRefresh() {
			if (autoRefreshInterval) {
				clearInterval(autoRefreshInterval);
				autoRefreshInterval = null;
				console.log('Otomatik yenileme durduruldu');
			}
		}

		$(document).ready(function() {
			// Footer'a arama inputları ekle
			$('#table tfoot th').each(function () {
				var title = $(this).text();
				if (title == "#") {
					$(this).html('<b>Git</b>');
				} else {
					$(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
				}
			});

			// DataTable başlatma
			table = $('#table').DataTable({
				"ajax": {
					"url": "{{ route('stok_tv_data') }}",
					"dataSrc": "data",
					"error": function(xhr, error, thrown) {
						console.error('Veri yükleme hatası:', error);
						alert('Veriler yüklenirken bir hata oluştu. Lütfen sayfayı yenileyin.');
					}
				},
				"columns": [
					{ 
						"data": "KOD", 
						"render": function(data) { 
							return '<b>' + (data || '') + '</b>'; 
						} 
					},
					{ 
						"data": "STOK_ADI", 
						"render": function(data) { 
							return '<b>' + (data || '') + '</b>'; 
						} 
					},
					{ 
						"data": "NAME2", 
						"render": function(data) { 
							return '<b>' + (data || '') + '</b>'; 
						} 
					},
					{ 
						"data": "REVNO", 
						"render": function(data) { 
							return '<b>' + (data || '') + '</b>'; 
						} 
					},
					{ 
						"data": "MIKTAR", 
						"render": function(data) { 
							return '<b style="color:blue">' + (data || '0') + '</b>'; 
						} 
					},
					{ 
						"data": "SF_SF_UNIT", 
						"render": function(data) { 
							return '<b>' + (data || '') + '</b>'; 
						} 
					},
					{ "data": "LOTNUMBER", "defaultContent": "" },
					{ "data": "SERINO", "defaultContent": "" },
					{ 
						"data": null,
						"render": function(data) { 
							return (data.AMBCODE || '') + (data.DEPO_ADI ? ' - ' + data.DEPO_ADI : ''); 
						}
					},
					{ "data": "TEXT1", "defaultContent": "" },
					{ "data": "TEXT2", "defaultContent": "" },
					{ "data": "TEXT3", "defaultContent": "" },
					{ "data": "TEXT4", "defaultContent": "" },
					{ "data": "NUM1", "defaultContent": "" },
					{ "data": "NUM2", "defaultContent": "" },
					{ "data": "NUM3", "defaultContent": "" },
					{ "data": "NUM4", "defaultContent": "" },
					{ "data": "LOCATION1", "defaultContent": "" },
					{ "data": "LOCATION2", "defaultContent": "" },
					{ "data": "LOCATION3", "defaultContent": "" },
					{ "data": "LOCATION4", "defaultContent": "" },
					{
						"data": "imgSrc",
						"orderable": false,
						"render": function(data) {
							return data ? '<img class="kart_img" src="' + data + '" alt="" width="100" style="cursor: pointer;">' : '';
						}
					},
					{
						"data": "id",
						"orderable": false,
						"render": function(data) {
							return '<a class="btn btn-info" href="kart_stok?ID=' + data + '" target="_blank"><i class="fa fa-chevron-circle-right text-white"></i></a>';
						}
					}
				],
				"order": [[0, "desc"]],
				"dom": 'rtip',
				"deferRender": true,
				"processing": true,
				"language": {
					url: '{{ asset("tr.json") }}',
				},
				"initComplete": function () {
					updateLastRefreshTime();
					
					// Kolon bazlı arama - DEBOUNCE İLE
					this.api().columns().every(function () {
						var that = this;
						
						// Debounce'lu arama fonksiyonu
						const debouncedSearch = debounce(function(value) {
							if (that.search() !== value) {
								that.search(value).draw();
								// Arama sonrası verileri yenile (3 saniye sonra)
								setTimeout(function() {
									refreshData(false);
								}, 3000);
							}
						}, 800); // 800ms bekle, sonra ara
						
						$('input', this.footer()).on('keyup change clear', function () {
							debouncedSearch(this.value);
						});
					});
				}
			});

			// Otomatik yenileme başlat - 30 saniyede bir
			startAutoRefresh(30);
			
			// Sayfa kapatılırken interval'ı temizle
			$(window).on('beforeunload', function() {
				stopAutoRefresh();
			});
		});


		// Tüm verileri Excel'e aktar
		function exportAllTableToExcel(tableId) {
			let dt = $('#'+tableId).DataTable();

			let data = [];

			// Header
			data.push([
				"Kod","Ad","Ad 2","Rev","Miktar","Birim","Lot","Seri No","Depo",
				"Text1","Text2","Text3","Text4",
				"Num1","Num2","Num3","Num4",
				"Lok1","Lok2","Lok3","Lok4"
			]);

			// 🔥 TÜM VERİLER (filtre vs umrumda değil)
			dt.rows().every(function () {
				let d = this.data();
				data.push([
					d.KOD ?? '',
					d.STOK_ADI ?? '',
					d.NAME2 ?? '',
					d.REVNO ?? '',
					d.MIKTAR ?? '',
					d.SF_SF_UNIT ?? '',
					d.LOTNUMBER ?? '',
					d.SERINO ?? '',
					(d.AMBCODE ?? '') + (d.DEPO_ADI ? ' - ' + d.DEPO_ADI : ''),
					d.TEXT1 ?? '',
					d.TEXT2 ?? '',
					d.TEXT3 ?? '',
					d.TEXT4 ?? '',
					d.NUM1 ?? '',
					d.NUM2 ?? '',
					d.NUM3 ?? '',
					d.NUM4 ?? '',
					d.LOCATION1 ?? '',
					d.LOCATION2 ?? '',
					d.LOCATION3 ?? '',
					d.LOCATION4 ?? ''
				]);
			});

			let wb = XLSX.utils.book_new();
			let ws = XLSX.utils.aoa_to_sheet(data);
			XLSX.utils.book_append_sheet(wb, ws, "Stok");

			// 🔥 kritik nokta (Excel’i Excel yapan yer)
			let wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
			let blob = new Blob(
				[wbout],
				{ type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" }
			);

			let link = document.createElement("a");
			link.href = URL.createObjectURL(blob);
			link.download = "Stok_Listesi_Tumu_" + new Date().toLocaleDateString('tr-TR') + ".xlsx";
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}


		// Word'e aktar
		function exportTableToWord(tableId) {
			let table = document.getElementById(tableId).outerHTML;
			let htmlContent = `<!DOCTYPE html>
				<html>
				<head>
					<meta charset='UTF-8'>
					<style>
						table { border-collapse: collapse; width: 100%; }
						th, td { border: 1px solid black; padding: 8px; text-align: left; }
						th { background-color: #4CAF50; color: white; }
					</style>
				</head>
				<body>
					<h2>Stok Listesi - ${new Date().toLocaleDateString('tr-TR')}</h2>
					${table}
				</body>
				</html>`;

			let blob = new Blob(['\ufeff', htmlContent], { type: 'application/msword' });
			let url = URL.createObjectURL(blob);
			let link = document.createElement("a");
			link.href = url;
			link.download = "Stok_Listesi_" + new Date().toLocaleDateString('tr-TR') + ".doc";
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}
	</script>
</div>
@endsection