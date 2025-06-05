<?php
include('connection.php');

header('Content-Type: application/json');

try {
    // Get the selected year and office from the query parameters
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
    $office = isset($_GET['office']) ? $_GET['office'] : 'all';

    // Base query to fetch the count of each competency selection for the selected year
    $query = "
        SELECT 
            c.competencyname AS name,
            COUNT(ic.competency_id) AS count
        FROM idp_competencies ic
        JOIN competency c ON ic.competency_id = c.competency_id
        JOIN idp i ON ic.idp_id = i.id
        JOIN user_details ud ON i.userID = ud.userID
    ";

    // Add a condition for filtering by office if an office is selected
    $params = [];
    if ($office !== 'all') {
        $query .= " WHERE ud.officeID = :office AND YEAR(i.created_at) = :year";
        $params[':office'] = $office;
        $params[':year'] = $year;
    } else {
        $query .= " WHERE YEAR(i.created_at) = :year";
        $params[':year'] = $year;
    }

    $query .= " GROUP BY ic.competency_id";

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $competencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data as JSON
    echo json_encode(['competencies' => $competencies]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>