<?php
session_start();
include('connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

// Fetch filter values
$selectedYear = $_POST['year'] ?? '';
$selectedOffice = $_POST['office'] ?? '';

// Build the SQL query with filters
$sql = "SELECT training.trainingid, users.empname, training.trainingtitle, training.trainingdate, training.submitted_at, training.status
        FROM training
        JOIN users ON training.userID = users.userID
        JOIN user_details ON users.userID = user_details.userID"; // Join user_details to access officeID

$conditions = [];
$params = [];

if (!empty($selectedYear)) {
    $conditions[] = "YEAR(training.trainingdate) = :year";
    $params[':year'] = $selectedYear;
}

if (!empty($selectedOffice)) {
    $conditions[] = "user_details.officeID = :office"; // Use user_details.officeID
    $params[':office'] = $selectedOffice;
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY training.trainingdate DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$trainingResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If this is an AJAX request, return only the table rows
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($trainingResult as $row) {
        echo "<tr>
                <td>" . htmlspecialchars($row['empname']) . "</td>
                <td>" . htmlspecialchars($row['trainingtitle']) . "</td>
                <td>" . date('F-d-Y | h:i A', strtotime($row['trainingdate'])) . "</td>
                <td>" . date('F-d-Y | h:i A', strtotime($row['submitted_at'])) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td data-label='Action'>
                    <button type='button' class='view-btn' data-bs-toggle='modal' data-bs-target='#viewModal' data-id='" . $row['trainingid'] . "'>
                        <i class='bi bi-eye'></i> View Details
                    </button>
                </td>
              </tr>";
    }
    exit();
}

try {
    // Fetch office names from the database
    $stmt = $conn->prepare("SELECT officeID, officeName FROM office");
    $stmt->execute();
    $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching offices: " . $e->getMessage();
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
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <a href="../Homepage/index.html" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
            <span style="color: maroon;">HR</span><span style="color: gold; text-shadow: 1px 1px maroon;">Lynk</span>
        </a>
    </div>
    <div class="header-navigation">
        <nav class="sidebar-navigation">
            <a href="index.php" class="sidebar-link active">Home</a>
            <a href="../HRMD/idpdashboard.php" class="sidebar-link">IDP</a>
            <a href="../HRMD/trainingrequestdashboard.php" class="sidebar-link">Training History</a>
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
            <h1>Training Request Dashboard</h1>
        </div>
        <div class="year-menu">
            <form id="filterForm">
                <div class="left-section">
                    <label for="yearSelect">Year:</label>
                    <select id="yearSelect" name="year" class="short-select">
                        <option value="">All Years</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>

                    <label for="officeSelect" style="margin-left: 10px;">Office:</label>
                    <select id="officeSelect" name="office" class="short-select">
                        <option value="">All Offices</option>
                        <?php foreach ($offices as $office): ?>
                            <option value="<?php echo htmlspecialchars($office['officeID']); ?>">
                                <?php echo htmlspecialchars($office['officeName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
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
                                    <th>Date Submitted</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (count($trainingResult) > 0): ?>
                                <?php foreach ($trainingResult as $training) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($training['empname']); ?></td>
                                        <td><?php echo htmlspecialchars($training['trainingtitle']); ?></td>
                                        <td><?php echo date('F-d-Y | h:i A', strtotime($training['trainingdate'])); ?></td>
                                        <td><?php echo date('F-d-Y | h:i A', strtotime($training['submitted_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($training['status']); ?></td>
                                        <td>
                                            <button class="btn btn-primary view-btn" data-trainingid="<?php echo $training['trainingid']; ?>">
                                                <i class="bi bi-eye"></i> View Details
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No training records found.</td>
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
      document.getElementById('filterForm').addEventListener('change', function () {
        const formData = new FormData(this);

        fetch('trainingrequestdashboard.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.querySelector('tbody').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
    });
$(document).ready(function() {
    // Handle the click event on the View button
    $(".view-btn").click(function() {
        var trainingId = $(this).data("trainingid");

        // Use AJAX to load the content into the modal
        $.ajax({
            url: "view.php", // URL to fetch data
            type: "GET",
            data: { trainingid: trainingId }, // Send the training ID
            success: function(response) {
                // On success, load the content into the modal
                $("#viewModalContent").html(response);

                // Show the modal
                var myModal = new bootstrap.Modal(document.getElementById('viewModal'), {
                    keyboard: true
                });
                myModal.show();
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error);
                console.error("Status: " + status);
                console.dir(xhr);
                alert("An error occurred while loading the training details.");
            }
        });
    });

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

    document.getElementById('filterForm').addEventListener('change', function () {
        const formData = new FormData(this);

        fetch('trainingrequestdashboard.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.querySelector('tbody').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
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

<!-- Modal for Viewing Terminal Report -->
<div class="modal fade" tabindex="-1" id="terminalModal" aria-hidden="true">
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
        <!-- Removed close button -->
      </div>
    </div>
  </div>
</div>

<!-- Modal for Viewing Learning Application Plan -->
<div class="modal fade" tabindex="-1" id="learningAppModal" aria-hidden="true">
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
        <!-- Removed close button -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
      function viewDetails(trainingId) {
        // Redirect to a detailed view of the training
        window.location.href = `view.php?trainingid=${trainingId}`;
    }
$(document).ready(function() {
    // Handle the click event on the View button
    $(".view-btn").click(function() {
        var trainingId = $(this).data("trainingid");

        // Use AJAX to load the content into the modal
        $.ajax({
            url: "view.php", // URL to fetch data
            type: "GET",
            data: { trainingid: trainingId }, // Send the training ID
            success: function(response) {
                // On success, load the content into the modal
                $("#modalContent").html(response);

                // Show the modal
                var myModal = new bootstrap.Modal(document.getElementById('trainingModal'), {
                    keyboard: true
                });
                myModal.show();
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error);
                console.error("Status: " + status);
                console.dir(xhr);
                alert("An error occurred while loading the training details.");
            }
        });
    });

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
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
  max-width: 90%; /* Increase the width to 90% of the screen */
  width: 100%; /* Ensure the modal takes the full available width */
}

/* Increase the max-width of the modal content */
.modal-content {
  width: 100%; /* Full width of the modal dialog */
  max-width: 1000px; /* Set a maximum width to make it wider */
  background-color: #fff;
  color: #000;
  border-radius: 10px;
  padding: 20px;
  overflow: auto;
}
/* Modal header and footer style */
.modal-header {
  background-color: maroon;
  color: white;
  border-radius: 10px 10px 0 0; /* Rounded top corners */
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
}

.table td {
  color: black; /* Black text for the table content */
  border: 1px solid #ddd; /* Light border between table cells */
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
.year-menu {
    display: flex;
    justify-content: space-between; /* Align left and right sections */
    align-items: center;
    padding: 10px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #ddd;
}

.left-section {
    display: flex;
    align-items: center;
    gap: 10px; /* Add spacing between elements */
}

.year-menu label {
    font-size: 14px;
    font-weight: bold;
    color: #000;
}

.year-menu select {
    padding: 5px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.short-select {
    width: 100px; /* Shorter width for dropdowns */
}

.right-section {
    display: flex;
    align-items: center;
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
  min-height: calc(100vh - 1rem); /* Adjusts for the modal's height */
  overflow: auto;
  max-width: 95%; /* Increase the width to 95% of the screen */
  width: 100%; /* Ensure the modal takes the full available width */
}

#trainingModal .modal-content {
  width: 100%; /* Full width of the modal dialog */
  max-width: 1600px; /* Set a maximum width to make it wider */
  background-color: #fff;
  color: #000;
  border-radius: 10px;
  padding: px;
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
  min-height: calc(100vh - 1rem); /* Adjusts for the modal's height */
  overflow: auto;
  max-width: 95%; /* Increase the width to 95% of the screen */
  width: 100%; /* Ensure the modal takes the full available width */
}

#terminalModal .modal-content,
#learningAppModal .modal-content {
  width: 100%; /* Full width of the modal dialog */
  max-width: 1400px; /* Set a maximum width to make it wider */
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
  border-radius: 10px 10px 0 0; /* Rounded top corners */
}

#terminalModal .modal-footer,
#learningAppModal .modal-footer {
  background-color: #fdfcff;
  border-radius: 0 0 10px 10px; /* Rounded bottom corners */
}

#terminalModal .btn-close,
#learningAppModal .btn-close {
  color: white;
  background-color: maroon;
  border-color: maroon;
}

/* Ensure modal content looks good on smaller screens */
@media (max-width: 1000px) {
  #terminalModal .modal-dialog,
  #learningAppModal .modal-dialog {
    margin: 0 20px;
    max-width: 100%;
  }

  #terminalModal .modal-content,
  #learningAppModal .modal-content {
    padding: 15px;
  }
}

</style>

</body>
</html>