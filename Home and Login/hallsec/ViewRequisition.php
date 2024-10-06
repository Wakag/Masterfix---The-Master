<?php
session_start();

if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Hall Secretary") {
    echo "<script>alert('Sorry. You do not have access to this page :(');</script>";
    header("Location: ../HomeV2.php");
    exit(); 
}
require_once("config.php");

    

if (isset($_GET['id'])) {
    $ticketID = $_GET['id'];
    $mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Fetch ticket details
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
                student s ON t.TicketStudentID = s.StudentID
            JOIN
                residence r ON t.TicketResID = r.Residence_id
            LEFT JOIN
                pictures p ON t.TicketID = p.picture_ticket_id
            WHERE TicketID = ? ";
    $stmt = $mysqli->prepare($sql);
    // Check if the prepare() method failed
    if ($stmt === false) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("i", $ticketID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Set default image path if no image is uploaded
        $imagePath = !empty($row['filePath']) ? $row['filePath'] : 'images/RU-logo.png';
        
        // Fetch comments for the ticket
        $commentsSql = "SELECT 
                            c.comment_id,
                            u.UserID,
                            u.Role, 
                            c.commentDate, 
                            c.content 
                        FROM 
                            comments c 
                        JOIN 
                            users u ON c.comment_user_id = u.UserID
                        WHERE 
                            c.comment_ticket_id = ?";
        $commentsStmt = $mysqli->prepare($commentsSql);
        $commentsStmt->bind_param("i", $ticketID);
        $commentsStmt->execute();
        $commentsResult = $commentsStmt->get_result();

        $comments = [];
        while ($commentRow = $commentsResult->fetch_assoc()) {
            $comments[] = $commentRow;
        }

        // Handle new comment submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addComment'])) {
            $commentContent = $_POST['commentContent'];
            $userId = $_SESSION['userID'];

            $insertCommentSql = "INSERT INTO comments (comment_user_id, comment_ticket_id, commentDate, content) VALUES (?, ?, NOW(), ?)";
            $insertStmt = $mysqli->prepare($insertCommentSql);
            $insertStmt->bind_param("iis", $userId, $ticketID, $commentContent);

            if ($insertStmt->execute()) {
                // Refresh comments after insertion
                $commentsStmt->execute();
                $commentsResult = $commentsStmt->get_result();
                $comments = [];
                while ($commentRow = $commentsResult->fetch_assoc()) {
                    $comments[] = $commentRow;
                }
                echo "<script>alert('Comment added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding comment: " . $insertStmt->error . "');</script>";
            }

            $insertStmt->close();
        }
        // Handle status update
        if (isset($_POST['updateStatus'])) {
            $newStatus = $_POST['status'];
            $checkSql = "SELECT DateResolved FROM maintenancetickets WHERE TicketID = ?";
            $checkStmt = $mysqli->prepare($checkSql);
            $checkStmt->bind_param("i", $ticketID);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                $checkRow = $checkResult->fetch_assoc();
                $currentDateResolved = $checkRow['DateResolved'];

                $statuses = ['Requisition', 'Closed'];
                $currentStatusIndex = array_search($row['Status'], $statuses);
                $newStatusIndex = array_search($newStatus, $statuses);

                // Check if the new status is lower than the current status
                if ($newStatusIndex <= $currentStatusIndex) {
                    echo "<script>alert('You cannot downgrade the ticket status.');</script>";
                } else {
                    if ($newStatus === 'Closed') {
                        if (is_null($currentDateResolved)) {
                            $updateSql = "UPDATE maintenancetickets SET Status = ?, DateResolved = NOW() WHERE TicketID = ?";
                            $updateStmt = $mysqli->prepare($updateSql);
                            $updateStmt->bind_param("si", $newStatus, $ticketID);
                        } else {
                            $updateSql = "UPDATE maintenancetickets SET Status = ? WHERE TicketID = ?";
                            $updateStmt = $mysqli->prepare($updateSql);
                            $updateStmt->bind_param("si", $newStatus, $ticketID);
                        }
                    } else {
                        $updateSql = "UPDATE maintenancetickets SET Status = ? WHERE TicketID = ?";
                        $updateStmt = $mysqli->prepare($updateSql);
                        $updateStmt->bind_param("si", $newStatus, $ticketID);
                    }

                    // Execute the update statement
                    if ($updateStmt->execute()) {
                        echo "<script>alert('Ticket status has been updated.'); window.location.href='requisitions.php';</script>";
                    } else {
                        echo "Error updating status: " . $updateStmt->error;
                    }

                    $updateStmt->close();
                }
            }
            $checkStmt->close();
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterFix - Ticket Details</title>
    <link rel="shortcut icon" href="images/icon-200x200.png" type="image/x-icon">
    <script src="mainScript.js"></script>
    <link rel="stylesheet" href="sliding-nav.css">
    <link rel="stylesheet" href="mainCss.css">
    <link rel="stylesheet" href="viewrequisition.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body style="background-image: url(images/justus-menke-Wrf4izDg8Pg-unsplash.jpg); background-size: cover; background-position: center;">
    <div class="navbar" id="sideNavbar">
        <a href="dashboard.php" class="nav-link">My Dashboard</a>
        <a href="requisitions.php" class="nav-link">View Requisitions</a>
        <a href="../Maintenance Fault Reports/NewPage/NewPageHallSec.php" class="nav-link">Stats for Geeks</a>
        <footer>
            <p>&copy; 2024 MasterFix</p>
            <a href="Broken.html"><i class="fas fa-exclamation-circle" title="Something's not working?"></i></a>
        </footer>
    </div>
    <head>
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
    
    <div class="Kontent">
        <div class="page-header">
            <h2><?php echo "Showing Details for Requisition #".$ticketID?></h2>
        </div>

        <div class="ticket-details">
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
                    <input type="text" class="info-value" id="requisition-date" value="<?php echo htmlspecialchars(date('d-M-Y', strtotime($row['DateCreated']))); ?>" readonly>
                </div>
                <div class="info-field">
                    <label class="info-label" for="ticket-status">Ticket Status</label>
                    <input type="text" class="info-value" id="ticket-status" value="<?php echo htmlspecialchars($row['Status']); ?>" readonly>
                </div>
                <div class="description-section">
                    <label class="info-label" for="description">Description</label>
                    <textarea id="description" class="description-textarea" readonly><?php echo htmlspecialchars($row['Description']); ?></textarea>
                </div>
            </div>

            <div class="ticket-image">
                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Maintenance Issue Image">
            </div>
        </div>

        <div class="buttons">
            <button class="primary" title="View Comments" onclick="openCommentsModal()">View Comments</button>
            <button class="primary" title="Edit Status" onclick="document.getElementById('statusModal').style.display='block'">Edit Status</button>
            <button type="button" class="secondary" title="Close Ticket" onclick="openCloseModal();">Close Ticket</button>
        </div>

        <a href="requisitions.php" class="back-link" title="Back to Requisitions">← Back to Requisitions</a>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('statusModal').style.display='none'">&times;</span>
            <h2>Update Ticket Status</h2>
            <form method="POST" onsubmit="return validateStatus();">
                <label for="status">Select New Status:</label>
                <select name="status" id="status" required>
                    <option value="" disabled selected>Select a status</option>
                    <?php
                    $statuses = [ "open",'Requisition',  'Closed'];
                    $currentStatus = $row['Status'];

                    foreach ($statuses as $status) {
                        if (array_search($status, $statuses) > array_search($currentStatus, $statuses)) {
                            echo "<option value='$status'>$status</option>";
                        }
                    }
                    ?>
                </select>
                <br>
                <div id="commentField" style="display:none;">
                    <label for="closeComment" style="font-weight: 500; margin-bottom: 5px;">Comment for Closing:</label>
                    <textarea name="closeComment" id="closeComment" rows="4" placeholder="Provide a comment for closing the ticket..." required style="padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: 'Inter', sans-serif; font-size: 14px; resize: none;"></textarea>
                </div>
                <input type="submit" name="updateStatus" value="Update Status">
            </form>
        </div>
    </div>
    <!-- Comments Modal -->
    <div id="commentsModal" class="comments-modal">
        <div class="comments-modal-content">
            <span class="close" onclick="document.getElementById('commentsModal').style.display='none'">&times;</span>
            <h2>Comments</h2>
            <div class="comments-list">
                <?php if (empty($comments)): ?>
                    <p>No comments available for this ticket.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($comments as $comment): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($comment['Role']); ?></strong>
                                <span><?php echo htmlspecialchars(date('d-M-Y', strtotime($comment['commentDate']))); ?></span>
                                <p><?php echo htmlspecialchars($comment['content']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <form method="POST">
                <textarea name="commentContent" rows="4" placeholder="Add a comment..." required></textarea>
                <input type="submit" name="addComment" value="Add Comment" style="padding: 10px; background-color: #6A1B9A; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
            </form>
        </div>
    </div>


    <script>
        function openCommentsModal() {
            document.getElementById('commentsModal').style.display = 'block';
        }

        function openCloseModal() {
            document.getElementById('statusModal').style.display = 'block';
            const statusSelect = document.getElementById('status');
            statusSelect.value = 'Closed'; // Automatically select "Closed"
            document.getElementById('commentField').style.display = 'block'; // Show comment field for closing
        }

        function confirmCloseTicket() {
            return confirm('Are you sure you want to close this ticket?');
        }

        function validateStatus() {
            const statuses = ['Requisition', 'Closed'];
            const currentStatus = "<?php echo $row['Status']; ?>";
            const currentStatusIndex = statuses.indexOf(currentStatus);
            const statusSelect = document.getElementById('status');
            const newStatusIndex = statuses.indexOf(statusSelect.value);

            if (newStatusIndex <= currentStatusIndex) {
                alert('You cannot downgrade the ticket status.');
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }

        document.getElementById('status').addEventListener('change', function() {
            const commentField = document.getElementById('commentField');
            commentField.style.display = this.value === 'Closed' ? 'block' : 'none';
        });

        window.onclick = function(event) {
            if (event.target == document.getElementById('commentModal')) {
                document.getElementById('commentModal').style.display = 'none';
            }
            if (event.target == document.getElementById('statusModal')) {
                document.getElementById('statusModal').style.display = 'none';
            }
        }
    </script>
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
