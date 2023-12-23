<?php
include '../../koneksi.php';

session_start();
if (!isset($_SESSION['id_admin'])) {
    echo '<script>alert("Mohon Login Dulu"); window.location.href = "../../login.php";</script>';
    exit(); // Stop execution if not logged in
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add your logic to handle form submission and database insertion here
    // Example: Insert data into the database
    $tanggalLibur = $_POST['tanggal_libur'];
    $keterangan = $_POST['keterangan'];

    // Perform database insertion
    $sqlInsert = "INSERT INTO data_jadwal (id_admin, tanggal_libur, keterangan) VALUES (1, '$tanggalLibur', '$keterangan')";

    $resultInsert = $conn->query($sqlInsert);

    if ($resultInsert) {
        echo '<script>alert("Data berhasil ditambahkan."); window.location.href = "jadwal.php";</script>';
        exit();
    } else {
        echo '<script>alert("Gagal menambahkan data. Silakan coba lagi.");</script>';
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700");

        form {
            max-width: 1200px;
            background-color: #fff;
            padding: 10px;
            margin: 0;
            border: 1px solid #ccc;
        }

        table {
            width: 50%;
        }

        td {
            padding: 8px;
        }

        input[type="text"],
        input[type="file"] {
            padding: 5px;
        }

        input[type="submit"],
        input[type="reset"] {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            margin-right: 10px;
        }

        input[type="reset"] {
            background-color: #e74c3c;
            color: white;
        }

        input[type="submit"]:hover {
            filter: brightness(90%);
        }

        input[type="reset"]:hover {
            filter: brightness(90%);
        }

        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700");

        * {
            margin: 0;
            padding: 0;
            outline: none;
            border: none;
            text-decoration: none;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: #F0F4F7;
        }

        .container {
            display: flex;
        }

        nav {
            position: relative;
            top: 0;
            bottom: 0;
            height: 100vh;
            left: 0;
            background: #39424E;
            width: 280px;
            overflow: hidden;
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.1);
        }

        a {
            position: relative;
            color: #E6E6E6;
            font-size: 14px;
            display: table;
            width: 280px;
            padding: 10px;
            transition: background 0.3s;

        }

        .logo {
            text-align: center;
            display: flex;
            margin: 10px 0 0 10px;
        }

        .logo img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
        }

        .logo span {
            font-weight: bold;
            padding-left: 15px;
            font-size: 18px;
            text-transform: uppercase;
            color: #E6E6E6;
        }


        nav .fas {
            position: relative;
            width: 70px;
            height: 40px;
            top: 14px;
            font-size: 20px;
            text-align: center;
            color: #E6E6E6;
        }

        .nav-item {
            position: relative;
            top: 12px;
            margin-left: 10px;
        }

        a:hover {
            background: #546E7A;
        }

        .logout {
            position: absolute;
            bottom: 0;
        }

        .main {
            position: relative;
            padding: 20px;
            width: calc(100% - 280px);
        }


        .lapangan-table {
            width: 100%;
            border: 4px solid #ccc;
            border-collapse: collapse;
        }

        thead {
            background: #39424E;
            color: #E6E6E6;
            border-bottom: 4px solid #ccc;
        }

        thead th {
            padding: 8px 15px;
            text-align: center;
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

            <form method="POST" action="" enctype="multipart/form-data">
                <table>
                    <tr>
                        <td>Tanggal Libur </td>
                        <td>: <input type="date"  id="tanggal_libur" name="tanggal_libur" required></td>
                    </tr>
                    <tr>
                        <td>Keterangan</td>
                        <td>: <input type="text" id="keterangan" name="keterangan" required></td>
                    </tr>
                    
                    <tr>
                        <td colspan="2">
                            <input type="submit" name="submit" value="Tambahkan">
                            <input type="reset" name="reset" value="Reset">
                        </td>
                    </tr>
                </table>
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