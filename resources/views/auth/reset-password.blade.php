<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IFMMS — Reset Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .login-bg {
      background: linear-gradient(135deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.4)),
                 url('/images/login-bg.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: scroll;
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
  <div class="w-full max-w-md px-8 py-12 glass-effect rounded-3xl shadow-2xl fade-in">
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
      <h1 class="text-3xl font-bold text-gray-800 logo-glow mb-3">Reset Password</h1>
      <p class="text-sm text-gray-600 font-medium">Enter your new password</p>
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

    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
      @csrf
      <input type="hidden" name="token" value="{{ $request->route('token') }}">

      <div class="space-y-2">
        <label for="email" class="block text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">Email Address</label>
        <div class="relative">
          <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                 class="input-focus w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-300"
                 placeholder="your.email@company.com">
          <svg class="w-5 h-5 text-gray-400 absolute right-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
          </svg>
        </div>
      </div>

      <div class="space-y-2">
        <label for="password" class="block text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">New Password</label>
        <div class="relative">
          <input type="password" id="password" name="password" required
                 class="input-focus w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-300"
                 placeholder="Enter new password">
          <svg class="w-5 h-5 text-gray-400 absolute right-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
        </div>
      </div>

      <div class="space-y-2">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">Confirm Password</label>
        <div class="relative">
          <input type="password" id="password_confirmation" name="password_confirmation" required
                 class="input-focus w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-300"
                 placeholder="Confirm new password">
          <svg class="w-5 h-5 text-gray-400 absolute right-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
        </div>
      </div>

      <button type="submit"
              class="btn-hover w-full flex items-center justify-center rounded-xl text-white px-6 py-4 font-semibold text-lg shadow-lg mt-8">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Reset Password
      </button>
    </form>

    <div class="mt-8 text-center">
      <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
        ← Back to Login
      </a>
    </div>

    <p class="mt-6 text-xs text-gray-500 text-center">
      Access restricted: Admin, Driver, Technician only.<br>
      © 2024 ZAR Logistics - Intelligent Fleet Solutions
    </p>
  </div>
</body>
</html>
