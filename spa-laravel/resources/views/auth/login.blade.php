<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - School Management</title>
    <link rel="stylesheet" href="/css/app.css">
    <style>
        body { display:flex; justify-content:center; align-items:center; height:100vh; margin:0; background:#1a1a2e; }
        .login-box { background:#fff; border-radius:12px; padding:40px; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,0.3); width:320px; }
        .login-box h1 { margin-bottom:5px; font-size:22px; color:#333; }
        .login-box p { color:#888; font-size:14px; margin-bottom:30px; }
        .btn-google { display:inline-flex; align-items:center; gap:10px; background:#fff; border:1px solid #ddd; border-radius:6px; padding:12px 24px; text-decoration:none; color:#333; font-size:15px; box-shadow:0 1px 3px rgba(0,0,0,0.1); transition:box-shadow 0.2s; }
        .btn-google:hover { box-shadow:0 3px 8px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>School Management</h1>
        <p>Inicia sesión para continuar</p>
        <a href="{{ route('google.login') }}" class="btn-google">
            <img src="https://developers.google.com/identity/images/g-logo.png" width="20" height="20">
            Continuar con Google
        </a>
    </div>
</body>
</html>