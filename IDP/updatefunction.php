<?php
include('connection.php'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idp_id = $_POST['idp_id'] ?? null;

    if (!$idp_id) {
        die("Error: IDP ID is missing.");
    }

    $competencies = $_POST['competencies'];

    try {
        $conn->beginTransaction();

        // Update the status of the IDP to "To be reviewed"
        $updateStatusStmt = $conn->prepare("UPDATE idp SET status = 'To be reviewed' WHERE id = ?");
        if (!$updateStatusStmt->execute([$idp_id])) {
            die("Error: Failed to update IDP status.");
        }

        // Verify the status update
        $checkStatusStmt = $conn->prepare("SELECT status FROM idp WHERE id = ?");
        $checkStatusStmt->execute([$idp_id]);
        $updatedStatus = $checkStatusStmt->fetchColumn();

        if ($updatedStatus !== 'To be reviewed') {
            die("Error: Status was not updated. Current status: $updatedStatus");
        }

        // Update competencies
        foreach ($competencies as $competency) {
            $sql_comp = "UPDATE idp_competencies 
                         SET priority_no = ?, competency_id = ?, workplace_learning = ?, social_learning = ?, 
                             structured_learning = ?, resources_needed = ?, accomplishment_indicator = ?, 
                             fromdate = ?, todate = ?, estimated_budget = ? 
                         WHERE id = ?";
            $stmt_comp = $conn->prepare($sql_comp);
            $stmt_comp->execute([
                $competency['priority_no'], $competency['competency_id'], $competency['workplace_learning'], 
                $competency['social_learning'], $competency['structured_learning'], $competency['resources_needed'], 
                $competency['accomplishment_indicator'], $competency['fromdate'], $competency['todate'], 
                $competency['estimated_budget'], $competency['id']
            ]);
        }

        $conn->commit();
        $_SESSION['message'] = array("text" => "IDP updated and submitted for review.", "alert" => "success");
        header("Location: idpdashboard.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>