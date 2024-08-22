<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Pavilion - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-size: 14px; /* Adjust the base font size */
            line-height: 1.6; /* Increase line height for better readability */
            padding: 20px 0; /* Adjust padding as needed */
        }

        .footer {
            font-size: 12px; /* Smaller font size for footer */
        }

        .footer p {
            margin-bottom: 0.5rem; /* Reduce bottom margin for paragraphs */
            font-size: 0.7rem;
        }

        .footer h5 {
            font-size: 14px; /* Smaller font size for headings */
            margin-bottom: 0.5rem; /* Reduce bottom margin for headings */
            font-weight: bold;
        }

        .footer ul {
            margin-bottom: 0; /* Remove bottom margin for unordered lists */
            font-size: 0.9rem;
        }

        .footer a {
            text-decoration: none; /* Remove underline from links */
        }

        .footer hr {
            margin-top: 1rem; /* Adjust spacing above the horizontal rule */
            margin-bottom: 1rem; /* Adjust spacing below the horizontal rule */
        }

        @media (max-width: 768px) {
            .footer ul {
                font-size: 0.8rem; /* Adjust font size for smaller screens */
            }

            .footer .logo {
                height: 50px; /* Adjust logo size for smaller screens */
            }

            .footer .col-lg-5, .footer .col-lg-3 {
                text-align: flex start; /* Center text for smaller screens */
            }

            .footer .col-lg-5 p, .footer .col-lg-3 ul {
                margin-bottom: 1rem; /* Add margin bottom for spacing */
            }

            .footer .col-lg-6.col-md-7, .footer .col-lg-6.col-md-5 {
                text-align: center; /* Center text for smaller screens */
                margin-bottom: 1rem; /* Add margin bottom for spacing */
            }
        }
    </style>
</head>
<body>
<!--------------Footer-------------->
<footer class="mt-5 px-5 py-5 bg-black text-white footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <img class="logo" src="assets/imgs/logo2.png" alt="Logo" style="height: 60px; width: auto;">
                <p class="pt-3">We provide the best books for the most reasonable prices.</p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <h5 class="pb-2">Quick Links</h5>
                <ul class="text-uppercase list-unstyled">
                    <li><a href="about.php" class="text-white">About Us</a></li>
                    <li><a href="shop.php" class="text-white">Shop</a></li>
                    <li><a href="accessories.php" class="text-white">Accessories</a></li>
                    <li><a href="login.php" class="text-white">Login</a></li>
                    <li><a href="register.php" class="text-white">Sign Up</a></li>

                </ul>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <h5 class="pb-2">Customer Area</h5>
                <ul class="text-uppercase list-unstyled">
                    <li><a href="account.php" class="text-white">Account</a></li>
                    <li><a href="pp.php" class="text-white">Privacy Policy</a></li>
                    <li><a href="contact.php" class="text-white">Contact Us</a></li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-6 col-md-7 col-sm-12 mb-3">
                <p class="mb-0">Ecommerce @ 2024 All Rights Reserved</p>
            </div>
            <div class="col-lg-6 col-md-5 col-sm-12 mb-3 text-lg-end text-md-end text-center">
                <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
