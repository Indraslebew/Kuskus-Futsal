<?php
include '../../koneksi.php';

session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['simpan'])) {
        $statuses = $_POST['status'];

        $availableStatus = array_keys($statuses);

        $sql = "UPDATE data_jadwal SET status_jadwal='Tidak Tersedia'";

        if (!empty($availableStatus)) {
            $ids = implode(",", $availableStatus);
            $sql .= " WHERE id_jadwal NOT IN ($ids)";
        }

        $conn->query($sql);

        foreach ($statuses as $id_jadwal => $status) {
            $status = isset($status) ? 'Tersedia' : 'Tidak Tersedia';
            $sql = "UPDATE data_jadwal SET status_jadwal='$status' WHERE id_jadwal='$id_jadwal'";
            $conn->query($sql);
        }
    }
}

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
                    <a href="#" class="logout" onclick="konfirmasiLogout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="nav-item">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="main">
            <form method="POST" action="">
                <table class="lapangan-table">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Status</th>
                            <th>Jam</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["hari_jadwal"] . "</td>";
                                echo "<td><input type='checkbox' name='status[" . $row["id_jadwal"] . "]' value='Tersedia' " . ($row["status_jadwal"] == 'Tersedia' ? 'checked' : '') . "></td>";
                                echo "<td>
                                        <form method='POST' action='jam.php'>
                                            <input type='hidden' name='id_jadwal' value='" . $row["id_jadwal"] . "'>
                                            <input type='submit' class='btn-cek' name='pilih' value='Pilih'>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Tidak ada jadwal.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <button type="submit" name="simpan">Simpan Perubahan</button>
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

</body>

</html>