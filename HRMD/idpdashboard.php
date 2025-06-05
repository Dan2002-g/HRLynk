<?php
session_start();
include('connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

$userID = $_SESSION['user'];

// Fetch filter values
$selectedYear = $_POST['year'] ?? '';
$selectedOffice = $_POST['office'] ?? '';

// Build the SQL query with filters
$sql = "SELECT idp.id, users.empname, idp.created_at, idp.status
        FROM idp
        JOIN users ON idp.userID = users.userID
        JOIN user_details ON users.userID = user_details.userID"; // Join user_details to access officeID

$conditions = [];
$params = [];

if (!empty($selectedYear)) {
    $conditions[] = "YEAR(idp.created_at) = :year";
    $params[':year'] = $selectedYear;
}

if (!empty($selectedOffice)) {
    $conditions[] = "user_details.officeID = :office"; // Use user_details.officeID
    $params[':office'] = $selectedOffice;
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY idp.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$trainingResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If this is an AJAX request, return only the table rows
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($trainingResult as $row) {
        echo "<tr>
                <td>" . htmlspecialchars($row['empname']) . "</td>
                <td data-label='Individual Development Plan'>Individual Development Plan (" . date('Y', strtotime($row['created_at'])) . ")</td>
                <td>" . date('F-d-Y | h:i A', strtotime($row['created_at'])) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td data-label='Action'>
                    <button type='button' class='view-btn' data-bs-toggle='modal' data-bs-target='#viewModal' data-id='" . $row['id'] . "'>
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
    <title>Individual Development Plan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Add Bootstrap CSS for Modal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <a href="../Homepage/index.html" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
            <span style="color: maroon;">HR</span><span style="color: gold; text-shadow: 1px 1px maroon;">Lynk</span>
            <a href="../Homepage/logout.php" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
                    <h4>HRMD Portal</h4>
            </a>
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
            <h1>Individual Development Plan Dashboard</h1>
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Learning & Development Form</th>
                                    <th>Date Submitted</th>
                                    <th>Status</th>
                                    <th>Action <br> (Approved or Rejected)</th>
                                </tr>
                            </thead>
                            <tbody id="idpTableBody">
                                <?php if (count($trainingResult) > 0): ?>
                                    <?php foreach ($trainingResult as $row) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['empname']); ?></td>
                                            <td data-label="Individual Development Plan">
                                                <?php
                                                $year = date('Y', strtotime($row['created_at']));
                                                echo "Individual Development Plan ($year)";
                                                ?>
                                            </td>
                                            <td><?php echo date('F-d-Y | h:i A', strtotime($row['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                                            <td data-label="Action">
                                                <button type="button" class="view-btn" data-bs-toggle="modal" data-bs-target="#viewModal" data-id="<?php echo $row['id']; ?>">
                                                    <i class="bi bi-eye"></i> View Details
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">No IDP records found.</td>
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

<!-- Modal Structure -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewModalLabel">IDP Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalContent">
        <!-- The IDP details will be dynamically loaded here -->
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Javascript modals -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // When the view button is clicked, fetch IDP details using AJAX
  document.querySelectorAll('.view-btn').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const modalContent = document.getElementById('modalContent');
        modalContent.innerHTML = '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>';

        // Make an AJAX request to fetch the details from view_idp.php
        fetch('view_idp.php?id=' + id)
            .then(response => response.text())
            .then(data => {
                modalContent.innerHTML = data; // Populate the modal with the data from view_idp.php
            })
            .catch(error => {
                modalContent.innerHTML = 'Error loading details. Please try again.';
            });
    });
});
    // Handle form changes dynamically
    document.getElementById('filterForm').addEventListener('change', function () {
        const formData = new FormData(this);

        fetch('idpdashboard.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('idpTableBody').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
    });
</script>
<script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
<script src='https://unpkg.com/phosphor-icons'></script>

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
h4 {
            font-size: 20px;
            font-weight: 500;
            color: gold;
            text-align: center;
            margin-top: 20px;
            shadow: 0.5px 0.5px maroon;
            text-shadow: 0.5px 0.5px maroon;
        }
/* CSS modals */
 /* Modal Adjustments */
 .modal-dialog {
    width: 100%; /* Make modal take full width */
    max-width: 1200px; /* Set maximum width (you can adjust this as needed) */
    height: 90vh; /* Make modal take up 90% of the viewport height */
    max-height: 90vh; /* Set max height to prevent excessive overflow */
    margin: 0 auto; /* Center the modal in the viewport */
}
 .modal-content {
    background-color: #ffffff; /* White background for better readability */
    color: #333333; /* Text color */
    max-width: 100%; /* Ensure the content uses the full modal width */
    max-height: 100%; /* Ensure content takes up the full height */
    overflow-y: auto; /* Allows vertical scrolling for content that overflows */
}

.modal-body {
    height: 100%; /* Make modal body take up the full height */
    padding: 20px; /* Add some padding to make it look nicer */
    overflow: auto; /* Prevent overflow inside the modal */
    font-size: 16px; /* Set the font size */
}

.modal-header {
    background-color: maroon; /* Set background color for the header and footer */
    color: white; /* Set text color to white for contrast */
    border: none;
}

.modal-title {
    font-size: 20px; /* Set font size for the title */
}

.btn-close {
    background-color: transparent;
    color: white; /* Close button text color */
}

/* Optional: Enhance the button style */
.view-btn {
    background-color: maroon;
    color: white;
    border-radius: 5px;
    padding: 8px 15px;
    border: none;
}

.view-btn:hover {
    background-color: #800000;
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



/* Enhancing the table with black text for the content */
/* Enhancing the table with borders and shadow */
.table-responsive {
  overflow-x: auto;
}

.table {
  width: 1500px;
  max-width: 1500px;
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
@media screen and (max-width: 1500px) {
  .table thead {
    display: none; /* Hide headers on small screens */
  }

  .table, 
  .table tbody, 
  .table tr, 
  .table td {
    display: block;
    width: 1500px;
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
</style>

</body>
</html>