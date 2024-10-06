<?php
session_start();

if (!isset($_SESSION["access"]) || $_SESSION["access"] !== "Warden") {
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
    <title>MasterFix</title>
    <link rel="shortcut icon" href="images/icon-200x200.png" type="image/x-icon">

    <!--Scripts-->
    <script src="mainScript.js"></script>
    <script src="dashboardPage.js"></script>
    <!--Stylesheets-->
    <link rel="stylesheet" href="mainCss.css">
    <link rel="stylesheet" href="../mainCssReports_2.css">
    <link rel="stylesheet" href="sliding-nav.css">
    <link rel="stylesheet" href="dashboardCss.css">
    <link rel="stylesheet" href="modalCssReports.css">
    <!--Font and Icons-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Tooltip container */
        .tooltip {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        /* Tooltip text */
        .tooltip .tooltiptext {
            visibility: hidden;
            width: 300px;
            /* Increased width for a larger tooltip */
            background-color: #f9f9f9;
            color: #333;
            text-align: left;
            border-radius: 5px;
            padding: 15px;
            /* Increased padding for more space */
            position: absolute;
            z-index: 5;
            bottom: -1000%;
            /* Adjusted position to accommodate larger size */
            left: -350%;
            margin-left: -100px;
            /* Adjusted to center the tooltip */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            /* A slightly stronger shadow */
            opacity: 0;
            transition: opacity 0.3s;
        }

        /* Show the tooltip text when hovering over the icon */
        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>

<body>

    <!-- Sliding Nav -->
    <div class="navbar" id="sideNavbar">
        <a href="../../Warden/dashboard.php" class="nav-link">My Dashboard</a>
        <a href="../../Warden/requisitions.php" class="nav-link">View Requisitions</a>
        <a href="../../Warden/new-ticket.php" class="nav-link">Make a requisition</a>
        <a href="" class="nav-link">Stats for Geeks</a>
        <footer>
            <p>&copy; 2024 MasterFix</p>
            <a href="Broken.html"><i class="fas fa-exclamation-circle" title="Something's not working?"></i></a>
        </footer>
    </div>

    <section class="top">
        <header class="top-bar">
            <div class="left-section">
                <div class="toggle-btn" onclick="toggleNavbar()">
                    <i class="fas fa-bars"></i>
                </div>
                <p class="page-title">&nbsp&nbspMasterFix</p>
            </div>
            <img src="images/RU_Logo_with_RU120_Logo-1.png" height="50" alt="RU Logo">
            <div class="account-section">
                <span id="user-type"><?php echo $_SESSION["role"]; ?></span>
                <div class="tooltip">
                    <i class="fas fa-user icon" alt="user" style="font-size: 25px;"></i>
                    <span class="tooltiptext">

                        <h4 style="text-align: center;">User Info:</h4>
                        <p><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
                        <p><strong>Role:</strong> <?php echo $_SESSION['role']; ?></p>
                        <p><strong>Residence Name:</strong> <?php echo $_SESSION['residence_name']; ?></p>
                    </span>
                </div>
                <div class="dropdown">
                    <i id="trigger-popup" class="fas fa-ellipsis-h " alt="options" title="Options"
                        onclick="toggleDropdown()" style="font-size: 25px;"></i>
                    <div id="options" class="dropdown-content">
                        <a href="" class="dropdown-item">Reset Password</a>
                        <a href="../logout.php" class="dropdown-item"
                            onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                    </div>
                </div>
        </header>
        <section>
            <section class="Under-Top-Bar-section">
                <div class="utbs-text">
                    <p class="utbs-title">MasterFix</p>
                    <p class="utbs-subtitle">Maintenance Fault Reports</p>
                </div>
            </section>

            <section class="blockchart3-section">
                <?php include("../blockchart3_2.php"); ?>
                </div>
            </section>

            <section class="charts-container">
                <div class="charts-wrapper">
                    <!-- Include piechart2.php -->
                    <?php include("../piechart2.php"); ?>

                    <!-- Include piechart4.php -->
                    <?php include("../piechart4.php"); ?>
                </div>
            </section>

            <!-- Scatter chart section -->
            <section class="scatter-chart-section">
                <!-- Include scatter chart (number of total maintenance requests per month) -->
                <div id="scatterchart_maintenance_requests"></div>
                <div class="scatterChartContainer">
                    <?php include("../graph5.php"); ?>
                </div>
            </section>

        </section>

        <!-- Google Charts Loader -->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

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

    <!-- Main content area -->
    <section class="tickets-list">

    </section>
    </section>

    <!-- Modal for Task Details -->

    <script src="dashboardPage.js"></script>

</body>

</html>