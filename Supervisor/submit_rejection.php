<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Debugging: Output POST data
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Use 'trainingid' as the key
    $trainingid = $_POST['trainingid'] ?? null;
    $remarks = $_POST['remarks'] ?? null;

    if (empty($trainingid) || empty($remarks)) {
        echo "Debug: trainingid = " . htmlspecialchars($trainingid) . ", remarks = " . htmlspecialchars($remarks);
        die("Error: Missing training ID or remarks.");
    }

    try {
        $query = "UPDATE training SET remarks = :remarks, status = 'Rejected' WHERE trainingid = :trainingid";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':remarks', $remarks, PDO::PARAM_STR);
        $stmt->bindValue(':trainingid', $trainingid, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: trainingrequestdashboard.php");
            exit;
        } else {
            die("Error updating record.");
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>