<?php
session_start();
$website_role = $_SESSION['website_role'] ?? null; // 'admin', 'user', or null

if (!isset($_SESSION['website_role']) || $_SESSION['website_role'] !== 'admin') {
    header('Location: login.php?website_role=admin');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_user_id = intval($_POST['user_id'] ?? 0);
    if ($search_user_id > 0) {
        $_SESSION['user_id'] = $search_user_id;
        header('Location: user_dashboard.php');
        exit;
    } else {
        $error = "Please enter a valid User ID.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search User</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
    <h1>Admin: Search User</h1>
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
    <h2>Enter User ID to View Dashboard</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post">
        <input class="search-bar" type="number" name="user_id" placeholder="User ID" required>
        <button type="submit" class="btn">Go to Dashboard</button>
    </form>
</section>
<footer>
    <p>&copy; 2025 Blood Bank System | All Rights Reserved</p>
</footer>
</body>
</html>