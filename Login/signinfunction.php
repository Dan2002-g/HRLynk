<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}// Start the session to use session variables
require_once 'connection.php';

if (isset($_POST['signin'])) {
    if ($_POST['email'] != "" && $_POST['password'] != "" && isset($_POST['role'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $selectedRole = strtolower($_POST['role']); // Capture the selected role

        try {
            // Fetch user details from the database
            $sql = "SELECT * FROM `users` WHERE `email` = ?";
            $query = $conn->prepare($sql);
            $query->execute([$email]);
            $fetch = $query->fetch(PDO::FETCH_ASSOC);

            if ($fetch && $password === $fetch['password']) {
                $_SESSION['user'] = $fetch['userID'];
                $_SESSION['roleID'] = $fetch['roleID'];

                // Debugging: Log the roleID to confirm it's being set
                error_log("Role ID set in session: " . ($_SESSION['roleID'] ?? 'null'));

                // Define role-based redirection
                $supervisorRoles = [1, 2, 5, 7, 9, 10]; // Roles that can log in as Supervisor
                $hrmdRoles = [3, 4]; // Roles that can log in as HRMD
                $employeeRoles = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]; // Roles that can log in as Employee

                // Redirect based on the selected role and user's roleID
                if ($selectedRole === "supervisor" && in_array($fetch['roleID'], $supervisorRoles)) {
                    header("location: ../Supervisor/index.html");
                } elseif ($selectedRole === "hrmd" && in_array($fetch['roleID'], $hrmdRoles)) {
                    header("location: ../HRMD/index.php");
                } elseif ($selectedRole === "employee" && in_array($fetch['roleID'], $employeeRoles)) {
                    header("location: ../Homepage/index.html");
                } else {
                    $_SESSION['error'] = "Access denied: Your account does not have permission to sign in as '" . strtoupper($selectedRole) . "'.";
                    header("location: index.php#signin");
                }
                exit();
            } else {
                // Invalid email or password
                $_SESSION['error'] = "Invalid email or password.";
                header("location: index.php#signin");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
            header("location: index.php#signin");
            exit();
        }
    } else {
        $_SESSION['error'] = "Please fill in all fields.";
        header("location: index.php#signin");
        exit();
    }
}
?>