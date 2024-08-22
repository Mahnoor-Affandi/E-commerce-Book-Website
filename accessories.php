<?php
// Database connection
include('server/connection.php');

// Pagination setup
$results_per_page = 18; // Number of products per page
$sql = "SELECT COUNT(*) AS total FROM products WHERE product_category = 'accessories'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_results = $row['total'];
$total_pages = ceil($total_results / $results_per_page);

// Get the current page number from the URL, default to 1 if not set
if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
    $current_page = 1;
} else {
    $current_page = intval($_GET['page']);
}

// Calculate the starting limit for the SQL query
$start_limit = ($current_page - 1) * $results_per_page;

// Fetch accessories products for the current page
$sql = "SELECT * FROM products WHERE product_category = 'accessories' LIMIT $start_limit, $results_per_page";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessories</title>
    <?php include 'header.php'; ?>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body.accessories-page {
            background-color: #ecf0f1;
        }

        .accessories-page #featured-accessories .cardd {
            border: none;
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .accessories-page #featured-accessories .cardd:hover {
            transform: translateY(-5px);
        }

        .accessories-page #featured-accessories .cardd .card-img-topp {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            height: 200px; /* Set the desired height for the images */
            object-fit: cover;
        }

        .accessories-page #featured-accessories .cardd .card-bodyy {
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .accessories-page #featured-accessories .cardd .btn-buy {
            background-color: transparent;
            color: #000;
            border: 2px solid #000;
            border-radius: 20px;
            transition: background-color 0.3s, color 0.3s;
            padding: 8px 16px; /* Adjust padding as needed */
            margin-top: auto; /* Push the button to the bottom */
            text-decoration: none;
        }

        .accessories-page #featured-accessories .cardd .btn-buy:hover {
            background-color: #000;
            color: #fff;
        }

        .accessories-page .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
        }

        .accessories-page .pagination li {
            margin: 0 5px;
        }

        .accessories-page .pagination a {
            display: block;
            padding: 8px 16px;
            text-decoration: none;
            background-color: #f1f1f1;
            color: #000;
            border-radius: 5px;
        }

        .accessories-page .pagination a:hover {
            background-color: #000;
            color: #fff;
        }

        .accessories-page .pagination .active a {
            background-color: #000;
            color: #fff;
        }

        @media (max-width: 1200px) {
            .accessories-page .col-lg-2 {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }

        @media (max-width: 992px) {
            .accessories-page .col-lg-2,
            .accessories-page .col-md-4 {
                flex: 0 0 33.3333%;
                max-width: 33.3333%;
            }
        }

        @media (max-width: 768px) {
            .accessories-page .col-lg-2,
            .accessories-page .col-md-4,
            .accessories-page .col-sm-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 576px) {
            .accessories-page .col-lg-2,
            .accessories-page .col-md-4,
            .accessories-page .col-sm-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
    </style>
</head>
<body class="accessories-page">
    <section id="featured-accessories" class="my-5 pb-5">
        <div class="container text-center mt-5 py-5">
            <h3>Accessories</h3>
            <hr>
        </div>
        <div class="row mx-auto container-fluid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-lg-2 col-md-4 col-sm-6 col-xs-6 mb-4">
                        <div class="cardd h-100 text-center d-flex flex-column" style="padding: 10px;">
                            <img class="card-img-topp img-fluid" src="assets/imgs/accessories/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-bodyy" style="padding: 10px;">
                                <h5 class="card-titlee" style="font-size: 1rem; font-weight: bold; margin-bottom: 5px;"><?php echo $row['product_name']; ?></h5>
                                <p class="card-textt" style="font-size: 1rem; margin-bottom: 10px; max-height: 3em; overflow: hidden; text-overflow: ellipsis;"><?php echo "Rs " . $row['product_price']; ?></p>
                                <a href="single-product.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-buy mt-auto">Buy Now</a>
                            </div>
                        </div>
                    </div>
                    <?php 
                }
            } else {
                echo "<p class='col-12 text-center'>No accessories found.</p>";
            }
            ?>
        </div>
        <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php
                // Previous page link
                if ($current_page > 1) {
                    echo '<li><a href="accessories.php?page=' . ($current_page - 1) . '">Previous</a></li>';
                }

                // Page number links
                for ($page = 1; $page <= $total_pages; $page++) {
                    echo '<li' . ($page == $current_page ? ' class="active"' : '') . '><a href="accessories.php?page=' . $page . '">' . $page . '</a></li>';
                }

                // Next page link
                if ($current_page < $total_pages) {
                    echo '<li><a href="accessories.php?page=' . ($current_page + 1) . '">Next</a></li>';
                }
                ?>
            </ul>
        </nav>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
