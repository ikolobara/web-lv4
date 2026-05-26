<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("No access");
}

require_once __DIR__ . "/../includes/db.php";

if (isset($_POST['add'])) {

    $naslov = $_POST['naslov'];
    $zanr = $_POST['zanr'];
    $godina = intval($_POST['godina']);
    $trajanje = intval($_POST['trajanje']);
    $ocjena = floatval($_POST['ocjena']);
    $drzava = $_POST['drzava'];

    $errors = [];

    if ($godina < 1880 || $godina > 2030) {
        $errors[] = "Neispravna godina";
    }

    if ($trajanje < 1 || $trajanje > 500) {
        $errors[] = "Neispravno trajanje";
    }

    if ($ocjena < 0 || $ocjena > 10) {
        $errors[] = "Ocjena mora biti između 0 i 10";
    }

    if (empty($errors)) {

        mysqli_query($conn, "
            INSERT INTO filmovi
            (naslov, zanr, godina, trajanje, ocjena, drzava)
            VALUES
            ('$naslov', '$zanr', $godina, $trajanje, $ocjena, '$drzava')
        ");

        header("Location: dashboard.php");
        exit;
    }
}

if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    mysqli_query($conn, "DELETE FROM zeljeni_filmovi WHERE film_id=$id");

    mysqli_query($conn, "DELETE FROM filmovi WHERE id=$id");

    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['update'])) {

    $id = intval($_POST['id']);

    $naslov = $_POST['naslov'];
    $zanr = $_POST['zanr'];
    $godina = intval($_POST['godina']);
    $trajanje = intval($_POST['trajanje']);
    $ocjena = floatval($_POST['ocjena']);
    $drzava = $_POST['drzava'];

    mysqli_query($conn, "
        UPDATE filmovi SET
        naslov='$naslov',
        zanr='$zanr',
        godina=$godina,
        trajanje=$trajanje,
        ocjena=$ocjena,
        drzava='$drzava'
        WHERE id=$id
    ");

    header("Location: dashboard.php");
    exit;
}

$result = mysqli_query($conn, "
    SELECT * FROM filmovi
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="hr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="container">

    <div class="top-bar">

        <h1>Admin Dashboard</h1>

        <div>
            <a href="../index.php">Početna</a>
            <a href="../auth/logout.php">Logout</a>
        </div>

    </div>

    <?php
    if (!empty($errors)) {
        foreach ($errors as $e) {
            echo "<div class='error'>$e</div>";
        }
    }
    ?>

    <h2>Dodaj novi film</h2>

    <form method="POST" class="add-form">

        <input name="naslov" placeholder="Naslov" required>

        <input name="zanr" placeholder="Žanr" required>

        <input
            name="godina"
            type="number"
            min="1880"
            max="2100"
            placeholder="Godina"
            required
        >

        <input
            name="trajanje"
            type="number"
            min="1"
            max="500"
            placeholder="Trajanje"
            required
        >

        <input
            name="ocjena"
            type="number"
            min="0"
            max="10"
            step="0.1"
            placeholder="Ocjena"
            required
        >

        <input name="drzava" placeholder="Država" required>

        <button type="submit" name="add">
            Dodaj film
        </button>

    </form>

    <hr>

    <h2>Svi filmovi</h2>

    <div class="table-wrapper">

    <table>

        <tr>
            <th>ID</th>
            <th>Naslov</th>
            <th>Žanr</th>
            <th>Godina</th>
            <th>Trajanje</th>
            <th>Ocjena</th>
            <th>Država</th>
            <th>Akcije</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

        <tr>

            <td><?= $row['id'] ?></td>

            <form method="POST">

                <td>
                    <input
                        name="naslov"
                        value="<?= htmlspecialchars($row['naslov']) ?>"
                    >
                </td>

                <td>
                    <input
                        name="zanr"
                        value="<?= htmlspecialchars($row['zanr']) ?>"
                    >
                </td>

                <td>
                    <input
                        type="number"
                        name="godina"
                        value="<?= $row['godina'] ?>"
                    >
                </td>

                <td>
                    <input
                        type="number"
                        name="trajanje"
                        value="<?= $row['trajanje'] ?>"
                    >
                </td>

                <td>
                    <input
                        type="number"
                        step="0.1"
                        name="ocjena"
                        value="<?= $row['ocjena'] ?>"
                    >
                </td>

                <td>
                    <input
                        name="drzava"
                        value="<?= htmlspecialchars($row['drzava']) ?>"
                    >
                </td>

                <td class="actions">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $row['id'] ?>"
                    >

                    <button type="submit" name="update">
                        Update
                    </button>

                    <a
                        class="delete-btn"
                        href="dashboard.php?delete=<?= $row['id'] ?>"
                        onclick="return confirm('Obrisati film?')"
                    >
                        Delete
                    </a>

                </td>

            </form>

        </tr>

        <?php } ?>

    </table>

    </div>

</div>

</body>
</html>