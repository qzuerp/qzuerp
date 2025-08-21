  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

   <!-- sidebar: style can be found in sidebar.less -->
   <section class="sidebar">

     <!-- Sidebar user panel (optional) -->
     <div class="user-panel">
       <div class="pull-left image">
         <img src="{{URL::asset('/assets/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image">
       </div>
       <div class="pull-left info">
         <p>Berkay</p>
         <!-- Status -->
         <a href="#"> Yönetici</a>
       </div>
     </div>



     <!-- Sidebar Menu -->
     <ul class="sidebar-menu" data-widget="tree">
       <li class="header">Menüler</li>
       <!-- Optionally, you can add icons to the links -->
       <li><a href="index.php"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
       <li><a href="staffs.php"><i class="fa fa-user"></i> <span>Personeller</span></a></li>
       <!-- <li><a href="news.php"><i class="fa fa-file"></i> <span>Duyurular</span></a></li> -->

       <li class=" treeview">
         <a href="#">
           <i class="fa fa-dashboard"></i> <span >Evraklar</span>
           <span class="pull-right-container">
             <!-- <span class="label label-primary pull-right">5</span> -->

             <i class="fa fa-angle-left pull-right"></i>
           </span>
         </a>

         <ul class="treeview-menu">
             <li ><a href="evrak_urunagaci.php"><i class="fa fa-circle-o"></i> Ürün Ağacı</a></li>



         </ul>
         <ul class="sidebar-menu" data-widget="tree">
         </ul>
       </li>


       <li class=" treeview">
         <a href="#">
           <i class="fa fa-dashboard"></i> <span >Kartlar</span>
           <span class="pull-right-container">
             <!-- <span class="label label-primary pull-right">5</span> -->

             <i class="fa fa-angle-left pull-right"></i>
           </span>
         </a>

         <ul class="treeview-menu">
           <li><a href="kart_cari"><i class="fa fa-circle-o"></i> Cari Kartı </a></li>
           <li><a href="kart_stok"><i class="fa fa-circle-o"></i> Stok Kartı </a></li>
           <li><a href="kart_personel"><i class="fa fa-circle-o"></i> Personel Kartı</a></li>
           <li><a href="kart_depo"><i class="fa fa-circle-o"></i> Depo Kartı</a></li>
           <li><a href="kart_tezgah"><i class="fa fa-circle-o"></i> Tezgah Kartı </a></li>

           <li><a href="kart_operasyon"><i class="fa fa-circle-o"></i> Operasyon Kartı</a></li>
           <li><a href="dys"><i class="fa fa-circle-o"></i> Döküman Yönetimi </a></li>  
         </ul>
         <ul class="sidebar-menu" data-widget="tree">
         </ul>
       </li>
       <li class=" treeview">
         <a href="#">
           <i class="fa fa-circle-o"></i> <span>Evraklar</span>
           <span class="pull-right-container">
           <span class="label label-primary pull-right">3</span>
           </span>
         </a>

         <ul class="treeview-menu">
           <li><a href="satinalmairsaliyesi"><i class="fa fa-circle-o"></i>Satınalma İrsaliyesi ile Giriş</a></li>
           <li><a href="sevkirsaliyesi"><i class="fa fa-circle-o"></i> Sevk İrsaliyesi</a></li>
           <li><a href="satinalmasiparisi"><i class="fa fa-circle-o"></i> Satın Alma Siparişi</a></li>
           <li><a href="satissiparisi"><i class="fa fa-circle-o"></i> Satış Siparişi</a></li>
           <li><a href="urunagaci"><i class="fa fa-circle-o"></i> Ürün Ağacı </a></li>
         </ul>
       </li>
       <li class="treeview">
         <a href="#"><i class="fa fa-key"></i> <span> Kullanıcı İşlemleri</span>
           <span class="pull-right-container">
             <i class="fa fa-angle-left pull-right"></i>
           </span>
         </a>
         <ul class="treeview-menu">

           <li><a href="admins.php"><i class="fa fa-user"></i> Kullanıcılar</a></li>
         </ul>
       </li>


       <li class=" treeview">
         <a href="#"><i class="fa fa-key"></i> <span> Yönetim</span>
           <span class="pull-right-container">
             <i class="fa fa-angle-left pull-right"></i>
           </span>
         </a>
         <ul class="treeview-menu">
          <li><a href="settings.php"><i class="fa fa-cog"></i> Ayarlar</a></li>

        </ul>
      </li>
    </ul>
    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
 </aside>
