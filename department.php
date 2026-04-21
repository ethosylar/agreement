<?php
session_start();
include 'dbconn.php';

if (!isset($_SESSION['department'])) {
    header("Location: index.php");
    exit();
}

// pull list of distinct departments
$res = $connection->query("SELECT DISTINCT department FROM form");
$depts = [];
while ($r = $res->fetch_assoc()) {
    $depts[] = $r['department'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Departments</title>
  <?php include "assets.php"; /* Bootstrap, jQuery, Boxicons */ ?>
  <link rel="stylesheet" href="style.css">
  <style>
    .card-columns {
      column-count: 2;
      column-gap: 1rem;
    }
    .card-columns .card {
      break-inside: avoid;
      margin-bottom: 1rem;
      background: #f7f7f7;
      border: 1px solid #ddd;
    }
  </style>
</head>
<body>
  <?php
    $pageTitle   = "Departments";
    $headerTitle = "Departments";
    $activePage  = "department";
    include "header.php";
    include "sidebar.php";
  ?>

  <div class="container" style="margin-top:2rem;">
    <div class="card-columns">
      <?php foreach($depts as $dept): ?>
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title text-dark"><?= htmlspecialchars($dept) ?></h5>
            <button
              class="btn btn-primary view-records-btn"
              data-department="<?= htmlspecialchars($dept) ?>">
              View Records
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Modal is identical to your other pages -->
  <div class="modal fade" id="recordsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-dark">Department Records</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body"></div>
      </div>
    </div>
  </div>

  <?php include "footer.php"; ?>

  <script>
  $(function(){
    $('.toggle-btn').click(()=>$('.sidebar').toggleClass('active'));
    $('.view-records-btn').click(function(){
      const d = $(this).data('department');
      $.post('record.php',{department:d})
        .done(html=>{
          $('#recordsModal .modal-body').html(html);
          $('#recordsModal').modal('show');
        })
        .fail(()=>alert('Failed to load records.'));
    });
  });
  </script>
</body>
</html>
