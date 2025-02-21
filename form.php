<?php
session_start();
include("dbconn.php"); 

// Check if user is logged in
if (!isset($_SESSION['department'])) {
    echo "<script>
            alert('You are not logged in.');
            window.location.href='login.html';
          </script>";
    exit();
}
   

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture values from the HTML form and sanitize inputs
    $category = mysqli_real_escape_string($connection, $_POST['category']);
    $pic = mysqli_real_escape_string($connection, $_POST['pic']);
    $service = mysqli_real_escape_string($connection, $_POST['service']);
    $company = mysqli_real_escape_string($connection, $_POST['company']);
    $start = mysqli_real_escape_string($connection, $_POST['start']);
    $endDate = mysqli_real_escape_string($connection, $_POST['endDate']);
    $sqft = mysqli_real_escape_string($connection, $_POST['sqft']);
    $rent = mysqli_real_escape_string($connection, $_POST['rent']);
    $remarks = mysqli_real_escape_string($connection, $_POST['remarks']);
    $department = mysqli_real_escape_string($connection, $_SESSION['department']);

    // Calculate months left from the end date
    $endDateTimestamp = strtotime($endDate);
    $currentTimestamp = time();

    // Calculate the difference in months
    $monthsLeft = 0;
    $loopTimestamp = $currentTimestamp;
    while ($loopTimestamp < $endDateTimestamp) {
        $loopTimestamp = strtotime('+1 month', $loopTimestamp);
        $monthsLeft++;
    }

    // If the end date is in the past, calculate the difference in the opposite direction
    if ($currentTimestamp > $endDateTimestamp) {
        $monthsLeft = -$monthsLeft;
        $loopTimestamp = $endDateTimestamp;
        while ($loopTimestamp < $currentTimestamp) {
            $loopTimestamp = strtotime('+1 month', $loopTimestamp);
            $monthsLeft--;
        }
    }

    // Alternatively, you can use a simpler approach using date_diff
    $endDateDate = new DateTime($endDate);
    $currentDate = new DateTime();
    $interval = $endDateDate->diff($currentDate);
    $monthsLeft = $interval->m + ($interval->y * 12);
    if ($endDateDate < $currentDate) {
        $monthsLeft = -$monthsLeft;
    }
    // Directory where the files will be uploaded
    $target_dir = "uploads/";

    // Create the directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Check if files were uploaded without errors
    if (isset($_FILES["files"]) && count($_FILES['files']['name']) > 0) {
        // Allowed file types
        $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf", "docx", "doc", "xls", "xlsx", "ppt", "pptx", "txt", "zip");
        $uploaded_file_names = [];

        // Loop through each uploaded file
        for ($i = 0; $i < count($_FILES["files"]["name"]); $i++) {
            $file_name = $_FILES["files"]["name"][$i];
            $file_tmp = $_FILES["files"]["tmp_name"][$i];
            $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Check if the file type is allowed
            if (!in_array($file_type, $allowed_types)) {
                echo "Sorry, only allowed file types are: " . implode(", ", $allowed_types) . ".<br>";
                continue; // Skip to the next file
            }

            // New file name to avoid overwriting
            $new_file_name = !empty($_POST["newFileName"]) ? basename($_POST["newFileName"]) . "_$i." . $file_type : basename($file_name);

            // Set the target file path
            $target_file = $target_dir . $new_file_name;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $target_file)) {
                echo "File " . $new_file_name . " has been uploaded.<br>";
                $uploaded_file_names[] = $new_file_name; // Store uploaded file names for DB
            } else {
                echo "Sorry, there was an error uploading your file " . $file_name . ".<br>";
            }
        }

        // Prepare file names for database insertion
        $file_names_string = implode(", ", $uploaded_file_names);

        
        // SQL query to insert data
        $sql = "INSERT INTO form (category, pic, service, company, start, endDate, sqft, rent, filename, remarks, monthsLeft, department) 
                VALUES ('$category', '$pic', '$service', '$company', '$start', '$endDate', '$sqft', '$rent', '$file_names_string', '$remarks', '$monthsLeft', '$department')";

        // Execute query and handle errors
        if (mysqli_query($connection, $sql)) {
            echo "<script type='text/javascript'>
                    alert('Submission successful.');
                    window.location.href = 'home.php';
                  </script>";
        } else {
            echo "Error: " . mysqli_error($connection);
        }
    } else {
        echo "No files were uploaded.";
    }

    // Close the database connection
    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" type="x-icon" href="hsptl.png">
    <title>Form</title>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7fafc;
            color: #333;
            margin: 0;
        }
        .container {
            max-width: 600px;
            max-height: 660px;
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
            width: 80%;
            font-size: 16px;
            background-color: #f9f9f9;
        }
        input[type="date"], input[type="number"] {
            width: 80%;
        }
        textarea {
            height: 80px;
            resize: none;
            width: 80%;
        }

        .button {
            background-color: blue;
            color: white;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
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
        <a href="home.php" class="back-button" type="button"><i class='bx bx-arrow-back'></i></a>
    <h1>Form</h1>
    <form name="borang" method="POST" enctype="multipart/form-data">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="licensing">Licensing</option>
                        <option value="tenant">Tenant</option>
                        <option value="service">Service</option>
                        <option value="outsource">Outsource</option>
                        <option value="biomedical-facilities">Biomedical Facilities</option>
                        <option value="marcomm">Marcomm</option>
                        <option value="clinical">Clinical</option>
                        <option value="support">Support</option>
                    </select>
                
                        <label for="pic">PIC</label>
                        <input type="text" name="pic" id="pic" >
                  
                        <label for="service">Service</label>
                        <input type="text" name="service" id="service" >
                   
                        <label for="company">Company / Act</label>
                        <input type="text" id="company" name="company" >
                   
                        <label for="start">Start Date</label>
                        <input type="date" name="start" id="start" >
                    
                        <label for="endDate">End Date</label>
                        <input type="date" name="endDate" id="endDate" >
                    
                        <label for="sqft">SQFT</label>
                        <input type="text" name="sqft" id="sqft" >
                    
                        <label for="rent">Rental</label>
                        <input type="text" name="rent" id="rent" placeholder="RM" >
                   
                        <label for="file">Upload Files</label>
                        <input type="file" name="files[]" id="file" multiple >
                   
                        <label for="newFileName">Rename file (optional)</label>
                        <input type="text" name="newFileName" id="newFileName" placeholder="Enter new file name">
                    
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks" id="remarks"></textarea>
                   
                    <button type="submit" class="btn">Submit</button>
                </div>
            
        </div>
    </form>
</body>
</html>
