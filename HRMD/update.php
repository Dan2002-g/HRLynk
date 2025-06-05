<?php
require 'connection.php'; // Your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the "approve" or "reject" button was clicked
    if (isset($_POST['approve'])) {
        $status = 'Approved';
    } elseif (isset($_POST['reject'])) {
        $status = 'Rejected';
    } else {
        exit('Invalid action');
    }

    // Get the ID from the form
    $id = $_POST['id'];
    
    // Check if the ID is present
    if (!empty($id)) {
        $sql = "UPDATE `idp` SET `status` = :status WHERE `id` = :id";
        $stmt = $conn->prepare($sql);
        
        // Execute the statement
        if ($stmt->execute([':status' => $status, ':id' => $id])) {
            header("Location: idpdashboard.php");
        } else {
            echo "Failed to update the record.";
        }
    } else {
        echo "No ID provided.";
    }
}
?>
