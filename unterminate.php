<?php
session_start();
include 'dbconn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Move the record back to active records table
    $query = "INSERT INTO active_records (id, department, category, pic, service, company, start, endDate, rent, remarks, monthsLeft)
              SELECT id, department, category, pic, service, company, start, endDate, rent, remarks, monthsLeft 
              FROM terminate WHERE id = $id";
    
    // Delete from terminate table
    $delete_query = "DELETE FROM terminate WHERE id = $id";

    if (mysqli_query($connection, $query) && mysqli_query($connection, $delete_query)) {
        echo "Success";
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}

mysqli_close($connection);
?>
