<?php
include "../koneksi.php";
$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM motor WHERE id_motor='$id'");
$row = mysqli_fetch_assoc($data);
if (isset($_POST['update'])) {
    $merk = $_POST['merk'];
    $harga_sewa = $_POST['harga_sewa'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE motor SET
        merk='$merk',
        harga_sewa='$harga_sewa',
        status='$status'
        WHERE id_motor='$id'
    ");
    header("Location: index_motor.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Motor</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="form-card">
    <a href="index_motor.php" class="back-button">
        ← Kembali
    </a>
        <div class="form-header">
            <h2>Edit Data Motor</h2>
            <p>Perbarui informasi motor yang dipilih</p>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>Merk Motor</label>
                <input
                    type="text"
                    name="merk"
                    value="<?= htmlspecialchars($row['merk']); ?>"
                    required
                >
            </div>
            <div class="form-group">
                <label>Harga Sewa</label>
                <input
                    type="number"
                    name="harga_sewa"
                    value="<?= $row['harga_sewa']; ?>"
                    required
                >
            </div>
            <div class="form-group">
                <label>Status Motor</label>
                <select name="status" required>
                    <option value="Tersedia"
                        <?= $row['status'] == 'Tersedia' ? 'selected' : ''; ?>>
                        Tersedia
                    </option>
                    <option value="Disewa"
                        <?= $row['status'] == 'Disewa' ? 'selected' : ''; ?>>
                        Disewa
                    </option>
                </select>
            </div>
            <button type="submit" name="update" class="primary-button">
                Update
        </button>
        </form>
</div>
</body>
</html>