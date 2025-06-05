<?php
session_start();
include('connection.php');

// Check if the user is logged in and the userID is set in the session
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('Location: ../Login/index.php');
    exit();
}

$userID = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $trainingID = $_POST['trainingid'] ?? null;
    $type = isset($_POST['type']) ? implode(", ", $_POST['type']) : '';
    $others_specify = $_POST['others_specify'] ?? '';
    $title = $_POST['title'] ?? '';
    $sponsor = $_POST['sponsor'] ?? '';
    $fromdate = $_POST['fromdate'] ?? '';
    $todate = $_POST['todate'] ?? '';
    $days = $_POST['days'] ?? '';
    $hours = $_POST['hours'] ?? '';
    $venue = $_POST['venue'] ?? '';
    $objectives = $_POST['objectives'] ?? '';
    $briefreport = $_POST['briefreport'] ?? '';
    $synthesis = $_POST['synthesis'] ?? '';

    // Check for required fields
    if (empty($title) || empty($sponsor) || empty($fromdate) || empty($todate) || 
        empty($venue) || empty($objectives) || empty($briefreport) || empty($synthesis)) {
        $_SESSION['message'] = array(
            "text" => "All fields are required.",
            "alert" => "danger"
        );
        header('Location: terminalform.php?trainingid=' . $trainingID);
        exit();
    }

    try {
        // Insert data into the terminal_reports table
        $sql = "INSERT INTO terminal (
            userID, 
            trainingid,
            type,
            others_specify,
            title,
            sponsor,
            fromdate,
            todate,
            days,
            hours,
            venue,
            objectives,
            briefreport,
            synthesis,
            submission_date
        ) VALUES (
            :userID, 
            :trainingID,
            :type,
            :others_specify,
            :title,
            :sponsor,
            :fromdate,
            :todate,
            :days,
            :hours,
            :venue,
            :objectives,
            :briefreport,
            :synthesis,
            NOW()
        )";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':trainingID', $trainingID, PDO::PARAM_INT);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':others_specify', $others_specify);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':sponsor', $sponsor);
        $stmt->bindParam(':fromdate', $fromdate);
        $stmt->bindParam(':todate', $todate);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->bindParam(':hours', $hours, PDO::PARAM_INT);
        $stmt->bindParam(':venue', $venue);
        $stmt->bindParam(':objectives', $objectives);
        $stmt->bindParam(':briefreport', $briefreport);
        $stmt->bindParam(':synthesis', $synthesis);
        
        if ($stmt->execute()) {
            // Update training status to completed
            $updateSql = "UPDATE training_requests SET status = 'Completed' WHERE id = :trainingID";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(':trainingID', $trainingID, PDO::PARAM_INT);
            if ($updateStmt->execute()) {
            $_SESSION['message'] = array(
                "text" => "Terminal Report submitted successfully!",
                "alert" => "success"
            );
            header('Location: ../Training Request/trainingdashboard.php');
            exit();
        } else {
            throw new Exception("Failed to update training status");
        }
    } else {
        throw new Exception("Failed to save terminal report");
    }

} catch (PDOException $e) {
    $_SESSION['message'] = array(
        "text" => "Database Error: " . $e->getMessage(),
        "alert" => "danger"
    );
    header('Location: ../Training Request/trainingdashboard.php');
    exit();
}
}
?>