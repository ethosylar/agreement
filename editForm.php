<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['department'])) {
    header("Location: index.php");
    exit();
}

include 'dbconn.php';

$id = $_GET['id'] ?? '';
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

// Initialize existing_files for GET
$existing_files = !empty($row['filename'])
    ? array_map('trim', explode(',', $row['filename']))
    : [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $category = htmlspecialchars($_POST['category']);
    $pic      = htmlspecialchars($_POST['pic']);
    $service  = htmlspecialchars($_POST['service']);
    $company  = htmlspecialchars($_POST['company']);
    $start    = htmlspecialchars($_POST['start']);
    $endDate  = htmlspecialchars($_POST['endDate']);
    $sqft     = htmlspecialchars($_POST['sqft']);
    $rent     = htmlspecialchars($_POST['rent']);
    $remarks  = htmlspecialchars($_POST['remarks']);
    $duration = htmlspecialchars($_POST['duration']);
    $status   = $row['status']; // keep existing status

    $target_dir = "uploads/";

    // Remove checked files
    $to_remove = $_POST['remove_files'] ?? [];
    foreach ($to_remove as $file) {
        $path = $target_dir . basename($file);
        if (file_exists($path)) unlink($path);
        $existing_files = array_filter($existing_files, fn($f)=> $f!==$file);
    }

    // Upload new files
    $new_files = [];
    if (!empty($_FILES['files']['name'][0])) {
        $allowed = ['jpg','jpeg','png','gif','pdf','docx','doc'];
        foreach ($_FILES['files']['name'] as $i => $name) {
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) continue;
            if ($_FILES['files']['size'][$i] > 5_000_000) continue;
            $dst = $target_dir . basename($name);
            if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $dst)) {
                $new_files[] = $name;
            }
        }
    }

    // Merge filenames
    $all = array_merge($existing_files, $new_files);
    $filenames = $all ? implode(',', $all) : NULL;

    // Calculate monthsLeft
    $endObj = new DateTime($endDate);
    $now   = new DateTime();
    $diff  = $endObj->diff($now);
    $monthsLeft = $diff->m + ($diff->y*12);
    if ($endObj < $now) $monthsLeft = -$monthsLeft;

    // Update record
    $upd = $connection->prepare(
      "UPDATE form SET
         category=?, pic=?, service=?, company=?,
         start=?, endDate=?, sqft=?, rent=?,
         filename=?, remarks=?, monthsLeft=?,
         status=?, duration=?
       WHERE id=? AND department=?"
    );
    $upd->bind_param(
      'ssssssssssissis',
      $category,
      $pic,
      $service,
      $company,
      $start,
      $endDate,
      $sqft,
      $rent,
      $filenames,
      $remarks,
      $monthsLeft,
      $status,
      $duration,
      $id,
      $_SESSION['department']
    );
    if ($upd->execute()) {
        header("Location: view.php?id=".urlencode($id));
        exit();
    } else {
        echo "Error updating record: " . $upd->error;
    }
    $upd->close();
    $connection->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Update Agreement</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    body { font-family:'Segoe UI',sans-serif; background:#f7fafc; margin:0; }
    .container {
      width:90%; max-width:900px; margin:15px auto; padding:30px;
      background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);
    }
    h1 { text-align:center; color:#444; margin-bottom:10px; }
    .required-note {
      grid-column:1/3; margin-left:50px;
      color:#555; font-size:.9rem; margin-bottom:15px;
    }
    form { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    label { font-weight:600; margin-left:50px; color:#555; }
    .required-star { color:red; margin-left:4px; }
    input, select, textarea {
      padding:5px; border:1px solid #ddd; border-radius:8px;
      width:100%; font-size:16px; background:#f9f9f9;
    }
    textarea { height:80px; resize:none; }
    .btn {
      grid-column:span 2; background:blue; color:#fff; padding:10px;
      border:none; border-radius:15px; cursor:pointer;
      transition:background .3s;
    }
    .btn:hover { background:#fff; color:grey; }
    .back-button {
      background:none; border:none; font-size:40px; cursor:pointer;
      transition:transform .3s;
    }
    .back-button:hover { transform:scale(1.2); color:#aaa; }

    /* Existing files */
    .existing-files { grid-column:1/3; margin-top:20px; }
    .existing-files > label {
      display:flex; align-items:center; margin-left:50px; color:#555;
    }
    .existing-files .note {
      margin-left:50px; color:#555; font-size:.9rem; margin-bottom:10px;
    }
    .file-row {
      display:flex; justify-content:space-between;
      align-items:center; margin:0 50px 5px;
    }
  </style>
</head>
<body>
  <div class="container">
    <button onclick="history.back()" class="back-button">
      <i class="bx bx-arrow-back"></i>
    </button>
    <h1>Update Agreement</h1>
    <p class="required-note">
      <span class="required-star">*</span> Required fields
    </p>
    <form method="POST" enctype="multipart/form-data">
      <label for="category">
        Category<span class="required-star">*</span>:
      </label>
      <select name="category" id="category" required>
        <option value="licensing"      <?= $row['category']=='licensing'?'selected':'' ?>>Licensing</option>
        <option value="tenant"         <?= $row['category']=='tenant'   ?'selected':'' ?>>Tenant</option>
        <option value="service"        <?= $row['category']=='service'  ?'selected':'' ?>>Service</option>
        <option value="outsource"      <?= $row['category']=='outsource'?'selected':'' ?>>Outsource</option>
        <option value="biomedical-facilities" <?= $row['category']=='biomedical-facilities'?'selected':'' ?>>Marcomm</option>
        <option value="marcomm"        <?= $row['category']=='marcomm'  ?'selected':'' ?>>Marcomm/Insurance</option>
        <option value="clinical"       <?= $row['category']=='clinical' ?'selected':'' ?>>Clinical</option>
        <option value="support"        <?= $row['category']=='support'  ?'selected':'' ?>>Support Mtn.</option>
      </select>

      <label for="pic">
        PIC/Owner Name<span class="required-star">*</span>:
      </label>
      <input type="text" name="pic" id="pic" value="<?= htmlspecialchars($row['pic']) ?>" required>

      <label for="service">
        Services<span class="required-star">*</span>:
      </label>
      <input type="text" name="service" id="service" value="<?= htmlspecialchars($row['service']) ?>" required>

      <label for="company">
        Company Name/Act Name<span class="required-star">*</span>:
      </label>
      <input type="text" name="company" id="company" value="<?= htmlspecialchars($row['company']) ?>" required>

      <label for="start">
        Start Date<span class="required-star">*</span>:
      </label>
      <input type="date" name="start" id="start" value="<?= $row['start'] ?>" required>

      <label for="endDate">
        End Date<span class="required-star">*</span>:
      </label>
      <input type="date" name="endDate" id="endDate" value="<?= $row['endDate'] ?>" required>

      <label for="sqft">SQFT:</label>
      <input type="text" name="sqft" id="sqft" value="<?= htmlspecialchars($row['sqft']) ?>">

      <label for="rent">
        Amount (RM)<span class="required-star">*</span>:
      </label>
      <input type="text" name="rent" id="rent" value="<?= htmlspecialchars($row['rent']) ?>" required>

      <label for="remarks">Remarks:</label>
      <textarea name="remarks" id="remarks"><?= htmlspecialchars($row['remarks']) ?></textarea>

      <label for="duration">Duration:</label>
      <textarea name="duration" id="duration"><?= htmlspecialchars($row['duration']) ?></textarea>

      <label for="files">Upload Files:</label>
      <input type="file" name="files[]" id="files" multiple>

      <!-- Existing Files -->
      <div class="existing-files">
        <label>
          Existing Files<span class="required-star">*</span>:
        </label>
        <p class="note">
          Check the box next to any file you want to remove before clicking “Update.”
        </p>
        <?php if (empty($existing_files)): ?>
          <p class="note">No existing files.</p>
        <?php else: ?>
          <?php foreach ($existing_files as $file): ?>
            <div class="file-row">
              <span><?= htmlspecialchars($file) ?></span>
              <label>
                <input type="checkbox" name="remove_files[]" value="<?= htmlspecialchars($file) ?>">
                Remove
              </label>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <input type="hidden" name="status" value="<?= htmlspecialchars($row['status']) ?>">
      <button type="submit" class="btn">Update</button>
    </form>
  </div>
</body>
</html>
