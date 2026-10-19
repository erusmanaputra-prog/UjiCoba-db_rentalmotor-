<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Rental Motor</title>
    <link rel="stylesheet" href="style.css">
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
            <a href="dashboard.php" class="sidebar-link active">
                <span>▦</span>
                Dashboard
            </a>
            <a href="motor/index_motor.php" class="sidebar-link">
                <span>🏍️</span>
                Data Motor
            </a>
            <a href="pelanggan/index_pelanggan.php" class="sidebar-link">
                <span>👤</span>
                Data Pelanggan
            </a>
            <a href="rental/index_rental.php" class="sidebar-link">
                <span>📋</span>
                Data Rental
            </a>
            <a href="rekap/index_rekap.php" class="sidebar-link">
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
            <a href="logout.php" class="logout-link">
                ⇥ &nbsp; Logout
            </a>
        </div>
    </aside>
    <!-- MAIN CONTENT -->
    <main class="dashboard-main">
        <!-- TOPBAR -->
        <header class="dashboard-topbar">
            <div>
                <span class="page-label">DASHBOARD</span>
                <h1>Dashboard</h1>
            </div>
            <div class="admin-profile">
                <div class="admin-avatar">
                    <?= strtoupper(substr($_SESSION['nama_petugas'], 0, 1)); ?>
                </div>
                <div class="admin-info">
                    <b><?= $_SESSION['nama_petugas']; ?></b>
                    <span>Administrator</span>
                </div>
            </div>
        </header>
        <!-- WELCOME -->
        <section class="dashboard-welcome">
            <div>
                <span class="welcome-label">OVERVIEW</span>
                <h2>
                    Selamat datang kembali,
                    <?= $_SESSION['nama_petugas']; ?> 👋
                </h2>
                <p>
                    Kelola data motor, pelanggan, dan transaksi rental
                    melalui dashboard ini.
                </p>
            </div>
            <div class="welcome-icon">
                🏍️
            </div>
        </section>
        <!-- STATISTICS -->
        <!-- QUICK ACCESS -->
        <section class="quick-section">
            <div class="section-heading">
                <div>
                    <span>QUICK ACCESS</span>
                    <h2>Akses Menu</h2>
                </div>
                <p>Kelola sistem rental dengan cepat</p>
            </div>
            <div class="quick-grid">
                <a href="motor/index_motor.php" class="quick-card">
                    <div class="quick-number">01</div>
                    <div class="quick-icon">
                        🏍️
                    </div>
                    <div>
                        <h3>Data Motor</h3>
                        <p>
                            Tambah, edit, dan hapus data motor.
                        </p>
                    </div>
                    <div class="quick-arrow">→</div>
                </a>
                <a href="pelanggan/index_pelanggan.php" class="quick-card">
                    <div class="quick-number">02</div>
                    <div class="quick-icon">
                        👤
                    </div>
                    <div>
                        <h3>Data Pelanggan</h3>
                        <p>
                            Kelola informasi pelanggan rental.
                        </p>
                    </div>
                    <div class="quick-arrow">→</div>
                </a>
                <a href="rental/index_rental.php" class="quick-card">
                    <div class="quick-number">03</div>
                    <div class="quick-icon">
                        📋
                    </div>
                    <div>
                        <h3>Data Rental</h3>
                        <p>
                            Kelola transaksi penyewaan motor.
                        </p>
                    </div>
                    <div class="quick-arrow">→</div>
                </a>
                <a href="rekap/index_rekap.php" class="quick-card">
                    <div class="quick-number">03</div>
                    <div class="quick-icon">
                        📊
                    </div>
                    <div>
                        <h3>Data Rekap</h3>
                        <p>
                            Kelola Rekap.
                        </p>
                    </div>
                    <div class="quick-arrow">→</div>
                </a>
            </div>
        </section>
        <!-- FOOTER -->
        <footer class="dashboard-footer">
            <span>
                RENTAL MOTOR MANAGEMENT SYSTEM
            </span>
            <span>
                DATA RENTAL • MOTOR • PELANGGAN • LAPORAN
            </span>
        </footer>
    </main>
</body>
</html>