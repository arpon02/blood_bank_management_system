<?php
session_start();
$website_role = $_SESSION['website_role'] ?? null; // 'admin', 'user', or null
$user_id = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Blood Bank | Home</title>
  <link rel="stylesheet" href="style.css" >
  
</head>
<body>
  <header>
    <h1>Blood Bank Management System</h1>
    <p>Give the gift of life. Donate blood today.</p>
  </header>

<nav class="multi-row-nav">
  <div class="nav-row nav-row-1">
    <a href="index.php">Home</a>
	<a href="request_blood.php">Request Blood</a>
	<a href="blood_stock_search.php">Search Blood</a>
    <a href="search_emergency_donor.php">Search Emergency Donor</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
   </div>	
   <div class="nav-row nav-row-2">
    <?php if ($website_role  === 'admin'): ?>
      <a href="record_donation.php">Record Donation</a>
      <a href="record_received.php">Record Received</a>
      <a href="Records.php">All Records</a>
      <a href="search_user.php">Seach User</a>
      <a href="admin_dashboard.php">Show Requests</a> 
	  
      
	  <a href="register.php">Register User</a>
      <a href="logout.php">Logout</a>
   </div> 
   <div class="nav-row nav-row-1">   
    <?php elseif ($website_role  === 'user'): ?>
      <a href="user_dashboard.php">Dashboard</a>
      <a href="logout.php">Logout</a>
   </div>	  
    <?php else: ?>
   <div class="nav-row nav-row-1">	
      <a href="register.php">Register</a>
      <div class="dropdown">
        <a href="#">Login &#9662;</a>
        <div class="dropdown-content">
          <a href="login.php?website_role=admin">Admin</a>
          <a href="login.php?website_role=user">User</a>
        </div>
      </div>
    </div>	  
    <?php endif; ?>
</nav>

  <section class="hero">
    <h2>Be a Hero. Save Lives.</h2>
    <p>Your small act of kindness can save a life. Join us today.</p>
    <div class="buttons">
      <a href="register.php" class="btn">Become a User</a>
      <a href="request_blood.php" class="btn-outline">Request Blood</a>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 Blood Bank System | All Rights Reserved</p>
  </footer>
</body>
</html>