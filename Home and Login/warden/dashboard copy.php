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

 $ticketStudentID = $_SESSION["userID"];  // Fetch the student user ID from session

// SQL Query to get the residence details for the logged-in student
$sql = "SELECT r.ResidenceName, r.Residence_id, w.WardenID
        FROM users AS u
        INNER JOIN warden AS w ON u.UserID = w.Warden_UserID
        INNER JOIN residence AS r ON w.ResidenceID = r.Residence_id
        WHERE u.UserID = ?";

if ($stmt = $conn->prepare($sql)) {
    // Bind the student user ID
    $stmt->bind_param("i", $ticketStudentID);
    
    // Execute the query
    $stmt->execute();
    
    // Bind result variables
    $stmt->bind_result($residenceName, $residenceID, $studentID);
    
    // Fetch the result
    if ($stmt->fetch()) {
        // Residence data is available
        $_SESSION['residence_name'] = $residenceName;
        $_SESSION['residence_id'] = $residenceID;
         $_SESSION['warden_id'] = $studentID;
    } else {
        echo "No residence found for the current student.";
        exit();
    }
    
    // Close the statement
    $stmt->close();
} else {
    echo "Error preparing query: " . $conn->error;
}

// Close the connection
$conn->close();
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
    <script src="dashboardPage.js"></script>
    <!--Stylesheets-->
    <link rel="stylesheet" href="mainCss.css">
    <link rel="stylesheet" href="sliding-nav.css">
    <link rel="stylesheet" href="dashboardCss.css">
    <link rel="stylesheet" href="modalCss.css">
    <!--Font and Icons-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!-- Sliding Nav -->
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
                <i class="fas fa-user icon" alt="user" title="User" style="font-size: 25px;"></i>
                <div class="dropdown">
                    <i id="trigger-popup" class="fas fa-ellipsis-h " alt="options" title="Options"
                        onclick="toggleDropdown()" style="font-size: 25px;"></i>
                    <div id="options" class="dropdown-content">
                        <a href="#" class="dropdown-item">Help</a>
                        <a href="../logout.php" class="dropdown-item"
                            onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                    </div>
                </div>
        </header>
        <?php
        require_once('config.php');
        $conn = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);
        $resName = $_SESSION['residence_id'];
        // Fetch counts for stats overview
        $pending_tasks_sql = "SELECT COUNT(*) AS count FROM maintenancetickets WHERE Status = 'Open' AND TicketResID = $resName ";
        $completed_today_sql = "SELECT COUNT(*) AS count FROM maintenancetickets WHERE Status = 'Resolved' AND DATE(DateResolved) = CURDATE()";
        $urgent_tasks_sql = "SELECT COUNT(*) AS count FROM maintenancetickets WHERE TicketCategory IN ('Plumbing', 'Electrical') AND Status = 'Requisition'";
        $upcoming_tasks_sql = "SELECT COUNT(*) AS count FROM maintenancetickets WHERE Status = 'Confirmed' AND TicketResID = $resName";

        $pending_tasks_result = $conn->query($pending_tasks_sql);
        $completed_today_result = $conn->query($completed_today_sql);
        $urgent_tasks_result = $conn->query($urgent_tasks_sql);
        $upcoming_tasks_result = $conn->query($upcoming_tasks_sql);

        $pending_tasks_count = $pending_tasks_result->fetch_assoc()['count'];
        $completed_today_count = $completed_today_result->fetch_assoc()['count'];
        $urgent_tasks_count = $urgent_tasks_result->fetch_assoc()['count'];
        $upcoming_tasks_count = $upcoming_tasks_result->fetch_assoc()['count'];

        $conn->close();
        ?>

        <!-- Overview Section -->
        <div class="stats-overview">
            <div class="stat-card">
                <h3>Tasks Awaiting Confirmation</h3>
                <p><?php echo $pending_tasks_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Duplicate Tickets</h3>
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
            <input type="text" name="search" placeholder="Search by ticket id, residence, or student name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <select name="priority" onchange="this.form.submit()">
                <option value="all" <?php echo isset($_GET['priority']) && $_GET['priority'] == 'all' ? 'selected' : ''; ?>>All Priorities</option>
                <option value="high" <?php echo isset($_GET['priority']) && $_GET['priority'] == 'high' ? 'selected' : ''; ?>>High Priority</option>
                <option value="medium" <?php echo isset($_GET['priority']) && $_GET['priority'] == 'medium' ? 'selected' : ''; ?>>Medium Priority</option>
            </select>
            <select name="category" onchange="this.form.submit()">
                <option value="all" <?php echo isset($_GET['category']) && $_GET['category'] == 'all' ? 'selected' : ''; ?>>All Categories</option>
                <option value="Electrical" <?php echo isset($_GET['category']) && $_GET['category'] == 'Electrical' ? 'selected' : ''; ?>>Electrical</option>
                <option value="Plumbing" <?php echo isset($_GET['category']) && $_GET['category'] == 'Plumbing' ? 'selected' : ''; ?>>Plumbing</option>
                <option value="General Maintenance" <?php echo isset($_GET['category']) && $_GET['category'] == 'General Maintenance' ? 'selected' : ''; ?>>General Maintenance</option>
                <option value="Carpentry" <?php echo isset($_GET['category']) && $_GET['category'] == 'Carpentry' ? 'selected' : ''; ?>>Carpentry</option>
            </select>
        </form>


        <!-- Main content area -->
        <section class="tickets-list">
        <?php
        require_once('config.php');
        $conn = new mysqli(SERVER,USERNAME,PASSWORD,DATABASE);
        // Determine the priority filter
        $priority_filter = isset($_GET['priority']) ? $_GET['priority'] : 'all';
        $priority_condition = '';

        if ($priority_filter == 'high') {
            $priority_condition = "AND (maintenancetickets.TicketCategory = 'Plumbing' OR maintenancetickets.TicketCategory = 'Electrical')";
        } elseif ($priority_filter == 'medium') {
            $priority_condition = "AND (maintenancetickets.TicketCategory != 'Plumbing' AND maintenancetickets.TicketCategory != 'Electrical')";
        }

        // Determine the category filter
        $category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
        $category_condition = '';

        if ($category_filter != 'all') {
            $category_condition = "AND maintenancetickets.TicketCategory = '$category_filter'";
        }
        // Determine the search filter
        $search_filter = isset($_GET['search']) ? $_GET['search'] : '';
        $search_condition = '';

        if (!empty($search_filter)) {
            $search_condition = "AND (maintenancetickets.TicketID LIKE '%$search_filter%' OR 
                                    residence.ResidenceName LIKE '%$search_filter%' OR 
                                    student.FirstName LIKE '%$search_filter%' OR 
                                    student.LastName LIKE '%$search_filter%')";
        }
        
        // SQL Query to fetch tickets with associated pictures
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
            WHERE maintenancetickets.Status = 'Closed' AND 1=1 $priority_condition $category_condition $search_condition
            ORDER BY maintenancetickets.DateCreated DESC";

        $result = $conn->query($sql);

        if (!$result) {
            echo "Error: " . $conn->error;  // Output the SQL error if the query fails
        } else if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {  // Add opening brace
        ?>
                <!-- Display each ticket -->
                <div class="ticket-item">
                    <div class="ticket-info">
                        <h4><?php echo $row['TicketTitle']; ?></h4>
                        <p>Reported by: <?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></p>
                        <p>Residence: <?php echo $row['ResidenceName'] . ', ' . $row['hall_name']; ?></p>
                        <p>Room: <?php echo $row['RoomNumber'];?></p>
                        <p>Category: <?php echo $row['TicketCategory']; ?> </p>
                        <p>Reported on <?php echo date('d-M-Y', strtotime($row['DateCreated'])); ?></p>
                    </div>
                    <div class="ticket-actions">
                <?php
                    echo '<form method="POST" action="dashboard.php">';
                    echo '<input type="hidden" name="TicketID" value="' . $row['TicketID'] . '">';
                    echo '<button type="submit" class="btn-action">View Details</button>';
                    echo '</form>';
                    echo '<form method="POST" action="dashboard.php">';
                    echo '<input type="hidden" name="TicketID" value="' . $row['TicketID'] . '">';
                    echo '<input type="hidden" name="action" value="resolve">';
                    echo '<button type="submit" class="btn-complete">Mark as Completed</button>';
                    echo '</form>';
                ?>
                    </div>
                </div>
        <?php 
            }  // Add closing brace
        } else {  // Opening else block
        ?>
            <p>No tickets found.</p>
        <?php 
        }  // End of if-else
        ?>
        </section>
    </section>

    <!-- Modal for Task Details -->
    <div id="taskModal" class="modal" style="display: <?php echo isset($_POST['TicketID']) && !isset($_POST['action']) ? 'block' : 'none'; ?>;">
    <span class="close" onclick="closeModal()">&times;</span>
    <div id="modal-content" class="modal-content">
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['TicketID']) && !isset($_POST['action'])) {
            $ticket_id = intval($_POST['TicketID']);  // Sanitize the input
            $ticket_sql = "
                SELECT 
                    maintenancetickets.TicketTitle, 
                    maintenancetickets.Description, 
                    maintenancetickets.TicketCategory, 
                    student.FirstName, 
                    student.LastName, 
                    residence.ResidenceName, 
                    student.RoomNumber, 
                    halls.hall_name,
                    pictures.filepath
                FROM maintenancetickets
                JOIN student ON maintenancetickets.TicketStudentID = student.StudentID
                JOIN residence ON maintenancetickets.TicketResID = residence.Residence_id
                JOIN halls ON residence.res_hall_id = halls.hall_id
                LEFT JOIN pictures ON pictures.picture_ticket_id = maintenancetickets.TicketID
                WHERE maintenancetickets.TicketID = $ticket_id";
            
            $ticket_result = $conn->query($ticket_sql);
            
            if (!$ticket_result) {
                echo "Error fetching ticket details: " . $conn->error;
            } else if ($ticket_result->num_rows > 0) {
                $ticket_row = $ticket_result->fetch_assoc();
                ?>
                <h2>Task Details: <?php echo $ticket_row['TicketTitle']; ?></h2>
                <p><strong>Reported by:</strong> <?php echo $ticket_row['FirstName'] . ' ' . $ticket_row['LastName']; ?></p>
                <p><strong>Location:</strong> <?php echo $ticket_row['ResidenceName'] . ', ' . $ticket_row['hall_name'] . ', Room ' . $ticket_row['RoomNumber']; ?></p>
                <p><strong>Description:</strong> <?php echo $ticket_row['Description']; ?></p>
                <p><strong>Category:</strong> <?php echo $ticket_row['TicketCategory']; ?></p>
                <p><strong>Attachments:</strong> 
                    <?php if (!empty($ticket_row['filepath'])): ?>
                        <a href="<?php echo $ticket_row['filepath']; ?>" target="_blank">View Image</a>
                    <?php else: ?>
                        No attachments.
                    <?php endif; ?>
                </p>
                <form method="POST" action="">
                    <input type="hidden" name="TicketID" value="<?php echo $ticket_id; ?>">
                    <input type="hidden" name="action" value="resolve">
                    <button type="submit" class="btn-complete">Mark as completed</button>
                </form>
                <?php
            } else {
                echo "<p>No ticket details found.</p>";
            }
        }
        ?>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['TicketID']) && isset($_POST['action']) && $_POST['action'] == 'resolve') {
        $ticket_id = intval($_POST['TicketID']);  // Sanitize the input
        $update_sql = "UPDATE maintenancetickets SET Status = 'Confirmed' WHERE TicketID = $ticket_id";
        
        if ($conn->query($update_sql) === TRUE) {
            echo "<script>
                document.getElementById('success-message').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('success-message').style.display = 'none';
                    location.reload();  // Refresh the page
                }, 3000);
            </script>";
            echo "<div id=\"success-message\" class=\"success-message\" style=\"display: none;\">
            Ticket marked as completed successfully.
            </div>";
        } else {
            echo "Error updating ticket status: " . $conn->error;
        }
    }
    ?>
    <!-- Close MySQL connection -->
    <?php
    
    $conn->close();
    ?>
    <script src="dashboardPage.js"></script>
    
</body>

</html>