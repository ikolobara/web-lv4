<?php
session_start();

require_once __DIR__ . "/../includes/db.php";

if ($_POST) {

    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE username='$u'");
    $user = mysqli_fetch_assoc($res);

    if ($user && password_verify($p, $user['password'])) {

        $_SESSION['user'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        header("Location: ../index.php");
        exit;

    } else {
        echo "Login error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>Login</h2>

<form method="POST">
<input name="username" placeholder="username">
<input name="password" type="password" placeholder="password">
<button>Login</button>
</form>
<button href="register.php" style="margin:20px;">Register</button>

</body>
</html>