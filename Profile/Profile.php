<?php

    include('profilefunction.php');
    include('connection.php');

    $userID = $_SESSION['user'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE userID = ?");
    $stmt->execute([$userID]);
    $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!isset($_SESSION['user'])) {
        // User is not authenticated, redirect to login page or display an error message
        $_SESSION['message'] = array("text" => "Please log in to access this page.", "alert" => "danger");
        header('location: ../Login/index.php');
        exit();
    }
    // Retrieve user details from the database


    if(!$userDetails) {
        // User details not found, display an error message or handle the situation accordingly
        $_SESSION['message'] = array("text" => "User details not found.", "alert" => "danger");
        header('location: ../Login/index.php');
        exit();
    }

    // Extract name and email from the user details
    $empname = $userDetails['empname'];
    $email = $userDetails['email'];
    $officeID = isset($userDetails['officeID']) ? $userDetails['officeID'] : '';

    // Fetch office names from the database
    $officeStmt = $conn->prepare("SELECT officeID, officeName FROM office");
    $officeStmt->execute();
    $offices = $officeStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fill form fields with saved data, if available
    $position = isset($userDetails['position']) ? $userDetails['position'] : '';
    $jobdescription = isset($userDetails['jobdescription']) ? $userDetails['jobdescription'] : '';
    $employmentstatus = isset($userDetails['employmentstatus']) ? $userDetails['employmentstatus'] : '';
    $datehired = isset($userDetails['datehired']) ? $userDetails['datehired'] : '';
    $monthsintheposition = isset($userDetails['monthsintheposition']) ? $userDetails['monthsintheposition'] : '';
    $yearsiniit = isset($userDetails['yearsiniit']) ? $userDetails['yearsiniit'] : '';

     
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='#' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


</head>
<body>
<div class="sidebar">
        <div class="logo">
            <a href="index.html" style="font-size: 32px; font-weight: bold; color: maroon; text-decoration: none;">
                <span style="color: maroon;">HR</span><span style="color: gold; text-shadow: 1px 1px maroon;">Lynk</span>
            </a>
        </div>
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

    <div class="background-container"></div>
</header>
<main class="main">
    <div class="responsive-wrapper">
        <div class="main-header">
             <h2>User Profile</h2> 
        </div>
        <form action="profilefunction.php" method="POST" enctype="multipart/form-data">
            <div class="content">
                <div class="row">
                    <div class="col-md-3 border-right">
                    <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <input type="file" name="profile_picture" accept="image/*">
                        <span class="font-weight-bold"><?php echo $empname; ?></span>
                        <span class="text-black-50"><?php echo $email; ?></span>
                        <span> </span>
                    </div>
                    </div>
                    <div class="col-md-5 border-right">
                        <div class="p-3 py-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-right">Edit Profile</h4>
                            </div>
                            <div class="row mt-2" id="editprofile">
                                <div class="col-md-12">
                                    <label class="labels">Office</label>
                                    <select name="officeID" class="form-control">
                                        <option value="">Select Office</option>
                                        <?php foreach ($offices as $office): ?>
                                            <option value="<?php echo $office['officeID']; ?>" <?php echo ($office['officeID'] == $officeID) ? 'selected' : ''; ?>>
                                                <?php echo $office['officeName']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12"><label class="labels">Position</label><input name="position" type="text" class="form-control" placeholder="Position" value="<?php echo $position; ?>"></div>
                                <div class="col-md-12"><label class="labels">Job Description</label><input name="jobdescription" type="text" class="form-control" placeholder="Description" value="<?php echo $jobdescription; ?>"></div>
                                <div class="col-md-12"><label class="labels">Employment Status</label><input name="employmentstatus" type="text" class="form-control" placeholder="Status" value="<?php echo $employmentstatus; ?>"></div>
                                <div class="col-md-12"><label class="labels">Date Hired</label><input name="datehired" type="date" class="form-control" placeholder="Date" value="<?php echo $datehired; ?>"></div>
                                <div class="col-md-12"><label class="labels">Numbers of Months in the Position</label><input name="monthsintheposition" type="number" class="form-control" placeholder="No. of months" value="<?php echo $monthsintheposition; ?>"></div>
                                <div class="col-md-12"><label class="labels">Numbers of years in MSU-IIT</label><input name="yearsiniit" type="number" class="form-control" placeholder="No. of years" value="<?php echo $yearsiniit; ?>"></div>
                            </div>
                            <div class="mt-5 text-center"><button class="btn btn-primary profile-button" type="submit" name="user_details">Save Profile</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
        <style>
@import url("https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");
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
  width: 220px;
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
  color: #000000;
  font-size: 100px;
  font-weight: 600;
}

h2 {
  color: maroon;
  text-shadow:
    -1px -1px 0 #ffd700,  
     1px -1px 0 #ffd700,
    -1px  1px 0 #ffd700,
     1px  1px 0 #ffd700;
  font-size: 50px;
  font-weight: 600;
  text-align: left;
  padding-left: 100px;
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
  margin-left: 200px; /* Adjust to match the width of the sidebar + some spacing */
  margin-right: 0px;
  padding: 0px; /* Add padding for main content */
  flex-grow: 1; /* Allow the main content to take available space */
}
.main-header h1 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #000000; /* Set to black */
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
/* Ensuring text is visible with proper contrast */

/* Setting text color to black for visibility */

/* Profile section titles */
h4.text-right {
    color: #000000; /* Black text */
    font-size: 22px;
    font-weight: bold;
}

/* User name and email */
.font-weight-bold {
    font-size: 20px;
    color: #000000; /* Black text */
}

.text-black-50 {
    color: #000000; /* Black text for email as well */
}

/* Form labels */
.labels {
    font-size: 14px;
    font-weight: bold;
    color: #000000; /* Black color for labels */
    margin-bottom: 5px;
    text-align: right; /* Align text to the right */
}

/* Form fields (input and textarea) */
.form-control {
    font-size: 14px;
    color: #000000; /* Black text inside form fields */
    background-color: #ffffff; /* Ensure background is white */
    border: 1px solid var(--c-border-primary);
    border-radius: 5px;
    padding: 10px;
}
.profile-button {
    background-color: maroon;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.profile-button:hover {
    background-color: yellow;
    color: maroon;
}
       /* Full-page background container */
       .background-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('../Profile/prism2.jpg'); /* Set your background image path */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            filter: blur(2px); /* Apply blur effect to the background */
            z-index: -1; /* Keep it behind all content */
        }

        /* Content styling */
        .content {
            position: relative;
            z-index: 1;
            margin: 20px;
            padding: 5px;
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background for readability */
            border-radius: 8px;
            max-width: 1000px;
            margin: auto;
            text-align: left;
        }




        </style>

<script src='https://unpkg.com/phosphor-icons'></script>
<script  src="./script2.js"></script>
</body>
</html>