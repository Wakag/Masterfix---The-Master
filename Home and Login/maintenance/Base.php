<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasterFix</title>
    <link rel="shortcut icon" href="images\icon-200x200.png" type="image/x-icon">

<!--Scripts-->
    <script src="mainScript.js"></script>
<!--Stylesheets-->
    <link rel="stylesheet" href="sliding-nav.css">
    <link rel="stylesheet" href="mainCss.css">
    <link rel="stylesheet" href="dashboardCss.css">
<!--Font and Icons-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
</head>
<body>
  <section class="content">
    <div class="navbar">
        <a href="#">My dashboard</a>
        <a href="#">View requisitions</a>
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
            <i class="fas fa-bars"></i>  <!-- Font Awesome hamburger icon -->
            </div>
            <p class="page-title">Maintenance</p>
            
        </div>
        <img src="images\RU_Logo_with_RU120_Logo-1.png" height="50" alt="RU Logo">
        <div class="account-section">
            <span id="user-type">User-Type</span>
            <i class="fas fa-user icon" alt="user" title="User" style="font-size: 25px;"></i>
            <div class="dropdown">
            <i id="trigger-popup" class="fas fa-ellipsis-h " alt="options" title="Options" onclick="toggleDropdown()"style="font-size: 25px;"></i>
                <div id="options" class="dropdown-content">
                    <a href="#" class="dropdown-item">Help</a>
                    <a href="#" class="dropdown-item" onclick="return confirm('Are you sure you want to log out?')">Logout</a>
                </div>
            </div>
    </header>
    
   
    <!-- Overview Section -->
    <div class="stats-overview">
      <div class="stat-card">
          <h3>Pending Tasks</h3>
          <p>15</p>
      </div>
      <div class="stat-card">
          <h3>Completed Today</h3>
          <p>5</p>
      </div>
      <div class="stat-card">
          <h3>Urgent Tasks</h3>
          <p>3</p>
      </div>
      <div class="stat-card">
          <h3>Overdue Tasks</h3>
          <p>2</p>
      </div>
    </div>
   
    <!-- Search and Filter Bar -->
    <div class="filter-bar">
      <input id="search" type="text" placeholder="Search by title, residence, or student name">
      <select id="status">
        <option value="all">All Statuses</option>
        <option value="Requisition">Requisitioned</option>
        <option value="Resolved">Resolved</option>
        <option value="Closed">Closed</option>
      </select>
      <select id="categories">
        <option value="all">All Categories</option>
        <option value="Electrical">Electrical</option>
        <option value="Plumbing">Plumbing</option>
        <option value="General">General Maintenance</option>
        <option value="Carpentry">Carpentry</option>
      </select>
    </div>

    <!-- Task List -->
    <div class="task-list">
      <!-- Task Card -->
      <div class="task-card">
          <h4>Fix Broken Window</h4>
          <p>Residence: $row['ResidenceName'],$row['HallName'], $row['RoomNumber']</p>
          <p>Category: General Maintenance · Priority: <span class="priority-high">High</span></p><!--Only Plumbing and Electrical are High Priority, Carpentry and General Maintenance are medium priority-->
          <p>Reported on: 19-Sep-2024</p>
          <button class="btn-action" onclick="openModal()">View Details</button>
          <button class="btn-complete">Mark as Completed</button>
      </div>
    </div>
    </section>
    <!-- Modal for Task Details -->
    <div id="taskModal" class="modal">
      <h3>Task Details: Fix $row['TicketTitle']</h3>
      <p><strong>Reported by:</strong> John Doe</p>
      <p><strong>Location:</strong> $row['ResidenceName'],$row['HallName'], $row['RoomNumber']</p>
      <p><strong>Description:</strong> The window in the room is cracked and needs to be replaced.</p>
      <p><strong>Category:</strong> General Maintenance</p>
      <p><strong>Priority:</strong> High</p>
      <p><strong>Attachments:</strong> <a href="#">View Image</a></p>
      <button class="btn-close" onclick="closeModal()">Close</button>
    </div>

<!-- JavaScript to handle modal -->
<script>
  function openModal() {
      document.getElementById("taskModal").style.display = "block";
  }

  function closeModal() {
      document.getElementById("taskModal").style.display = "none";
  }
</script>
</body>
</html>