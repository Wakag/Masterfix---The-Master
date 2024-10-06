<?php

require_once("configpie.php");

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("<p class=\"error\">Connection to the database failed!</p>");
}

$tickenametId = $_SESSION['residence_name'];
$ticketId = $_SESSION['residence_id'];

// Fetch historical data by month for the year 2024
$sql = "SELECT DATE_FORMAT(DateCreated, '%Y-%m') AS MonthYear, COUNT(*) AS count 
        FROM maintenancetickets 
        WHERE YEAR(DateCreated) = 2024 AND TicketResID = $ticketId
        GROUP BY MonthYear 
        ORDER BY MonthYear";

$result = $conn->query($sql);

if ($result === FALSE) {
    die("<p class=\"error\">Unable to retrieve data!</p>");
}

$ticketData = [];
while ($row = $result->fetch_assoc()) {
    $monthYear = $row['MonthYear'];
    $count = (int)$row['count'];
    $ticketData[$monthYear] = $count;
}
$conn->close();
?>

<script type="text/javascript">
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Month');
        data.addColumn('number', 'Number of Maintenance Requests');

        // Populate data for each month in 2024
        <?php
        // Ensure the months are sorted
        ksort($ticketData);
        foreach ($ticketData as $monthYear => $count) {
            $month = date('F Y', strtotime($monthYear . '-01')); // Format to 'Month Year'
            echo "data.addRow(['" . $month . "', " . $count . "]);";
        }
        ?>

        var options = {
            title: 'Maintenance Requests by Month for 2024 of <?php echo $tickenametId ?>',
            hAxis: {
                title: 'Month'
            },
            vAxis: {
                title: 'Number of Maintenance Requests',
                minValue: 0
            },
            legend: 'none',
            pointSize: 5, // Adjust the size of scatter points
            width: '100%',
            height: 500,
            colors: ['#6A1B9A'], // Custom color for the scatter points
        };

        var chart = new google.visualization.ScatterChart(document.getElementById('scatterchart_maintenance_requests'));

        chart.draw(data, options);
    }
</script>

<!-- Use the correct <div> ID -->
<div id="scatterchart_maintenance_requests" style="width: 100%; height: 500px;"></div>