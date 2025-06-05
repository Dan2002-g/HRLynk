<?php
include('connection.php');
header('Content-Type: application/json');

// Decode the incoming JSON payload
$data = json_decode(file_get_contents('php://input'), true);

// Debugging: Log the received data
file_put_contents('php://stderr', "Received data: " . print_r($data, true) . "\n");

// Validate the input
if (isset($data['id']) && isset($data['remarks'])) {
    $id = $data['id'];
    $remarks = $data['remarks'];

    try {
        // Update the remarks in the database
        $query = "UPDATE competency SET remarks = :remarks WHERE competency_id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':remarks', $remarks, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Remarks saved successfully!']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to execute query.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}
?>