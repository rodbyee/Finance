<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance.com – Iniciar sesión</title>
    <link rel="stylesheet" href="style.css">
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
                <h2>Iniciar sesión</h2>

                <form action="controllers/login.php" method="post">

                    <?php
                    if (!empty($_GET['error'])) {
                        echo '<p class="error-msg">Usuario o contraseña incorrectos.</p>';
                    }
                    ?>

                    <div class="form-group">
                        <label for="username">Usuario:</label>
                        <input type="text" id="username" name="username" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div>
                        <button type="submit">Entrar</button>
                    </div>

                    <div>
                        <a href="reg.php">¿No tienes cuenta? Regístrate aquí.</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>