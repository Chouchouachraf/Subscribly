<?php
require_once __DIR__ . '/../config/database.php';

// Create tables if they don't exist
function initializeDatabase() {
    $db = getDBConnection();
    
    try {
        // Read and execute the SQL setup file
        $sql = file_get_contents(__DIR__ . '/../database/setup.sql');
        $db->exec($sql);
    } catch(PDOException $e) {
        die("Error initializing database: " . $e->getMessage());
    }
}

// Initialize the database tables
initializeDatabase();
?>
