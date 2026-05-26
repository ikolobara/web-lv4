<?php
session_start();
require_once __DIR__ . "/../includes/db.php";


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("No access");
}

if (isset($_POST['upload'])) {

    if (!isset($_FILES['slika']) || $_FILES['slika']['error'] !== 0) {
        die("Greška pri uploadu");
    }

    $file = $_FILES['slika'];

    $allowed = ['image/jpeg', 'image/png'];

    if (!in_array($file['type'], $allowed)) {
        die("Samo JPG/PNG");
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        die("Max 5MB");
    }

    $uploadDir = __DIR__ . "/../uploads/images/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = time() . "_" . basename($file['name']);
    $fullPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
        die("Upload nije uspio");
    }

    $dbPath = "uploads/images/" . $filename;

    mysqli_query($conn, "
        INSERT INTO slike (naziv, putanja)
        VALUES ('Slika', '$dbPath')
    ");

    header("Location: photo_dashboard.php");
    exit;
}

if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    $res = mysqli_query($conn, "SELECT * FROM slike WHERE id=$id");
    $img = mysqli_fetch_assoc($res);

    if ($img) {

        $filePath = __DIR__ . "/../" . $img['putanja'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        mysqli_query($conn, "DELETE FROM ocjene_slike WHERE id_slika=$id");

        mysqli_query($conn, "DELETE FROM slike WHERE id=$id");
    }

    header("Location: photo_dashboard.php");
    exit;
}


$res = mysqli_query($conn, "SELECT * FROM slike ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Photo Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<h1>Photo Dashboard (Admin)</h1>

<form method="POST" enctype="multipart/form-data" style="margin:20px;">
    <input type="file" name="slika" required>
    <button type="submit" name="upload">Upload</button>
</form>

<div style="margin:20px; display:flex; gap:10px; justify-content:center;">
    <a href="../index.php"><button>Početna</button></a>
    <a href="../gallery.php"><button>Galerija</button></a>
    <a href="../auth/logout.php"><button>Logout</button></a>
</div>

<hr>

<h2>Učitane slike</h2>

<div style="display:flex; flex-wrap:wrap; gap:20px; justify-content:center;">

<?php while ($row = mysqli_fetch_assoc($res)) { ?>

    <div style="border:1px solid #ddd; padding:10px; border-radius:10px; background:white;">

        <img src="../<?= $row['putanja'] ?>" width="200">

        <p><?= htmlspecialchars($row['naziv']) ?></p>

        <a href="photo_dashboard.php?delete=<?= $row['id'] ?>"
           onclick="return confirm('Obrisati sliku?')">
            <button>Delete</button>
        </a>

    </div>

<?php } ?>

</div>

</body>
</html>