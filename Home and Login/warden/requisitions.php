<?php
session_start();

if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Warden") {
    echo "<script>alert('Sorry. You do not have access to this page :(');</script>";
    header("Location: ../HomeV2.php");
    exit(); 
}

// Assuming you have already set the warden's residence ID in session
$residence_id = $_SESSION['residence_id']; // Ensure this is set upon login
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterFix</title>
    <link rel="shortcut icon" href="images/icon-200x200.png" type="image/x-icon">

    <!-- Scripts -->
    <script src="mainScript.js"></script>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="sliding-nav.css">
    <link rel="stylesheet" href="mainCss.css">
    <link rel="stylesheet" href="requisitions.css">
    
    <!-- Font and Icons -->
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
    <div class="navbar" id="sideNavbar">
        <a href="dashboard.php" class="nav-link">My Dashboard</a>
        <a href="requisitions.php" class="nav-link">View Requisitions</a>
        <a href="#" class="nav-link">Stats for Geeks</a>
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
        <img src="images/RU_Logo_with_RU120_Logo-1.png" height="50" alt="RU Logo">
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
                    <a href="../logout.php" class="dropdown-item" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Table Manipulation Controls -->
    <div class="table-controls">
        <form method="GET" action="" id="filterSortForm">
            <input type="text" name="search" placeholder="Search by ticket ID or student name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <select name="category" onchange="this.form.submit()">
                <option value="all" <?php echo isset($_GET['category']) && $_GET['category'] == 'all' ? 'selected' : ''; ?>>Filter by</option>
                <option value="Electrical" <?php echo isset($_GET['category']) && $_GET['category'] == 'Electrical' ? 'selected' : ''; ?>>Electrical</option>
                <option value="Plumbing" <?php echo isset($_GET['category']) && $_GET['category'] == 'Plumbing' ? 'selected' : ''; ?>>Plumbing</option>
                <option value="General Maintenance" <?php echo isset($_GET['category']) && $_GET['category'] == 'General Maintenance' ? 'selected' : ''; ?>>General Maintenance</option>
                <option value="Carpentry" <?php echo isset($_GET['category']) && $_GET['category'] == 'Carpentry' ? 'selected' : ''; ?>>Carpentry</option>
            </select>
            <select name="status[]" onchange="this.form.submit()">
                <option value="" <?php echo empty($_GET['status']) ? 'selected' : ''; ?>>All Statuses</option>
                <option value="Open" <?php echo isset($_GET['status']) && in_array('Open', $_GET['status']) ? 'selected' : ''; ?>>Open</option>
                <option value="Confirmed" <?php echo isset($_GET['status']) && in_array('Confirmed', $_GET['status']) ? 'selected' : ''; ?>>Confirmed</option>
                <option value="Requisition" <?php echo isset($_GET['status']) && in_array('Requisition', $_GET['status']) ? 'selected' : ''; ?>>Requisition</option>
            </select>
            <select name="sort" onchange="this.form.submit()">
                <option value="">Sort by</option>
                <option value="Sort by Name" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'Sort by Name') ? ' selected' : ''; ?>>Sort by Name</option>
                <option value="Sort by Date" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'Sort by Date') ? ' selected' : ''; ?>>Sort by Date</option>
                <option value="Sort by Category" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'Sort by Category') ? ' selected' : ''; ?>>Sort by Category</option>
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

            // Initialize filters
            $search_filter = isset($_GET['search']) ? $_GET['search'] : '';
            $category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
            $status_filter = isset($_GET['status']) ? $_GET['status'] : [];
            $conditions = [];
            $param_types = 'i'; // Start with 'i' for residence ID
            $params = [$residence_id]; // Start with the residence ID

            // Search condition
            if (!empty($search_filter)) {
                $conditions[] = "(t.TicketID LIKE ? OR s.FirstName LIKE ? OR s.LastName LIKE ?)";
                $param_types .= 'sss'; // Append 'sss' for three strings
                $search_term = "%$search_filter%";
                $params[] = $search_term;
                $params[] = $search_term;
                $params[] = $search_term;
            }

            // Category condition
            if ($category_filter !== 'all') {
                $conditions[] = "t.TicketCategory = ?";
                $param_types .= 's'; // Append 's' for category
                $params[] = $category_filter;
            }

            // Status condition
            if (!empty($status_filter) && !in_array('', $status_filter)) { // Check if status_filter is not empty and not "All"
                $placeholders = str_repeat('?,', count($status_filter) - 1) . '?';
                $conditions[] = "t.Status IN ($placeholders)";
                $param_types .= str_repeat('s', count($status_filter)); // Append 's' for each status
                $params = array_merge($params, $status_filter);
            }

            // Build the WHERE clause
            $where_clause = 'WHERE t.TicketResID = ?'; // Filter for residence ID
            if (!empty($conditions)) {
                $where_clause .= ' AND ' . implode(' AND ', $conditions);
            }

            // Query to fetch data
            $query = "SELECT 
                        t.TicketID, 
                        t.DateCreated, 
                        t.TicketTitle,
                        t.TicketCategory, 
                        t.Status,
                        s.FirstName,
                        s.LastName,
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
                    $where_clause"; 

            // Apply sorting
            if (isset($_GET['sort']) && $_GET['sort'] !== '') {
                $sort = $_GET['sort'];
                switch ($sort) {
                    case 'Sort by Name':
                        $query .= " ORDER BY s.FirstName, s.LastName";
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
            } else {
                $query .= " ORDER BY t.TicketID";
            }

            // Prepare and execute SQL query
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param($param_types, ...$params); // Bind all parameters

            $stmt->execute();
            $result = $stmt->get_result();

            // Display results in a table
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr>
                        <th>Requisition Number</th>
                        <th>Requisitioned On</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Student Name</th>
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
                            <td>" . htmlspecialchars($row["FirstName"]) . " " . htmlspecialchars($row["LastName"]) . "</td>
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

            // Close statement and connection
            $stmt->close();
            $mysqli->close();
        ?>
        </div>
    </div> 
</body>
</html>
