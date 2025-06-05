<?php

require_once 'connection.php';

// Fetch roles from the database
try {
    $sql = "SELECT roleID, roleName FROM role";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Database Error: " . $e->getMessage();
    header('location:index.php#signup');
    exit();
}

if (isset($_POST['signup'])) {
    // Store form values in session to retain them if an error occurs
    $_SESSION['empname'] = $_POST['empname'];
    $_SESSION['mobilenumber'] = $_POST['mobilenumber'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['role'] = $_POST['role'];

    // Check if all required fields are not empty
    if ($_POST['empname'] != "" && $_POST['mobilenumber'] != "" && $_POST['email'] != "" && $_POST['password'] != "" && $_POST['role'] != "") {
        // Get form inputs
        $empname = $_POST['empname'];
        $mobilenumber = $_POST['mobilenumber'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmpassword = $_POST['confirmpassword'];
        $roleID = $_POST['role']; // Use roleID from the dropdown

        // Check if the email ends with "@g.msuiit.edu.ph"
        if (strpos($email, '@g.msuiit.edu.ph') === false) {
            $_SESSION['error'] = "Email must end with @g.msuiit.edu.ph";
            header('location:index.php#signup');
            exit();
        }

        // Check if passwords match
        if ($password === $confirmpassword) {
            try {
                // Insert user into the database
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql = "INSERT INTO `users` (empname, mobilenumber, email, password, roleID) VALUES (:empname, :mobilenumber, :email, :password, :roleID)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':empname', $empname);
                $stmt->bindParam(':mobilenumber', $mobilenumber);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':roleID', $roleID); // Bind roleID
                $stmt->execute();

                // Set success message and clear session values
                $_SESSION['message'] = array("text" => "Account Created Successfully", "alert" => "info");
                unset($_SESSION['empname'], $_SESSION['mobilenumber'], $_SESSION['email'], $_SESSION['role']); // Clear form values after success
                header('location:index.php');
                exit();
            } catch (PDOException $e) {
                $_SESSION['error'] = "Database Error: " . $e->getMessage();
                header('location:index.php#signup');
                exit();
            }
        } else {
            // Passwords don't match, set error message and redirect
            $_SESSION['error'] = "Passwords do not match";
            header('location:index.php#signup');
            exit();
        }
    } else {
        // Required fields are empty, set error message and redirect
        $_SESSION['error'] = "Please fill up the required fields!";
        header('location:index.php#signup');
        exit();
    }
}
?>