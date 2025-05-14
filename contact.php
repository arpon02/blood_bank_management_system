<?php

session_start();
$website_role = $_SESSION['website_role'] ?? null; // 'admin', 'user', or null
$user_id = $_SESSION['user_id'] ?? null;

$contacts = [
    [
        'name' => 'Aniqa Nawar',
        'phone' => '017XXXXXXXX',
        'email' => 'aniqa.nawar@example.com'
    ],
    [
        'name' => 'Aurpon Sharma',
        'phone' => '019YYYYYYYY',
        'email' => 'aurpon234@example.com'
    ],
    [
        'name' => 'Rahabar Islam',
        'phone' => '018ZZZZZZZZ',
        'email' => 'rahabarzzz@example.com'
    ],
    [
        'name' => 'Alice Johnson',
        'phone' => '013ZZZZZZZZ',
        'email' => 'alice_j@example.com'
    ],
    [
        'name' => 'Abdur Rahman',
        'phone' => '019ZZZZZZZZ',
        'email' => 'arahman@example.com'
    ],
    [
        'name' => 'Zarin Tasnim',
        'phone' => '017ZZZZZZZZ',
        'email' => 'zarin@example.com'
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .contacts-section {
            margin: 40px auto;
            width: 80%;
        }
        .contact-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .contact-card strong {
            display: inline-block;
            width: 80px;
        }
    </style>
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
    <section class="contacts-section">
        <?php if (empty($contacts)): ?>
            <p>No contacts available.</p>
        <?php else: ?>
            <?php foreach ($contacts as $contact): ?>
                <div class="contact-card">
                    <p><strong>Name:</strong> <?= htmlspecialchars($contact['name']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($contact['phone']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($contact['email']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; 2025 Blood Bank System | All Rights Reserved</p>
    </footer>
</body>
</html>
