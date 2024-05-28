<?php
$host = 'localhost:3310';
$db_username = 'root';
$db_password = '';
$db_name = 'KLINIKA';

$conn = new mysqli($host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


