<?php

include('connection.php');

header('Content-Type: application/json');

try {
    $query = "
    SELECT 
        c.competency_id, -- Include competency_id
        c.competencyname AS competency,
        IFNULL(GROUP_CONCAT(DISTINCT ic.structured_learning ORDER BY ic.structured_learning ASC SEPARATOR ', '), 'No Trainings') AS trainings,
        COUNT(DISTINCT CASE WHEN ic.fromdate = 'Q1' THEN ic.userID END) AS q1_participants,
        COUNT(DISTINCT CASE WHEN ic.fromdate = 'Q2' THEN ic.userID END) AS q2_participants,
        COUNT(DISTINCT CASE WHEN ic.fromdate = 'Q3' THEN ic.userID END) AS q3_participants,
        COUNT(DISTINCT CASE WHEN ic.fromdate = 'Q4' THEN ic.userID END) AS q4_participants,
        (SELECT GROUP_CONCAT(DISTINCT u.empname SEPARATOR ', ') 
         FROM idp_competencies ic_sub 
         LEFT JOIN users u ON ic_sub.userID = u.userID 
         WHERE ic_sub.competency_id = c.competency_id AND ic_sub.fromdate = 'Q1') AS q1_participant_names,
        (SELECT GROUP_CONCAT(DISTINCT u.empname SEPARATOR ', ') 
         FROM idp_competencies ic_sub 
         LEFT JOIN users u ON ic_sub.userID = u.userID 
         WHERE ic_sub.competency_id = c.competency_id AND ic_sub.fromdate = 'Q2') AS q2_participant_names,
        (SELECT GROUP_CONCAT(DISTINCT u.empname SEPARATOR ', ') 
         FROM idp_competencies ic_sub 
         LEFT JOIN users u ON ic_sub.userID = u.userID 
         WHERE ic_sub.competency_id = c.competency_id AND ic_sub.fromdate = 'Q3') AS q3_participant_names,
        (SELECT GROUP_CONCAT(DISTINCT u.empname SEPARATOR ', ') 
         FROM idp_competencies ic_sub 
         LEFT JOIN users u ON ic_sub.userID = u.userID 
         WHERE ic_sub.competency_id = c.competency_id AND ic_sub.fromdate = 'Q4') AS q4_participant_names,
        SUM(CASE WHEN ic.fromdate = 'Q1' THEN ic.estimated_budget END) AS q1_budget,
        SUM(CASE WHEN ic.fromdate = 'Q2' THEN ic.estimated_budget END) AS q2_budget,
        SUM(CASE WHEN ic.fromdate = 'Q3' THEN ic.estimated_budget END) AS q3_budget,
        SUM(CASE WHEN ic.fromdate = 'Q4' THEN ic.estimated_budget END) AS q4_budget,
        SUM(ic.estimated_budget) AS total_budget,
        c.remarks
    FROM competency c
    LEFT JOIN idp_competencies ic ON ic.competency_id = c.competency_id
    GROUP BY c.competency_id, c.competencyname -- Group by competency_id
    ORDER BY 
        CASE 
            WHEN GROUP_CONCAT(DISTINCT ic.structured_learning) IS NULL THEN 1
            WHEN GROUP_CONCAT(DISTINCT ic.structured_learning) = 'No Trainings' THEN 1
            ELSE 0
        END ASC,
        trainings ASC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate the grand total of estimated budgets
    $grandTotal = array_sum(array_column($result, 'total_budget'));

    // Append the grand total to the response
    $response = [
        'data' => $result,
        'footer' => [
            'label' => 'Grand Total of Estimated Budgets',
            'value' => 'Php: ' . number_format($grandTotal, 2)
        ]
    ];
    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>