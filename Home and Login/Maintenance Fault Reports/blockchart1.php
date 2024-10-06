<?php

require_once("configpie.php");

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("<p class=\"error\">Connection to the database failed!</p>");
}

// Function to determine the semester based on the date
function getSemester($date)
{
    $semester1Start = strtotime("12 February 2024");
    $semester1End = strtotime("14 June 2024");
    $semester2Start = strtotime("8 July 2024");
    $semester2End = strtotime("15 November 2024");

    $currentDate = strtotime($date);

    if ($currentDate >= $semester1Start && $currentDate <= $semester1End) {
        return 1;
    } elseif ($currentDate >= $semester2Start && $currentDate <= $semester2End) {
        return 2;
    } else {
        return null; // Out of semester, should not count
    }
}

// SQL query to retrieve the ResidenceName, DateCreated for all TicketResID
$sql = "
    SELECT r.ResidenceName, mt.DateCreated 
    FROM maintenancetickets mt
    JOIN residence r ON mt.TicketResID = r.Residence_id
";

$result = $conn->query($sql);

if ($result === FALSE) {
    die("<p class=\"error\">Unable to retrieve data!</p>");
}

$residenceStats = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $residenceName = $row['ResidenceName'];
        $creationDate = $row['DateCreated'];

        // Get the semester based on the CreationDate
        $semester = getSemester($creationDate);

        // Only count if the date belongs to a valid semester
        if ($semester !== null) {
            if (!isset($residenceStats[$residenceName][$semester])) {
                $residenceStats[$residenceName][$semester] = 0;
            }
            $residenceStats[$residenceName][$semester]++;
        }
    }
}

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

    .stat-card {
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100px;
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

    .stat-percentage {
        font-size: 14px;
        color: #388E3C;
    }

    .stat-increase {
        font-size: 12px;
        color: #888;
    }
    </style>
</head>

<body>

    <?php
    // Display the stats in block format
    foreach ($residenceStats as $residenceName => $semesters) {
        foreach ($semesters as $semester => $count) {
            echo "
        <div class='stat-card'>
            <p class='stat-title'>Maintenance Faults - $residenceName</p>
            <p class='stat-number'>$count</p>
            <p class='stat-percentage'>Semester $semester</p>
            <p class='stat-increase'>Total faults this semester</p>
        </div>";
        }
    }
    ?>

</body>

</html>