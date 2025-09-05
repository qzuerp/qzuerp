<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Giriş | Karakuzu ERP</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    :root {
      --primary: #3a7bd5;
      --primary-dark: #2a5db0;
      --secondary: #00d2ff;
      --text-dark: #333;
      --text-light: #767676;
      --white: #fff;
      --background: #f8faff;
      --error: #e74c3c;
      --success: #2ecc71;
    }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      padding: 20px;
    }

    .container {
      max-width: 900px;
      width: 100%;
      background: var(--white);
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
      display: flex;
      overflow: hidden;
    }
    
    .input-group input:valid {
      box-shadow: 0 0 0 3px rgba(46, 204, 112, 0.4);
    }

    .image-side {
      width: 50%;
      position: relative;
      overflow: hidden;
    }

    .image-side img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .image-side img:hover {
      transform: scale(1.05);
    }

    .image-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(180deg, rgba(58, 123, 213, 0.75), rgba(0, 210, 255, 0.75));
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
      text-align: center;
    }

    .image-overlay img.logo {
      width: 120px;
      margin-bottom: 20px;
    }

    .overlay-text h1 {
      color: var(--white);
      font-size: 30px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .overlay-text p {
      color: var(--white);
      font-size: 16px;
      font-weight: 400;
    }

    .form-side {
      width: 50%;
      padding: 50px 40px;
      display: flex;
      flex-direction: column;
    }

    .form-header {
      margin-bottom: 40px;
      text-align: center;
    }

    .form-header h2 {
      font-size: 28px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 8px;
      position: relative;
    }

    .form-header h2:after {
      content: '';
      position: absolute;
      left: 50%;
      bottom: -8px;
      height: 3px;
      width: 50px;
      background: linear-gradient(to right, var(--primary), var(--secondary));
      transform: translateX(-50%);
      border-radius: 10px;
    }

    .form-header p {
      color: var(--text-light);
      font-size: 14px;
    }

    .input-group {
      margin-bottom: 24px;
    }

    .input-group label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .input-wrapper {
      position: relative;
    }

    .input-wrapper i.left-icon {
      position: absolute;
      top: 50%;
      left: 15px;
      transform: translateY(-50%);
      color: var(--text-light);
      font-size: 18px;
      transition: color 0.3s ease;
    }

    .input-wrapper i.right-icon {
      position: absolute;
      top: 50%;
      right: 15px;
      transform: translateY(-50%);
      color: var(--text-light);
      font-size: 18px;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .input-group input {
      width: 100%;
      height: 50px;
      padding: 0 45px 0 45px;
      border: none;
      border-radius: 12px;
      background: var(--background);
      font-size: 15px;
      color: var(--text-dark);
      transition: box-shadow 0.3s ease;
    }

    .input-group input:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(58, 123, 213, 0.2);
    }

    .input-group input:focus + i {
      color: var(--primary);
    }

    .error-message {
      color: var(--error);
      font-size: 13px;
      margin-top: 6px;
    }

    .remember-forgot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      font-size: 13px;
    }

    .custom-checkbox {
      display: flex;
      align-items: center;
      cursor: pointer;
    }

    .custom-checkbox input {
      display: none;
    }

    .checkbox-mark {
      width: 18px;
      height: 18px;
      border: 2px solid #d1d5db;
      border-radius: 4px;
      margin-right: 8px;
      position: relative;
      transition: all 0.3s ease;
    }

    .custom-checkbox input:checked + .checkbox-mark {
      background: var(--primary);
      border-color: var(--primary);
    }

    .checkbox-mark:after {
      content: '';
      position: absolute;
      left: 5px;
      top: 1px;
      width: 5px;
      height: 10px;
      border: solid white;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
      display: none;
    }

    .custom-checkbox input:checked + .checkbox-mark:after {
      display: block;
    }

    .forgot-link {
      color: var(--primary);
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .forgot-link:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    .submit-btn {
      width: 100%;
      height: 50px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border: none;
      border-radius: 12px;
      color: var(--white);
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.1s ease;
      box-shadow: 0 5px 15px rgba(58, 123, 213, 0.3);
    }

    .submit-btn:hover {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary));
      transform: translateY(-2px);
      box-shadow: 0 7px 20px rgba(58, 123, 213, 0.4);
    }

    .submit-btn:disabled {
      background:rgb(165, 184, 213);
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        max-width: 450px;
      }

      .image-side, .form-side {
        width: 100%;
      }

      .image-side {
        height: 200px;
      }

      .form-side {
        padding: 40px 30px;
      }
    }

    @keyframes pulse {
      0% { transform: scale(0.8); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="image-side">
      <img src="{{URL::asset('/assets/img/QZU ERP.png')}}" alt="Login Background">
      <div class="image-overlay">

      </div>
    </div>
    <div class="form-side">
      <div class="form-header">
        <h2>Giriş Yap</h2>
        <p>Hesabına erişmek için bilgilerini gir aga!</p>
      </div>

      <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <div class="input-group">
          <label for="email">E-posta Adresi</label>
          <div class="input-wrapper">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="ornek@gmail.com">
            <i class="left-icon fas fa-envelope"></i>
          </div>
          @error('email')
            <div class="error-message">Hatalı e-posta adresi girdin!</div>
          @enderror
        </div>

        <div class="input-group">
          <label for="password">Şifre</label>
          <div class="input-wrapper">
            <input id="passwordInput" type="password" name="password" required placeholder="••••••">
            <i class="left-icon fas fa-lock"></i>
            <i class="right-icon fas fa-eye-slash" id="togglePassword"></i>
          </div>
          @error('password')
            <div class="error-message">Şifre yanlış aga!</div>
          @enderror
        </div>

        <div class="remember-forgot">
          <label class="custom-checkbox">
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <span class="checkbox-mark"></span>
            Beni Hatırla
          </label>
          <a href="{{ route('password.request') }}" class="forgot-link">Şifremi Unuttum</a>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">Giriş Yap</button>
      </form>
    </div>
  </div>

  <script>
    const toggleIcon = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("passwordInput");
    const submitBtn = document.getElementById("submitBtn");
    const loginForm = document.getElementById("loginForm");

    toggleIcon.addEventListener("click", () => {
      const isPassword = passwordInput.type === "password";
      passwordInput.type = isPassword ? "text" : "password";
      toggleIcon.classList.toggle("fa-eye");
      toggleIcon.classList.toggle("fa-eye-slash");
    });

    loginForm.addEventListener("submit", () => {
      submitBtn.disabled = true;
      submitBtn.textContent = "Giriş Yapılıyor...";
    });
  </script>
</body>
</html>