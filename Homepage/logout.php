<?php
// Start the session
session_start();

// Function to display a confirmation dialog
function confirmLogout() {
    echo "<script>
            var logout = confirm('Are you sure you want to log out?');
            if (logout) {
                window.location.href = 'logout.php?confirm=true';
            } else {
                window.location.href = 'index.html'; // Redirect to profile page if cancel
            }
          </script>";
}

// Check if the user is logged in
if (isset($_SESSION['user'])) {
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'true') {
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header("Location: ../Login/index.php");
        exit();
    } else {
        confirmLogout(); // Display the confirmation dialog
    }
} else {
    header("Location: index.php"); // If user is not logged in, redirect to login page
}
?>
