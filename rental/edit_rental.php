<?php
include "../koneksi.php";
$id = $_GET['id'];
$data = mysqli_query($conn, "
    SELECT * FROM rental
    WHERE id_rental = '$id'
");
$row = mysqli_fetch_assoc($data);
$pelanggan = mysqli_query($conn, "
    SELECT * FROM pelanggan
");
$motor = mysqli_query($conn, "
    SELECT * FROM motor
");
if (isset($_POST['update'])) {
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_motor_baru = $_POST['id_motor'];
    $tanggal_sewa = $_POST['tanggal_sewa'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    // Motor lama
    $id_motor_lama = $row['id_motor'];
    // Kalau motor diganti
    if ($id_motor_lama != $id_motor_baru) {
        // Motor lama kembali tersedia
        mysqli_query($conn, "
            UPDATE motor
            SET status = 'Tersedia'
            WHERE id_motor = '$id_motor_lama'
        ");
        // Motor baru menjadi disewa
        mysqli_query($conn, "
            UPDATE motor
            SET status = 'Disewa'
            WHERE id_motor = '$id_motor_baru'
        ");
    }
    // Update data rental
    mysqli_query($conn, "
        UPDATE rental SET
        id_pelanggan = '$id_pelanggan',
        id_motor = '$id_motor_baru',
        tanggal_sewa = '$tanggal_sewa',
        tanggal_kembali = '$tanggal_kembali'
        WHERE id_rental = '$id'
    ");
    header("Location: index_rental.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Rental</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="form-card">
    <a href="index_rental.php" class="back-button">
        ← Kembali
    </a>
    <h2>Edit Data Rental</h2>
    <form method="POST">
        <label>Pilih Pelanggan</label><br>
        <select name="id_pelanggan" required>
            <?php while ($p = mysqli_fetch_assoc($pelanggan)) { ?>
                <option value="<?= $p['id_pelanggan']; ?>"
                    <?= ($p['id_pelanggan'] == $row['id_pelanggan']) ? 'selected' : ''; ?>>
                    <?= $p['nama']; ?>
                </option>
            <?php } ?>
        </select>
        <br><br>
        <label>Pilih Motor</label><br>
        <select name="id_motor" required>
            <?php while ($m = mysqli_fetch_assoc($motor)) { ?>
                <option value="<?= $m['id_motor']; ?>"
                    <?= ($m['id_motor'] == $row['id_motor']) ? 'selected' : ''; ?>>
                    <?= $m['merk']; ?> - Rp<?= number_format($m['harga_sewa'], 0, ',', '.'); ?>
                </option>
            <?php } ?>
        </select>
        <br><br>
        <label>Tanggal Sewa</label><br>
        <input type="date"
               name="tanggal_sewa"
               value="<?= $row['tanggal_sewa']; ?>"
               required>
        <br><br>
        <label>Tanggal Kembali</label><br>
        <input type="date"
               name="tanggal_kembali"
               value="<?= $row['tanggal_kembali']; ?>"
               required>
        <br><br>
        <button type="submit" name="update" class="primary-button">
            Update
        </button>
</div>
    </form>
</div>
</body>
</html>