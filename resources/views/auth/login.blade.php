<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IFMMS — Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .login-bg {
      background: linear-gradient(135deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.4)),
                 url('/images/login-bg.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: scroll; /* Changed from fixed for better mobile performance */
      background-repeat: no-repeat;
      min-height: 100vh;
      image-rendering: -webkit-optimize-contrast;
    }

    .glass-effect {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .input-focus {
      transition: all 0.3s ease;
    }

    .input-focus:focus {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .btn-hover {
      transition: all 0.3s ease;
      background: linear-gradient(135deg, #2563eb, #1d4ed8);
    }

    .btn-hover:hover {
      background: linear-gradient(135deg, #1d4ed8, #1e40af);
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }

    .fade-in {
      animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .logo-glow {
      text-shadow: 0 0 30px rgba(37, 99, 235, 0.3);
    }

    .logo-container {
      animation: logoPulse 2s infinite;
    }

    @keyframes logoPulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
  </style>
</head>
<body class="min-h-screen login-bg flex items-center justify-center p-4">
  <div class="w-full max-w-md px-6 py-8 glass-effect rounded-3xl shadow-2xl fade-in">
    <div class="text-center mb-10">
      <div class="mb-6 logo-container">
        <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl mx-auto flex items-center justify-center shadow-lg transform rotate-45">
          <div class="transform -rotate-45">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
        </div>
      </div>
      <h1 class="text-3xl font-bold text-gray-800 logo-glow mb-3">Welcome Back</h1>
      <p class="text-sm text-gray-600 font-medium">Intelligent Fleet Maintenance & Management System</p>
      <div class="w-16 h-1 bg-gradient-to-r from-blue-600 to-blue-400 rounded-full mx-auto mt-4"></div>
    </div>

    @if ($errors->any())
      <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm">
        <div class="flex items-center">

            
          <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span class="font-medium">{{ $errors->first() }}</span>
        </div>
      </div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-6">
      @csrf
      <div class="space-y-2">
        <label for="email" class="block text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">Email Address</label>
        <div class="relative">
          <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                 class="input-focus w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-300"
                 placeholder="your.email@company.com">
          <svg class="w-5 h-5 text-gray-400 absolute right-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
          </svg>
        </div>
      </div>

      <div class="space-y-2">
        <label for="password" class="block text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">Password</label>
        <div class="relative">
          <input type="password" id="password" name="password" required
                 class="input-focus w-full px-4 py-3.5 pr-12 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-300"
                 placeholder="Enter your password">
          <button type="button" id="togglePassword" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors">
            <svg id="eyeOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <svg id="eyeClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
            </svg>
          </button>
        </div>
      </div>

      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2">
          <input type="checkbox" name="remember" class="w-4 h-4 rounded border-2 border-gray-300 text-blue-600 focus:ring-blue-500">
          <span class="text-sm text-gray-600">Remember me</span>
        </label>
        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">Forgot Password?</a>
      </div>

      <button type="submit"
              class="btn-hover w-full flex items-center justify-center rounded-xl text-white px-6 py-4 font-semibold text-lg shadow-lg mt-8">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
        </svg>
        Sign In Securely
      </button>
    </form>

    <p class="mt-8 text-xs text-gray-500 text-center">
      Access restricted: Admin, Driver, Technician only. Self‑registration disabled.<br>
      © 2024 ZAR Logistics - Intelligent Fleet Solutions
    </p>
  </div>

  <script>
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
      } else {
        passwordInput.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
      }
    });
  </script>
</body>
</html>
