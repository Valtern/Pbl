<?php
require_once 'connection.php';

if ($koneksi) {
    echo "Database connection successful!";
} else {
    echo "Failed to connect to the database.";
}
?>
