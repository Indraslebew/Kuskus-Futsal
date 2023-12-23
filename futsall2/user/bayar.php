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

$jmlHalamanPerData = 5;

// Fetch the total number of rows from the data_pemesanan table
$sqlCount = "SELECT COUNT(id_pemesanan) AS total_rows FROM data_pemesanan";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();

if ($resultCount) {
    $rowCount = $resultCount->fetch_assoc();
    $jumlahData = $rowCount['total_rows'];
} else {
    // Handle the error if the query was not successful
    die("Error fetching total number of rows: " . $stmtCount->error);
}

$jmlHalaman = ceil($jumlahData / $jmlHalamanPerData);

if (isset($_GET["halaman"])) {
    $halamanAktif = $_GET["halaman"];
} else {
    $halamanAktif = 1;
}

$awalData = ($jmlHalamanPerData * $halamanAktif) - $jmlHalamanPerData;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bayar_aja'])) {
    // Get data from the form
    $id_pemesanan = isset($_POST['id_pemesanan']) ? $_POST['id_pemesanan'] : null;

    // Fetch data_pemesanan using a prepared statement for the specific user
    $sqlPemesanan = "SELECT * FROM data_pemesanan WHERE id_user = ?";
    $stmtPemesanan = $conn->prepare($sqlPemesanan);
    $stmtPemesanan->bind_param("i", $_SESSION['id_user']);
    $stmtPemesanan->execute();
    $resultPemesanan = $stmtPemesanan->get_result();

    // Check if the query was successful
    if (!$resultPemesanan) {
        // Handle the error if the query was not successful
        die("Error fetching pemesanan data: " . $conn->error);
    }

    // Fetch the specific reservation data
    $rowPemesanan = $resultPemesanan->fetch_assoc();

    // Check if an image is uploaded
    if (!empty($_FILES['foto']['name'])) {
        // Check if the file has a valid extension
        $allowed_extensions = array('jpg', 'jpeg', 'png');
        $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            // Move the uploaded file to the 'bayar' folder
            $upload_folder = 'bayar/';
            $gambar_bukti_bayar = $_FILES['foto']['name'];
            $target_path = $upload_folder . $gambar_bukti_bayar;

            move_uploaded_file($_FILES['foto']['tmp_name'], $target_path);

            // Insert data into the data_bukti_bayar table
            $sqlInsert = "INSERT INTO data_bukti_bayar (id_user, id_pemesanan, gambar_bukti_bayar) VALUES (?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);

            // Bind parameters
            $stmtInsert->bind_param("iis", $_SESSION['id_user'], $id_pemesanan, $gambar_bukti_bayar);

            // Execute the statement
            if ($id_pemesanan !== null && $stmtInsert->execute()) {
                echo '<script>alert("Pembayaran berhasil!");</script>';

                // After successful payment, you may want to update the status in the data_pemesanan table
                // Set the status to 'Sudah Bayar' or any appropriate status
                $statusPembayaran = 'Sudah Bayar';
                $sqlUpdateStatus = "UPDATE data_pemesanan SET status_pembayaran_pemesanan = ? WHERE id_pemesanan = ?";
                $stmtUpdateStatus = $conn->prepare($sqlUpdateStatus);
                $stmtUpdateStatus->bind_param("si", $statusPembayaran, $id_pemesanan);

                if ($stmtUpdateStatus->execute()) {
                    // Success
                } else {
                    // Handle the error if the update fails
                }

                // Close the update statement
                $stmtUpdateStatus->close();
            } else {
                echo '<script>alert("Pembayaran gagal!");</script>';
            }

            // Close the statement
            $stmtInsert->close();
        } else {
            // Alert if the file extension is not allowed
            echo '<script>alert("Ekstensi file tidak diizinkan. Gunakan jpg, jpeg, atau png.");</script>';
        }
    } else {
        // Alert if no image is uploaded
        echo '<script>alert("Masukkan gambar terlebih dahulu!");</script>';
    }

    // Close the statement
    $stmtPemesanan->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hapus_aja'])) {
    // Get data from the form
    $id_pemesanan_to_delete = isset($_POST['id_pemesanan_to_delete']) ? $_POST['id_pemesanan_to_delete'] : null;

    // Check if the reservation status is 'Di Tolak'
    $sqlCheckStatus = "SELECT status_pembayaran_pemesanan FROM data_pemesanan WHERE id_pemesanan = ?";
    $stmtCheckStatus = $conn->prepare($sqlCheckStatus);
    $stmtCheckStatus->bind_param("i", $id_pemesanan_to_delete);
    $stmtCheckStatus->execute();
    $resultCheckStatus = $stmtCheckStatus->get_result();

    if ($resultCheckStatus) {
        $rowCheckStatus = $resultCheckStatus->fetch_assoc();
        $statusPembayaran = $rowCheckStatus['status_pembayaran_pemesanan'];

        // If status is 'Di Tolak', update the status to 'Ditolak' or any appropriate value
        if ($statusPembayaran == 'Di Tolak') {
            $updateStatus = "UPDATE data_pemesanan SET status_pembayaran_pemesanan = 'Ditolak' WHERE id_pemesanan = ?";
            $stmtUpdateStatus = $conn->prepare($updateStatus);
            $stmtUpdateStatus->bind_param("i", $id_pemesanan_to_delete);

            if ($stmtUpdateStatus->execute()) {
                $deleteMessage = "Data berhasil ditandai sebagai Ditolak!";
            } else {
                $deleteMessage = "Gagal menandai data!";
            }

            $stmtUpdateStatus->close();
        } else {
            // If the status is not 'Di Tolak', proceed with the physical deletion
            $sqlDelete = "DELETE FROM data_pemesanan WHERE id_pemesanan = ?";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bind_param("i", $id_pemesanan_to_delete);

            if ($stmtDelete->execute()) {
                $deleteMessage = "Data berhasil dihapus!";
            } else {
                $deleteMessage = "Gagal menghapus data!";
            }

            $stmtDelete->close();
        }
    } else {
        $deleteMessage = "Gagal mengecek status pemesanan!";
    }

    $stmtCheckStatus->close();
}



?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pembayaran</title>
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


    <section class="lapangan_212279 mb-5" id="lapangan_212279">
        <div class="container-fluid">

        <h2 class="text-center text-head mb-3">
  <span style="color: #9CD203;">Pembayaran</span> Lapangan
</h2>
            <form action="" method="post" enctype="multipart/form-data" class="px-4">
                <table class="table my-5">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Id</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Lapangan</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Mulai</th>
                            <th scope="col">Selesai</th>
                            <th scope="col">Lama Main</th>
                            <th scope="col">Total</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <!-- Existing HTML code before tbody -->

                            <!-- Existing HTML code before tbody -->

<tbody>
<?php
// Fetch data_pemesanan using a prepared statement
$sqlPemesanan = "SELECT * FROM data_pemesanan WHERE id_user = ?";
$stmtPemesanan = $conn->prepare($sqlPemesanan);
$stmtPemesanan->bind_param("i", $_SESSION['id_user']);
$stmtPemesanan->execute();
$resultPemesanan = $stmtPemesanan->get_result();

// Check if the query was successful
if (!$resultPemesanan) {
    // Handle the error if the query was not successful
    die("Error fetching pemesanan data: " . $conn->error);
}


// Loop through the result set and display data in the table
$rowNumber = 1;
while ($rowPemesanan = $resultPemesanan->fetch_assoc()) {
    echo "<tr>";
    echo "<th>{$rowNumber}</th>";
    echo "<th>{$rowPemesanan['id_pemesanan']}</th>";

    // Fetch user data for the current reservation
    $id_user = $rowPemesanan['id_user'];
    $sqlUser = "SELECT nama_user FROM data_user WHERE id_user = ?";
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->bind_param("i", $id_user);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    $rowUser = $resultUser->fetch_assoc();
    $nama_user = $rowUser['nama_user'];

    echo "<td>{$nama_user}</td>";

    // Fetch lapangan data for the current reservation
    $id_lapangan = $rowPemesanan['id_lapangan'];
    $sqlLapangan = "SELECT nama_lapangan FROM data_lapangan WHERE id_lapangan = ?";
    $stmtLapangan = $conn->prepare($sqlLapangan);
    $stmtLapangan->bind_param("i", $id_lapangan);
    $stmtLapangan->execute();
    $resultLapangan = $stmtLapangan->get_result();
    $rowLapangan = $resultLapangan->fetch_assoc();
    $nama_lapangan = $rowLapangan['nama_lapangan'];

    echo "<td>{$nama_lapangan}</td>";

    // Fetch harga lapangan data for the current reservation
    $sqlHarga = "SELECT harga_lapangan FROM data_lapangan WHERE id_lapangan = ?";
    $stmtHarga = $conn->prepare($sqlHarga);
    $stmtHarga->bind_param("i", $id_lapangan);
    $stmtHarga->execute();
    $resultHarga = $stmtHarga->get_result();
    $rowharga = $resultHarga->fetch_assoc();

// Sisa bagian-bagian yang tidak berubah
echo "<td>Rp. " . number_format($rowPemesanan['harga_lapangan'], 0, ',', '.') . "</td>";
echo "<td>{$rowPemesanan['waktu_main_pemesanan']}</td>";
echo "<td>{$rowPemesanan['waktu_selesai']}</td>";
echo "<td>{$rowPemesanan['lama_main']} jam</td>";
echo "<td>Rp. " . number_format($rowPemesanan['total_biaya_pemesanan'], 0, ',', '.') . "</td>";

echo "<td>{$rowPemesanan['status_pembayaran_pemesanan']}</td>";
echo "<td>";

// Check if payment is successful
if ($rowPemesanan['status_pembayaran_pemesanan'] == 'Sudah Bayar' || $rowPemesanan['status_pembayaran_pemesanan'] == 'Lunas') {
    // If successful, display the "Detail" button
    echo '<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detailModal' . $rowPemesanan['id_pemesanan'] . '">Detail</button>';
} else if ($rowPemesanan['status_pembayaran_pemesanan'] == 'Di Tolak') {
    echo '<a href="#" class="btn btn-danger delete-btn" onclick="konfirmasiHapus(' . $rowPemesanan['id_pemesanan'] . ')" data-id="' . $rowPemesanan['id_pemesanan'] . '">Hapus</a>';
} else {
    // If not successful, display the "Bayar" and "Hapus" buttons
    echo '<button type="button" class="btn btn-inti" data-bs-toggle="modal" data-bs-target="#bayarModal' . $rowPemesanan['id_pemesanan'] . '">Bayar</button>';
    echo '<a href="#" class="btn btn-danger delete-btn" onclick="konfirmasiHapus(' . $rowPemesanan['id_pemesanan'] . ')" data-id="' . $rowPemesanan['id_pemesanan'] . '">Hapus</a>';
}

echo "</td>";
echo "</tr>";
$rowNumber++;

                            // Modal Bayar
                            echo '<div class="modal fade" id="bayarModal' . $rowPemesanan['id_pemesanan'] . '" tabindex="-1" role="dialog" aria-labelledby="bayarModalLabel" aria-hidden="true">';

                            echo '    <div class="modal-dialog modal-dialog-centered">';
                            echo '        <div class="modal-content">';
                            echo '            <div class="modal-header">';
                            echo '                <h5 class="modal-title">Bayar Lapangan </h5>';
                            echo '                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                            echo '            </div>';
                            echo '            <form action="" method="post" enctype="multipart/form-data">';
                            echo '<input type="hidden" name="id_pemesanan" value="' . (isset($rowPemesanan["id_pemesanan"]) ? $rowPemesanan["id_pemesanan"] : '') . '">';


                            echo '                <div class="modal-body">';
                            echo '                    <!-- konten form modal -->';
                            echo '                    <div class="row justify-content-center align-items-center">';

                            // ID Pemesanan
                            echo '                    <div class="col">';
                            echo '                        <div class="mb-3">';
                            echo '                            <label for="exampleInputPassword1" class="form-label">ID Pemesanan</label>';
                            echo '                            <input type="text" name="tgl_main" class="form-control" id="exampleInputPassword1" value="' . $rowPemesanan['id_pemesanan'] . '" disabled>';
                            echo '                        </div>';
                            echo '                    </div>';

                            // Nama User
                            echo '                    <div class="col">';
                            echo '                        <div class="mb-3">';
                            echo '                            <label for="exampleInputPassword1" class="form-label">Nama User</label>';
                            echo '                            <input type="text" name="nama_user" class="form-control" id="exampleInputPassword1" value="' . $rowUser['nama_user'] . '" disabled>';
                            echo '                        </div>';
                            echo '                    </div>';

                            // Harga Lapangan
                            echo '                    <div class="col">';
                            echo '                        <div class="mb-3">';
                            echo '                            <label for="exampleInputPassword1" class="form-label">Harga Lapangan</label>';
                            echo '                            <input type="text" name="harga_lapangan" class="form-control" id="exampleInputPassword1" value="' . $rowharga['harga_lapangan'] . '" disabled>';
                            echo '                        </div>';
                            echo '                    </div>';

                            echo '                    <div class="input-group">';
                            echo '                        <div class="input-group-prepend border border-danger">';
                            echo '                            <span class="input-group-text">Total</span>';
                            echo '                        </div>';
                            echo '                        <input type="number" name="total" class="form-control border border-danger" id="exampleInputPassword1" value="' . number_format($rowPemesanan['total_biaya_pemesanan'], 0, ',', '.') . '" disabled>';
                            echo '                    </div>';


                            echo '                </div>';
                            echo '                <div class="mt-3">';
                            echo '                    <label for="exampleInputPassword1" class="form-label">Transfer ke : BRI 0892322132 a/n Indra</label>';
                            echo '                </div>';
                            echo '                <div class="mt-3">';
                            echo '                    <label for="exampleInputPassword1" class="form-label">Upload Bukti</label>';
                            echo '                    <input type="file" name="foto" class="form-control" id="exampleInputPassword1">';
                            echo '                </div>';
                            echo '            </div>';
                            echo '            <div class="mt-3 mx-3">';
                            echo '                <h6 class=" text-center border border-danger">Status : Belum Bayar</h6>';
                            echo '            </div>';
                            echo '            <div class="modal-footer">';
                            echo '                <button type="submit" class="btn btn-inti" name="bayar_aja" id="bayar_212279">Bayar</button>';
                            echo '            </div>';
                            echo '        </form>';
                            echo '    </div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            // End Modal Bayar
    $sqlBuktiBayar = "SELECT gambar_bukti_bayar FROM data_bukti_bayar WHERE id_pemesanan = " . $rowPemesanan['id_pemesanan'];
    // Add this line to see the generated SQL query
    $resultBuktiBayar = $conn->query($sqlBuktiBayar);



                            // Check if the query was successful
                            if ($resultBuktiBayar->num_rows > 0) {
                                // Fetch the data
                                $rowBuktiBayar = $resultBuktiBayar->fetch_assoc();
                                $gambar_bukti = $rowBuktiBayar["gambar_bukti_bayar"];

                                //detail
                                echo '<div class="modal fade" id="detailModal' . $rowPemesanan["id_pemesanan"] . '" tabindex="-1" role="dialog" aria-labelledby="bayarModalLabel" aria-hidden="true">';
                                echo '    <div class="modal-dialog modal-dialog-centered">';
                                echo '        <div class="modal-content">';
                                echo '            <div class="modal-header">';
                                echo '                <h5 class="modal-title">Detail Pembayaran Lapangan ' . $rowLapangan['nama_lapangan'] . '</h5>';
                                echo '                <button type="button" class="btn-close" onclick="location.href=\'bayar.php\'" data-bs-dismiss="modal" aria-label="Close"></button>';
                                echo '            </div>';

                                echo '            <form action="" method="post">';
                                echo '                <div class="modal-body">';
                                echo '                    <div class="row justify-content-center align-items-center">';
                                echo '                        <div class="mb-3">';
                                echo '                            <img src="bayar/' . $gambar_bukti . '" alt="" class="img-fluid">';
                                echo '                        </div>';

                                // ID Pemesanan
                                echo '                    <div class="col">';
                                echo '                        <div class="mb-3">';
                                echo '                            <label for="exampleInputPassword1" class="form-label">ID Pemesanan</label>';
                                echo '                            <input type="text" name="tgl_main" class="form-control" id="exampleInputPassword1" value="' . $rowPemesanan['id_pemesanan'] . '" disabled>';
                                echo '                        </div>';
                                echo '                    </div>';

                                // Nama User
                                echo '                    <div class="col">';
                                echo '                        <div class="mb-3">';
                                echo '                            <label for="exampleInputPassword1" class="form-label">Nama User</label>';
                                echo '                            <input type="text" name="nama_user" class="form-control" id="exampleInputPassword1" value="' . $rowUser['nama_user'] . '" disabled>';
                                echo '                        </div>';
                                echo '                    </div>';

                                // Harga Lapangan
                                echo '                    <div class="col">';
                                echo '                        <div class="mb-3">';
                                echo '                            <label for="exampleInputPassword1" class="form-label">Harga Lapangan</label>';
                                echo '                            <input type="text" name="harga_lapangan" class="form-control" id="exampleInputPassword1" value="' . $rowharga['harga_lapangan'] . '" disabled>';
                                echo '                        </div>';
                                echo '                    </div>';

                                echo '                    <div class="input-group">';
                                echo '                        <div class="input-group-prepend border border-danger">';
                                echo '                            <span class="input-group-text">Total</span>';
                                echo '                        </div>';
                                echo '                        <input type="number" name="total" class="form-control border border-danger" id="exampleInputPassword1" value="' . number_format($rowPemesanan['total_biaya_pemesanan'], 0, ',', '.') . '" disabled>';
                                echo '                    </div>';
                                echo '                    </div>';
                                echo '                    <div class="mt-3 mx-3">';
                                echo '                        <h6 class="text-center border border-danger">Status : ' . $rowPemesanan["status_pembayaran_pemesanan"] . '</h6>';
                                echo '                    </div>';
                                echo '                </div>';
echo '                <div class="modal-footer">';
echo '<button type="button" class="btn btn-secondary" onclick="location.href=\'bayar.php\'" data-bs-dismiss="modal">Tutup</button>';


// Conditionally add the Print button
if ($rowPemesanan["status_pembayaran_pemesanan"] == "Lunas") {
    echo '                    <button type="button" class="btn btn-primary" onclick="printReceipt(\'detailModal' . $rowPemesanan["id_pemesanan"] . '\')">Print</button>';
}

echo '                </div>';

                                echo '            </form>';
                                echo '        </div>';
                                echo '    </div>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </tbody>


                    <!-- ... (remaining HTML code) -->

                </table>
                <!-- Pagination -->
                <ul class="pagination">
                    <?php if ($halamanAktif > 1): ?>
                        <li class="page-item">
                            <a href="?halaman=<?= $halamanAktif - 1; ?>" class="page-link">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $jmlHalaman; $i++): ?>
                        <?php if ($i == $halamanAktif): ?>
                            <li class="page-item active"><a class="page-link" href="?halaman=<?= $i; ?>">
                                    <?= $i; ?>
                                </a></li>
                        <?php else: ?>
                            <li class="page-item "><a class="page-link" href="?halaman=<?= $i; ?>">
                                    <?= $i; ?>
                                </a></li>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($halamanAktif < $jmlHalaman): ?>
                        <li class="page-item">
                            <a href="?halaman=<?= $halamanAktif + 1; ?>" class="page-link">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <!-- Pagination -->
            </form>
        </div>
    </section>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script>
        feather.replace();
    </script>

<script>
    function printModal(modalId) {
        var printContents = document.getElementById(modalId).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get all elements with class 'delete-btn'
            var deleteButtons = document.querySelectorAll('.delete-btn');

            // Attach click event to each delete button
            deleteButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    // Get the id_pemesanan from the 'data-id' attribute
                    var idPemesanan = this.getAttribute('data-id');

                    // Show the confirmation modal
                    var modal = new bootstrap.Modal(document.getElementById('hapusModal' + idPemesanan));
                    modal.show();

                    // Handle the delete action when the modal is confirmed
                    var deleteConfirmButton = document.getElementById('deleteConfirmButton' + idPemesanan);
                    deleteConfirmButton.addEventListener('click', function () {
                        // You can perform additional actions before sending the delete request
                        // (e.g., show a loading spinner)

                        // Redirect to the PHP script that handles the delete action
                        window.location.href = 'controller/hapus.php?id=' + idPemesanan;
                    });
                });
            });
        });
    </script>

        <script>
        function konfirmasiHapus(id) {
            var hapus = confirm('Anda yakin mau menghapus data ini?');

            if (hapus) {
                window.location.href = 'controller/hapus.php?id=' + id;
            }
        }
    </script>
<script>
    function printReceipt(modalId) {
        var printContent = document.getElementById(modalId).innerHTML;
        var originalContent = document.body.innerHTML;

        var modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if (modal) {
            modal.hide();
        }

        document.body.innerHTML = printContent;
        window.print();

        document.body.innerHTML = originalContent;

        modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if (modal) {
            modal.show();
        }
    }
</script>




</body>

</html>