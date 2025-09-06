<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="robots" content="all" />
  <meta name="robots" content="index,follow" />
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1254" />
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-9" />
  <meta http-equiv="Content-Type" content="text/html; charset=x-mac-turkish" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="../master/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../master/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="../master/bower_components/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../master/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="../master/bower_components/jvectormap/jquery-jvectormap.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="../master/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="../master/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="../master/plugins/iCheck/all.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="../master/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
  <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="../master/plugins/timepicker/bootstrap-timepicker.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="../master/bower_components/select2/dist/css/select2.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../master/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
   folder instead of downloading all of them to reduce the load. -->

   <link rel="stylesheet" href="../master/dist/css/skins/_all-skins.min.css">

   <link rel="stylesheet" href="../master/dataTables/jquery.dataTables.min.css">
   <link rel="stylesheet" href="../master/dataTables/buttons.dataTables.min.css">
       <!-- jQuery 3 -->
<script src="../../master/bower_components/jquery/dist/jquery.min.js"></script>
<!-- <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> -->
<script type="text/javascript">
//sadece sayısal karakterlerin girişine izin veriliyor
  function validate(evt) {
  var theEvent = evt || window.event;

  // Handle paste
  if (theEvent.type === 'paste') {
      key = event.clipboardData.getData('text/plain');
  } else {
  // Handle key press
      var key = theEvent.keyCode || theEvent.which;
      key = String.fromCharCode(key);
  }
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}
</script>


   <script>
     $.getJSON("il/il-bolge.json", function(sonuc){
      $.each(sonuc, function(index, value){
       var row="";
       row +='<option value="'+value.il+'">'+value.il+'</option>';
       $("#il").append(row);
     })
    });


     $("#il").on("change", function(){
      var il=$(this).val();

      $("#ilce").attr("disabled", false).html("<option value=''>Seçin..</option>");
      $.getJSON("il/il-ilce.json", function(sonuc){
       $.each(sonuc, function(index, value){
        var row="";
        if(value.il==il)
        {
         row +='<option value="'+value.ilce+'">'+value.ilce+'</option>';
         $("#ilce").append(row);
       }
     });
     });
    });

  </script>



   <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
   <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Google Font -->

<!-- <link rel="stylesheet">
  <a href="../master/https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic"> -->
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.js"> </script> -->
<style type="text/css">
  #chartdiv {
  width   : 100%;
  height    : 500px;
  font-size : 11px;
}

</style>


</head>
<!-- <body class="hold-transition skin-blue fixed sidebar-collapse sidebar-mini"> -->
<body class="hold-transition skin-blue sidebar-collapse sidebar-mini">


<!-- <body class="hold-transition sidebar-collapse skin-primary sidebar-mini">
-->  <div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="index.php" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><small><?php echo $logo_mini ?></small></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><?php echo $logo_lg ?></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-bs-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
              <!-- Messages: style can be found in dropdown.less-->



              <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                <span class="hidden-xs">Oturum İşlemleri</span>
              </a>
              <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                  <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                  <p>Kullanıcı :
                    <?php

                    $K_ADI=$_SESSION['admin_kadi'];
                    echo $K_ADI;
                    ?>
                    <small></small>
                  </p>
                </li>
                <!-- Menu Body -->

                <!-- Menu Footer-->
                <li class="user-footer">

                  <div class="pull-left">
                    <a href="user.php" class="btn btn-default btn-flat">Kullanıcı Listesi</a>
                  </div>
                  <div class="pull-right">
                    <a href="logout.php" class="btn btn-danger btn-flat">Çıkış Yap</a>
                  </div>
                </li>
              </ul>
            </li>
              </ul>
      </div>
    </nav>
  </header>

  <aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Menü</p>
          <a href="#"><i class="fa fa-circle text-success"></i> </a>
        </div>
      </div>
      <form action="#" method="get" class="sidebar-form">
        <div class="d-flex ">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="d-flex -btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
            </button>
          </span>
        </div>
      </form>
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">   </li>

        <li class=" treeview">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span >Kartlar</span>
            <span class="pull-right-container">
              <span class="label label-primary pull-right">5</span>

              <!-- <i class="fa fa-angle-left pull-right">3</i> -->
            </span>
          </a>

          <ul class="treeview-menu">
            <?php if(strstr($k_grup, "CARİ HESAP KARTI") or strstr($k_grup, "ROOT") OR $admin_kadi=='ROOT' ) { ?>
              <li ><a href="kart_cari.php"><i class="fa fa-circle-o"></i> Cari Kartı</a></li>
            <?php } ?>
            <?php if(strstr($k_grup, "STOK KARTI") or strstr($k_grup, "ROOT") OR $admin_kadi=='ROOT') { ?>
              <li ><a href="kart_stok.php"><i class="fa fa-circle-o"></i> Stok Kartı</a></li>
            <?php } ?>
            <?php if(strstr($k_grup, "DEPO KARTI") or strstr($k_grup, "ROOT") OR $admin_kadi=='ROOT') { ?>
              <li ><a href="kart_depo.php"><i class="fa fa-circle-o"></i> Depo Kartı</a></li>
            <?php } ?>
            <?php if(strstr($k_grup, "PERSONEL KARTI") or strstr($k_grup, "ROOT") OR $admin_kadi=='ROOT') { ?>
              <li ><a href="kart_personel.php"><i class="fa fa-circle-o"></i> Personel Kartı</a></li>
            <?php } ?>
             <?php if(strstr($k_grup, "TEZGAH KARTI") or strstr($k_grup, "ROOT") OR $admin_kadi=='ROOT') { ?>
              <li ><a href="kart_tezgah.php"><i class="fa fa-circle-o"></i> Tezgah Kartı</a></li>
            <?php } ?>



          </ul>
          <ul class="sidebar-menu" data-widget="tree">
          </ul>
        </li>
          <li class=" treeview">
            <a href="#">
              <i class="fa fa-circle-o"></i> <span>Evraklar</span>
              <span class="pull-right-container">
              <span class="label label-primary pull-right">3</span>
                <!-- <i class="fa fa-angle-left pull-right">2</i> -->
              </span>
            </a>

            <ul class="treeview-menu">
             <!--  <?php if(strstr($k_grup, "İRSALİYE KABUL") or strstr($k_grup, "ROOT") OR $admin_kadi=='ROOT' ) { ?>
                <li ><a href="irsaliye_kabul.php"><i class="fa fa-circle-o"></i> İRSALİYE KABUL</a></li>
              <?php } ?> -->
              <?php if(strstr($k_grup, "İRSALİYE GİRİŞ") or strstr($k_grup, "ROOT") OR $admin_kadi=='ROOT') { ?>
                <li ><a href="irsaliye_giris.php"><i class="fa fa-circle-o"></i> İrsaliye Giriş</a></li>
              <?php } ?>

              <?php if(strstr($k_grup, "SEVK İRSALİYESİ") or strstr($k_grup, "ROOT") OR $admin_kadi=='ROOT') { ?>
                <li ><a href="evrak_sevk_irsaliyesi.php"><i class="fa fa-circle-o"></i> Sevk İrsaliyesi</a></li>
              <?php } ?>

              <?php if(strstr($k_grup, "SATIN ALMA") or strstr($k_grup, "ROOT") OR $admin_kadi=='ROOT') { ?>
                <li ><a href="evrak_satin_alma.php"><i class="fa fa-circle-o"></i> Satın Alma Siparişi</a></li>
              <?php } ?>


            </ul>
          </li>
<!--         <li class=" treeview">
          <a href="#">
            <i class="fa fa-folder-o"></i> <span style="color: cyan">Doküman</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="nav-item"><a href="document/bilgi.xlsx"><i class="fa fa-circle-o"></i> Proje Açıklamaları</a></li>
          </ul>
        </li> -->
      </ul>
    </section>

  </aside>
