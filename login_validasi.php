<?php
session_start();
include "koneksi.php";
$username = $_POST['username'];
$password = $_POST['password'];
$login = mysqli_query($conn, "
    SELECT * FROM petugas
    WHERE username='$username'
    AND password='$password'
") or die(mysqli_error($conn));
$cek = mysqli_num_rows($login);
if ($cek > 0) {
    $data = mysqli_fetch_assoc($login);
    $_SESSION['username'] = $data['username'];
    $_SESSION['nama_petugas'] = $data['nama_petugas'];
    $_SESSION['id_petugas'] = $data['id_petugas'];
    header("Location: dashboard.php");
    exit;
} else {
    header("Location: index.php?pesan=gagal");
    exit;
}
?>