<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../koneksi.php';

// Redirect to login page if 'id_user' is not set in the session
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data using a prepared statement
$sqlUser = "SELECT nama_user, nomor_telepon_user FROM data_user WHERE id_user = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $_SESSION['id_user']);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

// Check if the query was successful
if (!$resultUser) {
    // Handle the error if the query was not successful
    die("Error fetching user data: " . $conn->error);
}

// Fetch the user data
$userData = $resultUser->fetch_assoc();

// Set default values if user data is not available
$nama_user = $userData['nama_user'] ?? "Unknown";
$nomor_telepon_user = $userData['nomor_telepon_user'] ?? "N/A";

// Fetch lapangan data using a prepared statement
$lapangan_query = "SELECT * FROM data_lapangan";
$lapangan_result = $conn->query($lapangan_query);

// Check if the query was successful
if (!$lapangan_result) {
    die("Error fetching lapangan data: " . $conn->error);
}

// ... (existing code)

// Check if the form is submitted
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["pesan_submit"])) {
    // Retrieve form data
    $id_user = $_SESSION['id_user'];
    $id_lapangan = $_POST["id_lapangan"];
    $waktu_main_pemesanan = count($_POST["jam_mulai"]);
    $harga_lapangan = $_POST["harga_lapangan"];

    // Extract the selected id_jadwal from the posted array
    $hari_main = $_POST["hari_main"];
    $id_jadwal = !empty($hari_main) ? (int) $hari_main[0] : 0;

    // Remove non-numeric characters from harga_lapangan
    $harga_lapangan = (int) preg_replace('/[^0-9]/', '', $harga_lapangan);

    // Calculate total_biaya_pemesanan
    $total_biaya_pemesanan = $harga_lapangan * $waktu_main_pemesanan;

    // Your SQL query to insert data into the data_pemesanan table
    $insert_query = "INSERT INTO data_pemesanan (id_user, id_lapangan, id_jadwal, waktu_main_pemesanan, total_biaya_pemesanan, status_pembayaran_pemesanan)
                     VALUES (?, ?, ?, ?, ?, '-')";

    // Prepare the statement
    $stmt = $conn->prepare($insert_query);

    // Bind parameters
    $stmt->bind_param("iiids", $id_user, $id_lapangan, $id_jadwal, $waktu_main_pemesanan, $total_biaya_pemesanan);

    // Execute the statement
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Successful insertion
        echo "<script>
          alert('Berhasil DiPesan');
          document.location.href = 'bayar.php';
          </script>";
    } else {
        // Error in the query
        echo "Error in booking: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
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
                                <a href="../index.php" class="btn btn-danger">Logout</a>
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
                    while ($rowLapangan = $lapangan_result->fetch_assoc()):
                        // Fetch jadwal data using a prepared statement
                        $jadwal_query = "SELECT id_jadwal, hari_jadwal FROM data_jadwal WHERE id_jadwal BETWEEN 14 AND 21 AND status_jadwal = 'Tersedia'";
                        $jadwal_result = $conn->query($jadwal_query);

                        // Check if the query was successful
                        if (!$jadwal_result) {
                            die("Error fetching jadwal data: " . $conn->error);
                        }
                        ?>
                        <div class="col">
                            <div class="card">
                                <!-- Add a hidden input field to store the id_lapangan -->
                                <input type="hidden" name="id_lapangan" value="<?= $rowLapangan["id_lapangan"]; ?>">
                                <img src="../admin/lapangan/gambar/<?= $rowLapangan["gambar_lapangan"]; ?>" height='270'
                                    alt="gambar lapangan" class="card-img-top">
                                <div class="card-body text-center">
                                    <h5 class="card-title">
                                        <?= $rowLapangan["nama_lapangan"]; ?>
                                    </h5>
                                    <p class="card-text">Rp.
                                        <?= $rowLapangan["harga_lapangan"]; ?>
                                    </p>
                                    <button type="button" class="btn btn-inti" data-bs-toggle="modal"
                                        data-bs-target="#pesanModal<?= $rowLapangan["id_lapangan"]; ?>">Booking</button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Pesan -->
                        <div class="modal fade" id="pesanModal<?= $rowLapangan["id_lapangan"]; ?>" tabindex="-1"
                            aria-labelledby="pesanModalLabel<?= $rowLapangan["id_lapangan"]; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="pesanModalLabel<?= $rowLapangan["id_lapangan"]; ?>">
                                            Pesan Lapangan
                                            <?= $rowLapangan["nama_lapangan"]; ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <?php
                                    // Fetch jadwal data using a prepared statement
                                    $jadwal_query = "SELECT id_jadwal, hari_jadwal, status_jadwal, jam_0800, jam_0900, jam_1000, jam_1100, jam_1200, jam_1300, jam_1400 FROM data_jadwal WHERE id_jadwal BETWEEN 14 AND 21 AND status_jadwal = 'Tersedia'";
                                    $jadwal_result = $conn->query($jadwal_query);

                                    // Check if the query was successful
                                    if (!$jadwal_result) {
                                        die("Error fetching jadwal data: " . $conn->error);
                                    }
                                    ?>
                                    <form action="" method="post">
                                        <div class="modal-body">
                                            <input type="hidden" name="id_lapangan"
                                                value="<?= $rowLapangan["id_lapangan"]; ?>">
                                            <input type="hidden" name="id_jadwal" value="<?= $rowJadwal["id_jadwal"]; ?>">
                                            <input type="hidden" name="harga_lapangan"
                                                value="<?= $rowLapangan["harga_lapangan"]; ?>">
                                            <div class="row justify-content-center align-items-center">
                                                <div class="mb-3">
                                                    <img src="../admin/lapangan/gambar/<?= $rowLapangan["gambar_lapangan"]; ?>"
                                                        alt="gambar lapangan" class="card-img-top">
                                                </div>
                                                <div class="text-center">
                                                    <h6 name="harga">Harga : Rp.
                                                        <?= $rowLapangan["harga_lapangan"]; ?>
                                                    </h6>
                                                </div>
                                                <div class="col">
                                                    <input type="hidden" name="id_lpg" class="form-control"
                                                        value="<?= $rowLapangan["id_lapangan"]; ?>">
                                                    <table class="table" align="text-center">
                                                        <tr>
                                                            <th scope="col">Hari</th>
                                                            <th scope="col">Jam</th>
                                                        </tr>
                                                        <?php
                                                        $jadwal_result->data_seek(0);
                                                        while ($jadwal = $jadwal_result->fetch_assoc()):
                                                            $isJadwalAvailable = ($jadwal['status_jadwal'] == 'Tersedia');
                                                            ?>
                                                            <?php if ($isJadwalAvailable): ?>
                                                                <tr>
                                                                    <td>
                                                                        <div class="form-check">
                                                                            <!-- Inside the loop where you display checkboxes -->
                                                                            <input class="form-check-input" type="radio"
                                                                                name="hari_main[]"
                                                                                value="<?= $jadwal['id_jadwal']; ?>"
                                                                                id="<?= $jadwal['id_jadwal']; ?>"
                                                                                onchange="enableJamMulai(this)">

                                                                            <label class="form-check-label"
                                                                                for="<?= $jadwal['id_jadwal']; ?>">
                                                                                <?= $jadwal['hari_jadwal']; ?>
                                                                            </label>
                                                                        </div>

                                                                    </td>
                                                                    <td>
                                                                        <div class="form-check">
                                                                            <?php
                                                                            $jamMulaiArray = array("jam_0800", "jam_0900", "jam_1000", "jam_1100", "jam_1200", "jam_1300", "jam_1400");
                                                                            ?>
                                                                            <div class="form-group">
                                                                                <?php foreach ($jamMulaiArray as $jamMulai): ?>
                                                                                    <?php
                                                                                    $isKeyAvailable = array_key_exists($jamMulai, $jadwal);
                                                                                    $isAvailable = ($isKeyAvailable && $jadwal[$jamMulai] == 'Tersedia');
                                                                                    ?>
                                                                                    <?php if ($isAvailable): ?>
                                                                                        <div class="form-check form-check-inline">
                                                                                            <!-- Inside the loop where you display time checkboxes -->
<input class="form-check-input" type="checkbox" name="jam_mulai[]" value="<?= $jamMulai; ?>" id="<?= $jamMulai . '_' . $jadwal['id_jadwal']; ?>" data-day="<?= $jadwal['id_jadwal']; ?>" disabled>
<label class="form-check-label" for="<?= $jamMulai . '_' . $jadwal['id_jadwal']; ?>">
    <?= substr_replace(substr($jamMulai, 4), ":", 2, 0); ?>
</label>

                                                                                        </div>
                                                                                    <?php endif; ?>
                                                                                <?php endforeach; ?>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        <?php endwhile; ?>
                                                    </table>
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
                    <?php endwhile; ?>
                </div>
            </main>
        </div>
    </section>

    <!-- footer -->
    <footer><br>
        <h4>Follow Us on</h4>
        <div class="social">
            <a href="#"><i data-feather="instagram"></i></a>
            <a href="#"><i data-feather="facebook"></i></a>
            <a href="#"><i data-feather="twitter"></i></a><br>
            -------------------------------------------------------
        </div>

        <div class="links">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#bayar">Tata Cara</a>
            <a href="#contact">Kontak </a>
        </div>

    </footer>
    <!-- End Footer -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script>
        feather.replace();
    </script>
    <script>
        
function enableJamMulai(radio) {
    var selectedDayId = radio.value;

    // Disable all time checkboxes
    var allJamMulaiCheckboxes = document.querySelectorAll('input[name="jam_mulai[]"]');
    allJamMulaiCheckboxes.forEach(function (jamMulaiCheckbox) {
        jamMulaiCheckbox.setAttribute('disabled', 'disabled');
    });

    // Enable time checkboxes for the selected day
    if (radio.checked) {
        var jamMulaiCheckboxes = document.querySelectorAll('input[name="jam_mulai[]"][data-day="' + selectedDayId + '"]');
        jamMulaiCheckboxes.forEach(function (jamMulaiCheckbox) {
            jamMulaiCheckbox.removeAttribute('disabled');
        });
    }
}



        function validateForm() {
            var checkboxes = document.querySelectorAll('input[name="hari_main[]"]');
            var checked = Array.prototype.slice.call(checkboxes).filter(chk => chk.checked);
            if (checked.length !== 1) {
                alert("Pilih satu hari saja!");
                return false;
            }
            return true;
        }
    </script>


    <script>
        // JavaScript to handle dynamic changes based on selected day
        document.addEventListener('DOMContentLoaded', function () {
            const hariMainSelect = document.getElementById('hari_main');
            const jamMulaiContainer = document.getElementById('jam_mulai_container');

            // Define availableDays using PHP to JavaScript conversion
            const availableDays = <?= json_encode($availableDays); ?>;

            hariMainSelect.addEventListener('change', function () {
                // Clear previous options
                jamMulaiContainer.innerHTML = '';

                // Get the selected day
                const selectedDay = hariMainSelect.value;

                // Find the selected day in the availableDays array
                const selectedDayObject = availableDays.find(day => day.id_jadwal === parseInt(selectedDay));

                if (selectedDayObject) {
                    // Extract corresponding jam columns from the selected day object
                    const jamColumns = Object.keys(selectedDayObject).filter(key => key.startsWith('jam_'));

                    // Insert new checkboxes for selected day
                    jamColumns.forEach(function (jamColumn) {
                        const checkbox = document.createElement('div');
                        checkbox.classList.add('form-check');
                        checkbox.innerHTML = `
            <input class='form-check-input' type='checkbox' name='jam_mulai[]' value='${jamColumn}' id='${jamColumn}'>
            <label class='form-check-label' for='${jamColumn}'>${getJamLabel(jamColumn)}</label>
          `;
                        jamMulaiContainer.appendChild(checkbox);
                    });
                }
            });

            // Helper function to get label for jam column
            function getJamLabel(jamColumn) {
                // Extract hour from jam column name
                const hour = parseInt(jamColumn.replace('jam_', ''), 10);
                return `${hour}:00`;
            }
        });
    </script>

</body>

</html>