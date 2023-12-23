<?php
error_reporting(E_ALL);

// Start the session
session_start();

// Include the database connection file
include 'koneksi.php';

// Check if the user is logged in
if (isset($_SESSION['id_user'])) {
  $id_user = $_SESSION['id_user'];

  $query = "SELECT * FROM data_user WHERE id_user = $id_user";
  $result = mysqli_query($conn, $query);

  // Check if the query was successful
  if ($result) {
    // Fetch the user data as an associative array
    $row = mysqli_fetch_assoc($result);
    $bookingLink = 'user/lapangan.php'; // Set the link to lapangan.php if the user is logged in
  } else {
    echo "Error in query: " . mysqli_error($conn);
  }
} else {
  $bookingLink = 'login.php'; // Set the link to login.php if the user is not logged in
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Futsal Kuskus</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Serif&family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
    /* Add this style for the grass-green-card */
    .grass-green-card {
      background-color: #D0F0C0;
      /* Change this color to your desired shade of green */
      color: black;
      /* Text color on the card, you can adjust this accordingly */
    }

    /* Style for the button inside the grass-green-card */
    .grass-green-card button {
      background-color: #12AE25 !important;
      color: #fff !important;
      border: none;
      /* Remove button border if needed */
      /* Add any other button styles as needed */
    }

    .grass-green-card button:hover {
      filter: brightness(80%);
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <div class="container">
    <nav class="navbar fixed-top bg-body-secondary navbar-expand-lg">
      <div class="container">
        <a class="navbar-brand" href="index.php">
          <img src="12.png" alt="Logo" width="80" height="50" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
            <li class="nav-item ">
              <a class="nav-link active" aria-current="page" href="#home">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="#about">Tentang Kami</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="#bayar">Tata Cara</a>
            </li>
            <?php
            if (isset($_SESSION['id_user'])) {
              echo '
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="user/lapangan.php">Lapangan</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="user/bayar.php">Pembayaran</a>
            </li>
            ';
            }
            ?>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="#contact">Kontak</a>
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
                <?php
                // Check if $row is set and not empty before accessing its values
                if (isset($row) && !empty($row)) {
                  echo '<h5 class="mb-3">Nama : ' . $row["nama_user"] . '</h5>';
                  echo '<p>Nomor Telp : ' . $row["nomor_telepon_user"] . '</p><br>';
                  echo '<a href="logout.php" class="btn btn-danger">Logout</a>';
                } else {
                  echo 'User data not found.';
                }
                ?>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal Profil -->

  <!-- Jumbotron -->
  <section class="jumbotron" id="home">
    <main class="contain" data-aos="fade-right" data-aos-duration="1000">
      <h1 class="text-light">Sehatkan Dirimu Dengan Berolahraga di <span>Kuskus</span> Futsal </h1>
      <p>
        Mimpi besar membutuhkan tindakan besar. Mulailah dengan langkah kecil, dan terus maju.
      </p>
      <a href="<?php echo $bookingLink; ?>" class="btn btn-inti">Booking Sekarang</a>
    </main>
  </section>
  <!-- End Jumbotron -->

  <!-- About -->
  <section class="about" id="about">
    <h2 data-aos="fade-down" data-aos-duration="1000">
      <span>Tentang</span> Kami
    </h2>
    <div class="row">
      <div class="about-img" data-aos="fade-right" data-aos-duration="1000">
        <img src="1.jpg" alt="" />
      </div>
      <div class="contain" data-aos="fade-left" data-aos-duration="1000">
        <h4 class="text-center mb-3">Kenapa Memilih kami?</h4>
        <p>Kami yakin bahwa olahraga yang terjangkau adalah hak setiap orang. Dengan program berbiaya ringan dan beragam
          kegiatan, kami hadir untuk membantu Anda menjaga kesehatan tanpa memberatkan kantong. Bergabunglah dengan kami
          untuk mengubah pandangan bahwa gaya hidup sehat harus mahal. kami membuktikan sebaliknya. Rasakan keunggulan
          lapangan futsal terbaik kami, hadir dengan teknologi terkini dan fasilitas unggulan. Bergabunglah dengan
          komunitas futsal berkualitas, dan nikmati pengalaman bermain tak terlupakan.</p>
      </div>
    </div>
  </section>
  <!-- End About -->

  <!-- Pembayaran -->
  <section class="pembayaran" id="bayar">
    <h2 data-aos="fade-down" data-aos-duration="1000">
      <span>Tata Cara</span> Pembayaran
    </h2>
    <p class="text-center">Berikut adalah tata cara pembayaran lapangan pada website Kuskus Futsal:</p>
    <ul class="border list-group list-group-flush mt-5">
      <li class="list-group-item">1. Pengguna harus membuat akun atau mendaftar sebagai anggota pada website Kuskus
        Futsal.</li>
      <li class="list-group-item">2. Pengguna dapat memilih jenis lapangan yang ingin dibooking, dan melihat jadwal.</li>
      <li class="list-group-item">3. Pengguna harus memilih tanggal, waktu dan lama main, melihat harga sewa lapangan, melengkapi
        formulir pemesanan.</li>
      <li class="list-group-item">4. Bila Dirasa sudah sesuai, pengguna dapat meng klik tombol pesan.</li>
      <li class="list-group-item">5. Lalu pengguna akan diarahkan ke menu pembayaran.</li>
      <li class="list-group-item">6. Lakukan pembayaran ke rekening yang sudah tertera dan upload bukti pembayaran.</li>
      <li class="list-group-item">7. Setelah upload, tunggu admin menyetujui pembayaran anda.</li>
      <li class="list-group-item">8. Setelah status sudah di setujui maka status pembayaran akan menjadi "Lunas",
        pengguna dapat melakukan print bukti bayar.</li>
      <li class="list-group-item">9. Setelah status sudah di setujui maka status pembayaran akan menjadi "Lunas",
        silahkan datang ke Kuskus Futsal sesuai jadwal yang di pesan.</li>
    </ul>
  </section>
  <!-- End Pembayaran -->


  <!-- Contact -->
  <section id="contact" class="contact" data-aos="fade-down" data-aos-duration="1000">
    <h2><span>Kontak</span> Kami</h2>
    <div class="row">
      <div class="col">
        <div class="col">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4288.136819785928!2d119.49346591179611!3d-5.146209514560984!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbee38ed8438df1%3A0x9eb1ca3f7a2fce!2sPasific%20Kost%2024!5e1!3m2!1sid!2sid!4v1700218300208!5m2!1sid!2sid"
            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

      </div>
      <div class="col">
        <form action="">
          <!-- Card with text and button -->
          <div class="card text-center p-4 grass-green-card">
            <div class="card-body">
              <h5 class="card-title font-weight-bold mb-4">
                Jika Anda memiliki pertanyaan atau saran, <br>silakan hubungi kami melalui
              </h5>
              <button style="font-size: 24px;" onclick="redirectToWhatsApp()">
                <i class="fa fa-whatsapp"></i> WhatsApp
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
  <!-- End Contact -->


  <!-- footer -->
  <footer><br>
    <h4>Follow Us On</h4>
    <div class="social">
      <a href="https://www.instagram.com/indraa.e_/"><i data-feather="instagram"></i></a>
      <a href="https://www.facebook.com/"><i data-feather="facebook"></i></a>
      <a href="https://twitter.com/"><i data-feather="twitter"></i></a>
      <a href="https://www.youtube.com/@tbz25newera84"><i data-feather="youtube"></i></a><br>
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
    function redirectToWhatsApp() {
      // Ganti nomor telepon berikut dengan nomor WhatsApp yang diinginkan
      var phoneNumber = "6281299313729";

      // Format URL WhatsApp dengan nomor telepon
      var whatsappURL = "https://wa.me/" + phoneNumber;

      // Buka URL WhatsApp di tab atau jendela baru
      window.open(whatsappURL, '_blank');
    }
  </script>
</body>

</html>