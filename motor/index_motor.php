<?php
include "../koneksi.php";

// Ambil data motor beserta statistik penyewaan
$data = mysqli_query($conn, "
    SELECT
        motor.*,
        COUNT(rental.id_rental) AS jumlah_sewa,
        COALESCE(SUM(rental.total_harga), 0) AS total_pendapatan
    FROM motor
    LEFT JOIN rental
        ON motor.id_motor = rental.id_motor
    GROUP BY
        motor.id_motor,
        motor.merk,
        motor.harga_sewa,
        motor.status
    ORDER BY motor.id_motor ASC
");

if (!$data) {
    die("Gagal mengambil data motor: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Motor - Rental Motor</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="dashboard-page">

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="sidebar-brand">
        <div class="brand-icon">🏍️</div>
        <div>
            <h2>RENTAL MOTOR</h2>
            <span>MANAGEMENT SYSTEM</span>
        </div>
    </div>

    <div class="sidebar-menu">
        <p class="menu-title">MENU UTAMA</p>

        <a href="../dashboard.php" class="sidebar-link">
            <span>▦</span>
            Dashboard
        </a>

        <a href="index_motor.php" class="sidebar-link active">
            <span>🏍️</span>
            Data Motor
        </a>

        <a href="../pelanggan/index_pelanggan.php" class="sidebar-link">
            <span>👤</span>
            Data Pelanggan
        </a>

        <a href="../rental/index_rental.php" class="sidebar-link">
            <span>📋</span>
            Data Rental
        </a>

        <a href="../rekap/index_rekap.php" class="sidebar-link">
            <span>📊</span>
            Rekap Bulanan
        </a>
    </div>

    <div class="sidebar-bottom">
        <div class="system-online">
            <span>●</span>
            <div>
                <b>System Online</b>
                <small>Database connected</small>
            </div>
        </div>

        <a href="../logout.php" class="logout-link">
            ⇥ &nbsp; Logout
        </a>
    </div>

</aside>

<!-- MAIN CONTENT -->
<main class="dashboard-main">

    <!-- TOPBAR -->
    <header class="dashboard-topbar">
        <div>
            <h1>Data Motor</h1>
        </div>

        <div class="admin-profile">
            <div class="admin-avatar">🏍️</div>
            <div class="admin-info">
                <b>Rental Motor</b>
                <span>Administrator</span>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <section class="data-page">

        <div class="section-heading">
            <div>
                <span>DATABASE</span>
                <p>
                    Kelola data kendaraan yang tersedia untuk disewakan.
                </p>
            </div>

            <a href="tambah_motor.php" class="primary-button">
                + Tambah Motor
            </a>
        </div>

        <!-- TABLE CARD -->
        <div class="table-card">

            <div class="table-card-header"></div>

            <div class="table-wrapper">

                <table class="data-table">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Merk / Motor</th>
                            <th>Harga Sewa</th>
                            <th>Jumlah Disewa</th>
                            <th>Total Nilai Rental</th>
                            <th>Status</th>
                            <th>Tools</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $no = 1;

                        while ($row = mysqli_fetch_assoc($data)) {
                        ?>

                        <tr>
                            <!-- NOMOR -->
                            <td>
                                <?= $no++; ?>
                            </td>

                            <!-- MERK MOTOR -->
                            <td>
                                <div class="motor-name">
                                    <div class="motor-mini-icon">
                                        🏍️
                                    </div>

                                    <div>
                                        <strong>
                                            <?= htmlspecialchars(
                                                $row['merk'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </strong>
                                    </div>
                                </div>
                            </td>

                            <!-- HARGA SEWA -->
                            <td>
                                <strong class="price">
                                    Rp <?= number_format(
                                        (float) $row['harga_sewa'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>
                                </strong>
                            </td>

                            <!-- JUMLAH DISEWA -->
                            <td>
                                <strong>
                                    <?= number_format(
                                        (int) $row['jumlah_sewa'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?> kali
                                </strong>
                            </td>

                            <!-- TOTAL NILAI RENTAL -->
                            <td>
                                <strong class="price">
                                    Rp <?= number_format(
                                        (float) $row['total_pendapatan'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>
                                </strong>
                            </td>

                            <!-- STATUS -->
                            <td>
                                <?php if ($row['status'] == 'Tersedia') { ?>

                                    <span class="status-badge available">
                                        ● Tersedia
                                    </span>

                                <?php } else { ?>

                                    <span class="status-badge rented">
                                        ● Disewa
                                    </span>

                                <?php } ?>
                            </td>

                            <!-- TOOLS -->
                            <td>
                                <div class="action-buttons">

                                    <a
                                        href="edit_motor.php?id=<?= (int) $row['id_motor']; ?>"
                                        class="action-edit"
                                    >
                                        ✎ Edit
                                    </a>

                                    <a
                                        href="hapus_motor.php?id=<?= (int) $row['id_motor']; ?>"
                                        class="action-delete"
                                        onclick="return confirm('Yakin ingin menghapus motor ini?')"
                                    >
                                        🗑 Hapus
                                    </a>

                                </div>
                            </td>
                        </tr>

                        <?php
                        }
                        ?>

                        <!-- JIKA DATA KOSONG -->
                        <?php if (mysqli_num_rows($data) == 0) { ?>

                        <tr>
                            <td colspan="7" class="empty-data">
                                <div class="empty-icon">
                                    🏍️
                                </div>

                                <strong>
                                    Belum ada data motor
                                </strong>

                                <p>
                                    Silakan tambahkan data motor terlebih dahulu.
                                </p>
                            </td>
                        </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>
        </div>

    </section>

    <!-- FOOTER -->
    <footer class="dashboard-footer">
        <span>
            RENTAL MOTOR MANAGEMENT SYSTEM
        </span>

        <span>
            DATA MOTOR • PELANGGAN • RENTAL
        </span>
    </footer>

</main>

</body>
</html>

