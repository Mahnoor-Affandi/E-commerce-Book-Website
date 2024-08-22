<?php 
session_start();
include('server/connection.php');

// Redirect if already logged in
if (isset($_SESSION['logged_in'])) {
    header('location: account.php');
    exit;
}

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to select user data
    $stmt = $conn->prepare("SELECT user_id, user_name, user_password FROM users WHERE user_email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    
    if ($stmt->execute()) {
        $stmt->bind_result($user_id, $user_name, $hashed_password);
        $stmt->store_result();

        if ($stmt->num_rows() == 1) {
            $stmt->fetch();

            // Verify password with both md5 and password_hash
            if (password_verify($password, $hashed_password) || md5($password) === $hashed_password) {
                // If md5 hash is correct, update to new password hash
                if (md5($password) === $hashed_password) {
                    $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $update_stmt = $conn->prepare("UPDATE users SET user_password = ? WHERE user_id = ?");
                    $update_stmt->bind_param('si', $new_hashed_password, $user_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                }

                // Store data in session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['user_email'] = $email;  
                $_SESSION['logged_in'] = true;

                // Logged in successfully
                header('location: account.php');
                exit;
            } else {
                // Log the error
                error_log("Login attempt failed for email: $email", 0);
                $error_message = "Invalid email or password";
            }
        } else {
            // Log the error
            error_log("Login attempt failed for email: $email", 0);
            $error_message = "Invalid email or password";
        }
    } else {
        // Log the error
        error_log("Database query failed for email: $email", 0);
        // Error
        $error_message = "Something went wrong!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        /* Reset body and html margins */
        body, html {
            margin-top: 50px;
            height: 100%;
            font-family: 'Poppins', sans-serif;

        }

        /* Flexbox layout for containers */
        .wrapper {
            display: flex;
            height: 100vh; /* Full viewport height */
            margin: 0;
            padding: 0;
        }

        .welcome-text {
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            width: 50%; /* Each container takes half the width */
            height: 100%; /* Full height of the wrapper */
        }

        .login-section {
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            width: 50%; /* Each container takes half the width */
            height: 100%; /* Full height of the wrapper */
        }

        .welcome-text {
            background: linear-gradient(to right, #27405a, #12121a); /* Gradient background */
            color: #fff;
        }

        .login-section {
            background: rgba(255, 255, 255, 0.1); /* Glassmorphism effect */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .login-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .login-box h3{
            font-weight: bold;
        }
        
        .welcome-text .text-left {
            max-width: 500px;
            padding: 30px;
            text-align: center;
            color: #fff;
        }

        .welcome-text i {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        #login-form .form-group {
            margin-bottom: 20px;
        }

        /*#login-form .form-group label {
            font-weight: bold;
        }*/

        #login-form .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 25px;
            background-color: rgba(255, 255, 255, 0.5); /* Adjusted background color for better contrast */
            font-size: 1rem;
            color: #000; /* Set text color to black for better visibility */
        }

        #login-form .form-control::placeholder {
            color: #000; /* Set placeholder text color */
            opacity: 1; /* Ensures the color is applied properly */
        }

        #login-form .btn-outline-dark {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 25px;
            background-color: #323746;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #login-form .btn-outline-dark:hover {
            background-color: transparent;
            color: #323746;
            border: 3px solid #323746;
        }

        #register-url {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
        }

        #register-url:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease-in-out;
        }

        .text-center p {
            color: red;
            font-weight: bold;
        }
        /* Responsive Styles */
@media (max-width: 768px) {
    .wrapper {
        flex-direction: column; /* Stack the sections vertically on smaller screens */
    }

    .welcome-text, .login-section {
        width: 100%; /* Full width for each section */
        height: auto; /* Auto height to fit content */
    }

    .login-box {
        padding: 20px; /* Adjust padding for smaller screens */
        max-width: 90%; /* Reduce max width */
    }

    .login-box h3 {
        font-size: 1.5rem; /* Smaller heading font size */
    }

    #login-form .form-control {
        font-size: 0.875rem; /* Smaller font size for input fields */
    }

    #login-form .btn-outline-dark {
        font-size: 0.875rem; /* Smaller font size for button */
    }

    .welcome-text .text-left {
        padding: 15px; /* Adjust padding for smaller screens */
    }

    .welcome-text i {
        font-size: 2.5rem; /* Smaller icon size */
    }

    #register-url {
        font-size: 0.8rem; /* Smaller font size for register link */
    }

    .text-center p {
        font-size: 0.875rem; /* Smaller error message font size */
    }
}

@media (max-width: 480px) {
    .login-box {
        padding: 15px; /* Further reduce padding for very small screens */
    }

    .login-box h3 {
        font-size: 1.25rem; /* Even smaller heading font size */
    }

    #login-form .form-control {
        font-size: 0.75rem; /* Even smaller font size for input fields */
    }

    #login-form .btn-outline-dark {
        font-size: 0.75rem; /* Even smaller font size for button */
    }

    .welcome-text .text-left {
        padding: 10px; /* Further adjust padding */
    }

    .welcome-text i {
        font-size: 2rem; /* Even smaller icon size */
    }

    #register-url {
        font-size: 0.7rem; /* Even smaller font size for register link */
    }

    .text-center p {
        font-size: 0.75rem; /* Even smaller error message font size */
    }
}

    </style>
</head>
<body class="login-page">
    <?php include 'header.php'; ?>

    <!-- Wrapper for content -->
    <div class="wrapper">
        <!-- Welcome Text Section -->
        <section class="welcome-text">
            <div class="text-left">
                <i class="fas fa-book"></i> <!-- Example FontAwesome icon -->
                <div>
                    <h2>Welcome Back!</h2>
                    <p>Dive into a world of stories and knowledge with just a few clicks.</p>
                </div>
            </div>
        </section>

        <!-- Login Section -->
        <section class="login-section">
            <div class="login-box">
                <h3>Login</h3>
                <form id="login-form" method="POST" action="login.php" autocomplete="off">
                    <p style="color:red" class="text-center">
                        <?php if(isset($error_message)){ echo $error_message; } ?>
                    </p>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" id="login-email" name="email" placeholder="Email" required autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" id="login-password" name="password" placeholder="Password" required autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-outline-dark" id="login-btn" name="login_btn" value="Login"/>
                    </div>
                    <div class="form-group">
                        <a id="register-url" href="register.php" class="btn">Don't have an account? Sign Up Here</a>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
