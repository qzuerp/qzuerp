
{{-- CSS Başlangıç --}}
    <!-- Temel CSS Kütüphaneleri -->
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/bootsrap-5.3.7.css') }}">

    <!-- AdminLTE Tema -->
    <link rel="stylesheet" href="{{ URL::asset('qzuerp-sources/css/3rd-party/adminlte/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('qzuerp-sources/css/3rd-party/adminlte/skins/_all-skins.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ URL::asset('qzuerp-sources/css/3rd-party/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('qzuerp-sources/css/3rd-party/select2/select2-bootstrap.min.css') }}">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ URL::asset('qzuerp-sources/css/3rd-party/sweetalert2/sweetalert2.min.css') }}">

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/dataTables-1.13.6.css') }}">

    <!-- iziToast -->
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/izitoast-1.4.0.css') }}">

    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    
    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />      <!-- Ana CSS -->
    
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ URL::asset('qzuerp-sources/css/flatpickr.css') }}">

    <link rel="stylesheet" href="{{ URL::asset('qzuerp-sources/css/main.css') }}">
{{-- CSS Bitiş --}}

{{-- JavaScript Başlangıç --}}

    <!-- jQuery Core (3.6.0) -->
    <script src="{{ asset('qzuerp-sources/js/jquery-3.7.1.js') }}"></script>

    <!-- jQuery input mask eklentisi -->
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/jquery/jquery.inputmask.js') }}"></script>
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/jquery/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/jquery/jquery.inputmask.extensions.js') }}"></script>
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/jquery/jquery.slimscroll.min.js') }}"></script>

    <!-- Bootstrap -->
    <script src="{{ URL::asset('qzuerp-sources/js/bootstrap-5.3.7.js') }}"></script>

    <!-- AdminLTE -->
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/adminlte/adminlte.min.js') }}"></script>
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/adminlte/demo.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/select2/select2.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/sweetalert2/sweetalert2.js') }}"></script>

    <!-- DataTables ve Eklentileri -->
    <script src="{{ asset('qzuerp-sources/js/dataTables-1.13.6.js') }}"></script>
    <script src="{{ asset('qzuerp-sources/js/dataTables-bootstrap-1.13.6.js') }}"></script>
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/dataTables-1.13.6/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/dataTables-1.13.6/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/dataTables-1.13.6/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/dataTables-1.13.6/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/dataTables-1.13.6/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/dataTables-1.13.6/pdfmake.min.js') }}"></script>


    <!-- Yardımcı Kütüphaneler -->
    <script src="{{ URL::asset('qzuerp-sources/js/3rd-party/exceljs/exceljs.min.js') }}"></script>

    <!-- iziToast -->
    <script src="{{ asset('qzuerp-sources/js/izitoast-1.4.0.js') }}"></script>

    <script src="{{ asset('qzuerp-sources/js/select2-4.0.13-tr.js') }}"></script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script src="{{ asset('qzuerp-sources/js/flatpickr.js') }}"></script>   
    <script src="{{ asset('qzuerp-sources/js/flatpickr-tr.js') }}"></script>
    <!-- Ana Javascript -->
    <script src="{{ URL::asset('qzuerp-sources/js/main.js') }}"></script>
{{-- JS Bitiş --}}

<style>
    :root {
        --primary-color: #3c8dbc;
        --primary-dark: #2c7aaa;
        --secondary-color: #f8f9fa;
        --text-dark: #2c3e50;
        --border-light: #e9ecef;
        --shadow-light: 0 2px 10px rgba(0,0,0,0.08);
        --shadow-medium: 0 4px 20px rgba(0,0,0,0.12);
        --border-radius: 12px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Modern Header */
    .modern-header {
        background:linear-gradient(135deg, #3c8dbc 0%, #2c7aaa 100%);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow-light);
        height: 50px;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1050;
    }

    .header-container {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 100%;
        padding: 0 1.5rem;
        margin: 0;
    }

    .logo-section {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .logo-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: var(--text-dark);
        font-weight: 700;
        font-size: 1.5rem;
        transition: var(--transition);
    }

    .logo-link:hover {
        color: var(--primary-color);
        text-decoration: none;
    }

    .sidebar-toggle {
        background: none;
        border: none;
        color: #fff;
        font-size: 1.5rem;
        padding: 0.5rem;
        border-radius: 8px;
        transition: var(--transition);
    }

    .sidebar-toggle:hover {
        background:rgba(44, 62, 80, 0.12);
    }

    .user-dropdown {
        position: relative;
    }

    .user-toggle {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: none;
        border: none;
        padding: 0.4rem 0.9rem;
        border-radius: var(--border-radius);
        transition: var(--transition);
        color: #fff;
        text-decoration: none;
    }

    .user-toggle:hover {
        background:rgba(44, 62, 80, 0.12);
        transform: translateY(-1px);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid var(--border-light);
        transition: var(--transition);
        object-fit: cover;
    }

    .user-toggle:hover .user-avatar {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(60, 141, 188, 0.3);
    }

    .user-name {
        font-weight: 600;
        font-size: 0.9rem;
        margin: 0;
    }

    .dropdown-icon {
        font-size: 0.8rem;
        transition: var(--transition);
    }

    .user-dropdown.show .dropdown-icon {
        transform: rotate(180deg);
    }

    .user-dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        min-width: 320px;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-medium);
        border: 1px solid var(--border-light);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px) scale(0.95);
        transition: var(--transition);
        z-index: 1060;
        margin-top: 0.5rem;
    }

    .user-dropdown.show .user-dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }

    .dropdown-user-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        padding: 2rem;
        text-align: center;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        position: relative;
        overflow: hidden;
    }

    .dropdown-user-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.3);
        margin-bottom: 1rem;
        transition: var(--transition);
    }

    .dropdown-user-name {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .dropdown-user-role {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .dropdown-user-footer {
        padding: 1.5rem;
        background: #fafbfc;
        border-radius: 0 0 var(--border-radius) var(--border-radius);
    }

    .footer-btn {
        width: 100%;
        padding: 0.75rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .footer-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .btn-profile {
        background: var(--primary-color);
        border: 1px solid var(--primary-color);
        color: white;
    }

    .btn-logout {
        background: #dc3545;
        border: 1px solid #dc3545;
        color: white;
    }

    @media (max-width: 768px) {
        .user-name { display: none; }
        .user-dropdown-menu { 
            right: 0; 
            left: auto; 
            min-width: 280px; 
            margin-top: 0.5rem;
        }
        .header-container {
            padding: 0 1rem;
        }
    }
</style>

<div class="wrapper">
    <header class="modern-header">
        <div class="header-container">
            <div class="logo-section">
                <button class="sidebar-toggle" id="toggle-btn-sidebar">
                    <i id="icon" class='bx bx-menu'></i>
                </button>
                
                <a href="index" class="logo-link">
                    <span class="logo-mini" style="color: #f2f2f2;"><b>QZU</b></span>
                    <span class="logo-lg d-none d-md-inline"><b>ERP</b></span>
                </a>
            </div>

            <div class="user-dropdown" id="userDropdown">
                <a href="#" class="user-toggle" id="userDropdownToggle">
                    @if(trim($user->firma) == 'yukselcnc')
                        <img src="{{URL::asset('/assets/img/yukselcnc_LOGO.jpeg')}}" class="user-avatar" alt="User yuksel">
                    @else
                        <img src="{{URL::asset('/qzuerp-sources/img/qzu_logo.png')}}" class="user-avatar" alt="User Image">
                    @endif
                    <span class="user-name d-none d-sm-block">{{ $user->name }}</span>
                    <i class="fa fa-angle-down dropdown-icon"></i>
                </a>
                
                <div class="user-dropdown-menu" id="userDropdownMenu">
                    <div class="dropdown-user-header">
                        @if(trim($user->firma) == 'yukselcnc')
                            <img src="{{URL::asset('/assets/img/yukselcnc_LOGO.jpeg')}}" class="dropdown-user-avatar" alt="User Image">
                        @else
                            <img src="{{URL::asset('/qzuerp-sources/img/qzu_logo.png')}}" class="dropdown-user-avatar" alt="User Image">
                        @endif
                        <div class="dropdown-user-name">{{ $user->name }}</div>
                        <div class="dropdown-user-role">
                            @if ($user->perm == "ADMIN")
                                <i class="fa fa-shield"></i> Yönetici
                            @else
                                <i class="fa fa-user"></i> Kullanıcı
                            @endif
                        </div>
                    </div>
                    
                    <div class="dropdown-user-footer">
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="change_password" class="footer-btn btn-profile">
                                    <i class="fa fa-user"></i> {{ __('Profil') }}
                                </a>
                            </div>
                            <div class="col-6">
                                <a class="footer-btn btn-logout" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out"></i> {{ __('Çıkış') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    var dropdownToggle = document.getElementById('userDropdownToggle');
    var dropdown = document.querySelector('.user-dropdown');
    
    if (dropdownToggle && dropdown) {
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
        
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        });
    }
    });
</script>