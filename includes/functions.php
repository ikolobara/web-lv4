<?php
include "db.php";


function getFilms($conn, $filters = []) {

    $sql = "SELECT * FROM filmovi WHERE 1=1";

    if (!empty($filters['zanr'])) {
        $sql .= " AND zanr='" . mysqli_real_escape_string($conn, $filters['zanr']) . "'";
    }

    if (!empty($filters['godina'])) {
        $sql .= " AND godina >= " . intval($filters['godina']);
    }

    if (!empty($filters['drzava'])) {
        $sql .= " AND drzava LIKE '%" . mysqli_real_escape_string($conn, $filters['drzava']) . "%'";
    }

    return mysqli_query($conn, $sql);
}


function addToCart($conn, $user_id, $film_id) {

    $id = intval($film_id);

    $res = mysqli_query($conn, "SELECT * FROM filmovi WHERE id=$id");
    $film = mysqli_fetch_assoc($res);

    if (!$film) return;

    $check = mysqli_query($conn, "
        SELECT * FROM zeljeni_filmovi
        WHERE user_id=$user_id AND film_id=$id
    ");

    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "
            INSERT INTO zeljeni_filmovi (user_id, film_id)
            VALUES ($user_id, $id)
        ");
    }
}


function removeFromCart($conn, $user_id, $film_id) {

    $id = intval($film_id);

    mysqli_query($conn, "
        DELETE FROM zeljeni_filmovi
        WHERE user_id=$user_id AND film_id=$id
    ");
}


function clearCart($conn, $user_id) {

    mysqli_query($conn, "
        DELETE FROM zeljeni_filmovi
        WHERE user_id=$user_id
    ");
}


function getCart($conn, $user_id) {

    return mysqli_query($conn, "
        SELECT f.*
        FROM zeljeni_filmovi z
        JOIN filmovi f ON z.film_id = f.id
        WHERE z.user_id = $user_id
    ");
}
?>