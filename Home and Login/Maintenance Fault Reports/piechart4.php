<?php

require_once("configpie.php");

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("<p class=\"error\">Connection to the database failed!</p>");
}

$tickenametId = $_SESSION['residence_name'];
$ticketId = $_SESSION['residence_id'];

// Fetch data for all TicketResID
$sql = "SELECT TicketCategory, COUNT(*) as count FROM maintenancetickets where TicketResID = $ticketId GROUP BY TicketCategory";

$result = $conn->query($sql);

if ($result === FALSE) {
    die("<p class=\"error\">Unable to retrieve data!</p>");
}

$ticketData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ticketData[] = [$row['TicketCategory'], (int)$row['count']];
    }
}
$conn->close();

?>
<!DOCTYPE html>
<html>

<head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    google.charts.load("current", {
        packages: ["corechart"]
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Ticket Category', 'Number of Tickets'],
            <?php
                foreach ($ticketData as $data) {
                    echo "['" . $data[0] . "', " . $data[1] . "],";
                }
                ?>
        ]);

        var options = {
            title: 'Maintenance fault Maintenance categories stats of <?php echo $tickenametId ?>',
            pieHole: 0.4,
            colors: ['#6A1B9A', '#CE93D8', '#f9ccff', '#130b09'],
            chartArea: {
                width: '80%',
                height: '80%'
            }
        };

        var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
        chart.draw(data, options);

        var options = {
            responsive: true,
            maintainAspectRatio: false,
        };
    }
    </script>

    <style>
    body {
        background-color: #F3E5F5;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    #donutchart {
        width: 900px;
        height: 500px;
    }
    </style>
</head>

<body>
    <div id="donutchart"></div>
</body>

</html>