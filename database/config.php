<?php

$host = "localhost";
$db_name = "php_api";
$user = "root";
$pass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: ". $e->getMessage();
    exit();
}

?>