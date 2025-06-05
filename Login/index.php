<?php
session_start(); // Start the session
include('signupfunction.php');
include('signinfunction.php');
include('connection.php');

if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error']) . "</div>";
    unset($_SESSION['error']); // Clear the error message after displaying it
}
// Set default form values to session values or empty
$empname = isset($_SESSION['empname']) ? $_SESSION['empname'] : '';
$mobilenumber = isset($_SESSION['mobilenumber']) ? $_SESSION['mobilenumber'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$roleID = $_SESSION['roleID'] ?? null; // Replace this with the actual roleID of the logged-in user

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css'>
    <link rel="stylesheet" href="./style_login.css">
    <style>
        .footer {
            background-color: maroon;
        }

        .footer p {
            color: white;
        }

        .fa-heart {
            color: gold;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
    </style>
</head>
<body>

<!-- Display session messages -->
<?php
echo "<!-- Debug: roleID = " . ($_SESSION['roleID'] ?? 'null') . " -->";
?>

<div class="background-image"></div>
<h2>Welcome to HRLynk</h2>


<div class="container" id="container">
    <div class="form-container sign-up-container" id="signup">
        <form action="signupfunction.php" method="POST">
            <h1>Create Account</h1>
            <input type="text" required name="empname" placeholder="Employee Name" value="<?php echo htmlspecialchars($empname); ?>" />
            <input type="tel" required name="mobilenumber" placeholder="Mobile Number" value="<?php echo htmlspecialchars($mobilenumber); ?>" />
            <input type="text" required name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" />
            <select class="signup-select" name="role" required>
                <option value="" disabled selected>Role</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo htmlspecialchars($role['roleID']); ?>">
                        <?php echo htmlspecialchars($role['roleName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="password" required name="password" placeholder="Password" />
            <input type="password" required name="confirmpassword" placeholder="Confirm Password" />
            <button name="signup">Sign Up</button>
        </form>
    </div>
        <div class="form-container sign-in-container" id="signin">
            <form action="signinfunction.php" method="POST">
                <h1>Sign in</h1>
                <input type="email" required name="email" placeholder="Email" />
                <input type="password" required name="password" placeholder="Password" />
                <select name="role" required>
                    <option value="">SIGN IN AS</option>
                    <option value="employee">Employee</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="hrmd">HRMD</option>
                </select>

                <button name="signin">Sign In</button>
            </form>
        </div>
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h3>Connect with us!</h3>
                <p>To keep connected with us please login with your personal info</p>
                <button class="ghost" id="signIn">Sign In</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h3>Hello there!</h3>
                <p>Enter your personal details and start the journey with us</p>
                <button class="ghost" id="signUp">Sign Up</button>
            </div>
        </div>
    </div>
</div>


<footer>
    <p>© 2024 HRLynk</p>
</footer>

<script src="./script.js"></script>

</body>
</html>