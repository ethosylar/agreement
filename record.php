<?php
include 'dbconn.php'; // Include database connection

// Helper to run queries
function executeQuery($query) {
    global $connection;
    $result = mysqli_query($connection, $query);
    if (!$result) {
        die("Error in query: " . mysqli_error($connection));
    }
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['department'])) {
    $department = $connection->real_escape_string($_POST['department']);

    $query  = "SELECT * FROM form WHERE department='$department'";
    $result = executeQuery($query);

    if (mysqli_num_rows($result) > 0) {
        echo '<table class="table table-striped">';
        echo '<thead><tr>'
            . '<th>Category</th>'
            . '<th>PIC/Owner Name</th>'
            . '<th>Services</th>'
            . '<th>Company Name/Act Name</th>'
            . '<th>Start Date</th>'
            . '<th>End Date</th>'
            . '<th>Amount (RM)</th>'
            . '<th>Remarks</th>'
            . '<th>Duration</th>'
            . '<th>Status</th>'
            . '<th>Months Left Before End</th>'
            . '<th>Actions</th>'
            . '</tr></thead><tbody>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['category']) . '</td>';
            echo '<td>' . htmlspecialchars($row['pic']) . '</td>';
            echo '<td>' . htmlspecialchars($row['service']) . '</td>';
            echo '<td>' . htmlspecialchars($row['company']) . '</td>';
            echo '<td>' . htmlspecialchars($row['start']) . '</td>';
            echo '<td>' . htmlspecialchars($row['endDate']) . '</td>';
            echo '<td>' . htmlspecialchars($row['rent']) . '</td>';
            echo '<td>' . htmlspecialchars($row['remarks']) . '</td>';
            echo '<td>' . htmlspecialchars($row['duration']) . '</td>';
            echo '<td>' . htmlspecialchars($row['status']) . '</td>';
            echo '<td>' . htmlspecialchars($row['monthsLeft']) . '</td>';

            // Actions: View or Archive
            echo '<td>';
            // View button
            echo '<form action="view2.php" method="get" style="display:inline;">'
               .  '<input type="hidden" name="filename" value="' . htmlspecialchars($row['filename']) . '">'
               .  '<button type="submit" class="btn btn-primary btn-sm">View</button>'
               .  '</form> ';
            // Archive button
            echo '<form action="terminate.php" method="post" style="display:inline;">'
               .  '<input type="hidden" name="filename" value="' . htmlspecialchars($row['filename']) . '">'
               .  '<button type="submit" class="btn btn-secondary btn-sm">Archive</button>'
               .  '</form>';
            echo '</td>';

            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>No records found for this department.</p>';
    }
}
?>
