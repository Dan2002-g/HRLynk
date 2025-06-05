<?php
session_start();
include('connection.php');

// Check if the user is logged in and the userID is set in the session
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

// Fetch all training requests along with employee names
$stmt = $conn->prepare("SELECT training.trainingid, training.trainingtitle, training.trainingdate, training.submitted_at, training.file_path, training.status, users.empname 
                        FROM training 
                        JOIN users ON training.userID = users.userID
                        ORDER BY training.submitted_at DESC");
$stmt->execute();
$trainingResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format the training date and submitted date
function formatDateTime($dateTime) {
    return date("F j, Y - g:i A", strtotime($dateTime));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>External Training Request</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <a href="../Homepage/index.html" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
            <span style="color: maroon;">HR</span><span style="color: gold; text-shadow: 1px 1px maroon;">Lynk</span>
            <a href="../Homepage/logout.php" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
            <h4>Supervisor Portal</h4>
        </a>
    </div>
    <div class="header-navigation">
        <nav class="sidebar-navigation">
            <a href="index.html" class="sidebar-link active">Home</a>
            <a href="../Supervisor/idpdashboard.php" class="sidebar-link">IDP</a>
            <a href="../Supervisor/trainingrequestdashboard.php" class="sidebar-link">Training History</a>
        </nav>
        <nav class="sidebar-navigation side-nav">
            <a href="../Profile/Profile.php" class="icon-button"><i class="ph-user-bold"></i> Profile</a>
            <a href="logout.php" class="icon-button"><i class="ph-sign-out-bold"></i> Logout</a>
        </nav>
    </div>
</div>

<main class="main">
    <div class="responsive-wrapper">
        <div class="main-header">
            <h1>External Training Request History</h1>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="table-responsive" data-pattern="priority-columns">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Title</th>
                                    <th>Training Date</th>
                                    <th>Attachments</th>
                                    <th>Date Submitted</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Action <br>(Approve/Reject)</th>
                                    <th>Documents</th>     
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (count($trainingResult) > 0): ?>
                                <?php foreach ($trainingResult as $training) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($training['empname']); ?></td>
                                        <td><?php echo htmlspecialchars($training['trainingtitle']); ?></td>
                                        <td><?php echo formatDateTime($training['trainingdate']); ?></td>
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
                                              echo "<br><button class='btn btn-warning'>
                                                      Incomplete Files
                                                    </button>";
                                          }
                                          ?>
                                        </td>
                                        <td><?php echo formatDateTime($training['submitted_at']); ?></td>
                                        <td><?php echo htmlspecialchars($training['status']); ?></td>
                                        <td>
                                            <button class="btn btn-primary view-btn"  data-trainingid="<?php echo $training['trainingid']; ?>" style="padding: 5px 10px; font-size: 12px;">
                                                <i class="bi bi-eye" ></i> Request
                                            </button>
                                        </td>
                                        <td>
                                            <?php
                                            // Check if terminal report and learning application plan exist
                                            $stmt_terminal = $conn->prepare("SELECT COUNT(*) FROM terminal WHERE trainingid = :trainingid");
                                            $stmt_terminal->bindParam(':trainingid', $training['trainingid'], PDO::PARAM_INT);
                                            $stmt_terminal->execute();
                                            $terminal_exists = $stmt_terminal->fetchColumn() > 0;

                                            $stmt_learningapp = $conn->prepare("SELECT COUNT(*) FROM learningapplication WHERE trainingid = :trainingid");
                                            $stmt_learningapp->bindParam(':trainingid', $training['trainingid'], PDO::PARAM_INT);
                                            $stmt_learningapp->execute();
                                            $learningapp_exists = $stmt_learningapp->fetchColumn() > 0;

                                            if ($terminal_exists) {
                                                echo "<button class='btn btn-success view-terminal-btn' data-trainingid='" . $training['trainingid'] . "' data-bs-toggle='modal' data-bs-target='#terminalModal' style='padding: 5px 10px; font-size: 12px;'>
                                                    <i class='bi bi-eye'></i> Terminal Report
                                                  </button>";
                                            } else {
                                                echo "<span style='color: red;'>No Terminal Report</span>";
                                            }

                                            echo "<br> <br>";

                                            if ($learningapp_exists) {
                                                echo "<button class='btn btn-success view-learningapp-btn' data-trainingid='" . $training['trainingid'] . "' data-bs-toggle='modal' data-bs-target='#learningAppModal' style='padding: 5px 10px; font-size: 12px;'>
                                                    <i class='bi bi-eye'></i> Learning Application Plan
                                                  </button>";
                                            } else {
                                                echo "<span style='color: red;'>No Learning Application Plan</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="font-size: 18px !important;" >No training records found.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
$(document).ready(function() {
    // Initialize all modals
    var trainingModal = new bootstrap.Modal(document.getElementById('trainingModal'));
    var terminalModal = new bootstrap.Modal(document.getElementById('terminalModal'));
    var learningAppModal = new bootstrap.Modal(document.getElementById('learningAppModal'));
    var viewModal = new bootstrap.Modal(document.getElementById('viewModal'));

    // Handle View Request button
    $(document).on('click', '.view-btn', function(e) {
    e.preventDefault();
    var trainingId = $(this).data('trainingid');
    
    $.ajax({
        url: "view.php",
        type: "GET",
        data: { trainingid: trainingId },
        success: function(response) {
            $("#viewModalContent").html(response); // Ensure this div exists in your modal
            var modal = new bootstrap.Modal(document.getElementById('viewModal'));
            modal.show();
        },
        error: function(xhr, status, error) {
            console.error("Error loading training details:", error);
        }
    });
});

$(document).ready(function() {
    $('.view-terminal-btn').click(function() {
        var trainingid = $(this).data('trainingid');
        
        $('#terminalModalContent').html('<div class="text-center p-4"><div class="spinner-border text-maroon" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        
        $.ajax({
            url: 'view_terminal.php',
            type: 'GET',
            data: { trainingid: trainingid },
            success: function(response) {
                $('#terminalModalContent').html(response);
            },
            error: function() {
                $('#terminalModalContent').html('<div class="alert alert-danger m-3">Failed to load Terminal Report.</div>');
            }
        });
    });

    // Clean up when modal is hidden
    $('#terminalModal').on('hidden.bs.modal', function() {
        $('#terminalModalContent').html('');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });
});

    // Handle View Learning Application button
    $(document).on('click', '.view-learningapp-btn', function(e) {
        e.preventDefault();
        var trainingId = $(this).data('trainingid');
        
        $.ajax({
            url: "view_learningapp.php",
            type: "GET",
            data: { trainingid: trainingId },
            success: function(response) {
                $("#learningAppModalContent").html(response);
                learningAppModal.show();
            },
            error: function(xhr, status, error) {
                alert("Error loading learning application: " + error);
            }
        });
    });
});
</script>
<!-- Modal for Viewing Training Details -->
<div class="modal fade" tabindex="-1" id="trainingModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl"> <!-- Changed to modal-xl for larger size -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Training Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalContent">
        <!-- Training details will be loaded here -->
        <!-- Ensure content fits within the modal -->
      </div>
      <div class="modal-footer">
        <!-- Removed close button -->
      </div>
    </div>
  </div>
</div>

<!-- Modal for Rejection -->
<div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="submit_rejection.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="remarksModalLabel">Reject Training Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden input for trainingid -->
                    <input type="hidden" id="modal_trainingid" name="trainingid" value="">
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // When a reject button is clicked, set the trainingid in the modal
    document.querySelectorAll('.view-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var trainingId = this.getAttribute('data-trainingid');
            // When opening the remarks modal, set the trainingid
            document.getElementById('modal_trainingid').value = trainingId;
        });
    });
});
</script>
<div class="modal fade" id="terminalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body" id="terminalModalContent">
                <!-- Terminal report content will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="learningAppModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="align-items: center; justify-content: center;">
        <div class="modal-content">
            <div class="modal-body" id="learningAppModalContent">
                <!-- Learning application plan content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal for Viewing Training Details -->
<div class="modal fade" tabindex="-1" id="viewModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Training Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="viewModalContent">
        <!-- Training details will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script src='https://unpkg.com/phosphor-icons'></script>

<style type="text/css">@import url("https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap");
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

.sidebar {
  width: 200px;
  background-color: #ffffff;
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
main {
  margin-top: 100px;
  margin-left: 250px; /* Adjust to match the width of the sidebar + some spacing */
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
    font-size: 20px;
    font-weight: 500;
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
    font-size: 18px !important;           /* Font size */
    transition: background-color 0.3s, color 0.3s, border-color 0.3s; /* Smooth transition */
}

.view-btn:hover {
    background-color: gold;    /* Gold background on hover */
    color: maroon;             /* Maroon text on hover */
    border-color: gold;        /* Gold border on hover */
}
/* Ensure that the modal takes up the full height of the viewport */
.modal-dialog {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 1rem); /* Adjusts for the modal's height */
  overflow: auto;
  max-width: 95%; /* Increase the width to 90% of the screen */
  width: 100%; /* Ensure the modal takes the full available width */
}

/* Increase the max-width of the modal content */
.modal-content {
  width: 130%; /* Full width of the modal dialog */
  max-width: 1700px; /* Set a maximum width to make it wider */
  background-color: #fff;
  color: #000;
  border-radius: 10px;
  padding: 20px;
  overflow: auto;
  font-size: 18px; /* Increase font size for better readability */
}
/* Modal header and footer style */
.modal-header {
  background-color: maroon;
  color: white;
  border-radius: 10px 10px 0 0; /* Rounded top corners */
  font-size: 24px; /* Increase font size for header */
  font-weight: bold; /* Bold text for header */
}

.modal-footer {
  background-color: #fdfcff;
  border-radius: 0 0 10px 10px; /* Rounded bottom corners */
}

/* Close button style */
.modal-header .btn-close {
  color: white;
  background-color: maroon;
  border-color: maroon;
}

/* Ensure modal content looks good on smaller screens */
@media (max-width: 767px) {
  .modal-dialog {
    margin: 0 20px; /* Add some margin for small screens */
    max-width: 100%;
  }

  .modal-content {
    padding: 15px; /* Adjust padding on small screens */
  }
}

/* Optional: For adding shadow to the modal content */
.modal-content {
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Light shadow effect */
}

/* Optional: Styling for the backdrop */
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5); /* Darken the background */
}

/* Fix any potential overflow or text clipping */
.modal-body {
  overflow-y: auto;
  color: #000; /* Ensure text in the body is black */
  max-height: 70vh; /* Limit the height of the modal content */
}

/* Ensure modal close button is visible */
.modal-header .btn-close {
  font-size: 1.25rem; /* Increase size of close button */
  background-color: transparent; /* Transparent background */
  border: none; /* Remove border */
  color: white; /* White color */
}

/* Enhancing the table with black text for the content */
/* Enhancing the table with borders and shadow */
.table-container {
    display: flex;
    gap: 20px; /* Space between the tables */
    flex-wrap: wrap; /* Wraps tables on smaller screens */
}
.modal-footer {
    background-color: #fdfcff; /* Light background for footer */
    color: #000; /* Black text for footer */
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
  font-weight: bold;
  border-bottom: 2px solid maroon; /* Border under the header */
  font-size: 18px;
}

.table td {
  color: black; /* Black text for the table content */
  border: 1px solid #ddd; /* Light border between table cells */
  font-size: 18px; /* Font size for table content */

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
/* Modal for Viewing Training Details (View Request) */
#trainingModal .modal-dialog {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 1rem);
  overflow: auto;
  max-width: 90%;
  width: 100%;
}

#trainingModal .modal-content {
  width: 100%;
  max-width: 1000px;
  background-color: #fff;
  color: #000;
  border-radius: 10px;
  padding: 20px;
  overflow: auto;
}

#trainingModal .modal-header {
  background-color: maroon;
  color: white;
  border-radius: 10px 10px 0 0;
}

#trainingModal .modal-footer {
  background-color: #fdfcff;
  border-radius: 0 0 10px 10px;
}

#trainingModal .btn-close {
  color: white;
  background-color: maroon;
  border-color: maroon;
}

/* Shared Modal Design for Terminal Report and Learning Application Plan */
#terminalModal .modal-dialog,
#learningAppModal .modal-dialog {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 1rem);
  overflow: auto;
  max-width: 90%;
  width: 100%;
}

#terminalModal .modal-content,
#learningAppModal .modal-content {
  width: 100%;
  max-width: 1000px;
  background-color: #fff;
  color: #000;
  border-radius: 10px;
  padding: 20px;
  overflow: auto;
}

#terminalModal .modal-header,
#learningAppModal .modal-header {
  background-color: maroon;
  color: white;
  border-radius: 10px 10px 0 0;
}

#terminalModal .modal-footer,
#learningAppModal .modal-footer {
  background-color: #fdfcff;
  border-radius: 0 0 10px 10px;
}

#terminalModal .btn-close,
#learningAppModal .btn-close {
  color: white;
  background-color: maroon;
  border-color: maroon;
}

/* Ensure modal content looks good on smaller screens */
@media (max-width: 767px) {
  #trainingModal .modal-dialog,
  #terminalModal .modal-dialog,
  #learningAppModal .modal-dialog {
    margin: 0 20px;
    max-width: 100%;
  }

  #trainingModal .modal-content,
  #terminalModal .modal-content,
  #learningAppModal .modal-content {
    padding: 15px;
  }
}

/* Adjust modal size and remove scrolling for training details */
#trainingModal .modal-dialog {
  max-width: 1200px; /* Increase modal width */
  width: 100%;
}

#trainingModal .modal-body {
  max-height: none; /* Remove height restriction */
  overflow-y: visible; /* Remove scrolling */
}
</style>

</body>
</html>