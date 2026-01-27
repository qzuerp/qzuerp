{{-- CSS Başlangıç --}}
    <!-- Temel CSS Kütüphaneleri -->
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/bootsrap-5.3.7.css') }}">

    <!-- AdminLTE Tema -->
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/3rd-party/adminlte/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/3rd-party/adminlte/skins/_all-skins.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/3rd-party/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/3rd-party/select2/select2-bootstrap.min.css') }}">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/3rd-party/sweetalert2/sweetalert2.min.css') }}">

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/dataTables-1.13.6.css') }}">

    <!-- iziToast -->
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/izitoast-1.4.0.css') }}">

    <!-- Google Fonts - Preconnect ekleyerek hızlandırma -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=swap">

    <!-- Fontawesome - Preload ile hızlandırma -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"></noscript>

    <!-- Boxicons -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">

    <!-- Flatpickr -->
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/flatpickr.css') }}">
    
    <!-- Quill -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">

    <!-- Ana CSS -->
    <link rel="stylesheet" href="{{ asset('qzuerp-sources/css/main.css') }}">
{{-- CSS Bitiş --}}

{{-- JavaScript Başlangıç --}}
    <!-- jQuery Core -->
    <script src="{{ asset('qzuerp-sources/js/jquery-3.7.1.js') }}" ></script>

    <!-- Bootstrap - Kritik, defer yok -->
    <script src="{{ asset('qzuerp-sources/js/bootstrap-5.3.7.js') }}" defer></script>

    <!-- SweetAlert2 - Kritik, defer yok -->
    <script src="{{ asset('qzuerp-sources/js/3rd-party/sweetalert2/sweetalert2.js') }}" defer></script>

    <!-- DataTables - Kritik -->
    <script src="{{ asset('qzuerp-sources/js/dataTables-1.13.6.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/dataTables-bootstrap-1.13.6.js') }}" defer></script>

    <!-- iziToast -->
    <script src="{{ asset('qzuerp-sources/js/izitoast-1.4.0.js') }}" defer></script>

    <!-- Flatpickr -->
    <script src="{{ asset('qzuerp-sources/js/flatpickr.js') }}" defer></script>   
    <script src="{{ asset('qzuerp-sources/js/flatpickr-tr.js') }}" defer></script>

    <!-- Ana Javascript - Kritik -->
    <script src="{{ asset('qzuerp-sources/js/main.js') }}" defer></script>

    <!-- Defer ile yüklenecek scriptler -->
    <!-- <script src="{{ asset('qzuerp-sources/js/3rd-party/jquery/jquery.inputmask.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/3rd-party/jquery/jquery.inputmask.date.extensions.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/3rd-party/jquery/jquery.inputmask.extensions.js') }}" defer></script> -->
    <!-- <script src="{{ asset('qzuerp-sources/js/3rd-party/jquery/jquery.slimscroll.min.js') }}" defer></script> -->
    <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js" defer></script>
    <script src="{{ asset('qzuerp-sources/js/3rd-party/adminlte/adminlte.min.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/3rd-party/adminlte/demo.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/3rd-party/select2/select2.min.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/3rd-party/dataTables-1.13.6/buttons.bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/3rd-party/exceljs/exceljs.min.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/select2-4.0.13-tr.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/highcharts.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/highcharts-more.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/solid-gauge.js') }}" defer></script>
    <script src="{{ asset('qzuerp-sources/js/accessibility.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js" defer></script>
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
        --border-radius: 8px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Modern Header */
    .modern-header {
        background: linear-gradient(135deg, #3c8dbc 0%, #2c7aaa 100%);
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
        color: #fff;
        font-weight: 700;
        font-size: 1.5rem;
        transition: var(--transition);
    }

    .logo-link:hover {
        opacity: 0.9;
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
        cursor: pointer;
    }

    .sidebar-toggle:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .modern-header .dropdown {
        position: relative;
    }

    .modern-header .dropdown-toggle {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        border-radius: 10px !important;
        padding: 8px 14px !important;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #fff !important;
        font-size: 14px;
        backdrop-filter: blur(10px);
    }

    .modern-header .dropdown-toggle:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .modern-header .dropdown-toggle:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
    }

    .modern-header .dropdown-toggle::after {
        margin-left: 4px;
        font-size: 11px;
        transition: transform 0.3s ease;
        border-top-color: #fff;
    }

    .modern-header .dropdown-toggle[aria-expanded="true"] {
        background: rgba(255, 255, 255, 0.25) !important;
    }

    .modern-header .dropdown-toggle[aria-expanded="true"]::after {
        transform: rotate(180deg);
    }

    .modern-header .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.5);
        transition: var(--transition);
    }

    .modern-header .dropdown-toggle:hover .user-avatar {
        border-color: #fff;
        transform: scale(1.05);
    }

    .modern-header .user-name {
        font-weight: 500;
        font-size: 14px;
        color: #fff;
        letter-spacing: 0.3px;
    }

    .modern-header .dropdown-menu {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0, 0, 0, 0.08);
        min-width: 260px;
        padding: 8px;
        margin: 0;
        overflow: hidden;
        /* animation: headerDropdownFadeIn 0.3s ease; */
    }

    @keyframes headerDropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modern-header .dropdown-menu li {
        list-style: none;
        padding: 0;
        color: #6c757d;
        font-size: 13px;
        text-align: center;
        border-radius: 8px;
        margin:3px 0px;
    }

    .modern-header .dropdown-menu-user {
        min-width: 280px;
        padding: 0;
    }

    .modern-header .dropdown-user-header {
        background: linear-gradient(135deg, #3c8dbc 0%, #2c7aaa 100%);
        padding: 24px 20px !important;
        text-align: center;
        border-radius: 12px 12px 0 0;
        margin: 0;
    }

    .modern-header .dropdown-user-header .user-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.4);
        margin-bottom: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .modern-header .dropdown-user-name {
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 4px;
        letter-spacing: 0.3px;
    }

    .modern-header .dropdown-user-role {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .modern-header .dropdown-item-user {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: var(--text-dark);
        text-decoration: none;
        transition: all 0.2s ease !important;
        font-size: 14px;
        border-radius: 8px;
        margin: 4px 8px;
    }

    .modern-header .dropdown-item-user:hover {
        background: #f0f7ff;
        color: var(--primary-color);
        transform: translateX(4px);
    }

    .modern-header .dropdown-item-user i {
        width: 20px;
        text-align: center;
        font-size: 16px;
        color: #7f8c8d;
        transition: color 0.2s ease;
    }

    .modern-header .dropdown-item-user:hover i {
        color: var(--primary-color);
    }

    .modern-header .dropdown-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #e0e0e0, transparent);
        margin: 8px 16px;
        border: none;
    }

    .modern-header .fa-bell {
        font-size: 18px;
        animation: headerBellRing 2s ease-in-out infinite;
    }

    @keyframes headerBellRing {
        0%, 100% { transform: rotate(0deg); }
        10%, 30% { transform: rotate(-10deg); }
        20%, 40% { transform: rotate(10deg); }
        50% { transform: rotate(0deg); }
    }

    @media (max-width: 768px) {
        .modern-header .user-name {
            display: none !important;
        }
        
        .modern-header .dropdown-menu-user {
            min-width: 240px;
        }

        .modern-header .dropdown-toggle {
            padding: 6px 10px !important;
        }
    }

    @media (max-width: 576px) {
        .header-container {
            padding: 0 1rem;
        }

        .logo-lg {
            display: none !important;
        }
    }

     .notification-dropdown .dropdown-menu {
        border: none;
        border-radius: 12px;
    }
    
    .notification-dropdown .dropdown-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .notification-dropdown .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .notification-dropdown .dropdown-item:last-child {
        border-bottom: none;
    }
    
    .notification-dropdown .badge {
        font-size: 0.65rem;
        padding: 0.25em 0.5em;
        min-width: 18px;
        left:80%;
        top:9px;
    }
    
    .notification-item {
        display: flex;
        gap: 10px;
        align-items: start;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .notification-content {
        flex: 1;
        min-width: 0;
        text-decoration: none;
        text-align: start;
    }
    
    .notification-title {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 2px;
        color: #212529;
    }
    
    .notification-message {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .notification-time {
        font-size: 0.75rem;
        color: #adb5bd;
    }
    
    .notification-unread {
        background-color: #e7f3ff;
    }
    
    .notification-dot {
        width: 8px;
        height: 8px;
        background-color: #0d6efd;
        border-radius: 50%;
        margin-top: 6px;
    }

    /* Scrollbar stil */
    #notiList::-webkit-scrollbar {
        width: 6px;
    }
    
    #notiList::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    #notiList::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    #notiList::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
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

            <div class="d-flex gap-2 align-items-center" style="transform: scale(0.85);">
                @php
                    // Cache kullanarak firma bilgisini optimize et
                    $FIRMA = Cache::remember('firma_' . trim($user->firma), 3600, function() use ($user) {
                        return DB::table('FIRMA_TANIMLARI')->where('FIRMA', trim($user->firma))->first();
                    });
                @endphp

                @if($FIRMA)
                    <img src="{{ asset($FIRMA->LOGO_URL) }}" class="user-avatar" alt="{{ $FIRMA->FIRMA_ADI }}" loading="lazy">
                    <a href="index" class="logo-link">
                        <span class="logo-mini" style="color: #f2f2f2;"><b>{{ $FIRMA->FIRMA_ADI }}</b></span>
                    </a>
                @endif
            </div>

            <div class="d-flex align-items-center" style="gap: 8px;">
                <!-- Bildirim Dropdown -->
                <div class="dropdown notification-dropdown">
                    <button class="btn dropdown-toggle position-relative" type="button" id="notiDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-bell fs-5"></i>
                        <span id="notiCount" class="badge bg-danger position-absolute translate-middle rounded-pill" style="display: none;">0</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="notiDropdown" id="notiList" style="max-width:550px; max-height: 400px; overflow-y: auto;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Bildirimler</span>
                            <a href="#" id="markAllRead" class="text-primary text-decoration-none small">Tümünü Okundu İşaretle</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li id="emptyState" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-bell-slash fs-1 mb-2"></i>
                            <p class="mb-0">Henüz bildiriminiz yok</p>
                        </li>
                    </ul>
                </div>

                <!-- Kullanıcı Dropdown -->
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        @if($FIRMA)
                            <img src="{{ asset($FIRMA->LOGO_URL) }}" class="user-avatar" alt="User" loading="lazy">
                        @else
                            <img src="{{ asset('/qzuerp-sources/img/qzu_logo.png') }}" class="user-avatar" alt="User Image" loading="lazy">
                        @endif
                        <span class="user-name d-none d-sm-block">{{ $user->name }}</span>
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-user dropdown-menu-end" aria-labelledby="userDropdown">
                        <li class="dropdown-user-header">
                            @if($FIRMA)
                                <img src="{{ asset($FIRMA->LOGO_URL) }}" class="user-avatar" alt="User" loading="lazy">
                            @else
                                <img src="{{ asset('/qzuerp-sources/img/qzu_logo.png') }}" class="user-avatar" alt="QZUERP" loading="lazy">
                            @endif
                            <div class="dropdown-user-name">{{ $user->name }}</div>
                            <div class="dropdown-user-role">
                                @if ($user->perm == "ADMIN")
                                    <i class="fa fa-shield"></i> <span>Yönetici</span>
                                @else
                                    <i class="fa fa-user"></i> <span>Kullanıcı</span>
                                @endif
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a href="change_password" class="dropdown-item-user">
                                <i class="fa fa-user"></i>
                                <span>Profil</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item-user" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out"></i>
                                <span>Çıkış</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
</div>

<script>
    (() => {
        'use strict';

        // State yönetimi
        const NotificationState = {
            lastId: 0,
            isPolling: false,
            isFirstLoad: true,
            originalTitle: document.title,
            titleInterval: null,
            pollingTimeout: null,
            POLL_INTERVAL: 60000,
            
            // DOM elementleri cache'le
            elements: {
                notiList: null,
                notiCount: null,
                emptyState: null,
                notiDropdown: null,
                markAllRead: null
            },
            
            init() {
                this.elements.notiList = document.getElementById("notiList");
                this.elements.notiCount = document.getElementById("notiCount");
                this.elements.emptyState = document.getElementById("emptyState");
                this.elements.notiDropdown = document.getElementById("notiDropdown");
                this.elements.markAllRead = document.getElementById("markAllRead");
            }
        };

        // Utility fonksiyonları
        const Utils = {
            updateBadge(count) {
                const { notiCount } = NotificationState.elements;
                if (count > 0) {
                    notiCount.textContent = count > 99 ? '99+' : count;
                    notiCount.style.display = 'inline-block';
                } else {
                    notiCount.style.display = 'none';
                }
            },

            toggleEmptyState() {
                const { notiList, emptyState } = NotificationState.elements;
                const items = notiList.querySelectorAll('.notification-item');
                emptyState.style.display = items.length === 0 ? 'block' : 'none';
            },

            timeAgo(dateString) {
                const seconds = Math.floor((Date.now() - new Date(dateString)) / 1000);
                
                if (seconds < 60) return 'Az önce';
                const minutes = Math.floor(seconds / 60);
                if (minutes < 60) return `${minutes} dakika önce`;
                const hours = Math.floor(minutes / 60);
                if (hours < 24) return `${hours} saat önce`;
                const days = Math.floor(hours / 24);
                if (days < 7) return `${days} gün önce`;
                const weeks = Math.floor(days / 7);
                return weeks < 4 ? `${weeks} hafta önce` : 'Bir ay önce';
            },

            getNotificationIcon(type) {
                const icons = {
                    'info': 'fa-info-circle text-primary',
                    'success': 'fa-check-circle text-success',
                    'warning': 'fa-exclamation-triangle text-warning',
                    'error': 'fa-times-circle text-danger'
                };
                const iconClass = icons[type] || 'fa-bell text-secondary';
                return `<i class="fa-solid ${iconClass}"></i>`;
            },

            // Title animasyonu
            updateTitleNotification(count) {
                const { titleInterval, originalTitle } = NotificationState;
                
                if (titleInterval) {
                    clearInterval(titleInterval);
                    NotificationState.titleInterval = null;
                }
                
                if (count > 0) {
                    let toggle = false;
                    NotificationState.titleInterval = setInterval(() => {
                        toggle = !toggle;
                        document.title = toggle ? `(${count}) Bildirim` : originalTitle;
                    }, 1000);
                } else {
                    document.title = originalTitle;
                }
            },

            stopTitleNotification() {
                if (NotificationState.titleInterval) {
                    clearInterval(NotificationState.titleInterval);
                    NotificationState.titleInterval = null;
                    document.title = NotificationState.originalTitle;
                }
            }
        };

        // API çağrıları
        const NotificationAPI = {
            async poll() {
                if (NotificationState.isPolling) return;
                NotificationState.isPolling = true;

                try {
                    const response = await fetch(`/notifications/poll?lastId=${NotificationState.lastId}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal: AbortSignal.timeout(8000) // 8 saniye timeout
                    });

                    if (!response.ok) throw new Error('Network error');

                    const data = await response.json();

                    if (data.notifications?.length > 0) {
                        NotificationState.lastId = data.lastId;
                        this.renderNotifications(data.notifications);
                        
                        const currentCount = parseInt(NotificationState.elements.notiCount.textContent) || 0;
                        const newTotalCount = currentCount + data.notifications.length;
                        Utils.updateBadge(newTotalCount);
                        Utils.toggleEmptyState();

                        if (!document.hasFocus()) {
                            Utils.updateTitleNotification(newTotalCount);
                        }
                    }

                    NotificationState.isFirstLoad = false;
                    
                } catch (error) {
                    console.error('Bildirim alınamadı:', error);
                } finally {
                    NotificationState.isPolling = false;
                }
            },

            renderNotifications(notifications) {
                const { notiList } = NotificationState.elements;
                const divider = notiList.querySelector('.dropdown-divider');
                const fragment = document.createDocumentFragment();

                notifications.forEach(n => {
                    const li = document.createElement("li");
                    li.className = "dropdown-item notification-item notification-unread";
                    li.dataset.id = n.id;
                    
                    li.innerHTML = `
                        <div class="notification-icon bg-light">
                            ${Utils.getNotificationIcon(n.type || 'default')}
                        </div>
                        <a href='${n.url || '#'}' class="notification-content">
                            <div class="notification-title text-truncate">${n.title}</div>
                            <div class="notification-message text-truncate">${n.message}</div>
                            <div class="notification-time">${Utils.timeAgo(n.created_at)}</div>
                        </a>
                        <div class="notification-dot"></div>
                    `;
                    
                    fragment.appendChild(li);
                });

                divider.after(fragment);
            },

            async markAsRead(ids) {
                try {
                    await fetch('/notifications/mark-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ids })
                    });
                } catch (error) {
                    console.error('Okundu işaretleme hatası:', error);
                }
            }
        };

        // Event handlers
        const EventHandlers = {
            init() {
                // Bildirime tıklama - event delegation
                document.addEventListener('click', (e) => {
                    const item = e.target.closest('.notification-item');
                    if (!item) return;

                    const notificationId = item.dataset.id;
                    item.classList.remove('notification-unread');
                    item.querySelector('.notification-dot')?.remove();
                    
                    const currentCount = parseInt(NotificationState.elements.notiCount.textContent) || 0;
                    if (currentCount > 0) {
                        Utils.updateBadge(currentCount - 1);
                    }
                    
                    NotificationAPI.markAsRead([notificationId]);
                });

                // Tümünü okundu işaretle
                NotificationState.elements.markAllRead?.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const unreadItems = document.querySelectorAll('.notification-item.notification-unread');
                    const ids = Array.from(unreadItems).map(item => item.dataset.id);
                    
                    if (ids.length > 0) {
                        unreadItems.forEach(item => {
                            item.classList.remove('notification-unread');
                            item.querySelector('.notification-dot')?.remove();
                        });
                        
                        Utils.updateBadge(0);
                        Utils.stopTitleNotification();
                        NotificationAPI.markAsRead(ids);
                    }
                });

                // Dropdown açıldığında
                NotificationState.elements.notiDropdown?.addEventListener('click', () => {
                    Utils.stopTitleNotification();
                });

                // Visibility change
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) {
                        Utils.stopTitleNotification();
                        NotificationAPI.poll();
                    }
                });

                // Window focus
                window.addEventListener('focus', () => {
                    Utils.stopTitleNotification();
                });
            }
        };

        // Polling scheduler
        const PollingScheduler = {
            start() {
                this.scheduleNext();
            },

            scheduleNext() {
                if (NotificationState.pollingTimeout) {
                    clearTimeout(NotificationState.pollingTimeout);
                }

                NotificationState.pollingTimeout = setTimeout(async () => {
                    await NotificationAPI.poll();
                    this.scheduleNext();
                }, NotificationState.POLL_INTERVAL);
            },

            stop() {
                if (NotificationState.pollingTimeout) {
                    clearTimeout(NotificationState.pollingTimeout);
                    NotificationState.pollingTimeout = null;
                }
            }
        };

        // Ana initialization
        document.addEventListener('DOMContentLoaded', () => {
            NotificationState.init();
            EventHandlers.init();
            Utils.toggleEmptyState();
            
            // İlk polling
            NotificationAPI.poll();
            PollingScheduler.start();
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            PollingScheduler.stop();
            Utils.stopTitleNotification();
        });

    })();
</script>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>