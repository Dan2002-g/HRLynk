<?php

include('trainingfunction.php');

// Check if the user is logged in and the userID is set in the session
if (!isset($_SESSION['user'])) {
    // User is not authenticated, redirect to login page or display an error message
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

$userID = $_SESSION['user'];

// Fetch necessary columns from the training table and join with learningapplication table
$stmt = $conn->prepare("SELECT t.trainingid, t.trainingtitle, t.trainingdate, t.submitted_at, t.file_path, t.status, la.learningapp_id 
                        FROM training t 
                        LEFT JOIN learningapplication la ON t.trainingid = la.trainingid 
                        WHERE t.userID = :userID");
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();

$trainingResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>External Training Request</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
/* Styles for Training Details Section */
#trainingDetailsSection {
    display: none;
    margin-top: 20px;
}
#trainingDetailsSection .card {
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}
#trainingDetailsSection .card-header {
    background-color: maroon;
    color: white;
}
#trainingDetailsSection .card-body {
    color: black;
}
#trainingDetailsSection .card-footer {
    text-align: right;
}

/* Styles for Terminal Report and Learning Application Modals */
#terminalModal.terminal-report-modal .modal-dialog,
#learningAppModal.learning-app-modal .modal-dialog {
    max-width: 80vw;
    height: auto;
    max-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
#terminalModal.terminal-report-modal .modal-content,
#learningAppModal.learning-app-modal .modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    max-height: 90vh;
    overflow-y: auto;
}
#terminalModal.terminal-report-modal .modal-header,
#learningAppModal.learning-app-modal .modal-header {
    background-color: maroon;
    color: white;
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
}
#terminalModal.terminal-report-modal .modal-body,
#learningAppModal.learning-app-modal .modal-body {
    max-height: 75vh;
    overflow-y: auto;
    padding: 15px;
}
#terminalModal.terminal-report-modal .modal-footer,
#learningAppModal.learning-app-modal .modal-footer {
    border-top: 1px solid #dee2e6;
    padding: 15px;
}

/* Update modal backdrop to gray with reduced opacity and no blur */
.modal-backdrop {
    background-color: rgba(128, 128, 128, 0.5); /* Gray background with 50% opacity */
    backdrop-filter: none; /* Remove blur effect */
}
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <a href="../Homepage/index.html" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
            <span style="color: maroon;">HR</span><span style="color: gold; text-shadow: 1px 1px maroon;">Lynk</span>
        </a>
        <a href="../Homepage/logout.php" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
        <h4>Employee Portal</h4>
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
            <a href="../Profile/profiledisplay.php" class="icon-button" aria-label="Profile"><i class="bi bi-person-circle"></i> Profile</a>
            <a href="../Homepage/logout.php" class="icon-button" aria-label="Logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
    </div>
</div>

<main class="main">
    <div class="responsive-wrapper">
        <div class="main-header">
            <h1>External Training Request History</h1>
        </div>
        <div class="horizontal-tabs">
            <a class="horizontal" href="trainingdashboard.php">Dashboard</a>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="table-responsive" data-pattern="priority-columns">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Training Date</th>
                                    <th>Attachments</th>
                                    <th>Date Submitted</th> 
                                    <th>Status</th> <!-- Moved Status column -->
                                    <th>Documents</th> <!-- Moved Documents column -->
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (count($trainingResult) > 0): ?>
                                <?php foreach ($trainingResult as $training) : ?>
                                    <tr>
                                        <td>
                                            <a href="#" class="training-title-link" data-trainingid="<?php echo $training['trainingid']; ?>">
                                                <?php echo htmlspecialchars($training['trainingtitle']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars(date("F j, Y", strtotime($training['trainingdate']))); ?></td>
                                        <td>
                                            <?php
                                            // Calculate attachments dynamically
                                            $fileCount = 0;
                                            if (!empty($training['file_path'])) {
                                                // Split file paths and count
                                                $filePaths = array_filter(explode(',', $training['file_path'])); // Remove empty values
                                                $fileCount = count($filePaths);
                                            }

                                            // Display file count
                                            echo "$fileCount/2"; 

                                            // Dynamically display "attachment status"
                                            if ($fileCount >= 2) {
                                                echo " <span style='color: green;'>Complete</span>";
                                            } else {
                                                // Display "Submit Missing Files" button if not complete
                                                echo "<br><button class='btn btn-warning' onclick=\"window.location.href='missingfiles.php?trainingid=" . $training['trainingid'] . "'\">
                                                        Submit Missing Files
                                                      </button>";
                                            }
                                            ?>
                                        </td>

                                        <td><?php echo htmlspecialchars(date("F j, Y h:i A", strtotime($training['submitted_at']))); ?></td>

                                        <td><?php echo htmlspecialchars($training['status']); ?></td> <!-- Moved Status column -->
                                        <td>
                                        <?php
                                        // Check if terminal report and learning application plan exist
                                        $stmt_terminal = $conn->prepare("SELECT COUNT(*) FROM terminal WHERE trainingid = :trainingid");
                                        $stmt_terminal->bindParam(':trainingid', $training['trainingid'], PDO::PARAM_INT);
                                        $stmt_terminal->execute();
                                        $terminal_exists = $stmt_terminal->fetchColumn() > 0;

                                        $stmt_learningapp = $conn->prepare("SELECT COUNT(*) FROM learningapplication WHERE learningapp_id = :learningapp_id");
                                        $stmt_learningapp->bindParam(':learningapp_id', $training['learningapp_id'], PDO::PARAM_INT);
                                        $stmt_learningapp->execute();
                                        $learningapp_exists = $stmt_learningapp->fetchColumn() > 0;                                        

                                        if ($terminal_exists) {
                                            echo "<button class='btn btn-success view-terminal-btn' data-trainingid='" . $training['trainingid'] . "' data-bs-toggle='modal' data-bs-target='#terminalModal'>
                                                    <i class='bi bi-eye'></i> Terminal Report
                                                  </button>";
                                        } else {
                                            echo "<button class='btn btn-primary add-terminal-btn' onclick=\"window.location.href='../Terminal Report/terminalform.php?trainingid=" . $training['trainingid'] . "'\">
                                                    <i class='bi bi-plus-circle'></i> Terminal Report
                                                  </button>";
                                        }

                                        echo "<br><br>";

                                        if ($learningapp_exists) {
                                            echo "<button class='btn btn-success view-learningapp-btn' data-trainingid='" . $training['trainingid'] . "' data-bs-toggle='modal' data-bs-target='#learningAppModal'>
                                                    <i class='bi bi-eye'></i> Learning Application Plan
                                                  </button>";
                                        } else {
                                            echo "<button class='btn btn-primary add-learningapp-btn' onclick=\"window.location.href='../Terminal Report/learningapplication.php?trainingid=" . $training['trainingid'] . "'\">
                                                    <i class='bi bi-plus-circle'></i> Learning Application Plan
                                                  </button>";
                                        }
                                        ?>
                                    </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" style="text-align: center;">No training records found.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>  

                        <div><a class="horizontal-button" href="trainingform.php">
                            New Training<i class="bi bi-plus-circle"></i> 
                        </a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal for Viewing Terminal Report -->
<div class="modal fade terminal-report-modal" tabindex="-1" id="terminalModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Terminal Report</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="terminalModalContent">
        <!-- Terminal report details will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Viewing Learning Application Plan -->
<div class="modal fade learning-app-modal" tabindex="-1" id="learningAppModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Learning Application Plan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="learningAppModalContent">
        <!-- Learning application plan details will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Viewing Training Details -->
<div class="modal fade training-details-modal" tabindex="-1" id="viewModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width: auto; max-width: 90%; height: auto; max-height: 90vh; display: flex; align-items: center; justify-content: center;">
        <div class="modal-content">
            <div class="modal-header" style="background-color: maroon; color: white;">
                <h5 class="modal-title">Training Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalContent" style="max-height: 75vh; overflow-y: auto;">
                <!-- Training details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
$(document).ready(function () {
    // Prevent layout shift when modal is opened
    $('body').on('show.bs.modal', function () {
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        $('body').css('padding-right', scrollbarWidth + 'px');
    });

    $('body').on('hidden.bs.modal', function () {
        $('body').css('padding-right', '');
    });

    // Load Terminal Report via AJAX
    $('.view-terminal-btn').click(function () {
        var trainingid = $(this).data('trainingid');
        $('#terminalModalContent').html('<p class="text-center">Loading...</p>');

        $.ajax({
            url: 'preview.php', // Fetch from preview.php
            type: 'GET',
            data: { trainingid: trainingid },
            success: function (response) {
                $('#terminalModalContent').html(response);
            },
            error: function () {
                $('#terminalModalContent').html('<p class="text-danger text-center">Failed to load data.</p>');
            }
        });
    });

    // Load Learning Application Plan via AJAX
    $('.view-learningapp-btn').click(function () {
        var trainingid = $(this).data('trainingid');
        $('#learningAppModalContent').html('<p class="text-center">Loading...</p>');

        $.ajax({
            url: 'learningapp_view.php', // Fetch from learningapp_view.php
            type: 'GET',
            data: { trainingid: trainingid },
            success: function (response) {
                $('#learningAppModalContent').html(response);
            },
            error: function () {
                $('#learningAppModalContent').html('<p class="text-danger text-center">Failed to load data.</p>');
            }
        });
    });

    // Load Training Details via AJAX into the modal
    $('.training-title-link').click(function (e) {
        e.preventDefault();
        var trainingid = $(this).data('trainingid');
        $('#viewModalContent').html('<p class="text-center">Loading...</p>');

        $.ajax({
            url: 'view.php', // Fetch from view.php
            type: 'GET',
            data: { trainingid: trainingid },
            success: function (response) {
                $('#viewModalContent').html(response);
                $('#viewModal').modal('show'); // Show the modal
            },
            error: function () {
                $('#viewModalContent').html('<p class="text-danger text-center">Failed to load data.</p>');
            }
        });
    });
});
</script>

</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
<script src='https://unpkg.com/phosphor-icons'></script>
<script src="./script.js"></script>


<style type="text/css">@import url("https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");
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
  color: maroon;
  font-weight: 500;
  transition: color 0.15s ease;
}

.submit-button {
    
    color: white; /* White text */
    padding: 10px 20px; /* Top-bottom and left-right padding */
    text-align: center; /* Centered text */
    text-decoration: none; /* Remove underline */
    display: inline-block; /* Allow block elements to sit inline */
    font-size: 12px; /* Font size */
    border-radius: 8px; /* Rounded corners */
    border: none; /* Remove default border */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s, transform 0.3s; /* Smooth transitions */
}

.submit-btn {
    background-color: maroon; /* Maroon background */
    color: white; /* White text */
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 5px; /* Rounded corners */
    font-weight: bold;
    transition: background-color 0.3s, color 0.3s, border-color 0.3s; /* Smooth transition */
}
.submit-button:hover {
    background-color:gold; /* Darker green on hover */
   
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
.sidebar {
  width: 200px;
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

h1 {
  color: maroon;
  font-size: 30px !important;
  font-weight: 800;
}

.feature h2 {
  color: maroon;
}

.feature a {
  color: gold;
  text-shadow: 0.5px 0.5px maroon;
}

h4 {
    font-size: 20px;
    font-weight: 500;
    color: gold;
    text-align: center;
    margin-top: 20px;
    shadow: 0.5px 0.5px maroon;
    text-shadow: 0.5px 0.5px maroon;
}

/* Modal background */
#previewModal {
  display: none; /* Initially hidden */
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent dark background */
  z-index: 9999; /* Ensures it appears on top */
  overflow: hidden; /* Prevent scrolling of the background */
}

/* Modal content container */
#previewModal .modal-content {
  position: relative;
  background-color: #fff;
  padding: 20px;
  margin: 10% auto;
  width: auto; /* Set the width of the modal */
  height: auto;
  max-width: 1200px; /* Maximum width */
  max-height: 1000px;
  border-radius: 8px; /* Rounded corners */
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  overflow: hidden; /* Prevent scrolling within the modal content */
}

/* Close button */
#previewModal .close-btn {
  position: absolute;
  top: 20px;
  right: 30px;
  font-size: 70px;
  color: #333;
  font-weight: bold;
  cursor: pointer;
  transition: color 0.3s ease;
}

#previewModal .close-btn:hover {
  color:white; /* Change color when hovering */
}

/* Modal iframe styling */
#previewModal #modal-iframe {
  width: 100%;
  height: auto; /* Let the iframe adjust its height automatically */
  min-height: 400px; /* Ensure iframe has a minimum height */
  border: none;
  border-radius: 5px; /* Slightly round the edges of the iframe */
}

/* Responsive styling */
@media (max-width: 768px) {
  #previewModal .modal-content {
    width: 95%; /* Make modal narrower on smaller screens */
  }

  #previewModal .close-btn {
    font-size: 24px; /* Smaller close button on mobile */
  }

  #previewModal #modal-iframe {
    height: auto; /* Let iframe height adjust on mobile */
  }

  .submit-button.submit-btn {
    font-size: 14px; /* Smaller button on mobile */
    padding: 8px 16px; /* Adjust button padding */
  }
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
    color: black;
}
.horizontal:hover{
    color: maroon !important;
}

.horizontal-position{
    align-content: left;
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

.view-btn {
    background-color: maroon;  /* Maroon background */
    color: white;               /* Gold text */
    border: 1px solid maroon;  /* Maroon border */
    padding: 5px 15px;         /* Button padding */
    border-radius: 5px;        /* Rounded corners */
    font-weight: bold;         /* Bold text */
    transition: background-color 0.3s, color 0.3s, border-color 0.3s; /* Smooth transition */
}

.view-btn:hover {
    background-color: gold;    /* Gold background on hover */
    color: maroon;             /* Maroon text on hover */
    border-color: gold;        /* Gold border on hover */
}

.custom-close {
  background: transparent;
  border: none;
  font-size: 1.5rem;
  color: #000;
  font-weight: bold;
  position: absolute;
  right: 10px;
  top: 10px;
  cursor: pointer;
  padding: 0;
}

.custom-close:hover {
  color:white; /* Optional: change color on hover */
}

/* Enhancing the table with black text for the content */
/* Enhancing the table with borders and shadow */
.table-container {
    display: flex;
    gap: 20px; /* Space between the tables */
    flex-wrap: wrap; /* Wraps tables on smaller screens */
    margin-left: 0;
}

.table-responsive {
    flex: 1; /* Makes each table take equal space */
    min-width: 300px; /* Minimum width to maintain responsiveness */
}

/* Optional: Add media query for responsive behavior on smaller screens */
@media (max-width: 768px) {
    .table-container {
        flex-direction: column; /* Stacks tables vertically on small screens */
    }
}
a {
  color:maroon;
  text-decoration: none;
}
.table {
  width: 100%;
  max-width: 100%;
  border-collapse: collapse;
  margin-bottom: 1rem;
  background-color: transparent;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow around the table */
}

.table-bordered {
  border: 2px solid maroon; /* Darker border around the table */
  border-radius: 10px; /* Rounded corners */
  overflow: hidden; /* Ensure rounded corners stay when scrolling */
}

.table-hover tbody tr:hover {
  background-color: rgba(128, 0, 0, 0.1); /* Light hover effect */
}

.table th, 
.table td {
  padding: 12px 15px; /* Add padding for better spacing */
  text-align: left; /* Align text to the left */
  border-top: 1px solid #ddd; /* Border between rows */
}

.table th {
  background-color: maroon; /* Dark header background */
  color: white; /* White text for headers */
  font-size: 18px;
  font-weight: bold;
  border-bottom: 2px solid maroon; /* Border under the header */
}

.table td {
  color: black; /* Black text for the table content */
  border: 1px solid #ddd; /* Light border between table cells */
  font-size: 18px;
}

.table-striped tbody tr:nth-of-type(odd) {
  background-color: #f9f9f9; /* Alternating row background */
}

.table-striped tbody tr:nth-of-type(even) {
  background-color: #fdfdfd;
}

.table-bordered th, 
.table-bordered td {
  border: 1px solid #ddd; /* Borders between cells */
}

.table caption {
  caption-side: top;
  font-size: 1.2em;
  padding: 10px;
  text-align: center;
  color: maroon;
}

/* Responsive design for small screens */
@media screen and (max-width: 767px) {
  .table thead {
    display: none; /* Hide headers on small screens */
  }

  .table, 
  .table tbody, 
  .table tr, 
  .table td {
    display: block;
    width: 100%;
  }

  .table td {
    text-align: right;
    position: relative;
    padding-left: 50%;
    white-space: nowrap;
  }

  .table td::before {
    content: attr(data-label);
    position: absolute;
    left: 0;
    width: 45%;
    padding-left: 15px;
    font-weight: bold;
    text-align: left;
    white-space: nowrap;
  }


}
#terminalModal .modal-dialog {
    max-width: 80vw; /* Increase width to 80% of viewport */
}
#terminalModal .modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    width: 90%; /* Set width to 100% */
    height: auto; /* Set height to auto */
    max-height: 90vh; /* Set maximum height */
    overflow-y: auto; /* Enable vertical scrolling */
}

#terminalModal .modal-header {
    background-color: maroon; /* Maroon background */
    color: white; /* White text */
    border-bottom: 1px solid #dee2e6;
}

#terminalModal .modal-body {
    max-height: 75vh; /* Set maximum height */
    overflow-y: auto; /* Enable vertical scrolling */
    color: black; /* Set font color to black */
}

#terminalModal .modal-footer {
    border-top: 1px solid #dee2e6;
    color: black; /* Set font color to black */
}

/* Similar styles for the Learning Application Plan modal */
#learningAppModal .modal-dialog {
    max-width: 80vw; /* Increase width */
}
#learningAppModal .modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    width: 90%; /* Set width to 100% */
    height: auto; /* Set height to auto */
    max-height: 90vh; /* Set maximum height */
    overflow-y: auto; /* Enable vertical scrolling */
}

#learningAppModal .modal-header {
    background-color: maroon; /* Maroon background */
    color: white; /* White text */
    border-bottom: 1px solid #dee2e6;
}

#learningAppModal .modal-body {
    max-height: 75vh; /* Set maximum height */
    overflow-y: auto; /* Enable vertical scrolling */
    color: black; /* Set font color to black */
}

#learningAppModal .modal-footer {
    border-top: 1px solid #dee2e6;
    color: black; /* Set font color to black */
}
</style>
<script>
// Get the modal
var modal = document.getElementById("myModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the page loads, open the modal automatically
window.onload = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
// Open the Modal with the Preview Document URL
function openModal(trainingid) {
  const modal = document.getElementById('previewModal');
  const iframe = document.getElementById('modal-iframe');
  iframe.src = '../Training Request/preview.php?trainingid=<?php echo $training['trainingid']; ?>';  // Load the document preview in the iframe
  modal.style.display = 'block';
}

// Close the Modal
function closeModal() {
  const modal = document.getElementById('previewModal');
  const iframe = document.getElementById('modal-iframe');
  iframe.src = '';  // Clear the iframe content
  modal.style.display = 'none';
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
  const modal = document.getElementById('previewModal');
  if (event.target == modal) {
    closeModal();
  }
}
$(document).ready(function() {
    // Handle the click event on the View Terminal Report button
    $(".view-terminal-btn").click(function() {
        var trainingId = $(this).data("trainingid");

        // Use AJAX to load the content into the modal
        $.ajax({
            url: "view_terminal.php", // Corrected URL to fetch terminal report data
            type: "GET",
            data: { trainingid: trainingId }, // Send the training ID
            success: function(response) {
                // On success, load the content into the modal
                $("#terminalModalContent").html(response);

                // Show the modal
                var myModal = new bootstrap.Modal(document.getElementById('terminalModal'), {
                    keyboard: true
                });
                myModal.show();
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error);
                console.error("Status: " + status);
                console.dir(xhr);
                alert("An error occurred while loading the terminal report.");
            }
        });
    });

    // Handle the click event on the View Learning Application Plan button
    $(".view-learningapp-btn").click(function() {
        var trainingId = $(this).data("trainingid");

        // Use AJAX to load the content into the modal
        $.ajax({
            url: "view_learningapp.php", // Corrected URL to fetch learning application plan data
            type: "GET",
            data: { trainingid: trainingId }, // Send the training ID
            success: function(response) {
                // On success, load the content into the modal
                $("#learningAppModalContent").html(response);

                // Show the modal
                var myModal = new bootstrap.Modal(document.getElementById('learningAppModal'), {
                    keyboard: true
                });
                myModal.show();
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error);
                console.error("Status: " + status);
                console.dir(xhr);
                alert("An error occurred while loading the learning application plan.");
            }
        });
    });
});
</script>
</body>
</html>