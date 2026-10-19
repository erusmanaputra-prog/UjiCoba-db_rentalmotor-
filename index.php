<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}
$pesan = "";
if (isset($_GET['pesan']) && $_GET['pesan'] == "gagal") {
    $pesan = "Username atau password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rental Motor Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <!-- Background Decoration -->
    <div class="login-bg-circle circle-1"></div>
    <div class="login-bg-circle circle-2"></div>
    <div class="login-bg-circle circle-3"></div>
    <!-- Header -->
    <div class="login-header">
        <div>
            <div class="brand-title">
                RENTAL MOTOR
            </div>
            <div class="brand-subtitle">
                MANAGEMENT SYSTEM
            </div>
        </div>
        <div class="system-status">
            <span>●</span> SYSTEM ONLINE
        </div>
    </div>
    <!-- Login Card -->
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-card-header">
                <h1>Welcome back 👋</h1>
                <p>
                    Login untuk mengakses<br>
                    dashboard rental
                </p>
            </div>
            <?php if ($pesan != "") { ?>
                <div class="login-error">
                    <?= $pesan; ?>
                </div>
            <?php } ?>
            <form action="login_validasi.php" method="POST">
                <!-- Username -->
                <div class="input-group">
                    <label>Username</label>
                    <div class="input-box">
                        <span class="input-icon">👤</span>
                        <input
                            type="text"
                            name="username"
                            placeholder="Masukkan username"
                            required
                        >
                    </div>
                </div>
                <!-- Password -->
                <div class="input-group">
                    <label>Password</label>
                    <div class="input-box">
                        <span class="input-icon">🔒</span>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Masukkan password"
                            required
                        >
                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                        >
                            👁
                        </button>
                    </div>
                </div>
                <!-- Login Button -->
                <button
                    type="submit"
                    name="login"
                    class="login-button"
                >
                    MASUK
                    <span>→</span>
                </button>
            </form>
            <!-- Security -->
            <div class="secure-access">
                🔐 Secure Staff Access
            </div>
        </div>
    </div>
    <!-- Bottom Navigation -->
    <div class="login-footer">
        <span>DATA RENTAL</span>
        <b>•</b>
        <span>MOTOR</span>
        <b>•</b>
        <span>PELANGGAN</span>
        <b>•</b>
        <span>LAPORAN</span>
    </div>
    <script>
        function togglePassword() {
            const password =
                document.getElementById("password");
            if (password.type === "password") {
                password.type = "text";
            } else {
                password.type = "password";
            }
        }
    </script>
</body>
</html>         