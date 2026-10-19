<?php
include "../koneksi.php";
if (isset($_GET['id'])) {
    $id_rental = $_GET['id'];
    // Ambil ID motor dari rental
    $data = mysqli_query($conn, "
        SELECT id_motor
        FROM rental
        WHERE id_rental = '$id_rental'
    ");
    $row = mysqli_fetch_assoc($data);
    if ($row) {
        $id_motor = $row['id_motor'];
        // Ubah status motor menjadi Tersedia
        mysqli_query($conn, "
            UPDATE motor
            SET status = 'Tersedia'
            WHERE id_motor = '$id_motor'
        ");
        mysqli_query($conn, "
    UPDATE rental
    SET status = 'Selesai'
    WHERE id_rental = '$id_rental'
");
    }
    // Kembali ke halaman Data Rental
    header("Location: index_rental.php");
    exit;
}
?>