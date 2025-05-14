<?php
session_start();
include('db_connect.php');
$website_role = $_SESSION['website_role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

//Only allow admin 
if (!isset($_SESSION['website_role']) || $_SESSION['website_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

//  Delete
if (isset($_POST['delete_id'])) {
    $del_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM records WHERE record_id = ?");
    $stmt->bind_param('i', $del_id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF'] . (empty($_SERVER['QUERY_STRING']) ? "" : "?".$_SERVER['QUERY_STRING']));
    exit;
}

//  filters 
$where = [];
$params = [];
$types = '';

if (!empty($_GET['record_id'])) {
    $where[] = "record_id = ?";
    $params[] = intval($_GET['record_id']);
    $types .= 'i';
}
if (!empty($_GET['user_id'])) {
    $where[] = "user_id = ?";
    $params[] = intval($_GET['user_id']);
    $types .= 'i';
}
if (!empty($_GET['status'])) {
    $where[] = "status = ?";
    $params[] = $_GET['status'];
    $types .= 's';
}
if (!empty($_GET['date'])) {
    $where[] = "date = ?";
    $params[] = $_GET['date'];
    $types .= 's';
}
if (!empty($_GET['receiver_id'])) {
    $where[] = "receiver_id = ?";
    $params[] = intval($_GET['receiver_id']);
    $types .= 'i';
}
if (!empty($_GET['receive_date'])) {
    $where[] = "receive_date = ?";
    $params[] = $_GET['receive_date'];
    $types .= 's';
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

//query
$sql = "SELECT record_id, user_id, role, status, date, receiver_id, receive_date FROM records $where_sql ORDER BY record_id ASC";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Records</title>
    <link rel="stylesheet" href="style.css" />
     <style>
    table {
        width: 95%;
        border-collapse: collapse;
        margin: 24px auto;
    }
    th, td {
        border: 1px solid #aaa;
        padding: 8px 12px;
        text-align: center;
    }
    th {
        background: #e7e7e7;
    }
    tr:nth-child(even) {
        background: #f9f9f9;
    }
    tr:nth-child(odd) {
        background: #fff;
    }
    .filter-form {
        width:95%;
        margin: 24px auto 0 auto;
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
        justify-content: center;
        background: #f8f8f8;
        padding: 14px 0 10px 0;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
    .filter-form input,
    .filter-form select {
        padding: 6px 8px;
        border-radius: 4px;
        border: 1px solid #bbb;
        font-size: 1rem;
    }
    .filter-form label {
        margin-right: 4px;
        font-weight: 500;
    }
    .filter-form button {
        padding: 7px 16px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        margin-left: 8px;
    }
    .delete-btn {
        background: #e74c3c;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        cursor: pointer;
        font-weight: bold;
        transition: background 0.2s;
    }
    .delete-btn:hover {
        background: #c0392b;
    }
    </style>
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

    <h2 style="text-align:center">All Donation/Receive Records</h2>
    <form method="get" class="filter-form">
        <label>Record ID: <input type="number" name="record_id" value="<?php echo htmlspecialchars($_GET['record_id'] ?? ''); ?>" style="width:80px"></label>
        <label>User ID: <input type="number" name="user_id" value="<?php echo htmlspecialchars($_GET['user_id'] ?? ''); ?>" style="width:80px"></label>
        <label>Status:
            <select name="status">
                <option value="">--All--</option>
                <option value="available"<?php if(($_GET['status'] ?? '')==='available') echo ' selected'; ?>>available</option>
                <option value="unavailable"<?php if(($_GET['status'] ?? '')==='unavailable') echo ' selected'; ?>>unavailable</option>
            </select>
        </label>
        <label>Date: <input type="date" name="date" value="<?php echo htmlspecialchars($_GET['date'] ?? ''); ?>"></label>
        <label>Receiver ID: <input type="number" name="receiver_id" value="<?php echo htmlspecialchars($_GET['receiver_id'] ?? ''); ?>" style="width:80px"></label>
        <label>Receive Date: <input type="date" name="receive_date" value="<?php echo htmlspecialchars($_GET['receive_date'] ?? ''); ?>"></label>
        <button type="submit">Filter</button>
        <a href="Records.php" style="margin-left:10px;color:#e74c3c;font-weight:bold;text-decoration:underline;">Reset</a>
    </form>
    <table>
        <tr>
            <th>Delete</th>
            <th>Record ID</th>
            <th>User ID</th>
            <th>Role</th>
            <th>Status</th>
            <th>Date</th>
            <th>Receiver ID</th>
            <th>Receive Date</th>
        </tr>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <form method="post" onsubmit="return confirm('Delete this record?');" style="margin:0;">
                        <input type="hidden" name="delete_id" value="<?php echo $row['record_id']; ?>">
                        <button type="submit" class="delete-btn" title="Delete">&#128465; Delete</button>
                    </form>
                </td>
                <td><?php echo htmlspecialchars($row['record_id']); ?></td>
                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td><?php echo $row['receiver_id'] !== null ? htmlspecialchars($row['receiver_id']) : 'NULL'; ?></td>
                <td><?php echo $row['receive_date'] !== null ? htmlspecialchars($row['receive_date']) : 'NULL'; ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No records found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>