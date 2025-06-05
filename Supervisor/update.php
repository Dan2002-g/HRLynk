<?php
require 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
        $status = $_POST['status'] ?? null;
        $remarks = $_POST['remarks'] ?? '';

        $validStatuses = ['Approved', 'Rejected'];
        if (!$id || !$status || !in_array($status, $validStatuses)) {
            throw new Exception('Missing or invalid parameters');
        }

        $sql = "UPDATE idp SET status = :status, remarks = :remarks WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $params = [
            ':status' => $status,
            ':remarks' => $remarks,
            ':id' => $id
        ];

        if ($stmt->execute($params)) {
            echo json_encode([
                'success' => true,
                'message' => $status === 'Approved' ? 'IDP has been approved.' : 'IDP has been rejected.'
            ]);
        } else {
            throw new Exception('Failed to update the record');
        }
    } catch (Exception $e) {
        error_log('Error in update.php: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}