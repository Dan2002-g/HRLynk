<?php
include('connection.php');
include('learningfunction.php');
// Check if the user is logged in
if(!isset($_SESSION['user'])) {
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

$userID = $_SESSION['user'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learning Application Plan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>

    <div class="sidebar">
        <div class="logo-main">
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
    <div class="header">
    <img src="../assets/msu-iit-logo.png" class="logo" alt="logo">
    <div class="header-text">
      <div>Republic of the Philippines</div>
      <p style="margin: 0; font-family: 'Monotype Corsiva', 'Times New Roman', cursive; font-weight: bold; font-size: 16px; color: red;">Mindanao State University</p>
      <p style="margin: 0; font-weight: bold; color: red;">ILIGAN INSTITUTE OF TECHNOLOGY</p>
      <div>Iligan City 9200 Philippines</div>
      <a href="http://www.msuiit.edu.ph">http://www.msuiit.edu.ph</a>
    </div>
    <div style="text-align: right; font-size: 12px; color: red;">L&D Form No. 3</div>
  </div>

  <hr style="border-top: 2px dashed black;">

  <h4>LEARNING APPLICATION PLAN</h4>
  <div style="text-align: center;">
    (To be submitted to HRMD together with Terminal Report of attendee)<br>
    <span style="color: green;">[CSC PRIME-HRM Evidence]</span>
  </div>

<!-- Replace the checkbox section and form container with this -->
<form action="learningfunction.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="trainingid" value="<?php echo htmlspecialchars($_GET['trainingid'] ?? ''); ?>">
    
    <!-- CHECKBOX SECTION -->
    <div class="inline-checkbox">
        <input type="checkbox" name="type[]" value="Training"> Training
    </div>
    <div class="inline-checkbox">
        <input type="checkbox" name="type[]" value="Seminar"> Seminar/Symposium/Workshop/Conference/Convention/Online attendance
    </div>
    <div class="inline-checkbox">
        <input type="checkbox" name="type[]" value="Others"> Others (please specify):
        <input type="text" name="other_type" style="width: 50%; display: inline-block;">
    </div>

    <!-- FORM CONTAINER -->
    <div class="form-container">
        <div class="section-title">TITLE:</div>
        <input type="text" name="title" required>

        <div class="section-title">ORGANIZER/SPONSOR OF PROGRAM:</div>
        <input type="text" name="organizer" required>

        <div class="section-title">INCLUSIVE DATES:</div>
        <table>
            <tr>
                <td><strong>FROM</strong><br>
                    <input type="date" name="from_date" id="fromDate" onchange="calculateDays()" required>
                </td>
                <td><strong>TO</strong><br>
                    <input type="date" name="to_date" id="toDate" onchange="calculateDays()" required>
                </td>
                <td><strong>NO. OF DAYS</strong><br>
                    <input type="text" name="num_days" id="numDays" readonly required>
                </td>
                <td><strong>NO. OF HOURS</strong><br>
                    <input type="number" name="num_hours" required>
                </td>
            </tr>
        </table>

        <div class="section-title">VENUE:</div>
        <input type="text" name="venue" required>

        <div class="section-title">BRIEF LISTING OF LEARNING:</div>
        <textarea name="brief_learning" rows="4" required></textarea>

        <div class="section-title">RECOMMENDATION/ACTION STEPS TO APPLY LEARNING AT WORK:</div>
        <table>
            <tr>
                <th>Function</th>
                <th>Activity</th>
                <th>Period</th>
                <th>Resource Needed</th>
                <th>Monitoring & Evaluation</th>
            </tr>
            <tr>
                <td><textarea name="function" class="expandable" required></textarea></td>
                <td><textarea name="activity" class="expandable" required></textarea></td>
                <td><textarea name="period" class="expandable" required></textarea></td>
                <td><textarea name="resource_needed" class="expandable" required></textarea></td>
                <td><textarea name="moneval" class="expandable" required></textarea></td>
            </tr>
        </table>

    <p class="commitment" >We hereby commit ourselves to implement this recommendation or action steps.</p>

    <div style="text-align: center;" >
      <div class="sign-line">Employee</div>
      <div class="sign-line" style="margin-left: 30px;">Supervisor</div>
    </div>
  </div>

  <!-- ACKNOWLEDGMENT LINE OUTSIDE BORDER -->
  <p class="hrmd-receipt" style="margin-top: 30px;" >Received copy for HRMD-L&D by: ___________________________</p>
        <!-- File upload section -->
        <div class="file-upload-section">
            <p class="section-title">ATTACHMENTS:</p>
            <div class="file-requirements">
                <p>(1) Photocopy of proof of participation/attendance</p>
                <p>(2) Special Order</p>
            </div>
            <div class="file-input-container">
                <input type="file" 
                    name="files[]" 
                    multiple 
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                    class="file-input"
                    required>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" name="learning_application" class="submit-btn">Submit Learning Application</button>
        </div>
    </div>
</form>
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
    function calculateDays() {
      const from = new Date(document.getElementById('fromDate').value);
      const to = new Date(document.getElementById('toDate').value);
      if (!isNaN(from) && !isNaN(to)) {
        const timeDiff = to.getTime() - from.getTime();
        const days = Math.floor(timeDiff / (1000 * 3600 * 24)) + 1;
        document.getElementById('numDays').value = days > 0 ? days : 0;
      }
    }
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
.logo-main {
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
  color:rgb(0, 0, 0);
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

/* Main content styles */
    .main {
        margin-left: 300px; /* Keep sidebar space */
        padding: 20px;
        flex-direction: column;
        align-items: center; /* Center the content */
        font-family: 'Times New Roman', Times, serif; /* Default font for form */
    }
    .header, .header * {
      font-weight: normal !important;
    }

    /* Exceptions: Form input and textarea */
    input[type="text"],
    input[type="date"],
    textarea {
      font-weight: normal;
    }

    /* Exception: Final acknowledgment line */
    .hrmd-receipt {
      font-weight: normal;
    }

    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .logo {
      width: 85px;
      margin-right: 10px;
    }
    .header-text {
      flex: 1;
      text-align: left;
      line-height: 1.1;
    }
    .header-text h2 {
      color: red;
      font-family: 'Monotype Corsiva', cursive;
      margin: 0;
    }
    .header-text h3 {
      color: #ED1C24;
      margin: 0;
    }
    .header-text a {
      color: #FBBF18;
      text-decoration: none;
    }
    .form-container {
      border: 1px solid black;
      padding: 15px;
      margin-top: 10px;
    }
    h4 {
      text-align: center;
      margin-bottom: 5px;
      font-size: 16px;
      font-weight: bold;
    }
    .section-title {
      margin-top: 10px;
      font-size: 12px;
    }
    input[type="text"], input[type="date"], textarea {
      width: 100%;
      border: none;
      border-bottom: 1px solid black;
      font-family: 'Times New Roman';
    }
    textarea.expandable {
      resize: vertical;
      min-height: 50px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 5px;
    }
    td, th {
      border: 1px solid black;
      padding: 5px;
      text-align: center;
      font-size: 12px;
    }
    .commitment {
      text-align: left;
      margin-top: 10px;
    }
    .sign-line {
      width: 40%;
      border-top: 1px solid black;
      margin-top: 40px;
      display: inline-block;
      text-align: center;
    }
    .inline-checkbox {
      margin: 5px 0;
    }
    .section-title,
    .commitment,
    .inline-checkbox,
    table,
    th,
    td,
    .sign-line {
        font-weight: bold;
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