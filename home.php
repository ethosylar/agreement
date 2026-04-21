<?php
	session_start(); // Start the session
	include 'dbconn.php'; // Include database connection
	
	// Check if the user is logged in
	if (!isset($_SESSION['department'])) {
		header("Location: index.php");
		exit(); // Stop further script execution
	}
	
	$categoryLabels = [
	'licensing'               => 'Licensing',
	'tenant'                  => 'Tenant',
	'service'                 => 'Services',
	'outsource'               => 'Outsource',
	'biomedical‑facilities'   => 'Biomedical',
	'marcomm'                 => 'Marcomm / Insurance',
	'clinical'                => 'Clinical',
	'support'                 => 'Service & Support'
	];
	
	$department = $_SESSION['department'];
	// Define categories
	$categories = ["licensing", "tenant", "services", "outsource", "biomedical-facilities", "marcomm/insurance", "clinical", "service & support maintenance"];
	
	// Function to execute SQL query and return result
	function executeQuery($query) {
		global $connection;
		$result = mysqli_query($connection, $query);
		if (!$result) {
			die("Error in query: " . mysqli_error($connection));
		}
		return $result;
	}
	$today = date('Y-m-d');
	$updateQuery = "UPDATE form 
	SET status = CASE 
	WHEN endDate >= ? THEN 'Active' 
	ELSE 'Expired' 
	END 
	WHERE department = ?";
	
	$updateStmt = $connection->prepare($updateQuery);
	$updateStmt->bind_param("ss", $today, $department);
	$updateStmt->execute();
	$updateStmt->close();
	
	$countSql = "
	SELECT
    SUM(CASE WHEN monthsLeft < 0 THEN 1 ELSE 0 END)       AS over3,
    SUM(CASE WHEN monthsLeft BETWEEN 0 AND 3 THEN 1 ELSE 0 END) AS within3,
    SUM(CASE WHEN monthsLeft > 3 THEN 1 ELSE 0 END)       AS more3
	FROM form
	WHERE department = ?
	";
	$countStmt = $connection->prepare($countSql);
	$countStmt->bind_param("s", $department);
	$countStmt->execute();
	$countStmt->bind_result($over3, $within3, $more3);
	$countStmt->fetch();
	$countStmt->close();
	
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Dashboard</title>
		<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
		<link href="https://cdn.jsdelivr.net/npm/boxicons/css/boxicons.min.css" rel="stylesheet">
		<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
		<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<link rel="shortcut icon" type="x-icon" href="hsptl.png">
		<link rel="stylesheet" href="style.css">
		
		<style>
			.success-toast {
			background: #d4edda;
			color:     #155724;
			border:    1px solid #c3e6cb;
			}
		</style>
		<script>
			$(document).ready(function () {
				var table = $('#example').DataTable();
				
				// Filter table based on category selection
				$('#categoryFilter').on('change', function () {
					var selectedCategory = $(this).val();
					table.column(0).search(selectedCategory).draw();
				});
				
				// Toggle sidebar
				$('.toggle-btn').click(function () {
					$('.sidebar').toggleClass('active');
				});
			});
			document.querySelectorAll(".list-item").forEach(item => {
				item.addEventListener("mouseenter", () => {
					document.querySelector(".sidebar").classList.add("active");
				});
				
				item.addEventListener("mouseleave", () => {
					document.querySelector(".sidebar").classList.remove("active");
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
						
						if (colIndex === 10) { // Adjust this index if the target column position changes
							const monthsLeft = parseInt(cell.textContent.trim(), 10); // Ensure trimming whitespace
							
							if (!isNaN(monthsLeft)) { // Check if the value is a valid number
								if (monthsLeft <= 3 && monthsLeft >= 0) {
									cell.classList.add("lower3months");
									} else if (monthsLeft > 3) {
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
		<?php if (!empty($_SESSION['toast_success'])): ?>
		<div class="toast-container">
			<div class="toast success-toast">
				<?= htmlspecialchars($_SESSION['toast_success']) ?>
			</div>
		</div>
		<?php unset($_SESSION['toast_success']); ?>
		<?php endif; ?>
		
		<?php
			$pageTitle = "Dashboard";
			$headerTitle = "DEPARTMENT : " . htmlspecialchars($department);
			$activePage = "dashboard";
			include "header.php";
			include "sidebar.php";
		?>
		<div>
			<div class="card-container">
				<div class="over3months">
					<h3>Overdue Contracts</h3>
					<p><?= $over3 ?> contract<?= $over3 == 1 ? '' : 's' ?></p>
				</div>
				<div class="lower3months">
					<h3>Expiring in ≤ 3 Months</h3>
					<p><?= $within3 ?> contract<?= $within3 == 1 ? '' : 's' ?></p>
				</div>
				<div class="higher3months">
					<h3>Remaining > 3 Months</h3>
					<p><?= $more3 ?> contract<?= $more3 == 1 ? '' : 's' ?></p>
				</div>
			</div>
			
			<div class="filter-container">
				<label for="categoryFilter" style="color: black;">Category:</label>
				<select id="categoryFilter">
					<option value="">All</option>
					<?php foreach ($categories as $category): ?>
					<option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<a href="form.php" class="btn" type="button">+ Add New</a>
			
			<div class="center-table">
				<table id="example" class="table table-striped">
					<thead>
						<tr>
							<th>Category</th>
							<th>PIC/Owner Name</th>
							<th>Description</th>
							<th>Company Name/Act Name</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Amount(RM)</th>
							<th>Remarks</th>
							<th>Duration</th>
							<th>Status</th>
							<th>Months Left Before End</th>
							<th>Created</th>
							<th>Updated</th>
							<th>Actions</th>
						</tr>
					</thead>
					
					<tbody>
						<?php
							// Prepare the SQL statement
							$stmt = $connection->prepare("
							SELECT 
							id,
							created_at,
							updated_at,
							status,
							category,
							pic,
							service,
							company,
							start,
							endDate,
							rent,
							remarks,
							duration,
							monthsLeft,
							filename
							FROM form
							WHERE department = ?
							");
							if (!$stmt) {
								die("Prepare failed: " . $connection->error);
							}
							
							// Bind parameters
							if (!$stmt->bind_param("s", $department)) {
								die("Bind failed: " . $stmt->error);
							}
							
							// Execute the statement
							if (!$stmt->execute()) {
								die("Execution failed: " . $stmt->error);
							}
							
							// Get the result
							$result = $stmt->get_result();
							
							// Define the path to the documents folder
							$documentPath = "uploads/";
							
							// Check if there are results
							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									echo "<tr>";
									$key      = $row['category'];
									$label    = $categoryLabels[$key] ?? $key;
									echo "<td>" . htmlspecialchars($label) . "</td>";
									echo "<td>" . htmlspecialchars($row["pic"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["service"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["company"]) . "</td>";
									echo "<td>" . date("d/m/Y", strtotime($row["start"])) . "</td>";
									echo "<td>" . date("d/m/Y", strtotime($row["endDate"])) . "</td>";
									echo "<td>" . htmlspecialchars($row["rent"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["remarks"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["duration"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
									echo "<td>" . htmlspecialchars($row["monthsLeft"]) . "</td>";
									// Created at
									echo "<td>"
									. date("d/m/Y H:i", strtotime($row['created_at']))
									. "</td>";
									
									// Updated at
									if (empty($row['updated_at'])) {
										echo "<td>–</td>";
										} else {
										echo "<td>" . date("d/m/Y H:i", strtotime($row['updated_at'])) . "</td>";
									}
									// Check if 'id' exists before accessing it
									echo "<td>
									<form action='view.php' method='get' style='display:inline;'>
									<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
									<button type='submit' class='btn'>View</button>
									</form>
									<form action='terminate.php' method='post' style='display:inline;'>
									<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
									<button type='submit' class='btn'>Archive</button>
									</form>
									</td>";
									
									echo "</tr>";
								}
							}
							
							// Close statements and connection
							$stmt->close();
							$connection->close();
						?>
						
					</tbody>
					
				</table>
			</div>
		</div>
		<?php include "footer.php"; ?>
		