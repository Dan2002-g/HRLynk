<?php
include('connection.php');

header('Content-Type: application/json');

try {
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
    $office = isset($_GET['office']) ? htmlspecialchars($_GET['office']) : 'all';

    // Query to fetch employee names and their number of completeds training
    $query = "
        SELECT 
            u.empname AS employee_name,
            COUNT(CASE WHEN t.status = 'Completed' THEN 1 ELSE NULL END) AS completed_training_count
        FROM users u
        LEFT JOIN training t ON u.userID = t.userID 
            AND YEAR(t.trainingdate) = :year
        LEFT JOIN user_details ud ON u.userID = ud.userID
    ";

    // Add office filter if applicable
    $conditions = [];
    if ($office !== 'all') {
        $conditions[] = "ud.officeID = :office";
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    $query .= " GROUP BY u.userID, u.empname ORDER BY completed_training_count DESC, u.empname ASC";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    if ($office !== 'all') {
        $stmt->bindParam(':office', $office, PDO::PARAM_STR);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
} catch (Exception $e) {
    error_log("Error in get_training_report.php: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>