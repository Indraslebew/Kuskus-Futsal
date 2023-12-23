<?php
session_start();
include "koneksi.php";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $kategori = $_POST['kategori'];

    if ($kategori == "Admin") {
        $stmt = $conn->prepare("SELECT id_admin, nama_admin FROM data_admin WHERE nama_admin = ? AND password_admin = ?");
        $dashboard = "admin/Dashboard.php";
        $id_field = "id_admin"; // Use id_admin for Admin
    } elseif ($kategori == "User") {
        $stmt = $conn->prepare("SELECT id_user, nama_user FROM data_user WHERE nama_user = ? AND password_user = ?");
        $dashboard = "user/lapangan.php";
        $id_field = "id_user"; // Use id_user for User
    }

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION[$id_field] = $row[$id_field];
        $_SESSION['username'] = $username;
        header("Location: $dashboard");
        exit();
    } else {
        echo "<script>alert('Login gagal, pastikan data Anda benar!'); history.back();</script>";
    }
}
?>
