<?php
if (isset($_POST['closeTicket'])) {
    $ticketID = $_GET['id'];
    require_once("config.php");
    echo $ticketID = $_GET['id'];
    // Database connection
    $mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // First, check if DateResolved is NULL for the given ticketID
    $checkDateSql = "SELECT DateResolved FROM maintenancetickets WHERE TicketID = ?";
    $stmt = $mysqli->prepare($checkDateSql);
    $stmt->bind_param("i", $ticketID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Check if DateResolved is NULL or not
    if ($row && is_null($row['DateResolved'])) {
        // If DateResolved is NULL, update both DateResolved and Status
        $updateSql = "UPDATE maintenancetickets SET Status = 'Closed', DateResolved = CURDATE() WHERE TicketID = ?";
    } else {
        // If DateResolved is NOT NULL, update only the Status
        $updateSql = "UPDATE maintenancetickets SET Status = 'Closed' WHERE TicketID = ?";
    }

    // Prepare statement for update
    $stmt = $mysqli->prepare($updateSql);
    $stmt->bind_param("i", $ticketID);

    // Execute and handle success or failure
    if ($stmt->execute()) {
        echo "<script>alert('Ticket status updated to Closed');</script>";
        header("Location: requisitions.php");
        exit();  // Always use exit after header to stop further execution
    } else {
        echo "<script>alert('Error updating record: " . $mysqli->error . "');</script>";
    }

    // Close statement and connection
    $stmt->close();
    $mysqli->close();
}
?>