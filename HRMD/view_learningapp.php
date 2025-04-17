<?php
session_start();
// Include database connection
include 'connection.php';

if (!isset($_SESSION['user'])) {
    // User is not authenticated, redirect to login page or display an error message
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

$userID = $_SESSION['user'];

if (isset($_GET['trainingid'])) {
    $trainingid = $_GET['trainingid'];

    // Fetch data from the learningapplication table for the given training ID
    $learningAppQuery = "SELECT * FROM learningapplication WHERE trainingid = :trainingid";
    $learningAppStmt = $conn->prepare($learningAppQuery);
    $learningAppStmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);
    $learningAppStmt->execute();
    $learningAppResult = $learningAppStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Fetch data from the learningapplication table for the logged-in user
    $learningAppQuery = "SELECT * FROM learningapplication WHERE userID = :userID";
    $learningAppStmt = $conn->prepare($learningAppQuery);
    $learningAppStmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $learningAppStmt->execute();
    $learningAppResult = $learningAppStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Documents</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

   
    <table>
        <thead>
            <tr>
                <th>Function</th>
                <th>Activity</th>
                <th>Period</th>
                <th>Resource Needed</th>
                <th>Monitoring Evaluation</th>
                <th>Attachments</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($learningAppResult)): ?>
                <?php foreach ($learningAppResult as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['function']); ?></td>
                        <td><?= htmlspecialchars($row['activity']); ?></td>
                        <td><?= htmlspecialchars($row['period']); ?></td>
                        <td><?= htmlspecialchars($row['resource_needed']); ?></td>
                        <td><?= htmlspecialchars($row['moneval']); ?></td>
                        <td>
                            <?php if (!empty($row['file_path'])): ?>
                                <?php 
                                // Split file paths into an array if multiple files are present
                                $filePaths = explode(',', $row['file_path']); 
                                foreach ($filePaths as $filePath): 
                                    $filePath = trim($filePath);
                                    $fileName = basename($filePath);
                                ?>
                                    <a href="/HRLynk/Terminal Report/documents/<?=$row['userID'];?>/<?= htmlspecialchars($filePath); ?>" target="_blank">
                                        <?= htmlspecialchars($fileName); ?>
                                    </a><br>
                                <?php endforeach; ?>
                            <?php else: ?>
                                No file
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No data available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>