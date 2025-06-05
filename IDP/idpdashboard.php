<?php
session_start();
include('connection.php');
$year = date('Y');

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
    header('location: ../Login/index.php');
    exit();
}

// Get the logged-in user's ID
$userID = $_SESSION['user'];

// Fetch IDP records for the logged-in user
$stmt = $conn->prepare("
    SELECT id, created_at, status, remarks
    FROM idp 
    WHERE userID = :userID
");
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$trainingResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the user has an approved IDP
$hasApprovedIDP = false;
foreach ($trainingResult as $row) {
    if (strtolower($row['status']) === 'approved') { // Ensure case-insensitive comparison
        $hasApprovedIDP = true;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Individual Development Plan Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <script src='https://unpkg.com/phosphor-icons'></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    
    <style>
        /* Sidebar Styling */
        .sidebar {
            width: 200px;
            background-color: #fff;
            height: 100vh;
            padding: 10px;
            position: fixed;
        }
        .sidebar-link {
            padding: 10px;
            border-bottom: 1px solid black;
            text-decoration: none;
            color: maroon;
            transition: 0.3s;
        }
        .sidebar-link:hover {
            background-color: maroon;
            color: white;
        }
        
        /* Table Styling */
        .table th {
            background-color: maroon;
            color: white;
        }
        .table td {
            color: black;
        }
        
        /* Buttons */
        .view-btn, .edit-btn {
            background-color: maroon;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .view-btn:hover, .edit-btn:hover {
            background-color: gold;
            color: black;
        }
        

/* Annual Individual Development Plan Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    visibility: hidden;
    opacity: 0;
    transition: visibility 0s, opacity 0.3s ease-in-out;
}
.modal-content {
    background: #fff;
    color: #000;
    padding: 20px;
    width: 90%;
    max-width: 1200px;
    border-radius: 10px;
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    font-size:18px;
}
        .modal-close {
            position: absolute;
            top: 10px;
            right: 30px;
            font-size: 3rem;
            cursor: pointer;
            color: #333;
        }
        .modal-close:hover {
            color: white;
        }
        .modal-show {
    visibility: visible;
    opacity: 1;
}
        .modal-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .modal-table th, 
        .modal-table td {
             border: 2px solid black !important; /* Enforce thick black borders */
             padding: 8px;
             text-align: left;
        }
        .modal-table th {
            background-color: #f2f2f2;
            color: black;
            font-weight: bold;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px; /* Reduced padding from 15px to 10px */
            border-bottom: 1px solid black;
            background-color: maroon;
            color: white;
            font-weight: bold;
            position: sticky; /* Fix header at the top */
            top: 0;
            z-index: 10; /* Ensure it stays above the content */
        }
        .modal-title {
            margin: 0;
            font-size: 20px;
        }

        #modal-body {
            overflow-y: auto; /* Add scrollbar inside the modal body */
            flex-grow: 1; /* Allow the body to take up remaining space */
            padding: 15px;
        }

        .print-btn {
            background-color: maroon;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            display: block;
            width: 100px;
        }
        .print-btn:hover {
            background-color: gold;
            color: white;
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
    <!-- Main Content -->
    <main class="container mt-5" style="margin-left: 220px;">
        <h1>Individual Development Plan Dashboard</h1>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Learning & Development Form</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php if (!empty($trainingResult)): ?>
        <?php foreach ($trainingResult as $row): ?>
            <tr>
                <td><a href="#" class="view-link" data-id="<?= $row['id']; ?>">Individual Development Plan (<?= $year; ?>)</a></td>
                <td><?= date('F j, Y, g:i A', strtotime($row['created_at'])); ?></td>
                <td>
                    <?= htmlspecialchars($row['status']); ?>
                    <?php if (strtolower($row['status']) === 'rejected'): ?>
                        <button class="btn btn-link text-danger remarks-btn" data-remarks="<?= htmlspecialchars($row['remarks'] ?? 'No remarks provided.'); ?>">
                            View Remarks
                        </button>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (strtolower($row['status']) === 'approved' || strtolower($row['status']) === 'rejected'): ?>
                        <button class="btn btn-secondary" disabled>
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                    <?php else: ?>
                        <a href="edit_idp.php?id=<?= $row['id'] ?>" class="btn btn-primary">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" class="text-center">No IDP records found.</td>
        </tr>
    <?php endif; ?>
</tbody>
        </table>
        <!-- Disable the "New IDP" button if the user has an approved IDP -->
        <a class="btn btn-success <?= $hasApprovedIDP ? 'disabled' : '' ?>" 
   href="<?= $hasApprovedIDP ? '#' : 'idpform.php' ?>" 
   <?= $hasApprovedIDP ? 'aria-disabled="true"' : '' ?>>
    New IDP <i class="bi bi-plus-circle"></i>
</a>

    </main>
    <script>
    $(document).ready(function () {
        // Handle "View Remarks" button click
        $('.remarks-btn').click(function () {
            const remarks = $(this).data('remarks');
            $('#modal-remarks-body').html(remarks);
            $('#remarks-modal-overlay').addClass('modal-remarks-show');
        });

        // Close the Remarks modal
        $('#remarks-modal-close').click(function () {
            $('#remarks-modal-overlay').removeClass('modal-remarks-show');
        });

        $('#remarks-modal-overlay').click(function (e) {
            if (e.target === this) {
                $(this).removeClass('modal-remarks-show');
            }
        });
    });
</script>
<!-- Annual Individual Development Plan Modal -->
<div class="modal-overlay" id="modal-overlay">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2 class="modal-title">Annual Individual Development Plan</h2>
            <span class="modal-close" id="modal-close">&times;</span>
        </div>

        <!-- Modal Body -->
        <div id="modal-body">Loading...</div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button class="print-btn" id="print-btn">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>
</div>

<!-- Remarks Modal -->
<div class="modal-remarks-overlay" id="remarks-modal-overlay">
    <div class="modal-remarks-content">
        <span class="modal-remarks-close" id="remarks-modal-close">&times;</span>
        <div class="modal-remarks-header">Remarks</div>
        <div id="remarks-modal-body">Loading...</div>
    </div>
</div>
<script>
    $(document).ready(function () {
        // Handle "View Remarks" button click
        $('.remarks-btn').click(function () {
            const remarks = $(this).data('remarks');
            $('#remarks-modal-body').html('<p>Loading...</p>');
            $('#remarks-modal-overlay').addClass('modal-remarks-show');
            $.ajax({
                url: 'remarks_modal.php',
                type: 'GET',
                data: { remarks: remarks },
                success: function (response) {
                    $('#remarks-modal-body').html(response);
                },
                error: function () {
                    $('#remarks-modal-body').html('<p class="text-danger">Failed to load remarks. Please try again.</p>');
                }
            });
        });

        // Close the Remarks modal
        $('#remarks-modal-close').click(function () {
            $('#remarks-modal-overlay').removeClass('modal-remarks-show');
        });

        $('#remarks-modal-overlay').click(function (e) {
            if (e.target === this) {
                $(this).removeClass('modal-remarks-show');
            }
        });

        // Handle "View IDP" button click
        $('.view-link').click(function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#modal-body').html('<p>Loading...</p>');
            $('#modal-overlay').addClass('modal-show');
            $.ajax({
                url: 'view_idp.php',
                type: 'GET',
                data: { id: id },
                success: function (response) {
                    $('#modal-body').html(response);
                },
                error: function () {
                    $('#modal-body').html('<p class="text-danger">Failed to load data. Please try again.</p>');
                }
            });
        });

        // Close the IDP modal
        $('#modal-close').click(function () {
            $('#modal-overlay').removeClass('modal-show');
        });

        $('#modal-overlay').click(function (e) {
            if (e.target === this) {
                $('#modal-overlay').removeClass('modal-show');
            }
        });
    });
</script>

<!-- Remove Modal for Edit -->
    
    <!-- Bootstrap & jQuery for print-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.view-link').click(function (e) {
                e.preventDefault();
                const id = $(this).data('id');
                $('#modal-body').html('<p>Loading...</p>');
                $('#modal-overlay').addClass('modal-show');
                $.ajax({
                    url: 'view_idp.php',
                    type: 'GET',
                    data: { id: id },
                    success: function (response) {
                        $('#modal-body').html(response);
                    },
                    error: function () {
                        $('#modal-body').html('<p class="text-danger">Failed to load data. Please try again.</p>');
                    }
                });
            });

            // Remove edit button click handler

            $('#modal-close').click(function () {
                $('#modal-overlay').removeClass('modal-show');
            });

            $('#modal-overlay').click(function (e) {
                if (e.target === this) {
                    $('#modal-overlay').removeClass('modal-show');
                }
            });

            $('#print-btn').click(function () {
                var printContent = document.getElementById("modal-body").innerHTML;
                var newWin = window.open("", "", "width=800,height=600");
                newWin.document.write("<html><head><title>Print</title></head><body>");
                newWin.document.write(printContent);
                newWin.document.write("</body></html>");
                newWin.document.close();
                newWin.print();
            });
        });
    </script>
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

.view-btn {
    background-color: maroon; /* Maroon background */
    color: white; /* White text color */
    border: none; /* Remove border */
    padding: 10px 20px; /* Add padding for better size */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Change cursor to pointer */
    transition: background-color 0.3s, color 0.3s; /* Smooth transition */
}
.view-btn:hover {
    background-color: gold; /* Gold hover */
    color: black; /* Black text for contrast */
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
  font-size:18px;
}

.table td {
  color: black; /* Black text for the table content */
  border: 1px solid #ddd; /* Light border between table cells */
  font-size: 18px;
}

a {
    color: maroon; /* Maroon color for links */
    text-decoration: none; /* Remove underline */
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

</style>