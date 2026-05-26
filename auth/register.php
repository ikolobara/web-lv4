<?php
session_start();
include __DIR__ . "/../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $u = trim($_POST['username']);
    $p_raw = $_POST['password'];

    if (empty($u) || empty($p_raw)) {
        $message = "Username i password ne smiju biti prazni";
    } else {

        $p = password_hash($p_raw, PASSWORD_DEFAULT);

        mysqli_query($conn, "INSERT INTO users (username, password, role)
        VALUES ('$u', '$p', 'user')");

        $user_id = mysqli_insert_id($conn);

        $_SESSION['user'] = $user_id;
        $_SESSION['role'] = 'user';
        $_SESSION['username'] = $u;

        header("Location: ../index.php");
        exit;
    }
}
?>


<link rel="stylesheet" href="../assets/css/style.css">

<form method="POST" style="margin:30px;">
    <input name="username" placeholder="username">
    <input name="password" type="password" placeholder="password">
    <button type="submit">Register</button>
</form>

<?php if ($message) echo "<p>$message</p>"; ?>