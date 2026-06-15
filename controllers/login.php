<?php
require_once 'conexion.php';  


if (isset($_SESSION['id_user'])) {
    header("Location: movimientos.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: login.html?error=1");
    exit;
}


$stmt = mysqli_prepare($conn,
    "SELECT id_user, name_user, password_user FROM user WHERE name_user = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario   = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if (!$usuario || !password_verify($password, $usuario['password_user'])) {
    header("Location: ../index.php?error=1");
    exit;
}


session_regenerate_id(true);   
$_SESSION['id_user']   = $usuario['id_user'];
$_SESSION['name_user'] = $usuario['name_user'];

header("Location: movimientos.php");
exit;