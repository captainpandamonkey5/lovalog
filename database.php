<?php
$conn = new mysqli("localhost", "root", "", "store_price_ledger_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
