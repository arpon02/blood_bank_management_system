<?php
session_start();
include('db_connect.php');
$website_role = $_SESSION['website_role'] ?? null; // 'admin', 'user', or null
$user_id = $_SESSION['user_id'] ?? null;
$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name']);
    $blood_group = trim($_POST['blood_group']);
    $rh_factor = trim($_POST['rh_factor']);
	$is_emergency_donor =trim($_POST['is_emergency_donor']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
	$password = trim($_POST['password']);
	


    // Check if email already exists
    $check = $conn->prepare("SELECT user_id FROM user WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "A user with this email already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO user (name, blood_group, rh_factor,is_emergency_donor, address, phone, email,password) VALUES (?, ?, ?, ?, ?, ?,?,?)");
        $stmt->bind_param("ssssssss", $name, $blood_group, $rh_factor,$is_emergency_donor ,$address, $phone, $email,$password);

        if ($stmt->execute()) {
            $message = "Registration successful!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Register a User</h1>
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
    <form action="register.php" method="POST">
        <input type="text"  name="name" placeholder="Full Name" required><br><br>
		
		
        <input type="text"  name="blood_group" placeholder="Blood Group (e.g. A, O, B, AB)" required><br><br>
        <input type="text"  name="rh_factor" placeholder="Rh Factor (+ or -)" required><br><br>
		<input type="text" name="is_emergency_donor" placeholder="Emergency donor ? (yes or no)" required><br><br>
		
        <input type="text"  name="address" placeholder="Address" required><br><br>
        <input type="text"  name="phone" placeholder="Phone" required><br><br>
        <input type="email"  name="email" placeholder="Email" required><br><br>
		<input type="text"  name="password" placeholder="password" required><br><br>
        <button type="submit" class="btn-outline">Register</button>
    </form>
    <?php if ($message): ?>
        <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
</section>
<footer>
    <p>&copy; 2025 Blood Bank System | All Rights Reserved</p>
</footer>
</body>
</html>