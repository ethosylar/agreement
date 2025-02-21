<?php
// MySQL database connection
$user = "root"; // MySQL username
$pass = ""; // MySQL password
$host = "localhost"; // Server name or IP address
$dbname = "intern"; // Database name

$connection = mysqli_connect($host, $user, $pass, $dbname);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
