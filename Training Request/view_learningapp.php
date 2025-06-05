<?php
include('connection.php');

if (isset($_GET['trainingid'])) {
    $trainingid = $_GET['trainingid'];

    // Fetch learning application plan details
    $stmt = $conn->prepare("SELECT * FROM learningapplication WHERE trainingid = :trainingid");
    $stmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);
    $stmt->execute();
    $learningapp = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($learningapp) {
        echo "<h5>Learning Application Plan Details</h5>";
        echo "<p><strong>Function:</strong> " . htmlspecialchars($learningapp['function']) . "</p>";
        echo "<p><strong>Activity:</strong> " . htmlspecialchars($learningapp['activity']) . "</p>";
        echo "<p><strong>Period:</strong> " . htmlspecialchars($learningapp['period']) . "</p>";
        echo "<p><strong>Resource Needed:</strong> " . htmlspecialchars($learningapp['resource_needed']) . "</p>";
        echo "<p><strong>Monitoring and Evaluation:</strong> " . htmlspecialchars($learningapp['moneval']) . "</p>";
        echo "<p><strong>Attachments:</strong> " . htmlspecialchars($learningapp['file_path']) . "</p>";
    } else {
        echo "<p>No learning application plan found for this training ID.</p>";
    }
} else {
    echo "<p>Invalid request. Training ID is missing.</p>";
}
?>