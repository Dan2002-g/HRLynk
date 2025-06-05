<?php
include('connection.php');

header('Content-Type: application/json');

try {
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

    // Fetch the user names grouped by their training status for the selected year
    $stmt = $conn->prepare("
        SELECT 
            statuses.status_key,
            COALESCE(GROUP_CONCAT(DISTINCT u.empname), '') AS employees,
            COALESCE(COUNT(DISTINCT u.empname), 0) AS count
        FROM (
            SELECT 'submitted' AS status_key
            UNION ALL SELECT 'to_be_reviewed'
            UNION ALL SELECT 'approved'
            UNION ALL SELECT 'rejected'
            UNION ALL SELECT 'completed'
        ) AS statuses
        LEFT JOIN (
            SELECT 
                CASE 
                    WHEN LOWER(t.status) LIKE 'to be reviewed%' THEN 'to_be_reviewed'
                    WHEN LOWER(t.status) LIKE 'approved%' THEN 'approved'
                    WHEN LOWER(t.status) LIKE 'rejected%' THEN 'rejected'
                    WHEN LOWER(t.status) LIKE 'completed%' THEN 'completed'
                    ELSE 'submitted'
                END AS status_key,
                t.userID
            FROM training t
            WHERE YEAR(t.submitted_at) = :year
        ) AS data ON statuses.status_key = data.status_key
        LEFT JOIN users u ON data.userID = u.userID
        GROUP BY statuses.status_key
    ");
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Adjust the "submitted" status to include all employees
    $submittedEmployeesQuery = "
        SELECT 
            GROUP_CONCAT(DISTINCT u.empname) AS employees,
            COUNT(DISTINCT u.empname) AS count
        FROM training t
        LEFT JOIN users u ON t.userID = u.userID
        WHERE YEAR(t.submitted_at) = :year
    ";
    $submittedEmployees = $conn->prepare($submittedEmployeesQuery);
    $submittedEmployees->bindParam(':year', $year, PDO::PARAM_INT);
    $submittedEmployees->execute();
    $submittedResult = $submittedEmployees->fetch(PDO::FETCH_ASSOC);

    // Update the "submitted" status in the result
    foreach ($result as &$row) {
        if ($row['status_key'] === 'submitted') {
            $row['employees'] = $submittedResult['employees'];
            $row['count'] = (int)$submittedResult['count'];
        }
    }

    // Directly output the data for debugging
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>