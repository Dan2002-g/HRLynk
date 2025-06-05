<?php
include('connection.php');

header('Content-Type: application/json');

try {
    // Get the selected year from the query parameter
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

    // Fetch the user names grouped by their submission status for the selected year
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
    ) AS statuses
    LEFT JOIN (
        SELECT 
            CASE 
                WHEN status LIKE 'To be reviewed%' THEN 'to_be_reviewed'
                WHEN status LIKE 'Approved%' THEN 'approved'
                WHEN status LIKE 'Rejected%' THEN 'rejected'
                ELSE 'submitted'
            END AS status_key,
            userID
        FROM idp
        WHERE YEAR(created_at) = :year
    ) AS data ON statuses.status_key = data.status_key
    LEFT JOIN users u ON data.userID = u.userID
    GROUP BY statuses.status_key
    ");
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Adjust the "submitted" status to include all employees
    $submittedEmployees = $conn->prepare("
        SELECT 
            GROUP_CONCAT(DISTINCT u.empname) AS employees,
            COUNT(DISTINCT u.empname) AS count
        FROM idp
        LEFT JOIN users u ON idp.userID = u.userID
        WHERE YEAR(idp.created_at) = :year
    ");
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