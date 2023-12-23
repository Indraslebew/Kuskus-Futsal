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

// Fetch lapangan data using a prepared statement
$lapangan_query = "SELECT * FROM data_lapangan";
$lapangan_result = $conn->query($lapangan_query);

// Check if the query was successful
if (!$lapangan_result) {
    die("Error fetching lapangan data: " . $conn->error);
}

// Process form submission
if (isset($_POST['pesan_submit'])) {
    // Check if form data is being submitted

    $id_lapangan = $_POST['id_lpg'];
    $tgl_main = $_POST['tgl_jam_mulai'];
    $harga_lapangan = $_POST['harga'];
    $lama_main = isset($_POST['lama_main']) ? $_POST['lama_main'] : 0;

    // Retrieve id_user from the session
    $id_user = $_SESSION['id_user'];

    // Combine date and time into a single datetime string
    $datetime_main = "$tgl_main:00";

    // Define datetime_selesai
    $datetime_selesai = date("Y-m-d H:i:s", strtotime("$datetime_main +$lama_main hours"));

    // Check if the selected date is a holiday
    $checkHolidayQuery = "SELECT COUNT(*) as count 
                          FROM data_jadwal 
                          WHERE tanggal_libur = ?";

    $checkHolidayStmt = $conn->prepare($checkHolidayQuery);
    $checkHolidayStmt->bind_param("s", $tgl_main);
    $checkHolidayStmt->execute();
    $holidayCount = $checkHolidayStmt->get_result()->fetch_assoc()['count'];

    if ($holidayCount > 0) {
        // Date is a holiday
        echo "<script>alert('Maaf, tanggal yang dipilih sedang libur.'); window.location.href='lapangan.php';</script>";
        exit();
    }

    // Check if the selected slot is already booked
    $checkBookingProcedure = "CALL CheckBookingOverlap(?, ?, ?, ?, @overlap)";
    $checkBookingStmt = $conn->prepare($checkBookingProcedure);
    $checkBookingStmt->bind_param("isss", $id_lapangan, $tgl_main, $datetime_main, $datetime_selesai);
    $checkBookingStmt->execute();

    // Get the result of the stored procedure
    $selectOverlap = $conn->query("SELECT @overlap as overlap");
    $result = $selectOverlap->fetch_assoc();

    if ($result['overlap'] > 0) {
        echo "<script>alert('Maaf, jadwalnya sudah dipesan/Sedang Libur. Silakan pilih tanggal atau waktu lain.'); window.location.href='lapangan.php';</script>";
        exit();
    }

    // Calculate total biaya pemesanan
    $formatted_harga_lapangan = $harga_lapangan * 1000;
    $total_biaya_pemesanan = $lama_main * $formatted_harga_lapangan;

    // Default value for status_pembayaran_pemesanan
    $status_pembayaran = '-';

    // Insert the booking into data_pemesanan
    $insertBookingQuery = "INSERT INTO data_pemesanan 
                          (id_lapangan, id_user, waktu_main_pemesanan, harga_lapangan, total_biaya_pemesanan, status_pembayaran_pemesanan, waktu_selesai, lama_main, tanggal_main)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $insertBookingStmt = $conn->prepare($insertBookingQuery);
    $insertBookingStmt->bind_param("iisddssss", $id_lapangan, $id_user, $datetime_main, $formatted_harga_lapangan, $total_biaya_pemesanan, $status_pembayaran, $datetime_selesai, $lama_main, $tgl_main);

    if ($insertBookingStmt->execute()) {
        echo "<script>alert('Pemesanan berhasil.'); window.location.href='bayar.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal melakukan pemesanan. Silakan coba lagi.'); window.location.href='lapangan.php';</script>";
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Lapangan</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://unpkg.com/feather-icons"></script>
    <!-- Add these lines to include flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        thead {
            text-align: center;
        }

        th {
            padding: 10px;
        }
    </style>
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

    <section class="lapangan" id="lapangan">
        <div class="container">
            <main class="contain" data-aos="fade-right" data-aos-duration="1000">
                <h2 class="text-head">Lapangan di <span>Kuskus</span> Futsal </h2>
                <div class="row row-cols-1 row-cols-md-4">
                    <?php
                    while ($rowLapangan = $lapangan_result->fetch_assoc()) {
                        ?>
                        <div class="col mb-4">
                            <div class="card">
                                <!-- Add a hidden input field to store the id_lapangan -->
                                <input type="hidden" name="id_lapangan"
                                    value="<?= $rowLapangan["id_lapangan"]; ?>">
                                <img src="../admin/lapangan/gambar/<?= $rowLapangan["gambar_lapangan"]; ?>"
                                    height='270' alt="gambar lapangan"
                                    class="card-img-top">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <?= $rowLapangan["nama_lapangan"]; ?>
                                    </h5>
                                    <p class="card-text">Rp.
                                        <?= $rowLapangan["harga_lapangan"]; ?>
                                    </p>
                                    <button type="button" class="btn btn-inti" data-bs-toggle="modal"
                                        data-bs-target="#pesanModal<?= $rowLapangan["id_lapangan"]; ?>">Booking</button>
                                       <a href="jadwal.php?id_lapangan=<?= $rowLapangan["id_lapangan"]; ?>" type="button" class="btn btn-secondary">Jadwal</a>

                                </div>
                            </div>
                        </div>

                        <!-- Modal Pesan -->
                        <div class="modal fade" id="pesanModal<?= $rowLapangan["id_lapangan"]; ?>" tabindex="-1"
                            aria-labelledby="pesanModalLabel<?= $rowLapangan["id_lapangan"]; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="pesanModalLabel<?= $rowLapangan["id_lapangan"]; ?>">
                                            Pesan Lapangan
                                            <?= $rowLapangan["nama_lapangan"]; ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <form action="" method="post">
                                        <div class="modal-body">
                                            <!-- konten form modal -->
                                            <div class="row justify-content-center align-items-center">
                                                <div class="mb-3">
                                                    <img src="../admin/lapangan/gambar/<?= $rowLapangan["gambar_lapangan"]; ?>"
                                                        alt="gambar lapangan" class="card-img-top">
                                                </div>
                                                <div class="text-center">
                                                    <h6 name="harga">Harga : <?= $rowLapangan["harga_lapangan"]; ?></h6>
                                                </div>
                                                <div class="col">
                                                    <input type="hidden" name="id_lpg" class="form-control"
                                                        id="exampleInputPassword1"
                                                        value="<?= $rowLapangan["id_lapangan"]; ?>">
                                                    <input type="hidden" name="harga" class="form-control"
                                                        id="exampleInputPassword1"
                                                        value="<?= $rowLapangan["harga_lapangan"]; ?>">
                                                    <input type="hidden" name="lama_main" value="1"> <!-- Set the default value for lama_main -->
<div class="col">
    <div class="mb-3">
        <label for="tgl_jam_mulai" class="form-label">Tanggal & Jam Mulai</label>
        <input type="text" name="tgl_jam_mulai" class="form-control" id="tgl_jam_mulai" placeholder="Silahkan Klik Disini" required>
    </div>
</div>

<div class="col">
    <div class="mb-3">
        <label for="lama_main" class="form-label">Lama main (Jam)</label>
        <input type="number" name="lama_main" class="form-control" id="lama_main" min="1" value="1" max="12">
    </div>
</div>

<script>
    flatpickr("#tgl_jam_mulai", {
        enableTime: true,
        minTime: "07:00",
        maxTime: "19:00",
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        minuteIncrement: 60, // Set minute increment to 60 to allow only "00" minutes
        onClose: function(selectedDates, dateStr, instance) {
            const selectedTime = instance.selectedDates[0].getHours();

            // Calculate the maximum allowed duration based on the selected time
            const maxDuration = 19 - selectedTime;

            // Set the maximum duration for Lama main (Jam)
            document.getElementById("lama_main").max = maxDuration;

            // If the current duration exceeds the maximum, update it
            if (document.getElementById("lama_main").value > maxDuration) {
                document.getElementById("lama_main").value = maxDuration;
            }
        },
    });
</script>



                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-inti" name="pesan_submit">Pesan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </main>
            <p><br>keterangan :
                <br>Buka Setiap hari 24 Jam
                <br>Tanggal dan Jam yang tidak tersedia artinya full atau sedang libur
            </p>
        </div>
    </section>


    <footer><br>
        <h4>Follow Us on</h4>
        <div class="social">
            <a href="https://www.instagram.com/indraa.e_/"><i data-feather="instagram"></i></a>
            <a href="https://www.facebook.com/"><i data-feather="facebook"></i></a>
            <a href="https://twitter.com/"><i data-feather="twitter"></i></a>
            <a href="https://www.youtube.com/@tbz25newera84"><i data-feather="youtube"></i></a>
        </div>
    </footer>
    <!-- End Footer -->
    <!-- End Footer -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script>
        feather.replace();
    </script>
</body>

</html>

