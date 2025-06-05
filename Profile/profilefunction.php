<?php
session_start();
require_once 'connection.php';

if (isset($_POST['user_details'])) {
    if (!isset($_SESSION['user'])) {
        $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
        header('location: ../Login/index.php');
        exit();
    }

    if (
        !empty($_POST['officeID']) && !empty($_POST['position']) &&
        !empty($_POST['jobdescription']) && !empty($_POST['employmentstatus']) &&
        !empty($_POST['datehired']) && !empty($_POST['monthsintheposition']) &&
        !empty($_POST['yearsiniit'])
    ) {
        // Sanitize form inputs
        $officeID = htmlspecialchars($_POST['officeID']);
        $position = htmlspecialchars($_POST['position']);
        $jobdescription = htmlspecialchars($_POST['jobdescription']);
        $employmentstatus = htmlspecialchars($_POST['employmentstatus']);
        $datehired = htmlspecialchars($_POST['datehired']);
        $monthsintheposition = htmlspecialchars($_POST['monthsintheposition']);
        $yearsiniit = htmlspecialchars($_POST['yearsiniit']);
        $userID = $_SESSION['user'];

        // Handle profile picture upload
        $profilePicture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $uploadDir = 'uploads/';
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['profile_picture']['type'];

            if (in_array($fileType, $allowedTypes)) {
                $fileName = $userID . "_" . basename($_FILES['profile_picture']['name']);
                $targetFilePath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
                    $profilePicture = $fileName; // Save only the file name
                }
            }
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM `user_details` WHERE `userID` = ?");
            $stmt->execute([$userID]);
            $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userDetails) {
                $sql = "UPDATE `user_details` SET `officeID`=?, `position`=?, `jobdescription`=?, `employmentstatus`=?, `datehired`=?, `monthsintheposition`=?, `yearsiniit`=?, `profile_picture`=? WHERE `userID`=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    $officeID, $position, $jobdescription, $employmentstatus, $datehired,
                    $monthsintheposition, $yearsiniit, $profilePicture ?? $userDetails['profile_picture'], $userID
                ]);
            } else {
                $sql = "INSERT INTO `user_details` (`UserDetailsID`, `userID`, `officeID`, `position`, `jobdescription`, `employmentstatus`, `datehired`, `monthsintheposition`, `yearsiniit`, `profile_picture`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$userID, $officeID, $position, $jobdescription, $employmentstatus, $datehired, $monthsintheposition, $yearsiniit, $profilePicture]);
            }

            $_SESSION['message'] = array("text" => "Profile information saved successfully!", "alert" => "info");
            header('location: profiledisplay.php');
            exit();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $_SESSION['message'] = array("text" => "An error occurred while saving your profile information. Please try again later.", "alert" => "danger");
            header('location: Profile.php');
            exit();
        }
    } else {
        $_SESSION['message'] = array("text" => "Please fill up the required fields, including the office!", "alert" => "danger");
        header('location: Profile.php');
        exit();
    }
}
?>
