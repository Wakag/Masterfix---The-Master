<?php
session_start();

if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Hall Secretary") {
    echo "<script>alert('Sorry. You do not have access to this page :(');</script>";
    header("Location: ../HomeV2.php");
    exit(); 
}
require_once("config.php");?>
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
<body style="background-image: url(images/justus-menke-Wrf4izDg8Pg-unsplash.jpg); background-size: cover; background-position: center;">
    <!-- Sliding Nav -->
    <div class="navbar" id="sideNavbar">
        <a href="dashboard.php" class="nav-link">My Dashboard</a>
        <a href="requisitions.php" class="nav-link">View Requisitions</a>
        <a href="closedtickets.php" class = "nav-link">Closed Tickets</a>
        <a href="../Maintenance Fault Reports/NewPage/NewPageHallSec.php" class="nav-link">Stats for Geeks</a>
        <footer>
            <p>&copy; 2024 MasterFix</p>
            <a href="Broken.html"><i class="fas fa-exclamation-circle" title="Something's not working?"></i></a>
        </footer>
    </div>

    <header class="top-bar">
        <div class="left-section">
            <div class="toggle-btn" onclick="toggleNavbar()">
                <i class="fas fa-bars"></i>  <!-- Font Awesome hamburger icon -->
            </div>
            <p class="page-title">MasterFix</p>
        </div>
        <img src="images/RU_Logo_with_RU120_Logo-1.png" height="50" id="ru-logo" alt="RU Logo">
        <div class="account-section">
            <span id="user-type"><?php echo $_SESSION['role'];?></span>
            <div class="tooltip">
                <i class="fas fa-user icon" alt="user" style="font-size: 25px;"></i> 
                <span class="tooltiptext">
                    <?php require_once("helpers.php"); $hall_secretary_id = $_SESSION['userID']; $_SESSION['hallname'] = get_hall_name($hall_secretary_id); ?>
                <h4>User Info</h4>
                    <p><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
                    
                    <p><strong>Role:</strong> <?php echo $_SESSION['role']; ?></p>
                    <p><strong>Hall Name:</strong> <?php echo $_SESSION['hallname']; ?></p>
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

    <!-- Table Manipulation Controls -->
    <div class="table-controls">
        <form method="GET" action="" id="filterSortForm">
        <input type="text" name="search" placeholder="Search by ticket id, residence, or student name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <select name="category" onchange="this.form.submit()">
                <option value="all" <?php echo isset($_GET['category']) && $_GET['category'] == 'all' ? 'selected' : ''; ?>>All Categories</option>
                <option value="Electrical" <?php echo isset($_GET['category']) && $_GET['category'] == 'Electrical' ? 'selected' : ''; ?>>Electrical</option>
                <option value="Plumbing" <?php echo isset($_GET['category']) && $_GET['category'] == 'Plumbing' ? 'selected' : ''; ?>>Plumbing</option>
                <option value="General Maintenance" <?php echo isset($_GET['category']) && $_GET['category'] == 'General Maintenance' ? 'selected' : ''; ?>>General Maintenance</option>
                <option value="Carpentry" <?php echo isset($_GET['category']) && $_GET['category'] == 'Carpentry' ? 'selected' : ''; ?>>Carpentry</option>
            </select>
            <select name="sort" onchange="document.getElementById('filterSortForm').submit();">
                <option value="">Sort by</option>
                <option value="Sort by Residence"<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'Sort by Residence') ? ' selected' : ''; ?>>Sort by Residence</option>
                <option value="Sort by Date"<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'Sort by Date') ? ' selected' : ''; ?>>Sort by Date</option>
                <option value="Sort by Category"<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'Sort by Category') ? ' selected' : ''; ?>>Sort by Category</option>
            </select>
        </form>
    </div>

    <!-- Main content area -->
    <div class="table-container">
        <div class="table">
        <?php
        require_once("config.php");

        $mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

        // Check connection
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

       
        $hall_secretary_hall_id = 4;//$_SESSION['hall_id'];

        // Determine the category and search filters
        $category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
        $search_filter = isset($_GET['search']) ? $_GET['search'] : '';
        $sort_filter = isset($_GET['sort']) ? $_GET['sort'] : '';

        // Initialize conditions array and parameter types
        $conditions = ["h.hall_id = ?"]; // Ensure tickets are only for students from the hall the secretary is responsible for
        $param_types = 'i'; // The hall ID is an integer
        $params = [$hall_secretary_hall_id];

        // Category condition
        if ($category_filter !== 'all') {
            $conditions[] = "t.TicketCategory = ?";
            $param_types .= 's';
            $params[] = $category_filter;
        }

        // Search condition
        if (!empty($search_filter)) {
            $conditions[] = "(t.TicketID LIKE ? OR r.ResidenceName LIKE ? OR s.FirstName LIKE ? OR s.LastName LIKE ?)";
            $param_types .= 'ssss';
            $search_term = "%$search_filter%";
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }

        // Build the WHERE clause dynamically
        $where_clause = '';
        if (!empty($conditions)) {
            $where_clause = ' AND ' . implode(' AND ', $conditions);
        }

        // Default query with dynamic conditions
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
                JOIN
                    halls h ON r.res_hall_id = h.hall_id  
                WHERE Status ='Closed' $where_clause";

        // Apply sort
        switch ($sort_filter) {
            case 'Sort by Residence':
                $query .= " ORDER BY r.ResidenceName";
                break;
            case 'Sort by Date':
                $query .= " ORDER BY t.DateCreated";
                break;
            case 'Sort by Category':
                $query .= " ORDER BY t.TicketCategory";
                break;
            default:
                $query .= " ORDER BY t.TicketID";
                break;
        }

        // Prepare and execute SQL query
        $stmt = $mysqli->prepare($query);

        // Check if there are any parameters to bind
        if (!empty($params)) {
            $stmt->bind_param($param_types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        // Check for query execution errors
        if ($result === false) {
            die("Query failed: " . $mysqli->error);
        }

        // Display results in a table
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
                echo "<tr>
                        <td>" . htmlspecialchars($row["TicketID"]) . "</td>
                        <td>" . htmlspecialchars(date('d-M-Y', strtotime($row['DateCreated'])) ) . "</td>
                        <td>" . htmlspecialchars($row["TicketTitle"]) . "</td>
                        <td>" . htmlspecialchars($row["TicketCategory"]) . "</td>   
                        <td>" . htmlspecialchars($row["Status"]) . "</td>
                        <td>" . htmlspecialchars($row["TicketStudentID"]) . "</td>
                        <td>" . htmlspecialchars($row["RoomNumber"]) . "</td>
                        <td>" . htmlspecialchars($row["ResidenceName"]) . "</td>
                        <td>" . (is_null($row["DateResolved"]) ? "Not yet resolved" : htmlspecialchars(date('d-M-Y', strtotime($row['DateResolved'])) )) . "</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p id='failed' style='text-align:center;font-weight: 600;font-size:26px;'>No tickets found</p>";
        }

        // Close statement and connection
        $stmt->close();
        $mysqli->close();
        ?>


        </div>
    </div> 
</body>
</html>