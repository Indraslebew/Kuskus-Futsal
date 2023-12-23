<?php
include '../../koneksi.php';

session_start();
if (!isset($_SESSION['id_admin'])) {
    echo '<script>alert("Mohon Login Dulu"); window.location.href = "../login.php";</script>';
    exit(); // Stop execution if not logged in
}

include "../../koneksi.php";

if (isset($_GET['id'])) {
    $id_jadwal = $_GET['id'];
    echo "ID yang akan dihapus: $id_jadwal"; // Tambahkan pesan debug

    // Hapus data dari database
    $sql_delete = "DELETE FROM data_jadwal WHERE id_jadwal = $id_jadwal";
    if ($conn->query($sql_delete) === TRUE) {
        header("Location: jadwal.php");
    } else {
        echo "Error: " . $sql_delete . "<br>" . $conn->error;
    }
} else {
    echo "Data tidak ditemukan";
}

$conn->close();
?>
