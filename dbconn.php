<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "lms"; 
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// // Database connection settings from ProFreeHost
// $servername = "sql200.infinityfree.com";  // MySQL Hostname
// $username = "if0_38709850";  // MySQL Username
// $password = "PottnxwyKVdgf";  // MySQL Password
// $dbname = "if0_38709850_lms";  // MySQL Database Name (replace XXX with your actual database name)

// // Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// // Check connection
// if ($conn->connect_error) {
//    die("Connection failed: " . $conn->connect_error);
// } else {
//    echo "";
// }
?>