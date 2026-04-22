<?php
session_start();
include 'dbconn.php';

// only azean may view audits
if (!isset($_SESSION['username']) && ($_SESSION['username']==='azean' || $_SESSION['username']==='itservices')) {
    header('Location: home.php');
    exit;
}

// escape helper
function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// pagination
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 50;
$offset   = ($page - 1) * $perPage;

// date‐range defaults to today unless overridden
$today     = date('Y-m-d');
$startDate = $_GET['start'] ?? $today;
$endDate   = $_GET['end']   ?? $today;

// are we in detail view?
$isDetail = isset($_GET['id']) && (int)$_GET['id'] > 0;
$audit_id = $isDetail ? (int)$_GET['id'] : null;

// count total matching rows for pagination
$stmt = $connection->prepare("
    SELECT COUNT(*) 
      FROM audit_log
     WHERE created_at BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
");
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$stmt->bind_result($totalRows);
$stmt->fetch();
$stmt->close();

$totalPages = max(1, ceil($totalRows / $perPage));

// page metadata
$pageTitle   = "Audit Log";
$headerTitle = "Audit Log";
$activePage  = "audit";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= h($pageTitle) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
  <!-- Icons & Styles -->
  <link href="https://cdn.jsdelivr.net/npm/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">

  <style>
    .container { margin:1rem; color:#000; }
    .filter-container { margin-bottom:1rem; }
    .btn-sm { padding:.25em .5em; font-size:.875em; }
    table.display th, table.display td { color:#000!important; }
  </style>
</head>
<body>
  <?php include "header.php"; ?>
  <?php include "sidebar.php"; ?>

  <div class="container">
    <h1><?= h($headerTitle) ?></h1>

    <?php if (!$isDetail): ?>
      <!-- LIST VIEW -->
      <form method="get" class="filter-container">
        <label>From:
          <input type="date" name="start" value="<?= h($startDate) ?>">
        </label>
        <label style="margin-left:1em;">To:
          <input type="date" name="end" value="<?= h($endDate) ?>">
        </label>
        <button type="submit" class="btn">Apply</button>
      </form>

      <?php
      // fetch page of results
      $stmt = $connection->prepare("
        SELECT audit_id,user_id,department,action,table_name,record_id,created_at
          FROM audit_log
         WHERE created_at BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
         ORDER BY created_at DESC
         LIMIT ? OFFSET ?
      ");
      $stmt->bind_param("ssii", $startDate, $endDate, $perPage, $offset);
      $stmt->execute();
      $res = $stmt->get_result();
      ?>

      <table id="auditList" class="display" style="width:100%">
        <thead>
          <tr>
            <th>ID</th><th>User</th><th>Dept.</th>
            <th>Action</th><th>Table</th><th>Record ID</th>
            <th>When</th><th>View</th>
          </tr>
        </thead>
        <tbody>
        <?php while($r = $res->fetch_assoc()): ?>
          <tr>
            <td><?= h($r['audit_id']) ?></td>
            <td><?= h($r['user_id']) ?></td>
            <td><?= h($r['department']) ?></td>
            <td><?= h($r['action']) ?></td>
            <td><?= h($r['table_name']) ?></td>
            <td><?= h($r['record_id']) ?></td>
            <td><?= h($r['created_at']) ?></td>
            <td>
              <a
                href="audit.php?
                       id=<?=h($r['audit_id'])?>&
                       start=<?=h($startDate)?>&
                       end=<?=h($endDate)?>&
                       page=<?= $page ?>"
                class="btn btn-sm">
                View
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>

      <!-- pagination -->
      <div style="text-align:center; margin-top:1rem;">
        <?php if($page>1): ?>
          <a href="?start=<?=h($startDate)?>&end=<?=h($endDate)?>&page=<?=($page-1)?>" class="btn btn-sm">« Prev</a>
        <?php endif; ?>

        <span style="margin:0 1em;">Page <?= $page ?> of <?= $totalPages ?></span>

        <?php if($page<$totalPages): ?>
          <a href="?start=<?=h($startDate)?>&end=<?=h($endDate)?>&page=<?=($page+1)?>" class="btn btn-sm">Next »</a>
        <?php endif; ?>
      </div>

    <?php else: ?>
      <!-- DETAIL VIEW -->
      <?php
      $stmt = $connection->prepare("
        SELECT audit_id,user_id,department,action,table_name,
               record_id,changed_data,ip_address,user_agent,created_at
          FROM audit_log
         WHERE audit_id = ?
      ");
      $stmt->bind_param("i", $audit_id);
      $stmt->execute();
      $detail = $stmt->get_result()->fetch_assoc();
      $stmt->close();
      ?>

      <p>
        <a href="audit.php?
                   start=<?=h($startDate)?>&
                   end=<?=h($endDate)?>&
                   page=<?= $page ?>"
           class="btn btn-sm"><i class="bx bx-arrow-back"></i> Back</a>
      </p>

      <table class="table table-striped"
             style="width:100%; background:#fff; padding:1rem; border-radius:8px;">
        <tbody>
          <tr><th>Audit ID</th>  <td><?= h($detail['audit_id']) ?></td></tr>
          <tr><th>User</th>      <td><?= h($detail['user_id']) ?></td></tr>
          <tr><th>Dept.</th>     <td><?= h($detail['department']) ?></td></tr>
          <tr><th>Action</th>    <td><?= h($detail['action']) ?></td></tr>
          <tr><th>Table</th>     <td><?= h($detail['table_name']) ?></td></tr>
          <tr><th>Record ID</th> <td><?= h($detail['record_id']) ?></td></tr>
          <tr>
            <th>Changed Data</th>
            <td>
              <?php if($detail['changed_data']): ?>
                <pre style="background:#f4f4f4; padding:.5rem; border-radius:4px;"><?= 
                  json_encode(
                    json_decode($detail['changed_data'],true),
                    JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE
                  )
                ?></pre>
              <?php else: ?>
                <em>— none —</em>
              <?php endif; ?>
            </td>
          </tr>
          <tr><th>IP Address</th> <td><?= h($detail['ip_address']) ?></td></tr>
          <tr><th>User Agent</th> <td><?= h($detail['user_agent']) ?></td></tr>
          <tr><th>Timestamp</th>  <td><?= h($detail['created_at']) ?></td></tr>
        </tbody>
      </table>
    <?php endif; ?>

  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <script>
    $(function(){
      if ($('#auditList').length) {
        $('#auditList').DataTable({
          paging:   false,
          info:     false,
          searching:true,
          order: [[6,'desc']]
        });
      }
    });
  </script>

  <?php include "footer.php"; ?>
</body>
</html>
<?php $connection->close(); ?>
