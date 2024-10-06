<?php
require_once("configpie.php");

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("<p class=\"error\">Connection failed: " . $conn->connect_error . "</p>");
}

// Assume student is logged in and their StudentID is available
$studentID = 'G15M0001'; // Replace this with the actual logged-in student's ID

// Fetch the residence ID for the student
$sql = "SELECT ResID FROM student WHERE StudentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$stmt->bind_result($resID);
$stmt->fetch();
$stmt->close();

// Fetch maintenance tickets for the residence
$sql = "SELECT * FROM maintenancetickets WHERE TicketResID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $resID);
$stmt->execute();
$result = $stmt->get_result();

// Display maintenance tickets
?>
<!DOCTYPE html>
<html>

<head>
    <title>Maintenance Tickets</title>
    <style>
        .ticket-block {
            border: 1px solid #ddd;
            margin: 10px;
            padding: 10px;
        }
    </style>
</head>

<body>
    <h1>Maintenance Tickets for Your Residence</h1>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="ticket-block">
            <h2>Ticket Title: <?php echo htmlspecialchars($row['TicketTitle']); ?></h2>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($row['TicketCategory']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($row['Description']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($row['Status']); ?></p>
            <p><strong>Date Created:</strong> <?php echo htmlspecialchars($row['DateCreated']); ?></p>
            <?php if ($row['DateResolved']): ?>
                <p><strong>Date Resolved:</strong> <?php echo htmlspecialchars($row['DateResolved']); ?></p>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
    <?php $stmt->close(); ?>
    <?php $conn->close(); ?>
</body>

</html>