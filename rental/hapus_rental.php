<?php
include "../koneksi.php";
if (isset($_GET['id'])) {
    $id_rental = $_GET['id'];
    // Ambil data rental
    $data = mysqli_query($conn, "
        SELECT id_motor, status
        FROM rental
        WHERE id_rental = '$id_rental'
    ");
    $row = mysqli_fetch_assoc($data);
    if ($row) {
        $id_motor = $row['id_motor'];
        // Jika rental masih berjalan,
        // kembalikan motor menjadi tersedia
        if ($row['status'] == 'Masih') {
            mysqli_query($conn, "
                UPDATE motor
                SET status = 'Tersedia'
                WHERE id_motor = '$id_motor'
            ");
        }
        // Hapus rental
        mysqli_query($conn, "
            DELETE FROM rental
            WHERE id_rental = '$id_rental'
        ");
    }
}
header("Location: index_rental.php");
exit;
?>