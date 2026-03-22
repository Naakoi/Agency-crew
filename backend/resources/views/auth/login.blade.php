<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — CPPL Agency</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #0d1b2a;
            display: flex; align-items: center; justify-content: center;
            background-image:
                radial-gradient(ellipse at 20% 20%, rgba(26,120,194,0.22) 0%, transparent 55%),
                radial-gradient(ellipse at 80% 80%, rgba(12,184,168,0.14) 0%, transparent 55%);
        }
        .login-box {
            background: rgba(27,45,66,0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(30,58,95,0.6);
            border-radius: 20px;
            padding: 44px 40px;
            width: 100%; max-width: 420px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        }
        .login-brand { text-align: center; margin-bottom: 32px; }
        .login-logo {
            width: 70px; height: 70px; border-radius: 18px;
            background: linear-gradient(135deg, #1a78c2, #0cb8a8);
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: #fff; margin: 0 auto 16px;
        }
        .login-brand h1 { font-size: 20px; font-weight: 800; color: #e2e8f0; }
        .login-brand p { font-size: 13px; color: #94a3b8; margin-top: 5px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: #94a3b8; margin-bottom: 7px; text-transform: uppercase; letter-spacing: .5px; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #4e6a85; font-size: 14px; }
        .form-control {
            width: 100%; padding: 11px 14px 11px 38px;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(30,58,95,0.6);
            border-radius: 10px; color: #e2e8f0; font-size: 14px; font-family: 'Inter', sans-serif;
            transition: border .2s;
        }
        .form-control:focus { outline: none; border-color: #1a78c2; background: rgba(255,255,255,0.09); }
        .form-control::placeholder { color: rgba(148,163,184,0.4); }
        .btn-login {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #1a78c2, #0e9ae0);
            border: none; border-radius: 10px; color: #fff;
            font-size: 15px; font-weight: 700; cursor: pointer;
            transition: all .2s; margin-top: 6px; font-family: 'Inter', sans-serif;
        }
        .btn-login:hover { opacity: .88; transform: translateY(-1px); }
        .error-msg {
            background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25);
            color: #ef4444; padding: 11px 15px; border-radius: 9px;
            font-size: 13px; margin-bottom: 18px; display: flex; gap: 8px; align-items: center;
        }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-brand">
        <div class="login-logo" style="background: none; padding: 0;">
            <img src="/images/logo.svg" alt="CPPL Logo" style="width: 80px; height: 80px;">
        </div>
        <h1>CPPL</h1>
        <p>Agency Crew Accommodation</p>
    </div>

    @if($errors->any())
        <div class="error-msg">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/login">
        @csrf
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control"
                    placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control"
                    placeholder="••••••••" required>
            </div>
        </div>
        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
    </form>
</div>
</body>
</html>
