<?php
include "../koneksi.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index_pelanggan.php");
    exit;
}

$id = (int) $_GET['id'];

$data = mysqli_query(
    $conn,
    "SELECT * FROM pelanggan WHERE id_pelanggan = $id"
);

$row = mysqli_fetch_assoc($data);

if (!$row) {
    header("Location: index_pelanggan.php");
    exit;
}

if (isset($_POST['update'])) {
    $nama = trim($_POST['nama']);
    $no_hp = trim($_POST['no_hp']);
    $jaminan = $_POST['jaminan'] ?? '';

    if (!in_array($jaminan, ['KTP', 'Kartu Keluarga'], true)) {
        $pesan_error = "Silakan pilih jenis jaminan yang valid.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE pelanggan
             SET nama = ?, no_hp = ?, jaminan = ?
             WHERE id_pelanggan = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssi",
            $nama,
            $no_hp,
            $jaminan,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: index_pelanggan.php");
            exit;
        } else {
            $pesan_error = "Gagal memperbarui data pelanggan.";
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="form-card">
    <a href="index_pelanggan.php" class="back-button">
        ← Kembali
    </a>

    <div class="form-header">
        <h2>Edit Data Pelanggan</h2>
        <p>Perbarui informasi pelanggan yang dipilih</p>
    </div>

    <?php if (isset($pesan_error)) { ?>
        <p style="color: red;">
            <?= htmlspecialchars($pesan_error); ?>
        </p>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <label>Nama Pelanggan</label>
            <input
                type="text"
                name="nama"
                value="<?= htmlspecialchars($row['nama']); ?>"
                maxlength="100"
                required
            >
        </div>

        <div class="form-group">
            <label>No HP</label>
            <input
                type="tel"
                name="no_hp"
                value="<?= htmlspecialchars($row['no_hp']); ?>"
                maxlength="20"
                required
            >
        </div>

        <div class="form-group">
            <label>Jenis Jaminan</label>
            <select name="jaminan" required>
                <option value="KTP"
                    <?= ($row['jaminan'] ?? '') === 'KTP' ? 'selected' : ''; ?>>
                    KTP
                </option>

                <option value="Kartu Keluarga"
                    <?= ($row['jaminan'] ?? '') === 'Kartu Keluarga' ? 'selected' : ''; ?>>
                    Kartu Keluarga (KK)
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

