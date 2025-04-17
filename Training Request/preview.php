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

    // Fetch data from the terminal table for the given training ID
    $terminalQuery = "SELECT * FROM terminal WHERE trainingid = :trainingid";
    $terminalStmt = $conn->prepare($terminalQuery);
    $terminalStmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);
    $terminalStmt->execute();
    $terminalResult = $terminalStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Fetch data from the terminal table for the logged-in user
    $terminalQuery = "SELECT * FROM terminal WHERE userID = :userID";
    $terminalStmt = $conn->prepare($terminalQuery);
    $terminalStmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $terminalStmt->execute();
    $terminalResult = $terminalStmt->fetchAll(PDO::FETCH_ASSOC);
}

// Define the base folder for file paths
// Adjust this path based on your folder structure
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

    <h2>Terminal Report</h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Sponsor</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Days</th>
                <th>Hours</th>
                <th>Brief Report</th>
                <th>Synthesis</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($terminalResult)): ?>
                <?php foreach ($terminalResult as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= htmlspecialchars($row['sponsor']); ?></td>
                        <td><?= htmlspecialchars($row['fromdate']); ?></td>
                        <td><?= htmlspecialchars($row['todate']); ?></td>
                        <td><?= htmlspecialchars($row['days']); ?></td>
                        <td><?= htmlspecialchars($row['hours']); ?></td>
                        <td><?= htmlspecialchars($row['briefreport']); ?></td>
                        <td><?= htmlspecialchars($row['synthesis']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No data available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
