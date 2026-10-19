```php
<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_petugas'])) {
    header("Location: ../index.php");
    exit;
}

// Filter tahun
$tahun = isset($_GET['tahun'])
    ? (int) $_GET['tahun']
    : (int) date('Y');

if ($tahun < 2000 || $tahun > 2100) {
    $tahun = (int) date('Y');
}

// ========================================
// REKAP RENTAL PER BULAN
// ========================================

$sql = "
    SELECT
        MONTH(tanggal_sewa) AS bulan,
        COUNT(*) AS total_transaksi,
        COALESCE(SUM(total_harga), 0) AS total_pendapatan,
        SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) AS selesai,
        SUM(CASE WHEN status = 'Masih' THEN 1 ELSE 0 END) AS masih
    FROM rental
    WHERE YEAR(tanggal_sewa) = ?
    GROUP BY MONTH(tanggal_sewa)
    ORDER BY MONTH(tanggal_sewa)
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Gagal menyiapkan rekap bulanan: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $tahun);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$rekap = [];

while ($row = mysqli_fetch_assoc($result)) {
    $rekap[(int) $row['bulan']] = $row;
}

mysqli_stmt_close($stmt);

// Nama bulan
$nama_bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

// Hitung total tahunan
$total_transaksi = 0;
$total_pendapatan = 0;
$total_selesai = 0;
$total_masih = 0;

foreach ($rekap as $data) {
    $total_transaksi += (int) $data['total_transaksi'];
    $total_pendapatan += (float) $data['total_pendapatan'];
    $total_selesai += (int) $data['selesai'];
    $total_masih += (int) $data['masih'];
}

// ========================================
// MOTOR YANG PALING SERING DISEWA
// Semua riwayat rental
// ========================================

$sql_motor = "
    SELECT
        motor.id_motor,
        motor.merk,
        COUNT(rental.id_rental) AS jumlah_sewa,
        COALESCE(SUM(rental.total_harga), 0) AS total_pendapatan
    FROM motor
    INNER JOIN rental
        ON motor.id_motor = rental.id_motor
    GROUP BY motor.id_motor, motor.merk
    ORDER BY jumlah_sewa DESC, total_pendapatan DESC
";

$result_motor = mysqli_query($conn, $sql_motor);

if (!$result_motor) {
    die("Gagal mengambil data motor: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rekap Bulanan - Rental Motor</title>

    <link rel="stylesheet" href="../style.css">
</head>

<body class="dashboard-page">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="sidebar-brand">
            <div class="brand-icon">🏍️</div>
            <div>
                <h2>RENTAL MOTOR</h2>
                <span>SISTEM INFORMASI</span>
            </div>
        </div>

        <nav class="sidebar-menu">
            <p class="menu-title">MENU UTAMA</p>

            <a href="../dashboard.php" class="sidebar-link">
                <span>🏠</span> Dashboard
            </a>

            <a href="../motor/index_motor.php" class="sidebar-link">
                <span>🏍️</span> Data Motor
            </a>

            <a href="../pelanggan/index_pelanggan.php" class="sidebar-link">
                <span>👥</span> Data Pelanggan
            </a>

            <a href="../rental/index_rental.php" class="sidebar-link">
                <span>📋</span> Data Rental
            </a>

            <a href="index_rekap.php" class="sidebar-link active">
                <span>📊</span> Rekap Bulanan
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="system-online">
                <span>●</span>
                <div>
                    <b>Sistem Online</b>
                    <small>Rental Motor</small>
                </div>
            </div>
        </div>

    </aside>

    <!-- KONTEN UTAMA -->
    <main class="dashboard-main">

        <!-- HEADER -->
        <header class="dashboard-topbar">
            <div>
                <span class="page-label">LAPORAN PENYEWAAN</span>
                <h1>Rekap Bulanan</h1>
            </div>

            <div class="admin-profile">
                <div class="admin-avatar">A</div>
                <div class="admin-info">
                    <b>Administrator</b>
                    <span>Sistem Rental Motor</span>
                </div>
            </div>
        </header>

        <!-- FILTER TAHUN -->
        <section class="quick-section">

            <div class="section-heading">
                <div>
                    <span>FILTER LAPORAN</span>
                    <h2>Pilih Tahun Rekap</h2>
                </div>
            </div>

            <form method="GET">
                <label for="tahun">Tahun:</label>

                <select name="tahun" id="tahun">
                    <?php
                    $tahun_sekarang = (int) date('Y');

                    for (
                        $y = $tahun_sekarang;
                        $y >= $tahun_sekarang - 5;
                        $y--
                    ) {
                        $selected = ($tahun === $y) ? 'selected' : '';

                        echo "<option value=\"$y\" $selected>$y</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="primary-button">
                    Tampilkan
                </button>
            </form>

        </section>

        <!-- STATISTIK TAHUNAN -->
        <section class="quick-section">

            <div class="section-heading">
                <div>
                    <span>RINGKASAN</span>
                    <h2>Statistik Tahun <?= $tahun ?></h2>
                </div>
            </div>

            <div class="statistics">

                <div class="stat-card">
                    <div class="stat-icon blue">📋</div>
                    <div class="stat-info">
                        <span>TOTAL TRANSAKSI</span>
                        <strong>
                            <?= number_format($total_transaksi, 0, ',', '.') ?>
                        </strong>
                        <small>Transaksi tahun <?= $tahun ?></small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">💰</div>
                    <div class="stat-info">
                        <span>TOTAL NILAI RENTAL</span>
                        <strong>
                            Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
                        </strong>
                        <small>Semua status rental</small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon cyan">✅</div>
                    <div class="stat-info">
                        <span>RENTAL SELESAI</span>
                        <strong>
                            <?= number_format($total_selesai, 0, ',', '.') ?>
                        </strong>
                        <small>Transaksi selesai</small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">🕒</div>
                    <div class="stat-info">
                        <span>RENTAL BERLANGSUNG</span>
                        <strong>
                            <?= number_format($total_masih, 0, ',', '.') ?>
                        </strong>
                        <small>Belum selesai</small>
                    </div>
                </div>

            </div>

        </section>

        <!-- RINCIAN REKAP BULANAN -->
        <section class="quick-section">

            <div class="section-heading">
                <div>
                    <span>DATA LAPORAN</span>
                    <h2>Rincian Rekap Bulanan</h2>
                </div>

                <p>Tahun <?= $tahun ?></p>
            </div>

            <div style="width: 100%; overflow-x: auto;">
                <table class="data-table">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bulan</th>
                            <th>Total Transaksi</th>
                            <th>Total Nilai Rental</th>
                            <th>Selesai</th>
                            <th>Masih Berlangsung</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($nama_bulan as $nomor => $bulan): ?>

                            <?php
                            $data = $rekap[$nomor] ?? [
                                'total_transaksi' => 0,
                                'total_pendapatan' => 0,
                                'selesai' => 0,
                                'masih' => 0
                            ];
                            ?>

                            <tr>
                                <td><?= $nomor ?></td>

                                <td><?= $bulan ?></td>

                                <td>
                                    <?= number_format(
                                        (int) $data['total_transaksi'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>
                                    Rp <?= number_format(
                                        (float) $data['total_pendapatan'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td><?= (int) $data['selesai'] ?></td>

                                <td><?= (int) $data['masih'] ?></td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="2">TOTAL</th>

                            <th>
                                <?= number_format(
                                    $total_transaksi,
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </th>

                            <th>
                                Rp <?= number_format(
                                    $total_pendapatan,
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </th>

                            <th><?= $total_selesai ?></th>

                            <th><?= $total_masih ?></th>
                        </tr>
                    </tfoot>

                </table>
            </div>

        </section>

        <!-- MOTOR PALING SERING DISEWA -->
        <section class="quick-section">

            <div class="section-heading">
                <div>
                    <span>STATISTIK MOTOR</span>
                    <h2>Motor yang Paling Sering Disewa</h2>
                </div>

                <p>Semua riwayat rental</p>
            </div>

            <div style="width: 100%; overflow-x: auto;">
                <table class="data-table">

                    <thead>
                        <tr>
                            <th>Peringkat</th>
                            <th>Nama Motor</th>
                            <th>Jumlah Disewa</th>
                            <th>Total Nilai Rental</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $peringkat = 1;
                        $ada_motor = false;

                        while ($motor = mysqli_fetch_assoc($result_motor)):
                            $ada_motor = true;

                            if ($peringkat === 1) {
                                $label_peringkat = '🥇 1';
                            } elseif ($peringkat === 2) {
                                $label_peringkat = '🥈 2';
                            } elseif ($peringkat === 3) {
                                $label_peringkat = '🥉 3';
                            } else {
                                $label_peringkat = $peringkat;
                            }
                        ?>

                            <tr>
                                <td><?= $label_peringkat ?></td>

                                <td>
                                    <?= htmlspecialchars(
                                        $motor['merk'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <td>
                                    <?= number_format(
                                        (int) $motor['jumlah_sewa'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?> kali
                                </td>

                                <td>
                                    Rp <?= number_format(
                                        (float) $motor['total_pendapatan'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>
                            </tr>

                        <?php
                            $peringkat++;
                        endwhile;
                        ?>

                        <?php if (!$ada_motor): ?>
                            <tr>
                                <td colspan="4">
                                    Belum ada data penyewaan motor.
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>

                </table>
            </div>

        </section>

        <!-- FOOTER -->
        <footer class="dashboard-footer">
            <span>SISTEM INFORMASI RENTAL MOTOR</span>

            <span>
                REKAP BULANAN &copy; <?= date('Y') ?>
            </span>
        </footer>

    </main>

</body>
</html>
```
