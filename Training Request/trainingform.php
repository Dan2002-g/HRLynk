<?php

// Validate and assign ID from GET request
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Include functions and database connection
include('trainingfunction.php');

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    die("Error: User session not set. Please log in.");
}

$userID = $_SESSION['user']; // Assign user ID from session

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>External Training Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <a href="../Homepage/index.html" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
            <span style="color: maroon;">HR</span><span style="color: gold; text-shadow: 1px 1px maroon;">Lynk</span>
        </a>
        <a href="../Homepage/logout.php" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
        <h4>Employee Portal</h4>
    </div>
    <div class="header-navigation">
        <nav class="sidebar-navigation">
            <a href="../Homepage/index.html" class="sidebar-link active">Home</a>
            <a href="../IDP/idpdashboard.php" class="sidebar-link">IDP</a>
            <a href="../Training Request/trainingform.php" class="sidebar-link">Request Training</a>
            <a href="../Training Request/trainingdashboard.php" class="sidebar-link">Training History</a>
        </nav>
        <nav class="sidebar-navigation side-nav">
            <a href="../Profile/profiledisplay.php" class="icon-button" aria-label="Profile"><i class="bi bi-person-circle"></i> Profile</a>
            <a href="../Homepage/logout.php" class="icon-button" aria-label="Logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
    </div>
</div>

<main class="main">
    <div class="responsive-wrapper">
        <header class="main-header">
            <h1>External Training Request</h1>
        </header>

        <div class="horizontal-tabs">
            <a class="horizontal" href="trainingdashboard.php" style="color: black;">Dashboard</a>
        </div>

        <!-- Competency Assessment and Development Plan Table -->
        <section class="formbold-form-wrapper">
        <form action="trainingfunction.php" method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <h2>Requesting training on/with (Please Check One):</h2>
                    <label><input type="radio" name="objective[]" value="Official Time Only"> Official Time Only</label>
                    <label><input type="radio" name="objective[]" value="Allowable Allowances"> Allowable Allowances</label>
                    <label><input type="radio" name="objective[]" value="Reg. Fee Only"> Reg. Fee Only</label>
                    <label><input type="radio" name="objective[]" value="Reg. Fee & Allow. Allowances"> Reg. Fee & Allowable Allowances</label>
                    <br><br>
                    <h2>Brief Job Description of the Employee (related to Training Program):</h2>
                    <textarea required name="objective_other" id="objective_other" class="formbold-form-input auto-expand short-input" placeholder="Description" style="overflow: hidden; resize: none; width: 98%;"></textarea>
                </div>
                <table class="training-table" border="1" cellpadding="0" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>IDP Competency</th>
                            <th>Training/Seminar Title</th>
                            <th>Priority No.</th>
                            <th>Venue</th>  
                            <th>Objective of the Training</th>
                            <th>No. of Employees who Attended Before</th>
                            <th>Training Date (Inclusive)</th>
                        </tr>
                    </thead>
                    <tbody id="training-table-body">
                        <tr>
                        <td>
    <select name="idpcompetency" required id="idpcompetency" class="formbold-form-input" style="width: auto; min-width: 93%;"> 
        <option value="">Select Competency</option>
        <?php
        try {
            if (!isset($conn)) {
                die("<option value=''>Database connection error</option>");
            }

            // Updated SQL query to exclude competencies already approved in the training table
            $sql = "SELECT DISTINCT competency.competencyname, idp_competencies.priority_no 
                    FROM competency 
                    JOIN idp_competencies ON competency.competency_id = idp_competencies.competency_id 
                    JOIN idp ON idp_competencies.idp_id = idp.id
                    WHERE idp_competencies.userID = :userID 
                      AND idp.status = 'Approved'
                      AND competency.competencyname NOT IN (
                          SELECT DISTINCT idpcompetency 
                          FROM training 
                          WHERE userID = :userID 
                            AND status IN ('Approved', 'Completed')
                      )";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $competencies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($competencies as $competency) {
                    echo "<option value='{$competency['competencyname']}' data-priority='{$competency['priority_no']}'>{$competency['competencyname']}</option>";
                }
            } else {
                echo "<option value=''>No Approved Competencies Available</option>";
            }
        } catch (PDOException $e) {
            echo "<option value=''>Error loading competencies: " . htmlspecialchars($e->getMessage()) . "</option>";
        }
        ?>
    </select>
</td>
                            <td style="vertical-align: top;">
                            <textarea required name="trainingtitle" class="formbold-form-input auto-expand"
                                style="width: 100%; padding: 6px; resize: none; overflow: hidden; box-sizing: border-box;"></textarea>
                            </td>

                            <td style="vertical-align: top;">
                            <input type="number" required id="priorityno" name="priorityno_1" class="formbold-form-input" readonly
                                style="width: 100%; padding: 6px; height: 38px; box-sizing: border-box;">
                            </td>

                            <td style="vertical-align: top;">
                            <textarea required name="venue_1" class="formbold-form-input auto-expand"
                                style="width: 100%; padding: 6px; resize: none; overflow: hidden; box-sizing: border-box;"></textarea>
                            </td>

                            <td style="vertical-align: top;">
                            <textarea required name="course_objective_1" class="formbold-form-input auto-expand"
                                style="width: 100%; padding: 6px; resize: none; overflow: hidden; box-sizing: border-box;"></textarea>
                            </td>

                            <td style="vertical-align: top;">
                            <input type="number" required name="employees_attended_1" class="formbold-form-input" min="0" readonly
                                style="width: 100%; padding: 6px; height: 38px; box-sizing: border-box;">
                            </td>

                            <td style="vertical-align: top;">
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <div style="display: flex; align-items: center; gap: 4px;">
                                <label style="white-space: nowrap;">From:</label>
                                <input type="date" required name="training_date_start_1"
                                    style="width: 100%; padding: 6px; height: 38px; box-sizing: border-box;">
                                </div>
                                <div style="display: flex; align-items: center; gap: 20px;">
                                <label style="white-space: nowrap;">To:</label>
                                <input type="date" required name="training_date_end_1"
                                    style="width: 100%; padding: 6px; height: 38px; box-sizing: border-box;">
                                </div>
                            </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- File Upload Section -->
<div class="upload-section">
    <h3>Attach the following documents:</h3>
    <ul>
        <li>Invitation Letter/Program</li>
        <li>Approved Office WFP</li>
    </ul><br>
    
    <input type="file" id="file-upload" name="file-upload[]" multiple required>

    <!-- Display selected files -->
    <div id="file-list">
        <h3>Selected Files:</h3>
        <ul id="file-names"></ul>
    </div>

    <!-- Submit Button -->
    <button type="submit" name="submit_data" class="formbold-btn submit-btn">Submit</button>
</div>
<script>
  const start = document.querySelector('input[name="training_date_start_1"]');
  const end = document.querySelector('input[name="training_date_end_1"]');

  start.addEventListener('change', () => {
    end.min = start.value;
  });
</script>
<script>
    document.getElementById('file-upload').addEventListener('change', function(event) {
        let fileList = document.getElementById('file-names');
        fileList.innerHTML = ''; // Clear previous list

        Array.from(event.target.files).forEach(file => {
            let listItem = document.createElement('li');
            listItem.textContent = file.name; // Display file name
            fileList.appendChild(listItem);
        });
    });
</script>

            </form>
        </section>
    </div>
</main>

<script>
$(document).ready(function() {
    $('#idpcompetency').on('change', function() {
        var priority = $(this).find(':selected').data('priority');
        $('#priorityno').val(priority);
    });
});

document.addEventListener('input', function (event) {
    if (event.target.classList.contains('auto-expand')) {
        event.target.style.height = 'auto'; // Reset height
        event.target.style.height = event.target.scrollHeight + 'px'; // Adjust height dynamically
    }
});

// Initialize textareas to auto-expand on page load
document.querySelectorAll('.auto-expand').forEach(function (textarea) {
    textarea.style.height = textarea.scrollHeight + 'px';
});
</script>

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
.logo {
 text-align: center;
 padding-bottom: 20px;
}


.side-nav{
  padding-top: 455px;
  position: fixed;
}

.sidebar-navigation {
  display: flex;
  flex-direction: column;

}
.sidebar-link {
  padding: 10px 10px; /* Adds padding around the text */
  border-bottom: 1px solid rgb(0, 0, 0); /* Adds a border around the box */
  text-decoration: none; /* Removes underline from links */
  color: maroon; /* Sets the text color */
  transition: background-color 0.3s, color 0.3s; /* Smooth transition effect */
}

.sidebar-link:hover {
  background-color: rgba(128, 0, 0, 0.984) !important; /* Changes background on hover */
  color: rgb(255, 255, 255) !important; /* Changes text color on hover */
}
.sidebar-link:active {
  background-color: rgba(128, 0, 0, 0.984) !important; /* Changes background on hover */
  color: rgb(255, 255, 255) !important; /* Changes text color on hover */
}
.sidebar-navigation a {
  margin: 10px 0; /* Space between links */
  text-decoration: none;
  color: var(--c-text-action);
  font-weight: 500;
  transition: color 0.15s ease;
}
h4 {
    font-size: 20px;
    font-weight: 500;
    color: gold;
    text-align: center;
    margin-top: 0px;
    shadow: 0.5px 0.5px maroon;
    text-shadow: 0.5px 0.5px maroon;
}
.sidebar-navigation a:hover, .sidebar-navigation a:focus, .sidebar-navigation a:active {
  color: var(--c-accent-primary);
}@import url("https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@100;400;700&display=swap");

:root {
  --bg-img-url: url("/images/bg1.jpg");
  --bg-gradient-color: rgba(138, 32, 32, 0.75);
  --text-shadow-color: rgba(187, 38, 38, 0.75);
}

body {
  display: flex;
  font-family: "Be Vietnam Pro", sans-serif;
  background-color: #fdfcff;
  color: #ffffff;
}

label{
    color: #282a32;
    font-size: 18px;
}

.sidebar {
  width: 180px;
  background-color:rgb(255, 255, 255);
  height: 100vh;
  padding: 10px;
  position: fixed;
}

.sidebar-navigation a {
  margin: 10px 0;
  text-decoration: none;
  color: #404089;
  font-weight: 300;
  transition: color 0.15s ease;
}
/* Style the select dropdown */
select {
    font-size: 15px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: white;
    cursor: pointer;
}

/* Style the dropdown options */
select option {
    font-size: 15px;
    padding: 5px;
    background-color: white;
    color: black;
}

/* Style the placeholder option */
select option[value=""] {
    font-style: italic;
    color: gray;
}

/* Style for input fields inside table cells */
td .formbold-form-input {
    font-size: 16px; /* Adjust font size */
    color: black; /* Text color */
    font-family: Arial, sans-serif; /* Font style */
    padding: 5px; /* Padding for better spacing */
    border: 1px solid #ccc; /* Border for inputs */
    border-radius: 4px; /* Rounded corners */
    width: 100%; /* Ensures full width within <td> */
}

/* Read-only field styling */
td .formbold-form-input[readonly] {
    background-color: #f0f0f0; /* Light gray background */
    color: #666; /* Dimmed text for clarity */
    cursor: not-allowed; /* Indicate non-editable field */
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
    font-size: 21px;

}

ul {
    color: #282a32;
    font-size: 18px;
}
h3 {
  color: #282a32;
  font-weight:bold;
  font-size: 20px;
}

.feature a {
  color: gold;
  text-shadow: 0.5px 0.5px maroon;
}


main {
  margin-top: 100px;
  margin-left: 280px; /* Adjust to match the width of the sidebar + some spacing */
  margin-right: 80px;
  padding: 0px; /* Add padding for main content */
  flex-grow: 1; /* Allow the main content to take available space */
}
.main-header h1 {
    font-size: 24px;
    margin-bottom: 20px;
}
.horizontal{
    text-decoration: none;
    font-size: 26px;
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
    height: auto; /* Change from 100% to auto */
    overflow: hidden; /* Ensure no extra space is shown */
    margin-bottom: 20px; /* Add some spacing below the form */
}
/* Style the file input button */
#file-upload::-webkit-file-upload-button {
    background-color: maroon; /* Button background */
    color: white; /* Button text color */
    font-size: 14px; /* Adjust font size */
    padding: 10px 15px; /* Add padding */
    border: none; /* Remove default border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Make it clickable */
}

/* Hover effect */
#file-upload::-webkit-file-upload-button:hover {
    background-color: darkred; /* Darker shade on hover */
}

.short-input {
    font-size: 19px; /* Adjust font size */
    color: black; /* Change text color */
    font-weight: normal; /* Adjust weight (bold, normal, etc.) */
    font-family: Arial, sans-serif; /* Set font family */
    padding: 8px; /* Add padding for better spacing */
    border: 1px solid #ccc; /* Define border */
    border-radius: 5px; /* Rounded corners */
}

.short-input::placeholder {
    color: gray; /* Change placeholder text color */
    font-style: italic; /* Make it italic */
    font-size: 19px; /* Adjust font size */
    opacity: 1; /* Ensure full visibility */
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
    text-align: center;
    font-size: 18px;
    font-weight: bold; /* Reduced font size */
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
    font-size: 17px; /* Font size */
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

}
select.formbold-form-input {
    width: auto; /* Adjust width automatically */
    min-width: 93%; /* Ensure a minimum width */
}
</style>