<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_petugas'])) {
    header("Location: ../index.php");
    exit;
}

$id_petugas = $_SESSION['id_petugas'];
$pesan_error = "";

// Proses simpan rental
if (isset($_POST['simpan'])) {

    $id_pelanggan = (int) $_POST['id_pelanggan'];
    $id_motor = (int) $_POST['id_motor'];
    $tanggal_sewa = $_POST['tanggal_sewa'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    // Validasi tanggal
    if (
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_sewa) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_kembali)
    ) {
        $pesan_error = "Format tanggal tidak valid.";
    } elseif ($tanggal_kembali < $tanggal_sewa) {
        $pesan_error = "Tanggal kembali tidak boleh sebelum tanggal sewa.";
    } else {

        // Ambil harga dan pastikan motor tersedia
        $stmt_motor = mysqli_prepare(
            $conn,
            "SELECT harga_sewa
             FROM motor
             WHERE id_motor = ? AND status = 'Tersedia'"
        );

        mysqli_stmt_bind_param($stmt_motor, "i", $id_motor);
        mysqli_stmt_execute($stmt_motor);

        $hasil_motor = mysqli_stmt_get_result($stmt_motor);
        $data_motor = mysqli_fetch_assoc($hasil_motor);

        mysqli_stmt_close($stmt_motor);

        if (!$data_motor) {
            $pesan_error = "Motor tidak tersedia. Silakan pilih motor lain.";
        } else {

            // Pastikan pelanggan ada
            $stmt_pelanggan = mysqli_prepare(
                $conn,
                "SELECT id_pelanggan
                 FROM pelanggan
                 WHERE id_pelanggan = ?"
            );

            mysqli_stmt_bind_param(
                $stmt_pelanggan,
                "i",
                $id_pelanggan
            );

            mysqli_stmt_execute($stmt_pelanggan);
            $hasil_pelanggan = mysqli_stmt_get_result($stmt_pelanggan);
            $data_pelanggan = mysqli_fetch_assoc($hasil_pelanggan);

            mysqli_stmt_close($stmt_pelanggan);

            if (!$data_pelanggan) {
                $pesan_error = "Pelanggan tidak ditemukan.";
            } else {

                // Hitung lama sewa
                $tanggal1 = new DateTime($tanggal_sewa);
                $tanggal2 = new DateTime($tanggal_kembali);

                $lama_sewa = $tanggal1->diff($tanggal2)->days;

                if ($lama_sewa < 1) {
                    $lama_sewa = 1;
                }

                // Hitung total harga
                $harga_per_hari = (int) $data_motor['harga_sewa'];
                $total_harga = $lama_sewa * $harga_per_hari;

                // Gunakan transaksi database
                mysqli_begin_transaction($conn);

                try {
                    // Kunci dan periksa kembali motor
                    $stmt_cek = mysqli_prepare(
                        $conn,
                        "SELECT id_motor
                         FROM motor
                         WHERE id_motor = ? AND status = 'Tersedia'
                         FOR UPDATE"
                    );

                    mysqli_stmt_bind_param($stmt_cek, "i", $id_motor);
                    mysqli_stmt_execute($stmt_cek);

                    $cek_motor = mysqli_stmt_get_result($stmt_cek);
                    $motor_tersedia = mysqli_fetch_assoc($cek_motor);

                    mysqli_stmt_close($stmt_cek);

                    if (!$motor_tersedia) {
                        throw new Exception(
                            "Motor sudah disewa. Silakan pilih motor lain."
                        );
                    }

                    // Simpan rental
                    $stmt_simpan = mysqli_prepare(
                        $conn,
                        "INSERT INTO rental (
                            id_petugas,
                            id_pelanggan,
                            id_motor,
                            tanggal_sewa,
                            tanggal_kembali,
                            lama_sewa,
                            harga_per_hari,
                            total_harga,
                            status
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Masih')"
                    );

                    mysqli_stmt_bind_param(
                        $stmt_simpan,
                        "iiissiii",
                        $id_petugas,
                        $id_pelanggan,
                        $id_motor,
                        $tanggal_sewa,
                        $tanggal_kembali,
                        $lama_sewa,
                        $harga_per_hari,
                        $total_harga
                    );

                    if (!mysqli_stmt_execute($stmt_simpan)) {
                        throw new Exception("Gagal menyimpan data rental.");
                    }

                    mysqli_stmt_close($stmt_simpan);

                    // Ubah status motor
                    $stmt_update = mysqli_prepare(
                        $conn,
                        "UPDATE motor
                         SET status = 'Disewa'
                         WHERE id_motor = ?"
                    );

                    mysqli_stmt_bind_param(
                        $stmt_update,
                        "i",
                        $id_motor
                    );

                    if (!mysqli_stmt_execute($stmt_update)) {
                        throw new Exception("Gagal memperbarui status motor.");
                    }

                    mysqli_stmt_close($stmt_update);

                    mysqli_commit($conn);

                    header("Location: index_rental.php");
                    exit;

                } catch (Throwable $e) {
                    mysqli_rollback($conn);
                    $pesan_error = $e->getMessage();
                }
            }
        }
    }
}

// Ambil pelanggan
$pelanggan = mysqli_query(
    $conn,
    "SELECT * FROM pelanggan ORDER BY nama ASC"
);

// Ambil motor yang tersedia
$motor = mysqli_query(
    $conn,
    "SELECT * FROM motor
     WHERE status = 'Tersedia'
     ORDER BY merk ASC"
);

if (!$pelanggan || !$motor) {
    die("Gagal mengambil data: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Rental</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="form-card">

    <a href="index_rental.php" class="back-button">
        ← Kembali
    </a>

    <div class="form-header">
        <h2>Tambah Data Rental</h2>
        <p>Masukkan informasi penyewaan motor</p>
    </div>

    <?php if ($pesan_error !== "") { ?>
        <p style="color: red; margin-bottom: 16px;">
            <?= htmlspecialchars($pesan_error, ENT_QUOTES, 'UTF-8'); ?>
        </p>
    <?php } ?>

    <form method="POST">

        <!-- PELANGGAN -->
        <div class="form-group">
            <label for="id_pelanggan">Pilih Pelanggan</label>

            <select name="id_pelanggan" id="id_pelanggan" required>
                <option value="">-- Pilih Pelanggan --</option>

                <?php while ($p = mysqli_fetch_assoc($pelanggan)) { ?>
                    <option
                        value="<?= (int) $p['id_pelanggan']; ?>"
                        <?= (int) ($_POST['id_pelanggan'] ?? 0) === (int) $p['id_pelanggan'] ? 'selected' : ''; ?>
                    >
                        <?= htmlspecialchars($p['nama'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- MOTOR -->
        <div class="form-group">
            <label for="id_motor">Pilih Motor</label>

            <select name="id_motor" id="id_motor" required>
                <option value="">-- Pilih Motor --</option>

                <?php while ($m = mysqli_fetch_assoc($motor)) { ?>
                    <option
                        value="<?= (int) $m['id_motor']; ?>"
                        data-harga="<?= (int) $m['harga_sewa']; ?>"
                        <?= (int) ($_POST['id_motor'] ?? 0) === (int) $m['id_motor'] ? 'selected' : ''; ?>
                    >
                        <?= htmlspecialchars($m['merk'], ENT_QUOTES, 'UTF-8'); ?>
                        - Rp<?= number_format((float) $m['harga_sewa'], 0, ',', '.'); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- TANGGAL SEWA -->
        <div class="form-group">
            <label for="tanggal_sewa">Tanggal Sewa</label>

            <input
                type="date"
                name="tanggal_sewa"
                id="tanggal_sewa"
                value="<?= htmlspecialchars($_POST['tanggal_sewa'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <!-- TANGGAL KEMBALI -->
        <div class="form-group">
            <label for="tanggal_kembali">Tanggal Kembali</label>

            <input
                type="date"
                name="tanggal_kembali"
                id="tanggal_kembali"
                value="<?= htmlspecialchars($_POST['tanggal_kembali'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                required
            >
        </div>

        <!-- LAMA SEWA -->
        <div class="form-group">
            <label for="lama_sewa">Lama Sewa</label>

            <input
                type="text"
                id="lama_sewa"
                value="0 Hari"
                readonly
            >
        </div>

        <!-- HARGA PER HARI -->
        <div class="form-group">
            <label for="harga_per_hari">Harga Per Hari</label>

            <input
                type="text"
                id="harga_per_hari"
                value="Rp0"
                readonly
            >
        </div>

        <!-- TOTAL HARGA -->
        <div class="form-group">
            <label for="total_harga">Total Harga</label>

            <input
                type="text"
                id="total_harga"
                value="Rp0"
                readonly
            >
        </div>

        <!-- SIMPAN -->
        <button
            type="submit"
            name="simpan"
            class="primary-button"
        >
            Simpan
        </button>

    </form>

</div>

<script>
function hitungRental() {
    const motor = document.getElementById("id_motor");
    const tanggalSewa = document.getElementById("tanggal_sewa").value;
    const tanggalKembali = document.getElementById("tanggal_kembali").value;

    const pilihanMotor = motor.options[motor.selectedIndex];
    const harga = Number(pilihanMotor?.dataset.harga || 0);

    document.getElementById("harga_per_hari").value =
        "Rp" + harga.toLocaleString("id-ID");

    if (!tanggalSewa || !tanggalKembali) {
        document.getElementById("lama_sewa").value = "0 Hari";
        document.getElementById("total_harga").value = "Rp0";
        return;
    }

    const mulai = new Date(tanggalSewa + "T00:00:00");
    const kembali = new Date(tanggalKembali + "T00:00:00");

    const selisih = kembali - mulai;

    let lama = Math.round(selisih / (1000 * 60 * 60 * 24));

    if (lama < 1) {
        lama = 1;
    }

    const total = lama * harga;

    document.getElementById("lama_sewa").value =
        lama + " Hari";

    document.getElementById("total_harga").value =
        "Rp" + total.toLocaleString("id-ID");
}

document.getElementById("id_motor").addEventListener(
    "change",
    hitungRental
);

document.getElementById("tanggal_sewa").addEventListener(
    "change",
    hitungRental
);

document.getElementById("tanggal_kembali").addEventListener(
    "change",
    hitungRental
);

hitungRental();
</script>

</body>
</html>
