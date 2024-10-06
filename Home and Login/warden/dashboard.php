<?php
session_start();

if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Warden") {
    echo "<script>alert('Sorry. You do not have access to this page :(');</script>";
    header("Location: ../HomeV2.php");
    exit(); 
}
require_once("config.php");

$conn = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$ticketWardenID = $_SESSION["userID"];  // Fetch the student user ID from session

// SQL Query to get the residence details for the logged-in student
$sql = "SELECT r.ResidenceName, r.Residence_id, w.WardenID
        FROM users AS u
        INNER JOIN warden AS w ON u.UserID = w.Warden_UserID
        INNER JOIN residence AS r ON w.ResidenceID = r.Residence_id
        WHERE u.UserID = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $ticketWardenID);
    $stmt->execute();
    $stmt->bind_result($residenceName, $residenceID, $WardenID);
    
    if ($stmt->fetch()) {
        $_SESSION['residence_name'] = $residenceName;
        $_SESSION['residence_id'] = $residenceID;
        $_SESSION['warden_id'] = $WardenID;
    } else {
        echo "No residence found for the current student.";
        exit();
    }
    
    $stmt->close();
} else {
    echo "Error preparing query: " . $conn->error;
}

// Fetch counts for stats overview
$resName = $_SESSION['residence_id'];
$pending_tasks_sql = "SELECT COUNT(*) AS count FROM maintenancetickets WHERE Status = 'Open' AND TicketResID = ?";
$duplicate_tickets_sql = "SELECT COUNT(*) AS count FROM maintenancetickets WHERE TicketTitle IN (SELECT TicketTitle FROM maintenancetickets WHERE Status = 'Open' GROUP BY TicketTitle HAVING COUNT(*) > 1) AND TicketResID = ?";
$completed_today_sql = "SELECT COUNT(*) AS count FROM maintenancetickets WHERE Status = 'Resolved' AND DATE(DateResolved) = CURDATE() AND TicketResID = ?";
$urgent_tasks_sql = "SELECT COUNT(*) AS count FROM maintenancetickets WHERE (TicketCategory IN ('Plumbing', 'Electrical') AND Status = 'Open') AND TicketResID = ?";
$upcoming_tasks_sql = "SELECT COUNT(*) AS count FROM maintenancetickets WHERE Status = 'Confirmed' AND TicketResID = ?";

$stmt_pending = $conn->prepare($pending_tasks_sql);
$stmt_pending->bind_param("i", $resName);
$stmt_pending->execute();
$pending_tasks_result = $stmt_pending->get_result();
$pending_tasks_count = $pending_tasks_result->fetch_assoc()['count'];
$stmt_pending->close();

$stmt_duplicate = $conn->prepare($duplicate_tickets_sql);
$stmt_duplicate->bind_param("i", $resName);
$stmt_duplicate->execute();
$duplicate_tasks_result = $stmt_duplicate->get_result();
$duplicate_tasks_count = $duplicate_tasks_result->fetch_assoc()['count'];
$stmt_duplicate->close();

$stmt_completed_today = $conn->prepare($completed_today_sql);
$stmt_completed_today->bind_param("i", $resName);
$stmt_completed_today->execute();
$completed_today_result = $stmt_completed_today->get_result();
$completed_today_count = $completed_today_result->fetch_assoc()['count'];
$stmt_completed_today->close();

$stmt_urgent = $conn->prepare($urgent_tasks_sql);
$stmt_urgent->bind_param("i", $resName);
$stmt_urgent->execute();
$urgent_tasks_result = $stmt_urgent->get_result();
$urgent_tasks_count = $urgent_tasks_result->fetch_assoc()['count'];
$stmt_urgent->close();

$stmt_upcoming = $conn->prepare($upcoming_tasks_sql);
$stmt_upcoming->bind_param("i", $resName);
$stmt_upcoming->execute();
$upcoming_tasks_result = $stmt_upcoming->get_result();
$upcoming_tasks_count = $upcoming_tasks_result->fetch_assoc()['count'];
$stmt_upcoming->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterFix</title>
    <link rel="shortcut icon" href="images/icon-200x200.png" type="image/x-icon">
    <script src="mainScript.js"></script>
    <script src="dashboardPage.js"></script>
    <link rel="stylesheet" href="mainCss.css">
    <link rel="stylesheet" href="sliding-nav.css">
    <link rel="stylesheet" href="dashboardCss.css">
    <link rel="stylesheet" href="modalCss.css">
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
    <div class="navbar" id="sideNavbar">
        <a href="dashboard.php" class="nav-link">My Dashboard</a>
        <a href="requisitions.php" class="nav-link">View Requisitions</a>
        <a href="new-ticket.php" class="nav-link">Make a requisition</a>
        <a href="../Maintenance Fault Reports/NewPage/NewPageWarden.php" class="nav-link">Stats for Geeks</a>
        <footer>
            <p>&copy; 2024 MasterFix</p>
            <a href="Broken.html"><i class="fas fa-exclamation-circle" title="Something's not working?"></i></a>
        </footer>
    </div>

    <section class="top">
        <header class="top-bar">
            <div class="left-section">
                <div class="toggle-btn" onclick="toggleNavbar()">
                    <i class="fas fa-bars"></i>
                </div>
                <p class="page-title">MasterFix</p>
            </div>
            <img src="images/RU_Logo_with_RU120_Logo-1.png" height="50" alt="RU Logo">
            <div class="account-section">
                <span id="user-type"><?php echo $_SESSION["role"];?></span>
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
                        <a href="#" class="dropdown-item">Help</a>
                        <a href="../logout.php" class="dropdown-item" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Overview Section -->
        <div class="stats-overview">
            <div class="stat-card">
                <h3>Pending Tasks</h3>
                <p><?php echo $pending_tasks_count;   ?></p>
            </div>
            <div class="stat-card">
                <h3>Duplicate Tickets</h3>
                <p><?php echo $duplicate_tasks_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Resolved Today</h3>
                <p><?php echo $completed_today_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Urgent Tasks</h3>
                <p><?php echo $urgent_tasks_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Upcoming Tasks</h3>
                <p><?php echo $upcoming_tasks_count; ?></p>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <form method="GET" action="dashboard.php" class="filter-bar">
            <input type="text" name="search" placeholder="Search by ticket id or student name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">

            <select name="category" onchange="this.form.submit()">
                <option value="all" <?php echo isset($_GET['category']) && $_GET['category'] == 'all' ? 'selected' : ''; ?>>All Categories</option>
                <option value="Electrical" <?php echo isset($_GET['category']) && $_GET['category'] == 'Electrical' ? 'selected' : ''; ?>>Electrical</option>
                <option value="Plumbing" <?php echo isset($_GET['category']) && $_GET['category'] == 'Plumbing' ? 'selected' : ''; ?>>Plumbing</option>
            </select>
        </form>

        <!-- Tickets Section -->
        <section class="tickets-list">
            <?php
            require_once("config.php");

            $conn = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);
            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            // Create the conditions based on search and filter inputs
            $priority_condition = '';
            $category_condition = '';
            $search_condition = '';
            
            if (isset($_GET['priority']) && $_GET['priority'] != 'all') {
                $priority_condition = " AND TicketPriority = '" . $_GET['priority'] . "'";
            }
            if (isset($_GET['category']) && $_GET['category'] != 'all') {
                $category_condition = " AND TicketCategory = '" . $_GET['category'] . "'";
            }
            if (isset($_GET['search']) && $_GET['search'] != '') {
                $search = $conn->real_escape_string($_GET['search']);
                $search_condition = " AND (TicketID LIKE '%$search%' OR TicketTitle LIKE '%$search%' OR (SELECT CONCAT(FirstName, ' ', LastName) FROM student WHERE StudentID = maintenancetickets.TicketStudentID) LIKE '%$search%')";
            }

            // SQL Query to fetch tickets with associated pictures, filtered by residence
            $sql = "
                SELECT 
                    maintenancetickets.TicketID, 
                    maintenancetickets.TicketTitle, 
                    maintenancetickets.DateCreated, 
                    maintenancetickets.Description, 
                    maintenancetickets.TicketCategory, 
                    student.FirstName, 
                    student.LastName, 
                    residence.ResidenceName, 
                    student.RoomNumber, 
                    halls.hall_name
                FROM maintenancetickets
                JOIN student ON maintenancetickets.TicketStudentID = student.StudentID
                JOIN residence ON maintenancetickets.TicketResID = residence.Residence_id
                JOIN halls ON residence.res_hall_id = halls.hall_id
                WHERE maintenancetickets.Status = 'Confirmed' 
                  AND maintenancetickets.TicketResID = ?  
                  AND 1=1 $priority_condition $category_condition $search_condition
                ORDER BY maintenancetickets.DateCreated DESC";

            // Prepare statement with the Warden's residence ID
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['residence_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                echo "Error: " . $conn->error;  // Output the SQL error if the query fails
            } else if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Display each ticket
            ?>
                    
                    <!-- Display ticket information -->
                    <div class="ticket-item">
                        <div class="ticket-info">
                            <h4><?php echo $row['TicketID']. ":  " .$row['TicketTitle']; ?></h4>
                            <p>Reported by: <?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></p>
                            <p>Residence: <?php echo $row['ResidenceName'] . ', ' . $row['hall_name']; ?></p>
                            <p>Room: <?php echo $row['RoomNumber']; ?></p>
                            <p>Category: <?php echo $row['TicketCategory']; ?> </p>
                            <p>Reported on <?php echo date('d-M-Y', strtotime($row['DateCreated'])); ?></p>
                        </div>
                        <div class="ticket-actions">
                            <form method="POST" action="dashboard.php">
                                <input type="hidden" name="TicketID" value="<?php echo $row['TicketID']; ?>">
                                <button type="submit" class="btn-action">View Details</button>
                            </form>
                            <form method="POST" action="dashboard.php">
                                <input type="hidden" name="TicketID" value="<?php echo $row['TicketID']; ?>">
                                <input type="hidden" name="action" value="resolve">
                                <button type="submit" class="btn-complete">Mark as Completed</button>
                            </form>
                        </div>
                    </div>
            <?php 
                }
            } else {
            ?>
                <p>No tickets found.</p>
            <?php 
            }
            $stmt->close();
            $conn->close();
            ?>
        </section>
    </section>
</body>

</html>
