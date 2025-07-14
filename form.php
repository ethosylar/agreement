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
	
	$errors = [];
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// 1) Sanitize
		$status     = mysqli_real_escape_string($connection, $_POST['status']   ?? '');
		$category   = mysqli_real_escape_string($connection, $_POST['category'] ?? '');
		$pic        = mysqli_real_escape_string($connection, $_POST['pic']      ?? '');
		$service    = mysqli_real_escape_string($connection, $_POST['service']  ?? '');
		$company    = mysqli_real_escape_string($connection, $_POST['company']  ?? '');
		$start      = mysqli_real_escape_string($connection, $_POST['start']    ?? '');
		$endDate    = mysqli_real_escape_string($connection, $_POST['endDate']  ?? '');
		$sqft       = mysqli_real_escape_string($connection, $_POST['sqft']     ?? '');
		$rent       = mysqli_real_escape_string($connection, $_POST['rent']     ?? '');
		$remarks    = mysqli_real_escape_string($connection, $_POST['remarks']  ?? '');
		$duration   = mysqli_real_escape_string($connection, $_POST['duration'] ?? '');
		$department = mysqli_real_escape_string($connection, $_SESSION['department']);
		
		// 2) Validate required fields
		if ($category === '') $errors['category'] = 'Category is required.';
		if ($pic      === '') $errors['pic']      = 'PIC/Owner Name is required.';
		if ($service  === '') $errors['service']  = 'Service is required.';
		if ($company  === '') $errors['company']  = 'Company is required.';
		if ($start    === '') $errors['start']    = 'Start date is required.';
		if ($endDate  === '') $errors['endDate']  = 'End date is required.';
		if ($rent     === '') $errors['rent']     = 'Amount is required.';
		if (! in_array($status, ['active','expired'], true)) {
			$errors['status'] = 'Please select a valid status.';
		}
		
		// 3) If no errors, process uploads and insert
		if (empty($errors)) {
			// Calculate monthsLeft
			$endObj     = new DateTime($endDate);
			$nowObj     = new DateTime();
			$diff       = $endObj->diff($nowObj);
			$monthsLeft = $diff->m + ($diff->y * 12);
			if ($endObj < $nowObj) $monthsLeft = -$monthsLeft;
			
			// File uploads
			$target_dir = "uploads/";
			if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
			$uploaded = [];
			if (!empty($_FILES['files']['name'][0])) {
				$allowed = ["jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","txt","zip"];
				foreach ($_FILES['files']['name'] as $i => $name) {
					$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
					if (!in_array($ext, $allowed, true)) continue;
					$newName = (!empty($_POST['newFileName'])
					? basename($_POST['newFileName']) . "_$i.$ext"
					: basename($name));
					$dst = $target_dir . $newName;
					if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $dst)) {
						$uploaded[] = $newName;
					}
				}
			}
			$file_names_string = implode(", ", $uploaded);
			
			// Insert into DB
			$stmt = $connection->prepare(
			"INSERT INTO form
			(category,pic,service,company,start,endDate,
			sqft,rent,filename,remarks,monthsLeft,
			department,status,duration)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
			);
			
			$stmt->bind_param(
			// 10× s     i    3× s
			"ssssssssssisss",
			$category,           // s
			$pic,                // s
			$service,            // s
			$company,            // s
			$start,              // s (DATE)
			$endDate,            // s (DATE)
			$sqft,               // s
			$rent,               // s
			$file_names_string,  // s <-- your comma-list of filenames
			$remarks,            // s
			$monthsLeft,         // i <-- integer months left
			$department,         // s
			$status,             // s
			$duration            // s
			);
			
			if ($stmt->execute()) {
				// --- SUCCESS! either redirect immediately:
				header("Location: home.php");
				exit();
				
				// --- or, if you want to stay on this page and show a toast:
				// $_SESSION['toast_success'] = "Record inserted successfully.";
				} else {
				$errors['general'] = "DB error: " . $stmt->error;
			}
		}
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
			h1 { text-align:center; color:#444; margin-bottom:10px; }
			.required-note {
			grid-column: 1 / 3;
			margin-left: 50px;
			color: #555;
			font-size: .9rem;
			margin-bottom: 15px;
			}
			form { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
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
				<!-- Category -->
				<label for="category">Category<span class="required-star">*</span></label>
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
				<span class="inline-error"><?= htmlspecialchars($errors['category']) ?></span>
				<?php endif; ?>
				
				<!-- PIC -->
				<label for="pic">PIC/Owner Name<span class="required-star">*</span></label>
				<input type="text" name="pic" id="pic" value="<?= htmlspecialchars($_POST['pic'] ?? '') ?>">
				<?php if (isset($errors['pic'])): ?>
				<span class="inline-error"><?= htmlspecialchars($errors['pic']) ?></span>
				<?php endif; ?>
				
				<!-- Service -->
				<label for="service">Services<span class="required-star">*</span></label>
				<input type="text" name="service" id="service" value="<?= htmlspecialchars($_POST['service'] ?? '') ?>">
				<?php if (isset($errors['service'])): ?>
				<span class="inline-error"><?= htmlspecialchars($errors['service']) ?></span>
				<?php endif; ?>
				
				<!-- Company -->
				<label for="company">Company Name/Act name<span class="required-star">*</span></label>
				<input type="text" name="company" id="company" value="<?= htmlspecialchars($_POST['company'] ?? '') ?>">
				<?php if (isset($errors['company'])): ?>
				<span class="inline-error"><?= htmlspecialchars($errors['company']) ?></span>
				<?php endif; ?>
				
				<!-- Start Date -->
				<label for="start">Start Date<span class="required-star">*</span></label>
				<input type="date" name="start" id="start" value="<?= htmlspecialchars($_POST['start'] ?? '') ?>">
				<?php if (isset($errors['start'])): ?>
				<span class="inline-error"><?= htmlspecialchars($errors['start']) ?></span>
				<?php endif; ?>
				
				<!-- End Date -->
				<label for="endDate">End Date<span class="required-star">*</span></label>
				<input type="date" name="endDate" id="endDate" value="<?= htmlspecialchars($_POST['endDate'] ?? '') ?>">
				<?php if (isset($errors['endDate'])): ?>
				<span class="inline-error"><?= htmlspecialchars($errors['endDate']) ?></span>
				<?php endif; ?>
				
				<!-- SQFT -->
				<label for="sqft">SQFT</label>
				<input type="text" name="sqft" id="sqft" value="<?= htmlspecialchars($_POST['sqft'] ?? '') ?>">
				
				<!-- Rent -->
				<label for="rent">Amount (RM)<span class="required-star">*</span></label>
				<input type="text" name="rent" id="rent" value="<?= htmlspecialchars($_POST['rent'] ?? '') ?>">
				<?php if (isset($errors['rent'])): ?>
				<span class="inline-error"><?= htmlspecialchars($errors['rent']) ?></span>
				<?php endif; ?>
				
				<!-- Files -->
				<label for="files">Upload Files</label>
				<input type="file" name="files[]" id="files" multiple>
				
				<!-- Rename -->
				<label for="newFileName">Rename file (optional)</label>
				<input type="text" name="newFileName" id="newFileName" value="<?= htmlspecialchars($_POST['newFileName'] ?? '') ?>">
				
				<!-- Remarks -->
				<label for="remarks">Remarks</label>
				<textarea name="remarks" id="remarks"><?= htmlspecialchars($_POST['remarks'] ?? '') ?></textarea>
				
				<!-- Duration -->
				<label for="duration">Duration</label>
				<textarea name="duration" id="duration"><?= htmlspecialchars($_POST['duration'] ?? '') ?></textarea>
				
				<!-- Status -->
				<label for="status">Status<span class="required-star">*</span></label>
				<select name="status" id="status" required>
					<option value="">-- select --</option>
					<option value="active"  <?= (($_POST['status']??'')==='active')?'selected':'' ?>>Active</option>
					<option value="expired" <?= (($_POST['status']??'')==='expired')?'selected':'' ?>>Expired</option>
				</select>
				<?php if (isset($errors['status'])): ?>
				<span class="inline-error"><?= htmlspecialchars($errors['status']) ?></span>
				<?php endif; ?>
				
				<!-- Submit -->
				<button type="submit" class="btn">Submit</button>
			</form>
		</div>
		
	</body>
</html>
