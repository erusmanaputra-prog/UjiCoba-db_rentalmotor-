```php
<?php
include "../koneksi.php";
$data = mysqli_query($conn, "
    SELECT 
        rental.id_rental,
        pelanggan.nama,
        motor.merk,
        rental.lama_sewa,
        rental.harga_per_hari,
        rental.total_harga,
        rental.tanggal_sewa,
        rental.tanggal_kembali,
        rental.status
    FROM rental
    JOIN pelanggan 
        ON rental.id_pelanggan = pelanggan.id_pelanggan
    JOIN motor 
        ON rental.id_motor = motor.id_motor
    ORDER BY rental.id_rental ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Rental - Rental Motor</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="dashboard-page">
<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            🏍️
        </div>
        <div>
            <h2>RENTAL MOTOR</h2>
            <span>MANAGEMENT SYSTEM</span>
        </div>
    </div>
    <div class="sidebar-menu">
        <p class="menu-title">
            MENU UTAMA
        </p>
        <a href="../dashboard.php" class="sidebar-link">
            <span>▦</span>
            Dashboard
        </a>
        <a href="../motor/index_motor.php" class="sidebar-link">
            <span>🏍️</span>
            Data Motor
        </a>
        <a href="../pelanggan/index_pelanggan.php" class="sidebar-link">
            <span>👤</span>
            Data Pelanggan
        </a>
        <a href="index_rental.php" class="sidebar-link active">
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
            <span class="page-label">
                DATA RENTAL
            </span>
            <h1>
                Data Rental
            </h1>
        </div>
        <div class="admin-profile">
            <div class="admin-avatar">
                📋
            </div>
            <div class="admin-info">
                <b>
                    Rental Motor
                </b>
                <span>
                    Administrator
                </span>
            </div>
        </div>
    </header>
    <!-- CONTENT -->
    <section class="data-page">
        <!-- JUDUL + BUTTON -->
        <div class="section-heading">
            <div>
                <span>
                    TRANSAKSI
                </span>
                <h2>
                    Daftar Rental
                </h2>
                <p>
                    Kelola seluruh transaksi penyewaan motor.
                </p>
            </div>
            <a
                href="tambah_rental.php"
                class="primary-button"
            >
                + Tambah Rental
            </a>
        </div>
        <!-- TABLE -->
<div class="table-card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                 tr>
                    <th> No</th>
                    <th> Nama Pelanggan </th>
                    <th> Motor </th>
                    <th> Lama Sewa </th>
                    <th> Harga/Hari </th>        
                    <th> Total </th>
                    <th> Tanggal Sewa </th>          
                    <th> Tanggal Kembali</th>
                    <th> Status </th>
                    <th> Tools  </th>     
                </tr>           
            </thead>        
     <tbody>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($data)) {
        ?>
        <tr>
             <!-- NO -->
            <td><?= $no++; ?></td>
             <!-- PELANGGAN -->
            <td>
            <div class="customer-name">
            <div class="customer-mini-icon">👤</div>
            <div>
            <strong>
                <?= htmlspecialchars(
                $row['nama']
                ); ?>
            </strong>
            </div>
            </div>
            </td>
            <!-- MOTOR -->
            <td>
            <div class="motor-name">
            <div class="motor-mini-icon">🏍️</div>
            <div>                            
            <strong>                        
                <?= htmlspecialchars(
                $row['merk']
                ); ?>
            </strong>
            </div>                        
            </div>
            </td> 
            <!-- LAMA SEWA -->
            <td><?= $row['lama_sewa']; ?> Hari</td>
            <!-- HARGA PER HARI -->
            <td>
                <strong class="price">
                    Rp<?= number_format(
                    $row['harga_per_hari'],
                    0,
                    ',',
                    '.'
                    ); ?>
            </strong>
            </td>                    
           <!-- TOTAL HARGA -->
            <td>
                <strong class="price">
                    Rp<?= number_format(
                    $row['total_harga'],
                    0,
                    ',',
                    '.'
                    ); ?>
                </strong>
            </td>
            <!-- TANGGAL SEWA -->
            <td>
            <?= date(
                'd/m/Y',
                strtotime(
                $row['tanggal_sewa']
                )
                ); 
            ?>
            </td>
                            <!-- TANGGAL KEMBALI -->
                            <td>
                                <?= date(
                                    'd/m/Y',
                                    strtotime(
                                        $row['tanggal_kembali']
                                    )
                                ); ?>
                            </td>
                            <!-- STATUS -->
                            <td>
                                <?php if (
                                    $row['status'] == 'Masih'
                                ) { ?>
                                    <a href="selesai_rental.php?id=<?= $row['id_rental']; ?>"
                                        class="status-badge rented status-link"
                                        onclick="return confirm('Yakin penyewaan ini sudah selesai?')"
                                    >
                                        ● Masih Menyewa
                                    </a>
                                <?php } else { ?>
                                    <span class="status-badge available">
                                        ● Selesai
                                    </span>
                                <?php } ?>
                            </td>
                            <!-- TOOLS -->
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_rental.php?id=<?= $row['id_rental']; ?>"
                                        class="action-edit"
                                    >
                                        ✎ Edit
                                    </a>
                                    <a href="hapus_rental.php?id=<?= $row['id_rental']; ?>" class="action-delete"
                                        onclick="return confirm('Yakin ingin menghapus rental ini?')">🗑 Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        <!-- JIKA DATA KOSONG -->
                        <?php if (
                            mysqli_num_rows($data) == 0
                        ) { ?>
                        <tr>
                            <td colspan="10" class="empty-data">
                                <div class="empty-icon">
                                    📋
                                </div>
                                <strong>
                                    Belum ada data rental
                                </strong>
                                <p>
                                    Silakan tambahkan transaksi rental terlebih dahulu.
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
