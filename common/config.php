<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "root";
$db_name = "gamers_life_db";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database Connection Failure: " . $conn->connect_error);
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>