
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
  <title>Karakuzu </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="stylesheet" href="{{ URL::asset('assets/bower_components/bootstrap/dist/css/abootstrap.min.css') }}" />
<!-- Font Awesome -->
<link rel="stylesheet" href="{{ URL::asset('assets/bower_components/font-awesome/css/font-awesome.min.css') }}" />
<!-- Ionicons -->
<link rel="stylesheet" href="{{ URL::asset('assets/bower_components/Ionicons/css/ionicons.min.css') }}" />
<!-- Theme style -->
<link rel="stylesheet" href="{{ URL::asset('assets/dist/css/AdminLTE.min.css') }}" />

<link rel="stylesheet" href="{{ URL::asset('assets/dist/css/skins/skin-blue.min.css') }}" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/ozel.css') }}" />


<!-- Select2 -->
<link rel="stylesheet" href="{{ URL::asset('assets/bower_components/select2/dist/css/select2.min.css') }}" />
<!-- DataTables -->
<link rel="stylesheet" href="{{ URL::asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" />

<script src="https://cdn.ckeditor.com/4.11.4/standard/ckeditor.js"></script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ URL::asset('assets/bower_components/sweetalert2/dist/sweetalert2.min.js') }}"></script>
<link rel="stylesheet" href="{{ URL::asset('assets/bower_components/sweetalert2/dist/sweetalert2.min.css') }}" />

<link rel="stylesheet" href="{{ URL::asset('assets/bower_components/jquery/dist/jquery.min.js') }}" />


<!-- Google Font -->
<link rel="stylesheet"
href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">


   <style type="text/css">

     .form-control {
       height: 28PX;
       font-size: 11px;
     }
     .input-group-addon {
       font-size: 11px;
     }
     label {
       /*display: inline-block;*/
       max-width: 100%;
       margin-top: 5px;
       font-weight: 500;
     }

     .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
       font-size: 11px;
       border: 1px solid #d2d6de;
       border-radius: 0;
       padding: 6px 12px;
       height: 28px;
     }
     .select2-container--default .select2-selection--single .select2-selection__arrow {
       font-size: 11px;
       height: 20px;
       right: 3px;
     }
     .select2-container--default .select2-selection--single .select2-selection__rendered {
       color: #444;
       line-height: 20px;
     }
     body {
       font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
       font-size: 11px;
     }
     .sidebar-menu li>a>.pull-right-container {
      margin-top: 0px;
    }
    .treeview-menu>li>a {
     padding: 5px 5px 5px 15px;
     display: block;
     font-size: 11px
    }
    .sidebar-menu>li>a {
      padding: 8px 5px 12px 15px;
      display: block;
      font-size: 14px
    }
    .form-control1 {
      display: block;
      /*width: 100%;*/
      /*height: 34px;*/
      padding: 6px 12px;
      /*font-size: 14px;*/
      line-height: 1.42857143;
      color: #555;
      background-color: #fff;
      background-image: none;
      border: 1px solid #ccc;
      /*border-radius: 4px;*/
      -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
      /*box-shadow: inset 0 1px 1px rgba(0,0,0,.075);*/
      box-shadow: none;
      -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
      -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
      transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    }

   </style>


</head>


<body class="hold-transition skin-blue sidebar-collapse sidebar-mini">
  <div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">

      <!-- Logo -->
      <a href="index2.html" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>Karakuzu</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>KARAKUZU</b> BİLİŞİM</span>
      </a>

      <!-- Header Navbar -->
      <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-bs-toggle="push-menu" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li class="dropdown messages-menu">
              <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                <!-- <i class="fa fa-envelope-o"></i>
                <span class="label label-success">4</span> -->
                <b  id="veriSonucu" ></b>
              </a>

            </li>
            <!-- User Account Menu -->
            <li class="dropdown user user-menu">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                <!-- The user image in the navbar-->
                <img src="dimg/admins/" class="user-image" alt="User Image">
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs">Berkay</span>
              </a>
              <ul class="dropdown-menu">

                <!-- The user image in the menu -->
                <li class="user-header">
                  <img src="dimg/admins/" class="img-circle" alt="User Image">

                  <p>
                    Berkay
                    <small>Yönetici</small>
                  </p>
                </li>

                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="admins.php?adminsUpdate=true&admins_id=ID" class="btn btn-default btn-flat">Profil</a>
                  </div>
                  <div class="pull-right">
                    <a href="logout.php" class="btn btn-default btn-flat">Güvenli Çıkış</a>
                  </div>
                </li>
              </ul>
            </li>


          </ul>
        </div>
      </nav>
    </header>
