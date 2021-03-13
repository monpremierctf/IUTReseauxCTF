<h1>Hello !</h1>
<h4>Attempting MySQL connection from php...</h4>
<?php

//system('netstat -ln');
$host = 'localhost';
$user = 'root';
$pass = 'root';
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected to MySQL successfully!";
?>
