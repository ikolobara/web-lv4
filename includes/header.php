<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<header>

    <h1>Virtualna Videoteka</h1>

    <input type="checkbox" id="menu-toggle" aria-hidden="true">

    <label for="menu-toggle"
           class="menu-icon"
           aria-label="Otvori navigaciju">
        ☰
    </label>

    <nav class="nav-menu">
        <ul class="nav-list">

            <li><a href="index.php">Početna</a></li>
            <li><a href="grafikoni.php">Grafikoni</a></li>
            <li><a href="gallery.php">Slike</a></li>

            <?php if (isset($_SESSION['user'])) { ?>

                <?php if ($_SESSION['role'] === 'admin') { ?>

                    <?php if ($currentPage === "gallery.php" || $currentPage === "photo.php") { ?>
                        <li><a href="admin/photo_dashboard.php">Dashboard</a></li>
                    <?php } else { ?>
                        <li><a href="admin/dashboard.php">Dashboard</a></li>
                    <?php } ?>

                <?php } ?>

                <li><a href="auth/logout.php">Logout</a></li>

            <?php } else { ?>

                <li><a href="auth/login.php">Login</a></li>
                <li><a href="auth/register.php">Register</a></li>

            <?php } ?>

        </ul>
    </nav>

</header>