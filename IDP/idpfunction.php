<?php
session_start();
include('connection.php');

$userID = $_SESSION['user'];
$stmt = $conn->prepare("SELECT * FROM users WHERE userID = ?");
$stmt->execute([$userID]);
$userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

// Check if the user already has an approved IDP for the current year
$currentYear = date('Y');
$checkStmt = $conn->prepare("
    SELECT * FROM idp 
    WHERE userID = :userID 
    AND status = 'Approved' 
    AND YEAR(created_at) = :currentYear
");
$checkStmt->bindParam(':userID', $userID);
$checkStmt->bindParam(':currentYear', $currentYear);
$checkStmt->execute();
$existingIDP = $checkStmt->fetch(PDO::FETCH_ASSOC);

if ($existingIDP) {
    $_SESSION['message'] = array(
        "text" => "You already have an approved IDP for this year. You cannot submit another one.",
        "alert" => "danger"
    );
    header('location: idpdashboard.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction(); // Start transaction

        $objectives = isset($_POST['objectives']) ? implode(", ", $_POST['objectives']) : 'No objective selected';
        $other_objective = !empty($_POST['other_objective']) ? $_POST['other_objective'] : 'No other objective specified';

        // Insert into the IDP table
        $stmt = $conn->prepare("INSERT INTO idp (userID, objectives, other_objective) VALUES (:userID, :objectives, :other_objective)");
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':objectives', $objectives);
        $stmt->bindParam(':other_objective', $other_objective);
        $stmt->execute();

        // Retrieve last inserted IDP ID
        $idp_id = $conn->lastInsertId();

        // Prepare the competency insertion query
        $stmt = $conn->prepare("INSERT INTO idp_competencies 
            (idp_id, userID, competency_id, priority_no, workplace_learning, social_learning, structured_learning, resources_needed, accomplishment_indicator, fromdate, todate, estimated_budget) 
            VALUES 
            (:idp_id, :userID, :competency_id, :priority_no, :workplace_learning, :social_learning, :structured_learning, :resources_needed, :accomplishment_indicator, :fromdate, :todate, :estimated_budget)");

        // Loop through competency arrays and insert them
        foreach ($_POST['competency_id'] as $index => $competency_id) {
            $priority_no = $_POST['priority_no'][$index] ?? null;
            $workplace_learning = $_POST['workplace_learning'][$index] ?? '';
            $social_learning = $_POST['social_learning'][$index] ?? '';
            $structured_learning = $_POST['structured_learning'][$index] ?? '';
            $resources_needed = $_POST['resources_needed'][$index] ?? '';
            $accomplishment_indicator = $_POST['accomplishment_indicator'][$index] ?? '';
            $fromdate = $_POST['fromdate'][$index] ?? null;
            $todate = $_POST['todate'][$index] ?? null;
            $estimated_budget = $_POST['estimated_budget'][$index] ?? null;

            // Bind parameters
            $stmt->bindParam(':userID', $userID);
            $stmt->bindParam(':idp_id', $idp_id);
            $stmt->bindParam(':competency_id', $competency_id);
            $stmt->bindParam(':priority_no', $priority_no);
            $stmt->bindParam(':workplace_learning', $workplace_learning);
            $stmt->bindParam(':social_learning', $social_learning);
            $stmt->bindParam(':structured_learning', $structured_learning);
            $stmt->bindParam(':resources_needed', $resources_needed);
            $stmt->bindParam(':accomplishment_indicator', $accomplishment_indicator);
            $stmt->bindParam(':fromdate', $fromdate);
            $stmt->bindParam(':todate', $todate);
            $stmt->bindParam(':estimated_budget', $estimated_budget);

            // Execute the statement
            $stmt->execute();
        }

        $conn->commit(); // Commit transaction

        $_SESSION['message'] = array("text" => "IDP submitted successfully.", "alert" => "success");
        header("Location: idpdashboard.php"); // Redirect to dashboard
        exit();
    } catch (Exception $e) {
        $conn->rollBack(); // Rollback on failure
        echo "Error: " . $e->getMessage();
    }
}
?>