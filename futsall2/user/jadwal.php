<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../koneksi.php';

// Redirect to login page if 'id_user' is not set in the session
if (!isset($_SESSION['id_user'])) {
    echo "<script>
              alert('Mohon Login Dulu');
              document.location.href = '../login.php';
              </script>";
    exit();
}

?>


<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Jadwal Lapangan</title>
  <link rel="stylesheet" href="../style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>
      <?php
    session_start();
    include '../koneksi.php';
    $id_user = $_SESSION['id_user'];

    // Menampilkan pesan selamat datang untuk admin yang login
    $sql = "SELECT * FROM data_user WHERE id_user = $id_user";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    ?>
    <!-- Navbar -->
    <div class="container ">
        <nav class="navbar fixed-top bg-body-secondary navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="../12.png" alt="Logo" width="80" height="50" class="d-inline-block align-text-top">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item ">
                            <a class="nav-link active" aria-current="page" href="../index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="lapangan.php">Lapangan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="bayar.php">Pembayaran</a>
                        </li>
                    </ul>
                    <?php
                    if (isset($_SESSION['id_user'])) {
                        // jika user telah login, tampilkan tombol profil dan sembunyikan tombol login
                        echo '<a href="user/profil.php" data-bs-toggle="modal" data-bs-target="#profilModal" class="btn btn-inti"><i data-feather="user"></i></a>';
                    } else {
                        // jika user belum login, tampilkan tombol login dan sembunyikan tombol profil
                        echo '<a href="login.php" class="btn btn-inti" type="submit">Login</a>';
                    }
                    ?>
                </div>
            </div>
        </nav>
    </div>
    <!-- End Navbar -->

    <!-- Modal Profil -->
    <div class="modal fade" id="profilModal" tabindex="-1" aria-labelledby="profilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profilModalLabel">Profil Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-8">
                                <h5 class="mb-3">Nama :
                                    <?= $row["nama_user"]; ?>
                                </h5>
                                <p>Nomor Telp :
                                    <?= $row["nomor_telepon_user"]; ?>
                                </p><br>
                                <a href="../logout.php" class="btn btn-danger">Logout</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Profil -->



    <section class="lapangan mb-5" id="lapangan">
        <div class="container-fluid">
            <h2 class="text-head mb-3"><span>Jadwal</span> Libur </h2>

            <form action="" method="post" class="px-4 mb-3">

                <table class="table my-4">

                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">ID Jadwal</th>
                            <th scope="col">Tanggal Libur</th>
                            <th scope="col">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Assume you have a database connection established
                       include "koneksi.php";

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Fetch data from data_jadwal
                        $result = $conn->query("SELECT * FROM data_jadwal");

                        if ($result->num_rows > 0) {
                            $i = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<th scope='row'>$i</th>";
                                echo "<td>" . $row["id_jadwal"] . "</td>";
                                echo "<td>" . $row["tanggal_libur"] . "</td>";
                                echo "<td>" . $row["keterangan"] . "</td>";
                                echo "</tr>";
                                $i++;
                            }
                        } else {
                            echo "<tr><td colspan='4'>No records found</td></tr>";
                        }

                       
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
    </section>

    <!-- Pemesanan Section -->
<section class="pemesanan mb-5" id="pemesanan">
    <div class="container-fluid">
        <h2 class="text-center text-head mb-3">
            <span style="color: #9CD203;">Jadwal</span> Pemesanan
        </h2>

        <form action="" method="post" class="px-4 mb-3">

            <table class="table my-4">

                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Id</th>
                        <th scope="col">Id User</th>
                        <th scope="col">Lapangan</th>
                        <th scope="col">Mulai</th>
                        <th scope="col">Selesai</th>
                        <th scope="col">Lama Main</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    error_reporting(E_ALL);
                    ini_set('display_errors', 1);

                    include '../koneksi.php';

                    // Redirect to login page if 'id_user' is not set in the session
                    if (!isset($_SESSION['id_user'])) {
                        echo "<script>
                            alert('Mohon Login Dulu');
                            document.location.href = '../login.php';
                            </script>";
                        exit();
                    }

                    // Retrieve id_lapangan from URL parameters
                    $id_lapangan = isset($_GET['id_lapangan']) ? $_GET['id_lapangan'] : null;

                    // Your SQL query using prepared statement to avoid SQL injection
                    $sql = "SELECT p.*, l.nama_lapangan 
                            FROM data_pemesanan p
                            JOIN data_lapangan l ON p.id_lapangan = l.id_lapangan
                            WHERE p.id_lapangan = ? AND p.status_pembayaran_pemesanan != 'Di Tolak'";

                    // Prepare and bind the statement
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id_lapangan);

                    // Execute the query
                    $stmt->execute();

                    // Fetch the results
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $i = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<th scope='row'>$i</th>";
                            echo "<td>" . $row["id_pemesanan"] . "</td>";
                            echo "<td>" . $row["id_user"] . "</td>";
                            echo "<td>" . $row["nama_lapangan"] . "</td>";
                            echo "<td>" . $row["waktu_main_pemesanan"] . "</td>";
                            echo "<td>" . $row["waktu_selesai"] . "</td>";
                            echo "<td>" . $row["lama_main"] . " Jam</td>";
                            echo "</tr>";
                            $i++;
                        }
                    } else {
                        echo "<tr><td colspan='4'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </div>
</section>
<!-- End Pemesanan Section -->




  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
  <script>
    feather.replace();
  </script>
</body>

</html>