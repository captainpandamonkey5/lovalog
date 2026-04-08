<?php
$isLocal = ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
$isRender = getenv('RENDER') === 'true';

if ($isLocal) {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "store_price_ledger_db";
    $port = 3306;
} elseif ($isRender) {
    $host = getenv('DB_HOST');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');
    $database = getenv('DB_NAME');
    $port = (int) getenv('DB_PORT');
} else {
    // request for env file
    $host = "DB_HOST";
    $username = "DB_USER";
    $password = "DB_PASS";
    $database = "DB_NAME";
    $port = 'DB_PORT';
}
$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
