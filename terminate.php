<?php
session_start();
include 'dbconn.php'; 

// Check if the user is logged in
if (!isset($_SESSION['department'])) {
    header("Location: index.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id']; // Ensure this matches the input field in home.php

    // Check if the record exists before terminating
    $query_check = "SELECT * FROM form WHERE id = '$id'";
    $result_check = mysqli_query($connection, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Insert terminated record into the terminate table
        $query_insert = "INSERT INTO terminate (id, status, department, category, pic, service, company, start, endDate, rent, remarks, duration, termination_date)
                         SELECT id, status, department, category, pic, service, company, start, endDate, rent, remarks, duration, NOW() 
                         FROM form WHERE id = '$id'";

        if (mysqli_query($connection, $query_insert)) {
            // Optional: Delete from form table after termination
            $query_delete = "DELETE FROM form WHERE id = '$id'";
            mysqli_query($connection, $query_delete);

            echo "Record successfully terminated.";
        } else {
            echo "Error terminating record: " . mysqli_error($connection);
        }
    } else {
        echo "Record not found.";
    }
} else {
    echo "Invalid request.";
}
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
