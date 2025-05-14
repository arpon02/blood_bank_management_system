<?php
session_start();
include('db_connect.php');
$website_role = $_SESSION['website_role'] ?? null;

// Handle search filters
$blood_grp = isset($_GET['blood_grp']) ? $_GET['blood_grp'] : '';
$rh = isset($_GET['rh']) ? $_GET['rh'] : '';

// Prepare filter query
$where = [];
$params = [];
$types = '';
if ($blood_grp !== '') {
    $where[] = "blood_grp = ?";
    $params[] = $blood_grp;
    $types .= 's';
}
if ($rh !== '') {
    $where[] = "rh = ?";
    $params[] = $rh;
    $types .= 's';
}
$where_sql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

$sql = "SELECT id, blood_grp, rh, qty FROM blood_stock $where_sql ORDER BY blood_grp, rh";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// For dropdowns
$blood_groups = [];
$rh_factors = [];
$bg_res = $conn->query("SELECT DISTINCT blood_grp FROM blood_stock");
while ($row = $bg_res->fetch_assoc()) $blood_groups[] = $row['blood_grp'];
$rh_res = $conn->query("SELECT DISTINCT rh FROM blood_stock");
while ($row = $rh_res->fetch_assoc()) $rh_factors[] = $row['rh'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blood Stock Search</title>
    <link rel="stylesheet" href="style.css" />
	
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
	  <a href="admin_dashboard.php">Show Requests</a>

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
        <h2>Blood Stock Search</h2>
        <form  method="get">
            <select name="blood_grp">
                <option value="">All Blood Groups</option>
                <?php foreach ($blood_groups as $bg): ?>
                    <option value="<?= htmlspecialchars($bg) ?>" <?= $bg == $blood_grp ? 'selected' : '' ?>><?= htmlspecialchars($bg) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="rh">
                <option value="">All Rh</option>
                <?php foreach ($rh_factors as $r): ?>
                    <option value="<?= htmlspecialchars($r) ?>" <?= $r == $rh ? 'selected' : '' ?>><?= htmlspecialchars($r) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Search</button>
        </form>
        <table style="margin: 5px auto ; width: 50%;">
            <tr>
                <th>Sl</th>
                <th>Blood Group</th>
                <th>Rh</th>
                <th>Quantity</th>
            </tr>
            <?php
            $i = 1;
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['blood_grp']) ?></td>
                <td><?= htmlspecialchars($row['rh']) ?></td>
                <td><?= htmlspecialchars($row['qty']) ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if ($i == 1): ?>
            <tr><td colspan="4">No records found.</td></tr>
            <?php endif; ?>
        </table>
    </section>
    <footer>
        <p>&copy; 2025 Blood Bank System | All Rights Reserved</p>
    </footer>
</body>
</html>