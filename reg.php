<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance.com – Registro</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="assets/finance.jpg">
</head>
<body>
    <header>
        <div class="header-content">
            <img src="https://img.pikbest.com/wp/202424/dollar-money-coins-gold-icon-vector-illustration_10613260.jpg!sw800"
                 alt="Finance.com Logo" class="logo">
            <h1>Finance.com</h1>
        </div>
    </header>

    <main>
        <div class="root">
            <div class="container">
                <h2>Crear cuenta</h2>


                <?php if (!empty($_GET['error'])): ?>
                    <?php
                    $errores = [
                        '1' => 'Todos los campos son obligatorios.',
                        '2' => 'Las contraseñas no coinciden.',
                        '3' => 'Ese nombre de usuario ya está en uso.',
                        '4' => 'Ocurrió un error al guardar. Intenta de nuevo.',
                    ];
                    $msg = $errores[$_GET['error']] ?? 'Error desconocido.';
                    ?>
                    <p class="error-msg"><?= htmlspecialchars($msg) ?></p>
                <?php endif; ?>

                <?php if (!empty($_GET['ok'])): ?>
                    <p class="success-msg">Cuenta creada con éxito. <a href="index.php">Inicia sesión</a>.</p>
                <?php endif; ?>

                <form action="reg.php" method="post">

                    <div class="form-group">
                        <label for="username">Usuario:</label>
                        <input type="text" id="username" name="username"
                               value="<?= htmlspecialchars($_GET['username'] ?? '') ?>"
                               required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm">Confirmar contraseña:</label>
                        <input type="password" id="confirm" name="confirm" required>
                    </div>

                    <div>
                        <button type="submit">Registrarse</button>
                    </div>

                    <div>
                        <a href="index.php">¿Ya tienes cuenta? Inicia sesión.</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>