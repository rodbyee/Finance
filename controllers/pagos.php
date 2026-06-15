<?php
require_once 'conexion.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_p    = trim($_POST['name_p'] ?? '');
    $descrip_p = trim($_POST['descrip_p'] ?? '');
    $monto     = $_POST['montototal'] ?? '';
    $limite    = $_POST['fecha_limite'] ?? '';

    if ($name_p !== '' && is_numeric($monto) && $monto > 0 && $limite !== '') {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO payments (id_user, name_p, descrip_p, montototal, fecha_limite, estado)
             VALUES (?, ?, ?, ?, ?, 'pendiente')");
        mysqli_stmt_bind_param($stmt, "issds", $id_user, $name_p, $descrip_p, $monto, $limite);

        if (mysqli_stmt_execute($stmt)) {
            $mensaje = "Pago pendiente registrado.";
        } else {
            $mensaje = "No se pudo guardar: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $mensaje = "Revisa los datos: nombre, monto y fecha límite son obligatorios.";
    }
}

if (isset($_GET['marcar_pagado'])) {
    $id_pay = (int) $_GET['marcar_pagado'];

    $stmt = mysqli_prepare($conn,
        "UPDATE payments SET estado = 'pagado' WHERE id_pay = ? AND id_user = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id_pay, $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: pagos.php");
    exit;
}

$stmt = mysqli_prepare($conn,
    "SELECT id_pay, name_p, descrip_p, montototal, fecha_limite, estado
     FROM payments
     WHERE id_user = ?
     ORDER BY (estado = 'pagado'), fecha_limite ASC");
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$pagos = [];
$total_pendiente = 0;

while ($fila = mysqli_fetch_assoc($resultado)) {
    $pagos[] = $fila;
    if ($fila['estado'] === 'pendiente') {
        $total_pendiente += $fila['montototal'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pagos pendientes · Finance.com</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="../assets/finance.jpg">
<link rel="stylesheet" href="dashboard.css?v=<?= time() ?>">
</head>
<body>
<header>
    <div class="header-content">
        <img src="https://img.pikbest.com/wp/202424/dollar-money-coins-gold-icon-vector-illustration_10613260.jpg!sw800" alt="Finance.com Logo" class="logo">
        <h1>Finance.com</h1>
    </div>
    <nav class="nav-links">
        <a href="movimientos.php">Movimientos</a>
        <a href="pagos.php" class="activo">Pagos pendientes</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<div class="dashboard">

    <div class="ticket">
        <div class="etiqueta">Deuda pendiente</div>
        <div class="monto negativo">$<?= number_format($total_pendiente, 2) ?></div>
    </div>

    <?php if ($mensaje): ?>
        <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <div class="tarjeta">
        <h2>Registrar pago pendiente / deuda</h2>
        <form action="pagos.php" method="post">
            <div class="fila">
                <div class="campo">
                    <label for="name_p">Nombre</label>
                    <input type="text" id="name_p" name="name_p" placeholder="Ej. Tarjeta de crédito, préstamo a..." required>
                </div>
                <div class="campo">
                    <label for="montototal">Monto total</label>
                    <input type="number" id="montototal" name="montototal" step="0.01" min="0.01" placeholder="0.00" required>
                </div>
            </div>
            <div class="fila">
                <div class="campo">
                    <label for="descrip_p">Descripción (opcional)</label>
                    <input type="text" id="descrip_p" name="descrip_p" placeholder="Detalles adicionales">
                </div>
                <div class="campo">
                    <label for="fecha_limite">Fecha límite</label>
                    <input type="date" id="fecha_limite" name="fecha_limite" required>
                </div>
            </div>
            <button type="submit">Guardar pago pendiente</button>
        </form>
    </div>

    <div class="tarjeta">
        <h2>Mis pagos y deudas</h2>
        <?php if (empty($pagos)): ?>
            <p class="vacio">No tienes pagos pendientes registrados.</p>
        <?php else: ?>
            <table class="libro">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Fecha límite</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['name_p']) ?></td>
                            <td><?= htmlspecialchars($p['descrip_p']) ?></td>
                            <td>$<?= number_format($p['montototal'], 2) ?></td>
                            <td><?= date('d/m/Y', strtotime($p['fecha_limite'])) ?></td>
                            <td>
                                <span class="badge <?= $p['estado'] ?>">
                                    <?= $p['estado'] === 'pendiente' ? 'Pendiente' : 'Pagado' ?>
                                </span>
                            </td>
                            <td class="acciones">
                                <?php if ($p['estado'] === 'pendiente'): ?>
                                    <a href="?marcar_pagado=<?= $p['id_pay'] ?>">Marcar pagado</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>
</body>
</html>