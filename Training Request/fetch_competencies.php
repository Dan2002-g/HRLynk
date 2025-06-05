<?php
include('trainingfunction.php'); // Include database connection

$userID = $_SESSION['user']; // Get the logged-in user ID

try {
    $sql = "SELECT DISTINCT competency.competencyname, idp_competencies.priority_no 
            FROM competency 
            JOIN idp_competencies ON competency.competency_id = idp_competencies.competency_id 
            JOIN idp ON idp_competencies.idp_id = idp.id
            LEFT JOIN training ON idp_competencies.competency_id = training.idpcompetency AND training.status = 'Approved'
            WHERE idp_competencies.userID = :userID 
              AND idp.status = 'Approved'
              AND (training.idpcompetency IS NULL OR training.status != 'Approved')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $competencies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($competencies as $competency) {
            echo "<option value='{$competency['competencyname']}' data-priority='{$competency['priority_no']}'>{$competency['competencyname']}</option>";
        }
    } else {
        echo "<option value=''>No Approved Competencies Available</option>";
    }
} catch (PDOException $e) {
    echo "<option value=''>Error loading competencies: " . htmlspecialchars($e->getMessage()) . "</option>";
}
?>