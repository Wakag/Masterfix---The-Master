<?php
require_once("configpie.php");

$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DATABASE);

if ($conn->connect_error) {
    die("<p class=\"error\">Connection to the database failed!</p>");
}

// Query for resolved tickets
$sqlResolved = "
    SELECT 
        CASE 
            WHEN DATEDIFF(DateResolved, DateCreated) <= 1 THEN 'Less than 1 day'
            WHEN DATEDIFF(DateResolved, DateCreated) BETWEEN 2 AND 3 THEN '1-3 days'
            WHEN DATEDIFF(DateResolved, DateCreated) BETWEEN 4 AND 7 THEN '4-7 days'
            ELSE 'More than 7 days'
        END as TurnaroundCategory,
        COUNT(*) as count
    FROM maintenancetickets
    WHERE Status = 'Resolved'
    GROUP BY TurnaroundCategory
";

$resultResolved = $conn->query($sqlResolved);
$resolvedData = [];
if ($resultResolved->num_rows > 0) {
    while ($row = $resultResolved->fetch_assoc()) {
        $resolvedData[] = [$row['TurnaroundCategory'], (int)$row['count']];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolved Tickets Swiper</title>

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
</head>

<body>

    <div class="section-title">Resolved Tickets</div>

    <!-- Swiper container -->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <!-- Loop through resolved tickets data -->
            <?php
            foreach ($resolvedData as $data) {
                echo "
                    <div class='swiper-slide'>
                        <div class='stat-card'>
                            <p class='stat-title'>Turnaround Time: {$data[0]}</p>
                            <p class='stat-number'>{$data[1]}</p>
                            <p class='stat-category'>Resolved Tickets</p>
                        </div>
                    </div>";
            }
            ?>
        </div>

        <!-- Add navigation buttons -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>

        <!-- Add pagination -->
        <div class="swiper-pagination"></div>
    </div>

    <!-- Include Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

    <script>
        var swiper = new Swiper('.mySwiper', {
            slidesPerView: 3,
            spaceBetween: 30,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    </script>

</body>

</html>