<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterFix</title>
    <link rel="shortcut icon" href="images/icon-200x200.png" type="image/x-icon">

    <!--Scripts-->
    <script src="mainScript copy.js"></script>
    <!--Stylesheets-->
    <link rel="stylesheet" href="sliding-nav copy.css">
    <link rel="stylesheet" href="mainCss copy.css">
    <!--Font and Icons-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Include Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <style>
    /* Base styles */
    body,
    html {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
        height: 100%;
    }

    /* Ensure full height for body */
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* Navbar styles */
    .navbar {
        display: flex;
        justify-content: space-between;
        background-color: #f0f0f0;
        padding: 10px 20px;
        flex-wrap: wrap;
    }

    .navbar a {
        padding: 10px 20px;
        text-decoration: none;
        color: black;
        font-size: 1rem;
    }

    /* Top bar section */
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .left-section {
        display: flex;
        align-items: center;
    }

    .toggle-btn {
        margin-right: 10px;
        cursor: pointer;
    }

    /* Swiper section container */
    .swiper-section {
        width: 100%;
        background-color: #f9f9f9;
        flex-grow: 1;
        padding: 40px 0;
    }

    /* Swiper container */
    .swiper-container {
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 40px;
    }

    /* Stat card styles */
    .stat-card {
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 250px;
        margin: 10px;
        padding: 20px;
        text-align: center;
    }

    /* Pie chart container */
    #progressChart {
        width: 90%;
        max-width: 900px;
        height: 500px;
        margin: 50px auto;
    }

    /* Footer at the bottom */
    footer {
        text-align: center;
        padding: 20px;
        background-color: #f0f0f0;
        font-size: 0.8rem;
        position: relative;
        bottom: 0;
        width: 100%;
    }

    /* Responsive styles using media queries */
    @media (max-width: 1200px) {
        .swiper-container {
            width: 90%;
        }

        .stat-card {
            width: 220px;
        }
    }

    @media (max-width: 768px) {
        .swiper-container {
            width: 95%;
        }

        .stat-card {
            width: 200px;
        }

        .navbar a {
            font-size: 0.9rem;
            padding: 8px 15px;
        }

        .top-bar {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .swiper-container {
            width: 100%;
        }

        .stat-card {
            width: 180px;
        }

        .navbar a {
            font-size: 0.8rem;
        }

        .top-bar {
            font-size: 0.8rem;
        }
    }

    .charts-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        width: 100%;
    }

    #progressChart,
    #linechart_container {
        width: 90%;
        max-width: 900px;
        /* Keeps charts responsive */
        margin: 0 auto;
        /* Centers the charts */
    }
    </style>
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
            <span id="user-type">User-Type</span>
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

    <!-- Background image section -->
    <section class="Under-Top-Bar-section">
        <div class="utbs-text">
            <p class="utbs-title">MasterFix</p>
            <p class="utbs-subtitle">Name of Residence</p>
        </div>
    </section>

    <!-- Swiper Section -->
    <section class="swiper-section">
        <div class="swiper-container mySwiper">
            <div class="swiper-wrapper">
                <!-- PHP logic to dynamically populate swiper slides -->
                <?php
                require_once("configpie.php");

                $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

                // Check for database connection error
                if ($conn->connect_error) {
                    die("<p class=\"error\">Connection to the database failed!</p>");
                }

                // Initialize $residenceStats as an empty array
                $residenceStats = [];

                // Function to determine the semester based on the creation date
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

                // SQL query to fetch residence names and ticket creation dates
                $sql = "
    SELECT r.ResidenceName, mt.DateCreated 
    FROM maintenancetickets mt
    JOIN residence r ON mt.TicketResID = r.Residence_id
";

                $result = $conn->query($sql);

                // Check if the query executed successfully
                if ($result === FALSE) {
                    die("<p class=\"error\">Unable to retrieve data!</p>");
                }

                // Process the result if there are rows returned
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $residenceName = $row['ResidenceName'];
                        $creationDate = $row['DateCreated'];
                        $semester = getSemester($creationDate);

                        // If semester is valid, increment the count for the residence and semester
                        if ($semester !== null) {
                            if (!isset($residenceStats[$residenceName][$semester])) {
                                $residenceStats[$residenceName][$semester] = 0;
                            }
                            $residenceStats[$residenceName][$semester]++;
                        }
                    }
                } else {
                    echo "<p>No maintenance tickets found.</p>";
                }

                $conn->close();
                ?>

                <!-- Swiper Section -->
                <div class="swiper-container mySwiper">
                    <div class="swiper-wrapper">
                        <?php
                        // Ensure $residenceStats has data before looping
                        if (!empty($residenceStats)) {
                            foreach ($residenceStats as $residenceName => $semesters) {
                                foreach ($semesters as $semester => $count) {
                                    echo "
                    <div class='swiper-slide'>
                        <div class='stat-card'>
                            <p class='stat-title'>Maintenance Faults - $residenceName</p>
                            <p class='stat-number'>$count</p>
                            <p class='stat-percentage'>Semester $semester</p>
                            <p class='stat-increase'>Total faults this semester</p>
                        </div>
                    </div>";
                                }
                            }
                        } else {
                            echo "<p>No data available to display.</p>";
                        }
                        ?>
                    </div>

                    <!-- Swiper navigation -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>

                    <!-- Swiper pagination -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>
    </section>
    <section>
        <!-- Pie Chart Section -->
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
        <div id="progressChart">
            <canvas id="pieChart"></canvas>
        </div>
    </section>

    <section>
        <!-- Add this section where you have the Pie Chart Section in your existing code -->
        <div class="charts-container" style="display: flex; justify-content: space-between; padding: 20px;">
            <?php

            require_once("configpie.php");

            $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

            if ($conn->connect_error) {
                die("<p class=\"error\">Connection to the database failed!</p>");
            }

            // Fetch historical data by month and category for the year 2024
            $sql = "SELECT DATE_FORMAT(DateCreated, '%Y-%m') AS MonthYear, COUNT(*) AS count 
        FROM maintenancetickets 
        WHERE YEAR(DateCreated) = 2024
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
            <div id="progressChart" style="width: 90%; max-width: 900px;">
                <canvas id="pieChart"></canvas>
            </div>
            <!-- Line Chart Section -->
            <div id="linechart_container" style="width: 90%; max-width: 900px; margin-top: 20px;">
                <div id="linechart_material" style="height: 500px;"></div>
            </div>

        </div>


    </section>

    <!-- JavaScript for Swiper -->
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
    };
    </script>

    <script type="text/javascript">
    google.charts.load('current', {
        'packages': ['line']
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
            chart: {
                title: 'Maintenance Requests by Month for 2024',
                subtitle: ''
            },
            width: '100%',
            height: 500,
            axes: {
                x: {
                    0: {
                        label: 'Month',
                    }
                },
                y: {
                    0: {
                        label: 'Number of Maintenance Requests',
                    }
                }
            },
            curveType: 'function',
            legend: {
                position: 'bottom',
            }
        };

        var chart = new google.charts.Line(document.getElementById('linechart_material'));

        chart.draw(data, google.charts.Line.convertOptions(options));

        var options = {
            responsive: true,
            maintainAspectRatio: false,
        };
    };
    </script>

    </script>
</body>

</html>