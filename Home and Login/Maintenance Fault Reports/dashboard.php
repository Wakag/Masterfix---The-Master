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

        /* Pie and Line chart container */
        .charts-container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        /* Pie chart container */
        #progressChart {
            width: 45%;
            /* Adjust as needed */
            height: 500px;
        }

        /* Line chart container */
        #linechart_material {
            width: 45%;
            /* Adjust as needed */
            height: 500px;
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

            .charts-container {
                flex-direction: column;
                align-items: center;
            }

            #progressChart,
            #linechart_material {
                width: 100%;
                /* Make charts full width on smaller screens */
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
                $sql = "SELECT r.ResidenceName, mt.DateCreated 
                        FROM maintenancetickets mt
                        JOIN residence r ON mt.TicketResID = r.Residence_id";

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
                }

                // Close the database connection
                $conn->close();

                // Output the stats into Swiper slides
                foreach ($residenceStats as $residenceName => $semesters) {
                    echo '<div class="swiper-slide">';
                    echo '<div class="stat-card">';
                    echo "<h2>$residenceName</h2>";
                    echo "<p>Semester 1: " . ($semesters[1] ?? 0) . "</p>";
                    echo "<p>Semester 2: " . ($semesters[2] ?? 0) . "</p>";
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <!-- Charts Section -->
    <section class="charts-container">
        <div id="progressChart"></div>
        <div id="linechart_material"></div>
    </section>

    <footer>
        <p>&copy; 2024 MasterFix. All Rights Reserved.</p>
    </footer>

    <!-- Include necessary scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/google-charts@1.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

    <script>
        // Initialize the Swiper
        const swiper = new Swiper('.mySwiper', {
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

        // Pie Chart Data
        const pieData = {
            labels: ['Completed', 'Pending'],
            datasets: [{
                label: 'Maintenance Tickets',
                data: [5, 10],
                backgroundColor: ['#36A2EB', '#FF6384'],
            }]
        };

        // Line Chart Data
        const lineData = {
            labels: ['January', 'February', 'March', 'April', 'May', 'June'],
            datasets: [{
                label: 'Monthly Tickets',
                data: [10, 20, 30, 40, 50, 60],
                borderColor: '#FF6384',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
            }]
        };

        // Draw the Pie Chart
        const ctx1 = document.getElementById('progressChart').getContext('2d');
        new Chart(ctx1, {
            type: 'pie',
            data: pieData,
        });

        // Draw the Line Chart
        google.charts.load('current', {
            packages: ['corechart', 'line']
        });
        google.charts.setOnLoadCallback(drawLineChart);

        function drawLineChart() {
            const data = new google.visualization.DataTable();
            data.addColumn('string', 'Month');
            data.addColumn('number', 'Tickets');
            data.addRows([
                ['January', 10],
                ['February', 20],
                ['March', 30],
                ['April', 40],
                ['May', 50],
                ['June', 60],
            ]);

            const options = {
                title: 'Monthly Tickets',
                curveType: 'function',
                legend: {
                    position: 'bottom'
                },
            };

            const chart = new google.visualization.LineChart(document.getElementById('linechart_material'));
            chart.draw(data, options);
        }

        // Toggle Navbar function
        function toggleNavbar() {
            // Your toggle logic
        }

        // Toggle Dropdown function
        function toggleDropdown() {
            // Your dropdown logic
        }
    </script>
</body>

</html>