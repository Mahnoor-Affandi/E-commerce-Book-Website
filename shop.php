<?php 
include('server/connection.php');

// Initialize variables
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$category = '';
$price = 100000;
$searchQuery = '';
$products_per_page = 10; // 3 rows per page, 5 products per row
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $products_per_page;

// Error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if(isset($_POST['search'])){
        $category = $_POST['category'];
        $price = $_POST['price'];

        // Prepare and execute query
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_category=? AND product_price<=? LIMIT ? OFFSET ?");
        $stmt->bind_param("siii", $category, $price, $products_per_page, $offset);
        $stmt->execute();
        $products = $stmt->get_result();
        
        // Fetch total number of products for pagination
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE product_category=? AND product_price<=?");
        $stmt->bind_param("si", $category, $price);
        $stmt->execute();
        $total_products_result = $stmt->get_result();
        $total_products_row = $total_products_result->fetch_assoc();
        $total_products = $total_products_row['total'];
    } else if (isset($_GET['search'])) {
        // Handle search query from search bar
        $searchQuery = $_GET['search'];

        $stmt = $conn->prepare("SELECT * FROM products WHERE product_name LIKE ? OR product_category LIKE ? LIMIT ? OFFSET ?");
        $likeSearchQuery = '%' . $searchQuery . '%';
        $stmt->bind_param("ssii", $likeSearchQuery, $likeSearchQuery, $products_per_page, $offset);
        $stmt->execute();
        $products = $stmt->get_result();
        
        // Fetch total number of products for pagination
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE product_name LIKE ? OR product_category LIKE ?");
        $stmt->bind_param("ss", $likeSearchQuery, $likeSearchQuery);
        $stmt->execute();
        $total_products_result = $stmt->get_result();
        $total_products_row = $total_products_result->fetch_assoc();
        $total_products = $total_products_row['total'];
    } else if (isset($_GET['category'])) {
        // Handle category filter from search bar
        $category = $_GET['category'];

        $stmt = $conn->prepare("SELECT * FROM products WHERE product_category=?  LIMIT ? OFFSET ?");
        $stmt->bind_param("sii", $category, $products_per_page, $offset);
        $stmt->execute();
        $products = $stmt->get_result();
        
        // Fetch total number of products for pagination
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE product_category=?");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $total_products_result = $stmt->get_result();
        $total_products_row = $total_products_result->fetch_assoc();
        $total_products = $total_products_row['total'];
    } else {
        // Return all products
        $stmt = $conn->prepare("SELECT * FROM products LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $products_per_page, $offset);
        $stmt->execute();
        $products = $stmt->get_result();
        
        // Fetch total number of products for pagination
        $total_products_result = $conn->query("SELECT COUNT(*) AS total FROM products");
        $total_products_row = $total_products_result->fetch_assoc();
        $total_products = $total_products_row['total'];
    }
} catch (mysqli_sql_exception $e) {
    echo "SQL Error: " . $e->getMessage();
}

$total_pages = ceil($total_products / $products_per_page);
?>


<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>


@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

/* Reset margin and padding globally */
body, html {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
}

body.shop-page {
    background-color: #ecf0f1;
}

/* Ensure zero margin on top of .container-flex */
.container-flex {
    display: flex;
    justify-content: flex-start;
    margin-top: 0 !important; /* Important to override any existing margin */
}

/* Ensure zero margin on top of specific sections */
#search {
    margin-top: 0 !important;
}

#featured {
    margin-top: 0 !important;
}

/* Adjust container width and padding as needed */
#search {
    width: 25%;
    padding: 10px;
    border-right: 1px solid #ccc;
}

#featured {
    width: 75%; /* Adjusted width for product display */
    padding: 10px;
}

/* Filter Section Styling */
#search {
    background-color: #ecf0f1; /* Background color for the filter section */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Light shadow for modern look */
    padding: 20px; /* Padding for better spacing */
}

/* Filter Form Elements */
#search p {
    font-size: 1.2rem;
    font-weight: bold;
    color: #333; /* Darker text color for readability */
}

#search hr {
    border: 1px solid #ccc; /* Light border for separation */
    margin: 10px 0; /* Margin around the separator */
}

.form-check {
    margin-bottom: 10px; /* Space between form checks */
}

.form-check-input {
    margin-right: 10px; /* Space between radio button and label */
}

.form-check-label {
    font-size: 1rem; /* Adjust font size for labels */
    color: #555; /* Slightly lighter text color */
}

.form-range {
    margin-top: 10px;
}

input[type="submit"].btn {
    background-color: black; /* Primary button color */
    color: #fff; /* White text color */
    border: none; /* Remove border */
    border-radius: 50px; /* Rounded corners */
    padding: 10px 20px; /* Padding inside the button */
    font-size: 1rem; /* Font size for button text */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s; /* Smooth background color transition */
}

input[type="submit"].btn:hover {
    background-color: gainsboro; /* Darker button color on hover */
    color: navy;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .container-flex {
        flex-direction: column; /* Stack items vertically on smaller screens */
    }
    #search, #featured {
        width: 100%; /* Full width on smaller screens */
        border-right: none; /* No border on smaller screens */
    }
}

.product img {
    object-fit: cover;
}

.pagination a {
    color: black;
}

.pagination li:hover a {
    color: aliceblue;
    background-color: #c57e74;
}

.col-lg-4 {
    flex: 0 0 20%;
    max-width: 20%;
}


/* Featured Section Styling */
#featured .card {
            border: none;
            transition: transform 0.3s ease-in-out;
            background: rgba(229, 233, 234, 0.75); /* Transparent glassmorphism effect */
        }

        #featured .card:hover {
            transform: translateY(-5px);
        }

        #featured .card .card-img-top {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        #featured .card .card-body {
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-weight: bold;
        }

        #featured .card .btn-shop {
            background-color: transparent;
            color: #000;
            border: 2px solid #000;
            border-radius: 20px;
            transition: background-color 0.3s, color 0.3s;
            padding: 8px 16px; /* Adjust padding as needed */
            margin-top: auto; /* Push the button to the bottom */
            text-decoration: none;
        }

        #featured .card .btn-shop:hover {
            background-color: #000;
            color: #fff;
        }

        /* Grid layout */
        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between; /* Adjust spacing between items */
        }

        .col-xl-2, .col-lg-2, .col-md-4, .col-sm-6 {
            flex: 0 0 20%; /* 5 products per row */
            max-width: 20%;
            margin-bottom: 20px; /* Adjust spacing between rows */
            display: flex;
            justify-content: center; /* Center card horizontally */
        }
        .card-body {
            margin-bottom: 20px; /* Adjust this value to move spacing */
            text-align: center;
        }
        /* Styling for the button */
        .btn-shop {
            display: block; /* Ensure the button is block-level */
            width: 100%; /* Button width to match card width */
            text-align: center; /* Center align text within the button */
            border-radius: 20px; /* Rounded corners */
            padding: 8px 16px; /* Adjust padding as needed */
            font-size: 1rem; /* Font size for button text */
            background-color: transparent; /* Transparent background */
            color: #000; /* Button text color */
            border: 2px solid #000; /* Border color and style */
            transition: background-color 0.3s, color 0.3s; /* Smooth transition */
        }

        /* Button hover effect */
        .btn-shop:hover {
            background-color: #000; /* Background color on hover */
            color: #fff; /* Text color on hover */
        }
        /* General pagination container */
.pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
}

/* Pagination list items */
.pagination li {
    margin: 0 5px;
}

/* Pagination links */
.pagination a {
    display: block;
    padding: 8px 16px;
    text-decoration: none;
    background-color: #f1f1f1; /* Default background color */
    color: #000; /* Default text color */
    border-radius: 5px;
}

/* Pagination links on hover */
.pagination a:hover {
    background-color: #000; /* Background color on hover */
    color: #fff; /* Text color on hover */
    text-decoration: none;
}

/* Active pagination link */
.pagination a.active {
    background-color: #000; /* Background color for active link */
    color: #fff; /* Text color for active link */
}

/* Disabled pagination link */
.pagination .disabled {
    background-color: #e0e0e0; /* Background color for disabled link */
    color: #b0b0b0; /* Text color for disabled link */
    cursor: not-allowed; /* Pointer cursor for disabled link */
    padding: 8px 16px; /* Match the padding of the links */
    border-radius: 5px; /* Match the border-radius of the links */
}

       .pagination {
        display: flex;
        justify-content: flex-end;
        align-items: flex-end; /* Align pagination to bottom */
        margin-top: 5px; /* Add some spacing from content */
        }
        .pagination a {
            margin: 0 10px;
            padding: 10px 20px;
        }

        .container-flex {
        width: 100%; /* Adjust as needed (e.g., 70%, 90%) */
        margin: 0 auto; /* Center the container horizontally */
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .col-lg-4 {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }

        @media (max-width: 992px) {
            .col-md-4 {
                flex: 0 0 33.33%;
                max-width: 33.33%;
            }
        }

        @media (max-width: 768px) {
            .col-sm-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 576px) {
            .col-xs-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .btn-shop {
                padding: 8px;
                font-size: 10px;
            }
            .card {
                width: 70%; /* Adjust as needed */
                margin: 0 auto; /* Center the card */
            }
            .card img {
                width: 100%; /* Adjust as needed */
                height: auto; /* Maintain aspect ratio */
            }
        }

    </style>
</head>
<body class="shop-page">
    <!-- Flex container for side-by-side layout -->
    <div class="container-flex">
        <!-- Category Filter Section -->
        <section id="search" class="my-5 ms-2">
            <div class="container mt-5 py-5">
                <p>Search Products</p>
                <hr>
                <form action="shop.php" method="POST">
                    <input type="hidden" name="category" value="<?php echo isset($_POST['category']) ? htmlspecialchars($_POST['category']) : ''; ?>">                    <div class="row mx-auto container">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <p>Shop by Categories</p>
                            <!-- Add form-check elements for each category -->
                            <?php
                            // Array of categories
                            $categories = [
                                'Non-fiction', 'Fiction', 'Mystery and Thriller', 'Science Fiction and Fantasy',
                                'Romance', 'Biographies and Memoirs', 'Self-Help', 'Cookbooks', 'Children’s Books',
                                'Young Adult', 'Graphic Novels and Comics', 'Travel', 'Spirituality and Religion',
                                'Poetry', 'Education and Textbooks'
                            ];

                            // Loop through categories to generate radio buttons
                            foreach ($categories as $cat) {
                                $checked = ($category == $cat) ? 'checked' : '';
                                echo '<div class="form-check">';
                                echo '<input class="form-check-input" type="radio" name="category" value="' . htmlspecialchars($cat) . '" id="category_' . htmlspecialchars($cat) . '" ' . $checked . '>';
                                echo '<label class="form-check-label" for="category_' . htmlspecialchars($cat) . '">' . htmlspecialchars($cat) . '</label>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        <div class="row mx-auto container mt-5">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <p>Price</p>
                                <input type="range" class="form-range w-50" name="price" value="<?php echo $price; ?>" min="1" max="100000" id="customRange2">
                                <div class="w-50">
                                    <span style="float: left;">1</span>
                                    <span style="float: right;">10000</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group my-3 mx-3">
                            <input type="submit" name="search" value="Search" class="btn">
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <!-- Products Section -->
        <section id="featured" class="my-5 pb-5">
            <div class="container text-center mt-5 py-5">
                <h3>Products</h3>
                <hr>
                <p></p>
            </div>
           <div class="product-container mt-5">
                <div class="row">
                    <div class="col-md-12">
                    <div class="row">
                        <?php
                        $count = 0;
                        while ($row = $products->fetch_assoc()) {
                            if ($count % 5 == 0 && $count != 0) {
                            echo '</div><div class="row">';
                            }
                        ?>
                        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 mb-4">
                            <div class="card">
                                <img src="assets/imgs/<?php echo $row['product_category']; ?>/<?php echo $row['product_image']; ?>" class="card-img-top" alt="<?php echo $row['product_name']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: 1rem; font-weight: bold; margin-bottom: 5px;" ><?php echo $row['product_name']; ?></h5>
                                    <p class="card-text" style="font-size: 1rem; margin-bottom: 5px;" >Rs <?php echo $row['product_price']; ?></p>
                                    <a href="single-product.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-shop">Buy Now</a>
                                </div>
                            </div>
                        </div>
                    <?php
                    $count++;
                    } 
                    ?>
            </div>
        </section>
    </div>

    <div class="pagination">
        <?php if ($current_page > 1): ?>
             <a href="shop.php?page=<?php echo $current_page - 1; ?>&category=<?php echo htmlspecialchars($category); ?>">Previous</a>
        <?php else: ?>
            <span class="disabled">Previous</span>
        <?php endif; ?>

       <!---<?php for ($page = 1; $page <= $total_pages; $page++): ?>
            <a href="shop.php?page=<?php echo $page; ?>&category=<?php echo htmlspecialchars($category); ?>" class="<?php if ($current_page == $page) echo 'active'; ?>"><?php echo $page; ?></a>
        <?php endfor; ?>-->
        
        <?php if ($current_page < $total_pages): ?>
            <a href="shop.php?page=<?php echo $current_page + 1; ?>&category=<?php echo htmlspecialchars($category); ?>">Next</a>
        <?php else: ?>
            <span class="disabled">Next</span>
        <?php endif; ?>
    </div>

</body>
</html>
