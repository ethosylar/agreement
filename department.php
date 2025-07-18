<?php
	session_start(); // Start the session
	include 'dbconn.php'; // Include database connection
	
	// Check if the user is logged in
	if (!isset($_SESSION['department'])) {
		header("Location: index.php");
		exit(); // Stop further script execution
	}
	
	// Function to execute SQL query and return result
	function executeQuery($query) {
		global $connection;
		$result = mysqli_query($connection, $query);
		if (!$result) {
			die("Error in query: " . mysqli_error($connection));
		}
		return $result;
	}
	
	// Retrieve departments from database
	$query = "SELECT DISTINCT department FROM form";
	$result = executeQuery($query);
	$departments = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$departments[] = $row['department'];
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Departments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="shortcut icon" type="x-icon" href="hsptl.png">
    <link rel="stylesheet" href="style.css">

		
		<style>
			
			body {
            color: black; /* Ensure text is visible */
            font-family: 'Poppins', sans-serif;
			}
			
			.navbar {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			}
			
			.navdiv {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-around;
			}
			
			.navbar h2 {
            font-size: 30px;
            font-weight: 600;
            color: black;
            margin: 0;
			}
			
			.breadcrumb {
            display: flex;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
			}
			
			.breadcrumb li {
            margin: 0 10px;
            color: grey;
			}
			
			.breadcrumb a {
            text-decoration: none;
            color: grey;
			}
			.container {
            margin-left: 250px; /* Adjusted for sidebar */
            margin-top: 80px; /* Adjusted for navbar */
			}
			
			.card-columns {
            column-count: 3;
			}
			
			.card {
            margin-bottom: 20px;
			}
			
			.modal-xl {
            max-width: 95%;
			}
		</style>
		<script>
  $(function(){
    // sidebar toggle
    $('.toggle-btn').click(function(){
      $('.sidebar').toggleClass('active');
    });

    // View Records via AJAX + show Bootstrap modal
    $('.view-records-btn').on('click', function(){
      var dept = $(this).data('department');
      $.post('record.php', { department: dept })
        .done(function(html){
          $('#recordsModal .modal-body').html(html);
          $('#recordsModal').modal('show');
        })
        .fail(function(){
          alert('Failed to load records.');
        });
    });
  });
</script>
		
	</head>
	<body>
		<?php
			$pageTitle="Departments";
			$headerTitle="Departments";
			$activePage="department";
			include "header.php";
			include "sidebar.php";
		?>
		
		<div class="container">
			<div class="card-columns">
				<?php foreach ($departments as $department): ?>
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($department); ?></h5>
                        <button class="btn btn-primary view-records-btn" data-department="<?php echo htmlspecialchars($department); ?>">View Records</button>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	
	<!-- Modal -->
	<div class="modal fade" id="recordsModal" tabindex="-1" role="dialog" aria-labelledby="recordsModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="recordsModalLabel">Department Records</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<!-- Records will be loaded here via AJAX -->
				</div>
			</div>
		</div>
	</div>
	
	<?php include "footer.php"; ?>
