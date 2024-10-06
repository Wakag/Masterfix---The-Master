<?php
session_start();

if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Maintenance Staff") {
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

        // ... (existing HTML code)

        // Check if a comment was successfully added
        $showSuccessMessage = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
            // Example variables - replace these with your actual data or session variables
            $commentUserId = $_SESSION["userID"]; // User ID of the person making the comment
            $commentTicketId = $_GET["id"]; // Ticket ID the comment relates to
            $commentText = $_POST["comment"]; // The comment content
            $currentDate = date("Y-m-d"); // Use the current date

            // Prepare the SQL statement
            $sql = "INSERT INTO comments (comment_user_id, comment_ticket_id, commentDate, content) VALUES (?, ?, ?, ?)";
            $commentStmt = $mysqli->prepare($sql);

            if ($commentStmt) {
                // Bind the parameters
                $commentStmt->bind_param("iiss", $commentUserId, $commentTicketId, $currentDate, $commentText);
                
                // Execute the statement
                if ($commentStmt->execute()) {
                    $showSuccessMessage = true;
                } else {
                    echo "Error: " . $commentStmt->error; // Output error if execution fails
                }

                // Close the statement
                $commentStmt->close();
            } else {
                echo "Prepare failed: " . $mysqli->error; // Output error if prepare fails
            }
        }

        // ... (existing HTML code)
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
</head>
<body>
  <!-- Sliding Nav -->
  <div class="navbar" id="sideNavbar">
        <a href="dashboard.php" class="nav-link">My Dashboard</a>
        <a href="requisitions.php" class="nav-link">View Requisitions</a>
        <a href="new-ticket.php" class="nav-link">Make a requisition</a>
        <a href="#" class="nav-link">Stats for Geeks</a>
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
            <i class="fas fa-user icon" alt="user" title="User" style="font-size: 25px;"></i>
            <div class="dropdown">
            <i id="trigger-popup" class="fas fa-ellipsis-h " alt="options" title="Options" onclick="toggleDropdown()"style="font-size: 25px;"></i>
                <div id="options" class="dropdown-content">
                    <a href="#" class="dropdown-item">Help</a>
                    <a href="../logout.php" class="dropdown-item" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                </div>
            </div>
    </header>
    
    

   
    <!-- Main content area --> 
    <div class="Kontent">
        <!-- Header -->
        <div class="page-header">
            <h2><?php echo "Showing Details for Requisition #".$ticketID?></h2>
        </div>

        <!-- Ticket Details Section -->
        <div class="ticket-details" >
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

                
                <div class="description-section">
                    <label class="info-label" for="description">Description</label>
                    <textarea id="description" class="description-textarea" readonly><?php echo htmlspecialchars($row['Description']); ?></textarea>
                </div><br>

                <div class="description-section">
                <h4>Comments: </h4>
            <?php
                // Database connection (ensure config.php contains your database connection settings)
                require_once("config.php");

                // Establish the connection
                $mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

                // Check for connection errors
                if ($mysqli->connect_error) {
                    die("Connection failed: " . $mysqli->connect_error);
                }

                // Prepare the SQL query
                $sql = "SELECT Username, role, content 
                        FROM comments 
                        JOIN users ON users.UserID = comments.comment_user_id
                        Where comments.comment_ticket_id = ?";

                // Prepare the statement
                if ($stmt = $mysqli->prepare($sql)) {
                    $stmt->bind_param("i", $ticketID);
                    // Execute the statement
                    $stmt->execute();
                    
                    // Get the result set
                    $result = $stmt->get_result();

                    // Check if there are results
                    if ($result->num_rows > 0) {
                        // Loop through the results and display them
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="comment-box">';
                            echo '<p><strong>' . htmlspecialchars($row['Username']) . ' (' . htmlspecialchars($row['role']) . ')</strong></p>';
                            echo '<p>' . htmlspecialchars($row['content']) . '</p>';
                            echo '</div>';}
                    } else {
                        echo "<p>No comments found.</";
                    }

                    // Close the statement
                    $stmt->close();
                } else {
                    echo "Query preparation failed: " . $mysqli->error;
                }

                // Close the database connection
                $mysqli->close();
                ?>

            </div>



                <div id="comment-form" class="description-section" style="display: none;">
                    <form action="../addcomment.php?id=<?php echo $ticketID; ?>" method="POST">
                    <textarea  class="description-textarea" id="new-comment" name="comment" placeholder="Enter your comment..."></textarea><br><br>
                    <button id="submit-comment-btn" class="secondary">Submit</button>
                    </form>
                </div><br><br><br><br><br>
                <?php
            // Check if the comment was successfully added
            if (isset($_GET['success']) && $_GET['success'] == '1') {
                echo "<script>alert('Comment added successfully.');</script>";
                
            }?>

          
            </div>

            <!-- Ticket Image -->
            <div class="ticket-image">
            <?php
// Database connection (ensure config.php contains your database connection settings)
require_once("config.php");

// Establish the connection
$mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

// Check for connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Assume you have a variable $ticketId for the current ticket ID you are working with
if (isset($_GET['id'])) {
    $ticketId = $_GET['id']; // Get the ticket ID from the URL parameter

    // Prepare the SQL query to fetch images
    $sql = "SELECT picture_ticket_id, filePath 
            FROM pictures 
            JOIN maintenancetickets ON picture_ticket_id = TicketID 
            WHERE picture_ticket_id = ?";

    // Prepare the statement
    if ($stmt = $mysqli->prepare($sql)) {
        // Bind the parameter
        $stmt->bind_param("i", $ticketId);
        
        // Execute the statement
        $stmt->execute();
        
        // Get the result set
        $result = $stmt->get_result();

        // Check if there are results
        if ($result->num_rows > 0) {
            echo '<div class="image-gallery">'; // Optional: Add a container for styling
            // Loop through the results and display images
            while ($row = $result->fetch_assoc()) {
                echo '<div class="image-container">';
                echo '<img src="' . htmlspecialchars($row['filePath']) . '" alt="Ticket Image" style="max-width: 100%; height: auto;"/> '; // Display the image
                echo '</div>';
            }
            echo '</div>'; // Close the image gallery container
        } else {
            // If no images are found, display the default image
            $imagePath = 'images/icon-200x200.png'; // Set the path to your default image here
            echo '<div class="image-container">';
            echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Maintenance Issue Image" style="max-width: 100%; height: auto;"/>'; // Display the default image
            echo '</div>';
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Query preparation failed: " . $mysqli->error;
    }
} else {
    echo "<p>Ticket ID is missing.</p>";
}

// Close the database connection
$mysqli->close();
?>

                    <!-- <img src="<?php //echo htmlspecialchars($imagePath); ?>" alt="Maintenance Issue Image"> -->
                </div>
            </div>
            <div class="button-container">
                <a href="requisitions.php" class="back-link" title="Back to Requisitions">← Back to Requisitions</a>
                <div class="buttons">
                    <button id="add-comment-btn" class="primary" title="View Comments">Add Comments</button>
                    <button class="primary" title="Edit Status">Edit Status</button>
                    <button class="secondary" title="Close Ticket">Close Ticket</button>
                </div>
            </div>

            <script>
                // Show or hide the comment form when the "Add Comments" button is clicked
                document.getElementById('add-comment-btn').addEventListener('click', function() {
                    var commentForm = document.getElementById('comment-form');
                    commentForm.style.display = (commentForm.style.display === 'none' || commentForm.style.display === '') ? 'block' : 'none';
                });

                // Submit the new comment
                document.getElementById('submit-comment-btn').addEventListener('click', function() {
                    var newComment = document.getElementById('new-comment').value.trim(); // Trim whitespace
                    if (newComment !== ''){
                        // Clear the comment input and hide the form
                        document.getElementById('comment-form').style.display = 'none';
                    } else {
                        alert('Please enter a comment.'); // Alert if comment is empty
                    }
                });
            </script>

        </div>

        <!-- Action Buttons -->

    </div>
            </section>
</body>
</html>

