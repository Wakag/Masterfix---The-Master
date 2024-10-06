
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterFix</title>
    <link rel="shortcut icon" href="assets/Picture1.png" type="image/x-icon">
    <link rel="icon" type="image/x-icon" href="images/Screenshot 2024-08-19 204136.png">
    <link rel="stylesheet" href="css/MyStyles.css">
    <link rel="stylesheet" href="aboutus.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="header-panel">
              <div id="main">
                
              </div>
            <h1 class="roboto-bold"><a href="#home">MasterFix</a></h1>
        <div class="right-bar">
            <ul class="navigation">
                
                <li><a href="#team-members" style="text-align: right;">About</a></li>
                <li><a href="help Page/help Page.html">Help</a></li>
                <li><button class="custom-button" id="openFormButton">Sign-Up</button></li>
                <li><a href="popuplogin.php"><button  class="custom-button">Login</button></a></li>
            </ul>
        </div>
        </div>   
    </header>
    <?php
// PHP code to handle form submission
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    // Define valid roles
    $validRoles = ['Student', 'Warden', 'Maintenance Staff', 'Hall Secretary'];

    // Check if the submitted role is valid
    if (in_array($role, $validRoles)) {
        switch ($role) {
            case 'Student':
                header('Location: create.php');
                exit();
            case 'Warden':
                header('Location: createwarden.php?role=' . urlencode($role));
                exit();
            case 'Maintenance Staff':
                header('Location: createwarden.php?role=' . urlencode($role));
                exit();
            case 'Hall Secretary':
                header('Location: createwarden.php?role=' . urlencode($role));
                exit();
        }
    } else {
        echo 'Invalid role selected.'; // Handle the invalid role case
    }
    ?>
    <div class="main-overlay" id="main-overlay"></div>

    <div class="form-popup" id="myForm">
        <form action="" method="Post" class="form-container">
          <h1>Create Account</h1><br><br>
      
          <label for=role><b>User Role</b></label><br>
          <select name="role" id="role">
            <option value="Student">Student</option>
            <option value="Warden">Warden</option>
            <option value="Hall Secretary">Hall Secretary</option>
            <option value="Maintenance Staff">Maintenance Staff</option>
          </select>
          <br><br>
      
          <button type="submit" class="custom-button">Create Account</button><br>
          <button type="button" id="closeFormButton" class="close-button" onclick="closeForm()"><i class="fa-solid fa-xmark"></i></button>
        </form>
    </div>

    
    <section class="home" id="home">
      <div class="home-content">
          
          <h1>Welcome to MasterFix</h1>
          <p class="roboto-regular">
            The Master Fix System is a user-friendly web application designed to simplify maintenance for students in Rhodes University Residences. It allows students to register, link their residence, and submit detailed maintenance reports.

            <br><br>
            
            House Wardens can quickly access these reports to validate issues. Once verified, they escalate the concerns to Hall Secretaries, who work with maintenance staff to prioritize and resolve the problems efficiently.
          </p>
      </div>

      <div class="profession-container">
          <div class="profession-box">
              <div class="profession" style="--i:0;">
                  <i class='bx bx-code-alt'></i>
                  <h3>Wardens</h3>
              </div>
              <div class="profession" style="--i:1;">
                  <i class='bx bx-camera'></i>
                  <h3>Students</h3>
              </div>
              <div class="profession" style="--i:2;">
                  <i class='bx bx-palette'></i>
                  <h3>Hall Secretaries</h3>
              </div>
              <div class="profession" style="--i:3;">
                  <i class='bx bx-video-recording'></i>
                  <h3>Maintenance Staff</h3>
              </div>

              <div class="circle"></div>
          </div>

          <div class="overlay"></div>
      </div>
  </section>
  <!--Section for Meet The Team-->
  <h2 style="font-size: 55px; font-weight: 700">-Meet Our Team-</h2>
    <section id="team-members">
            <!--Div containers for all the different members of The Masters Team-->
    <table>
        <tr>
            <td>
                <div class="team-member">
                <img src="waka.jpeg" alt="Member 1">
                <h3>Wakanaka Gurure</h3>
                <p>Role: Project Manager  <br>Email: gururewakanaka@gmail.com</p> <!--Technique used for all the details to fit-->
                
                </div>
                    </td>
                    <td>
                        <div class="team-member">
                            <img src="ambrose.jpeg" alt="Member 2">
                            <h3>Ambrose Kibowa</h3>
                            <p>Role: Software Tester <br>Email: ambrosekibowa@gmail.com</p> <!--Technique used for all the details to fit-->
                            
                        </div>
                    </td>
                    <td>
                        <div class="team-member">
                            <img src="mako.jpeg" alt="Member 3">
                            <h3>Makomborero Murwira</h3>
                            <p>Role: Software Coder <br>Email: makomurwira23@gmail.com</p> <!--Technique used for all the details to fit-->
                            
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="team-member">
                            <img src="sihle.jpeg" alt="Member 4">
                            <h3>Siphosihle Ngcukana</h3>
                            <p>Role: System Designer <br>Email: siphosihlengcukana@gmail.com</p> <!--Technique used for all the details to fit-->
                       
                        </div>
                    </td>
                    <td>
                        <div class="team-member">
                            <img src="munya.jpeg" alt="Member 5">
                            <h3>Munyaradzi Chiradza</h3>
                            <p>Role: Systems Analyst <br>Email: mrchiradza@gmail.com</p> <!--Technique used for all the details to fit-->
                    
                        </div>
                    </td>
                </tr>
            </table>
    </section>
    
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

<script src="scripts.js"></script>

</html>