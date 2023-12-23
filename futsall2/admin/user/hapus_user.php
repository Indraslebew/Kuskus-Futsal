<?php
include '../../koneksi.php';

session_start();
if (!isset($_SESSION['id_admin'])) {
    echo '<script>alert("Mohon Login Dulu"); window.location.href = "../login.php";</script>';
    exit(); // Stop execution if not logged in
}


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id_user = $_GET['id'];
    $sql = "DELETE FROM data_user WHERE id_user=$id_user";

    if ($conn->query($sql) === TRUE) {
        header("Location: user.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>