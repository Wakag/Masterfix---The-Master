<?php

require_once("configpie.php");

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("<p class=\"error\">Connection to the database failed!</p>");
}

// Function to retrieve the average turnaround times and counts for closed tickets
function getClosedData()
{
    global $conn;
    $ticketId = $_SESSION['residence_id'];

    $sql = "SELECT AVG(TIMESTAMPDIFF(DAY, DateCreated, DateResolved)) as avg_turnaround_time, COUNT(*) as count
            FROM maintenancetickets
            WHERE Status = 'Closed' AND TicketResID = $ticketId";

    $result = $conn->query($sql);
    $closedData = [];

    if ($result) {
        $row = $result->fetch_assoc();
        $avgTime = round($row['avg_turnaround_time'], 2) . ' days'; // rounding for better display
        $closedData[] = [$avgTime, (int)$row['count']];
    }

    return $closedData;
}

// Function to retrieve the average turnaround times and counts for resolved tickets
function getResolvedData()
{
    global $conn;
    $ticketId = $_SESSION['residence_id'];

    $sql = "SELECT AVG(TIMESTAMPDIFF(DAY, DateCreated, DateResolved)) as avg_turnaround_time, COUNT(*) as count
            FROM maintenancetickets
            WHERE Status = 'Resolved' AND TicketResID = $ticketId";

    $result = $conn->query($sql);
    $resolvedData = [];

    if ($result) {
        $row = $result->fetch_assoc();
        $avgTime = round($row['avg_turnaround_time'], 2) . ' days'; // rounding for better display
        $resolvedData[] = [$avgTime, (int)$row['count']];
    }

    return $resolvedData;
}

// Function to get total number of maintenance faults
function getTotalMaintenanceFaults()
{
    global $conn;

    $ticketId = $_SESSION['residence_id'];

    $sql = "SELECT COUNT(*) as total FROM maintenancetickets where TicketResID = $ticketId";
    $result = $conn->query($sql);
    $total = 0;

    if ($result) {
        $row = $result->fetch_assoc();
        $total = (int)$row['total'];
    }

    return $total;
}

// Function to retrieve the count of tickets stuck in "Confirmed" for more than 7 days
function getStuckConfirmedTickets()
{
    global $conn;
    $ticketId = $_SESSION['residence_id'];

    $sql = "SELECT COUNT(*) as count
            FROM maintenancetickets
            WHERE Status = 'Confirmed' AND TIMESTAMPDIFF(DAY, DateCreated, NOW()) > 7 AND TicketResID = $ticketId";

    $result = $conn->query($sql);
    $count = 0;

    if ($result) {
        $row = $result->fetch_assoc();
        $count = (int)$row['count'];
    }

    return $count;
}

// Get the data
$closedData = getClosedData();
$resolvedData = getResolvedData();
$totalFaults = getTotalMaintenanceFaults();
$stuckConfirmedCount = getStuckConfirmedTickets();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Fault Stats</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #F3E5F5;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .stat-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        .stat-card {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 200px;
            margin: 20px;
            padding: 20px;
            text-align: center;
        }

        .stat-title {
            font-size: 16px;
            font-weight: bold;
            color: #444;
        }

        .stat-number {
            font-size: 40px;
            color: #6A1B9A;
            font-weight: bold;
        }

        .stat-category {
            font-size: 14px;
            color: #388E3C;
        }
    </style>
</head>

<body>
    <div class="stat-container">
        <!-- Section for Closed Tickets -->
        <?php
        foreach ($closedData as $data) {
            echo "
                <div class='stat-card'>
                    <p class='stat-title'>Average Turnaround Time: {$data[0]}</p>
                    <p class='stat-number'>{$data[1]}</p>
                    <p class='stat-category'>Closed Tickets</p>
                </div>";
        }
        ?>

        <!-- Section for Resolved Tickets -->
        <?php
        foreach ($resolvedData as $data) {
            echo "
                <div class='stat-card'>
                    <p class='stat-title'>Average Turnaround Time: {$data[0]}</p>
                    <p class='stat-number'>{$data[1]}</p>
                    <p class='stat-category'>Resolved Tickets</p>
                </div>";
        }
        ?>

        <!-- Section for Total Maintenance Faults -->
        <div class='stat-card'>
            <p class='stat-title'>Total Maintenance Faults</p>
            <p class='stat-number'><?php echo $totalFaults; ?></p>
            <p class='stat-category'>Total Faults</p>
        </div>

        <!-- Section for Tickets Stuck at Confirmed for More Than 7 Days -->
        <div class='stat-card'>
            <p class='stat-title'>Tickets Stuck at Confirmed > 7 Days</p>
            <p class='stat-number'><?php echo $stuckConfirmedCount; ?></p>
            <p class='stat-category'>Stuck Confirmed</p>
        </div>
    </div>
</body>
<section id="blockchart3-section"></section>

</html>
