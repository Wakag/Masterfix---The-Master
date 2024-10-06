<?php
session_start();

if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Student") {
    echo "<script>alert('Sorry. You do not have access to this page :(');</script>";
    header("Location: ../HomeV2.php");
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterFix</title>
    <link rel="shortcut icon" href="images/icon-200x200.png" type="image/x-icon">

    <!--Scripts-->
    <script src="mainScript.js"></script>
    
    <!--Stylesheets-->
    <link rel="stylesheet" href="sliding-nav.css">
    <link rel="stylesheet" href="mainCss.css">
    <link rel="stylesheet" href="requisitions.css">
    
    <!--Font and Icons-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
 
 /* Tooltip container */
 .tooltip {
     position: relative;
     display: inline-block;
     cursor: pointer;
 }

 /* Tooltip text */
 .tooltip .tooltiptext {
 visibility: hidden;
 width: 300px;  /* Increased width for a larger tooltip */
 background-color: #f9f9f9;
 color: #333;
 text-align: left;
 border-radius: 5px;
 padding: 15px; /* Increased padding for more space */
 position: absolute;
 z-index: 5;
 bottom: -1000%; /* Adjusted position to accommodate larger size */
 left: -350%;
 margin-left: -100px; /* Adjusted to center the tooltip */
 box-shadow: 0 0 10px rgba(0,0,0,0.2); /* A slightly stronger shadow */
 opacity: 0;
 transition: opacity 0.3s;
}

 /* Show the tooltip text when hovering over the icon */
 .tooltip:hover .tooltiptext {
     visibility: visible;
     opacity: 1;
 }

</style>
</head>
<body>
    <!-- Sliding Nav -->
    <div class="navbar" id="sideNavbar">
        <a href="dashboard.php" class="nav-link">My Dashboard</a>
        <a href="requisitions.php" class="nav-link">View Requisitions</a>
        <a href="new-ticket.php" class="nav-link">Make a requisition</a>
        <a href="../Maintenance Fault Reports/NewPage/NewPageStudent.php" class="nav-link">Stats for Geeks</a>
        <footer>
            <p>&copy; 2024 MasterFix</p>
            <a href="Broken.html"><i class="fas fa-exclamation-circle" title="Something's not working?"></i></a>
        </footer>
    </div>
    <header class="top-bar">
        <div class="left-section">
            <div class="toggle-btn" onclick="toggleNavbar()">
                <i class="fas fa-bars"></i>
            </div>
            <p class="page-title">MasterFix</p>
        </div>
        <img src="images/RU_Logo_with_RU120_Logo-1.png" height="50" id="ru-logo" alt="RU Logo">
        <div class="account-section">
            <span id="user-type"><?php echo $_SESSION['role'];?></span>
            <div class="tooltip">
                <i class="fas fa-user icon" alt="user" style="font-size: 25px;"></i> 
                <span class="tooltiptext">
                    
                    <h4 style="text-align: center;">User Info:</h4>
                    <p><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
                    <p><strong>Role:</strong> <?php echo $_SESSION['role']; ?></p>
                    <p><strong>Residence Name:</strong> <?php echo $_SESSION['residence_name']; ?></p>
                </span> 
            </div>
            <div class="dropdown">
                <i id="trigger-popup" class="fas fa-ellipsis-h" alt="options" title="Options" onclick="toggleDropdown()" style="font-size: 25px;"></i>
                <div id="options" class="dropdown-content">
                <a href="../help Page/help Page.html" class="dropdown-item">Help</a>
                    <a href="#" class="dropdown-item" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <section class="Under-Top-Bar-section">
        <div class="utbs-text">
            <p class="utbs-title">MasterFix</p>
            <p class="utbs-subtitle"><?php echo $_SESSION['residence_name'];?></p>
        </div>
    </section>

    <div class="table-controls">
        <form method="GET" action="" id="filterSortForm">
            <input type="text" name="search" placeholder="Search by Ticket ID or Student ID" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            
            <select name="category" onchange="this.form.submit()">
                <option value="">Filter by Category</option>
                <option value="General Maintenance" <?php echo (isset($_GET['category']) && $_GET['category'] == 'General Maintenance') ? 'selected' : ''; ?>>General Maintenance</option>
                <option value="Electrical" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Electrical') ? 'selected' : ''; ?>>Electrical</option>
                <option value="Plumbing" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Plumbing') ? 'selected' : ''; ?>>Plumbing</option>
                <option value="Carpentry" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Carpentry') ? 'selected' : ''; ?>>Carpentry</option>
            </select>
            
            <select name="status" onchange="this.form.submit()">
                <option value="">Filter by Status</option>
                <option value="Open" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Open') ? 'selected' : ''; ?>>Open</option>
                <option value="Confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                <option value="Requisition" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Requisition') ? 'selected' : ''; ?>>Requisition</option>
                <option value="Resolved" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                <option value="Closed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Closed') ? 'selected' : ''; ?>>Closed</option>
            </select>

            <select name="sortBy" onchange="this.form.submit()">
                <option value="">Sort By</option>
                <option value="DateCreated" <?php echo (isset($_GET['sortBy']) && $_GET['sortBy'] == 'DateCreated') ? 'selected' : ''; ?>>Date Created</option>
                <option value="Status" <?php echo (isset($_GET['sortBy']) && $_GET['sortBy'] == 'Status') ? 'selected' : ''; ?>>Status</option>
                <option value="TicketCategory" <?php echo (isset($_GET['sortBy']) && $_GET['sortBy'] == 'TicketCategory') ? 'selected' : ''; ?>>Category</option>
            </select>
        </form>
    </div>

    <div class="table-container">
        <div class="table">
            <?php
            require_once("config.php");
            $mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

            // Check connection
            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }

            // Default query
            $query = "SELECT 
                        t.TicketID, 
                        t.DateCreated, 
                        t.TicketTitle,
                        t.TicketCategory, 
                        t.Status,
                        t.TicketStudentID, 
                        s.RoomNumber, 
                        r.ResidenceName, 
                        t.DateResolved 
                      FROM
                        maintenancetickets t
                      JOIN
                        residence r ON t.TicketResID = r.Residence_id
                      JOIN
                        student s ON t.TicketStudentID = s.StudentID
                      WHERE 
                        r.ResidenceName = ? 
                        AND s.StudentID = ? ";

            // Initialize parameters array
            $params = [$_SESSION['residence_name'], $_SESSION['student_id']];

            // Apply search functionality using LIKE
            if (isset($_GET['search']) && $_GET['search'] !== '') {
                $search = '%' . $_GET['search'] . '%'; // Add wildcards for LIKE
                $query .= " AND (t.TicketID LIKE ? OR t.TicketStudentID LIKE ?)";
                array_push($params, $search, $search);
            }

            // Apply category filter
            if (isset($_GET['category']) && $_GET['category'] !== '') {
                $category = $_GET['category'];
                $query .= " AND t.TicketCategory = ?";
                array_push($params, $category);
            }

            // Apply status filter
            if (isset($_GET['status']) && $_GET['status'] !== '') {
                $status = $_GET['status'];
                $query .= " AND t.Status = ?";
                array_push($params, $status);
            }

            // Apply sorting
            if (isset($_GET['sortBy']) && $_GET['sortBy'] !== '') {
                $sortBy = $_GET['sortBy'];
                $query .= " ORDER BY t.$sortBy"; // Use the selected sort option
            } else {
                $query .= " ORDER BY t.TicketID"; // Default sorting
            }

            // Prepare and execute SQL query
            $stmt = $mysqli->prepare($query);
            // Create the dynamic binding string
            $types = str_repeat('s', count($params)); // all parameters are strings
            $stmt->bind_param($types, ...$params);

            $stmt->execute();
            $result = $stmt->get_result();

            // Check for query execution errors
            if ($result === false) {
                die("Query failed: " . $mysqli->error);
            }

            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr>
                        <th>Requisition Number</th>
                        <th>Requisitioned On</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Student ID</th>
                        <th>Room Number</th>
                        <th>Residence Name</th>
                        <th>Resolved On</th>
                      </tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr onclick=\"window.location.href='ViewRequisition.php?id=" . htmlspecialchars($row["TicketID"]) . "'\">
                            <td>" . htmlspecialchars($row["TicketID"]) . "</td>
                            <td>" . htmlspecialchars($row["DateCreated"]) . "</td>
                            <td>" . htmlspecialchars($row["TicketTitle"]) . "</td>
                            <td>" . htmlspecialchars($row["TicketCategory"]) . "</td>
                            <td>" . htmlspecialchars($row["Status"]) . "</td>
                            <td>" . htmlspecialchars($row["TicketStudentID"]) . "</td>
                            <td>" . htmlspecialchars($row["RoomNumber"]) . "</td>
                            <td>" . htmlspecialchars($row["ResidenceName"]) . "</td>
                            <td>" . (is_null($row["DateResolved"]) ? "Not yet resolved" : htmlspecialchars($row["DateResolved"])) . "</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p id='failed' style='text-align:center;font-weight: 600;font-size:26px;'>No tickets found</p>";
            }
            $stmt->close();
            $mysqli->close();  
            ?>
        </div>
    </div>  
</body>
</html>
