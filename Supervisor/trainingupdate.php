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

    // Get the training ID from the form
    $trainingid = $_POST['trainingid'];
    
    // Check if the training ID is present
    if (!empty($trainingid)) {
        $sql = "UPDATE `training` SET `status` = :status WHERE `trainingid` = :trainingid";
        $stmt = $conn->prepare($sql);
        
        // Execute the statement
        if ($stmt->execute([':status' => $status, ':trainingid' => $trainingid])) {
            header("Location: trainingrequestdashboard.php");
        } else {
            echo "Failed to update the record.";
        }
    } else {
        echo "No training ID provided.";
    }
}
?>