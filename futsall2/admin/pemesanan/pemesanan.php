<?php
require '../../koneksi.php';

session_start();
if (!isset($_SESSION['id_admin'])) {
    echo '<script>alert("Mohon Login Dulu"); window.location.href = "../../login.php";</script>';
    exit(); // Stop execution if not logged in
}

include "../../koneksi.php";

$sql = "SELECT dp.id_pemesanan, dp.id_lapangan, dl.nama_lapangan, dp.waktu_main_pemesanan, dp.waktu_selesai, dp.lama_main, dp.status_pembayaran_pemesanan, dl.harga_lapangan, dp.total_biaya_pemesanan
        FROM data_pemesanan dp
        JOIN data_lapangan dl ON dp.id_lapangan = dl.id_lapangan";

// Check if a search term is provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $sql .= " WHERE dp.id_pemesanan LIKE '%{$search}%' OR dp.status_pembayaran_pemesanan LIKE '%{$search}%'";
}

$result = $conn->query($sql);

$pemesanan_data = array(); // Initialize the array to store data

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_lapangan = $row['id_lapangan'];
        $nama_lapangan = $row['nama_lapangan'];
        $waktu_main = $row['waktu_main_pemesanan'];

        // Mengonversi harga lapangan ke integer untuk kalkulasi
        $harga_lapangan = (int) str_replace('.', '', $row['harga_lapangan']);

        // Perhitungan total biaya
        $total_biaya = $harga_lapangan * $row['lama_main'];

        // Update total_biaya_pemesanan di database
        $update_query = "UPDATE data_pemesanan 
                         SET total_biaya_pemesanan = '$total_biaya'
                         WHERE id_pemesanan = {$row['id_pemesanan']}";
        $conn->query($update_query);

        $row['total_biaya_pemesanan'] = 'Rp.' . number_format($total_biaya, 0, ',', '.');
        $row['nama_lapangan'] = $nama_lapangan;
        $row['id_lapangan'] = $id_lapangan;
        $pemesanan_data[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Pemesanan</title>
    <link rel="stylesheet" type="text/css" href="pesan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>

<body>
    <div class="container">
        <!-- Your navigation menu code goes here -->
        <nav>
            <ul>
                <br><br><br>
                <li>
                    <a href="../Dashboard.php">
                        <i class="fas fa-home"></i>
                        <span class="nav-item">Home</span>
                    </a>
                </li>
                <li>
                    <a href="../lapangan/Lapangan.php">
                        <i class="fas fa-futbol"></i>
                        <span class="nav-item">Lapangan</span>
                    </a>
                </li>
                <li>
                    <a href="pemesanan.php">
                        <i class="fas fa-money-check"></i>
                        <span class="nav-item">Pemesanan</span>
                    </a>
                </li>
                <li>
                    <a href="../jadwal/jadwal.php">
                        <i class="fas fa-clock"></i>
                        <span class="nav-item">Jadwal</span>
                    </a>
                </li>
                <li>
                    <a href="../user/user.php">
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
        <div class="main">
            <form class="search-form" action="">
                <input type="text" name="search" placeholder="Cari...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <table class="lapangan-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Pemesanan</th>
                        <th>ID Lapangan</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Lama Main</th>
                        <th>Total Biaya</th>
                        <th>Status</th>
                        <th>Bukti Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($pemesanan_data)) {
                        $counter = 1;
                        foreach ($pemesanan_data as $pemesanan) {
                            echo "<tr>";
                            echo "<td>" . $counter . "</td>";
                            echo "<td>" . $pemesanan['id_pemesanan'] . "</td>";
                            echo "<td>" . $pemesanan['id_lapangan'] . "</td>";
                            echo "<td>" . $pemesanan['waktu_main_pemesanan'] . "</td>";
                            echo "<td>" . $pemesanan['waktu_selesai'] . "</td>";
                            echo "<td>" . $pemesanan['lama_main'] . "Jam </td>";
                            echo "<td>" . $pemesanan['total_biaya_pemesanan'] . "Jam</td>";
                            echo "<td>" . $pemesanan['status_pembayaran_pemesanan'] . "</td>";
                            echo "<td>";
                            echo "<button class='btn-cek' onclick=\"window.location.href='cek.php?id_pemesanan=" . $pemesanan['id_pemesanan'] . "'\">Cek</button>";
                            echo "</td>";
                            echo "</tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='9'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
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
                        window.location.href = '../../login.php';
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