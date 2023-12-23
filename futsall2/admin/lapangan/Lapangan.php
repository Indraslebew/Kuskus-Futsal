<!DOCTYPE html>
<html>

<head>
    <title>Lapangan</title>
    <link rel="stylesheet" type="text/css" href="lapangan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
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
                    <a href="lapangan.php">
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
            <button class="btn-tambah" onclick="window.location.href='tambah.php'">Tambah</button>
            <form class="search-form" action="">
                <input type="text" name="search" placeholder="Cari...">
                <button type="submit"><i class="fas fa-search"></i></button>

            </form>

            <table class="lapangan-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Lapangan</th>
                        <th>Nama Lapangan</th>
                        <th>Harga Lapangan</th>
                        <th>Gambar Lapangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php

require '../../koneksi.php';

session_start();
if (!isset($_SESSION['id_admin'])) {
    echo '<script>alert("Mohon Login Dulu"); window.location.href = "../../login.php";</script>';
    exit(); // Stop execution if not logged in
}

                    include '../../koneksi.php';

                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = $_GET['search'];
                        $sql_lapangan = "SELECT * FROM data_lapangan WHERE nama_lapangan LIKE '%$search%'";
                    } else {
                        $sql_lapangan = "SELECT * FROM data_lapangan";
                    }

                    $result_lapangan = $conn->query($sql_lapangan);

                    if ($result_lapangan->num_rows > 0) {
                        $nomor = 1;
                        while ($row = $result_lapangan->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $nomor . "</td>";
                            echo "<td>" . $row["id_lapangan"] . "</td>";
                            echo "<td>" . $row["nama_lapangan"] . "</td>";
                            echo "<td> Rp." . $row["harga_lapangan"] . " / jam</td>";
                            echo "<td><img src='gambar/" . $row["gambar_lapangan"] . "' height='80'></td>";
                            echo "<td>";
                            echo "<button class='btn-edit' onclick=\"window.location.href='edit.php?id=" . $row["id_lapangan"] . "'\">Edit</button>";
                            echo "<button class='btn-hapus' onclick=\"konfirmasiHapus(" . $row["id_lapangan"] . ")\">Hapus</button>";
                            echo "</td>";
                            echo "</tr>";
                            $nomor++;
                        }
                    } else {
                        echo "0 results";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function konfirmasiHapus(id) {
            var hapus = confirm('Anda yakin mau menghapus data ini?');

            if (hapus) {
                window.location.href = 'hapus.php?id=' + id;
            }
        }
    </script>

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