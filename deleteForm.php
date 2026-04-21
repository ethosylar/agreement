<?php
	session_start();
	include 'dbconn.php';
	
	// 1) Must be logged in
	if (!isset($_SESSION['department'])) {
		header('Location: index.php');
		exit();
	}
	
	// 2) Only azean may delete
	if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'azean') {
		die("You do not have permission to delete records.");
	}
	
	// 3) Get & validate ID
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	if ($id < 1) {
		die("Invalid request.");
	}
	
	/**
		* Insert an entry into audit_log
	*/
	function write_audit($conn, $user, $dept, $action, $table, $recordId) {
		$ip = $_SERVER['REMOTE_ADDR']    ?? '';
		$ua = $_SERVER['HTTP_USER_AGENT']?? '';
		$stmt = $conn->prepare(
		"INSERT INTO audit_log
		(user_id, department, action, table_name, record_id, ip_address, user_agent)
		VALUES (?,?,?,?,?,?,?)"
		);
		$stmt->bind_param(
		"sssis s s",
		$user,
		$dept,
		$action,
		$table,
		$recordId,
		$ip,
		$ua
		);
		$stmt->execute();
		$stmt->close();
	}
	
	// 4) Perform the delete
	$stmt = $connection->prepare("DELETE FROM form WHERE id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	
	if ($stmt->affected_rows > 0) {
		// 5) Log it
		write_audit(
		$connection,
		$_SESSION['username'],
		$_SESSION['department'],
		'DELETE',
		'form',
		$id
		);
		echo "<script>
        alert('Record has been successfully deleted.');
        window.location.href = 'dashboard.php';
		</script>";
		} else {
		echo "<script>
        alert('No matching record found.');
        window.location.href = 'dashboard.php';
		</script>";
	}
	
	$stmt->close();
	$connection->close();
