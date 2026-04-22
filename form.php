<?php
session_start();
include("dbconn.php");

// 1) Only allow logged-in users
if (!isset($_SESSION['department'])) {
    echo "<script>
      alert('You are not logged in.');
      window.location.href='login.html';
    </script>";
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 2) Sanitize
    $status     = mysqli_real_escape_string($connection, $_POST['status']   ?? '');
    $category   = mysqli_real_escape_string($connection, $_POST['category'] ?? '');
    $pic        = mysqli_real_escape_string($connection, $_POST['pic']      ?? '');
    $service    = mysqli_real_escape_string($connection, $_POST['service']  ?? '');
    $company    = mysqli_real_escape_string($connection, $_POST['company']  ?? '');
    $start      = mysqli_real_escape_string($connection, $_POST['start']    ?? '');
    $sqft       = mysqli_real_escape_string($connection, $_POST['sqft']     ?? '');
    $rent       = mysqli_real_escape_string($connection, $_POST['rent']     ?? '');
    $remarks    = mysqli_real_escape_string($connection, $_POST['remarks']  ?? '');
    $duration   = mysqli_real_escape_string($connection, $_POST['duration'] ?? '');
    $department = mysqli_real_escape_string($connection, $_SESSION['department']);

    // 3) Figure out if “no end date” was checked
    $noEnd = isset($_POST['noEndDate']);

    // 4) Validate required fields
    if ($category === '')    $errors['category'] = 'Category is required.';
    if ($pic      === '')    $errors['pic']      = 'PIC/Owner Name is required.';
    if ($service  === '')    $errors['service']  = 'Service is required.';
    if ($company  === '')    $errors['company']  = 'Company is required.';
    if ($start    === '')    $errors['start']    = 'Start date is required.';
    if (! $noEnd && $_POST['endDate'] === '') {
        $errors['endDate'] = 'End date is required, or check "No end date".';
    }
    if ($rent     === '')    $errors['rent']     = 'Amount is required.';
    if (! in_array($status, ['active','expired'], true)) {
        $errors['status'] = 'Please select a valid status.';
    }

    if (empty($errors)) {
        // 5) Compute monthsLeft
        if ($noEnd) {
            // sentinel far-future date + large monthsLeft
            $endDate    = '9999-12-31';
            $monthsLeft = 9999;
            $duration   = 'Ongoing';
        } else {
            $endDate    = mysqli_real_escape_string($connection, $_POST['endDate']);
            $endObj     = new DateTime($endDate);
            $nowObj     = new DateTime();
            $diff       = $endObj->diff($nowObj);
            $monthsLeft = $diff->m + ($diff->y * 12);
            if ($endObj < $nowObj) {
                $monthsLeft = - $monthsLeft;
            }
        }

        // 6) Handle file uploads into department folder
        $baseDir = __DIR__ . "/uploads";
        $deptDir = $baseDir . DIRECTORY_SEPARATOR . $department . DIRECTORY_SEPARATOR;
        if (!is_dir($baseDir)) mkdir($baseDir, 0777, true);
        if (!is_dir($deptDir)) mkdir($deptDir, 0777, true);

        $uploaded = [];
        if (!empty($_FILES['files']['name'][0])) {
            $allowed = ["jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","txt","zip"];
            foreach ($_FILES['files']['name'] as $i => $origName) {
                $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) continue;
                $newName = !empty($_POST['newFileName'])
                         ? basename($_POST['newFileName']) . "_{$i}.{$ext}"
                         : basename($origName);
                $dst = $deptDir . $newName;
                if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $dst)) {
                    $uploaded[] = $newName;
                }
            }
        }
        $file_names_string = implode(", ", $uploaded);

        // 7) Insert into DB
        $stmt = $connection->prepare(<<<'SQL'
            INSERT INTO form
              (category,pic,service,company,start,endDate,
               sqft,rent,filename,remarks,monthsLeft,
               department,status,duration)
            VALUES
              (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        SQL
        );
        $stmt->bind_param(
            "sssssssssiisss",
            $category,           // s
            $pic,                // s
            $service,            // s
            $company,            // s
            $start,              // s
            $endDate,            // s (may be sentinel)
            $sqft,               // s
            $rent,               // s
            $file_names_string,  // s
            $remarks,            // s
            $monthsLeft,         // i
            $department,         // s
            $status,             // s
            $duration            // s
        );

        if ($stmt->execute()) {
            $newId = $stmt->insert_id;
            // 8) Audit
            $changedData = [
                'category'=>$category,'pic'=>$pic,'service'=>$service,
                'company'=>$company,'start'=>$start,'endDate'=>$endDate,
                'rent'=>$rent,'duration'=>$duration,'filename'=>$uploaded
            ];
            write_audit(
                $connection,
                $_SESSION['username'],
                $_SESSION['department'],
                'INSERT',
                'form',
                $newId,
                $changedData
            );
            header("Location: home.php");
            exit();
        } else {
            $errors['general'] = "DB error: " . $stmt->error;
        }
    }
}

// 9) Write audit helper
function write_audit(
    mysqli $conn,
    string $user,
    string $dept,
    string $action,
    string $table_name,
    int $record_id,
    array $data = []
) {
    $ip   = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua   = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);

    if ($json === false) {
        $json = '{}';
    }

    $stmt = $conn->prepare("
        INSERT INTO audit_log
        (user_id, department, action, table_name, record_id, changed_data, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        'ssssisss',
        $user,
        $dept,
        $action,
        $table_name,
        $record_id,
        $json,
        $ip,
        $ua
    );

    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Agreement Form</title>
		<link href='https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
		<style>
			body { font-family: 'Segoe UI', sans-serif; background:#f7fafc; margin:0; }
			.container { max-width:900px; margin:20px auto; padding:30px;
			background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
			.container > form {
			  display: block !important;
			  grid-template-columns: none !important;
			}
			h1 { text-align:center; color:#444; margin-bottom:10px; }
			.required-note {
			grid-column: 1 / 3;
			margin-left: 50px;
			color: #555;
			font-size: .9rem;
			margin-bottom: 15px;
			}
			form {
			  display: grid;
			  grid-template-columns: 1fr 1fr;
			  gap: 10px;
			}
			label { margin-left:50px; font-weight:600; color:#555; display:flex; align-items:center; }
			input, select, textarea {
			padding:5px; border:1px solid #ddd; border-radius:8px;
			width:100%; background:#f9f9f9; font-size:16px;
			}
			textarea { resize:none; height:80px; }
			.btn {
			grid-column: span 2; background:blue; color:#fff; padding:10px;
			border:none; border-radius:15px; cursor:pointer;
			transition:background .3s;
			}
			.btn:hover { background:#fff; color:grey; }
			.back-button {
			background:none; border:none; font-size:40px; cursor:pointer;
			transition:transform .3s,color .3s;
			}
			.back-button:hover { transform:scale(1.2); color:#aaa; }
			
			/* toast styles */
			.toast-container {
			position:fixed; top:1rem; right:1rem;
			display:flex; flex-direction:column; gap:.5rem;
			z-index:999;
			}
			.toast {
			padding:.75rem 1rem; border-radius:.5rem;
			box-shadow:0 2px 6px rgba(0,0,0,0.2);
			font-weight:600;
			}
			.error-toast {
			background:#f8d7da; color:#721c24;
			border:1px solid #f5c6cb;
			}
			
			/* inline error spans full row */
			.inline-error {
			grid-column: 1 / 3;
			margin-left: 50px;
			color: #c00;
			font-size: .875rem;
			}
			
			/* required star */
			.required-star {
			color: red;
			margin-left: 4px;
			}
			 /* new form‐table styles */
    .form-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }
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
    .form-table .required-star {
      color: red;
      margin-left: 4px;
    }
    .form-table input[type="date"]:disabled,
    .form-table input[disabled],
    .form-table textarea[disabled] {
      background: #eee;
    }
    /* inline errors spanning both columns */
    .form-error {
      color: #c00;
      font-size: .875rem;
      padding: 4px 0;
      grid-column: 1 / 3;
    }
	.form-table .no-end-row td {
  padding-left: 0;     /* kill the usual 10px cell padding */
}
.form-table .no-end-row td label {
  display: inline-flex;  /* ensure label&checkbox align side by side */
  align-items: center;
  margin: 0;             /* remove any stray margins */
  white-space: nowrap;
}
		</style>
	</head>
	<body>
		
		<div class="container">
			<a href="home.php" class="back-button"><i class='bx bx-arrow-back'></i></a>
			<h1>Agreement Form</h1>
			
			<p class="required-note">
				<span class="required-star">*</span> Required fields
			</p>
			
			<?php if (!empty($errors)): ?>
			<div class="toast-container">
				<?php foreach ($errors as $msg): ?>
				<div class="toast error-toast"><?= htmlspecialchars($msg) ?></div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
			
			<form method="POST" enctype="multipart/form-data">
			  <table class="form-table">
				<tbody>
				  <!-- Category -->
				  <tr>
					<th for="category">
					  Category<span class="required-star">*</span>
					</th>
					<td>
					  <select name="category" id="category" required>
						<option value="">-- select --</option>
						<option value="licensing"      <?= (($_POST['category']??'')==='licensing')?'selected':'' ?>>Licensing</option>
						<option value="tenant"         <?= (($_POST['category']??'')==='tenant')?'selected':'' ?>>Tenant</option>
						<option value="service"        <?= (($_POST['category']??'')==='service')?'selected':'' ?>>Services</option>
						<option value="outsource"      <?= (($_POST['category']??'')==='outsource')?'selected':'' ?>>Outsource</option>
						<option value="biomedical-facilities" <?= (($_POST['category']??'')==='biomedical-facilities')?'selected':'' ?>>Biomedical</option>
						<option value="marcomm"        <?= (($_POST['category']??'')==='marcomm')?'selected':'' ?>>Marcomm/Insurance</option>
						<option value="clinical"       <?= (($_POST['category']??'')==='clinical')?'selected':'' ?>>Clinical</option>
						<option value="support"        <?= (($_POST['category']??'')==='support')?'selected':'' ?>>Service & Support</option>
					  </select>
					  <?php if (isset($errors['category'])): ?>
						<div class="form-error"><?= htmlspecialchars($errors['category']) ?></div>
					  <?php endif; ?>
					</td>
				  </tr>

				  <!-- PIC/Owner -->
				  <tr>
					<th for="pic">
					  PIC/Owner Name<span class="required-star">*</span>
					</th>
					<td>
					  <input type="text" name="pic" id="pic" value="<?= htmlspecialchars($_POST['pic'] ?? '') ?>">
					  <?php if (isset($errors['pic'])): ?>
						<div class="form-error"><?= htmlspecialchars($errors['pic']) ?></div>
					  <?php endif; ?>
					</td>
				  </tr>

				  <!-- Service -->
				  <tr>
					<th for="service">
					  Description<span class="required-star">*</span>
					</th>
					<td>
					  <input type="text" name="service" id="service" value="<?= htmlspecialchars($_POST['service'] ?? '') ?>">
					  <?php if (isset($errors['service'])): ?>
						<div class="form-error"><?= htmlspecialchars($errors['service']) ?></div>
					  <?php endif; ?>
					</td>
				  </tr>

				  <!-- Company -->
				  <tr>
					<th for="company">
					  Company Name/Act name<span class="required-star">*</span>
					</th>
					<td>
					  <input type="text" name="company" id="company" value="<?= htmlspecialchars($_POST['company'] ?? '') ?>">
					  <?php if (isset($errors['company'])): ?>
						<div class="form-error"><?= htmlspecialchars($errors['company']) ?></div>
					  <?php endif; ?>
					  </td>
				  </tr>

				  <!-- Start Date -->
				  <tr>
					<th for="start">
					  Start Date<span class="required-star">*</span>
					</th>
					<td>
					  <input type="date" name="start" id="start"
							 value="<?= htmlspecialchars($_POST['start'] ?? '') ?>" required>
					  <?php if (isset($errors['start'])): ?>
						<div class="form-error"><?= htmlspecialchars($errors['start']) ?></div>
					  <?php endif; ?>
					</td>
				  </tr>

				  <!-- End Date -->
				  <tr>
					<th for="endDate">
					  End Date
					  <?php if (!isset($_POST['noEndDate'])): ?>
						<span class="required-star">*</span>
					  <?php endif; ?>
					</th>
					<td>
					  <input type="date" name="endDate" id="endDate"
							 value="<?= htmlspecialchars($_POST['endDate'] ?? '') ?>"
							 <?= isset($_POST['noEndDate']) ? 'disabled' : 'required' ?>>
					  <?php if (isset($errors['endDate'])): ?>
						<div class="form-error"><?= htmlspecialchars($errors['endDate']) ?></div>
					  <?php endif; ?>
					</td>
				  </tr>

				  <!-- No end date -->
				  <tr class="no-end-row">
					<th></th>
					<td>
					  <label>
						<input type="checkbox" name="noEndDate" id="noEndDate"
							   <?= isset($_POST['noEndDate']) ? 'checked' : '' ?>>
						No end date
					  </label>
					</td>
				  </tr>

				  <!-- Duration -->
				  <tr>
					<th for="duration">Duration</th>
					<td>
					  <textarea name="duration" id="duration"><?= htmlspecialchars($_POST['duration'] ?? '') ?></textarea>
					</td>
				  </tr>

				  <!-- SQFT -->
				  <tr>
					<th for="sqft">SQFT</th>
					<td>
					  <input type="text" name="sqft" id="sqft" value="<?= htmlspecialchars($_POST['sqft'] ?? '') ?>">
					</td>
				  </tr>

				  <!-- Amount -->
				  <tr>
					<th for="rent">
					  Amount (RM)<span class="required-star">*</span>
					</th>
					<td>
					  <input type="text" name="rent" id="rent" value="<?= htmlspecialchars($_POST['rent'] ?? '') ?>">
					  <?php if (isset($errors['rent'])): ?>
						<div class="form-error"><?= htmlspecialchars($errors['rent']) ?></div>
					  <?php endif; ?>
					</td>
				  </tr>

				  <!-- Upload Files -->
				  <tr>
					<th for="files">Upload Files</th>
					<td>
					  <input type="file" name="files[]" id="files" multiple>
					</td>
				  </tr>

				  <!-- Rename -->
				  <tr>
					<th for="newFileName">Rename file (optional)</th>
					<td>
					  <input type="text" name="newFileName" id="newFileName"
							 value="<?= htmlspecialchars($_POST['newFileName'] ?? '') ?>">
					</td>
				  </tr>

				  <!-- Remarks -->
				  <tr>
					<th for="remarks">Remarks</th>
					<td>
					  <textarea name="remarks" id="remarks"><?= htmlspecialchars($_POST['remarks'] ?? '') ?></textarea>
					</td>
				  </tr>

				  <!-- Status -->
				  <tr>
					<th for="status">
					  Status<span class="required-star">*</span>
					</th>
					<td>
					  <select name="status" id="status" required>
						<option value="">-- select --</option>
						<option value="active"  <?= (($_POST['status']??'')==='active')?'selected':'' ?>>Active</option>
						<option value="expired" <?= (($_POST['status']??'')==='expired')?'selected':'' ?>>Expired</option>
					  </select>
					  <?php if (isset($errors['status'])): ?>
						<div class="form-error"><?= htmlspecialchars($errors['status']) ?></div>
					  <?php endif; ?>
					</td>
				  </tr>

				  <!-- Submit button -->
				  <tr>
					<th></th>
					<td>
					  <button type="submit" class="btn">Submit</button>
					</td>
				  </tr>
				</tbody>
			  </table>
			</form>
		</div>
		<script>
  (function(){
    const startInput = document.getElementById('start');
    const endInput   = document.getElementById('endDate');
    const noEndChk   = document.getElementById('noEndDate');
    const duration   = document.getElementById('duration');
    const form       = document.querySelector('form');

    function updateUI(){
      if(noEndChk.checked){
        endInput.disabled = true;
        endInput.required = false;
        duration.value    = 'Ongoing';
      } else {
        endInput.disabled = false;
        endInput.required = true;
        calcDuration();
      }
    }
    function calcDuration(){
      if(!startInput.value||!endInput.value) return duration.value = '';
      let s = new Date(startInput.value),
          e = new Date(endInput.value);
      if(e < s) return duration.value = '';
      let y = e.getFullYear()-s.getFullYear(),
          m = e.getMonth()-s.getMonth(),
          d = e.getDate()-s.getDate();
      if(d<0){ m--; d+= new Date(e.getFullYear(),e.getMonth(),0).getDate(); }
      if(m<0){ y--; m+=12; }
      duration.value = 
        (y? y+' year'+(y>1?'s ':' '):'')+
        (m? m+' month'+(m>1?'s ':' '):'')+
        (d? d+' day'+(d>1?'s':''):'');
    }

    noEndChk.addEventListener('change', updateUI);
    startInput.addEventListener('change', ()=>{ if(!noEndChk.checked) calcDuration() });
    endInput.addEventListener('change', calcDuration);
    form.addEventListener('submit', e=>{
      if(!noEndChk.checked && !endInput.value){
        endInput.reportValidity();
        e.preventDefault();
      }
    });

    updateUI();
  })();
  </script>
	</body>
</html>