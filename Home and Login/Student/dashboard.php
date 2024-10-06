<?php

session_start();


if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Student") {
    echo "<script>alert('Sorry. You do not have access to this page :(');</script>";
    header("Location: ../HomeV2.php");
    exit(); 
}

// Include your database configuration file
require_once("config.php");

$conn = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$ticketStudentID = $_SESSION["userID"];  // Fetch the student user ID from session

// SQL Query to get the residence details for the logged-in student
$sql = "SELECT r.ResidenceName, r.Residence_id, s.StudentID
        FROM users AS u
        INNER JOIN student AS s ON u.UserID = s.Student_UserID
        INNER JOIN residence AS r ON s.ResID = r.Residence_id
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
         $_SESSION['student_id'] = $studentID;
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resubmit_ticket'])) {
    // Retrieve form data
    $ticketId = $_POST['ticket_id'];
    $ticketTitle = $_POST['title'];
    $ticketCategory = $_POST['category'];
    $description = $_POST['description'];
    
    // Define the status and current date
    
    $dateCreated = date('Y-m-d');
    

    $ticketStudentID = $_SESSION["student_id"]; 
    $ticketResID = $_SESSION['residence_id']; 
    
    // Connect to the database
    $conn = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert the new ticket as a resubmission
    $stmt = $conn->prepare("INSERT INTO maintenancetickets (TicketTitle, TicketCategory, Description, , DateCreated, TicketStudentID, TicketResID) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("ssssss", $ticketTitle, $ticketCategory, $description,  $dateCreated, $ticketStudentID, $ticketResID);

        if ($stmt->execute()) {
            // Display success message or set a success flag
            $resubmissionSuccess = true;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }

    $conn->close();
}
?>

<?php
// Database connection
require_once("config.php");
$conn = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set default status to "Resolved"
$ticketStatus = isset($_GET['status']) ? $_GET['status'] : 'Resolved';
$studentId =  $_SESSION["student_id"];  

// Prepare query based on status
if ($ticketStatus === 'Resolved') {
    $query = "SELECT TicketID, TicketTitle, TicketCategory, DateCreated, DateResolved 
              FROM maintenancetickets 
              WHERE TicketStudentID = ? AND Status in ('Resolved', 'Closed') 
              Order by DateResolved DESC
              LIMIT 7";
} elseif ($ticketStatus === 'Pending') {
    $query = "SELECT TicketID, TicketTitle, TicketCategory, DateCreated, DateResolved 
              FROM maintenancetickets 
              WHERE TicketStudentID = ? AND (Status = 'Open' OR Status = 'Confirmed') 
              Order by DateCreated DESC
              LIMIT 7";
}

// Prepare the SQL statement
if ($stmt = $conn->prepare($query)) {
    // Bind the parameter (since student ID is a string, use 's')
    $stmt->bind_param('s', $studentId);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
} else {
    // Output error for debugging
    die("Query failed: " . $conn->error);
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
    <script src="dashboardPage.js"></script>
    <!--Stylesheets-->
    <link rel="stylesheet" href="mainCss.css">
    <link rel="stylesheet" href="sliding-nav.css">
    <link rel="stylesheet" href="dashboardCss.css">
    <link rel="stylesheet" href="modalCss.css">
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
        <a href="new-ticket.php"class="nav-link">Make a requisition</a>
        <a href="../Maintenance Fault Reports/NewPage/NewPageStudent.php" class="nav-link">Stats for Geeks</a>
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
                    <i id="trigger-popup" class="fas fa-ellipsis-h " alt="options" title="Options"
                        onclick="toggleDropdown()" style="font-size: 25px;"></i>
                    <div id="options" class="dropdown-content">
                    <a href="../help Page/help Page.html" class="dropdown-item">Help</a>
                        <a href="../logout.php" class="dropdown-item"
                            onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                    </div>
                </div>
        </header>

        <!--Background image section-->
        <section class="Under-Top-Bar-section">
            <div class="utbs-text">
                <p class="utbs-title">My Dashboard</p>
                <p class="utbs-subtitle"><?php echo $residenceName?></p>
            </div>
        </section>

        <!--Buttons to filter tickets-->
        <section class="segmented-buttons">
            <button class="segment-btn left <?php echo ($ticketStatus === 'Resolved') ? 'active' : ''; ?>"
                onclick="window.location.href='dashboard.php?status=Resolved'">Resolved</button>
            <button class="segment-btn right <?php echo ($ticketStatus === 'Pending') ? 'active' : ''; ?>"
                onclick="window.location.href='dashboard.php?status=Pending'">Pending/Other</button>
        </section>

        <!-- Main content area -->
        <section class="tickets-list">
            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="spinner" style="display: none;">
                <div class="spin"></div>
            </div>
            
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <div class="ticket-separator"></div> <!-- Line that separates tickets -->
            <div class="ticket-item">
                <div class="ticket-icon">
                <?php

                    $ticketCategory = $row['TicketCategory']; // Example: Fetch this from your SQL query

                    switch ($ticketCategory) {
                        case 'General Maintenance':
                            // Code for handling general maintenance tickets
                            echo "<i class=\"fa-solid fa-screwdriver-wrench\"></i>";
                            break;

                        case 'Electrical':
                            // Code for handling electrical tickets
                            echo "<i class=\"fa-solid fa-bolt\"></i>";
                            break;

                        case 'Plumbing':
                            // Code for handling plumbing tickets
                            echo "<i class=\"fa-solid fa-toilet\"></i>";
                            break;

                        case 'Carpentry':
                            // Code for handling carpentry tickets
                            echo "<i class=\"fa-solid fa-hammer\"></i>";
                            break;

                        default:
                            // Code for handling unknown categories
                            echo "<i class=\"fa-solid fa-toolbox\"></i>";
                            break;
                    }
                ?>
                </div>
                <div class="ticket-info">
                    <h4><?php echo $row['TicketTitle']; ?></h4>
                    <p>Category: <?php echo $row['TicketCategory']; ?> &nbsp;&nbsp;·&nbsp;&nbsp; Reported on
                        <?php echo date('d-M-Y', strtotime($row['DateCreated'])); ?> &nbsp;&nbsp;  <?php if ($ticketStatus === 'Resolved'): ?>
                        · &nbsp;&nbsp;Maintenance issue resolved on <?php echo date('d-M-Y', strtotime($row['DateResolved'])); ?>
                    <?php endif; ?></p>
                    
                </div>
                <div class="ticket-actions">
                    <i class="fas fa-edit"
                        onclick="openEditModal(<?php echo $row['TicketID']; ?>, '<?php echo $row['TicketTitle']; ?>', '<?php echo $row['TicketCategory']; ?>')"></i>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p id='failed' style='text-align:center;font-weight: 600;font-size:18px;'>You have no recently resolved tickets</p>
            <?php endif; ?>
        </section>
    </section>

    <!-- Close MySQL connection -->
    <?php
    $stmt->close();
    $conn->close();
    ?>

    <!-- Edit Ticket Popup Modal -->
    <div id="editTicketModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Ticket</h2>
            <p>Use the form below to resubmit the ticket if the issue has reoccurred</p>
            <form id="editTicketForm" method="POST">
                <input type="hidden" name="ticket_id" id="ticketId">
                <label for="ticketTitle">Maintenance Issue:</label>
                <input type="text" name="title" id="ticketTitle" readonly>
                <label for="ticketCategory">Category:</label>
                <input type="text" name="category" id="ticketCategory" readonly>
                <label for="ticketDescription">Description:</label>
                <textarea name="description" id="ticketDescription" required></textarea>
                <button type="submit" name="resubmit_ticket">Resubmit Ticket</button>
            </form>
        </div>
    </div>
    <script src="dashboardPage.js"></script>
    <script src="editticketmodal.js"></script>
</body>

</html>