<?php
session_start();

require_once __DIR__ . "/includes/db.php";

$id = intval($_GET['id']);
$user_id = $_SESSION['user'] ?? null;

$res = mysqli_query($conn, "
    SELECT * FROM slike
    WHERE id=$id
");

$photo = mysqli_fetch_assoc($res);

if (!$photo) {
    die("Slika ne postoji");
}

if (isset($_POST['ocjena']) && $user_id) {

    $ocjena = intval($_POST['ocjena']);

    $check = mysqli_query($conn, "
        SELECT *
        FROM ocjene_slike
        WHERE id_korisnik=$user_id
        AND id_slika=$id
    ");

    if (mysqli_num_rows($check) > 0) {

        mysqli_query($conn, "
            UPDATE ocjene_slike
            SET ocjena=$ocjena
            WHERE id_korisnik=$user_id
            AND id_slika=$id
        ");

    } else {

        mysqli_query($conn, "
            INSERT INTO ocjene_slike
            (id_korisnik, id_slika, ocjena)
            VALUES ($user_id, $id, $ocjena)
        ");
    }

    header("Location: photo.php?id=$id");
    exit;
}

$avg_q = mysqli_query($conn, "
    SELECT AVG(ocjena) as prosjek
    FROM ocjene_slike
    WHERE id_slika=$id
");

$avg = mysqli_fetch_assoc($avg_q);
?>

<!DOCTYPE html>
<html lang="hr">

<head>
    <meta charset="UTF-8">
    <title><?= $photo['naziv'] ?></title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .photo-wrapper {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 60px;
        }

        .photo-wrapper img {
            max-width: 300px; 
            width: 90%;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .rating-box {
            margin-top: 20px;
        }

        select, button {
            padding: 8px;
            margin-top: 10px;
        }
    </style>
</head>

<?php include __DIR__ . "/includes/header.php"; ?>

<body>

<h1 style="text-align:center; margin-top:20px;">
    <?= $photo['naziv'] ?>
</h1>

<div class="photo-wrapper">

    <img src="<?= $photo['putanja'] ?>" alt="slika">

    <h2>
        ⭐ Prosjek:
        <?= $avg['prosjek'] ? round($avg['prosjek'], 1) : "Nema ocjena" ?>
    </h2>

    <?php if ($user_id) { ?>

        <div class="rating-box">

            <form method="POST">

                <select name="ocjena">
                    <option value="1">1 ⭐</option>
                    <option value="2">2 ⭐</option>
                    <option value="3">3 ⭐</option>
                    <option value="4">4 ⭐</option>
                    <option value="5">5 ⭐</option>
                </select>

                <br>

                <button type="submit">Ocijeni</button>

            </form>

        </div>

    <?php } ?>

</div>

</body>
</html>