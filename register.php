<?php
include("includes/head.php");

// Initialize variables to store feedback messages
$feedback_message = '';

// Registration script
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Simple validation
    if (empty($username) || empty($email) || empty($password)) {
        $feedback_message = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $feedback_message = "Invalid email format!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare and execute the SQL statement
        $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                header("Location: login.php");
                exit(); // Ensure no further code is executed after redirection
            } else {
                $feedback_message = "Error occurred while registering. Please try again.";
            }
            $stmt->close();
        } else {
            $feedback_message = "Database query failed!";
        }
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Custom styles for centering the form */
        .login-page {
            display: flex;
            flex-direction: column;
            height: 100vh;
            justify-content: center;
        }
        .login-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            width: 100%;
            max-width: 500px; /* Adjust as needed */
        }
    </style>
</head>
<body class="login-page">
<div class="login-header box-shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="brand-logo">
            <a href="login.php" style="text-decoration: none; color: #1b00ff;">
                <h1 style="margin: 0;">Expense Tracker</h1>
            </a>
        </div>
            <div class="login-menu">
                <ul>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
        <div class="container">
            <div class="col-md-12 col-lg-12">
                <div class="login-box bg-white box-shadow border-radius-10">
                    <div class="login-title">
                        <h2 class="text-center text-primary">Register</h2>
                    </div>
                    <?php if ($feedback_message): ?>
                        <div class="alert alert-danger text-center"><?php echo htmlspecialchars($feedback_message); ?></div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="input-group custom">
                            <input type="text" name="username" class="form-control form-control-lg" placeholder="Username" required />
                            <div class="input-group-append custom">
                                <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
                            </div>
                        </div>
                        <div class="input-group custom">
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" required />
                            <div class="input-group-append custom">
                                <span class="input-group-text"><i class="icon-copy dw dw-mail1"></i></span>
                            </div>
                        </div>
                        <div class="input-group custom">
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" autocomplete="new-password" required />
                            <div class="input-group-append custom">
                                <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="input-group mb-0">
                                    <input class="btn btn-primary btn-lg btn-block" type="submit" name="Sign_up" value="Register" />
                                </div>
                            </div>
                        </div>
                        <div class="font-16 weight-600 pt-10 pb-10 text-center" data-color="#707373">
                            OR
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-12 text-center">
                                <a href="login.php" class="btn btn-outline-primary btn-lg btn-block">Already have an account? Login</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
