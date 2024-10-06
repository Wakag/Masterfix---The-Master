<?php

session_start();


if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Maintenance Staff") {
    echo "<script>alert('Sorry. You do not have access to this page :(');</script>";
    header("Location: ../HomeV2.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterFix Maintenance Dashboard</title>
    <link rel="shortcut icon" href="images/icon-200x200.png" type="image/x-icon">

    <!-- Load Stylesheets -->
    <link rel="stylesheet" href="sliding-navReports.css">
    <link rel="stylesheet" href="mainCssReports.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Include Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />

</head>

<body>

    <div class="navbar">
        <a href="#">My dashboard</a>
        <a href="#">View requisitions</a>
        <a href="#">Make a requisition</a>
        <a href="#">Stats for geeks</a>
        <footer>
            <a href="#">Something's not working?</a>
            <p>&copy; 2024 MasterFix</p>
        </footer>
    </div>

    <header class="top-bar">
        <div class="left-section">
            <div class="toggle-btn" onclick="toggleNavbar()">
                <i class="fas fa-bars"></i>
            </div>
            <p class="page-title">MasterFix</p>
        </div>
        <img src="images/RU_Logo_with_RU120_Logo-1.png" height="50" alt="RU Logo">
        <div class="account-section">
            <span id="user-type"><?php echo $_SESSION['role'] ?></span>
            <i class="fas fa-user icon" style="font-size: 25px;"></i>
            <div class="dropdown">
                <i id="trigger-popup" class="fas fa-ellipsis-h" onclick="toggleDropdown()" style="font-size: 25px;"></i>
                <div id="options" class="dropdown-content">
                    <a href="#" class="dropdown-item">Help</a>
                    <a href="#" class="dropdown-item"
                        onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <section class="Under-Top-Bar-section">
        <div class="utbs-text">
            <p class="utbs-title">MasterFix</p>
            <p class="utbs-subtitle">Maintenance Fault Reports</p>
        </div>
    </section>

    <section class="swiper-section">
        <div class="swiper-container mySwiper">
            <div class="swiper-wrapper">
                <!-- Include the Fault Swiper PHP file content here -->
                <?php include("FaultSwiper.php"); ?>
            </div>
            <!-- Swiper navigation buttons -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <!-- Add the Pie Chart section below FaultSwiper -->
    <section class="charts-container">
        <div class="charts-wrapper">
            <!-- Include piechart2.php -->
            <?php include("piechart2.php"); ?>

            <!-- Include piechart4.php -->
            <?php include("piechart4.php"); ?>
        </div>

        <section class="block3graph-section">
            <!-- Include the Fault Swiper PHP file -->
            <?php include("block3graph.php"); ?>
        </section>

        <!-- Scatter chart section -->
        <section class="scatter-chart-section">
            <!-- Include scatter chart (number of total maintenance requests per month) -->
            <div id="scatterchart_maintenance_requests"></div>
            <div class="scatterChartContainer">
                <?php include("graph5.php"); ?>
        </section>


        <!-- Scripts -->
        <script src="mainScript copy.js"></script>
        <!-- Include Swiper JS -->
        <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
        <script>
        var swiper = new Swiper(".mySwiper", {
            slidesPerView: 3,
            spaceBetween: 30,
            loop: true,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
        </script>

        <!-- Google Charts Loader -->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

        <!-- Bar chart for turnaround times -->
        <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['bar']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Turnaround Time', 'Closed Tickets', 'Resolved Tickets'],
                <?php
                    foreach ($closedData as $index => $data) {
                        echo "['{$data[0]}', {$data[1]}, " . (isset($resolvedData[$index][1]) ? $resolvedData[$index][1] : 0) . "],";
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

        <!-- Scatter chart for maintenance requests -->
        <script type="text/javascript">
        google.charts.setOnLoadCallback(drawScatterChart);

        function drawScatterChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Month');
            data.addColumn('number', 'Maintenance Requests');

            // Populate data for each month in 2024
            <?php
                ksort($ticketData);
                foreach ($ticketData as $monthYear => $count) {
                    $month = date('F Y', strtotime($monthYear . '-01')); // Format to 'Month Year'
                    echo "data.addRow(['" . $month . "', " . $count . "]);";
                }
                ?>

            var options = {
                title: 'Maintenance Requests by Month for 2024',
                hAxis: {
                    title: 'Month'
                },
                vAxis: {
                    title: 'Number of Maintenance Requests',
                    minValue: 0
                },
                legend: 'none',
                pointSize: 5,
                chartArea: {
                    width: '85%', // Increase chart area width
                    height: '75%' // Increase chart area height
                },
                height: 500, // Adjust chart height to fit container
                width: '100%' // Use full width of the container
            };

            var chart = new google.visualization.ScatterChart(document.getElementById(
                'scatterchart_maintenance_requests'));
            chart.draw(data, options);
        }
        </script>

    </section>

</body>

</html>