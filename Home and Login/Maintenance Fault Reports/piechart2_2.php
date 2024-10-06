<?php
require_once("configpie.php");

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("<p class=\"error\">Connection to the database failed!</p>");
}

$sql = "
    SELECT Status, COUNT(*) as count
    FROM maintenancetickets
    GROUP BY Status
";

$result = $conn->query($sql);

if ($result === FALSE) {
    die("<p class=\"error\">Unable to retrieve data!</p>");
}

$statusData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusData[] = [$row['Status'], (int)$row['count']];
    }
}
$conn->close();
?>

<!-- Pie Chart Section -->
<div id="progressChart"></div>

<!-- Google Charts Script -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load("current", {
    packages: ["corechart"]
});
google.charts.setOnLoadCallback(drawProgressChart);

function drawProgressChart() {
    var data = google.visualization.arrayToDataTable([
        ['Status', 'Number of Faults'],
        <?php
            foreach ($statusData as $data) {
                echo "['" . $data[0] . "', " . $data[1] . "],";
            }
            ?>
    ]);

    var options = {
        title: 'Maintenance Faults Progress',
        pieHole: 0.4,
        colors: ['#6A1B9A', '#CE93D8', '#f9ccff', '#130b09'],
        chartArea: {
            width: '80%',
            height: '80%'
        },
    };

    var chart = new google.visualization.PieChart(document.getElementById('progressChart'));
    chart.draw(data, options);

    var options = {
        responsive: true,
        maintainAspectRatio: false,
    };
}
</script>

<style>
#progressChart {
    width: 900px;
    height: 500px;
    margin: 0 auto;
}
</style>