<?php
include('connection.php');

header('Content-Type: application/json');

try {
    // Get the selected year from the query parameter
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

    // Fetch the count of each competency selection for the selected year
    $stmt = $conn->prepare("
        SELECT 
            c.competencyname AS name,
            COUNT(ic.competency_id) AS count
        FROM idp_competencies ic
        JOIN competency c ON ic.competency_id = c.competency_id
        JOIN idp i ON ic.idp_id = i.id
        WHERE YEAR(i.created_at) = :year
        GROUP BY ic.competency_id
    ");
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->execute();
    $competencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data as JSON
    echo json_encode(['competencies' => $competencies]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>