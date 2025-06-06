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

// If the filename is invalid or empty, redirect back
if (empty($filename)) {
    echo "<script>
            alert('Filename is invalid or empty.');
            window.location.href='home.php';
          </script>";
    exit();
}

// Prepare the SQL statement to fetch the record based on the filename and user ID
$stmt = $connection->prepare("SELECT * FROM form WHERE filename = ? AND department = ?");
$stmt->bind_param("si", $filename, $_SESSION['department']);
$stmt->execute();
$result = $stmt->get_result();

// Check if the row exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "<script>alert('No data found for this entry.'); window.location.href='department.php';</script>";
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
            width: 90%;
            border-collapse: collapse;
            margin: 10px auto;
        }
        table th, table td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f5f5f5;
            width: 40%;
        }
        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            background: blue;
            color: white;
            padding: 8px 20px;
            margin: 10px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 16px;
            display: inline-block;
            cursor: pointer;
            transition: background 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btn:hover {
            background: darkblue;
        }
        .back-button {
            background-color: transparent;
            border: none;
            color: black;
            font-size: 40px;
            cursor: pointer; 
            transition: color 0.3s ease, transform 0.5s ease;
        }
        .back-button:hover {
            color: gray;
            transform: scale(1.4);
        }
    </style>
</head>
<body>

<div class="container">
    <a href="department.php" class="back-button" type="button"><i class='bx bx-arrow-back'></i></a>
    <h1>View Record Details</h1>
    <table>
        <tr><th>Category:</th><td><?php echo htmlspecialchars($row['category']); ?></td></tr>
        <tr><th>PIC:</th><td><?php echo htmlspecialchars($row['pic']); ?></td></tr>
        <tr><th>Services:</th><td><?php echo htmlspecialchars($row['service']); ?></td></tr>
        <tr><th>Company Name/Act Name:</th><td><?php echo htmlspecialchars($row['company']); ?></td></tr>
        <tr><th>Start Date:</th><td><?php echo date("d/m/Y", strtotime($row["start"])); ?></td></tr>
        <tr><th>End Date:</th><td><?php echo date("d/m/Y", strtotime($row["endDate"])); ?></td></tr>
        <tr><th>SQFT:</th><td><?php echo htmlspecialchars($row['sqft']); ?></td></tr>
        <tr><th>Amount(RM):</th><td><?php echo htmlspecialchars($row['rent']); ?></td></tr>
        <tr><th>Remarks:</th><td><?php echo htmlspecialchars($row['remarks']); ?></td></tr>
        <tr><th>Duration:</th><td><?php echo htmlspecialchars($row['duration']); ?></td></tr>
        <tr><th>Months Left Before Ends:</th><td><?php echo htmlspecialchars($row['monthsLeft']); ?></td></tr>
        <tr><th>Document:</th>
        <td>
                <?php
                if (!empty($row['filename'])) {
                    // Handle multiple filenames (comma-separated)
                    $file_names = preg_split('/\s*,\s*/', $row['filename']);
                    echo "<ul>";
                    foreach ($file_names as $file_name) {
                        echo "<li><a href='uploads/" . htmlspecialchars($file_name) . "' download>" . htmlspecialchars($file_name) . "</a></li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p style='color: red;'>No document available.</p>";
                }
                ?>
            </td>
        </tr>
    </table>
</div>
</body>
</html>

<?php
$connection->close();
?>