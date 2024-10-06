<?php
    session_start();

    if (!isset($_SESSION["access"]) || isset($_POST['comment'])) {
        echo "<script>alert('Sorry. You do not have access to this page :(');</script>";
        header("Location: HomeV2.php");
        exit(); 
    }

    require_once("toolkit/config.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $commentUserId = $_SESSION["userID"]; // User ID of the person making the comment
        $commentTicketId = $_GET["id"]; // Ticket ID the comment relates to
        $commentText = $_POST["comment"]; // The comment content
        $currentDate = date("Y-m-d"); // Use the current date

        // Establish a database connection
        $mysqli = new mysqli(SERVER, USER, PASS, DB);

        // Check for connection errors
        if ($mysqli->connect_error) {
            die("Database connection failed: " . $mysqli->connect_error);
        }

        // Prepare the SQL statement
        $sql = "INSERT INTO comments (comment_user_id, comment_ticket_id, commentDate, content) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            // Bind the parameters
            $stmt->bind_param("iiss", $commentUserId, $commentTicketId, $currentDate, $commentText);
            
            // Execute the statement
            if ($stmt->execute()) {
                header("Location: Student/ViewRequisition.php?id=" . $commentTicketId . "&success=1");
                exit;
            } else {
                echo "Error: " . $stmt->error; // Output error if execution fails
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Prepare failed: " . $mysqli->error; // Output error if prepare fails
        }
    }