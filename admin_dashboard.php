<?php
session_start();
include('db_connect.php');

// Check if the user is an admin
$website_role = $_SESSION['website_role'] ?? null;

if (!isset($_SESSION['website_role']) || $_SESSION['website_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $del_stmt = $conn->prepare("DELETE FROM request_blood WHERE request_id = ?");
    $del_stmt->bind_param('i', $delete_id);
    $del_stmt->execute();
    $del_stmt->close();
    // Redirect to avoid resubmission
    header("Location: admin_dashboard.php?" . http_build_query(array_diff_key($_GET, ['delete_id' => ''])));
    exit;
}

// Collect filter values from GET
$request_id = $_GET['request_id'] ?? '';
$blood_group = $_GET['blood_group'] ?? '';
$rh_factor = $_GET['rh_factor'] ?? '';
$name = $_GET['name'] ?? '';
$phone = $_GET['phone'] ?? '';
$request_date = $_GET['request_date'] ?? '';

// Build SQL dynamically with filters
$sql = "SELECT * FROM request_blood WHERE 1=1";
$params = [];
$types = "";

// Add conditions for each filter if present
if ($request_id !== '') {
    $sql .= " AND request_id LIKE ?";
    $params[] = "%$request_id%";
    $types .= "s";
}
if ($blood_group !== '') {
    $sql .= " AND blood_group LIKE ?";
    $params[] = "%$blood_group%";
    $types .= "s";
}
if ($rh_factor !== '') {
    $sql .= " AND rh_factor LIKE ?";
    $params[] = "%$rh_factor%";
    $types .= "s";
}
if ($name !== '') {
    $sql .= " AND name LIKE ?";
    $params[] = "%$name%";
    $types .= "s";
}
if ($phone !== '') {
    $sql .= " AND phone LIKE ?";
    $params[] = "%$phone%";
    $types .= "s";
}
if ($request_date !== '') {
    $sql .= " AND request_date = ?";
    $params[] = $request_date;
    $types .= "s";
}

$sql .= " ORDER BY request_id ASC";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css" />
    <title>Admin Dashboard - Blood Requests</title>
    <style>
        table { border-collapse: collapse; width: 80%; margin: auto; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
        th { background: #e0e0e0; }
        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #f1f1f1; }
        .search-box { text-align: center; margin-bottom: 20px; }
        .search-box input, .search-box select { margin: 2px; padding: 5px; }
        .delete-btn { color: #fff; background: #c00; border: none; padding: 4px 10px; border-radius: 3px; cursor: pointer; }
    </style>
    <script>
        function confirmDelete(request_id) {
            if (confirm('Are you sure you want to delete this record?')) {
                // Preserve filters in the URL
                let params = new URLSearchParams(window.location.search);
                params.set('delete_id', request_id);
                window.location.href = 'admin_dashboard.php?' + params.toString();
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Admin Dashboard - Blood Bank Management System</h1>
        <p>Manage all blood requests here.</p>
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
                <a href="search_user.php">Search User</a>
                <a href="register.php">Register User</a>
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </nav>

    <h2 style="text-align:center;">Blood Requests Table</h2>
    <div class="search-box">
        <form method="get">
            <input type="text" name="request_id" placeholder="Request ID" value="<?php echo htmlspecialchars($request_id); ?>">
            <select name="blood_group">
                <option value="">Blood Group</option>
                <option value="A" <?php if($blood_group=="A") echo 'selected'; ?>>A</option>
                <option value="B" <?php if($blood_group=="B") echo 'selected'; ?>>B</option>
                <option value="AB" <?php if($blood_group=="AB") echo 'selected'; ?>>AB</option>
                <option value="O" <?php if($blood_group=="O") echo 'selected'; ?>>O</option>
            </select>
            <select name="rh_factor">
                <option value="">Rh Factor</option>
                <option value="+" <?php if($rh_factor=="+") echo 'selected'; ?>>+</option>
                <option value="-" <?php if($rh_factor=="-") echo 'selected'; ?>>-</option>
            </select>
            <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>">
            <input type="text" name="phone" placeholder="Phone" value="<?php echo htmlspecialchars($phone); ?>">
            <input type="date" name="request_date" value="<?php echo htmlspecialchars($request_date); ?>">
            <button type="submit">Filter</button>
            <a href="admin_dashboard.php" style="margin-left:8px;">Reset</a>
        </form>
    </div>
    <table>
        <tr>
            <th>Request ID</th>
            <th>Blood Group</th>
            <th>Rh Factor</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Request Date</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['request_id']}</td>
                    <td>{$row['blood_group']}</td>
                    <td>{$row['rh_factor']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['request_date']}</td>
                    <td>
                        <button class='delete-btn' onclick='confirmDelete({$row['request_id']});'>Delete</button>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center;'>No records found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>