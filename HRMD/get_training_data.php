<?php
include('connection.php');

header('Content-Type: application/json');

try {
    // Get the selected year and office from the query parameters
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
    $office = isset($_GET['office']) ? $_GET['office'] : 'all';

    // Base query to fetch the count of submitted, approved, to be reviewed, and rejected training requests
    $query = "
        SELECT 
            COUNT(*) AS submitted,
            SUM(CASE WHEN status = 'To be reviewed.' THEN 1 ELSE 0 END) AS to_be_reviewed,
            SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS approved,
            SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS rejected,
            SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed
        FROM training
        JOIN user_details ON training.userID = user_details.userID
    ";

    // Add a condition for filtering by office if an office is selected
    $params = [];
    if ($office !== 'all') {
        $query .= " WHERE user_details.officeID = :office AND YEAR(training.submitted_at) = :year";
        $params[':office'] = $office;
        $params[':year'] = $year;
    } else {
        $query .= " WHERE YEAR(training.submitted_at) = :year";
        $params[':year'] = $year;
    }

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the data as JSON
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>