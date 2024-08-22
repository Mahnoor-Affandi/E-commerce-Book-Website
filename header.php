<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asass-Al-Hikmat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            min-height: 100vh;
            overflow-x: hidden;
        }
        header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 80px;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000; /* Ensure header is above other content */
        }
        .group {
            display: flex;
            align-items: center;
        }
        header ul {
            position: relative;
            display: flex;
            gap: 30px;
            align-items: center;
            margin-top: 15px;
        }
        header ul li {
            list-style: none;
        }
        header ul li a {
            position: relative;
            text-decoration: none;
            font-size: 1rem;
            color: black;
            text-transform: uppercase;
            letter-spacing: 0.2rem;
        }
        header ul li a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            width: 100%;
            height: 2px;
            background: black;
            transform: scaleX(0);
            transition: transform 0.5s ease-in-out;
            transform-origin: right;
        }
        header ul li a:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }
        header .search {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            z-index: 1001;
            cursor: pointer;
            margin-left: 20px;
        }
        .searchbox {
            position: absolute;
            right: -100%;
            width: 100%;
            height: 100%;
            display: flex;
            background: white;
            align-items: center;
            padding: 0 30px;
            transition: right 0.5s ease-in-out;
            z-index: 1001;
        }
        .searchbox input {
            width: 100%;
            border: none;
            outline: none;
            height: 50px;
            color: black;
            font-size: 1.25em;
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.5);
        }
        .search-btn {
            position: relative;
            left: 30px;
            top: 2.5px;
            transition: opacity 0.5s ease-in-out;
            z-index: 1001;
        }
        .close-btn {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out;
            z-index: 1001;
        }
        .menuToggle {
            position: relative;
            display: none;
            margin-left: 20px;
            z-index: 1002;
        }
        @media(max-width: 800px) {
            .search-btn {
                left: 0;
            }
            .close-btn {
                right: 30px;
            }
            .menuToggle {
                position: absolute;
                display: block;
                font-size: 2em;
                cursor: pointer;
                transform: translateX(30px);
            }
            header .navigation {
                position: absolute;
                opacity: 0;
                visibility: hidden;
                left: 100%;
                transition: opacity 0.5s ease-in-out, visibility 0.5s ease-in-out;
                z-index: 1000;
            }
            header.open .navigation {
                top: 80px;
                opacity: 1;
                visibility: visible;
                left: 0;
                display: flex;
                flex-direction: column;
                background: white;
                width: 100%;
                height: calc(100vh - 80px);
                padding: 40px;
                border-top: 1px solid rgba(0, 0, 0, 0.5);
            }
            header.open .navigation li a {
                font-size: 1.25em;
            }
        }
    </style>
</head>
<body>
    <header>
        <a class="logo" href="index.php">
            <img src="assets/imgs/logo.png" style="height: 50px; width: auto;">
        </a>
        <div class="group">
            <ul class="navigation">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="accessories.php">Accessories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        <i class="fa-light fa-cart-shopping-fast"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="bookmarks.php">
                        <i class="far fa-bookmark"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link icon-bold" href="account.php">
                        <i class="far fa-user"></i>
                    </a>
                </li>
            </ul>
            <div class="search">
                <span class="icon">
                    <ion-icon name="search-outline" class="search-btn"></ion-icon>
                    <ion-icon name="close-outline" class="close-btn"></ion-icon>
                </span>
            </div>
            <ion-icon name="menu-outline" class="menuToggle"></ion-icon>
        </div>
        <div class="searchbox">
            <input type="text" placeholder="Search here..." id="searchInput">
        </div>
    </header>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        document.querySelector('.search-btn').addEventListener('click', function() {
            document.querySelector('.searchbox').style.right = '0';
            document.querySelector('.search-btn').style.opacity = '0';
            document.querySelector('.search-btn').style.visibility = 'hidden';
            document.querySelector('.close-btn').style.opacity = '1';
            document.querySelector('.close-btn').style.visibility = 'visible';
        });

        document.querySelector('.close-btn').addEventListener('click', function() {
            document.querySelector('.searchbox').style.right = '-100%';
            document.querySelector('.search-btn').style.opacity = '1';
            document.querySelector('.search-btn').style.visibility = 'visible';
            document.querySelector('.close-btn').style.opacity = '0';
            document.querySelector('.close-btn').style.visibility = 'hidden';
        });

        let menuToggle = document.querySelector('.menuToggle');
        let header = document.querySelector('header');

        menuToggle.onclick = function() {
            header.classList.toggle('open');
        };

        document.querySelector('#searchInput').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                let query = event.target.value.trim().toLowerCase();
                if (query) {
                    searchProducts(query);
                }
            }
        });

        function searchProducts(query) {
            let categories = ['fiction', 'non-fiction', 'mystery', 'science', 'history'];
            if (categories.includes(query)) {
                window.location.href = 'shop.php?category=' + query;
            } else {
                fetch(`/search.php?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            let firstResult = data[0];
                            let redirectUrl = '';

                            if (data.length === 1 && firstResult.product_id) {
                                redirectUrl = `single-product.php?product_id=${encodeURIComponent(firstResult.product_id)}`;
                            } else if (firstResult.product_category) {
                                redirectUrl = `shop.php?category=${encodeURIComponent(firstResult.product_category)}`;
                            } else {
                                redirectUrl = `search-results.php?q=${encodeURIComponent(query)}`;
                            }

                            window.location.href = redirectUrl;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        }
    </script>
</body>
</html>
