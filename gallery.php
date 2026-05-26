<?php
session_start();

include __DIR__ . "/includes/db.php";

$images = mysqli_query($conn, "
    SELECT * FROM slike
");
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Galerija</title>

    <link rel="stylesheet" href="assets/css/style_slike.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php include __DIR__ . "/includes/header.php"; ?>

<section class="galerija">

<div class="img-gallery-magnific">

<?php while ($img = mysqli_fetch_assoc($images)) { ?>

    <figure class="magnific-img">

        <a href="photo.php?id=<?= $img['id'] ?>">

            <img src="<?= $img['putanja'] ?>"
                 alt="<?= $img['naziv'] ?>">

        </a>

        <figcaption>
            <?= $img['naziv'] ?>
        </figcaption>

    </figure>

<?php } ?>

</div>

</section>

<?php include __DIR__ . "/includes/footer.php"; ?>

</body>
</html>