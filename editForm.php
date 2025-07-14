<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['department'])) {
    header("Location: index.php");
    exit();
}

include 'dbconn.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id)) {
    header("Location: home.php");
    exit();
}

// Fetch the existing record
$stmt = $connection->prepare("SELECT * FROM form WHERE id = ? AND department = ?");
$stmt->bind_param("is", $id, $_SESSION['department']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No data found for this entry.";
    exit();
}

$row = $result->fetch_assoc();

// If form is submitted, update the record
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $category = htmlspecialchars($_POST['category']);
    $pic = htmlspecialchars($_POST['pic']);
    $service = htmlspecialchars($_POST['service']);
    $company = htmlspecialchars($_POST['company']);
    $start = htmlspecialchars($_POST['start']);
    $endDate = htmlspecialchars($_POST['endDate']);
    $sqft = htmlspecialchars($_POST['sqft']);
    $rent = htmlspecialchars($_POST['rent']);
    $remarks = htmlspecialchars($_POST['remarks']);
    $duration = htmlspecialchars($_POST['duration']);
    $status = isset($_POST['status']) ? htmlspecialchars($_POST['status']) : $row['status'];


    // Directory for file uploads
    $target_dir = "uploads/";
    $existing_files = !empty($row['filename']) ? explode(',', $row['filename']) : [];

    // Handle file removal
    $files_to_remove = isset($_POST['remove_files']) ? $_POST['remove_files'] : [];
    foreach ($files_to_remove as $file) {
        $file_path = $target_dir . basename($file);
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $existing_files = array_filter($existing_files, function ($f) use ($file) {
            return $f !== $file;
        });
    }

    $new_file_names = [];

    // Handle new file uploads
    if (!empty($_FILES["files"]["name"][0])) {
        foreach ($_FILES["files"]["name"] as $key => $name) {
            $target_file = $target_dir . basename($name);
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate file type
            $allowed_types = ["jpg", "jpeg", "png", "gif", "pdf", "docx", "doc"];
            if (!in_array($file_type, $allowed_types)) {
                echo "Invalid file type: $name.";
                exit();
            }

            // Check file size
            if ($_FILES["files"]["size"][$key] > 5000000) {
                echo "File too large: $name.";
                exit();
            }

            // Upload the file
            if (move_uploaded_file($_FILES["files"]["tmp_name"][$key], $target_file)) {
                $new_file_names[] = $name;
            } else {
                echo "Error uploading file: $name.";
                exit();
            }
        }
    }

    // Merge existing and new files
    $all_file_names = array_merge($existing_files, $new_file_names);
    $file_names_string = !empty($all_file_names) ? implode(',', $all_file_names) : NULL;

    // Calculate months left
    $endDateObj = new DateTime($endDate);
    $currentDateObj = new DateTime();
    $interval = $endDateObj->diff($currentDateObj);
    $monthsLeft = $interval->m + ($interval->y * 12);
    if ($endDateObj < $currentDateObj) {
        $monthsLeft = -$monthsLeft;
    }

    // Update the database using ID instead of filename
    $stmt = $connection->prepare("UPDATE form SET category=?, pic=?, service=?, company=?, start=?, endDate=?, sqft=?, rent=?, filename=?, remarks=?, monthsLeft=?, status=?, duration=? WHERE id=? AND department=?");
    $stmt->bind_param("sssssssssssisss", $category, $pic, $service, $company, $start, $endDate, $sqft, $rent, $file_names_string, $remarks, $monthsLeft, $status, $duration, $id, $_SESSION['department']);

    if ($stmt->execute()) {
        header("Location: view.php?id=" . urlencode($id));
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="x-icon" href="hsptl.png">
    <title>Update Record</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7fafc;
            color: #333;
            margin: 0;
        }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 15px auto;
            padding: 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            font-size: 28px;
            color: #444;
            margin-bottom: 20px;
        }
        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        label {
            font-weight: 600;
            margin-bottom: -30px;
            margin-left: 50px;
            color: #555;
        }
        input, textarea, select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            background-color: #f9f9f9;
        }
        input[type="date"], input[type="number"] {
            width: 100%;
        }
        textarea {
            height: 80px;
            resize: none;
            width: 100%;
        }
        .button {
            background-color: blue;
            color: white;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 10px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn {
            background-color: blue;
            color: white;
            padding: 5px 20px;
            border-radius: 15px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
            transition: background 0.3s ease;
            grid-column: span 2;
        }
        .btn:hover {
            background: #fff;
            color: grey;
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
    <a onclick="history.back()" class="back-button" type="button"><i class='bx bx-arrow-back'></i></a>
    <h1>Update Record</h1>
    <form method="POST" enctype="multipart/form-data">
        <label for="category">Category:</label>
        <select name="category" id="category" required>
            <option value="licensing" <?php echo ($row['category'] == 'licensing') ? 'selected' : ''; ?>>Licensing</option>
            <option value="tenant" <?php echo ($row['category'] == 'tenant') ? 'selected' : ''; ?>>Tenant</option>
            <option value="service" <?php echo ($row['category'] == 'service') ? 'selected' : ''; ?>>Service</option>
            <option value="outsource" <?php echo ($row['category'] == 'outsource') ? 'selected' : ''; ?>>Outsource</option>
            <option value="biomedical-facilities" <?php echo ($row['category'] == 'biomedical-facilities') ? 'selected' : ''; ?>>Marcomm</option>
            <option value="tenant" <?php echo ($row['category'] == 'marcomm') ? 'selected' : ''; ?>>Marcomm/Insurance</option>
            <option value="clinical" <?php echo ($row['category'] == 'clinical') ? 'selected' : ''; ?>>Clinical</option>
            <option value="support" <?php echo ($row['category'] == 'support') ? 'selected' : ''; ?>>Service  Support Maintenance</option>
        </select>

        <label for="pic">PIC/Owner Name:</label>
        <input type="text" name="pic" id="pic" value="<?php echo $row['pic']; ?>" >

        <label for="service">Services:</label>
        <input type="text" name="service" id="service" value="<?php echo $row['service']; ?>" >

        <label for="company">Company Name/Act Name:</label>
        <input type="text" name="company" id="company" value="<?php echo $row['company']; ?>" >

        <label for="start">Start Date:</label>
        <input type="date" name="start" id="start" value="<?php echo $row['start']; ?>" >

        <label for="endDate">End Date:</label>
        <input type="date" name="endDate" id="endDate" value="<?php echo $row['endDate']; ?>" >

        <label for="sqft">SQFT:</label>
        <input type="text" name="sqft" id="sqft" value="<?php echo $row['sqft']; ?>" >

        <label for="rent">Amount(RM):</label>
        <input type="text" name="rent" id="rent" value="<?php echo $row['rent']; ?>" >

        <label for="remarks">Remarks:</label>
        <textarea name="remarks" id="remarks" ><?php echo $row['remarks']; ?></textarea>

        <label for="remarks">Duration:</label>
        <textarea name="duration" id="duration" ><?php echo $row['duration']; ?></textarea>

        <label for="files">Upload Files:</label>
        <input type="file" name="files[]" id="files" value="<?php echo htmlspecialchars($row['filename']); ?>" multiple>

        <h3>Existing Files:</h3>
        <ul>
            <?php
            $file_path = ''; // Initialize to avoid undefined variable notice
            if (isset($_POST['remove_files']) && !empty($_POST['remove_files'])) {
                $files_to_remove = $_POST['remove_files'];
                foreach ($files_to_remove as $file) {
                    $file_path = $target_dir . basename($file);
                    if (file_exists($file_path)) {
                        unlink($file_path); // Remove the file from the server
                    }
                }
            }

            if (isset($_POST['remove_files']) && !empty($_POST['remove_files'])) {
                $files_to_remove = $_POST['remove_files'];
                foreach ($files_to_remove as $file) {
                    $file_path = $target_dir . basename($file);
                    if (file_exists($file_path)) {
                        unlink($file_path); // Remove the file from the server
                    } else {
                        echo "File does not exist: $file_path<br>"; // Debugging output
                    }
                }
            } else {
                echo "No files to remove.<br>"; // Debugging output
            }


            $existing_files = explode(',', $row['filename']);
            foreach ($existing_files as $file) {
                echo "<li>$file <input type='checkbox' name='remove_files[]' value='" . htmlspecialchars($file) . "'> Remove</li>";
            }
            ?>
        </ul>

        <input type="hidden" name="file_names_string" value="<?php echo $row['filename']; ?>">
        <input type="hidden" name="status" value="<?php echo htmlspecialchars($row['status']); ?>">
        <button type="submit" class="btn">Update</button>
    </form>
</div>
</body>
</html>