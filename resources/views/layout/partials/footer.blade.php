</div>
    <footer class="main-footer footer-ayar row">

      <strong>Copyright &copy; {{ now()->year }} QzuERP <a href="https:\\karakuzu.info" target="_blank">Karakuzu Bilişim</a></strong> Tüm Hakları Saklıdır.

    </footer>

    <div class="control-sidebar-bg"></div>

    <script>
      document.title = '{{ $ekranAdi }} - {{ $firmaAdi }}';
    </script>
    @if (session('error'))
      <script>
        iziToast.success({
          // title: 'Başarılı!',
          message: '{{ session('error') }}',
          position: 'topRight',
          timeout: 5000,
          progressBar: true,
          transitionIn: 'fadeInUp',
          transitionOut: 'fadeOut',
          close: true,
          backgroundColor: '#f9f9f9', 
          titleColor: '#333',         
          messageColor: '#555',       
          progressBarColor: '#4CAF50',
          iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845648.png',
          closeOnEscape: true
        });
      </script>


    @elseif (isset($_GET['kayit']) && $_GET['kayit'] == "ok")
      <script>
        iziToast.success({
          // title: 'Başarılı!',
          message: 'Evrak Başarıyla Oluşturuldu',
          position: 'topRight',
          timeout: 5000,
          progressBar: true,
          transitionIn: 'fadeInUp',
          transitionOut: 'fadeOut',
          close: true,
          backgroundColor: '#f9f9f9', 
          titleColor: '#333',         
          messageColor: '#555',       
          progressBarColor: '#4CAF50',
          iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845646.png',
          closeOnEscape: true
        });
      </script>

    @elseif (isset($_GET['duzenleme']) && $_GET['duzenleme'] == "ok")
      <script>
        iziToast.success({
          // title: 'Başarılı!',
          message: 'Değişiklikler Başarıyla Kaydedildi',
          position: 'topRight',
          timeout: 5000,
          progressBar: true,
          transitionIn: 'fadeInUp',
          transitionOut: 'fadeOut',
          close: true,
          backgroundColor: '#f9f9f9',  // Daha beyaz arka plan
          titleColor: '#333',         // Başlık rengi koyu gri
          messageColor: '#555',       // Mesaj rengi biraz koyu
          progressBarColor: '#4CAF50',// Yeşil progress bar
          iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845646.png', // Başarı ikonu
          closeOnEscape: true
        });
      </script>

    @elseif (isset($_GET['silme']) && $_GET['silme'] == "ok")
      <script>
        iziToast.success({
          // title: 'Başarılı!',
          message: 'Silme İşlemi Başarıyla Tamamlandı',
          position: 'topRight',
          timeout: 5000,
          progressBar: true,
          transitionIn: 'fadeInUp',
          transitionOut: 'fadeOut',
          close: true,
          backgroundColor: '#f9f9f9',  // Daha beyaz arka plan
          titleColor: '#333',         // Başlık rengi koyu gri
          messageColor: '#555',       // Mesaj rengi biraz koyu
          progressBarColor: '#4CAF50',// Yeşil progress bar
          iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845646.png', // Başarı ikonu
          closeOnEscape: true
        });
      </script>
    @endif

    @if (!in_array($ekran, $kullanici_read_yetkileri) && $ekran != "index" && $ekran != "sifreDegistir" && $ekran != "kullaniciTanimlari")
      <script>
        window.location = "/index?hata=yetkisizgiris";
      </script>
    @endif

    @if (!isset($kart_veri))
      <script>
        $(document).ready(function() { 
          inputTemizle();
        });
      </script>
    @endif

    <script>
      // Setup - add a text input to each footer cell
      $('#evrakSuzTable tfoot th').each( function () {
        var title = $(this).text();
        if(title == "#") {
          $(this).html( '<b>Git</b>' );
        }
        else {
          $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />' );
        }

      });

      $('#evrakSuzTable2 tfoot th').each( function () {
        var title = $(this).text();
        if(title == "#") {
          $(this).html( '<b>Git</b>' );
        }
        else {
          $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />' );
        }

      });

      $('#popupSelect tfoot th').each( function () {
          var title = $(this).text();
          if(title == "#") {
            $(this).html( '<b>Git</b>' );
          }
          else {
            $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />' );
          }
      });

      $('#popupSelect2 tfoot th').each( function () {
          var title = $(this).text();
          if(title == "#") {
            $(this).html( '<b>Git</b>' );
          }
          else {
            $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />' );
          }
      });

      $('#popupInfo tfoot th').each( function () {
        var title = $(this).text();
        if(title == "#") {
          $(this).html( '<b>Git</b>' );
        }
        else {
          $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />' );
        }
      });
      
      $('#example2 tfoot th').each( function () {
        var title = $(this).text();
        if(title == "#") {
          $(this).html( '<b>Git</b>' );
        }
        else {
          $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />' );
        }
      });

      $('#listeleTable tfoot th').each( function () {
        var title = $(this).text();
        if(title == "#") {
          $(this).html( '<b>Git</b>' );
        }
        else {
          $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />' );
        }
      });

      $(document).ready(function() {
        // DataTable
        var table = $('#evrakSuzTable').DataTable({
          "order": [[ 0, "desc" ]],
          dom: 'rtip',
          buttons: ['copy', 'excel', 'print'],
          language: {
            url: '{{ asset("tr.json") }}'
          },
          initComplete: function () {
            // Apply the search
            this.api().columns().every( function () {
              var that = this;

              $( 'input', this.footer() ).on( 'keyup change clear', function () {
                if ( that.search() !== this.value ) {
                  that
                  .search( this.value )
                  .draw();
                }
              });
            });
          }
        });

        var table = $('#listeleTable').DataTable({
          "order": [[ 0, "desc" ]],
          dom: 'brtip',
          buttons: ['copy', 'excel', 'print'],
          paging: false,
          language: {
            url: '{{ asset("tr.json") }}'
          },
          initComplete: function () {
            // Apply the search
            this.api().columns().every( function () {
              var that = this;

              $( 'input', this.footer() ).on( 'keyup change clear', function () {
                if ( that.search() !== this.value ) {
                  that
                  .search( this.value )
                  .draw();
                }
              });
            });
          }
        });

        var table = $('#evrakSuzTable2').DataTable({
          "order": [[ 0, "desc" ]],
          dom: 'rtip',
          buttons: ['copy', 'excel', 'print'],
          language: {
            url: '{{ asset("tr.json") }}'
          },
          initComplete: function () {
            // Apply the search
            this.api().columns().every( function () {
              var that = this;

              $( 'input', this.footer() ).on( 'keyup change clear', function () {
                if ( that.search() !== this.value ) {
                  that
                  .search( this.value )
                  .draw();
                }
              });
            });
          }
        });

        if($.fn.DataTable.isDataTable('#popupSelect')){
          $('#popupSelect').DataTable().destroy();
        }
        
        if($.fn.DataTable.isDataTable('#example2')){
          $('#example2').DataTable().destroy();
        }
        
        if($.fn.DataTable.isDataTable('#popupInfo')){
          $('#popupInfo').DataTable().destroy();
        }
        
        if($.fn.DataTable.isDataTable('#popupSelect2') && !$('#popupSelect2')){
          $('#popupSelect2').DataTable().destroy();
        }

        var table = $('#popupSelect').DataTable({
          "order": [[ 0, "desc" ]],
          dom: 'rtip',
          buttons: ['copy', 'excel', 'print'],
          language: {
            url: '{{ asset("tr.json") }}'
          },
          initComplete: function () {
            this.api().columns().every( function () {
              var that = this;

              $( 'input', this.footer() ).on( 'keyup change clear', function () {
                if ( that.search() !== this.value ) {
                  that
                  .search( this.value )
                  .draw();
                }
              });
            });
          }
        });
        var table = $('#example2').DataTable({
            order: [[0, "desc"]],
            dom: 'rtip',
            paging: false, // << bu olacak
            buttons: ['copy', 'excel', 'print'],
            language: {
                url: '{{ asset("tr.json") }}'
            },
            initComplete: function () {
                this.api().columns().every(function () {
                    var that = this;

                    $('input', this.footer()).on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                });
            }
        });


        var table = $('#popupInfo').DataTable({
          "order": [[ 0, "desc" ]],
          dom: 'rtip',
          buttons: ['copy', 'excel', 'print'],
          language: {
            url: '{{ asset("tr.json") }}'
          },
          initComplete: function () {
            this.api().columns().every( function () {
              var that = this;

              $( 'input', this.footer() ).on( 'keyup change clear', function () {
                if ( that.search() !== this.value ) {
                  that
                  .search( this.value )
                  .draw();
                }
              });
            });
          }
        });


        var table = $('#popupSelect2').DataTable({
          "order": [[ 0, "desc" ]],
          dom: 'rtip',
          buttons: ['copy', 'excel', 'print'],
          language: {
            url: '{{ asset("tr.json") }}'
          },
          initComplete: function () {
            this.api().columns().every( function () {
              var that = this;

              $( 'input', this.footer() ).on( 'keyup change clear', function () {
                if ( that.search() !== this.value ) {
                  that
                  .search( this.value )
                  .draw();
                }
              });
            });
          }
        });

        refreshBaglantiliDokumanlarTable();

      });

    </script>

  </body>
</html>
