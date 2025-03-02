<?php
$host = 'localhost';
$username = 'root';
$password = 'mysql';
$dbname = 'myshop_db';

// Создание соединения
$conn = new mysqli($host, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
