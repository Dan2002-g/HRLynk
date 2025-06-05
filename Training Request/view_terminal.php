<?php
include('connection.php');

if (isset($_GET['trainingid'])) {
    $trainingid = (int) $_GET['trainingid']; // Ensure it's an integer

    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare("SELECT * FROM terminal WHERE trainingid = :trainingid");
        $stmt->bindValue(':trainingid', $trainingid, PDO::PARAM_INT);
        $stmt->execute();
        $terminal = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($terminal) {
            echo "<h5 class='mb-3'>Terminal Report Details</h5>";
            echo "<p><strong>Title:</strong> " . htmlspecialchars($terminal['title']) . "</p>";
            echo "<p><strong>Sponsor:</strong> " . htmlspecialchars($terminal['sponsor']) . "</p>";
            echo "<p><strong>From:</strong> " . htmlspecialchars($terminal['fromdate']) . " <strong>To:</strong> " . htmlspecialchars($terminal['todate']) . "</p>";
            echo "<p><strong>Days:</strong> " . htmlspecialchars($terminal['days']) . " | <strong>Hours:</strong> " . htmlspecialchars($terminal['hours']) . "</p>";
            echo "<h6 class='mt-3'>Brief Report:</h6><p>" . nl2br(htmlspecialchars($terminal['briefreport'])) . "</p>";
            echo "<h6 class='mt-3'>Synthesis:</h6><p>" . nl2br(htmlspecialchars($terminal['synthesis'])) . "</p>";
        } else {
            echo "<p class='text-danger'>No terminal report found for this training ID.</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='text-danger'>Error fetching data: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p class='text-danger'>Invalid request. Training ID is missing.</p>";
}
?>
