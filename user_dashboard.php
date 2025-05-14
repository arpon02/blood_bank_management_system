<?php
session_start();
include('db_connect.php');
$website_role = $_SESSION['website_role'] ?? null; // 'admin', 'user', or null

// Make sure user is logged in.
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['website_role']) ||
    !in_array($_SESSION['website_role'], ['admin', 'user'])
) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Emergency Donor Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_emergency'])) {
    $new_val = ($_POST['current_val'] === 'yes') ? 'no' : 'yes';
    $stmt = $conn->prepare("UPDATE user SET is_emergency_donor = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_val, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: user_dashboard.php");
    exit;
}

// Get last donated date from user table
$stmt = $conn->prepare("SELECT last_donated, name, blood_group, rh_factor, is_emergency_donor, address, phone, email FROM user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($last_donated, $name, $blood_group, $rh_factor, $is_emergency_donor, $address, $phone, $email);
$stmt->fetch();
$stmt->close();

// Get total donation count from records
$stmt = $conn->prepare("SELECT COUNT(*) FROM records WHERE user_id = ? AND role = 'donation'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total_donation);
$stmt->fetch();
$stmt->close();

// Get total number of donations received by this user
$stmt = $conn->prepare("SELECT COUNT(*) FROM records WHERE receiver_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total_donations_received);
$stmt->fetch();
$stmt->close();

// Get all donation dates
$stmt = $conn->prepare("SELECT date FROM records WHERE user_id = ? AND role = 'donation' ORDER BY date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($donation_date);
$donation_dates = [];
while ($stmt->fetch()) {
    $donation_dates[] = $donation_date;
}
$stmt->close();

// Get all received dates
$stmt = $conn->prepare("SELECT receive_date FROM records WHERE receiver_id = ? AND receive_date IS NOT NULL ORDER BY receive_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($received_date);
$received_dates = [];
while ($stmt->fetch()) {
    $received_dates[] = $received_date;
}
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css" />
    <style>
 
 
    </style>
</head>
<body>
<?php if ($website_role  === 'admin'): ?>
 <header>
    <h1>Dashboard of User ID : <?php echo $user_id ?></h1>
 </header>
<?php else: ?> 
 <header>
    <h1>Welcome to Your Dashboard</h1>
 </header> 
<?php endif; ?> 
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
	  <a href="admin_dashboard.php">Show Requests</a>

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
<div class="dashboard-container">
    <div class="dashboard-left">
        <h2 style="text-align:center;">Summary</h2>
        <table class="dashboard-table">
            <tr>
                <th>Total Donations</th>
                <td><?php echo $total_donation ?: 0; ?></td>
            </tr>
            <tr>
                <th>Total Received</th>
                <td><?php echo $total_donations_received ?: 0; ?></td>
            </tr>
            <tr>
                <th>Last Donated Date</th>
                <td><?php echo $last_donated ?: 'N/A'; ?></td>
            </tr>
        </table>
    </div>
	
    <div class="dashboard-middle">
        <h2>Donations & Receives</h2>
        <table class="dashboard-table">
            <tr>
                <th>Donation Dates</th>
                <th>Received Dates</th>
            </tr>
            <?php
            $max_rows = max(count($donation_dates), count($received_dates));
            for ($i = 0; $i < $max_rows; $i++): ?>
            <tr>
                <td><?php echo isset($donation_dates[$i]) ? htmlspecialchars($donation_dates[$i]) : ''; ?></td>
                <td><?php echo isset($received_dates[$i]) ? htmlspecialchars($received_dates[$i]) : ''; ?></td>
            </tr>
            <?php endfor; ?>
        </table>
    </div>
	 
    <div class="dashboard-right">
      <h2 style="text-align:center;">Profile</h2>
      <div class="profile-details">
        <p><strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($blood_group); ?></p>
        <p><strong>Rh Factor:</strong> <?php echo htmlspecialchars($rh_factor); ?></p>
        <p class="emergency-row">
            <strong>Emergency Donor:</strong>
            <form method="post" style="display:flex;align-items:center;gap:8px;margin:0;">
                <span><?php echo htmlspecialchars($is_emergency_donor ? ucfirst($is_emergency_donor) : 'No'); ?></span>
                <input type="hidden" name="toggle_emergency" value="1">
                <input type="hidden" name="current_val" value="<?php echo htmlspecialchars(strtolower($is_emergency_donor)); ?>">
                <button class="emergency-toggle-btn" type="submit">
                    Set <?php echo (strtolower($is_emergency_donor) === 'yes') ? 'No' : 'Yes'; ?>
                </button>
            </form>
        </p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
      </div>	
    </div>
</div>
<footer>
    <p>&copy; 2025 Blood Bank System | All Rights Reserved</p>
</footer>
</body>
</html>