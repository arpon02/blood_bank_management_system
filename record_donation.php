<?php
session_start();
include('db_connect.php');
$website_role = $_SESSION['website_role'] ?? null;
$message="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);

    // Step 1: Get user's blood group and rh factor
    $stmt = $conn->prepare("SELECT blood_group, rh_factor FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($blood_group, $rh_factor);
    if ($stmt->fetch()) {
        $stmt->close();

        // Step 2: Update blood_stock table
        $stock = $conn->prepare("SELECT qty FROM blood_stock WHERE blood_grp = ? AND rh = ?");
        $stock->bind_param("ss", $blood_group, $rh_factor);
        $stock->execute();
        $stock->bind_result($qty);
        if ($stock->fetch()) {
            $stock->close();
            $update = $conn->prepare("UPDATE blood_stock SET qty = qty + 1 WHERE blood_grp = ? AND rh = ?");
            $update->bind_param("ss", $blood_group, $rh_factor);
            $update->execute();
            $update->close();
        } else {
            $stock->close();
            $insert = $conn->prepare("INSERT INTO blood_stock (blood_grp, rh, qty) VALUES (?, ?, 1)");
            $insert->bind_param("ss", $blood_group, $rh_factor);
            $insert->execute();
            $insert->close();
        }

        // Step 3: Insert into records
        $today = date('Y-m-d');
        $status = "available";
        $role = "donation";
        $insert_rec = $conn->prepare("INSERT INTO records (user_id, status, role, date) VALUES (?, ?, ?, ?)");
        $insert_rec->bind_param("isss", $user_id, $status, $role, $today);
        $rec_success = $insert_rec->execute();
        $insert_rec->close();

        // Step 4: Update last_donated in user table
        $update_last = $conn->prepare("UPDATE user SET last_donated = ? WHERE user_id = ?");
        $update_last->bind_param("si", $today, $user_id);
        $update_last->execute();
        $update_last->close();

        if ($rec_success) {
            $message = "Donation recorded,Blood stock increased,last donation date saved & records added!";
        } else {
            $message = "Failed to record donation: " . $insert_rec->error;
        }

    } else {
        $message = "User not found with that ID.";
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donate Blood</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Blood Donation</h1>
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
    <h2>Enter User ID to Record Donation</h2>
    <form method="POST" action="record_donation.php">
        <input type="number" class="search-bar" name="user_id" placeholder="Enter User's ID" required>
        <br><br>
        <button type="submit" class="btn">Donated</button>
    </form>
    
</section>
<?php if ($message): ?>
    <div style="width:100%;text-align:center;margin-bottom:10px;">
        <p style="color: green;font-weight:bold;"><?php echo htmlspecialchars($message); ?></p>
    </div>
<?php endif; ?>
<footer>
    <p>&copy; 2025 Blood Bank System | All Rights Reserved</p>
</footer>
</body>
</html>