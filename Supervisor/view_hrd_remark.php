<?php
include('connection.php');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['remarks'])) {
    $id = $data['id'];
    $remarks = $data['remarks'];

    try {
        $query = "UPDATE competency SET remarks = :remarks WHERE competency_id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':remarks', $remarks);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}
?>