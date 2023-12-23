<?php
include '../../koneksi.php';

session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status_jam'])) {
    $status_jam = $_POST['status_jam'];

    foreach ($status_jam as $jam => $status) {
        $sql = "UPDATE data_jadwal SET status_jam_jadwal = '$status' WHERE jam_jadwal = '$jam'";
        $result = $conn->query($sql);

        if (!$result) {
            echo "Error updating record: " . $conn->error;
        }
    }
}

header("jam.php");
?>