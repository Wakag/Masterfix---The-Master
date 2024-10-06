<!-- Include Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
<style>
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
</style>
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

            $hallId = $_SESSION['hall_id'];
            $hallName = $_SESSION['hallname'];

            // SQL query to fetch residence names and ticket creation dates
            $sql = "
    SELECT r.ResidenceName, mt.DateCreated 
    FROM maintenancetickets mt
    JOIN residence r ON mt.TicketResID = r.Residence_id
    WHERE TicketResID = $ticketId
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
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
</script>

</body>

</html>