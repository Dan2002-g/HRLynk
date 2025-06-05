<?php
include('trainingfunction.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trainingId = $_POST['trainingid'];
    $file = $_FILES['missingFile'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $filePath = 'documents/' . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Update the database with the new file path
            $stmt = $conn->prepare("UPDATE training SET file_path = CONCAT(IFNULL(file_path, ''), :filePath, ',') WHERE trainingid = :trainingid");
            $stmt->bindParam(':filePath', $filePath, PDO::PARAM_STR);
            $stmt->bindParam(':trainingid', $trainingId, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['message'] = array("text" => "File uploaded successfully!", "alert" => "success");
        } else {
            $_SESSION['message'] = array("text" => "Failed to upload file.", "alert" => "danger");
        }
    } else {
        $_SESSION['message'] = array("text" => "Error uploading file.", "alert" => "danger");
    }

    header('Location: trainingdashboard.php');
    exit();
}
?>
