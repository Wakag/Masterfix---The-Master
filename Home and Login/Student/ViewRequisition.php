<?php
session_start();

if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Student") {
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
    // Check if the prepare() function failed
    if ($stmt === false) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("i", $ticketID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Check if filePath is empty or null
        $imagePath = !empty($row['filePath']) ? $row['filePath'] : 'images\RU-logo.png';

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
    } else {
        echo "No record found";
    }

    $stmt->close();
    $mysqli->close();
} else {
    echo "Invalid request";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterFix</title>
    <link rel="shortcut icon" href="images\icon-200x200.png" type="image/x-icon">

<!--Scripts-->
    <script src="mainScript.js"></script>
<!--Stylesheets-->
    <link rel="stylesheet" href="sliding-nav.css">
    <link rel="stylesheet" href="mainCss.css">
    <link rel="stylesheet" href="viewrequisition.css">
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
    <section class="top">
    <header class="top-bar">
        <div class="left-section">
        <div class="toggle-btn" onclick="toggleNavbar()">
            <i class="fas fa-bars"></i>  <!-- Font Awesome hamburger icon -->
            </div>
            <p class="page-title">MasterFix</p>
            
        </div>
        <img src="images\RU_Logo_with_RU120_Logo-1.png" height="50" alt="RU Logo">
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
            <i id="trigger-popup" class="fas fa-ellipsis-h " alt="options" title="Options" onclick="toggleDropdown()"style="font-size: 25px;"></i>
                <div id="options" class="dropdown-content">
                    <a href="../help Page/help Page.html" class="dropdown-item">Help</a>
                    <a href="../logout.php" class="dropdown-item" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                </div>
            </div>
    </header>
    
    
    <!--Background image section, just under top bar-->
    <section class="Under-Top-Bar-section">
        <div class="utbs-text">
            <p class="utbs-title">MasterFix</p>
            <p class="utbs-subtitle"><?php echo $_SESSION['residence_name']?></p><!--This name will be set using php-->
        </div>
    </section>
   
    <!-- Main content area --> 
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
        </div>

        <a href="requisitions.php" class="back-link" title="Back to Requisitions">← Back to Requisitions</a>
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
        window.onclick = function(event) {
            if (event.target == document.getElementById('commentModal')) {
                document.getElementById('commentModal').style.display = 'none';
            } 
        }

    </script>
</body>
</html>

