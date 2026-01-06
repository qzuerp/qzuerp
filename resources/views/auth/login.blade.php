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
      background: #0a0e27;
    }

    .login-container {
      display: flex;
      height: 100vh;
      width: 100%;
    }

    /* Sol Taraf - Animasyonlu Görsel */
    .left-side {
      flex: 1;
      position: relative;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .animated-background {
      position: absolute;
      width: 100%;
      height: 100%;
      overflow: hidden;
    }

    .circle {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      animation: float 20s infinite ease-in-out;
    }

    .circle:nth-child(1) {
      width: 300px;
      height: 300px;
      top: 10%;
      left: 10%;
      animation-delay: 0s;
    }

    .circle:nth-child(2) {
      width: 200px;
      height: 200px;
      top: 60%;
      left: 70%;
      animation-delay: 2s;
    }

    .circle:nth-child(3) {
      width: 150px;
      height: 150px;
      top: 30%;
      left: 60%;
      animation-delay: 4s;
    }

    .circle:nth-child(4) {
      width: 100px;
      height: 100px;
      top: 70%;
      left: 20%;
      animation-delay: 6s;
    }

    @keyframes float {
      0%, 100% {
        transform: translateY(0) rotate(0deg);
        opacity: 0.3;
      }
      50% {
        transform: translateY(-50px) rotate(180deg);
        opacity: 0.6;
      }
    }

    .content-overlay {
      position: relative;
      z-index: 10;
      text-align: center;
      color: white;
      padding: 40px;
      max-width: 500px;
    }

    .logo-container {
      margin-bottom: 40px;
    }

    .logo-icon {
      width: 120px;
      height: 120px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 30px;
      backdrop-filter: blur(10px);
      border: 2px solid rgba(255, 255, 255, 0.3);
      animation: pulse 3s infinite;
    }

    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
      }
      50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 20px rgba(255, 255, 255, 0);
      }
    }

    .logo-icon h1 {
      font-size: 70px;
      margin-bottom: 10px;
      color: white;
    }

    .welcome-text h1 {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    }

    .welcome-text p {
      font-size: 18px;
      line-height: 1.6;
      opacity: 0.95;
      text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
    }

    .features {
      display: flex;
      gap: 30px;
      margin-top: 40px;
      justify-content: center;
    }

    .feature-item {
      text-align: center;
    }

    .feature-icon {
      width: 60px;
      height: 60px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
      backdrop-filter: blur(10px);
    }

    .feature-icon i {
      font-size: 24px;
      color: white;
    }

    .feature-item h3 {
      font-size: 16px;
      margin-bottom: 5px;
    }

    .feature-item p {
      font-size: 12px;
      opacity: 0.8;
    }

    /* Sağ Taraf - Login Form */
    .right-side {
      flex: 1;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
      position: relative;
    }

    .form-container {
      width: 100%;
      max-width: 450px;
    }

    .form-header {
      margin-bottom: 40px;
    }

    .form-header h2 {
      font-size: 32px;
      color: #1a1a1a;
      margin-bottom: 10px;
      font-weight: 700;
    }

    .form-header p {
      color: #666;
      font-size: 16px;
    }

    .form-group {
      margin-bottom: 25px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #333;
      font-size: 14px;
      font-weight: 600;
    }

    .input-wrapper {
      position: relative;
    }

    .input-wrapper input {
      width: 100%;
      padding: 15px 20px 15px 50px;
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      font-size: 15px;
      transition: all 0.3s ease;
      background: #f8f9fa;
    }

    .input-wrapper input:focus {
      outline: none;
      border-color: #667eea;
      background: white;
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .input-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #999;
      font-size: 18px;
      pointer-events: none;
    }

    .input-wrapper input:focus ~ .input-icon {
      color: #667eea;
    }

    .toggle-password {
      position: absolute;
      right: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: #999;
      cursor: pointer;
      font-size: 18px;
      transition: color 0.3s;
    }

    .toggle-password:hover {
      color: #667eea;
    }

    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .remember-me {
      display: flex;
      align-items: center;
      cursor: pointer;
    }

    .remember-me input[type="checkbox"] {
      width: 18px;
      height: 18px;
      margin-right: 8px;
      cursor: pointer;
      accent-color: #667eea;
    }

    .remember-me label {
      font-size: 14px;
      color: #666;
      cursor: pointer;
    }

    .forgot-password {
      color: #667eea;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      transition: color 0.3s;
    }

    .forgot-password:hover {
      color: #764ba2;
    }

    .login-button {
      width: 100%;
      padding: 16px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .login-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }

    .login-button:active {
      transform: translateY(0);
    }

    .login-button:disabled {
      background: linear-gradient(135deg, #ccc 0%, #999 100%);
      cursor: not-allowed;
      box-shadow: none;
      transform: none;
    }

    .error-message {
      color: #e74c3c;
      font-size: 13px;
      margin-top: 8px;
    }

    @media (max-width: 968px) {
      .login-container {
        flex-direction: column;
      }

      .left-side {
        min-height: 300px;
      }

      .welcome-text h1 {
        font-size: 36px;
      }

      .features {
        flex-direction: column;
        gap: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <!-- Sol Taraf -->
    <div class="left-side">
      <div class="animated-background">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
      </div>
      
      <div class="content-overlay">
        <div class="logo-container">
          <div class="logo-icon">
            <h1>Q</h1>
          </div>
        </div>
        
        <div class="welcome-text">
          <h1>QZU ERP</h1>
          <p>İşletmenizi dijital dünyaya taşıyın. Tüm operasyonlarınızı tek platformdan yönetin.</p>
        </div>

        <div class="features">
          <div class="feature-item">
            <div class="feature-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Güvenli</h3>
            <p>256-bit şifreleme</p>
          </div>
          <div class="feature-item">
            <div class="feature-icon">
              <i class="fas fa-bolt"></i>
            </div>
            <h3>Hızlı</h3>
            <p>Gerçek zamanlı veri</p>
          </div>
          <div class="feature-item">
            <div class="feature-icon">
              <i class="fas fa-users"></i>
            </div>
            <h3>Ekip Çalışması</h3>
            <p>Kolay işbirliği</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Sağ Taraf -->
    <div class="right-side">
      <div class="form-container">
        <div class="form-header">
          <h2>Hoş Geldiniz</h2>
          <p>Hesabınıza giriş yapın ve işinize devam edin</p>
        </div>

        <form method="POST" action="{{ route('login') }}" id="loginForm">
          @csrf
          
          <div class="form-group">
            <label for="email">E-posta Adresi</label>
            <div class="input-wrapper">
              <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="ornek@karakuzu.com">
              <i class="fas fa-envelope input-icon"></i>
            </div>
            @error('email')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="password">Şifre</label>
            <div class="input-wrapper">
              <input type="password" id="password" name="password" required placeholder="••••••••">
              <i class="fas fa-lock input-icon"></i>
              <i class="fas fa-eye toggle-password" id="togglePassword"></i>
            </div>
            @error('password')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-options">
            <div class="remember-me">
              <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <label for="remember">Beni Hatırla</label>
            </div>
            <a href="{{ route('password.request') }}" class="forgot-password">Şifremi Unuttum?</a>
          </div>

          <button type="submit" class="login-button" id="loginBtn">
            Giriş Yap
          </button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });

    // Form submit handling
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');

    loginForm.addEventListener('submit', function() {
      loginBtn.disabled = true;
      loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Giriş Yapılıyor...';
    });
  </script>
</body>
</html>