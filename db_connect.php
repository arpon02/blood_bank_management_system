<?php
$host = "localhost"; 
$username = "root";
$password = ""; 
$dbname = "test_bloodbank"; 


$conn = new mysqli($host, $username, $password);
if ($conn->connect_error) {
    die("Connection Failed: ". $conn->connect_error);
}else{
    //echo "Connection Established";
    mysqli_select_db($conn, $dbname);
}
?>