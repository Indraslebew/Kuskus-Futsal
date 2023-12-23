<?php
include '../../koneksi.php';

session_start();
if (!isset($_SESSION['id_admin'])) {
    echo '<script>alert("Mohon Login Dulu"); window.location.href = "../login.php";</script>';
    exit(); // Stop execution if not logged in
}

include "../../koneksi.php";

if (isset($_GET['id'])) {
    $id_lapangan = $_GET['id'];

    // Ambil nama file gambar sebelum data dihapus
    $sql = "SELECT gambar_lapangan FROM data_lapangan WHERE id_lapangan = $id_lapangan";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $gambar_lapangan = $row["gambar_lapangan"];

        // Hapus file gambar dari direktori
        $file_path = "gambar/" . $gambar_lapangan;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Hapus data dari database
        $sql_delete = "DELETE FROM data_lapangan WHERE id_lapangan = $id_lapangan";
        if ($conn->query($sql_delete) === TRUE) {
            header("Location: Lapangan.php");
        } else {
            echo "Error: " . $sql_delete . "<br>" . $conn->error;
        }
    } else {
        echo "Data tidak ditemukan";
    }

    $conn->close();
}
?>