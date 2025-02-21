<?php
session_start(); // Start the session
include 'dbconn.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['department'])) {
    header("Location: index.php");
    exit(); // Stop further script execution
}

$department = $_SESSION['department'];
// Define department
$dpt = ["ACCIDENT AND EMERGENCY", "ACCOUNTS", "ADMINISTRATION", "AUDIOLOGY","BUSINESS DEVELOPMENT" ,"BUSINESS DEVELOPMENT/OFFICE" ,"BUSINESS OFFICE" ,"CLINICAL SERVICES & CLINICAL QUALITY" ,"CONSULTANT CLINIC" ,"CUSTOMER CARE & EXPERIENCE" ,"CUSTOMER SERVICE" ,"DIAGNOSTIC IMAGING SERVICES" ,"DIETARY" ,"ENDOSCOPY ROOM" ,"FINANCE" ,"HAEMODIALYSIS" ,"HEALTH INFORMATION MANAGEMENT SERVICES" ,"HEALTH SCREENING","HEALTHCARE ENGINEERING SERVICES","HUMAN RESOURCES MANAGEMENT","ICU/CCU/CICU","INFORMATION TECHNOLOGY","KLINIK WAQAF AN-NUR","KPJ WELLNESS SERVICES","MARKETING & CORPORATE COMMUNICATION","MARKETING DEPARTMENT","MATERNITY","MEDICAL OFFICER","MEDICAL RECORDS","MEDICAL WARD","NURSING ADMINISTRATION","OPERATION THEATRE","OPTOMETRIST","OUTSOURCE SERVICES","PAEDIATRIC WARD","PATIENT LIAISON SERVICES","PATIENT SERVICE","PHARMACY","PHYSIOTHERAPY","PREMIER WARD","PUBLIC RELATION DEPARTMENT","PUBLIC RELATIONS AND MARKETING","PURCHASING","QUALITY","RISK & COMPLIANCE SERVICES","SAFETY & HEALTH","SURGICAL WARD"];

// Function to execute SQL query and return result
function executeQuery($query) {
    global $connection;
    $result = mysqli_query($connection, $query);
    if (!$result) {
        die("Error in query: " . mysqli_error($connection));
    }
    return $result;
}

// Fetch contract counts for all records (no department filter)
$query = "SELECT 
            SUM(CASE WHEN monthsLeft < 0 THEN 1 ELSE 0 END) AS over3MonthsCount,
            SUM(CASE WHEN monthsLeft >= 0 AND monthsLeft <= 3 THEN 1 ELSE 0 END) AS lower3MonthsCount,
            SUM(CASE WHEN monthsLeft > 3 THEN 1 ELSE 0 END) AS higher3MonthsCount
          FROM form";

$result = $connection->query($query);
$row = $result->fetch_assoc();

// Store the counts
$over3MonthsCount = isset($row['over3MonthsCount']) ? $row['over3MonthsCount'] : 0;
$lower3MonthsCount = isset($row['lower3MonthsCount']) ? $row['lower3MonthsCount'] : 0;
$higher3MonthsCount = isset($row['higher3MonthsCount']) ? $row['higher3MonthsCount'] : 0;

// Loop through each record and count based on monthsLeft
while ($row = $result->fetch_assoc()) {
    if (!isset($row['monthsLeft'])) {
        continue; // Skip if monthsLeft is not set
    }

    $monthsLeft = (int) $row['monthsLeft']; // Convert to integer

    if ($monthsLeft < 0) {
        $over3MonthsCount++; // Count overdue contracts
    } elseif ($monthsLeft >= 0 && $monthsLeft <= 3) {
        $lower3MonthsCount++; // Count contracts with 0 to 3 months left
    } else {
        $higher3MonthsCount++; // Count contracts with more than 3 months left
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="shortcut icon" type="x-icon" href="hsptl.png">
    <link rel="stylesheet" href="style.css">
    <style>
        .card-container {
    display: flex;
    justify-content: space-between; /* Even spacing */
    gap: 15px; /* Adds space between cards */
    padding: 20px;
}

.card-container div {
    flex: 1; /* Make cards equal width */
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    font-weight: bold;
}

.over3months {
    background-color: #FF9A9A; /* Red */
    color: black;
}

.lower3months {
    background-color: #FDFF90; /* Yellow */
    color: black;
}

.higher3months {
    background-color: #A5D6A7; /* Green */
    color: black;
}

    </style>
    <script>
        $(document).ready(function () {
            var table = $('#example').DataTable();

            // Filter table based on department selection
            $('#departmentFilter').on('change', function () {
                var selectedDepartment = $(this).val();
                table.column(0).search(selectedDepartment).draw();
            });

            // Toggle sidebar
            $('.toggle-btn').click(function () {
                $('.sidebar').toggleClass('active');
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
</div>
   
    <nav class="navbar">
    <div class="navdiv">
        <h2>Overview Record</h2>
        <ul class="breadcrumb">
            <li><a href="#">Overview</a></li>
            <li><i class='bx bx-chevron-right' style="color: black;"></i></li>
            <li><a class="active" href="home.php">Dashboard</a></li>
        </ul>
    </div>
</nav>
    
    <div class="wrapper">
    <div class="sidebar">
        <div class="logo-menu">
            <h2 class="menu" style="color: white;">Menu</h2>
            <i class='bx bx-menu toggle-btn'></i>
        </div>
        <ul class="list">
            <li class="list-item">
                <a href="home.php">
                    <i class='bx bx-home'></i>
                    <span class="link-name" style="--i:1;">Dashboard</span>
                </a>
            </li>
            <li class="list-item active">
                <a href="">
                    <i class='bx bx-file'></i>
                    <span class="link-name" style="--i:2;">Others</span>
                </a>
            </li>
            <li class="list-item">
                <a href="department.php">
                    <i class='bx bx-buildings'></i>
                    <span class="link-name" style="--i:3;">Department</span>
                </a>
            </li>
            <li class="list-item">
                <a href="terminate.php">
                    <i class='bx bx-folder-minus'></i>
                    <span class="link-name" style="--i:4;">Terminate</span>
                </a>
            </li>
            <li class="list-item">
                <a href="logout.php">
                    <i class='bx bx-log-out'></i>
                    <span class="link-name" style="--i:5;">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-container">
    <div class="over3months">
        <h3>Over 3 Months Contracts</h3>
        <p><?= $over3MonthsCount; ?> contracts</p>
    </div>

    <div class="lower3months">
        <h3>Contracts Expiring in 3 Months or Less</h3>
        <p><?= $lower3MonthsCount; ?> contracts</p>
    </div>

    <div class="higher3months">
        <h3>Contracts with More than 3 Months Left</h3>
        <p><?= $higher3MonthsCount; ?> contracts</p>
    </div>
</div>

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
            <table id="example" class="table table-striped">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Category</th>
                        <th>PIC</th>
                        <th>Service</th>
                        <th>Company/Act</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Rental</th>
                        <th>Remarks</th>
                        <th>Months Left Before End</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Prepare the SQL statement
                    $stmt = $connection->prepare("SELECT department,category, pic, service, company, start, endDate, rent, remarks, monthsLeft,filename FROM form WHERE department = ?");
                    if (!$stmt) {
                        die("Prepare failed: " . $connection->error);
                    }

                    // Bind parameters
                    if (!$stmt->bind_param("i", $department)) {
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
                            echo "<td>" . htmlspecialchars($row["department"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["category"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["pic"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["service"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["company"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["start"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["endDate"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["rent"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["remarks"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["monthsLeft"]) . "</td>";

                            // Check if 'filename' exists before accessing it
                            if (!empty($row['filename'])) {
                                echo "<td><form action='view1.php' method='get'>
                                <input type='hidden' name='filename' value='" . htmlspecialchars($row['filename']) . "'>
                                <button type='submit' class='btn'>View</button>
                              </form></td>";

                            } else {
                                echo "<td>Not available</td>";
                            }
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
    </div>
</body>
</html>
