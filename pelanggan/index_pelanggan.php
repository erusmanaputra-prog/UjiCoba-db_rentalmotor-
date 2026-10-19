<?php
include "../koneksi.php";

$data = mysqli_query($conn, "
    SELECT 
        pelanggan.*,
        COALESCE(
            (
                SELECT rental.status
                FROM rental
                WHERE rental.id_pelanggan = pelanggan.id_pelanggan
                ORDER BY rental.id_rental DESC
                LIMIT 1
            ),
            'Selesai'
        ) AS status
    FROM pelanggan
    ORDER BY pelanggan.id_pelanggan ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan - Rental Motor</title>
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

        <a href="../motor/index_motor.php" class="sidebar-link">
            <span>🏍️</span>
            Data Motor
        </a>

        <a href="index_pelanggan.php" class="sidebar-link active">
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
            <span class="page-label">DATA PELANGGAN</span>
            <h1>Data Pelanggan</h1>
        </div>

        <div class="admin-profile">
            <div class="admin-avatar">👤</div>
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
                <h2>Daftar Pelanggan</h2>
                <p>Kelola informasi pelanggan rental motor.</p>
            </div>

            <a href="tambah_pelanggan.php" class="primary-button">
                + Tambah Pelanggan
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
                            <th>Nama Pelanggan</th>
                            <th>No HP</th>
                            <th>Jenis Jaminan</th>
                            <th>Status Sewa</th>
                            <th>Tools</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;

                        while ($row = mysqli_fetch_assoc($data)) {
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>

                            <td>
                                <div class="customer-name">
                                    <div class="customer-mini-icon">👤</div>
                                    <div>
                                        <strong>
                                            <?= htmlspecialchars($row['nama']); ?>
                                        </strong>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($row['no_hp']); ?>
                                </strong>
                            </td>

                            <td>
                                <span>
                                    <?= htmlspecialchars($row['jaminan'] ?? '-'); ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($row['status'] == 'Masih') { ?>
                                    <span class="status-badge rented">
                                        ● Masih Menyewa
                                    </span>
                                <?php } else { ?>
                                    <span class="status-badge available">
                                        ● Selesai
                                    </span>
                                <?php } ?>
                            </td>

                            <td>
                                <div class="action-buttons">
                                    <a
                                        href="edit_pelanggan.php?id=<?= (int) $row['id_pelanggan']; ?>"
                                        class="action-edit"
                                    >
                                        ✎ Edit
                                    </a>

                                    <a
                                        href="hapus_pelanggan.php?id=<?= (int) $row['id_pelanggan']; ?>"
                                        class="action-delete"
                                        onclick="return confirm('Yakin ingin menghapus pelanggan ini?')"
                                    >
                                        🗑 Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>

                        <?php if (mysqli_num_rows($data) == 0) { ?>
                        <tr>
                            <td colspan="6" class="empty-data">
                                <div class="empty-icon">👤</div>
                                <strong>Belum ada data pelanggan</strong>
                                <p>
                                    Silakan tambahkan data pelanggan terlebih dahulu.
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
        <span>RENTAL MOTOR MANAGEMENT SYSTEM</span>
        <span>DATA MOTOR • PELANGGAN • RENTAL</span>
    </footer>

</main>
</body>
</html>
