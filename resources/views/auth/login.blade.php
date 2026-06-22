@php
  DB::statement("UPDATE users SET is_logged_in = 0 WHERE last_activity < DATEADD(hour, -4, GETDATE())");
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Giriş | Karakuzu ERP</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
      overflow: hidden;
      background: #f0f2f8;
      display: flex;
      align-items: stretch;
    }

    .login-container {
      display: flex;
      width: 100%;
      height: 100vh;
      border-radius: 0;
      overflow: hidden;
    }

    /* ── Sol Panel ── */
    .left-side {
      flex: 0.9;
      position: relative;
      overflow: hidden;
      background: #2d2450;
    }

    .video-wrap {
      position: absolute;
      inset: 0;
      z-index: 1;
    }

    .background-video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0;
      transition: opacity 1.6s cubic-bezier(.4, 0, .2, 1);
    }

    .background-video.ready {
      opacity: 1;
    }

    .video-overlay {
      position: absolute;
      inset: 0;
      z-index: 2;
      background: linear-gradient(
        160deg,
        rgba(30, 20, 70, 0.55) 0%,
        rgba(80, 60, 180, 0.28) 60%,
        rgba(0, 0, 0, 0.38) 100%
      );
    }

    .left-content {
      position: relative;
      z-index: 3;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 36px 36px 44px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: auto;
      padding-top: 36px;
    }

    .brand-dot {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.18);
      backdrop-filter: blur(8px);
      border: 1.5px solid rgba(255, 255, 255, 0.22);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .brand-dot i {
      font-size: 16px;
      color: #fff;
    }

    .brand-name {
      font-size: 17px;
      font-weight: 600;
      color: #fff;
      letter-spacing: 0.04em;
      opacity: 0.92;
    }

    .left-tag {
      display: inline-block;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.18);
      border-radius: 20px;
      color: rgba(255, 255, 255, 0.82);
      font-size: 12px;
      padding: 5px 14px;
      margin-bottom: 16px;
      backdrop-filter: blur(6px);
    }

    .left-content h2 {
      font-size: 28px;
      font-weight: 700;
      color: #fff;
      line-height: 1.35;
      margin-bottom: 10px;
      text-shadow: 0 2px 12px rgba(0, 0, 0, 0.18);
    }

    .left-content > p {
      font-size: 14px;
      color: rgba(255, 255, 255, 0.72);
      line-height: 1.6;
      max-width: 280px;
    }

    .pills {
      display: flex;
      gap: 10px;
      margin-top: 22px;
      flex-wrap: wrap;
    }

    .pill {
      display: flex;
      align-items: center;
      gap: 6px;
      background: rgba(255, 255, 255, 0.10);
      border: 1px solid rgba(255, 255, 255, 0.16);
      border-radius: 30px;
      padding: 6px 14px;
      backdrop-filter: blur(6px);
    }

    .pill i {
      font-size: 12px;
      color: #a0c4ff;
    }

    .pill span {
      font-size: 12px;
      color: rgba(255, 255, 255, 0.80);
    }

    /* ── Sağ Panel ── */
    .right-side {
      flex: 1;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 44px 40px;
    }

    .form-container {
      width: 100%;
      max-width: 500px;
    }

    .form-hello {
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.12em;
      color: #9b93c4;
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    .form-container h1 {
      font-size: 26px;
      font-weight: 700;
      color: #1e1b3a;
      margin-bottom: 6px;
    }

    .form-sub {
      font-size: 14px;
      color: #9492a6;
      margin-bottom: 36px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      font-size: 12.5px;
      font-weight: 600;
      color: #5f5b7a;
      margin-bottom: 7px;
      letter-spacing: 0.02em;
    }

    .input-wrapper {
      position: relative;
    }

    .input-wrapper input {
      width: 100%;
      padding: 13px 44px 13px 44px;
      border: 1.5px solid #e8e5f4;
      border-radius: 12px;
      font-size: 14.5px;
      color: #1e1b3a;
      background: #fafafe;
      transition: border 0.2s, box-shadow 0.2s, background 0.2s;
      outline: none;
    }

    .input-wrapper input::placeholder {
      color: #bbb8d0;
    }

    .input-wrapper input:focus {
      border-color: #7c6fce;
      background: #fff;
      box-shadow: 0 0 0 4px rgba(124, 111, 206, 0.10);
    }

    .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #bbb8d0;
      font-size: 16px;
      pointer-events: none;
      transition: color 0.2s;
    }

    .input-wrapper input:focus ~ .input-icon {
      color: #7c6fce;
    }

    .toggle-password {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #bbb8d0;
      cursor: pointer;
      font-size: 16px;
      transition: color 0.2s;
      background: none;
      border: none;
      padding: 2px;
      display: flex;
      align-items: center;
    }

    .toggle-password:hover {
      color: #7c6fce;
    }

    .error-message {
      font-size: 12.5px;
      color: #c0392b;
      margin-top: 6px;
    }

    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 26px;
      margin-top: -4px;
    }

    .remember-me {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
    }

    .remember-me input[type="checkbox"] {
      width: 15px;
      height: 15px;
      accent-color: #7c6fce;
      cursor: pointer;
    }

    .remember-me label {
      font-size: 13px;
      color: #7e7a99;
      cursor: pointer;
    }

    .forgot-password {
      font-size: 13px;
      color: #7c6fce;
      font-weight: 600;
      text-decoration: none;
      transition: color 0.2s;
    }

    .forgot-password:hover {
      color: #5044a0;
    }

    .login-button {
      width: 100%;
      padding: 14px;
      background: #7c6fce;
      color: #fff;
      border: none;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      box-shadow: 0 4px 18px rgba(124, 111, 206, 0.22);
      letter-spacing: 0.02em;
    }

    .login-button:hover {
      background: #6a5cbd;
      box-shadow: 0 6px 24px rgba(124, 111, 206, 0.32);
      transform: translateY(-1px);
    }

    .login-button:active {
      transform: translateY(0);
    }

    .login-button:disabled {
      background: #b0aad8;
      cursor: not-allowed;
      box-shadow: none;
      transform: none;
    }

    .form-bottom {
      margin-top: 32px;
      text-align: center;
      font-size: 12.5px;
      color: #b0adc4;
    }

    .form-bottom a {
      color: #7c6fce;
      font-weight: 600;
      text-decoration: none;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
      }

      .left-side {
        flex: none;
        min-height: 220px;
      }

      .left-content h2 {
        font-size: 22px;
      }

      .pills {
        display: none;
      }

      .right-side {
        padding: 32px 24px;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">

    {{-- Sol Panel --}}
    <div class="left-side">
      <div class="video-wrap">
        <video
          id="bgVideo"
          src="{{ asset('/qzuerp-sources/video/login.mp4') }}"
          autoplay
          loop
          muted
          playsinline
          class="background-video"
        ></video>
      </div>
      <div class="video-overlay"></div>
      <div class="left-content">
        <div class="brand">
          <div class="brand-dot">
            <i class="fas fa-layer-group"></i>
          </div>
          <span class="brand-name">QZU ERP</span>
        </div>
        <h2>Güçlü yönetim, tek platformda</h2>
        <p>İş süreçlerinizi anlık takip edin, raporlayın ve optimize edin.</p>
        <div class="pills">
          <div class="pill">
            <i class="fas fa-shield-halved"></i>
            <span>Güvenli bağlantı</span>
          </div>
          <div class="pill">
            <i class="fas fa-clock"></i>
            <span>7/24 erişim</span>
          </div>
          <div class="pill">
            <i class="fas fa-th-large"></i>
            <span>Modüler yapı</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Sağ Panel --}}
    <div class="right-side">
      <div class="form-container">
        <div class="form-hello">Hoş geldiniz</div>
        <h1>Giriş Yapın</h1>
        <p class="form-sub">Hesabınıza erişmek için bilgilerinizi girin</p>

        <form method="POST" action="{{ route('login') }}" id="loginForm">
          @csrf

          {{-- E-posta --}}
          <div class="form-group">
            <label for="email">E-posta adresi</label>
            <div class="input-wrapper">
              <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                placeholder="ornek@karakuzu.info"
              >
              <i class="fas fa-envelope input-icon"></i>
            </div>
            @error('email')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>

          {{-- Şifre --}}
          <div class="form-group">
            <label for="password">Şifre</label>
            <div class="input-wrapper">
              <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
              >
              <i class="fas fa-lock input-icon"></i>
              <button type="button" class="toggle-password" id="togglePassword" aria-label="Şifreyi göster/gizle">
                <i class="fas fa-eye" id="eyeIcon"></i>
              </button>
            </div>
            @error('password')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>

          {{-- Seçenekler --}}
          <div class="form-options">
            <div class="remember-me">
              <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <label for="remember">Beni hatırla</label>
            </div>
          </div>

          <button type="submit" class="login-button" id="loginBtn">
            Giriş Yap
          </button>
        </form>

        <div class="form-bottom">
          Sorun mu yaşıyorsunuz? <a href="mailto:destek@karakuzu.info">Destek alın</a>
        </div>
      </div>
    </div>

  </div>

  <script>
    // Video: yüklenince yumuşakça belir
    const bgVideo = document.getElementById('bgVideo');
    bgVideo.addEventListener('canplaythrough', function () {
      setTimeout(function () {
        bgVideo.classList.add('ready');
      }, 200);
    }, { once: true });

    // Şifre göster/gizle
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput  = document.getElementById('password');
    const eyeIcon        = document.getElementById('eyeIcon');
    let passwordVisible  = false;

    togglePassword.addEventListener('click', function () {
      passwordVisible = !passwordVisible;
      passwordInput.setAttribute('type', passwordVisible ? 'text' : 'password');
      eyeIcon.classList.toggle('fa-eye',      !passwordVisible);
      eyeIcon.classList.toggle('fa-eye-slash', passwordVisible);
    });

    // Form gönderiminde butonu devre dışı bırak
    const loginForm = document.getElementById('loginForm');
    const loginBtn  = document.getElementById('loginBtn');

    loginForm.addEventListener('submit', function () {
      loginBtn.disabled    = true;
      loginBtn.innerHTML   = '<i class="fas fa-spinner fa-spin"></i> Giriş yapılıyor…';
    });
  </script>
</body>
</html>