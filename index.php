<?php
session_start();

require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/functions.php";

$user_id = $_SESSION['user'] ?? null;

if (isset($_POST['film_id']) && $user_id) {
    addToCart($conn, $user_id, $_POST['film_id']);
    header("Location: index.php");
    exit;
}

if (isset($_GET['remove']) && $user_id) {
    removeFromCart($conn, $user_id, $_GET['remove']);
    header("Location: index.php");
    exit;
}

if (isset($_GET['clear']) && $user_id) {
    clearCart($conn, $user_id);
    header("Location: index.php");
    exit;
}

$filters = [
    'zanr' => $_GET['zanr'] ?? '',
    'godina' => $_GET['godina'] ?? '',
    'drzava' => $_GET['drzava'] ?? ''
];

$result = getFilms($conn, $filters);
$cart = $user_id ? getCart($conn, $user_id) : null;
?>

<!DOCTYPE html>
<html lang="hr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmovi</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php include __DIR__ . "/includes/header.php"; ?>

<main>

<h1>Virtualna Videoteka</h1>

<div class="filters">
<form method="GET">

    <label>Žanr:</label>
    <select name="zanr">
        <option value="">Svi</option>
        <?php
        $g = mysqli_query($conn, "SELECT DISTINCT zanr FROM filmovi");
        while ($r = mysqli_fetch_assoc($g)) {
            $sel = ($filters['zanr'] == $r['zanr']) ? "selected" : "";
            echo "<option $sel value='{$r['zanr']}'>{$r['zanr']}</option>";
        }
        ?>
    </select>

    <label>Godina:</label>
    <input type="number" name="godina" value="<?= htmlspecialchars($filters['godina']) ?>">

    <label>Država:</label>
    <input type="text" name="drzava" value="<?= htmlspecialchars($filters['drzava']) ?>">

    <button type="submit">Filtriraj</button>
</form>
</div>

<table id="movieTable">
<thead>
<tr>
    <th>Naslov</th>
    <th>Godina</th>
    <th>Trajanje</th>
    <th>Žanr</th>
    <th>Ocjena</th>
    <th></th>
</tr>
</thead>

<tbody>
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['naslov'] ?></td>
    <td><?= $row['godina'] ?></td>
    <td><?= $row['trajanje'] ?></td>
    <td><?= $row['zanr'] ?></td>
    <td><?= $row['ocjena'] ?></td>

    <td>
        <form method="POST">
            <input type="hidden" name="film_id" value="<?= $row['id'] ?>">
            <button>Dodaj</button>
        </form>
    </td>
</tr>
<?php } ?>
</tbody>
</table>

<aside>

<h3>Košarica</h3>

<button onclick="window.location.href='index.php?clear=1'">
    Isprazni košaricu
</button>

<hr>

<?php if (!$cart || mysqli_num_rows($cart) == 0): ?>
    <p>Košarica je prazna</p>
<?php else: ?>

    <?php while ($film = mysqli_fetch_assoc($cart)) { ?>
        <div style="margin-bottom:10px;">
            <strong><?= $film['naslov'] ?></strong><br>
            Ocjena: <?= $film['ocjena'] ?><br>

            <button onclick="window.location.href='index.php?remove=<?= $film['id'] ?>'">
                Ukloni
            </button>
        </div>
    <?php } ?>

<?php endif; ?>

</aside>

</main>

<?php include __DIR__ . "/includes/footer.php"; ?>

</body>
</html>