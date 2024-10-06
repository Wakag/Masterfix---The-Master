<?php
session_start();

// Include your database configuration file
require_once("toolkit/config.php");

// Check if the TicketID is set in the query string
if (isset($_GET['id'])) {
    $ticketID = $_GET['id'];

    // Establish a database connection
    $mysqli = new mysqli(SERVER, USER, PASS, DB);

    // Check for connection errors
    if ($mysqli->connect_error) {
        die("Database connection failed: " . $mysqli->connect_error);
    }

    // Start a transaction
    $mysqli->begin_transaction();

    try {
        // Check if the ticket exists in the picture table
        $checkSql = "SELECT pictureID FROM pictures WHERE picture_ticket_id = ?";
        $checkStmt = $mysqli->prepare($checkSql);

        // Check for preparation errors
        if (!$checkStmt) {
            throw new Exception("Prepare failed: " . $mysqli->error);
        }

        $checkStmt->bind_param("i", $ticketID);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        // If a picture exists, delete it from the picture table
        if ($checkResult->num_rows > 0) {
            $deletePictureSql = "DELETE FROM pictures WHERE picture_ticket_id = ?";
            $deletePictureStmt = $mysqli->prepare($deletePictureSql);

            // Check for preparation errors
            if (!$deletePictureStmt) {
                throw new Exception("Prepare failed: " . $mysqli->error);
            }

            $deletePictureStmt->bind_param("i", $ticketID);
            $deletePictureStmt->execute();
            $deletePictureStmt->close();
        }

        // Delete the ticket from the maintenancetickets table
        $deleteTicketSql = "DELETE FROM maintenancetickets WHERE TicketID = ?";
        $deleteTicketStmt = $mysqli->prepare($deleteTicketSql);

        // Check for preparation errors
        if (!$deleteTicketStmt) {
            throw new Exception("Prepare failed: " . $mysqli->error);
        }

        $deleteTicketStmt->bind_param("i", $ticketID);
        $deleteTicketStmt->execute();
        $deleteTicketStmt->close();

        // Commit the transaction
        $mysqli->commit();

        echo "<script>alert('Ticket and associated pictures deleted successfully.'); window.location.href='Student/dashboard.php';</script>";
    } catch (Exception $e) {
        // Rollback the transaction on error
        $mysqli->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close the statement
    $checkStmt->close();

    // Close the database connection
    $mysqli->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='Student/dashboard.php';</script>";
}
?>
