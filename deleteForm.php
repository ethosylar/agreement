<?php
include "dbconn.php";

// Check if the filename is provided via GET
if (isset($_GET['filename'])) {
    // Retrieve the filename from the GET request
    $filename = $_GET['filename'];

    // Create a prepared statement to safely delete the record
    $stmt = $connection->prepare("DELETE FROM form WHERE filename = ?");
    $stmt->bind_param("s", $filename); // "s" stands for string
    $stmt->execute();

    // Check if any rows were affected (i.e., the file was deleted)
    if ($stmt->affected_rows > 0) {
        // File successfully deleted, show alert and redirect to dashboard
        echo "<script type='text/javascript'>
            alert('File has been successfully deleted from the system.');
            window.location.href = 'home.php';
        </script>";
    } else {
        // No file found or delete failed
        echo "No file found or deletion failed.";
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$connection->close();
?>
