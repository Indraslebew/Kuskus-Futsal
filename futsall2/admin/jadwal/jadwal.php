<?php
include '../../koneksi.php';

session_start();
if (!isset($_SESSION['id_admin'])) {
    echo '<script>alert("Mohon Login Dulu"); window.location.href = "../../login.php";</script>';
    exit(); // Stop execution if not logged in
}

include "../../koneksi.php";

$sql = "SELECT * FROM data_jadwal";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal</title>
    <link rel="stylesheet" type="text/css" href="cek.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        .lapangan-table th,
        .lapangan-table td {
            font-size: 1.2em;
        }

        .pilih-button {
            text-align: center;
            margin-top: 10px;
        }

        .pilih-button form {
            display: inline;
        }

        .btn-tambah, .btn-hapus {
            padding: 3px 10px;
            margin: 2px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 15px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-tambah {
            background-color: #4CAF50;
            color: #fff;
        }

        .btn-tambah:hover {
            filter: brightness(90%);

        }
        .btn-hapus {
    background-color: #e74c3c;
    color: #fff;
}

.btn-hapus:hover {
    filter: brightness(90%);
}
    </style>
</head>

<body>
    <div class="container">
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
                    <a href="../pemesanan/pemesanan.php">
                        <i class="fas fa-money-check"></i>
                        <span class="nav-item">Pemesanan</span>
                    </a>
                </li>
                <li>
                    <a href="jadwal.php">
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
            <button class="btn-tambah" onclick="window.location.href='jam.php'">Tambah</button>
            <form method='POST'>
                <table class="lapangan-table"><br>
                    <thead>
                        <tr>
                        <th>ID</th>
                        <th>Tanggal Libur</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>

                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['id_jadwal']}</td>";
                                echo "<td>{$row['tanggal_libur']}</td>";
                                echo "<td>{$row['keterangan']}</td>";
                                echo "<td><button class='btn-hapus' onclick=\"konfirmasiHapus('".$row['id_jadwal']."')\">Hapus</button></td>";

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Tidak ada jadwal.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <!-- You can add any additional HTML or form elements here if needed -->
            </form>
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
        <script>
function konfirmasiHapus(id) {
    var hapus = confirm('Anda yakin mau menghapus data ini?');

    if (hapus) {
        // Ganti ini dengan menggunakan AJAX untuk memastikan permintaan GET terkirim
        $.get('hapus.php', { id: id }, function (data) {
            // Tambahkan fungsi yang perlu dijalankan setelah penghapusan, jika ada
            window.location.href = 'jadwal.php';
        });
    }
}

    </script>
</body>

</html>
