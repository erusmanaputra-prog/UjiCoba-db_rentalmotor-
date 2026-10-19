<?php
include "../koneksi.php";

$pesan_error = "";

if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama']);
    $no_hp = trim($_POST['no_hp']);
    $jaminan = $_POST['jaminan'];

    // Validasi pilihan jaminan
    if (!in_array($jaminan, ['KTP', 'Kartu Keluarga'], true)) {
        $pesan_error = "Pilihan jaminan tidak valid.";
    } elseif ($nama === "" || $no_hp === "") {
        $pesan_error = "Nama dan nomor HP wajib diisi.";
    } else {

        // Simpan data pelanggan
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO pelanggan (nama, no_hp, jaminan)
             VALUES (?, ?, ?)"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $nama,
                $no_hp,
                $jaminan
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                header("Location: index_pelanggan.php");
                exit;
            } else {
                $pesan_error = "Gagal menyimpan data pelanggan.";
            }

            if ($stmt) {
                mysqli_stmt_close($stmt);
            }
        } else {
            $pesan_error = "Gagal menyiapkan penyimpanan data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="form-card">

    <a href="index_pelanggan.php" class="back-button">
        ← Kembali
    </a>

    <div class="form-header">
        <h2>Tambah Data Pelanggan</h2>
        <p>Masukkan informasi pelanggan rental motor</p>
    </div>

    <?php if ($pesan_error !== "") { ?>
        <p style="color: red; margin-bottom: 16px;">
            <?= htmlspecialchars($pesan_error, ENT_QUOTES, 'UTF-8'); ?>
        </p>
    <?php } ?>

    <form method="POST">

        <!-- NAMA PELANGGAN -->
        <div class="form-group">
            <label for="nama">Nama Pelanggan</label>

            <input
                type="text"
                id="nama"
                name="nama"
                required
                maxlength="100"
                placeholder="Masukkan nama pelanggan"
                value="<?= htmlspecialchars($_POST['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            >
        </div>

        <!-- NOMOR HP -->
        <div class="form-group">
            <label for="no_hp">No HP</label>

            <input
                type="tel"
                id="no_hp"
                name="no_hp"
                required
                maxlength="20"
                placeholder="Masukkan nomor HP"
                value="<?= htmlspecialchars($_POST['no_hp'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            >
        </div>

        <!-- JENIS JAMINAN -->
        <div class="form-group">
            <label for="jaminan">Jenis Jaminan</label>

            <select name="jaminan" id="jaminan" required>
                <option value="">-- Pilih Jaminan --</option>

                <option value="KTP"
                    <?= ($_POST['jaminan'] ?? '') === 'KTP' ? 'selected' : ''; ?>>
                    KTP
                </option>

                <option value="Kartu Keluarga"
                    <?= ($_POST['jaminan'] ?? '') === 'Kartu Keluarga' ? 'selected' : ''; ?>>
                    Kartu Keluarga (KK)
                </option>
            </select>
        </div>

        <!-- TOMBOL SIMPAN -->
        <button
            type="submit"
            name="simpan"
            class="primary-button"
        >
            Simpan
        </button>

    </form>

</div>

</body>
</html>
