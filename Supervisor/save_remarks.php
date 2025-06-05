<?php

include('connection.php');

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    foreach ($data as $row) {
        $query = "UPDATE competency SET remarks = :remarks WHERE competencyname = :competency";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':remarks', $row['remarks']);
        $stmt->bindParam(':competency', $row['competency']);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}