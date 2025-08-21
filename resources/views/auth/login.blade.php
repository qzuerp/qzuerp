<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Giriş | Karakuzu ERP</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{ URL::asset('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('assets/bower_components/font-awesome/css/font-awesome.min.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('assets/bower_components/Ionicons/css/ionicons.min.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('assets/dist/css/AdminLTE.min.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('assets/dist/css/skins/skin-blue.min.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('assets/css/ozel.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('assets/bower_components/select2/dist/css/select2.min.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" />
  <script src="https://cdn.ckeditor.com/4.11.4/standard/ckeditor.js"></script>
  <link rel="stylesheet" href="{{ URL::asset('assets/bower_components/jquery/dist/jquery.min.js') }}" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

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
      position: relative;
      max-width: 900px;
      width: 100%;
      background: var(--white);
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      display: flex;
      padding: 0px;
    }
    
    .image-side {
      position: relative;
      width: 50%;
      overflow: hidden;
    }
    
    .image-side img {
      position: absolute;
      height: 100%;
      width: 100%;
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
      height: 100%;
      width: 100%;
      background: linear-gradient(180deg, rgba(58, 123, 213, 0.7), rgba(0, 210, 255, 0.7));
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
      text-align: center;
    }
    
    .image-overlay img.logo {
      width: 100px;
      margin-bottom: 30px;
    }
    
    .overlay-text h1 {
      color: var(--white);
      font-size: 28px;
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
      display: inline-block;
    }
    
    .form-header h2:after {
      content: '';
      position: absolute;
      left: 50%;
      bottom: -8px;
      height: 3px;
      width: 40px;
      background: linear-gradient(to right, var(--primary), var(--secondary));
      transform: translateX(-50%);
      border-radius: 10px;
    }
    
    .form-header p {
      color: var(--text-light);
      font-size: 15px;
    }
    
    .input-group {
      position: relative;
      margin-bottom: 24px;
    }
    
    .input-group label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      color: var(--text-dark);
      font-weight: 500;
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
    transition: all 0.3s ease;
  }

  .input-wrapper i.right-icon {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    color: var(--text-light);
    font-size: 18px;
    transition: all 0.3s ease;
  }

    
    .input-group
    {
      width: 100%;
    }
    .input-group input {
      width: 100%;
      height: 54px;
      background: var(--background);
      border: none;
      border-radius: 12px;
      padding: 0 15px 0 45px;
      font-size: 16px;
      color: var(--text-dark);
      transition: all 0.3s ease;
    }
    
    .input-group input:focus {
      outline: none;
      box-shadow: 0 0 0 2px rgba(58, 123, 213, 0.3);
    }
    
    .input-group input:focus + i {
      color: var(--primary);
    }
    
    .remember-forgot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
    }
    
    .remember {
      display: flex;
      align-items: center;
    }
    
    .remember input {
      margin-right: 8px;
      accent-color: var(--primary);
    }
    
    .remember label {
      font-size: 14px;
      color: var(--text-light);
      cursor: pointer;
    }
    
    .forgot-link {
      font-size: 14px;
      color: var(--primary);
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .forgot-link:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }
    
    .submit-btn {
      width: 100%;
      height: 54px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border: none;
      border-radius: 12px;
      color: var(--white);
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(58, 123, 213, 0.3);
    }
    
    .submit-btn:hover {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary));
      transform: translateY(-2px);
      box-shadow: 0 7px 20px rgba(58, 123, 213, 0.4);
    }
    
    .error-message {
      color: var(--error);
      font-size: 13px;
      margin-top: 6px;
    }
    
    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        max-width: 480px;
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

    .custom-checkbox {
      position: relative;
      display: flex;
      align-items: center;
      cursor: pointer;
      font-size: 14px;
      user-select: none;
      color: #767676;
    }

    .custom-checkbox input {
      position: absolute;
      opacity: 0;
      height: 0;
      width: 0;
      cursor: pointer;
    }

    .checkbox-mark {
      position: relative;
      display: inline-block;
      height: 18px;
      width: 18px;
      margin-right: 8px;
      background-color: #f8faff;
      border: 2px solid #ddd;
      border-radius: 4px;
      transition: all 0.3s ease;
    }

    .custom-checkbox:hover input ~ .checkbox-mark {
      border-color: #3a7bd5;
    }

    .custom-checkbox input:checked ~ .checkbox-mark {
      background-color: #3a7bd5;
      border-color: #3a7bd5;
    }

    .checkbox-mark:after {
      content: "";
      position: absolute;
      display: none;
      left: 5px;
      top: 1px;
      width: 5px;
      height: 10px;
      border: solid white;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }

    .custom-checkbox input:checked ~ .checkbox-mark:after {
      display: block;
    }

    .custom-checkbox input:focus ~ .checkbox-mark {
      box-shadow: 0 0 0 3px rgba(58, 123, 213, 0.3);
    }

    .custom-checkbox input:checked ~ .checkbox-mark {
      animation: pulse 0.3s;
    }

    @keyframes pulse {
      0% {
        transform: scale(0.8);
      }
      50% {
        transform: scale(1.1);
      }
      100% {
        transform: scale(1);
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="image-side">
      <img src="{{URL::asset('/assets/img/QZU ERP.png')}}" alt="Login Background">
      <div class="image-overlay">
        <!-- Eğer bir logo varsa buraya ekleyin -->
        <!-- <img class="logo" src="{{URL::asset('/assets/img/qzu_logo.png')}}" alt="Logo"> -->
        <div class="overlay-text">
         
        </div>
      </div>
    </div>
    <div class="form-side">
      <div class="form-header">
        <h2>Giriş Yap</h2>
        <p>Hesabınıza erişmek için bilgilerinizi girin</p>
      </div>
      
      <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="input-group">
          <label for="email">E-posta Adresi</label>
          <div class="input-wrapper">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="ornek@gmail.com">
            <i class="left-icon fas fa-envelope"></i>
          </div>
          @error('email')
            <div class="error-message">
              Hatalı bir e-posta adresi girdiniz!
            </div>
          @enderror
        </div>
        
        <div class="input-group">
          <label for="password">Şifre</label>
          <div class="input-wrapper">
            <input id="passwordInput" type="password" name="password" required placeholder="••••••">
            <i class="left-icon fas fa-lock"></i>
            <i class="right-icon fa-solid fa-eye-slash" id="togglePassword"></i>
          </div>
          @error('password')
            <div class="error-message">
              Şifre hatalı!
            </div>
          @enderror
        </div>
        
        <div class="remember-forgot">
          <div class="remember">
          <label class="custom-checkbox">
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <span class="checkbox-mark"></span>
            Beni Hatırla
          </label>
          </div>
          <!-- Eğer şifre sıfırlama linki eklemek isterseniz -->
          <!-- <a href="{{ route('password.request') }}" class="forgot-link">Şifremi Unuttum</a> -->
        </div>
        
        <button type="submit" class="submit-btn">
          Giriş Yap
        </button>
      </form>
    </div>
  </div>

  <script>
    const toggleIcon = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("passwordInput");

    toggleIcon.addEventListener("click", function () {
      if (!passwordInput) return;

      const isPassword = passwordInput.type === "password";
      passwordInput.type = isPassword ? "text" : "password";
      toggleIcon.classList.toggle("fa-eye");
      toggleIcon.classList.toggle("fa-eye-slash");
    });
  </script>
</body>
</html>