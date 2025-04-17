<?php
session_start();
$servername = "localhost";
$email = "root";
$password = "";
try {
    $conn = new PDO("mysql:host=$servername;dbname=db_login", $email, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Output the entire POST data
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';

    // Retrieve and sanitize form data
    $objectives = isset($_POST['objective']) ? implode(", ", $_POST['objective']) : 'No objective selected';
    $description = filter_input(INPUT_POST, 'objective_other', FILTER_SANITIZE_STRING);
    $idpcompetency = isset($_POST['idpcompetency']) ? $_POST['idpcompetency'] : '';
    $trainingtitle = isset($_POST['trainingtitle']) ? $_POST['trainingtitle'] : '';  // Directly accessing POST data
    $competency_id = filter_input(INPUT_POST, 'competency_id_1', FILTER_SANITIZE_STRING);
    $priorityNum = filter_input(INPUT_POST, 'priorityno_1', FILTER_SANITIZE_NUMBER_INT);
    $venue = filter_input(INPUT_POST, 'venue_1', FILTER_SANITIZE_STRING);
    $courseObjective = filter_input(INPUT_POST, 'course_objective_1', FILTER_SANITIZE_STRING);
    $prevEmployNum = filter_input(INPUT_POST, 'employees_attended_1', FILTER_SANITIZE_NUMBER_INT);
    $trainingDateStart = filter_input(INPUT_POST, 'training_date_start_1', FILTER_SANITIZE_STRING);
    $trainingDateEnd = filter_input(INPUT_POST, 'training_date_end_1', FILTER_SANITIZE_STRING);

    // Debugging: Verify the value of trainingtitle
    echo "Training Title: '" . $trainingtitle . "'<br>";  // Debugging output

    // Ensure userID is set and valid
    if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
        $userID = $_SESSION['user'];
    } else {
        die("Error: User ID is not set. Please log in.");
    }

    // Validate required fields
    if (empty($venue)) {
        die("Error: Venue is required.");
    }
    if (empty($trainingDateStart) || empty($trainingDateEnd)) {
        die("Error: Training dates are required.");
    }

    // Ensure trainingtitle is not empty
    if (empty($trainingtitle)) {
        die("Error: Training title is required.");
    }

    // File upload handling
    $filePaths = [];
    if (isset($_FILES['file-upload']) && $_FILES['file-upload']['error'][0] === UPLOAD_ERR_OK) {
        $uploadDir = 'documents/' . $userID . '/';
        if (!is_dir($uploadDir)) {  
            mkdir($uploadDir, 0777, true);
        }
        foreach ($_FILES['file-upload']['name'] as $index => $fileName) {
            $fileTmpPath = $_FILES['file-upload']['tmp_name'][$index];
            $destinationPath = $uploadDir . basename($fileName);
            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                $filePaths[] = $destinationPath;
            } else {
                echo "Error uploading file: $fileName<br>";
            }
        }
        $filePath = implode(',', $filePaths);
    } else {
        $filePath = 'No file uploaded';
    }

    // Prepare and execute SQL to insert data
    $sql = "INSERT INTO training (userID, objective, description, idpcompetency, trainingtitle, prioNum, venue, courseobjective, prevEmployNum, trainingdate, trainingdate_end, file_path) 
            VALUES (:userID, :objective, :description, :idpcompetency, :trainingtitle, :prioNum, :venue, :courseobjective, :prevEmployNum, :trainingdate, :trainingdate_end, :file_path)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':objective', $objectives);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':idpcompetency', $idpcompetency);
    $stmt->bindParam(':trainingtitle', $trainingtitle);
    $stmt->bindParam(':prioNum', $priorityNum);
    $stmt->bindParam(':venue', $venue);
    $stmt->bindParam(':courseobjective', $courseObjective);
    $stmt->bindParam(':prevEmployNum', $prevEmployNum);
    $stmt->bindParam(':trainingdate', $trainingDateStart);
    $stmt->bindParam(':trainingdate_end', $trainingDateEnd);
    $stmt->bindParam(':file_path', $filePath);

    try {
        if ($stmt->execute()) {
            echo "Training report submitted successfully!";
            header("Location: trainingdashboard.php");
            exit;
        } else {
            echo "Error: Could not submit the training report.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
?>