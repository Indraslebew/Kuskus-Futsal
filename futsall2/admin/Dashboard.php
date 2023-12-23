<?php
require '../koneksi.php';

session_start();
if (!isset($_SESSION['id_admin'])) {
    echo '<script>alert("Mohon Login Dulu"); window.location.href = "../login.php";</script>';
    exit(); // Stop execution if not logged in
}

include "../koneksi.php";

// Ambil jumlah dari masing-masing entitas
$sql_user = "SELECT COUNT(*) AS total_user FROM data_user";
$result_user = $conn->query($sql_user);
$row_user = $result_user->fetch_assoc();

$sql_lapangan = "SELECT COUNT(*) AS total_lapangan FROM data_lapangan";
$result_lapangan = $conn->query($sql_lapangan);
$row_lapangan = $result_lapangan->fetch_assoc();

$sql_pemesanan = "SELECT COUNT(*) AS total_pemesanan FROM data_pemesanan";
$result_pemesanan = $conn->query($sql_pemesanan);
$row_pemesanan = $result_pemesanan->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Futsal Kuskus Bakar</title>
  <link rel="stylesheet" href="dash.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

</head>

<body>
  <div class="container">
    <nav>
      <ul><br><br><br>
        <li>
          <a href="Dashboard.php">
            <i class="fas fa-home"></i>
            <span class="nav-item">Home</span>
          </a>
        </li>
        <li>
          <a href="lapangan/Lapangan.php">
            <i class="fas fa-futbol"></i>
            <span class="nav-item">Lapangan</span>
          </a>
        </li>
        <li>
          <a href="pemesanan/pemesanan.php">
            <i class="fas fa-money-check"></i>
            <span class="nav-item">Pemesanan</span>
          </a>
        </li>
        <li>
          <a href="jadwal/jadwal.php">
            <i class="fas fa-clock"></i>
            <span class="nav-item">Jadwal</span>
          </a>
        </li>
        <li>
          <a href="user/user.php">
            <i class="fas fa-user"></i>
            <span class="nav-item">User</span>
          </a>
        </li>
        <li>
          <a href="#" class="logout" onclick="konfirmasiLogout()">
            <i class="fas fa-sign-out-alt"></i>
            <span class="nav-item">Logout</span>
          </a>
        </li>
      </ul>
    </nav>

    <section class="main">
      <div class="main-skills">
        <div class="card" onclick="window.location.href='user/user.php'">
          <i class="fas fa-user"></i>
          <h3>User</h3>
          <p><b>
              <?php echo $row_user['total_user']; ?>
            </b></p>
        </div>
        <div class="card" onclick="window.location.href='lapangan/Lapangan.php'">
          <i class="fas fa-futbol"></i>
          <h3>Lapangan</h3>
          <p><b>
              <?php echo $row_lapangan['total_lapangan']; ?>
            </b></p>
        </div>
        <div class="card" onclick="window.location.href='pemesanan/pemesanan.php'">
          <i class="fas fa-money-check"></i>
          <h3>Pemesanan</h3>
          <p><b>
              <?php echo $row_pemesanan['total_pemesanan']; ?>
            </b></p>
        </div>
      </div>
    </section>
  </div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    function konfirmasiLogout() {
        var logout = confirm('Anda ingin logout?');

        if (logout) {
            $.ajax({
                url: 'logout.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        window.location.href = '../login.php';
                    } else {
                        alert('Gagal logout. Silakan coba lagi.');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        }
    }
</script>

</body>

</html>