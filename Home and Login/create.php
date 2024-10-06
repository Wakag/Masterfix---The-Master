<?php
    require_once("toolkit\config.php");

// Create a connection
    $conn = new mysqli(SERVER, USER, PASS, DB);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
 
    
    // Prepare and execute the query to fetch residences
    $sql = "SELECT ResidenceName FROM residence"; // Adjust table and column names as needed
    $result = $conn->query($sql);
    
    // Fetch all residences
    $residences = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $residences[] = $row;
        }
    }




// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form input data
    $firstName = $conn->real_escape_string($_POST['Name']);
    $lastName = $conn->real_escape_string($_POST['LastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $rhodesID = $conn->real_escape_string($_POST['rhodesid']); // Rhodes ID = Student ID
    $username = $conn->real_escape_string($_POST['user']);
    $password = $conn->real_escape_string($_POST['pass']);
    $residenceName = $conn->real_escape_string($_POST['residences']);
    $roomNumber = $conn->real_escape_string($_POST['RoomNum']);

    
    // Hash the password using SHA1
    $hashedPassword = sha1($password);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into users table
        $sql1 = "INSERT INTO users (Username, Password, Role) VALUES ('$username', '$hashedPassword', 'Student')";
        if (!$conn->query($sql1)) {
            throw new Exception("Error inserting into users table: " . $conn->error);
        }

        // Get the last inserted user ID
        $userID = $conn->insert_id;

        // Fetch the Residence ID based on ResidenceName
        $residenceQuery = "SELECT Residence_id FROM residence WHERE ResidenceName = '$residenceName'";
        $resResult = $conn->query($residenceQuery);
        if ($resResult->num_rows > 0) {
            $resRow = $resResult->fetch_assoc();
            $residenceID = $resRow['Residence_id'];
        } else {
            throw new Exception("Residence not found.");
        }

        // Insert into student table
        $sql2 = "INSERT INTO student (StudentID, Student_UserID, FirstName, LastName, Email, RoomNumber, ResID) 
                 VALUES ('$rhodesID', '$userID', '$firstName', '$lastName', '$email', '$roomNumber', '$residenceID')";
        if (!$conn->query($sql2)) {
            throw new Exception("Error inserting into student table: " . $conn->error);
        }

        // Commit transaction
        $conn->commit();

        // Display success message using JavaScript alert
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

 
// Close the connection at the end of script execution
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
                <input class="log1" type="text" id="Name" name="Name" required placeholder="Lerato">
            </div>

            <div class="form-group">
                <label for="LastName" class="roboto-regular">Last Name:</label>
                <input class="log1" type="text" id="LastName" name="LastName" required placeholder="Dlamini">
            </div>
            <div class="form-group">
                <label for="email" class="roboto-regular">Email Address:</label>
                <input class="log1" type="email" id="email" name="email" required placeholder="dlamini@example.com">
            </div>

            <div class="form-group">
                <label for="rhodesid" class="roboto-regular">Rhodes ID:</label>
                <input class="log1" type="text" id="rhodesid" name="rhodesid" pattern="^G\d{2}[A-Za-z]\d{4}$" required placeholder="G22X8069">
            </div>


            <div class="form-group">
                <label for="username" class="roboto-regular">Username:</label>
                <input class="log1" type="text" id="username" name="user" required placeholder="LDlamini">
            </div>

            <div class="form-group">
                <label for="pass" class="roboto-regular">Password:</label>
                <input class="log1" type="password" id="pass" name="pass" required placeholder="Password">
            </div>

            <div class="form-group">
                <label for="residences">Select Residence:</label>
                <select name="residences" id="residences" required>
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

            <div class="form-group">
                <label for="rhodesid" class="roboto-regular">Room Number:</label>
                <input class="log1" type="text" id="RoomNum" name="RoomNum" required placeholder="XX">
            </div>

            <button class="custom-button" type="submit">Create Account</button>
        </form>
        <a class="help" href="help Page/help Page.html">Help?</a>
    </div>


    <footer>
        <div>
            <p>The Masters &nbsp;
                <a href="#"><i class="fa-brands fa-facebook-f" style="color: #f8d4fd; font-size: 1.5rem; padding-right: 1rem;"></i></a>
                <a href="#"><i class="fa-brands fa-twitter" style="color: #f8d4fd; font-size: 1.5rem; padding-right: 1rem;"></i></a>
                <a href="#"><i class="fa-brands fa-linkedin" style="color: #f8d4fd; font-size: 1.5rem;padding-right: 1rem;"></i></a>
                <a href="#"><i class="fa-brands fa-instagram" style="color: #f8d4fd; font-size: 1.5rem;"></i></a>
            </p>
            <p>&copy; Copyright <script>document.write(new Date().getFullYear())</script> | All Rights Reserved.</p>
        </div>
    </footer>
</body>

</body>
</html>




