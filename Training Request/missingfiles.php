<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted Documents</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: rgba(128, 0, 0, 0.8);
            color: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container h1 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Submit Missing Files</h1>
        <?php
include 'connection.php'; // Include database connection file

try {
    // Start session to get user ID (if not already started)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trainingid'])) {
        $trainingid = intval($_POST['trainingid']); // Sanitize training ID
        $baseDir = __DIR__ . '/documents/' . $userID . '/'; // Base directory for user uploads
        $filePaths = [];
        $uploadErrors = [];

        // Ensure the user's directory exists
        if (!is_dir($baseDir)) {
            if (!mkdir($baseDir, 0777, true)) {
                throw new Exception("Failed to create the user's document directory.");
            }
        }

        if (!empty($_FILES['file_upload']['name'][0])) {
            foreach ($_FILES['file_upload']['name'] as $key => $fileName) {
                $fileTmpPath = $_FILES['file_upload']['tmp_name'][$key];
                $fileType = mime_content_type($fileTmpPath);
                $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

                // Sanitize the file name
                $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);

                if (in_array($fileType, $allowedTypes)) {
                    $destinationPath = $baseDir . basename($fileName);

                    if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                        // Store relative path (e.g., "documents/{userID}/filename.ext")
                        $relativePath = 'documents/' . $userID . '/' . basename($destinationPath);
                        $filePaths[] = $relativePath;
                    } else {
                        $uploadErrors[] = "Error uploading file: $fileName.";
                    }
                } else {
                    $uploadErrors[] = "Invalid file type for: $fileName. Only PDF, JPEG, and PNG are allowed.";
                }
            }

            if (!empty($filePaths)) {
                // Safely concatenate file paths with commas
                $filePathsString = implode(',', $filePaths);

                // Fetch existing file paths from the database
                $stmt = $conn->prepare("SELECT file_path FROM training WHERE trainingid = :trainingid");
                $stmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);
                $stmt->execute();
                $existingFilePaths = $stmt->fetchColumn();

                // Merge existing file paths with new ones, avoiding duplicates
                $allFilePaths = array_filter(array_unique(array_merge(
                    explode(',', $existingFilePaths ?? ''),
                    $filePaths
                )));

                // Convert merged paths back to a comma-separated string
                $newFilePaths = implode(',', $allFilePaths);

                // Update the database with the merged file paths
                $stmt = $conn->prepare("UPDATE training 
                                        SET file_path = :filePaths 
                                        WHERE trainingid = :trainingid");
                $stmt->bindParam(':filePaths', $newFilePaths, PDO::PARAM_STR);
                $stmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    // Redirect to the training dashboard after a successful update
                    header('Location: trainingdashboard.php');
                    exit;
                } else {
                    echo "<p style='color: red;'>Error updating file paths in the database.</p>";
                }
            }

            // Display any upload errors
            if (!empty($uploadErrors)) {
                foreach ($uploadErrors as $error) {
                    echo "<p style='color: red;'>$error</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>No files selected for upload.</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>





        <form action="" method="post" enctype="multipart/form-data">
            <label for="fileUpload"><strong>Choose files:</strong></label>
            <input type="file" name="file_upload[]" id="fileUpload" multiple required>
            <input type="hidden" name="trainingid" value="<?php echo htmlspecialchars($_GET['trainingid']); ?>">
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>