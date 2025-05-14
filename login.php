<?php
session_start();
include('db_connect.php');

$website_role = $_GET['website_role'] ?? ''; // 'admin' or 'user'
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $website_role = $_POST['website_role'] ?? $role;

    if ($username && $password && $website_role) {
        if ($website_role === 'admin') {
            $stmt = $conn->prepare("SELECT name FROM admin_list WHERE name = ? AND password = ?");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $_SESSION['username'] = $username;
                $_SESSION['website_role'] = 'admin';
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid admin credentials.";
            }
            $stmt->close();
        } elseif ($website_role === 'user') {
            $stmt = $conn->prepare("SELECT user_id, name FROM user WHERE name = ? AND password = ?");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($user_id, $name);
                $stmt->fetch();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $name;
                $_SESSION['website_role'] = 'user';
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid user credentials.";
            }
            $stmt->close();
        } else {
            $error = "Invalid role selected.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Blood Bank | Login</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .full-page-center {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 70vh;
    }
    .login-container {
      text-align: center;
      background: #fafafa;
      padding: 30px;
      border: 1px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
    }
    .login-container form label {
      display: block;
      margin: 12px 0;
    }
    .login-container button {
      margin-top: 10px;
      padding: 6px 16px;
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
  </nav>

  <div class="full-page-center">
    <div class="login-container">
      <h2><?php echo ($website_role === 'admin') ? 'Admin' : 'User'; ?> Login</h2>

      <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>

      <?php if ($website_role === 'admin' || $website_role === 'user'): ?>
        <form method="post" action="login.php?website_role=<?php echo $website_role; ?>">
          <input type="hidden" name="website_role" value="<?php echo $website_role; ?>">
          <label>Username: <input type="text" name="username" required></label>
          <label>Password: <input type="password" name="password" required></label>
          <button type="submit">Login</button>
        </form>
      <?php else: ?>
        <p>Please select login type from the homepage.</p>
      <?php endif; ?>

      <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
  </div>
</body>
</html>
