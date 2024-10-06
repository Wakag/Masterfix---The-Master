<?php


require_once("toolkit/config.php");

// Create a connection
$conn = new mysqli(SERVER, USER, PASS, DB);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute the query to fetch residences
$sql = "SELECT ResidenceName FROM residence";
$result = $conn->query($sql);

// Fetch all residences
$residences = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $residences[] = $row;
    }
}

// Prepare and execute the query to fetch halls
$sqlHalls = "SELECT hall_id, hall_name FROM halls";
$resultHalls = $conn->query($sqlHalls);

// Fetch all halls
$halls = [];
if ($resultHalls->num_rows > 0) {
    while ($row = $resultHalls->fetch_assoc()) {
        $halls[] = $row;
    }
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form input data
    $role = isset($_POST['role']) ? $_POST['role'] : '';
    $rhodesID = $conn->real_escape_string($_POST['rhodesid']); 
    $firstName = $conn->real_escape_string($_POST['Name']); 
    $lastName = $conn->real_escape_string($_POST['LastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);  
    $username = $conn->real_escape_string($_POST['user']);
    $password = $conn->real_escape_string($_POST['pass']);

    // Hash the password using SHA1
    $hashedPassword = sha1($password);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into users table using prepared statements
        $stmt1 = $conn->prepare("INSERT INTO users (Username, Password, Role) VALUES (?, ?, ?)");
        $stmt1->bind_param("sss", $username, $hashedPassword, $role);

        if (!$stmt1->execute()) {
            throw new Exception("Error inserting into users table: " . $conn->error);
        }

        // Get the last inserted user ID
        $userID = $conn->insert_id;

        if ($role == "Maintenance Staff") {
            // Insert into maintenance staff table
            $stmt2 = $conn->prepare("INSERT INTO maintenancestaff (StaffID, MUserID, FirstName, LastName, Email, Phone) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("sissss", $rhodesID, $userID, $firstName, $lastName, $email, $phone);

            if (!$stmt2->execute()) {
                throw new Exception("Error inserting into maintenancestaff table: " . $conn->error);
            }
        }

        // Commit transaction
        $conn->commit();

        // Display success message using JavaScript alert
        // header("Location: Success.php");

        echo "<script>
            alert('Account created successfully! Please log in with your new credentials.');
            window.location.href = 'HomeV2.php';
          </script>";

    } catch (Exception $e) {
        // Rollback transaction in case of an error
        $conn->rollback();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/create.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <title>Create Account</title>
</head>
<body>

    <header>
        <!-- Your header content -->
    </header>

    <div class="sign-in-container">
        <img src="assets/picture1.png" alt="Master Logo" class="logo">

        <h1>MasterFix</h1>
        <form action="" method="post">
    <div class="form-group">
        <label for="Name" class="roboto-regular">Name:</label>
        <input class="log1" type="text" id="Name" name="Name" required placeholder="First Name">
    </div>

    <div class="form-group">
        <label for="LastName" class="roboto-regular">Last Name:</label>
        <input class="log1" type="text" id="LastName" name="LastName" required placeholder="Last Name">
    </div>

    <div class="form-group">
        <label for="email" class="roboto-regular">Email Address:</label>
        <input class="log1" type="email" id="email" name="email" required placeholder="email@example.com">
    </div>

    <div class="form-group">
        <label for="phone" class="roboto-regular">Phone:</label>
        <input class="log1" type="tel" id="phone" name="phone" required placeholder="Phone Number">
    </div>

    <div class="form-group">
                <label for="rhodesid" class="roboto-regular">Rhodes ID:</label>
                <input class="log1" type="text" id="rhodesid" name="rhodesid" required placeholder="22X8069">
    </div>


    <div class="form-group">
        <label for="username" class="roboto-regular">Username:</label>
        <input class="log1" type="text" id="username" name="user" required placeholder="Username">
    </div>

    <div class="form-group">
        <label for="pass" class="roboto-regular">Password:</label>
        <input class="log1" type="password" id="pass" name="pass" required placeholder="Password">
    </div>

  


    <!-- Residence dropdown (initially hidden) -->
    <div class="form-group" id="residence-box" style="display:none;">
        <label for="residences">Select Residence:</label>
        <select name="residences" id="residences">
            <option value="" disabled selected></option> <!-- Placeholder option -->
            <?php
            // Check if there are residences and populate the dropdown
            if (!empty($residences)) {
                foreach ($residences as $residence) {
                    echo '<option value="' . htmlspecialchars($residence['ResidenceName']) . '">' . htmlspecialchars($residence['ResidenceName']) . '</option>';
                }
            } else {
                echo '<option value="">No residences available</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group" id="hall-box" style="display:none;">
    <label for="halls">Select Hall:</label>
    <select name="halls" id="halls">
        <option value="" disabled selected></option> <!-- Placeholder option -->
        <?php
        // Check if there are halls and populate the dropdown
        if (!empty($halls)) {
            foreach ($halls as $hall) {
                echo '<option value="' . htmlspecialchars($hall['hall_id']) . '">' . htmlspecialchars($hall['hall_name']) . '</option>';
            }
        } else {
            echo '<option value="">No halls available</option>';
        }
        ?>
    </select>
    </div>
    <div class="form-group">        
        <input type="text" name="role" id="role" value="<?php echo $role = htmlspecialchars($_GET['role']);?>" hidden>
    </div>

    <button class="custom-button" type="submit">Create Account</button>
</form>

<script>
    // JavaScript function to toggle the residence box visibility
    function toggleFields() {
        var role = document.getElementById('role').value;  // Get the role from the hidden input
        var residenceBox = document.getElementById('residence-box');  // Residence field
        var hallBox = document.getElementById("hall-box");// Hall field

        if (role === 'Warden') {
            residenceBox.style.display = 'block';  // Show residence box for Warden
            hallBox.style.display = "none";        // Hide hall box
        } else if (role === 'Hall Secretary') {
            hallBox.style.display = "block";       // Show hall box for Hall Secretary
            residenceBox.style.display = 'none';   // Hide residence box
        } else {
            residenceBox.style.display = 'none';   // Hide both for other roles
            hallBox.style.display = "none";
        }
    }

    // Ensure fields are toggled correctly when the page loads
    window.onload = toggleFields;

    // Add event listener to trigger toggle when the role value changes
    document.getElementById('role').addEventListener('change', toggleFields);
</script>



        <a class="help" href="help Page/help Page.html">Help?</a>
    </div>


    <footer>
        <div>
        <p>The Masters &nbsp;
    <a href="https://www.facebook.com/" target="_blank">
        <i class="fa-brands fa-facebook-f" style="color: #f8d4fd; font-size: 1.5rem; padding-right: 1rem;"></i>
    </a>
    <a href="https://www.twitter.com/" target="_blank">
        <i class="fa-brands fa-twitter" style="color: #f8d4fd; font-size: 1.5rem; padding-right: 1rem;"></i>
    </a>
    <a href="https://www.linkedin.com/" target="_blank">
        <i class="fa-brands fa-linkedin" style="color: #f8d4fd; font-size: 1.5rem; padding-right: 1rem;"></i>
    </a>
    <a href="https://www.instagram.com/" target="_blank">
        <i class="fa-brands fa-instagram" style="color: #f8d4fd; font-size: 1.5rem;"></i>
    </a>
    </p>

            <p>&copy; Copyright <script>document.write(new Date().getFullYear())</script> | All Rights Reserved.</p>
        </div>
    </footer>
</body>

</body>
</html>




