<?php
session_start();

require_once __DIR__ . "/includes/db.php";

$result = mysqli_query($conn, "
    SELECT zanr, COUNT(*) as total
    FROM filmovi
    GROUP BY zanr
");

$genres = [];
$totalFilms = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $genres[] = $row;
    $totalFilms += $row['total'];
}

if ($totalFilms == 0) {
    $totalFilms = 1;
}

$colors = [
    "#e63946",
    "#f1faee",
    "#457b9d",
    "#a8dadc",
    "#f4a261",
    "#2a9d8f",
    "#9b59b6",
    "#34495e"
];

$parts = [];
$current = 0;

foreach ($genres as $index => $g) {

    $percentage = ($g['total'] / $totalFilms) * 100;

    $start = $current;
    $end = $current + $percentage;

    $color = $colors[$index % count($colors)];

    $parts[] = "$color {$start}% {$end}%";

    $current = $end;
}

$gradient = implode(", ", $parts);
?>

<!DOCTYPE html>
<html lang="hr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/grafikoni.css">

    <title>Grafikoni</title>
</head>

<body>

<?php include __DIR__ . "/includes/header.php"; ?>

<section class="chart-section">

    <h2>Popularnost žanrova filmova</h2>

    <div class="chart-container">

        <div class="pie-chart-new"
             style="background: conic-gradient(<?= $gradient ?>);">
        </div>

        <ul class="legend-new">

            <?php foreach ($genres as $index => $g): ?>

                <?php
                    $percentage = round(($g['total'] / $totalFilms) * 100);
                    $color = $colors[$index % count($colors)];
                ?>

                <li>
                    <span style="background: <?= $color ?>"></span>

                    <?= htmlspecialchars($g['zanr']) ?>
                    (<?= $percentage ?>%)
                </li>

            <?php endforeach; ?>

        </ul>

    </div>

</section>

<?php include __DIR__ . "/includes/footer.php"; ?>

</body>
</html>