<?php
include('trainingfunction.php');

// Ensure the session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

$userID = $_SESSION['user'];
$trainingID = $_GET['trainingid'] ?? null;

// Validate training ID
if (!$trainingID) {
    die("Error: Missing training ID. Please provide a valid training ID.");
}

// Fetch training details for validation
$stmt = $conn->prepare("SELECT trainingtitle, venue FROM training WHERE trainingid = :trainingID");
$stmt->bindParam(':trainingID', $trainingID);
$stmt->execute();
$trainingDetails = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trainingDetails) {
    die("Error: Invalid training ID. No training found with this ID.");
}

// Extract training details
$trainingTitle = $trainingDetails['trainingtitle'] ?? '';
$venue = $trainingDetails['venue'] ?? '';

if (empty($venue)) {
    die("Error: Venue is required.");
}

// Debugging: Print received files and training details
echo '<pre>';
print_r($_FILES);
echo '</pre>';
echo "Training Title: '{$trainingTitle}'<br>";
echo "Venue: '{$venue}'<br>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if any files are uploaded
    if (isset($_FILES['missing_files']) && !empty($_FILES['missing_files']['name'][0])) {
        $uploadsDir = '../uploads/';
        $uploadedFiles = [];
        foreach ($_FILES['missing_files']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['missing_files']['name'][$key]);
            $targetFilePath = $uploadsDir . $fileName;

            if (move_uploaded_file($tmpName, $targetFilePath)) {
                $uploadedFiles[] = $fileName; // Save only the file names
            }
        }

        // Update database with new file paths
        if (!empty($uploadedFiles)) {
            $filePaths = implode(',', $uploadedFiles);
            $stmt = $conn->prepare("UPDATE training SET file_path = CONCAT(file_path, ',', :filePaths) WHERE trainingid = :trainingID");
            $stmt->bindParam(':filePaths', $filePaths);
            $stmt->bindParam(':trainingID', $trainingID);

            if ($stmt->execute()) {
                $_SESSION['message'] = array("text" => "Missing files successfully resubmitted.", "alert" => "success");
            } else {
                $_SESSION['message'] = array("text" => "Database update failed. Please try again.", "alert" => "danger");
            }
        } else {
            $_SESSION['message'] = array("text" => "No files were successfully uploaded.", "alert" => "warning");
        }
    } else {
        $_SESSION['message'] = array("text" => "No files selected for upload.", "alert" => "warning");
    }

    // Redirect to the training dashboard
    header("Location: trainingdashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resubmit Missing Files</title>
</head>
<body>
    <h1>Resubmit Missing Files</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="missing_files">Upload Missing Files:</label>
        <input type="file" name="missing_files[]" multiple>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
