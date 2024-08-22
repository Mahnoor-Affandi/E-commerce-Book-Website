<?php 

session_start();
include('server/connection.php');

// If user is already registered, take user to account page
if(isset($_SESSION['logged_in'])){
    header('location: account.php');
    exit;
}

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if passwords match
    if($password !== $confirmPassword){
        header('location:register.php?error=Passwords do not match');
        exit;
    } else if(strlen($password) < 6){
        header('location: register.php?error=Password must be at least 6 characters');
        exit;
    } else {
        // Check if user with this email already exists
        $stmt1 = $conn->prepare("SELECT count(*) FROM users WHERE user_email=?");
        $stmt1->bind_param('s', $email);
        $stmt1->execute();
        $stmt1->bind_result($num_rows);
        $stmt1->store_result();
        $stmt1->fetch();

        if($num_rows != 0){
            header('location: register.php?error=Account with this Email already exists');
            exit;
        } else {
            // Handle file upload
            if(isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == UPLOAD_ERR_OK){
                $fileTmpPath = $_FILES['profilePicture']['tmp_name'];
                $fileName = $_FILES['profilePicture']['name'];
                $fileSize = $_FILES['profilePicture']['size'];
                $fileType = $_FILES['profilePicture']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

                // Directory in which the uploaded file will be moved
                $uploadFileDir = 'uploads/';
                $dest_path = $uploadFileDir . $newFileName;

                if(move_uploaded_file($fileTmpPath, $dest_path)){
                    $profilePicture = $dest_path;
                } else {
                    header('location: register.php?error=Failed to move uploaded file');
                    exit;
                }
            } else {
                $profilePicture = 'uploads/default.png'; // Default profile picture
            }

            // Create a new user
            $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password, user_profile_picture) VALUES(?,?,?,?)");
            $stmt->bind_param('ssss', $name, $email, md5($password), $profilePicture);

            if($stmt->execute()){
                $user_id = $stmt->insert_id;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $name;
                $_SESSION['logged_in'] = true;
                header('location: account.php?register_success=You Registered Successfully');
                exit;
            } else {
                header('location: register.php?error=Could not create an account at the moment');
                exit;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset body and html margins */
        body, html {
            margin-top: 50px;
            height: 100%;
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

        .register-section {
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

        .register-section {
            background: rgba(255, 255, 255, 0.1); /* Glassmorphism effect */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .register-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .welcome-text .text-left {
            max-width: 500px;
            padding: 30px;
            text-align: center;
            color: #fff;
        }

        .welcome-text i {
            font-size: 6rem; /* Increase icon size */
            margin-bottom: 20px;
        }

        .welcome-text h2 {
            font-size: 2.5rem; /* Increase header font size */
            margin-bottom: 10px;
        }

        .welcome-text p {
            font-size: 1.25rem; /* Increase paragraph font size */
        }

        #register-form .form-group {
            margin-bottom: 20px;
        }

        #register-form .form-group label {
            font-weight: bold;
        }

        #register-form .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 25px;
            background-color: rgba(255, 255, 255, 0.5); /* Adjusted background color for better contrast */
            font-size: 1rem;
            color: #000; /* Set text color to black for better visibility */
        }

        #register-form .form-control::placeholder {
            color: #000; /* Set placeholder text color */
            opacity: 1; /* Ensures the color is applied properly */
        }

        #register-form .btn-outline-dark {
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

        #register-form .btn-outline-dark:hover {
            background-color: #21242e;
            color: #fff;
        }

        #login-url, #forgot-password-url {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
        }

        #login-url:hover, #forgot-password-url:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease-in-out;
        }

        .text-center p {
            color: red;
            font-weight: bold;
        }
        /* General responsive adjustments */
@media (max-width: 1200px) {
    .welcome-text, .register-section {
        width: 100%;
    }
}

@media (max-width: 992px) {
    .welcome-text i {
        font-size: 4rem; /* Adjust icon size for medium screens */
    }

    .welcome-text h2 {
        font-size: 2rem; /* Adjust header font size for medium screens */
    }

    .welcome-text p {
        font-size: 1rem; /* Adjust paragraph font size for medium screens */
    }

    .register-box {
        padding: 20px; /* Adjust padding for medium screens */
        max-width: 90%; /* Allow the box to use more width on medium screens */
    }
}

@media (max-width: 768px) {
    .wrapper {
        flex-direction: column; /* Stack sections vertically on small screens */
    }

    .welcome-text, .register-section {
        width: 100%; /* Ensure each section takes full width on small screens */
        height: auto; /* Allow height to adjust based on content */
    }

    .welcome-text i {
        font-size: 3rem; /* Further adjust icon size for small screens */
    }

    .welcome-text h2 {
        font-size: 1.5rem; /* Further adjust header font size for small screens */
    }

    .welcome-text p {
        font-size: 0.9rem; /* Further adjust paragraph font size for small screens */
    }

    .register-box {
        padding: 15px; /* Further adjust padding for small screens */
        max-width: 100%; /* Ensure the box takes full width on small screens */
    }

    #register-form .form-control {
        font-size: 0.9rem; /* Adjust font size for form controls on small screens */
    }

    #register-form .btn-outline-dark {
        font-size: 0.9rem; /* Adjust button font size for small screens */
    }
}

@media (max-width: 576px) {
    .register-box {
        padding: 10px; /* Adjust padding to fit content better */
        max-width: 100%; /* Full width on extra small screens */
        margin: 0 auto; /* Center the form container */
    }

    #register-form .form-group {
        margin-bottom: 10px; /* Reduce spacing between form elements */
    }

    #register-form .form-control {
        font-size: 0.85rem; /* Reduce font size */
        padding: 8px; /* Adjust padding */
    }

    #register-form .btn-outline-dark {
        font-size: 0.85rem; /* Reduce button font size */
        padding: 8px; /* Adjust button padding */
    }

    .welcome-text i {
        font-size: 2rem; /* Adjust icon size for extra small screens */
    }

    .welcome-text h2 {
        font-size: 1.25rem; /* Adjust header font size for extra small screens */
    }

    .welcome-text p {
        font-size: 0.8rem; /* Adjust paragraph font size for extra small screens */
    }
}

    </style>
</head>
<body class="register-page">
    <?php include 'header.php'; ?>

    <!-- Wrapper for the content -->
    <div class="wrapper">
        <!-- Welcome Text Section -->
        <section class="welcome-text">
            <div class="text-left">
                <i class="fas fa-user-plus" style="font-size: 3rem !important;"></i> <!-- Example FontAwesome icon -->
            <div>
                    <h2>Welcome to our Bookstore!</h2>
                    <p>Sign up now to explore a wide range of books. Whether you enjoy fiction, non-fiction, mysteries, or more, we have something for every reader.</p>
                </div>
            </div>
        </section>

        <!-- Registration Section -->
        <section class="register-section">
            <div class="register-box">
                <form id="register-form" method="POST" action="register.php" enctype="multipart/form-data">
                    <p style="color:red;" class="text-center">
                        <?php if(isset($_GET['error'])){ echo $_GET['error'];}?>
                    </p>

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" id="register-name" name="name" placeholder="Name" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" id="register-email" name="email" placeholder="Email" required>
                    </div>

                    <div class="form-group">
                        <label>Profile Picture</label>
                        <input type="file" class="form-control-file" id="register-profile-picture" accept=".jpg, .jpeg, .png" name="profilePicture">
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" id="register-password" name="password" placeholder="Password" required>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" id="register-confirm-password" name="confirmPassword" placeholder="Confirm Password" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-outline-dark" name="register">Register</button>
                    </div>

                    <a href="login.php" id="login-url">Already have an account? Login</a>
                </form>
            </div>
        </section>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Optional JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
