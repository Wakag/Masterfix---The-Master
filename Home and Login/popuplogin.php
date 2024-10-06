<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/create.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <title>Login</title>
</head>

<body>

    <header>
        <!-- Your header content -->
    </header>

    <div class="login-container">
        <img src="assets/Picture1.png" alt="Master Logo" class="logo">

        <h1>MasterFix</h1>
        <form action="" method="post">



            <div class="form-group">
                <label for="username" class="roboto-regular">Username:</label>
                <input class="log1" type="text" id="username" name="user" value="" required placeholder="LDlamini">
            </div>

            <div class="form-group">
                <label for="pass" class="roboto-regular">Password:</label>
                <input class="log1" type="password" id="pass" name="pass" value="" required placeholder="Password">
            </div>


            <button class="custom-button" type="submit">Login</button>
        </form>
        <a class="help" href="FAQ/faq.php">Help?</a>
    </div>


    <footer>
        <div>
            <p>The Masters &nbsp;
                <a href="#"><i class="fa-brands fa-facebook-f"
                        style="color: #f8d4fd; font-size: 1.5rem; padding-right: 1rem;"></i></a>
                <a href="#"><i class="fa-brands fa-twitter"
                        style="color: #f8d4fd; font-size: 1.5rem; padding-right: 1rem;"></i></a>
                <a href="#"><i class="fa-brands fa-linkedin"
                        style="color: #f8d4fd; font-size: 1.5rem;padding-right: 1rem;"></i></a>
                <a href="#"><i class="fa-brands fa-instagram" style="color: #f8d4fd; font-size: 1.5rem;"></i></a>
            </p>
            <p>&copy; Copyright <script>
                    document.write(new Date().getFullYear())
                </script> | All Rights Reserved.</p>
        </div>
    </footer>
</body>

</body>

</html>

<?php

session_start();
require_once("toolkit/config.php");

// Create a connection
$conn = new mysqli(SERVER, USER, PASS, DB);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only process the form if it's been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Check if form data is actually being posted
    //var_dump($_POST);

    // Check if both the user and pass fields are set and not empty
    if (!empty($_POST['user']) && !empty($_POST['pass'])) {

        // Get the login credentials from the form
        $user = $conn->real_escape_string($_POST['user']);
        $pass = $conn->real_escape_string($_POST['pass']);

        // Hash the input password using SHA1 to compare it with the stored hash
        $hashedPassword = sha1($pass);

        // Prepare the SQL query to check the login credentials
        $sql = "SELECT UserID, Username, Role FROM users WHERE Username = '$user' AND Password = '$hashedPassword'";
        $result = $conn->query($sql);

        if ($result->num_rows === 1) {
            // If the user is found, fetch the user data
            $userData = $result->fetch_assoc();
            $role = $userData['Role'];

            // Store user data in session to track login
            $_SESSION['userID'] = $userData['UserID'];
            $_SESSION['username'] = $userData['Username'];
            $_SESSION['access'] = $role;
            $_SESSION['role'] = $role;

            // Role-based redirection
            switch ($role) {
                case 'Admin':
                    header('Location: admin_dashboard.php');
                    exit();
                case 'Student':
                    header('Location:Student/dashboard.php');
                    exit();
                case 'Warden':
                    header('Location: warden/dashboard.php');
                    exit();
                case 'Maintenance Staff':
                    header('Location: maintenance/dashboard.php');
                    exit();
                case 'Hall Secretary':
                    header('Location: hallsec/dashboard.php');
                    exit();
                default:
                    echo '<script>alert("No matching users found.");</script>';
                    break;
            }
        } else {
            // If the credentials are incorrect, show an error message
            echo "<script>alert('Invalid username or password. Please try again.');</script>";
        }
    } else {
        // If either field is empty, we don't process and display nothing
        // Optionally, you can display an error if both fields are required.
        echo "<script>alert('Both username and password are required.');</script>";
    }
}

// Close the database connection
$conn->close();
?>