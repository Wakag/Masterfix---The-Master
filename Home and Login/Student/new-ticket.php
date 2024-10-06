<?php

session_start();

if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Student") {
    echo "<script>alert('Sorry. You do not have access to this page :(');</script>";
    header("Location: ../HomeV2.php");
    exit(); 
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
    <link rel="stylesheet" href="new-ticket.css">
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
<?php
    // Include your database connection
    require 'config.php';

    // Define a variable to hold success or error messages
    $message = '';

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
        // Collect form data
        $title = $_POST['title'];
        $category = $_POST['category'];
        $description = $_POST['description'];
    
        $studentId = $_SESSION['student_id']; // Assuming student ID is fetched from session or elsewhere
        $residenceId = $_SESSION['residence_id']; // Assuming residence ID is known
        $status = 'Open'; // Default status for a new ticket
        $mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);
        
        // Handle file upload
        if (!empty($_FILES['upload']['name'])) {
            $targetDir = "../wwroot/";
            $fileName = time().basename($_FILES["upload"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
            // Allow only specific file formats
            $allowedTypes = array('jpg', 'png', 'jpeg');
            if (in_array($fileType, $allowedTypes)) {
                // Move file to the server
                if (move_uploaded_file($_FILES["upload"]["tmp_name"], $targetFilePath)) {
                    // Insert ticket into the 'maintenancetickets' table
                    $stmt = $mysqli->prepare("INSERT INTO maintenancetickets (Status, DateCreated, TicketStudentID, TicketCategory, Description, TicketResID, TicketTitle) 
                                              VALUES (?, CURDATE(), ?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        die("SQL error when preparing ticket insertion: " . $mysqli->error);
                    }
                    $stmt->bind_param("ssssss", $status, $studentId, $category, $description, $residenceId, $title);
                    $stmt->execute();
    
                    // Get the last inserted ticket ID
                    $ticketId = $mysqli->insert_id;
    
                    // Insert picture into 'picture' table
                    $stmt = $mysqli->prepare("INSERT INTO pictures (filePath, picture_ticket_id) VALUES (?, ?)");
                    $stmt->bind_param("si", $targetFilePath, $ticketId);
                    $stmt->execute();
                    $message = "Ticket submitted successfully!";
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                }
            } else {
                $message = "Sorry, only JPG, JPEG, & PNG files are allowed.";
            }
        } else {
            // If no file uploaded, still insert the ticket without a picture
            $stmt = $mysqli->prepare("INSERT INTO maintenancetickets (Status, DateCreated, TicketStudentID, TicketCategory, Description, TicketResID, TicketTitle) 
                                      VALUES (?, CURDATE(), ?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("SQL error: " . $mysqli->error);
            }
            $stmt->bind_param("ssssss", $status, $studentId, $category, $description, $residenceId, $title);
            $stmt->execute();

            echo "Title: $title, Category: $category, Description: $description, Student ID: $studentId, Residence ID: $residenceId";

    
            $message = "Ticket submitted successfully without a picture!";
        }
    }
    
    if (!empty($message)) {
        echo "<script>alert('$message');</script>";
    }
?>    
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
            <p class="utbs-subtitle"><?php echo $_SESSION["residence_name"];?></p><!--This name will be set using php-->
        </div>
    </section>
   
    <!-- Main content area --> 
    <form action="" method="POST" class="form-container" enctype="multipart/form-data">
        <div class="form-content">
            <div class="title">Please fill complete the form to submit a requisition</div>
            <div class="form-group">
                <div class="half-width">
                    <label for="title">Maintenance Issue</label>
                    <input type="text" id="title" name="title" placeholder="Enter a title for the issue, e.g. Broken window" required>
                </div>
                <div class="half-width">
                    <label for="category">Problem Category</label>
                    <select name="category" id="category" required>
                        <option value="" disabled selected></option>
                        <option value="General Maintenance">General Maintenance</option>
                        <option value="Plumbing">Plumbing</option>
                        <option value="Electrical">Electrical</option>
                        <option value="Carpentry">Carpentry</option>
                    </select>
                </div>
            </div>
            <div class="full-width">
                <label for="description">Please give a short description of the problem</label>
                <textarea id="description" name="description" placeholder="Enter a description of the issue here" required></textarea>
            </div>
            <div class="button-container">
                <div class="upload-section">
                    <label for="upload" class="upload-label">Upload a picture of the problem</label>
                    <input id="upload" name="upload" type="file" accept=".jpg, .jpeg, .png">
                </div>
                <button name="submit" id="submit" type="submit">Submit</button>
            </div>
        </div>
    </form>
</section>
</body>
</html>