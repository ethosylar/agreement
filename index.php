<?php
session_start();
include("dbconn.php");

// Get contract alerts before login
$sql_alerts = "SELECT 
                SUM(CASE WHEN monthsLeft < 3 AND monthsLeft >= 0 THEN 1 ELSE 0 END) AS near_expiry,
                SUM(CASE WHEN monthsLeft < 0 THEN 1 ELSE 0 END) AS overdue
               FROM form";
$result_alerts = $connection->query($sql_alerts);
$alerts = $result_alerts->fetch_assoc();
$near_expiry = isset($alerts['near_expiry']) ? $alerts['near_expiry'] : 0;
$overdue = isset($alerts['overdue']) ? $alerts['overdue'] : 0;

// Handle login request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department = $_POST['department'];
    $user_pass = $_POST['user_pass'];

    // Use prepared statements to prevent SQL injection
    $stmt = $connection->prepare("SELECT department FROM depart WHERE department = ? AND user_pass = ?");
    $stmt->bind_param("ss", $department, $user_pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<script type='text/javascript'>
                alert('You are not registered.');
                window.location.href = 'index.php';
              </script>";
    } else {
        $sid = $result->fetch_assoc();
        $_SESSION['department'] = $sid['department'];
        header("Location: home.php");
        exit();
    }

    $stmt->close();
}
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Form</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <link rel="shortcut icon" type="x-icon" href="hsptl.png">
    <link rel="stylesheet" href="styleLogin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .lower3months {
            background-color: #FDFF90;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
            color: black;
        }

        .over3months {
            background-color: #FF9A9A;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            color: black;
        }
    </style>
</head>
<body>
    <img class="hospital">
    <div class="container">
        <div class="img">
            <img src="img/hospital.svg" alt="Hospital">
        </div>
        <div class="login-content">
            <form name="form" method="POST" action="index.php">
                <img src="img/avatar.svg" alt="Avatar">
                <h2 class="title">Welcome</h2>

                <?php if ($near_expiry > 0): ?>
                    <div class="lower3months">
                        ⚠️ <b><?php echo $near_expiry; ?></b> contract(s) expiring in less than 3 months!
                    </div>
                <?php endif; ?>

                <?php if ($overdue > 0): ?>
                    <div class="over3months">
                        ❌ <b><?php echo $overdue; ?></b> contract(s) are OVERDUE!
                    </div>
                <?php endif; ?>

                <div class="input-div one">
                    <div class="i">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="div">
                        <input type="text" class="form-control" name="department" placeholder="Enter username" required>
                    </div>
                </div>
                <div class="input-div pass">
                    <div class="i"> 
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <h5>Password</h5>
                        <input type="password" class="input" name="user_pass" required>
                    </div>
                </div>
                <button type="submit" name="hantar" class="btn">Login</button>
            </form>
        </div>
    </div>
    <script type="text/javascript" src="main.js"></script>
</body>
</html>
