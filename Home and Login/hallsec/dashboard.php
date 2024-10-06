<?php
session_start(); 


if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Hall Secretary") {
    echo "<script>alert('Sorry. You do not have access to this page :(');</script>";
    header("Location: ../HomeV2.php");
    exit(); 
}
require_once("config.php");


require_once('helpers.php'); // Include the helper functions

// Handle the POST request when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'Requisition') {
    $ticketID = intval($_POST['TicketID']);
    
    // Update the ticket status to 'Requisition'
    if (update_ticket_status($ticketID, 'Requisition')) {
        // Success: Set a session message
        $_SESSION['message'] = "Ticket updated to Requisition successfully.";
    } else {
        // Error: Set a session message
        $_SESSION['message'] = "Error updating ticket status.";
    }

    // Redirect to avoid form resubmission
    header('Location: dashboard.php');
    exit;
}

// Display success/error messages
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']); // Clear the message after displaying it
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
    <script src="dashboardPage.js"></script>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="mainCss.css">
    <link rel="stylesheet" href="sliding-nav.css">
    <link rel="stylesheet" href="dashboardCss.css">
    <link rel="stylesheet" href="modalCss.css">

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
        width: 300px;
        /* Increased width for a larger tooltip */
        background-color: #f9f9f9;
        color: #333;
        text-align: left;
        border-radius: 5px;
        padding: 15px;
        /* Increased padding for more space */
        position: absolute;
        z-index: 5;
        bottom: -1000%;
        /* Adjusted position to accommodate larger size */
        left: -350%;
        margin-left: -100px;
        /* Adjusted to center the tooltip */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        /* A slightly stronger shadow */
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
        <a href="closedtickets.php" class="nav-link">Closed Tickets</a>
        <a href="../Maintenance Fault Reports/NewPage/NewPageHallSec.php" class="nav-link">Stats for Geeks</a>
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
                        <?php require_once("helpers.php"); $hall_secretary_id = $_SESSION['userID']; $_SESSION['hallname'] = get_hall_name($hall_secretary_id); ?>
                        <h4 style="text-align: center;">User Info:</h4>
                        <p><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
                        <p><strong>Role:</strong> <?php echo $_SESSION['role']; ?></p>
                        <p><strong>Hall Name:</strong> <?php echo $_SESSION['hallname']; ?></p>
                    </span>
                </div>
                <div class="dropdown">
                    <i id="trigger-popup" class="fas fa-ellipsis-h" alt="options" title="Options"
                        onclick="toggleDropdown()" style="font-size: 25px;"></i>
                    <div id="options" class="dropdown-content">
                        <a href="#" class="dropdown-item">Help</a>
                        <a href="../logout.php" class="dropdown-item"
                            onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                    </div>
                </div>
            </div>
        </header>



        <!-- Overview Section -->
        <?php
        require_once('helpers.php'); // Use a separate file for helper functions
        
        // Get hall secretary's hall ID
        $hall_secretary_id = $_SESSION['userID'];
        $hall_id = get_hall_id($hall_secretary_id);
        $_SESSION['hall_id'] = $hall_id;

        // Get ticket counts
        $pending_tasks_count = get_ticket_count_by_status($hall_id, 'Confirmed');
        $closed_count = get_ticket_count_by_status($hall_id, 'Closed');
        $in_progress_count = get_ticket_count_by_status($hall_id, 'Requisition');
        $total_open_count = get_total_open_tickets($hall_id);
        ?>

        <div class="stats-overview">
            <div class="stat-card">
                <h3>Tickets Pending Requisition</h3>
                <p><?php echo $pending_tasks_count;?></p>
            </div>
            <div class="stat-card">
                <h3>Closed Tickets</h3>
                <p><?php echo $closed_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Tickets In Progress</h3>
                <p><?php echo $in_progress_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Open Tickets</h3>
                <p><?php echo $total_open_count; ?></p>
            </div>
        </div>

        <!-- Message Area -->
        <div class="message-area" id="messageArea" style="<?= $message ? '' : 'display:none;' ?>">
            <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message, ENT_QUOTES) ?></p>
            <?php endif; ?>
        </div>

        <!-- Search and Filter Bar -->
        <form method="GET" action="dashboard.php" class="filter-bar">
            <input type="text" name="search" placeholder="Search by ticket id, residence, or student name"
                value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>">
            <select name="priority" onchange="this.form.submit()">
                <option value="all" <?php echo ($_GET['priority'] ?? '') === 'all' ? 'selected' : ''; ?>>All Priorities
                </option>
                <option value="high" <?php echo ($_GET['priority'] ?? '') === 'high' ? 'selected' : ''; ?>>High Priority
                </option>
                <option value="medium" <?php echo ($_GET['priority'] ?? '') === 'medium' ? 'selected' : ''; ?>>Medium
                    Priority</option>
            </select>
            <select name="category" onchange="this.form.submit()">
                <option value="all" <?php echo ($_GET['category'] ?? '') === 'all' ? 'selected' : ''; ?>>All Categories
                </option>
                <option value="Electrical" <?php echo ($_GET['category'] ?? '') === 'Electrical' ? 'selected' : ''; ?>>
                    Electrical</option>
                <option value="Plumbing" <?php echo ($_GET['category'] ?? '') === 'Plumbing' ? 'selected' : ''; ?>>
                    Plumbing</option>
                <option value="General Maintenance"
                    <?php echo ($_GET['category'] ?? '') === 'General Maintenance' ? 'selected' : ''; ?>>General
                    Maintenance</option>
                <option value="Carpentry" <?php echo ($_GET['category'] ?? '') === 'Carpentry' ? 'selected' : ''; ?>>
                    Carpentry</option>
            </select>
        </form>

        <!-- Main content area -->
        <section class="tickets-list">
            <?php
            // Get filtered tickets based on search and filters
            $tickets = get_filtered_tickets($hall_id, $_GET);

            if (count($tickets) > 0) {
                foreach ($tickets as $ticket) {
                    ?>
            <div class="ticket-item">
                <div class="ticket-info">
                    <h4><?= htmlspecialchars($ticket['TicketTitle'], ENT_QUOTES) ?></h4>
                    <p>Student Name: <?= htmlspecialchars($ticket['FirstName'], ENT_QUOTES) ?>
                        <?= htmlspecialchars($ticket['LastName'], ENT_QUOTES) ?></p>
                    <p>Category: <?= htmlspecialchars($ticket['TicketCategory'], ENT_QUOTES) ?></p>
                    <p>Residence: <?= htmlspecialchars($ticket['ResidenceName'], ENT_QUOTES) ?></p>
                    <p>Room Number: <?= htmlspecialchars($ticket['RoomNumber'], ENT_QUOTES) ?></p>
                    <p>Date Reported: <?= date('d-M-Y', strtotime($ticket['DateCreated'])) ?></p>
                </div>
                <div class="ticket-actions">
                    <form method="POST" action="dashboard.php"
                        onsubmit="return confirmAction('Mark this ticket as requisition?')">
                        <input type="hidden" name="TicketID" value="<?= intval($ticket['TicketID']) ?>">
                        <input type="hidden" name="action" value="Requisition">
                        <button type="submit" class="btn-complete">Make Requisition</button>
                    </form>
                </div>
            </div>
            <?php
                }
            } else {
                echo "<p>No tickets found matching the criteria.</p>";
            }
            ?>
        </section>
    </section>
    <script>
    // Hide the message after 5 seconds
    setTimeout(function() {
        var messageArea = document.getElementById('messageArea');
        if (messageArea) {
            messageArea.style.display = 'none';
        }
    }, 5000); // 5000 milliseconds = 5 seconds
    // Function to confirm the action before form submission
    function confirmAction(message) {
        return confirm(message);
    }
    </script>

</body>

</html>