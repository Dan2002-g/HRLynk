<?php
// Assuming a database connection is set up
include 'db_connection.php'; // Replace with your actual DB connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rejectionId = $_POST['rejection_id'];  // The ID of the record being rejected
    $remarks = $_POST['remarks'];  // The rejection remarks

    // Sanitize and validate inputs
    if (empty($rejectionId) || empty($remarks)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
        exit;
    }

    // Prepare and execute the SQL statement
    $query = "UPDATE your_table_name SET rejection_remarks = ?, status = 'Rejected' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $remarks, $rejectionId);  // "si" means string for remarks and integer for ID

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    }
}
?>