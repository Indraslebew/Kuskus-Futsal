<?php
include 'koneksi.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to validate and sanitize input
function validateInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_user = validateInput($_POST['nama_user']);
    $password_user = validateInput($_POST['password_user']);
    $nomor_telepon_user = validateInput($_POST['nomor_telepon_user']);

    // Validate phone number format (you may customize this validation)
    if (!preg_match("/^[0-9]{10,15}$/", $nomor_telepon_user)) {
        echo "<script>alert('Daftar gagal, nomor telepon salah!'); history.back();</script>";
        exit();
    }

    // Check if the username already exists
    $check_query = $conn->prepare("SELECT * FROM data_user WHERE nama_user = ?");
    $check_query->bind_param("s", $nama_user);
    $check_query->execute();
    $result = $check_query->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Daftar gagal, username sudah terdaftar!'); history.back();</script>";
        exit();
    }

    // Use prepared statements to prevent SQL injection
    $insert_query = $conn->prepare("INSERT INTO data_user (nama_user, password_user, nomor_telepon_user) VALUES (?, ?, ?)");
    $insert_query->bind_param("sss", $nama_user, $password_user, $nomor_telepon_user);

    if ($insert_query->execute()) {
        // Registration success
        echo '<script>alert("Registrasi berhasil!");</script>';
        echo '<script>window.location.replace("login.php");</script>';
        exit();
    } else {
        echo '<script>alert("Error: ' . $insert_query->error . '");</script>';
    }

    $check_query->close();
    $insert_query->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="regis.css">
    <title>Registrasi</title>
</head>

<body>
    <div class="p">
        <form action="" method="post" enctype="multipart/form-data">
            <h1>Registrasi</h1>
            <div class="mb-3">
                <label for="nama" class="form-label">Username :</label>
                <input type="text" name="nama_user" class="form-control" id="nama" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password :</label>
                <input type="password" name="password_user" class="form-control" id="password" required>
            </div>
            <div class="mb-3">
                <label for="hp" class="form-label">Nomor Hp :</label>
                <input type="text" name="nomor_telepon_user" class="form-control" id="hp" required>
            </div>
            <div class="mb-3">
                <input type="checkbox" id="showPassword" onclick="togglePassword()">
                <label for="showPassword">Show Password</label>
            </div>
            <button type="submit" class="btn"><b>Daftar</b></button>
        </form>
    </div>

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById('password');
            passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
        }
    </script>
</body>

</html>