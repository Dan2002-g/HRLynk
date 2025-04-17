<?php
include('connection.php'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idp_id = $_POST['idp_id'];
    $competencies = $_POST['competencies'];

    try {
        $conn->beginTransaction();

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
        header("Location: idpdashboard.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>