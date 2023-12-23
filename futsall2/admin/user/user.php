<?php
include '../../koneksi.php';


session_start();
if (!isset($_SESSION['id_admin'])) {
    echo '<script>alert("Mohon Login Dulu"); window.location.href = "../../login.php";</script>';
    exit(); // Stop execution if not logged in
}


$sql = "SELECT * FROM data_user";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>User</title>
    <link rel="stylesheet" type="text/css" href="user.css">
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
                    <a href="../jadwal/jadwal.php">
                        <i class="fas fa-clock"></i>
                        <span class="nav-item">Jadwal</span>
                    </a>
                </li>
                <li>
                    <a href="user.php">
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
            <form class="search-form" action="" method="GET">
                <input type="text" name="search" placeholder="Cari...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <table class="lapangan-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID User</th>
                        <th>Nama</th>
                        <th>Password</th>
                        <th>Nomor Telepon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = $_GET['search'];
                        $sql_user = "SELECT * FROM data_user WHERE nama_user LIKE '%$search%'";
                    } else {
                        $sql_user = "SELECT * FROM data_user";
                    }

                    $result_user = $conn->query($sql_user);

                    if ($result_user->num_rows > 0) {
                        $nomor = 1;
                        while ($row = $result_user->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $nomor . "</td>";
                            echo "<td>" . $row["id_user"] . "</td>";
                            echo "<td>" . $row["nama_user"] . "</td>";
                            echo "<td>" . $row["password_user"] . "</td>";
                            echo "<td>" . $row["nomor_telepon_user"] . "</td>";
                            echo "<td>";
                            echo "<button class='btn-edit' onclick=\"window.location.href='edit_user.php?id=" . $row["id_user"] . "'\">Edit</button>";
                            echo "<button class='btn-hapus' onclick=\"konfirmasiHapus(" . $row["id_user"] . ")\">Hapus</button>";
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
                window.location.href = 'hapus_user.php?id=' + id;
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