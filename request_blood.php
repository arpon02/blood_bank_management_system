<?php
session_start();
include('db_connect.php');
$website_role = $_SESSION['website_role'] ?? null; // 'admin', 'user', or null
$user_id = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Request Blood</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>Request for Blood</h1>
    <p>Fill in the details and we’ll try to connect you with a donor</p>
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
    <form action="request_blood.php" method="post">
      <input type="text" name="patient_name" placeholder="Patient's Name" required><br><br>
      <input type="text" name="blood_group" placeholder="Required Blood Group" required><br><br>
	  <input type="text" name="rh_factor" placeholder="rh_factor" required><br><br>
      <input type="text" name="phone" placeholder="phone" required><br><br>
      <button type="submit" class="btn-outline">Submit Request</button>
    </form>
  </section>

  <footer>
    <p>&copy; 2025 Blood Bank System | All Rights Reserved</p>
  </footer>

<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $patient_name = $_POST['patient_name'];
    $blood_group = $_POST['blood_group'];
    $rh_factor = $_POST['rh_factor'];
    $phone = $_POST['phone'];
    $request_date = date('Y-m-d'); // Today date

   
    $stmt = $conn->prepare("INSERT INTO request_blood (blood_group, rh_factor, name, phone, request_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $blood_group, $rh_factor, $patient_name, $phone, $request_date);

    if ($stmt->execute()) {
        echo "<script>alert('Request Submitted Successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

</body>
</html>
