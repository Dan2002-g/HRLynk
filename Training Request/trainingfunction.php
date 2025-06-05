<?php
require_once '../includes/approval_flow.php';

session_start();    
    $servername = "localhost";
    $email = "root";
    $password = "";
    try {
        $conn
    = new PDO("mysql:host=$servername;dbname=db_login"
    , $email, $password);
    // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION);
    echo "";
    } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_data'])) {
    try {
        $conn->beginTransaction();
        
        // Retrieve and sanitize form data
        $objectives = isset($_POST['objective']) ? implode(", ", $_POST['objective']) : 'No objective selected';
        $description = filter_input(INPUT_POST, 'objective_other', FILTER_SANITIZE_STRING);
        $idpcompetency = isset($_POST['idpcompetency']) ? $_POST['idpcompetency'] : '';
        $trainingtitle = filter_input(INPUT_POST, 'trainingtitle', FILTER_SANITIZE_STRING);
        $priorityNum = filter_input(INPUT_POST, 'priorityno_1', FILTER_SANITIZE_NUMBER_INT);
        $venue = filter_input(INPUT_POST, 'venue_1', FILTER_SANITIZE_STRING);
        $courseObjective = filter_input(INPUT_POST, 'course_objective_1', FILTER_SANITIZE_STRING);
        $prevEmployNum = filter_input(INPUT_POST, 'employees_attended_1', FILTER_SANITIZE_NUMBER_INT);
        $trainingDateStart = filter_input(INPUT_POST, 'training_date_start_1', FILTER_SANITIZE_STRING);
        $trainingDateEnd = filter_input(INPUT_POST, 'training_date_end_1', FILTER_SANITIZE_STRING);

        // Ensure userID is set and valid
        if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            $userID = $_SESSION['user'];
        } else {
            die("Error: User ID is not set. Please log in.");
        }

        // Validate required fields
        if (empty($trainingtitle) || empty($venue) || empty($trainingDateStart) || empty($trainingDateEnd)) {
            die("Error: All required fields must be filled out.");
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

        if ($stmt->execute()) {
            // Get the last inserted training ID
            $trainingId = $conn->lastInsertId();
            
            // Get user's role and office with proper error handling
            try {
                $stmt = $conn->prepare("
                    SELECT r.roleID, ud.officeID 
                    FROM users u 
                    LEFT JOIN role r ON u.roleID = r.roleID 
                    LEFT JOIN user_details ud ON u.userID = ud.userID 
                    WHERE u.userID = ?
                ");
                $stmt->execute([$userID]);
                $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$userInfo || !$userInfo['roleID'] || !$userInfo['officeID']) {
                    throw new Exception("User profile incomplete. Please complete your profile first.");
                }
                
                // Initialize approval flow with try-catch
                try {
                    initializeApprovalFlow($trainingId, $userInfo['roleID'], $userInfo['officeID'], $conn);
                    $conn->commit();
                    
                    $_SESSION['message'] = array(
                        "text" => "Training request submitted successfully.", 
                        "alert" => "success"
                    );
                    header("Location: trainingdashboard.php");
                    exit;
                } catch (Exception $e) {
                    throw new Exception("Failed to initialize approval flow: " . $e->getMessage());
                }
            } catch (Exception $e) {
                $conn->rollBack();
                $_SESSION['message'] = array(
                    "text" => $e->getMessage(),
                    "alert" => "danger"
                );
                header("Location: ../Profile/Profile.php");
                exit;
            }
        } else {
            throw new Exception("Could not submit the training report.");
        }
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>