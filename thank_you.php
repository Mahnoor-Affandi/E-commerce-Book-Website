<?php
session_start();
// Check if total amount is set in session
if (isset($_SESSION['order_total'])) {
    $totalAmount = $_SESSION['order_total'];
} else {
    $totalAmount = 0; // Default value if total amount is not set
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <?php include 'header.php'; ?>
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body .order-confirmation {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to right, #cfd8dc, #90a4ae); /* Bluish-grey gradient background */
            text-align: center;
        }

        .order-confirmation h2 {
            margin-bottom: 20px;
            font-size: 2.5rem; /* Larger font size for large screens */
        }

        .order-confirmation p {
            font-size: 1.5rem; /* Bigger text for large screens */
            margin-bottom: 20px;
        }

        .icon-container {
            font-size: 5rem; /* Large icon size for large screens */
            color: #fff; /* Icon color */
            margin-bottom: 20px;
            animation: bounce 2s infinite; /* Apply the bounce animation */
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-30px);
            }
            60% {
                transform: translateY(-15px);
            }
        }

        #order-confirmation {
            background-color: #000;
            display: inline-block;
            padding: 10px 20px;
            border: 2px solid #fff;
            color: #fff;
            text-decoration: none;
            border-radius: 50px; /* Rounded button */
            transition: background-color 0.3s, color 0.3s;
            text-align: center; /* Center button */
        }

        #order-confirmation:hover {
            background-color: navy;
            color: #fff;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .order-confirmation h2 {
                font-size: 2rem; /* Smaller font size for tablets and smaller screens */
            }

            .order-confirmation p {
                font-size: 1.2rem; /* Smaller text for tablets and smaller screens */
            }

            .icon-container {
                font-size: 4rem; /* Smaller icon size for tablets and smaller screens */
            }
        }

        @media (max-width: 576px) {
            .order-confirmation h2 {
                font-size: 1.5rem; /* Even smaller font size for very small screens */
            }

            .order-confirmation p {
                font-size: 1rem; /* Even smaller text for very small screens */
            }

            .icon-container {
                font-size: 3rem; /* Even smaller icon size for very small screens */
            }

            #order-confirmation {
                padding: 8px 16px; /* Adjust button padding for small screens */
            }
        }
    </style>
</head>
<body class="order-confirmation">
    <div class="container mt-5 order-confirmation">
        <div class="row">
            <div class="col-md-12">
                <div class="icon-container">
                    <i class="bi bi-ui-checks"></i>
                </div>
                <h2>Thank You for Your Order!</h2>
                <p>Your order has been successfully placed and is being processed.</p>
                <p>Total Amount: PKR <?php echo $totalAmount; ?></p>
                <a href="index.php" id="order-confirmation" class="btn btn-primary">Return to Home</a>
            </div>
            <!---<p><strong></strong></p>
            <ul>
                <li>Name: <?php echo $name; ?></li>
                <li>Email: <?php echo $email; ?></li>
                <li>Phone: <?php echo $phone; ?></li>
                <li>City: <?php echo $city; ?></li>
                <li>Address: <?php echo $address; ?></li>
                <li>Payment Method: <?php echo $payment_method; ?></li>
                <li>Total Price: Rs <?php echo $total_price; ?></li>
            </ul>---->
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
