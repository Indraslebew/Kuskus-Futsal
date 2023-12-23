<?php
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    // Get the id_pemesanan from the query string
    $id_pemesanan = $_GET['id'];

    // Prepare the SQL statement to delete data from data_bukti_bayar
    $sqlDeleteImage = "DELETE FROM data_bukti_bayar WHERE id_pemesanan = ?";
    $stmtDeleteImage = $conn->prepare($sqlDeleteImage);
    $stmtDeleteImage->bind_param("i", $id_pemesanan);

    // Execute the query to delete data from data_bukti_bayar
    if ($stmtDeleteImage->execute()) {
        // Close the statement for deleting data from data_bukti_bayar
        $stmtDeleteImage->close();

        // Prepare the SQL statement to delete data from data_pemesanan
        $sqlDelete = "DELETE FROM data_pemesanan WHERE id_pemesanan = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $id_pemesanan);

        // Execute the query to delete data from data_pemesanan
        if ($stmtDelete->execute()) {
            // Redirect to the page where the deletion was initiated
            header("Location: ../bayar.php");
            exit();
        } else {
            // Handle the error if the deletion from data_pemesanan was not successful
            echo "Error deleting data from data_pemesanan: " . $stmtDelete->error;
        }

        // Close the statement for deleting data from data_pemesanan
        $stmtDelete->close();
    } else {
        // Handle the error if the deletion from data_bukti_bayar was not successful
        echo "Error deleting data from data_bukti_bayar: " . $stmtDeleteImage->error;
    }
} else {
    // If the id_pemesanan is not set in the query string, redirect to an error page or the main page
    header("Location: ../bayar.php");
    exit();
}

// Close the database connection
$conn->close();
?>
