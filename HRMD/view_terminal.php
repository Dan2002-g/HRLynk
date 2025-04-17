<?php
include('connection.php');

if (isset($_GET['trainingid'])) {
    $trainingid = $_GET['trainingid'];

    // Fetch terminal report details
    $stmt = $conn->prepare("SELECT * FROM terminal WHERE trainingid = :trainingid");
    $stmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);
    $stmt->execute();
    $terminal = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($terminal) {
        echo "<div style='padding: 10px;'>";
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<thead>";
        echo "<tr style='border: 1px solid black;'>";
        echo "<th style='border: 1px solid black; padding: 8px;'>Title</th>";
        echo "<th style='border: 1px solid black; padding: 8px;'>Sponsor</th>";
        echo "<th style='border: 1px solid black; padding: 8px;'>From Date</th>";
        echo "<th style='border: 1px solid black; padding: 8px;'>To Date</th>";
        echo "<th style='border: 1px solid black; padding: 8px;'>Days</th>";
        echo "<th style='border: 1px solid black; padding: 8px;'>Hours</th>";
        echo "<th style='border: 1px solid black; padding: 8px;'>Brief Report</th>";
        echo "<th style='border: 1px solid black; padding: 8px;'>Synthesis</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        echo "<tr style='border: 1px solid black;'>";
        echo "<td style='border: 1px solid black; padding: 8px;'>" . htmlspecialchars($terminal['title']) . "</td>";
        echo "<td style='border: 1px solid black; padding: 8px;'>" . htmlspecialchars($terminal['sponsor']) . "</td>";
        echo "<td style='border: 1px solid black; padding: 8px;'>" . htmlspecialchars($terminal['fromdate']) . "</td>";
        echo "<td style='border: 1px solid black; padding: 8px;'>" . htmlspecialchars($terminal['todate']) . "</td>";
        echo "<td style='border: 1px solid black; padding: 8px;'>" . htmlspecialchars($terminal['days']) . "</td>";
        echo "<td style='border: 1px solid black; padding: 8px;'>" . htmlspecialchars($terminal['hours']) . "</td>";
        echo "<td style='border: 1px solid black; padding: 8px;'>" . htmlspecialchars($terminal['briefreport']) . "</td>";
        echo "<td style='border: 1px solid black; padding: 8px;'>" . htmlspecialchars($terminal['synthesis']) . "</td>";
        echo "</tr>";
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>No terminal report found for this training ID.</p>";
    }
} else {
    echo "<p>Invalid request. Training ID is missing.</p>";
}
?>