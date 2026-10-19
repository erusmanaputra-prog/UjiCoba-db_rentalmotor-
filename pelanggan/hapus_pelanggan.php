<?php
include "../koneksi.php";
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM rental WHERE id_pelanggan='$id'");
mysqli_query($conn, "DELETE FROM pelanggan WHERE id_pelanggan='$id'");
header("Location: index_pelanggan.php");
exit;
?>