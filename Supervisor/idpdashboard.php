<?php
session_start();
include('connection.php');

// Check if the user is logged in and the userID is set in the session
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

$userID = $_SESSION['user']; // You can still keep this if you need to refer to the logged-in user's ID

// Adjusted SQL Query to retrieve all submitted IDPs for all employees (supervisor's view)
$stmt = $conn->prepare("SELECT idp.id, users.empname, idp.created_at, idp.status
                        FROM idp
                        JOIN users ON idp.userID = users.userID
                        ORDER BY idp.created_at DESC");
$stmt->execute();
$trainingResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            <h1>Individual Development Plan Dashboard</h1>
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
                                    <th>Action <br> (Approve/Reject)</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                            <!-- Modified button to include a view icon -->
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
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Rejection Remarks</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="reject-id">
        <label for="remarks" class="form-label">Please provide reason for rejection:</label>
        <textarea class="form-control" id="remarks" rows="4" required></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="submitReject">Submit Rejection</button>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript for AJAX loading -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('.view-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const modalContent = document.getElementById('modalContent');
        modalContent.innerHTML = '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>';

        fetch('view_idp.php?id=' + encodeURIComponent(id))
            .then(response => response.text())
            .then(data => {
                modalContent.innerHTML = data.trim() !== '' ? data : 'No data found.';
            })
            .catch(() => {
                modalContent.innerHTML = 'Error loading details. Please try again.';
            });
    });
  });
</script>

<script>
  document.addEventListener('click', function (e) {
    // ✅ Handle APPROVE click
    if (e.target && e.target.classList.contains('approve-btn')) {
        const id = e.target.getAttribute('data-id');
        if (!id) {
            alert("Missing ID");
            return;
        }

        if (!confirm("Are you sure you want to approve this IDP?")) return;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', 'Approved');
        formData.append('remarks', ''); // Clear remarks or you can keep existing if needed

        fetch('update.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating status.');
        });
    }

    // ✅ Already working reject code (keep this if not already done):
    if (e.target && e.target.classList.contains('reject-btn')) {
        const id = e.target.getAttribute('data-id');
        document.getElementById('reject-id').value = id;
        document.getElementById('remarks').value = ''; // clear old remarks
    }
});
document.addEventListener('click', function (e) {
    // Listen for click on .reject-btn from AJAX content
    if (e.target && e.target.classList.contains('reject-btn')) {
        const id = e.target.getAttribute('data-id');
        document.getElementById('reject-id').value = id;
        document.getElementById('remarks').value = ''; // Clear any previous input
    }
});

document.getElementById('submitReject')?.addEventListener('click', () => {
    const remarks = document.getElementById('remarks').value.trim();
    const id = document.getElementById('reject-id').value;

    if (!remarks) {
        alert('Please provide rejection remarks.');
        return;
    }

    if (!id) {
        alert('ID is missing.');
        return;
    }

    const formData = new FormData();
    formData.append('id', id);
    formData.append('status', 'Rejected');
    formData.append('remarks', remarks);

    fetch('update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            const rejectModal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
            rejectModal.hide();
            location.reload(); // Refresh to show updated table
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status.');
    });
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
    font-size: 18px;
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
  font-size: 18px; /* Larger font size for headers */
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
h4 {
    font-size: 20px;
    font-weight: 500;
    color: gold;
    text-align: center;
    margin-top: 20px;
    shadow: 0.5px 0.5px maroon;
    text-shadow: 0.5px 0.5px maroon;
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