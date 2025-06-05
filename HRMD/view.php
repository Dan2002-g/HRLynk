<?php
session_start();
include('connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

// Check if a training ID is provided via GET request
if (isset($_GET['trainingid'])) {
    $trainingid = $_GET['trainingid'];

    // Fetch training details from the database
    $stmt = $conn->prepare("SELECT * FROM training WHERE trainingid = :trainingid");
    $stmt->bindParam(':trainingid', $trainingid, PDO::PARAM_INT);
    $stmt->execute();
    $training = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the logged-in user is an L&D role
    $userID = $_SESSION['user'];
    $roleCheckStmt = $conn->prepare("
        SELECT r.roleName 
        FROM users u
        JOIN role r ON u.roleID = r.roleID
        WHERE u.userID = :userID
    ");
    $roleCheckStmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $roleCheckStmt->execute();
    $userRole = $roleCheckStmt->fetchColumn();

    $isLearningAndDevelopment = ($userRole === 'Learning and Development (L&D)');

    if ($training) {
        $idpcompetency = htmlspecialchars($training['idpcompetency']);
        $trainingTitle = htmlspecialchars($training['trainingtitle']);
        $priorityNo = htmlspecialchars($training['prioNum']);
        $veNue = htmlspecialchars($training['venue']);
        $courseObjective = htmlspecialchars($training['courseobjective']);
        $previousAttendance = htmlspecialchars($training['prevEmployNum']);
        $trainingDate = htmlspecialchars($training['trainingdate']);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
<div class="modal-body">
    <div class="responsive-wrapper">
        <section class="formbold-form-wrapper">
            <form action="trainingupdate.php" method="POST">
                <input type="hidden" name="trainingid" value="<?php echo $trainingid; ?>">
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
                            <td>
                                <?php if ($training['status'] !== 'Approved' && $training['status'] !== 'Rejected' && $isLearningAndDevelopment): ?>
                                    <input type="number" name="prevEmployNum" value="<?php echo $previousAttendance; ?>" class="form-control" required />
                                <?php else: ?>
                                    <input type="number" value="<?php echo $previousAttendance; ?>" class="form-control" disabled />
                                <?php endif; ?>
                            </td>
                            <td><?php echo $trainingDate; ?></td>   
                        </tr>
                    </tbody>
                </table>
                <form action="trainingupdate.php" method="POST">
                    <input type="hidden" name="trainingid" value="<?php echo $trainingid; ?>">

                    <?php if ($training['status'] !== 'Approved' && $training['status'] !== 'Rejected'): ?>
                        <!-- Editable Prev No. of Employees Attended -->
                        

                        <!-- Approve and Reject Buttons -->
                        <button type="submit" name="approve" class="btn btn-success">
                            Approve
                        </button>
                        <button type="button" class="btn btn-danger" onclick="showRejectModal()">
                            Reject
                        </button>
                    <?php elseif ($training['status'] === 'Approved'): ?>
                        <!-- Show only the disabled "Approved" button -->
                        <button type="button" class="btn btn-success" disabled>
                            Approved
                        </button>
                    <?php elseif ($training['status'] === 'Rejected'): ?>
                        <!-- Show only the disabled "Rejected" button -->
                        <button type="button" class="btn btn-danger" disabled>
                            Rejected
                        </button>
                    <?php endif; ?>
                </form>
            </form>
        </section>
    </div>
</div>

<div id="rejectModal" class="modal">
    <div class="modal-content">
        <h3>Provide Remarks for Rejection</h3>
        <form action="trainingupdate.php" method="POST">
            <input type="hidden" name="trainingid" value="<?php echo $trainingid; ?>">
            <textarea name="remarks" class="form-control" placeholder="Enter remarks..." required></textarea>
            <div class="button-container">
                <button type="submit" name="reject" class="btn btn-danger">Submit</button>
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>


<script>


    // Attach event listeners to buttons
    document.getElementById('acceptButton').addEventListener('click', () => {
        const trainingId = document.getElementById('trainingId').value;
        console.log("Approve button clicked.");
        updateStatus(trainingId, 'Approved');
    });
    function showRejectModal() {
    document.getElementById('rejectModal').style.display = 'block';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>

</body>
</html>



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
.modal {
    display: none; /* Hidden by default */
    position: flex; /* Fix the modal to the viewport */
    z-index: 1000; /* Ensure it appears above other elements */
    left: 50%; /* Center horizontally */
    top: 50%; /* Center vertically */
    transform: translate(-50%, -50%); /* Adjust position to center */
    width: 100%; /* Full width for the overlay */
    height: 100%; /* Full height for the overlay */
    background-color: rgba(0, 0, 0, 0.4); /* Black background with opacity */
}
.modal-content {
    background-color: #fff;
    padding: 20px;
    border: 1px solid #888;
    width: 50%; /* Adjust width as needed */
    max-width: 600px; /* Limit the maximum width */
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Optional shadow for better visibility */
    position: relative; /* Ensure proper positioning inside the modal */
    margin: auto; /* Center the modal content */
    top: 50%; /* Center vertically */
    left: 50%; /* Center horizontally */
    transform: translate(-50%, -50%); /* Adjust position to center */
}
.button-container {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
}