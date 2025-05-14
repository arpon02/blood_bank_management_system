<?php
session_start();
include('db_connect.php');
$website_role = $_SESSION['website_role'] ?? null;
if (!isset($_SESSION['website_role']) || $_SESSION['website_role'] !== 'admin') {
    header('Location: login.php?website_role=admin');
exit;}
function get_blood_compatibility($bg, $rh) {
    $compat = [
        'A+'  => [['A', '+'], ['A', '-'], ['O', '+'], ['O', '-']],
        'A-'  => [['A', '-'], ['O', '-']],
        'B+'  => [['B', '+'], ['B', '-'], ['O', '+'], ['O', '-']],
        'B-'  => [['B', '-'], ['O', '-']],
        'AB+' => [['A', '+'], ['A', '-'], ['B', '+'], ['B', '-'], ['AB', '+'], ['AB', '-'], ['O', '+'], ['O', '-']],
        'AB-' => [['AB', '-'], ['A', '-'], ['B', '-'], ['O', '-']],
        'O+'  => [['O', '+'], ['O', '-']],
        'O-'  => [['O', '-']],
    ];
    $key = $bg . $rh; // e.g., "A+"
    return $compat[$key] ?? [];
}

// Handle AJAX: Get compatible records for a given receiver user_id
if (isset($_GET['receiver_user_id'])) {
    $receiver_id = intval($_GET['receiver_user_id']);
    $usr = $conn->prepare("SELECT blood_group, rh_factor FROM user WHERE user_id=?");
    $usr->bind_param("i", $receiver_id);
    $usr->execute();
    $usr->bind_result($bg, $rh);
    if ($usr->fetch()) {
        $usr->close();
        $compat = get_blood_compatibility($bg, $rh);
        if ($compat) {
            // Build WHERE clause for compatible blood
            $where = [];
            $params = [];
            $types = '';
            foreach ($compat as $pair) {
                $where[] = "(u.blood_group=? AND u.rh_factor=?)";
                $params[] = $pair[0];
                $params[] = $pair[1];
                $types .= 'ss';
            }
			$sql = "SELECT r.record_id, r.user_id, r.status, r.date, u.blood_group, u.rh_factor
                    FROM records r
					JOIN user u ON r.user_id = u.user_id
					WHERE r.status='available' AND (" . implode(' OR ', $where) . ")
					ORDER BY r.date ASC";		
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) $rows[] = $row;
            echo json_encode(['success'=>true, 'records'=>$rows]);
            exit;
        }
    }
    echo json_encode(['success'=>false, 'records'=>[]]);
    exit;
}

// Handle AJAX: Mark as Received
if (isset($_POST['mark_as_received'])) {
    $record_id = intval($_POST['record_id']);
    $receiver_id = intval($_POST['receiver_id']);
    $today = date('Y-m-d');
    // Get blood info for this record
    $stmt = $conn->prepare("SELECT u.blood_group, u.rh_factor FROM records r JOIN user u ON r.user_id=u.user_id WHERE r.record_id=?");
    $stmt->bind_param("i", $record_id);
    $stmt->execute();
    $stmt->bind_result($bg, $rh);
    if ($stmt->fetch()) {
        $stmt->close();
        // Update records table
        $upd = $conn->prepare("UPDATE records SET status='unavailable', receiver_id=?, receive_date=? WHERE record_id=?");
        $upd->bind_param("isi", $receiver_id, $today, $record_id);
        $upd->execute();
        $upd->close();
        // Update blood_stock
        $stock = $conn->prepare("UPDATE blood_stock SET qty = qty - 1 WHERE blood_grp=? AND rh=? AND qty > 0");
        $stock->bind_param("ss", $bg, $rh);
        $stock->execute();
        $stock->close();
        echo json_encode(['success'=>true]);
        exit;
    }
    echo json_encode(['success'=>false]);
    exit;
}

// Normal page load: show all records, with search by record_id or date
$where = [];
$params = [];
$types = '';

if (isset($_GET['search_record_id']) && $_GET['search_record_id'] != '') {
    $where[] = "record_id = ?";
    $params[] = intval($_GET['search_record_id']);
    $types .= 'i';
}
if (isset($_GET['search_date']) && $_GET['search_date'] != '') {
    $where[] = "date = ?";
    $params[] = $_GET['search_date'];
    $types .= 's';
}
$where_sql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

$sql = "SELECT * FROM records $where_sql ORDER BY record_id DESC";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$records = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Record Blood Received</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .dashboard-container2 {
    display: flex;
    justify-content: center;      /* Center horizontally */
    align-items: flex-start;      /* Align items to the top */
    width: 100%;                  /* Take full width of the page */
    min-height: 30vh;
    margin-top: 20px;

}
.dashboard-mark_received {
    border: 1px solid #ccc;
    padding: 32px 36px;
    border-radius: 12px;
    background: #fafafa;
    box-shadow: 0 2px 8px #eee;
    text-align: center;       /* Center content inside the box */
    min-width: 340px;
}
    .search-boxes { display:flex; gap:16px; margin-bottom:12px; }
    .side-box { border:1px solid #ccc; padding:16px; border-radius:8px; }
    </style>
</head>
<body>
<header>
    <h1>Record Blood Received</h1>
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
<div class="dashboard-container2">
    <div class="dashboard-mark_received ">
        <h3 style="text-align: center;">Find Blood For Receiver</h3>

        <input type="number" id="receiver_user_id" placeholder="Receiver User ID">
        <div id="compatible-records"></div>
    </div>
</div>
<script>
document.getElementById('receiver_user_id').addEventListener('input', function() {
    let uid = this.value;
    let box = document.getElementById('compatible-records');
    box.innerHTML = 'Loading...';
    if (uid.length < 1) { box.innerHTML=''; return; }
    fetch('record_received.php?receiver_user_id=' + encodeURIComponent(uid))
    .then(res => res.json())
    .then(data => {
        if (data.success && data.records.length) {
            let html = `<table class="dashboard-table"><tr>
                 <th>Record ID</th><th>Donor User ID</th><th>Blood Group</th><th>Rh</th><th>Donation Date</th><th>Days Old</th><th>Action</th>
            </tr>`;
            for(let r of data.records) {
				// Calculate days old
				let donationDate = new Date(r.date);
				let today = new Date();
				// Zero out the time parts for accuracy
				donationDate.setHours(0,0,0,0);
				today.setHours(0,0,0,0);
				let daysOld = Math.floor((today - donationDate) / (1000 * 60 * 60 * 24));
				html += `<tr>
					<td>${r.record_id}</td>
					<td>${r.user_id}</td>
					<td>${r.blood_group}</td>
					<td>${r.rh_factor}</td>
					<td>${r.date}</td>
					<td>${daysOld}</td>
					<td>
						<button onclick="markReceived(${r.record_id}, ${uid})">Mark as Received</button>
					</td>
            </tr>`;
}
            html += '</table>';
            box.innerHTML = html;
        } else {
            box.innerHTML = 'No compatible available donations for this user.';
        }
    });
});
function markReceived(record_id, receiver_id) {
    if (!confirm('Mark record ' + record_id + ' as received by user ' + receiver_id + '?')) return;
    fetch('record_received.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'mark_as_received=1&record_id='+encodeURIComponent(record_id)+'&receiver_id='+encodeURIComponent(receiver_id)
    }).then(res=>res.json()).then(data=>{
        if (data.success) {
            alert('Marked as received!');
            document.getElementById('receiver_user_id').dispatchEvent(new Event('input'));
        } else alert('Failed to update');
    });
}
</script>
<footer>
    <p>&copy; 2025 Blood Bank System | All Rights Reserved</p>
</footer>
</body>
</html>