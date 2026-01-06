<?php
// Detect environment automatically
$isLocal = ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);

if ($isLocal) {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "store_price_ledger_db";
} else {
    $host = "sql304.infinityfree.com";
    $username = "if0_40836256";
    $password = "kdwFlcSrDnmSsE";
    $database = "if0_40836256_store_price_ledger_db";
}

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
