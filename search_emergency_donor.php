<?php
session_start();
include('db_connect.php'); // Include database connection
$website_role = $_SESSION['website_role'] ?? null;

// Handle search filters
$blood_grp = isset($_GET['blood_grp']) ? $_GET['blood_grp'] : '';
$rh_factor = isset($_GET['rh_factor']) ? $_GET['rh_factor'] : '';
$address = isset($_GET['address']) ? $_GET['address'] : '';

//  SQL query filters
$where = ["is_emergency_donor = 'yes'"]; // Filter emergency donors
$params = [];
$types = '';

if ($blood_grp !== '') {
    $where[] = "blood_group = ?";
    $params[] = $blood_grp;
    $types .= 's';
}
if ($rh_factor !== '') {
    $where[] = "rh_factor = ?";
    $params[] = $rh_factor;
    $types .= 's';
}
if ($address !== '') {
    $where[] = "address LIKE ?";
    $params[] = "%$address%";
    $types .= 's';
}
$where_sql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

$sql = "SELECT user_id, name, blood_group, rh_factor, address, phone, email FROM user $where_sql ORDER BY name";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// unique blood groups and Rh factors  dropdowns
$blood_groups = [];
$rh_factors = [];
$bg_res = $conn->query("SELECT DISTINCT blood_group FROM user WHERE is_emergency_donor = 'yes'");
while ($row = $bg_res->fetch_assoc()) $blood_groups[] = $row['blood_group'];
$rh_res = $conn->query("SELECT DISTINCT rh_factor FROM user WHERE is_emergency_donor = 'yes'");
while ($row = $rh_res->fetch_assoc()) $rh_factors[] = $row['rh_factor'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Emergency Donor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Emergency Donor Search</h1>
    <p>Find emergency donors quickly based on your criteria.</p>
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
    <h2>Search for Emergency Donors</h2>
    <form  method="get">
        <select name="blood_grp">
            <option value="">All Blood Groups</option>
            <?php foreach ($blood_groups as $bg): ?>
                <option value="<?= htmlspecialchars($bg) ?>" <?= $bg == $blood_grp ? 'selected' : '' ?>><?= htmlspecialchars($bg) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="rh_factor">
            <option value="">All Rh Factors</option>
            <?php foreach ($rh_factors as $rh): ?>
                <option value="<?= htmlspecialchars($rh) ?>" <?= $rh == $rh_factor ? 'selected' : '' ?>><?= htmlspecialchars($rh) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="address" placeholder="Enter Address" value="<?= htmlspecialchars($address) ?>">
        <button type="submit">Search</button>
    </form>
    <table style="margin: 20px auto; width: 80%;">
        <tr>
            <th>Sl</th>
            <th>Name</th>
            <th>Blood Group</th>
            <th>Rh Factor</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Email</th>
        </tr>
        <?php
        $i = 1;
        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['blood_group']) ?></td>
            <td><?= htmlspecialchars($row['rh_factor']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($i == 1): ?>
        <tr><td colspan="7">No emergency donors found for the given criteria.</td></tr>
        <?php endif; ?>
    </table>
</section>
<footer>
    <p>&copy; 2025 Blood Bank System | All Rights Reserved</p>
</footer>
</body>
</html>