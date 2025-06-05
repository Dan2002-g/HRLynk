<?php
require_once 'connection.php';

$year = $_GET['year'] ?? date('Y');
$office = $_GET['office'] ?? 'all';

try {
    $query = "SELECT 
                  u.empname AS employee_name, 
                  ud.position AS employee_position, 
                  COALESCE(SUM(t.hours), 0) AS total_hours
              FROM 
                  users u
              LEFT JOIN 
                  user_details ud ON u.userID = ud.userID
              LEFT JOIN 
                  terminal t ON u.userID = t.userID AND YEAR(t.fromdate) = :year";

    if ($office !== 'all') {
        $query .= " WHERE ud.officeID = :office";
    }

    $query .= " GROUP BY u.userID, ud.position";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);

    if ($office !== 'all') {
        $stmt->bindParam(':office', $office, PDO::PARAM_INT);
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>