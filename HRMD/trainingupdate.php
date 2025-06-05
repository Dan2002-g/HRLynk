<?php
session_start();
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trainingid = intval($_POST['trainingid']);

    if (isset($_POST['approve'])) {
        $prevEmployNum = isset($_POST['prevEmployNum']) ? intval($_POST['prevEmployNum']) : 0;

        // Debugging output
        echo "Debug: Prev No. of Employees Attended: " . $prevEmployNum . "<br>";
        echo "Debug: Training ID: " . $trainingid . "<br>";

        // Update the training status to 'Approved' and save the updated 'Prev No. of Employees Attended'
        $stmt = $conn->prepare("UPDATE training SET status = 'Approved', prevEmployNum = :prevEmployNum WHERE trainingid = :trainingid");
        $stmt->bindParam(':prevEmployNum', $prevEmployNum, PDO::PARAM_INT);
        $stmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['message'] = array("text" => "Training request approved successfully.", "alert" => "success");
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Error: " . $errorInfo[2]; // Output the error message
            exit();
        }
    } elseif (isset($_POST['reject'])) {
        $remarks = filter_input(INPUT_POST, 'remarks', FILTER_SANITIZE_STRING);

        // Update the training status to 'Rejected' and save the remarks
        $stmt = $conn->prepare("UPDATE training SET status = 'Rejected', remarks = :remarks WHERE trainingid = :trainingid");
        $stmt->bindParam(':remarks', $remarks, PDO::PARAM_STR);
        $stmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['message'] = array("text" => "Training request rejected with remarks.", "alert" => "success");
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Error: " . $errorInfo[2]; // Output the error message
            exit();
        }
    }

    // Redirect back to the dashboard
    header('Location: trainingrequestdashboard.php');
    exit();
}
?>