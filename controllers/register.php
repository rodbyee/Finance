<?php
require_once 'conexion.php';  


if (isset($_SESSION['id_user'])) {
    header("Location: movimientos.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: reg.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm']  ?? '';


if ($username === '' || $password === '' || $confirm === '') {
    header("Location: reg.php?error=1&username=" . urlencode($username));
    exit;
}

if ($password !== $confirm) {
    header("Location: reg.php?error=2&username=" . urlencode($username));
    exit;
}

$stmt = mysqli_prepare($conn,
    "SELECT id_user FROM user WHERE name_user = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    header("Location: reg.php?error=3&username=" . urlencode($username));
    exit;
}
mysqli_stmt_close($stmt);


$hash = password_hash($password, PASSWORD_BCRYPT);   // hash seguro

$stmt = mysqli_prepare($conn,
    "INSERT INTO user (name_user, password_user) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ss", $username, $hash);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: reg.php?error=4&username=" . urlencode($username));
    exit;
}
mysqli_stmt_close($stmt);


header("Location: reg.php?ok=1");
exit;