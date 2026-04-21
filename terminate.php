<?php
session_start();
include 'dbconn.php';

// 1) Must be logged in
if (!isset($_SESSION['department'])) {
    header('Location: index.php');
    exit();
}

// 2) Audit helper (must come before any calls)
function write_audit(
    mysqli $conn,
    string $user,
    string $dept,
    string $action,
    string $table_name,
    int    $record_id,
    array  $data = []
) {
    $ip   = $_SERVER['REMOTE_ADDR']     ?? '';
    $ua   = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);

    $stmt = $conn->prepare(<<<'SQL'
        INSERT INTO audit_log
          (user_id, department, action, table_name, record_id, changed_data, ip_address, user_agent)
        VALUES
          (?,       ?,          ?,      ?,          ?,         CAST(? AS JSON), ?,          ?)
    SQL
    );
    $stmt->bind_param("ssssisss",
        $user, $dept, $action, $table_name, $record_id,
        $json, $ip, $ua
    );
    $stmt->execute();
    $stmt->close();
}

// 3) If this is a POST, process the “archive” action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3a) Validate
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id < 1) {
        die("Invalid request.");
    }

    // 3b) Fetch the row
    $stmt = $connection->prepare("SELECT * FROM form WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die("Record not found.");
    }
    $old = $result->fetch_assoc();
    $stmt->close();

    // 3c) Insert into terminate
    $ins = $connection->prepare(<<<'SQL'
        INSERT INTO terminate
          (id, status, department, category, pic, service, company,
           start, endDate, rent, remarks, duration, monthsLeft, filename, termination_date)
        VALUES
          (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    SQL
    );
    $ins->bind_param(
        "issssssssissss",
        $old['id'], $old['status'],   $old['department'],
        $old['category'], $old['pic'], $old['service'],
        $old['company'],  $old['start'], $old['endDate'],
        $old['rent'],     $old['remarks'], $old['duration'],
        $old['monthsLeft'],$old['filename']
    );
    $ins->execute();
    $ins->close();

    // 3d) Audit that insert
    write_audit(
        $connection,
        $_SESSION['username'],
        $_SESSION['department'],
        'ARCHIVE_INSERT',
        'terminate',
        $id,
        $old
    );

    // 3e) Delete from form
    $del = $connection->prepare("DELETE FROM form WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    $deleted = $del->affected_rows;
    $del->close();

    if ($deleted) {
        // 3f) Audit that delete
        write_audit(
            $connection,
            $_SESSION['username'],
            $_SESSION['department'],
            'ARCHIVE_DELETE',
            'form',
            $id,
            $old
        );

        echo "<script>
            alert('Record successfully archived.');
            window.location.href='terminate.php';
        </script>";
        exit();
    } else {
        echo "<script>
            alert('Failed to archive record.');
            window.location.href='terminate.php';
        </script>";
        exit();
    }
}

// 4) If we get here, it’s a GET — render your archive list
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Terminated Records</title>
		<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
		<link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
		<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<link rel="shortcut icon" type="x-icon" href="hsptl.png">
		<link rel="stylesheet" href="style.css">
		<script>
			$(document).ready(function () {
				var table = $('#example').DataTable({
					columnDefs: [
					{ targets: 0, visible: false } // Hide the ID column
					]
				});
				
				// Filter table based on department selection
				$('#departmentFilter').on('change', function () {
					var selectedDepartment = $(this).val();
					table.column(1).search(selectedDepartment).draw();
				});
				
				$(document).ready(function () {
					var table = $('#example').DataTable();
					
					// Filter table based on category selection
					$('#categoryFilter').on('change', function () {
						var selectedCategory = $(this).val();
						table.column(0).search(selectedCategory).draw();
					});
					
					$('.toggle-btn').click(()=>$('.sidebar').toggleClass('active'));
				});
			});
			
			document.addEventListener("DOMContentLoaded", function() {
				const table = document.querySelector(".center-table tbody");
				const rows = table.querySelectorAll("tr");
				
				// Assuming the table is rectangular (each row has the same number of cells)
				const columnCount = rows[0].cells.length;
				
				for (let colIndex = 0; colIndex < columnCount; colIndex++) {
					for (let rowIndex = 0; rowIndex < rows.length; rowIndex++) {
						const cell = rows[rowIndex].cells[colIndex]; // Get the cell at the specific column index
						
						if (colIndex === 9) { // Adjust this index if the target column position changes
							const monthsLeft = parseInt(cell.textContent.trim(), 10); // Ensure trimming whitespace
							
							if (!isNaN(monthsLeft)) { // Check if the value is a valid number
								if (monthsLeft < 3 && monthsLeft >= 0) {
									cell.classList.add("lower3months");
									} else if (monthsLeft >= 3) {
									cell.classList.add("higher3months");
									} else {
									cell.classList.add("over3months");
								}
								} else {
								console.warn("Invalid monthsLeft value in cell:", cell.textContent);
							}
						}
					}
				}
			});
		</script>
	</head>
	<body>
		<?php
			$pageTitle="Terminated Records";
			$headerTitle="Terminated Records";
			$activePage="archive";
			include "header.php";
			include "sidebar.php";
		?>
		
		<div>
			<div class="filter-container">
				<label for="departmentFilter" style="color: black;">Department:</label>
				<select id="departmentFilter">
					<option value="">All</option>
					<?php foreach ($dpt as $department): ?>
					<option value="<?php echo htmlspecialchars($department); ?>"><?php echo htmlspecialchars($department); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<div class="center-table">
				<table id="example" class="display">
					<thead>
						<tr>
							<th>ID</th>
							<th>Status</th>
							<th>Department</th>
							<th>Category</th>
							<th>PIC</th>
							<th>Services</th>
							<th>Company Name/Act Name</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Amount(RM)</th>
							<th>Remarks</th>
							<th>Duration</th>
							<th>Months Left Before End</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$query_terminated = "SELECT id,status, department, category, pic, service, company, start, endDate, rent, remarks, duration, monthsLeft,  filename FROM terminate ORDER BY termination_date DESC";
							
							$result_terminated = mysqli_query($connection, $query_terminated);
							
							// Debugging - Check if the query fails
							if (!$result_terminated) {
								die("Query failed: " . mysqli_error($connection));
							}
							
							// Check if there are results
							if (mysqli_num_rows($result_terminated) > 0) {
								while ($row = mysqli_fetch_assoc($result_terminated)) {
									echo "<tr>";
									echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["department"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["category"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["pic"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["service"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["company"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["start"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["endDate"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["rent"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["remarks"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["duration"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["monthsLeft"]) . "</td>";
									echo "<td>
									<form action='view3.php' method='get' style='display:inline;'>
									<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
									<button type='submit' class='btn'>View</button>
									</form>
									</td>";
									echo "</tr>";
								}
								} else {
								echo "<tr><td colspan='14'>No archive records found.</td></tr>";
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<?php include "footer.php"; ?>
		