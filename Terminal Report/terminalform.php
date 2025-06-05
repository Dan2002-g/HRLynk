<?php
include('connection.php');
include('terminalfunction.php');
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('Location: ../Login/index.php');
    exit();
}

$userID = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Terminal Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <a href="../Homepage/index.html" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
                <span style="color: maroon;">HR</span><span style="color: gold; text-shadow: 1px 1px maroon;">Lynk</span>
            </a>
            <a href="../Homepage/logout.php" style="font-size: 20px; font-weight: 500; color: gold; margin-top: 20px; text-shadow: 0.5px 0.5px maroon; text-decoration: none;">
        Employee Portal
            </a>
        </div>
        <div class="header-navigation">
            <nav class="sidebar-navigation">
                <a href="../Homepage/index.html" class="sidebar-link active">Home</a>
                <a href="../IDP/idpdashboard.php" class="sidebar-link">IDP</a>
                <a href="../Training Request/trainingform.php" class="sidebar-link">Request Training</a>
                <a href="../Training Request/trainingdashboard.php" class="sidebar-link">Training History</a>
            </nav>
            <nav class="sidebar-navigation side-nav">
                <a href="../Profile/Profile.php" class="icon-button"><i class="ph-user-bold"></i> Profile</a>
                <a href="logout.php" class="icon-button"><i class="ph-sign-out-bold"></i> Logout</a>
            </nav>
        </div>
    </div>

    <main class="main">
        <div class="responsive-wrapper">
            <form action="terminalfunction.php" method="post">
                <input type="hidden" name="trainingid" value="<?php echo htmlspecialchars($_GET['trainingid']); ?>">
                <div class="form-container">
                    <div class="header" style="display: flex; align-items: flex-start; gap: 15px;">
                        <div style="flex-shrink: 0;">
                            <img src="../assets/msu-iit-logo.png" alt="MSU-IIT Logo" style="height: 85px; width: auto;">
                        </div>
                        <div style="line-height: 1.2; font-size: 14px; color: black;">
                            <p style="margin: 0;">Republic of the Philippines</p>
                            <p style="margin: 0; font-family: 'Monotype Corsiva', 'Times New Roman', cursive; font-weight: bold; font-size: 16px; color: red;">Mindanao State University</p>
                            <p style="margin: 0; font-weight: bold; color: red;">ILIGAN INSTITUTE OF TECHNOLOGY</p>
                            <p style="margin: 0;">Iligan City 9200 Philippines</p>
                            <p style="margin: 0;">http://www.msuiit.edu.ph</p>
                        </div>
                        <div style="margin-left: auto; text-align: right; font-size: 13px; color: black;">
                            <p style="margin: 0;">L&D Form No. 02</p>
                            <p style="margin: 0;">(TERMINAL REPORT)</p>
                        </div>
                    </div>

                    <div class="dashed-line"></div>

                    <div class="center-title">
                        <p><strong>TERMINAL REPORT</strong></p>
                        <p>(To be submitted to the HRMD by L&D Attendee together with L&D Form No. 3)</p>
                    </div>

                    <div class="checkboxes">
                        <label><input type="checkbox" name="type[]" value="Training"> Training</label><br>
                        <label><input type="checkbox" name="type[]" value="Seminar"> Seminar/Symposium/Workshop/Conference/Convention/Online attendance</label><br>
                        <label><input type="checkbox" name="type[]" value="Others"> Others (please specify): 
                            <input type="text" name="others_specify" style="width: 300px; border: none; border-bottom: 1px solid black;">
                        </label>
                    </div>

                    <div class="form-section">
                        <label>TITLE:</label>
                        <input type="text" name="title" class="form-input" required>
                    </div>

                    <div class="form-section">
                        <label>ORGANIZER/SPONSOR OF PROGRAM:</label>
                        <input type="text" name="sponsor" class="form-input" required>
                    </div>

                    <div class="form-section row">
                        <div class="col">
                            <label>INCLUSIVE DATES:</label>
                            <div class="row">
                                <input type="date" name="fromdate" class="form-input" required>
                                <input type="date" name="todate" class="form-input" required>
                            </div>
                        </div>
                        <div class="col">
                            <label>NO. OF DAYS</label>
                            <input type="number" name="days" class="form-input" required>
                        </div>
                        <div class="col">
                            <label>NO. OF HOURS</label>
                            <input type="number" name="hours" class="form-input" required>
                        </div>
                    </div>

                    <div class="form-section">
                        <label>VENUE:</label>
                        <input type="text" name="venue" class="form-input" required>
                    </div>

                    <div class="form-section">
                        <label>OBJECTIVES:</label>
                        <textarea name="objectives" class="form-input textarea" rows="3" required></textarea>
                    </div>

                    <div class="form-section">
                        <label>BRIEF REPORT OF UNDERTAKING:</label>
                        <textarea name="briefreport" class="form-input textarea" rows="4" required></textarea>
                    </div>

                    <div class="form-section">
                        <label>SYNTHESIS OF LEARNING:</label>
                        <textarea name="synthesis" class="form-input textarea" rows="4" required></textarea>
                    </div>

                    <div class="form-section" hidden>
                        <label>ATTACHMENTS:</label>
                        <p>( ) Photocopy of proof of participation/attendance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;( ) Special Order</p>
                        <p>( ) Recommendation/Action Steps on how to apply the learning in work site.</p>
                    </div>

                    <div class="signature row" style="margin-top: 30px;">
                        <div class="col" hidden>
                            <p><strong>Prepared and Submitted by:</strong></p><br>
                            <div class="signature-line"></div>
                            <p>Attendee/Participant</p>
                        </div>
                        <div class="col" hidden>
                            <p><strong>Reviewed/Evaluated by:</strong></p><br>
                            <div class="signature-line"></div>
                            <p>Cost Center Head</p>
                        </div>
                    </div>

                    <div class="form-section" hidden>
                        <p>Received at HRMD by: ___________________________________</p>
                    </div>
                    <div class="form-section" style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="submit-btn">Submit Terminal Report</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any necessary JavaScript functionality
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="type[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                checkboxes.forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
            }
        });
    });
});

// Add this inside your existing DOMContentLoaded event listener
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate required fields
    const requiredFields = ['title', 'sponsor', 'fromdate', 'todate', 'venue', 'objectives', 'briefreport', 'synthesis'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = 'red';
        } else {
            input.style.borderColor = 'black';
        }
    });

    // Check if at least one checkbox is selected
    const typeChecked = document.querySelectorAll('input[name="type[]"]:checked').length > 0;
    if (!typeChecked) {
        isValid = false;
        document.querySelector('.checkboxes').style.color = 'red';
    } else {
        document.querySelector('.checkboxes').style.color = 'black';
    }

    if (isValid) {
        this.submit();
    } else {
        alert('Please fill in all required fields');
    }
});

// Add this to your existing date calculation code
document.querySelectorAll('input[type="date"]').forEach(input => {
    input.addEventListener('change', function() {
        const fromDate = document.querySelector('[name="fromdate"]').value;
        const toDate = document.querySelector('[name="todate"]').value;
        
        if (fromDate && toDate) {
            const start = new Date(fromDate);
            const end = new Date(toDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            document.querySelector('[name="days"]').value = diffDays;
        }
    });
});
</script>


<script src='https://unpkg.com/phosphor-icons'></script>


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
.sidebar {
  width: 100px; /* Adjust width as needed */
  background-color: var(--c-background-primary);
  height: 100vh; /* Full height */
  padding: 10px;
  position: fixed; /* Fixes sidebar to the left */
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
}

.sidebar {
  width: 200px;
  background-color:white;
  height: 100vh;
  padding: 10px;
  position: fixed;
}

.sidebar-navigation a {
  margin: 10px 0;
  text-decoration: none;
  color:black;
  font-weight: 300;
  transition: color 0.15s ease;
}
.form-container {
    width: 800px;
    margin: 40px 0; /* Changed from auto to 0 */
    border: 1px solid black;
    padding: 30px 40px;
    box-sizing: border-box;
    background: white;
    font-family: "Times New Roman", Times, serif;
    font-size: 14px;
    color: black;
    line-height: 1.3;
}

.dashed-line {
    border-bottom: 2px dashed black;
    margin: 15px 0;
}

.center-title {
    text-align: center;
    margin-top: 10px;
    line-height: 1.2;
}

.center-title strong {
    font-size: 16px;
}

.form-section {
    margin-top: 15px;
    line-height: 1.2;
}

.form-section label {
    font-weight: bold;
    display: block;
    margin-bottom: 3px;
}

.form-input, .textarea {
    width: 100%;
    padding: 5px;
    font-family: "Times New Roman", Times, serif;
    border: 1px solid black;
    box-sizing: border-box;
}

.row {
    display: flex;
    gap: 10px;
    line-height: 1.3;
}

.col {
    flex: 1;
}

.checkboxes {
    margin-top: 10px;
    line-height: 1.3;
}

.signature {
    margin-top: 60px;
    line-height: 2.5;
}

.signature-line {
    border-bottom: 1px solid black;
    width: 70%;
    height: 1px;
    margin-bottom: 5px;
}

/* Adjust main content area */
main {
    margin-left: 250px; /* Increased from 220px */
    padding: 20px 40px; /* Added right padding */
    display: flex;
    justify-content: flex-start; /* Changed from center to flex-start */
}

.responsive-wrapper {
    width: 100%;
    max-width: 800px;
    margin-left: 100    px; /* Added margin to push form right */
}
.submit-btn {
    background-color: maroon;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    font-family: "Times New Roman", Times, serif;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.submit-btn:hover {
    background-color: #540000;
}

.submit-btn:active {
    transform: translateY(1px);
}        
    </style>
</body>
</html>