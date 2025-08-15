<?php
// Database connection
$host = '172.31.22.43';
$dbname = 'blog_admin';
$username = 'Patrick200626972';
$password = 'hJQ58RlEST';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>