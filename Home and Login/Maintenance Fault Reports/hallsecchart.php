<?php
// Database connection setup
require_once("configpie.php");

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("<p class=\"error\">Connection failed: " . $conn->connect_error . "</p>");
}

$hallId = $_SESSION['hall_id'];
$hallName = $_SESSION['hallname'];

// If hall name or ID is not set in the session, handle the error
if (!$hallId || !$hallName) {
    die("<p class=\"error\">No hall information found in the session.</p>");
}

// Query to get residence names and their respective ticket categories for the specific hall
$sql = "
    SELECT residence.ResidenceName AS ResidenceName, maintenancetickets.TicketCategory, COUNT(*) as TicketCount
    FROM halls
    JOIN residence ON halls.hall_id = residence.res_hall_id
    JOIN maintenancetickets ON residence.residence_id = maintenancetickets.TicketResID
    WHERE halls.hall_id = ?
    GROUP BY residence.ResidenceName, maintenancetickets.TicketCategory
";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("<p class=\"error\">SQL preparation failed: " . $conn->error . "</p>");
}

// Bind the hall ID parameter
$stmt->bind_param('i', $hallId);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("<p class=\"error\">Query execution failed: " . $stmt->error . "</p>");
}

// Initialize the residence data array
$residences = [];
while ($row = $result->fetch_assoc()) {
    // Initialize residence row if not already set
    if (!isset($residences[$row['ResidenceName']])) {
        $residences[$row['ResidenceName']] = [
            'Capentry' => 0,
            'Electrical' => 0,
            'General Maintenance' => 0,
            'Plumbing' => 0
        ];
    }

    // Define valid categories that should be counted
    $validCategories = ['Capentry', 'Electrical', 'General Maintenance', 'Plumbing'];

    if (in_array($row['TicketCategory'], $validCategories)) {
        $residences[$row['ResidenceName']][$row['TicketCategory']] += (int)$row['TicketCount'];
    }
}

$stmt->close();
$conn->close();

// Prepare the data for Google Charts
$chartData = [];
$chartData[] = ['Residence', 'Capentry', 'Electrical', 'General Maintenance', 'Plumbing']; // Header row
foreach ($residences as $residenceName => $categories) {
    $chartData[] = [
        $residenceName,
        (int)$categories['Capentry'],
        (int)$categories['Electrical'],
        (int)$categories['General Maintenance'],
        (int)$categories['Plumbing']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Ticket Categories for <?php echo htmlspecialchars($hallName); ?></title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // Load Google Charts
        google.charts.load('current', {
            'packages': ['corechart', 'bar']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable(<?php echo json_encode($chartData); ?>);

            var options = {
                chart: {
                    title: 'Maintenance Ticket Categories for <?php echo $hallName; ?>',
                    subtitle: 'Capentry, Electrical, General Maintenance, Plumbing'
                },
                isStacked: true,
                bars: 'horizontal',
                colors: ['#6A1B9A', '#CE93D8', '#f9ccff', '#130b09'],
                hAxis: {
                    title: 'Number of Tickets',
                },
                vAxis: {
                    title: 'Residence Name',
                }
            };

            var chartDiv = document.getElementById('chart_div');
            var chart = new google.visualization.BarChart(chartDiv);
            chart.draw(data, options);
        }
    </script>
    <style>
        body {
            background-color: #F3E5F5;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 20px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        #chart_div {
            width: 1200px;
            height: 600px;
            margin-bottom: 40px;
        }
    </style>
</head>

<body>
    <div id="chart_div">
    </div>
</body>

</html>