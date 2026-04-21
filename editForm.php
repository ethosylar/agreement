<?php
session_start();
include 'dbconn.php';

// 1) Auth check
if (!isset($_SESSION['department'])) {
    header("Location: index.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
    header("Location: home.php");
    exit();
}

// audit helper
function write_audit(mysqli $conn, string $user, string $dept, string $action, string $table_name, int $record_id, array $changed = []) {
    $ip   = $_SERVER['REMOTE_ADDR']     ?? '';
    $ua   = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $json = json_encode($changed, JSON_UNESCAPED_UNICODE);
    $stmt = $conn->prepare(<<<'SQL'
        INSERT INTO audit_log
          (user_id, department, action, table_name, record_id, changed_data, ip_address, user_agent)
        VALUES
          (?,       ?,          ?,      ?,          ?,         CAST(? AS JSON), ?,          ?)
    SQL
    );
    $stmt->bind_param("ssssisss", $user, $dept, $action, $table_name, $record_id, $json, $ip, $ua);
    $stmt->execute();
    $stmt->close();
}

// 2) Load existing record
if ($_SESSION['username']==='azean') {
    $stmt = $connection->prepare("SELECT * FROM form WHERE id = ?");
    $stmt->bind_param("i", $id);
} else {
    $stmt = $connection->prepare("SELECT * FROM form WHERE id = ? AND department = ?");
    $stmt->bind_param("is", $id, $_SESSION['department']);
}
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc() ?: die("No data.");
$stmt->close();

// detect if this was an “ongoing” (no end date) record
$noEndInit = ($row['endDate']==='9999-12-31');

// parse existing filenames
$existing_files = $row['filename']
    ? array_map('trim', explode(',', $row['filename']))
    : [];

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
  // sanitize
  $category    = $_POST['category'] ?? '';
  $pic         = $_POST['pic']      ?? '';
  $service     = $_POST['service']  ?? '';
  $company     = $_POST['company']  ?? '';
  $start       = $_POST['start']    ?? '';
  $endDateRaw  = $_POST['endDate']  ?? '';
  $noEnd       = isset($_POST['noEndDate']);
  $sqft        = $_POST['sqft']     ?? '';
  $rent        = $_POST['rent']     ?? '';
  $remarks     = $_POST['remarks']  ?? '';
  $duration    = $_POST['duration'] ?? '';
  $department  = $_SESSION['department'];

  // validate
  if ($category==='') $errors['category']='Category is required.';
  if ($pic==='')      $errors['pic']='PIC/Owner Name is required.';
  if ($service==='')  $errors['service']='Service is required.';
  if ($company==='')  $errors['company']='Company is required.';
  if ($start==='')    $errors['start']='Start date is required.';
  if (!$noEnd && $endDateRaw==='' ) {
    $errors['endDate']='End date is required or check “No end date.”';
  }
  if ($rent==='')     $errors['rent']='Amount is required.';

  if (empty($errors)) {
    // compute endDate & monthsLeft
    if ($noEnd) {
      $endDate    = '9999-12-31';
      $monthsLeft = 9999;
      $duration   = 'Ongoing';
    } else {
      $endDate    = $endDateRaw;
      $d1         = new DateTime($endDate);
      $d2         = new DateTime();
      $diff       = $d1->diff($d2);
      $monthsLeft = $diff->m + $diff->y*12;
      if ($d1 < $d2) $monthsLeft = -$monthsLeft;
    }
    // file uploads & removals
    $baseDir = __DIR__.'/uploads/';
    $deptDir = $baseDir.$department.'/';
    if (!is_dir($deptDir)) mkdir($deptDir,0777,true);

    // remove ticked files
    $to_remove = $_POST['remove_files'] ?? [];
    foreach ($to_remove as $f) {
      $p = $deptDir.basename($f);
      if (is_file($p)) unlink($p);
      $existing_files = array_filter($existing_files, fn($x)=>$x!==$f);
    }
    // upload new
    $new_files  = [];
    $renameBase = trim($_POST['newFileName'] ?? '');
    $allowed    = ['jpg','jpeg','png','gif','pdf','docx','doc','xls','xlsx','ppt','pptx','txt','zip'];
    foreach (($_FILES['files']['name']??[]) as $i=>$orig) {
      if (!$orig) continue;
      $ext = strtolower(pathinfo($orig,PATHINFO_EXTENSION));
      if (!in_array($ext,$allowed,true)) continue;
      $safe = $renameBase
          ? pathinfo($renameBase,PATHINFO_FILENAME)."_{$i}.{$ext}"
          : basename($orig);
      if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $deptDir.$safe)) {
        $new_files[] = $safe;
      }
    }
    $all_files = array_merge($existing_files,$new_files);
    $filenames = $all_files? implode(',', $all_files): null;

    // update
    $upd = $connection->prepare(<<<'SQL'
      UPDATE form SET
        category=?,pic=?,service=?,company=?,
        start=?,endDate=?,sqft=?,rent=?,
        filename=?,remarks=?,monthsLeft=?,
        status=?,duration=?
      WHERE id=? AND department=?
    SQL
    );
    $upd->bind_param(
      'sssssssssisssis',
      $category,$pic,$service,$company,
      $start,$endDate,$sqft,$rent,
      $filenames,$remarks,$monthsLeft,
      $row['status'],$duration,
      $id,$_SESSION['department']
    );
    if ($upd->execute()) {
      // audit diff
      $fields  = ['category','pic','service','company','start','endDate','sqft','rent','remarks','duration','filename'];
      $oldData = array_intersect_key($row,array_flip($fields));
      $newData = [
        'category'=>$category,'pic'=>$pic,'service'=>$service,'company'=>$company,
        'start'=>$start,'endDate'=>$endDate,'sqft'=>$sqft,'rent'=>$rent,
        'remarks'=>$remarks,'duration'=>$duration,'filename'=>$filenames
      ];
      write_audit(
        $connection,
        $_SESSION['username'],
        $_SESSION['department'],
        'UPDATE',
        'form',
        $id,
        ['old'=>$oldData,'new'=>$newData]
      );
      header("Location: view.php?id={$id}");
      exit;
    } else {
      $errors['general']="Update error: ".$upd->error;
    }
    $upd->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Agreement</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
  			body { font-family:'Segoe UI',sans-serif; background:#f7fafc; margin:0; }
			.container {
			width:90%; max-width:900px; margin:15px auto; padding:30px;
			background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);
			}
			.container > form {
			  display: block !important;
			  grid-template-columns: none !important;
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
    body { font-family:sans-serif; background:#f7fafc; margin:0 }
    .container {
      max-width:900px; margin:20px auto; padding:30px;
      background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);
    }
    h1 { text-align:center; margin-bottom:20px }
    .form-table { width:100%; border-collapse:collapse; }
    .form-table th,
    .form-table td {
      padding: 8px 10px;
      vertical-align: top;
    }
    .form-table th {
      text-align: right;
      width: 30%;
      color: #555;
      font-weight: 600;
    }
    .form-table td {
      width: 70%;
    }
    .required-star { color:red;margin-left:4px }
    .form-error { color:#c00; font-size:.875rem; margin-top:4px }
    .btn { background:blue;color:#fff;padding:10px 20px;border:none;border-radius:8px; cursor:pointer; }
    .btn:hover { background:#0053a0 }
    .back-button { background:none;border:none;font-size:28px;cursor:pointer }
    .no-end-row td { padding-left:0!important }
    .no-end-row label { display:inline-flex; align-items:center; white-space:nowrap }
  </style>
</head>
<body>
  <div class="container">
    <button onclick="history.back()" class="back-button"><i class="bx bx-arrow-back"></i></button>
    <h1>Edit Agreement</h1>
    <?php if(!empty($errors)): ?>
      <div class="form-error"><?= implode("<br>",$errors) ?></div>
    <?php endif ?>

    <form method="POST" enctype="multipart/form-data">
      <table class="form-table"><tbody>

        <tr>
          <th>Category<span class="required-star">*</span></th>
          <td>
            <select name="category" required>
              <option value="licensing"      <?= $row['category']=='licensing'?'selected':'' ?>>Licensing</option>
              <option value="tenant"         <?= $row['category']=='tenant'   ?'selected':'' ?>>Tenant</option>
              <option value="service"        <?= $row['category']=='service'  ?'selected':'' ?>>Service</option>
              <option value="outsource"      <?= $row['category']=='outsource'?'selected':'' ?>>Outsource</option>
              <option value="biomedical-facilities" <?= $row['category']=='biomedical-facilities'?'selected':'' ?>>Biomedical</option>
              <option value="marcomm"        <?= $row['category']=='marcomm'   ?'selected':'' ?>>Marcomm/Insurance</option>
              <option value="clinical"       <?= $row['category']=='clinical'  ?'selected':'' ?>>Clinical</option>
              <option value="support"        <?= $row['category']=='support'   ?'selected':'' ?>>Support</option>
            </select>
            <?php if(isset($errors['category'])): ?><div class="form-error"><?= $errors['category'] ?></div><?php endif ?>
          </td>
        </tr>

        <tr>
          <th>PIC/Owner<span class="required-star">*</span></th>
          <td>
            <input type="text" name="pic" value="<?= htmlspecialchars($row['pic']) ?>" required>
            <?php if(isset($errors['pic'])): ?><div class="form-error"><?= $errors['pic'] ?></div><?php endif ?>
          </td>
        </tr>

        <tr>
          <th>Description<span class="required-star">*</span></th>
          <td>
            <input type="text" name="service" value="<?= htmlspecialchars($row['service']) ?>" required>
            <?php if(isset($errors['service'])): ?><div class="form-error"><?= $errors['service'] ?></div><?php endif ?>
          </td>
        </tr>

        <tr>
          <th>Company<span class="required-star">*</span></th>
          <td>
            <input type="text" name="company" value="<?= htmlspecialchars($row['company']) ?>" required>
            <?php if(isset($errors['company'])): ?><div class="form-error"><?= $errors['company'] ?></div><?php endif ?>
          </td>
        </tr>

        <tr>
          <th>Start Date<span class="required-star">*</span></th>
          <td>
            <input type="date" name="start" id="start" value="<?= $row['start'] ?>" required>
            <?php if(isset($errors['start'])): ?><div class="form-error"><?= $errors['start'] ?></div><?php endif ?>
          </td>
        </tr>

        <tr>
          <th>End Date<?= $noEndInit?'':'<span class="required-star">*</span>' ?></th>
          <td>
            <input
              type="date"
              name="endDate"
              id="endDate"
              value="<?= $noEndInit?'':$row['endDate'] ?>"
              <?= $noEndInit?'disabled':'' ?>
              <?= $noEndInit?'':'required' ?>
            >
            <?php if(isset($errors['endDate'])): ?><div class="form-error"><?= $errors['endDate'] ?></div><?php endif ?>
          </td>
        </tr>

        <tr class="no-end-row">
          <th></th>
          <td>
            <label>
              <input
                type="checkbox"
                name="noEndDate"
                id="noEndDate"
                <?= $noEndInit?'checked':'' ?>
				> No end date
            </label>
          </td>
        </tr>

        <tr>
          <th>Duration</th>
          <td><textarea name="duration" id="duration"><?= htmlspecialchars($row['duration']) ?></textarea></td>
        </tr>

        <tr>
          <th>SQFT</th>
          <td><input type="text" name="sqft" value="<?= htmlspecialchars($row['sqft']) ?>"></td>
        </tr>

        <tr>
          <th>Amount (RM)<span class="required-star">*</span></th>
          <td>
            <input type="text" name="rent" value="<?= htmlspecialchars($row['rent']) ?>" required>
            <?php if(isset($errors['rent'])): ?><div class="form-error"><?= $errors['rent'] ?></div><?php endif ?>
          </td>
        </tr>

        <tr>
          <th>Remarks</th>
          <td><textarea name="remarks"><?= htmlspecialchars($row['remarks']) ?></textarea></td>
        </tr>

        <tr>
          <th>Upload Files</th>
          <td>
            <input type="file" name="files[]" multiple>
            <small>Optional new uploads</small>
          </td>
        </tr>

        <tr>
          <th>Rename base</th>
          <td>
            <input type="text" name="newFileName" placeholder="Base name">
            <small>(no extension)</small>
          </td>
        </tr>

        <tr>
          <th>Existing Files</th>
          <td>
            <?php if(empty($existing_files)): ?>
              <p><em>None.</em></p>
            <?php else: foreach($existing_files as $f): ?>
              <label style="display:block; margin-bottom:4px">
                <input type="checkbox" name="remove_files[]" value="<?= htmlspecialchars($f) ?>">
                Remove <?= htmlspecialchars($f) ?>
              </label>
            <?php endforeach; endif ?>
          </td>
        </tr>

        <tr>
          <th></th>
          <td><button type="submit" class="btn">Update</button></td>
        </tr>

      </tbody></table>
    </form>
  </div>

  <script>
  (function(){
    const startEl = document.getElementById('start');
    const endEl   = document.getElementById('endDate');
    const chk     = document.getElementById('noEndDate');
    const dur     = document.getElementById('duration');
    const form    = document.querySelector('form');

    function updateUI(){
      if(chk.checked){
        endEl.disabled = true;
        endEl.required = false;
        dur.value      = 'Ongoing';
      } else {
        endEl.disabled = false;
        endEl.required = true;
        calcDur();
      }
    }
    function calcDur(){
      if(!startEl.value||!endEl.value) return dur.value='';
      const s=new Date(startEl.value), e=new Date(endEl.value);
      if(e<s) return dur.value='';
      let y=e.getFullYear()-s.getFullYear(),
          m=e.getMonth()-s.getMonth(),
          d=e.getDate()-s.getDate();
      if(d<0){ m--; d+=new Date(e.getFullYear(),e.getMonth(),0).getDate(); }
      if(m<0){ y--; m+=12; }
      dur.value = 
        (y? y+' year'+(y>1?'s ':' '):'')+
        (m? m+' month'+(m>1?'s ':' '):'')+
        (d? d+' day'+(d>1?'s':''):'');
    }

    chk.addEventListener('change', updateUI);
    startEl.addEventListener('change', ()=>{ if(!chk.checked) calcDur() });
    endEl  .addEventListener('change', calcDur);
    form.addEventListener('submit', e=>{
      if(!chk.checked && !endEl.value){
        endEl.reportValidity();
        e.preventDefault();
      }
    });

    updateUI();
  })();
  </script>
</body>
</html>
