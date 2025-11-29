<!DOCTYPE html>
<html>
<head>
    <title>Login - NUBA</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #f0f0f0; }
        .login-box { background: white; padding: 30px; border-radius: 10px; max-width: 400px; margin: 0 auto; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #9b3876; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Iniciar Sesión - NUBA</h2>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Entrar</button>
        </form>
        <p><a href="/NUBA_SKINCARE/app/public/">← Volver al inicio</a></p>
    </div>
</body>
</html>