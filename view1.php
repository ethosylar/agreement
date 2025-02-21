<?php
session_start();
include("dbconn.php");

// Check if user is logged in
if (!isset($_SESSION['department'])) {
    echo "<script>
            alert('You must be logged in to view this page.');
            window.location.href='index.php';
          </script>";
    exit();
}

// Get the filename from the URL parameter
$filename = isset($_GET['filename']) ? $_GET['filename'] : '';

// Prepare the SQL statement to fetch the record based on the filename and user ID
$stmt = $connection->prepare("SELECT * FROM form WHERE filename = ? AND department = ?");
$stmt->bind_param("si", $filename, $_SESSION['department']);
$stmt->execute();
$result = $stmt->get_result();

// Check if the row exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "<script>alert('No data found for this entry.'); window.location.href='dashboard.php';</script>";
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" type="x-icon" href="hsptl.png">
    <title>View Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
        }
        .container {
            max-width: 700px;
            margin: 5px auto;
            padding: 15px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin: -10px 10px;
        }
        table th, table td {
            text-align: left;
            padding: 8px;
        }
        table th {
            background-color: #f5f5f5;
            max-width: 150px;
        }

        .back-button {
            background-color: transparent;
            border: none;
            color: black;
            font-size: 40px;
            cursor: pointer; 
            transition: color 0.3s ease, transform 0.5s ease;
        }
        .back-button:hover{
          color: rgb(208, 208, 208, 1.0);
          transform: scale(1.4);
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="back-button" type="button"><i class='bx bx-arrow-back'></i></a>
    <h1>View Record Details</h1>
    <table>
        <tr><th>Category:</th><td><?php echo htmlspecialchars($row['category']); ?></td></tr>
        <tr><th>PIC:</th><td><?php echo htmlspecialchars($row['pic']); ?></td></tr>
        <tr><th>Services:</th><td><?php echo htmlspecialchars($row['service']); ?></td></tr>
        <tr><th>Company/Act:</th><td><?php echo htmlspecialchars($row['company']); ?></td></tr>
        <tr><th>Start Date:</th><td><?php echo htmlspecialchars($row['start']); ?></td></tr>
        <tr><th>End Date:</th><td><?php echo htmlspecialchars($row['endDate']); ?></td></tr>
        <tr><th>SQFT:</th><td><?php echo htmlspecialchars($row['sqft']); ?></td></tr>
        <tr><th>Rental:</th><td><?php echo htmlspecialchars($row['rent']); ?></td></tr>
        <tr><th>Remarks:</th><td><?php echo htmlspecialchars($row['remarks']); ?></td></tr>
        <tr><th>Months Left Before Ends:</th><td><?php echo htmlspecialchars($row['monthsLeft']); ?></td></tr>
        <tr><th>Documents:</th>
        <td>
        <td><?php
                // Split the filenames and create download links for each
                $file_names = preg_split('/\s*,\s*/', $row['filename']);
                echo "<ul>";  // Start unordered list
                foreach ($file_names as $file_name) {
                    echo "<li><a href='uploads/" . htmlspecialchars($file_name) . "' download>" . htmlspecialchars($file_name) . "</a></li>";
                }
                echo "</ul>";  // End unordered list
            ?></td>
            </td>
        </tr>
    </table>
</div>

</body>
</html>