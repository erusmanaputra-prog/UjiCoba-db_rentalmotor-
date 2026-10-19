<?php
include "../koneksi.php";

if (isset($_POST['simpan'])) {
    $merk = $_POST['merk'];
    $harga_sewa = $_POST['harga_sewa'];
    $status = $_POST['status'];

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO motor (merk, harga_sewa, status)
         VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sis",
        $merk,
        $harga_sewa,
        $status
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index_motor.php");
        exit;
    }

    echo "Gagal menambahkan motor: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Motor</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="form-card">

    <a href="index_motor.php" class="back-button">
        ← Kembali
    </a>

    <div class="form-header">
        <h2>Tambah Data Motor</h2>
        <p>Masukkan informasi motor yang akan disewakan</p>
    </div>

    <form method="POST">

        <div class="form-group">
            <label for="merk">Merk Motor</label>
            <input
                type="text"
                id="merk"
                name="merk"
                required
            >
        </div>

        <div class="form-group">
            <label for="harga_sewa">Harga Sewa</label>
            <input
                type="number"
                id="harga_sewa"
                name="harga_sewa"
                min="0"
                required
            >
        </div>

        <div class="form-group">
            <label for="status">Status Motor</label>
            <select name="status" id="status" required>
                <option value="Tersedia">Tersedia</option>
                <option value="Disewa">Disewa</option>
            </select>
        </div>

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
