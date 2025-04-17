<?php
include('connection.php');

// Check if a training ID is provided via GET request
if (isset($_GET['trainingid'])) {
    $trainingid = $_GET['trainingid'];
    // Fetch training details from the database
    $stmt = $conn->prepare("SELECT * FROM training WHERE trainingid = :trainingid");
    $stmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);
    $stmt->execute();
    $training = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($training) {
        $objectives = htmlspecialchars($training['objective']);
        $description = htmlspecialchars($training['description']);
        $idpcompetency = htmlspecialchars($training['idpcompetency']);
        $trainingTitle = htmlspecialchars($training['trainingtitle']);
        $trainingDate = htmlspecialchars($training['trainingdate']);
        $submittedAt = htmlspecialchars($training['submitted_at']);
        $priorityNo = htmlspecialchars($training['prioNum']);
        $veNue = htmlspecialchars($training['venue']);
        $courseObjective = htmlspecialchars($training['courseobjective']);
        $previousAttendance = htmlspecialchars($training['prevEmployNum']);
    } else {
        $error = "No training details found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>External Training Request</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
<div class="modal-body">
    <div class="responsive-wrapper">
        <header class="main-header">
        </header>
        <!-- Competency Assessment and Development Plan Table -->
        <section class="formbold-form-wrapper">
            <form action="trainingupdate.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="trainingid" value="<?php echo $trainingid; ?>">
                <div class="form-section">
                    <h2>Requesting training on/with</h2>
                    <p><?php echo $objectives; ?></p>
                    <h2>Brief Job Description of the Employee (related to Training Program):</h2>
                    <p>
                        <?php 
                        if (!empty($description)) {
                            echo $description;
                        } else {
                            echo "No Description";
                        }
                        ?>
                    </p>
                </div>
                <!-- Training Program Table -->
                <table class="training-table table table-bordered">
                    <thead>
                        <tr>
                            <th>IDP Competency</th>
                            <th>Training/Seminar Title</th>
                            <th>Priority No.</th>
                            <th>Venue</th>  
                            <th>Course Objective</th>
                            <th>Prev No. of Employees Attended</th>
                            <th>Training Date (Inclusive)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $idpcompetency; ?></td>   
                            <td><?php echo $trainingTitle; ?></td>
                            <td><?php echo $priorityNo; ?></td>
                            <td><?php echo $veNue; ?></td>
                            <td><?php echo $courseObjective; ?></td>
                            <td><?php echo $previousAttendance; ?></td>
                            <td><?php echo $trainingDate; ?></td>
                        </tr>
                    </tbody>
                </table>
                <!-- File Upload Section -->
                <div class="upload-section">
                    <h3>Submitted Files:</h3>
                    <?php if (!empty($training['file_path'])): ?>
                        <?php 
                        $filePaths = explode(',', $training['file_path']); 
                        foreach ($filePaths as $filePath): 
                            $filePath = trim($filePath);
                            $fileName = basename($filePath);
                            $filePath = '../Training Request/' . $filePath;
                        ?>
                            <a href="<?= htmlspecialchars($filePath); ?>" target="_blank">
                                <?= htmlspecialchars($fileName); ?>
                            </a><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        No file attached
                    <?php endif; ?>
                </div>
            </form>
        </section>
    </div>
</div>
<div class="modal-body">
    <div class="responsive-wrapper">
        <section class="formbold-form-wrapper">
        </section>
    </div>
</div>
<div class="modal-footer">
    <form action="trainingupdate.php" method="POST">
        <input type="hidden" name="trainingid" value="<?php echo $trainingid; ?>">

        <?php if ($training['status'] === 'Approved'): ?>
            <!-- Show only the disabled "Approved" button -->
            <button type="button" class="btn btn-success" disabled>
                Approved
            </button>
        <?php elseif ($training['status'] === 'Rejected'): ?>
            <!-- Show only the disabled "Rejected" button -->
            <button type="button" class="btn btn-danger" disabled>
                Rejected
            </button>
        <?php else: ?>
            <!-- Show both "Approve" and "Reject" buttons if no final status -->
            <button type="submit" name="approve" class="btn btn-success">
                Approve
            </button>
            <button type="submit" name="reject" class="btn btn-danger">
                Reject
            </button>
        <?php endif; ?>
    </form>
</div>

<script>


    // Attach event listeners to buttons
    document.getElementById('acceptButton').addEventListener('click', () => {
        const trainingId = document.getElementById('trainingId').value;
        console.log("Approve button clicked.");
        updateStatus(trainingId, 'Approved');
    });

    document.getElementById('rejectButton').addEventListener('click', () => {
        const trainingId = document.getElementById('trainingId').value;
        console.log("Reject button clicked.");
        updateStatus(trainingId, 'Rejected');
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>

<script src='https://unpkg.com/phosphor-icons'></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style type="text/css">@import url("https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");
:root {
  --c-text-primary: #282a32;
  --c-text-secondary: #686b87;
  --c-text-action: #404089;
  --c-accent-primary: #434ce8;
  --c-border-primary: #eff1f6;
  --c-background-primary: #ffffff;
  --c-background-secondary: #fdfcff;
  --c-background-tertiary: #ecf3fe;
  --c-background-quaternary: #e9ecf4;
}

body {
  display: flex;
  line-height: 1.5;
  min-height: 100vh;
  font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif, sans-serif;
  background-color: var(--c-background-secondary);
  color: var(--c-text-primary);
}


body {
  display: flex;
  font-family: "Be Vietnam Pro", sans-serif;
  background-color: #fdfcff;
  color: #282a32;
}

label{
    color: #282a32;
}

h1 {
  color: maroon;
  font-size: 30px !important;
  font-weight: 800;
}

.feature h2 {
  color: maroon;
}

h2 {
    color: #282a32;
    font-weight: bold;
}

ul {
    color: #282a32;
}
h3 {
  color: #282a32;
  font-weight:bold;
}

.feature a {
  color: gold;
  text-shadow: 0.5px 0.5px maroon;
}


main {
  margin-top: 100px;
  margin-left: 100px; /* Adjust to match the width of the sidebar + some spacing */
  margin-right: 100px;
  padding: 0px; /* Add padding for main content */
  flex-grow: 1; /* Allow the main content to take available space */
}
.main-header h1 {
    font-size: 24px;
    margin-bottom: 20px;
}
.horizontal{
    text-decoration: none;
    font-size: 20px;
    font-weight: 500;

}
.horizontal:hover{
    color: maroon !important;
}

.horizontal-position{
    align-content: left;
}
.horizontal-tabs{
  border-bottom: 1px solid rgb(0, 0, 0);
  border-bottom-width: 100%;
}

.horizontal-button {
    padding: 5px 10px; /* Add more padding for better size */
    border: 1px solid rgb(0, 0, 0); /* Adds a border */
    text-decoration: none; /* Removes underline */
    color: maroon; /* Sets text color */
    transition: background-color 0.3s, color 0.3s; /* Smooth transition */
    border-radius: 5px; /* Rounds the corners */
    display: inline-flex; /* Aligns icon and text */
}

.horizontal-button i {
    margin-left: 5px; /* Adds space between icon and text */
}

.horizontal-button:hover {
    background-color: rgba(128, 0, 0, 0.984) !important; /* Background on hover */
    color: rgb(255, 255, 255) !important; /* Text color on hover */
}

.formbold-form-wrapper {
    padding: 5px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    height:  100%;

}

.formbold-mb-3 {
    margin-top: 10px;
    margin-bottom: 20px;
    color: black;
}

input[type="text"],
input[type="date"],
select {
    width: 83%;
    padding-right: 5px;
    padding-left: 5px;
    padding-top: 5px;
    padding-bottom: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 0px;
}

/* Table styles for Competency Assessment */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    margin-bottom: 15px;
}

table th,
table td {
    border: 1px solid #ddd;
    padding: 5px;
}

/* Adjusting header text color and size */
table th {
    background-color: maroon;
    color: black; /* Changed from white to black */
    text-align: left;
    font-size: 14px; /* Reduced font size */
}

thead th {
  color: white;
}

/* Objective section text */
.objective-section {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    gap: 0px;
}

.objective-label {
    font-size: 16px;
    font-weight: bold;
}

input[type="checkbox"] {
    margin-right: 10px;
}

/* Button styles */
/* Button styles */
button.formbold-btn, input.formbold-btn {
    background-color: maroon; /* Main background color */
    color: white; /* Text color */
    padding: 5px 10px; /* Padding for button */
    border: none; /* No border */
    border-radius: 4px; /* Rounded corners */
    cursor: pointer; /* Pointer on hover */
    font-size: 14px; /* Font size */
    transition: background-color 0.3s ease, transform 0.2s; 
    display: inline-block; 

button.formbold-btn:hover, input.formbold-btn:hover {
    background-color: darkred; 
}

button.formbold-btn.add-btn {
    background-color: #4CAF50;
}

button.formbold-btn.add-btn:hover {
    background-color: #45a049;
}

button.formbold-btn.submit-btn {
    background-color: #007BFF; 
}

button.formbold-btn.submit-btn:hover {
    background-color: #0056b3; 
}


.button-container {
    display: flex; 
    gap: 10px; 
    margin-top: 10px !important; 
} 
.custom-modal-width {
    max-width: 90%; /* Adjust the percentage as needed */
}
}