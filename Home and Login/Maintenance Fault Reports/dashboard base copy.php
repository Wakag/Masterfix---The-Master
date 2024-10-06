<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterFix</title>
    <link rel="shortcut icon" href="images\icon-200x200.png" type="image/x-icon">

    <!-- Scripts -->
    <script src="mainScript copy.js"></script>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="sliding-nav copy.css">
    <link rel="stylesheet" href="mainCss copy.css">
    <!-- Font and Icons -->
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Ensure full height for body */
        body {
            display: flex;
            min-height: 100vh;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            <a href=""></a>
            <a href="#">Something's not working?</a>
            <p>&copy; 2024 MasterFix</p>
        </footer>
    </div>

    <header class="top-bar">
        <div class="left-section">
            <div class="toggle-btn" onclick="toggleNavbar()">
                <i class="fas fa-bars"></i> <!-- Font Awesome hamburger icon -->
            </div>
            <p class="page-title">MasterFix</p>
        </div>
        <img src="images\RU_Logo_with_RU120_Logo-1.png" height="50" alt="RU Logo">
        <div class="account-section">
            <span id="user-type">User-Type</span>
            <i class="fas fa-user icon" alt="user" title="User" style="font-size: 25px;"></i>
            <div class="dropdown">
                <i id="trigger-popup" class="fas fa-ellipsis-h" alt="options" title="Options" onclick="toggleDropdown()"
                    style="font-size: 25px;"></i>
                <div id="options" class="dropdown-content">
                    <a href="#" class="dropdown-item">Help</a>
                    <a href="#" class="dropdown-item"
                        onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Background image section, just under top bar -->
    <section class="Under-Top-Bar-section">
        <div class="utbs-text">
            <p class="utbs-title">MasterFix</p>
            <p class="utbs-subtitle">Name of Residence</p>
            <!--This name will be set using php-->
        </div>
    </section>

    <!-- Main Content Section for Including Charts and Graphs -->
    <section class="dashboard-content">
        <div class="charts-section" style="padding: 50px;">
            <!-- Include the PHP files for different charts and graphs here -->
            <div class="chart">
                <?php include 'piechart2.php';
                ?>
            </div>

            <div class="chart">
                <?php include 'blockchart3.php';
                ?>
            </div>

            <div class="chart">
                <?php include 'piechart4.php';
                ?>
            </div>

            <div class="chart">
                <?php include 'FaultSwiper.php';
                ?>
            </div>

            <!-- Add more includes for other charts or sections -->
        </div>
    </section>

    <!-- You can add a footer section if needed -->
</body>

</html>