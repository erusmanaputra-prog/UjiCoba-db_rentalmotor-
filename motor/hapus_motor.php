<?php
include "../koneksi.php";
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM rental WHERE id_motor='$id'");
mysqli_query($conn, "DELETE FROM motor WHERE id_motor='$id'");
header("Location: index_motor.php");
exit;
?>