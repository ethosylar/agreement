<?php
  $active = $activePage ?? '';
  $isAdmin = isset($_SESSION['username']) && $_SESSION['username']==='azean';
?>
<div class="sidebar">
  <div class="logo-menu">
    <h2 class="menu" style="color: white;">Menu</h2>
    <i class='bx bx-menu toggle-btn'></i>
  </div>
  <ul class="list">
    <li class="list-item <?= $active==='dashboard' ? 'active' : '' ?>">
      <a href="home.php">
        <i class='bx bx-home'></i>
        <span class="link-name" style="--i:1;">My Dashboard</span>
      </a>
    </li>
    <li class="list-item <?= $active==='others' ? 'active' : '' ?>">
      <a href="dashboard.php">
        <i class='bx bx-file'></i>
        <span class="link-name" style="--i:2;">All Department</span>
      </a>
    </li>
    <li class="list-item <?= $active==='department' ? 'active' : '' ?>">
      <a href="department.php">
        <i class='bx bx-buildings'></i>
        <span class="link-name" style="--i:3;">Department</span>
      </a>
    </li>
    <li class="list-item <?= $active==='archive' ? 'active' : '' ?>">
      <a href="terminate.php">
        <i class='bx bx-folder-minus'></i>
        <span class="link-name" style="--i:4;">Archive</span>
      </a>
    </li>

    <?php if ($isAdmin): // only azean ?>
    <li class="list-item <?= $active==='audit' ? 'active' : '' ?>">
      <a href="audit.php">
        <i class='bx bx-history'></i>
        <span class="link-name" style="--i:5;">Audit Log</span>
      </a>
    </li>
    <?php endif; ?>

    <li class="list-item">
      <a href="logout.php">
        <i class='bx bx-log-out'></i>
        <span class="link-name" style="--i:6;">Logout</span>
      </a>
    </li>
  </ul>
</div>
