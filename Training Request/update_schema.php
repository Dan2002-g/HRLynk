<?php
include('connection.php');

try {
    // Drop the existing learningapp_id column if it exists
    $sql = "ALTER TABLE learningapplication DROP COLUMN learningapp_id";
    $conn->exec($sql);

    // Add the learningapp_id column as AUTO_INCREMENT
    $sql = "ALTER TABLE learningapplication ADD COLUMN learningapp_id INT AUTO_INCREMENT PRIMARY KEY";
    $conn->exec($sql);

    echo "Column learningapp_id added successfully as AUTO_INCREMENT.";
} catch (PDOException $e) {
    echo "Error modifying column: " . $e->getMessage();
}
?>
