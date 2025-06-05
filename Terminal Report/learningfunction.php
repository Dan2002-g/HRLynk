<?php
session_start();
include('connection.php');

// Update the saveLearningApplication function to include all fields
function saveLearningApplication($conn, $userID, $trainingID, $type, $otherType, $title, $organizer, 
    $fromDate, $toDate, $numDays, $numHours, $venue, $briefLearning, $function, $activity, 
    $period, $resourcesNeeded, $monitoringEvaluation, $filePath) {
    
    try {
        $stmt = $conn->prepare("INSERT INTO `learningapplication` 
            (`userID`, `trainingid`, `type`, `other_type`, `title`, `organizer`, `from_date`, `to_date`, 
            `num_days`, `num_hours`, `venue`, `brief_learning`, `function`, `activity`, `period`, 
            `resource_needed`, `moneval`, `file_path`) 
            VALUES 
            (:userID, :trainingID, :type, :otherType, :title, :organizer, :fromDate, :toDate, 
            :numDays, :numHours, :venue, :briefLearning, :function, :activity, :period, 
            :resourcesNeeded, :monitoringEvaluation, :filePath)");

        // Bind all parameters
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':trainingID', $trainingID);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':otherType', $otherType);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':organizer', $organizer);
        $stmt->bindParam(':fromDate', $fromDate);
        $stmt->bindParam(':toDate', $toDate);
        $stmt->bindParam(':numDays', $numDays);
        $stmt->bindParam(':numHours', $numHours);
        $stmt->bindParam(':venue', $venue);
        $stmt->bindParam(':briefLearning', $briefLearning);
        $stmt->bindParam(':function', $function);
        $stmt->bindParam(':activity', $activity);
        $stmt->bindParam(':period', $period);
        $stmt->bindParam(':resourcesNeeded', $resourcesNeeded);
        $stmt->bindParam(':monitoringEvaluation', $monitoringEvaluation);
        $stmt->bindParam(':filePath', $filePath);

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

// Handle file upload function
function handleFileUpload($files) {
    // Base directory for all documents
    $baseDir = __DIR__ . '/documents/';
    
    // Create documents directory if it doesn't exist
    if (!file_exists($baseDir)) {
        mkdir($baseDir, 0777, true);
    }

    // Create user-specific directory using userID
    $userDir = $baseDir . $_SESSION['user'] . '/';
    if (!file_exists($userDir)) {
        mkdir($userDir, 0777, true);
    }

    $filePaths = [];
    $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

    foreach ($files['name'] as $key => $name) {
        if (empty($name)) continue; // Skip empty file inputs
        
        $tmpName = $files['tmp_name'][$key];
        $fileType = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        
        if (in_array($fileType, $allowedTypes)) {
            $destination = $userDir . $name;
            
            // If file already exists, add number to filename while keeping extension
            $counter = 1;
            $fileName = pathinfo($name, PATHINFO_FILENAME);
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            
            while (file_exists($destination)) {
                $newName = $fileName . "($counter)." . $extension;
                $destination = $userDir . $newName;
                $counter++;
            }
            
            if (move_uploaded_file($tmpName, $destination)) {
                // Store relative path in database
                $filePaths[] = 'documents/' . $_SESSION['user'] . '/' . basename($destination);
            } else {
                error_log("Failed to move uploaded file: " . error_get_last()['message']);
            }
        }
    }
    
    return $filePaths;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['learning_application'])) {
    // ...existing session checks...

    // Get user ID from session
    $userID = $_SESSION['user'];
    
    // Retrieve all form data with corrected field names
    $trainingID = $_POST['trainingid'];
    
    // Get checkbox values and combine into string
    $type = '';
    if (isset($_POST['type'])) {
        $type = implode(',', $_POST['type']);
    }
    
    $otherType = isset($_POST['other_type']) ? $_POST['other_type'] : ''; // Changed from others_specify
    $title = $_POST['title'];
    $organizer = $_POST['organizer'];
    $fromDate = $_POST['from_date']; // Changed from fromdate
    $toDate = $_POST['to_date']; // Changed from todate
    $numDays = $_POST['num_days']; // Changed from days
    $numHours = $_POST['num_hours']; // Changed from hours
    $venue = $_POST['venue'];
    $briefLearning = $_POST['brief_learning']; // Changed from brief_learning
    $function = $_POST['function'];
    $activity = $_POST['activity'];
    $period = $_POST['period'];
    $resourcesNeeded = $_POST['resource_needed']; // Changed from resources_needed
    $monitoringEvaluation = $_POST['moneval']; // Changed from monitoringEvaluation

    // Handle file uploads
    $filePaths = [];
    if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
        $filePaths = handleFileUpload($_FILES['files']);
    }

    // Combine file paths
    $filePath = empty($filePaths) ? 'No file uploaded' : implode(',', $filePaths);

    try {
        // Save learning application data
        if (saveLearningApplication($conn, $userID, $trainingID, $type, $otherType, $title, 
            $organizer, $fromDate, $toDate, $numDays, $numHours, $venue, $briefLearning,
            $function, $activity, $period, $resourcesNeeded, $monitoringEvaluation, $filePath)) {
            
            $_SESSION['message'] = array("text" => "Learning Application Plan submitted successfully!", "alert" => "success");
            header('Location: ../Training Request/trainingdashboard.php');
            exit();
        } else {
            throw new Exception("Failed to save application");
        }
    } catch (Exception $e) {
        $_SESSION['message'] = array("text" => "Error saving Learning Application Plan: " . $e->getMessage(), "alert" => "danger");
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>