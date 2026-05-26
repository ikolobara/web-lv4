<?php
$conn = mysqli_connect("localhost", "root", "", "videoteka");

if (!$conn) {
    die("DB error: " . mysqli_connect_error());
}
?>