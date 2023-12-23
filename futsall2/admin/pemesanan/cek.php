<?php
include '../../koneksi.php';

session_start();
if (!isset($_SESSION['id_admin'])) {
    echo '<script>alert("Mohon Login Dulu"); window.location.href = "../../login.php";</script>';
    exit(); // Stop execution if not logged in
}

include '../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_bukti_bayar'], $_POST['id_pemesanan'], $_POST['status'])) {
        $idBuktiBayar = $_POST['id_bukti_bayar'];
        $idPemesanan = $_POST['id_pemesanan'];
        $status = $_POST['status'];

        // Update status berdasarkan nilai yang diterima
        $update_query = "UPDATE data_pemesanan
                         SET status_pembayaran_pemesanan = '$status'
                         WHERE id_pemesanan = $idPemesanan";

        if ($conn->query($update_query) === TRUE) {
            echo 'success';
        } else {
            echo 'error';
        }

        $conn->close();
        exit();
    } else {
        echo 'error';
        exit();
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Cek Bayar</title>
    <link rel="stylesheet" type="text/css" href="cek.css">
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
            <table class="lapangan-table">
                <thead>
                    <tr>
                        <th>Id Bukti Bayar</th>
                        <th>Id Pemesanan</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Get the id_pemesanan from the query parameter
                    $pemesanan = isset($_GET['id_pemesanan']) ? $_GET['id_pemesanan'] : null;

                    // Check if id_pemesanan is set and numeric
                    if ($pemesanan !== null && is_numeric($pemesanan)) {
                        $sql = "SELECT * FROM data_bukti_bayar WHERE id_pemesanan = $pemesanan";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id_bukti_bayar'] . "</td>";
                                echo "<td>" . $row['id_pemesanan'] . "</td>";
                                echo "<td><img src='../../user/bayar/" . $row['gambar_bukti_bayar'] . "' height='100'></td>";
                                echo "<td>";
                                echo "<button class='btn-cek' onclick='updateStatus(" . $row['id_bukti_bayar'] . ', ' . $row['id_pemesanan'] . ", true)'>Lunas</button>";
                                echo "<button class='btn-cekk' onclick='updateStatus(" . $row['id_bukti_bayar'] . ', ' . $row['id_pemesanan'] . ", false)'>Tolak</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Tidak ada data bukti bayar.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Id pemesanan tidak valid.</td></tr>";
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
<script>

        function updateStatus(idBuktiBayar, idPemesanan, isLunas) {
            var xhr = new XMLHttpRequest();

            xhr.open('POST', 'cek.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 400) {
                    alert('Status berhasil diperbarui!');
                    window.location.href = 'pemesanan.php';
                } else {
                    alert('Terjadi kesalahan saat memperbarui status.');
                }
            };

            xhr.onerror = function () {
                alert('Terjadi kesalahan koneksi.');
            };

            // Mengirim isLunas sebagai parameter
            xhr.send('id_bukti_bayar=' + idBuktiBayar + '&id_pemesanan=' + idPemesanan + '&status=' + (isLunas ? 'Lunas' : 'Di Tolak'));
        }


    </script>
</body>

</html>