<?php
// =================================================================
// DATABASE CONNECTION
// =================================================================

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root'); 
define('DB_NAME', 'project_psi_db'); // <<< GANTI NAMA DATABASE JIKA PERLU

// Create a new database connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    // If there's an error, stop the script and display the error message
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to utf8mb4 for full Unicode support
$conn->set_charset("utf8mb4");

// echo "Koneksi berhasil"; // Bisa di-uncomment untuk tes koneksi
?> 