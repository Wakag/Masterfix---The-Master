<?php
require_once("configpie.php");

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("<p class=\"error\">Connection to the database failed!</p>");
}

// Initialize categories
$categories = [
    'Less than 1 day' => [0, 0], // [Closed count, Resolved count]
    '1-3 days' => [0, 0],
    '4-7 days' => [0, 0],
    'More than 7 days' => [0, 0]
];

// Query for closed tickets
$sqlClosed = "
    SELECT 
        CASE 
            WHEN DATEDIFF(DateResolved, DateCreated) <= 1 THEN 'Less than 1 day'
            WHEN DATEDIFF(DateResolved, DateCreated) BETWEEN 2 AND 3 THEN '1-3 days'
            WHEN DATEDIFF(DateResolved, DateCreated) BETWEEN 4 AND 7 THEN '4-7 days'
            ELSE 'More than 7 days'
        END as TurnaroundCategory,
        COUNT(*) as count
    FROM maintenancetickets
    WHERE Status = 'Closed'
    GROUP BY TurnaroundCategory
";

$resultClosed = $conn->query($sqlClosed);
if (!$resultClosed) {
    die("<p class=\"error\">Error fetching closed tickets: " . $conn->error . "</p>");
}

// Populate the closed data into the categories array
if ($resultClosed->num_rows > 0) {
    while ($row = $resultClosed->fetch_assoc()) {
        $categories[$row['TurnaroundCategory']][0] = (int)$row['count'];
    }
}

// Query for resolved tickets
$sqlResolved = "
    SELECT 
        CASE 
            WHEN DATEDIFF(DateResolved, DateCreated) <= 1 THEN 'Less than 1 day'
            WHEN DATEDIFF(DateResolved, DateCreated) BETWEEN 2 AND 3 THEN '1-3 days'
            WHEN DATEDIFF(DateResolved, DateCreated) BETWEEN 4 AND 7 THEN '4-7 days'
            ELSE 'More than 7 days'
        END as TurnaroundCategory,
        COUNT(*) as count
    FROM maintenancetickets
    WHERE Status = 'Resolved'
    GROUP BY TurnaroundCategory
";

$resultResolved = $conn->query($sqlResolved);
if (!$resultResolved) {
    die("<p class=\"error\">Error fetching resolved tickets: " . $conn->error . "</p>");
}

// Populate the resolved data into the categories array
if ($resultResolved->num_rows > 0) {
    while ($row = $resultResolved->fetch_assoc()) {
        $categories[$row['TurnaroundCategory']][1] = (int)$row['count'];
    }
}

$conn->close();

// Prepare data for passing to JavaScript
$chartData = [];
foreach ($categories as $category => $counts) {
    $chartData[] = [$category, $counts[0], $counts[1]]; // [Category, Closed, Resolved]
}
?>

<!-- Block3graph Section -->
<section class="block3graph-section">
    <h2 class="graph-section-title">Maintenance Fault Stats on Turnaround Times</h2>
    <div id="block3graph-section"></div>
</section>

<script type="text/javascript">
    google.charts.load('current', {
        'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Turnaround Time', 'Closed Tickets', 'Resolved Tickets'],
            <?php
            foreach ($chartData as $data) {
                echo "['{$data[0]}', {$data[1]}, {$data[2]}],";
            }
            ?>
        ]);

        var options = {
            chart: {
                title: 'Maintenance Fault Stats on Turnaround Times',
                subtitle: 'Closed vs Resolved Tickets',
            },
            colors: ['#6A1B9A', '#000000'],
            chartArea: {
                width: '85%', // Increase chart area width
                height: '75%' // Increase chart area height
            },
            height: 500, // Adjust chart height to fit container
            width: '100%' // Use full width of the container
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
    }
</script>