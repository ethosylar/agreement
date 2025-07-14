<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
    <div class="navdiv">
        <h2><?= htmlspecialchars($headerTitle ?? '') ?></h2>
        <ul class="breadcrumb">
            <li><a href="#"><?= htmlspecialchars($headerTitle ?? '') ?></a></li>
            <li><i class='bx bx-chevron-right' style="color: black;"></i></li>
            <li><a class="active" href="home.php">Dashboard</a></li>
        </ul>
    </div>
</nav>
<div class="wrapper">
