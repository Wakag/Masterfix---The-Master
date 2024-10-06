<?php
require_once("config.php");

if (isset($_GET['id'])) {
    $ticketID = $_GET['id'];
    $mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $sql = "SELECT 
                s.FirstName,
                s.LastName, 
                s.RoomNumber, 
                r.ResidenceName, 
                t.TicketStudentID,
                t.TicketTitle,
                t.Description,
                t.TicketCategory,
                t.DateCreated,
                t.Status,
                p.filePath
            FROM 
                maintenancetickets t
            JOIN 
                student s ON t.TicketStudentID= s.StudentID
            JOIN
                residence r ON t.TicketResID= r.Residence_id
            LEFT JOIN
                pictures p ON t.TicketID = p.picture_ticket_id
            WHERE ticketID = ?";
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("i", $ticketID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = !empty($row['filePath']) ? $row['filePath'] : 'images/RU-logo.png';
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterFix - Ticket Details</title>
    <link rel="shortcut icon" href="images/icon-200x200.png" type="image/x-icon">
    
    <!-- External Stylesheets -->
    <link rel="stylesheet" href="sliding-nav.css">
    <link rel="stylesheet" href="mainCss.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8F9FD;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            background-color: #21005D;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
        }

        /* Main Content */
        .content {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .page-header h3 {
            color: #65558F;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Ticket Details Section */
        .ticket-details {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .details-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-field {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            color: #65558F;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-value {
            background-color: #F1F1F1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            color: #333;
        }

        .info-value[readonly] {
            background-color: #F1F1F1;
        }

        .comments-section {
            grid-column: span 2;
            margin-top: 20px;
        }

        .comments-textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }

        /* Ticket Image */
        .ticket-image {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .ticket-image img {
            max-width: 100%;
            border-radius: 10px;
            max-height: 300px;
            object-fit: cover;
        }

        /* Buttons */
        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .primary, .secondary {
            padding: 10px 20px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
        }

        .primary {
            background-color: #65558F;
            color: white;
            transition: background-color 0.3s ease;
        }

        .primary:hover {
            background-color: #CE93f8;
        }

        .secondary {
            background-color: #21005D;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ticket-details {
                grid-template-columns: 1fr;
            }

            .details-column {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="dashboard.php">My Dashboard</a>
        <a href="requisitions.php">View Requisitions</a>
        <a href="new-ticket.php">Make a Requisition</a>
        <a href="#">Stats for Geeks</a>
    </div>
    
    <header class="top-bar">
        <div class="left-section">
        <div class="toggle-btn" onclick="toggleNavbar()">
            <i class="fas fa-bars"></i>  <!-- Font Awesome hamburger icon -->
            </div>
            <p class="page-title">MasterFix</p>
            
        </div>
        <img src="images\RU_Logo_with_RU120_Logo-1.png" height="50" alt="RU Logo">
        <div class="account-section">
            <span id="user-type">User-Type</span>
            <i class="fas fa-user icon" alt="user" title="User" style="font-size: 25px;"></i>
            <div class="dropdown">
            <i id="trigger-popup" class="fas fa-ellipsis-h " alt="options" title="Options" onclick="toggleDropdown()"style="font-size: 25px;"></i>
                <div id="options" class="dropdown-content">
                    <a href="#" class="dropdown-item">Help</a>
                    <a href="#" class="dropdown-item" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                </div>
            </div>
    </header>
    <div class="content">
        <!-- Header -->
        <div class="page-header">
            <h3><?php echo "Showing Details for Requisition #".$ticketID?></h3>
        </div>

        <!-- Ticket Details Section -->
        <div class="ticket-details">
            <!-- Details Column -->
            <div class="details-column">
                <div class="info-field">
                    <label class="info-label" for="student-name">Student Name</label>
                    <input type="text" class="info-value" id="student-name" value="<?php echo htmlspecialchars($row['FirstName']." ".$row['LastName']); ?>" readonly>
                </div>

                <div class="info-field">
                    <label class="info-label" for="student-number">Student Number</label>
                    <input type="text" class="info-value" id="student-number" value="<?php echo htmlspecialchars($row['TicketStudentID']); ?>" readonly>
                </div>

                <div class="info-field">
                    <label class="info-label" for="residence-name">Residence Name</label>
                    <input type="text" class="info-value" id="residence-name" value="<?php echo htmlspecialchars($row['ResidenceName']); ?>" readonly>
                </div>

                <div class="info-field">
                    <label class="info-label" for="room-number">Room Number</label>
                    <input type="text" class="info-value" id="room-number" value="<?php echo htmlspecialchars($row['RoomNumber']); ?>" readonly>
                </div>

                <div class="info-field">
                    <label class="info-label" for="maintenance-issue">Maintenance Issue</label>
                    <input type="text" class="info-value" id="maintenance-issue" value="<?php echo htmlspecialchars($row['TicketTitle']); ?>" readonly>
                </div>

                <div class="info-field">
                    <label class="info-label" for="category">Category</label>
                    <input type="text" class="info-value" id="category" value="<?php echo htmlspecialchars($row['TicketCategory']); ?>" readonly>
                </div>

                <div class="info-field">
                    <label class="info-label" for="requisition-date">Requisition Date</label>
                    <input type="text" class="info-value" id="requisition-date" value="<?php echo htmlspecialchars($row['DateCreated']); ?>" readonly>
                </div>

                <div class="info-field">
                    <label class="info-label" for="ticket-status">Ticket Status</label>
                    <input type="text" class="info-value" id="ticket-status" value="<?php echo htmlspecialchars($row['Status']); ?>" readonly>
                </div>

                <!-- Comments Section -->
                <div class="comments-section">
                    <label class="info-label" for="student-comments">Student's Comments</label>
                    <textarea id="student-comments" class="comments-textarea" readonly><?php echo htmlspecialchars($row['Description']); ?></textarea>
                </div>
            </div>

            <!-- Ticket Image -->
            <div class="ticket-image">
                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Maintenance Issue Image">
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="buttons">
            <button class="primary" title="Add/Edit Comments">Add/Edit Comments</button>
            <button class="primary" title="Edit Status">Edit Status</button>
            <button class="secondary" title="Close Ticket">Close Ticket</button>
        </div>

        <!-- Back to Requisitions -->
        <a href="requisitions.php" title="Back to Requisitions">Back to Requisitions.</a>
    </div>
</body>
</html>
<?php
    } else {
        echo "No record found";
    }
    $stmt->close();
    $mysqli->close();
} else {
    echo "Invalid request";
}
?>