
<?php 
session_start();
include('server/connection.php');

// Fetch books from the best_sellers table
$sql = "SELECT product_image, product_name, product_price, product_category FROM best_sellers ORDER BY RAND() LIMIT 5";
$result = $conn->query($sql);

if ($result === false) {
    die("Query error: " . $conn->error);
}
$books = array();
while ($row = $result->fetch_assoc()) {
    // Prepend 'assets/img/' to the image path
    $books[] = array(
        'product_image' => 'assets/imgs/' . $row['product_image'],
        'product_name' => $row['product_name'],
        'product_price' => $row['product_price'],
        'product_category' => $row['product_category']
    );
}

// Fetch accessories from the products table
$sql_accessories = "SELECT product_image, product_name, product_price, product_category FROM products WHERE product_category = 'accessories' ORDER BY RAND() LIMIT 5";
$result_accessories = $conn->query($sql_accessories);

if ($result_accessories === false) {
    die("Query error: " . $conn->error);
}
$accessories = array();
while ($row = $result_accessories->fetch_assoc()) {
    $accessories[] = array(
        'product_image' => 'assets/imgs/' . $row['product_image'],
        'product_name' => $row['product_name'],
        'product_price' => $row['product_price'],
        'product_category' => $row['product_category']
    );
}

// Fetch the latest 3 reviews with a 5-star rating
$stmt = $conn->prepare("SELECT reviews.*, users.user_name, users.user_profile_picture FROM reviews JOIN users ON reviews.user_id = users.user_id WHERE reviews.rating = 5 ORDER BY reviews.created_at DESC LIMIT 3");
$stmt->execute();
$reviews_result = $stmt->get_result();

?>

<?php include 'header.php'; ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Home Page</title>
</head>
<style>

@import url('https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

body {
    font-family: 'Poppins', sans-serif;
}
/* Styling for the blank-section */
.blank-section {
    display: flex;
    align-items: center;
    justify-content: flex-end; /* Align text to the right */
    height: calc(100vh + 80px); /* Add the height of the navbar to the section height */
    background: url('assets/imgs/bg2.jpg') no-repeat center center/cover;
    display: flex;
    padding-bottom: 10px;
    background-size: cover;
    background-attachment: fixed;
    margin-top: -80px; /* Adjust this value to match the height of your navbar */
    font-family: 'Poppins', sans-serif;
}

.blank-section::after {
    content: "";
    display: table;
    clear: both;
}

/* Styling for the text-section */
#text-section {
    max-width: 45%; /* Limit the width of the text section */
    text-align: right; /* Align text to the right */
}

#text-section h1 {
    font-size: 2rem; /* Large and bold headline */
    font-weight: bold;
    color: #395458; /* Dark color for contrast */
    margin-bottom: 20px;
    text-transform: uppercase; /* Make text uppercase */

}

/* Hollow text with typewriter effect */
#text-section .hollow-text {
    text-transform: uppercase; /* Make text uppercase */
    font-size: 36px; /* Adjust the size as needed */
    color: transparent; /* Make the inside of the text transparent */
    -webkit-text-stroke-width: 1.5px; /* Outline thickness */
    -webkit-text-stroke-color: #395458; /* Outline color */
    font-weight: bold; /* Optional: makes the outline stand out more */
    overflow: hidden; /* Hide the overflow for the typewriter effect */
    white-space: nowrap; /* Prevent text from wrapping */
    animation: typing 4s steps(40, end), blink-caret 0.75s step-end infinite, restart 30s linear infinite;
}

/* Typewriter animation */
@keyframes typing {
    from {
        width: 0;
    }
    to {
        width: 100%;
    }
}

/* Cursor blink effect */
@keyframes blink-caret {
    from, to {
        border-right-color: transparent;
    }
    50% {
        border-right-color: black;
    }
}

/* Restart animation */
@keyframes restart {
    from {
        opacity: 1;
    }
    to {
        opacity: 1;
    }
}

#text-section .hollow-text::before {
    content: '';
    position: absolute;
    left: 100%;
    top: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, black);
    animation: blink-caret 0.75s step-end infinite;
}

/* Styling for the CTA button */
.cta-btn {
    display: inline-block; /* Ensure the button is properly aligned */
    padding: 10px 20px; /* Adjust padding as needed */
    font-size: 1rem; /* Adjust font size as needed */
    color: #395458; /* Text color */
    background-color: transparent; /* Transparent background */
    border: 2px solid #395458; /* Border color and thickness */
    border-radius: 0px; /* Rounded corners (optional) */
    text-decoration: none; /* Remove underline from link */
    text-align: center; /* Center text inside button */
    cursor: pointer; /* Pointer cursor on hover */
    outline: none; /* Remove any outline */
    box-shadow: none; /* Remove any box shadow */
}

/* Button focus state */
.cta-btn:focus {
    outline: none; /* Ensure outline is removed when focused */
}

/* Button hover state */
.cta-btn:hover {
    background-color: #395458; /* Background color on hover */
    color: white; /* Text color on hover */
    text-decoration: none;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .blank-section {
        justify-content: center; /* Center align on smaller screens */
        padding-bottom: 20px; /* Add padding to avoid text cut-off */
        margin-top: 0; /* Remove margin-top to prevent overlap */
    }

    #text-section {
        max-width: 90%; /* Increase max-width for smaller screens */
        text-align: center; /* Center align text on smaller screens */
    }

    #text-section h1 {
        font-size: 1.5rem; /* Adjust font size for medium screens */
        margin-bottom: 10px; /* Reduce bottom margin for medium screens */
    }

    #text-section .hollow-text {
        font-size: 20px; /* Adjust font size for medium screens */
    }

    .cta-btn {
        padding: 12px 24px; /* Increase padding for better clickability */
        font-size: 1.2rem; /* Increase font size for readability */
    }
}

@media (max-width: 480px) {
    .blank-section {
        padding: 10px; /* Adjust padding for very small screens */
        margin-top: 0; /* Ensure no margin-top on very small screens */
    }

    #text-section {
        max-width: 100%; /* Ensure text section takes full width */
    }

    #text-section h1 {
        font-size: 1.5rem; /* Further reduce font size for very small screens */
    }

    #text-section .hollow-text {
        font-size: 24px; /* Further adjust font size for readability */
    }

    .cta-btn {
        padding: 10px 20px; /* Adjust padding for very small screens */
        font-size: 1rem; /* Adjust font size for very small screens */
    }
}




body, html {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    font-family: 'Poppins', sans-serif;

}

body.index-page {
    background-color: #f0f7ff; /*#d6e0e9*/
}

#text-section {
    text-align: center;
    padding: 20px;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
    margin-top: 50px; /* Added margin-top to separate from the header */
}

.container-section {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: nowrap; /* Ensures items don't wrap */
    padding-top: 20px;
}

.book-container {
    position: relative;
    width: 49%; /* Adjust width to ensure both containers fit in a row */
    height: 300px; /* Adjust height to match the square shape */
    margin: 0 10px; /* Equal margins on both sides */
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    background: rgba(255, 255, 255, 0.1); /* Glassmorphism effect */
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 20px;
    box-sizing: border-box; /* Ensures padding and border are included in width/height */
    justify-content: center;
}

.book-item {
    position: absolute;
    transition: opacity 1s ease;
    display: flex;
    align-items: center;
    opacity: 0;
    width: 100%; /* Ensure book item takes full width of the container */
}

.book-item img {
    max-width: 120px; /* Adjust image size as needed */
    height: auto;
    border-radius: 8px;
    margin-right: 10px; /* Space between image and text */
    margin-left: 20px;
}

.book-item.active {
    opacity: 1;
}

.book-info {
    text-align: left;
}

.book-info h3 {
    margin: 0;
    font-size: 16px; /* Adjust font size as needed */
    line-height: 1.4; /* Line height for better readability */
    word-wrap: break-word; /* Allows text to wrap onto multiple lines */
}

.discover-container {
    position: absolute;
    bottom: 20px; /* Adjust as needed */
    right: 20px; /* Adjust as needed */
    padding: 10px 20px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    font-size: 14px;
    color: #333;
    z-index: 10;
    text-decoration: none;
}

.discover-container:hover {
    text-decoration: none;
}

.discover-container i {
    margin-right: 8px; /* Space between icon and text */
    font-size: 30px; /* Adjust icon size as needed */
    text-decoration: none;
}

.discover-container a {
    text-decoration: none;
    color: inherit;
}
.book-container h4 {
    font-size: 1.5rem; /* Adjust font size as needed */
    font-weight: bold;
    text-align: center;
    margin: 0;
    position: absolute;
    top: 20px; /* Space from the top */
    left: 50%;
    transform: translateX(-50%); /* Center the heading horizontally */
    width: 100%; /* Ensure the heading spans the full width of the container */
    z-index: 20; /* Ensure it is above other elements */
    color: black; /* Adjust text color as needed */
}

/* Comment Section */
.comments-section {
    padding: 20px;
    display: flex;
    justify-content: center; /* Center align the comments section horizontally */
    align-items: center; /* Center align the comments section vertically */
    flex-direction: column; /* Ensure the heading and comments are in a column */
    width: 100%;
    box-sizing: border-box;
}

/* Heading style */
.comments-section h2 {
    font-size: 2rem;
    margin-bottom: 20px;
    text-align: center;
    color: #333; /* Dark color for contrast */
}

/* Container for comments */
.comments {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* Small gap between comment cards */
    justify-content: center; /* Center align comments horizontally */
    max-width: 100%; /* Ensure the comments do not extend beyond the container */
}

/* Individual comment card */
.comment {
    background: rgba(255, 255, 255, 0.1); /* Glassmorphism effect */
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    padding: 20px;
    width: 320px; /* Increase width for larger cards */
    min-height: 180px; /* Increase minimum height for larger cards */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); /* Slightly stronger shadow */
    backdrop-filter: blur(10px);
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Space between header and text */
    align-items: center; /* Center content horizontally */
    transition: transform 0.3s ease; /* Smooth scaling effect */
}

/* Hover effect for comment cards */
.comment:hover {
    transform: scale(1.03); /* Slightly enlarge on hover */
}

/* Header section of the comment card */
.comment-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    width: 100%;
    justify-content: center; /* Center header contents */
}

/* Profile image in comment card */
.comment-header img {
    width: 70px; /* Slightly larger image */
    height: 70px; /* Slightly larger image */
    border-radius: 50%;
    margin-right: 15px; /* Increase spacing */
}

/* Icon in comment header */
.comment-header i {
    font-size: 40px; /* Adjust icon size */
    color: #aaa; /* Adjust icon color */
    margin-right: 15px; /* Increase spacing */
}

/* Author name style */
.comment-author {
    font-size: 1.2rem; /* Slightly larger font size */
    color: #555;
    margin: 0;
    text-align: center; /* Center align text */
}

/* Comment text style */
.comment-text {
    font-size: 1rem;
    margin-bottom: 10px;
    text-align: center; /* Center align text */
}

/* Star rating style */
.star-rating i {
    color: #f4b400; /* Gold color for stars */
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .comments {
        flex-direction: column; /* Stack comments vertically */
        align-items: center; /* Center align comments horizontally */
    }

    .comment {
        width: 90%; /* Full width on smaller screens */
        min-height: 200px; /* Increase height for better spacing */
    }
}

@media (max-width: 480px) {
    .comment {
        width: 100%; /* Full width on very small screens */
        padding: 15px; /* Adjust padding */
        min-height: 220px; /* Increase minimum height for better spacing */
    }

    .comment-header img {
        width: 60px; /* Smaller image on very small screens */
        height: 60px;
    }

    .comment-header i {
        font-size: 30px; /* Smaller icon size */
    }

    .comment-author {
        font-size: 1rem; /* Adjust font size for author name */
    }

    .comment-text {
        font-size: 0.9rem; /* Adjust font size for comment text */
    }
}


/* Featured Section */
#featuredd {
    padding: 10px 0;
}

#featuredd p {
    font-size: 1.3rem;
}

#featuredd h3 {
    background-color: #c9dcdc; /* Solid background color */
    color: #333333; /* Text color */
    padding: 10px 20px; /* Padding around the text */
    border-radius: 50px; /* Rounded edges */
    display: inline-block; /* Ensures the background only covers the text */
    font-size: 1.3rem;
}

#featuredd .card {
    border: none;
    transition: transform 0.3s ease-in-out;
    background: transparent;
    max-width: 150px; /* Set a maximum width for the card */
    margin: auto; /* Center the card within its column */
}

#featuredd .card:hover {
    transform: translateY(-5px);
}

#featuredd .card .card-img-top {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    height: 250px; /* Smaller image height */
    object-fit: cover; /* Ensure the image fits the card dimensions */
}

#featuredd .card .card-body {
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    text-align: center;
    padding: 10px; /* Smaller padding for a more compact layout */
}

#featuredd .card-title {
    font-size: 1rem; /* Slightly smaller font size */
    margin-bottom: 8px;
}

#featuredd .card-text {
    font-size: 0.875rem; /* Smaller font size */
    color: #555;
}

#featuredd .card .featuredd-btn {
    background-color: transparent;
    color: #000;
    border: 2px solid #000;
    border-radius: 20px;
    transition: background-color 0.3s, color 0.3s;
    padding: 4px 8px; /* Adjusted padding for a smaller button */
    margin-top: auto; /* Push the button to the bottom */
    text-decoration: none;
    font-size: 0.75rem;
}

#featuredd .card .featuredd-btn:hover {
    background-color: #000;
    color: #fff;
}

/* Adjust the grid layout for smaller columns */
#featuredd .col-lg-1-5, 
#featuredd .col-md-2, 
#featuredd .col-sm-3 {
    padding-left: 0.25rem; /* Adjust spacing between cards */
    padding-right: 0.25rem; /* Adjust spacing between cards */
    margin-bottom: 1rem; /* Adjust margin below cards */
}

/* Custom Column for responsive grid */
.col-lg-1-5 {
    flex: 0 0 12.5%; /* 8 items per row on large screens */
    max-width: 12.5%;
}

.col-md-2 {
    flex: 0 0 16.66%; /* 6 items per row on medium screens */
    max-width: 16.66%;
}

.col-sm-3 {
    flex: 0 0 25%; /* 4 items per row on small screens */
    max-width: 25%;
}

/* Responsive adjustments for extra small screens */
@media (max-width: 576px) {
    #featuredd .col-sm-3,
    #featuredd .col-md-2,
    #featuredd .col-lg-1-5 {
        flex: 0 0 50%; /* 2 items per row on extra small screens */
        max-width: 50%;
    }
}


/*category section*/
.book-categories {
  position: relative;
  padding: 20px 0;
  background-color: #f9f9f9;
}

.book-categories h2 {
  text-align: center;
  margin-bottom: 20px;
}

.categories-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.scroll-btn {
  background: #000;
  color: #fff;
  border: none;
  cursor: pointer;
  padding: 10px;
  font-size: 24px;
  position: absolute;
  z-index: 10; /* Ensure the buttons are on top */
}

.scroll-btn.left {
    left: 10px; /* Adjust as needed */
}

.scroll-btn.right {
    right: 10px; /* Adjust as needed */
}

.categories-container {
  display: flex;
  overflow-x: auto;
  scroll-behavior: smooth;
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}

.categories-container::-webkit-scrollbar {
  display: none;  /* Chrome, Safari, and Opera */
}

.category {
    position: relative;
    flex: 0 0 auto;
    width: 300px;
    height: 200px;
    margin: 0 10px;
    background-size: cover;
    background-position: center;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    color: #fff;
    text-shadow: 0 0 5px rgba(0, 0, 0, 0.7);
    border-radius: 10px;
    transition: transform 0.3s ease; /* Smooth zoom effect */
}

.category:hover {
    transform: scale(1.05); /* Slight zoom-in effect */
}
.category img {
  max-width: 100px;
  max-height: 100%;
  object-fit: cover;
  z-index: 2;
}

.category-name {
  z-index: 2;
  font-size: 18px;
  font-weight: bold;
  word-wrap: break-word; /* Enable word wrapping */
  width: 150px; /* Adjust width to control text wrapping */
  white-space: normal; /* Allow text to wrap */
  text-align: left; /* Center the text */
  font-size: 1rem;
}

/* Popup */
.popup {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.7);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s ease, visibility 0.3s ease;
}

.popup.show {
  opacity: 1;
  visibility: visible;
}

.popup-content {
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 90%;
  padding: 20px;
  position: relative;
  text-align: center;
}

.popup-close {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 1.5rem;
  color: #333;
  cursor: pointer;
}

.popup-close i {
  font-size: 2rem;
  color: #333;
}

.popup-img-container {
  margin-bottom: 20px;
}

.popup-img-container img.popup-img {
  max-width: 100%;
  border-radius: 10px;
  object-fit: cover;
}

.right-content h1 {
  font-size: 2rem;
  color: #333;
  margin-bottom: 1rem;
}

.right-content h1 span {
  color: #e74c3c;
}

.right-content p {
  font-size: 1rem;
  color: #666;
  margin-bottom: 1.5rem;
}

.popup-form {
  width: 100%;
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
  margin-bottom: 15px;
  font-size: 1rem;
  color: #333;
}

.popup-form::placeholder {
  color: #999;
}

.right-content button {
  display: inline-block;
  padding: 10px 20px;
  background-color: #e74c3c;
  color: #fff;
  border-radius: 5px;
  text-decoration: none;
  font-weight: bold;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.right-content button:hover {
  background-color: #c0392b;
}



@media only screen and (max-width: 768px) {
  .popup-content {
    width: 100%;
    padding: 15px;
  }

  .right-content h1 {
    font-size: 1.5rem;
  }

  .popup-img-container img.popup-img {
    height: auto;
  }
}

/* Button Styling */
.view-more-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #000; /* Default text color */
    text-decoration: none;
    font-size: 1rem;
    font-weight: bold;
    padding: 10px 20px;
    border: 2px solid #000;
    border-radius: 20px;
    position: relative;
    overflow: hidden;
    transition: color 0.3s, border-color 0.3s;
}

.view-more-btn i {
    font-size: 1.5rem; /* Adjust icon size */
    margin-left: 10px; /* Space between text and icon */
    transition: color 0.3s; /* Smooth color transition */
}

/* Hover Effect */
.view-more-btn:hover {
    color: #007bff; /* Change text color on hover */
    border-color: #007bff; /* Change border color on hover */
    outline: none; /* Removes the default outline */
    box-shadow: none; /* Removes any box-shadow */
    border: none; /* Ensures no border is present */
}

.view-more-btn:focus,
.view-more-btn:active {
    outline: none; /* Ensures no outline on focus */
    box-shadow: none; /* Ensures no box-shadow on focus */
    border: none; /* Ensures no border on active/click */
}

.view-more-btn:hover i {
    color: #007bff; /* Change icon color on hover */
    border: none;
    outline: none; /* Ensures there's no border or outline on focus */
    box-shadow: none; /* Removes any box-shadow that might appear on focus */
}

/* Promo Section */
.promo-section .promo-wrapper {
    display: flex;
    justify-content: center;
    gap: 35px;
    flex-wrap: wrap; /* Wrap items on smaller screens */
}

.promo-item {
    display: flex;
    align-items: flex-start; /* Center items vertically */
    width: 30%; /* Default width */
    border-radius: 12px; /* Rounded corners */
    padding: 15px; /* Padding inside containers */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden; /* Ensure child elements are clipped when zoomed */
    background-color: #fff; /* Default background color */
}

.promo-item img {
    width: 55%; /* Make the image smaller and consistent */
    height: auto;
    object-fit: cover;
    margin-left: 15px; /* Space between image and text */
    transition: transform 0.3s ease;
}

.promo-item .promo-text {
    flex: 1; /* Allow text to take up available space */
    display: flex;
    flex-direction: column;
    justify-content: flex-start; /* Align text to the top */
}

.promo-item h3 {
    margin-top: 50px;
    margin-bottom: 10px;
    font-size: 1.2rem; /* Font size */
    color: white;
    font-family: 'Poppins', sans-serif;
    text-transform: uppercase; /* Make text uppercase */
    letter-spacing: 1.5px; /* Add spacing between letters */
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Add a subtle text shadow */
    /*background: linear-gradient(45deg, #3a3d40, #575757, #767676);  Darker gradient 
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent; /* Make gradient visible */
}

#promo-btn {
    text-decoration: none;
    margin-top: 120px; /* Space between text and button */
    display: inline-block; /* Ensure proper spacing */
    background-color: transparent; /* No fill */
    color: white;
    border-radius: 50px;
    border: 2px solid white; /* White border */
    padding: 0.75rem 1.5rem; /* Adjust padding as needed */
    font-size: 1rem; /* Adjust size as needed */
    text-transform: uppercase;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

#promo-btn:hover {
    background-color: white;
    color: #000;
}

/* Hover effect */
.promo-item:hover {
    transform: scale(1.05); /* Zoom in the entire container */
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2); /* Enhance shadow on hover */
}

.promo-item:hover img {
    transform: scale(1.1); /* Zoom in the image */
}

.promo-item:hover .promo-text {
    filter: blur(1px); /* Apply slight blur to the text */
}

/* Responsive Styles */
@media (max-width: 1200px) {
    .promo-item {
        width: 45%; /* Adjust width for medium screens */
    }
}

@media (max-width: 768px) {
    .promo-item {
        width: 100%; /* Full width for small screens */
        margin-bottom: 20px; /* Space between rows of items */
    }
}

@media (max-width: 480px) {
    .promo-item h3 {
        font-size: 1.25em; /* Smaller font size for very small screens */
    }

    .promo-btn {
        padding: 8px 16px; /* Smaller padding for buttons on small screens */
    }
}


/* Banner Section */
#banner {
    background-image: url('assets/imgs/flag.jpeg');
    background-size: cover;
    background-position: center;
    background-color: #99B68D; /* Sage green background */
    color: white;
    padding: 3rem 0; /* Reduced padding for better responsiveness */
}

#banner .container {
    display: flex;
    justify-content: space-between; /* Space between text and images */
    align-items: center; /* Vertically center items */
    max-width: 1200px; /* Adjust as needed */
    margin: 0 auto; /* Center container */
    padding: 0 1rem; /* Padding for smaller screens */
    flex-wrap: wrap; /* Allow wrapping on smaller screens */
}

.banner-content {
    display: flex;
    justify-content: space-between; /* Space between text and images */
    align-items: center; /* Vertically center items */
    width: 100%;
    flex-wrap: wrap; /* Wrap content on smaller screens */
}

/* Text Section */
.text-section {
    max-width: 50%; /* Adjust width as needed */
    flex: 1; /* Allow text section to be flexible */
}

.text-section h4 {
    color: #fff;
    font-size: 1.5rem; /* Adjusted for better readability */
    margin-bottom: 1rem;
}

.text-section h1 {
    color: #fff;
    font-size: 2rem; /* Slightly reduced for better fit */
    margin-bottom: 1rem;
    line-height: 1.2;
}

.text-section button {
    background-color: transparent; /* No fill */
    color: white;
    border: 2px solid white; /* White border */
    padding: 0.75rem 1.5rem; /* Adjust padding as needed */
    font-size: 1rem; /* Adjust size as needed */
    text-transform: uppercase;
    cursor: pointer;
    border-radius: 50px; /* Rounded corners */
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.text-section button:hover {
    background-color: white; /* White fill on hover */
    color: #006400; /* Green text on hover */
    border-color: white; /* White border on hover */
    transform: scale(1.05); /* Slightly scale up on hover */
}

/* Images Section */
.images-section {
    display: flex;
    gap: 1rem; /* Space between images */
    flex: 1; /* Allow images section to be flexible */
    justify-content: center; /* Center images on small screens */
}

.book-cover {
    width: 120px; /* Reduced size for better responsiveness */
    height: auto;
    border-radius: 15px; /* Rounded corners for images */
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}

.book-cover:hover {
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.7); /* White glow effect */
    transform: scale(1.05); /* Slightly scale up on hover */
}

@media (max-width: 992px) {
    #banner {
        padding: 2.5rem 0; /* Adjust padding for medium screens */
    }

    .text-section h1 {
        font-size: 1.75rem; /* Further reduce font size */
    }

    .text-section h4 {
        font-size: 1.25rem; /* Adjust heading size */
    }

    .book-cover {
        width: 100px; /* Smaller image size */
    }
}

@media (max-width: 768px) {
    .banner-content {
        flex-direction: column; /* Stack text and images on smaller screens */
        text-align: center;
    }
    
    .text-section {
        max-width: 100%; /* Full width for text on small screens */
        margin-bottom: 2rem; /* Space below text */
    }
    
    .images-section {
        flex-direction: row; /* Stack images in a row on small screens */
        justify-content: center; /* Center images */
        flex-wrap: wrap; /* Allow images to wrap if needed */
    }
    
    .book-cover {
        width: 80%; /* Adjust size for smaller screens */
        max-width: 150px; /* Ensure a max width */
        margin: 0 auto; /* Center images */
    }
}

@media (max-width: 576px) {
    #banner {
        padding: 2rem 0; /* Smaller padding for extra small screens */
    }

    .text-section h1 {
        font-size: 1.5rem; /* Further reduce font size */
        margin-bottom: 1rem;
    }

    .text-section h4 {
        font-size: 1rem; /* Adjust heading size */
    }

    .book-cover {
        width: 70%; /* Reduce size further for small screens */
        max-width: 120px; /* Ensure a max width */
    }
}

/* Subscribe to NewsLetter section */
#subscribe {
    background: url('assets/imgs/Banner.png') no-repeat center center;
    background-size: cover;
    padding: 40px 20px;
    text-align: center;
    font-family: 'Roboto', sans-serif;
    color: white; /* Text color for contrast */
}

.newsletter-container {
    background-color: rgba(0, 0, 0, 0.4); /* Dark background with transparency */
    padding: 20px;
    border-radius: 10px;
    backdrop-filter: blur(2.5px); /* Apply blur effect */
    -webkit-backdrop-filter: blur(10px); /* For Safari */
}

#subscribe h3 {
    font-size: 1rem;
    color: white;
    margin-bottom: 15px;
    font-weight: 700;
    /*background-color: #000;
    border-radius: 50px;
    padding: 5px 15px;  Adjust padding to fit text size */
    display: inline-block; /* Ensures the background doesn't extend to the full width */
}

#subscribe p {
    font-size: 1rem;
    color: white;
    margin-bottom: 25px;
}

/* Subscription Form */
#subscribe .form-inline {
    display: flex;
    flex-direction: column;
    max-width: 100%;
    margin: 0 auto;
    align-items: center;
}

#subscribe .form-group {
    width: 100%;
    max-width: 100%;
    display: flex;
    justify-content: center;
    margin-bottom: 10px;
}

#subscribe .form-group input[type="email"] {
    width: 100%;
    max-width: 450px;
    padding: 12px 15px;
    border: 2px solid #ddd;
    border-radius: 50px;
    font-size: 1rem;
    outline: none;
    transition: border-color 0.3s;
}

#subscribe .form-group input[type="email"]:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: none;
}

#subscribe .btn-sub {
    width: auto;
    background-color: #405a64;
    color: #fff;
    border-radius: 30px;
    padding: 12px 25px;
    font-size: 1rem;
    transition: background-color 0.3s;
    border: none;
    margin-left: 10px;
    margin-top: 10px;
    outline: none;
    box-shadow: none;
}

#subscribe .btn-sub:hover {
    background-color: #5e8493;
}

/* Success/Error Messages */
#subscribe .alert {
    max-width: 90%;
    margin: 20px auto;
    border-radius: 10px;
    font-size: 1rem;
    padding: 10px;
}

#subscribe .alert-success {
    background-color: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

#subscribe .alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

/* Responsive Design */
@media (min-width: 576px) {
    #subscribe h3 {
        font-size: 2.5rem;
    }

    #subscribe p {
        font-size: 1.2rem;
    }

    #subscribe .form-inline {
        flex-direction: row;
    }

    #subscribe .form-group {
        max-width: 350px;
    }

    #subscribe .btn-primary {
        margin-left: 10px;
        margin-top: 0;
    }
}

@media (min-width: 768px) {
    #subscribe {
        padding: 50px 30px;
    }

    #subscribe h3 {
        font-size: 3rem;
    }

    #subscribe p {
        font-size: 1.3rem;
    }

    #subscribe .form-inline {
        flex-direction: row;
        max-width: 600px;
    }

    #subscribe .form-group input[type="email"] {
        max-width: 400px;
    }

    #subscribe .btn-primary {
        font-size: 1.1rem;
        padding: 12px 30px;
    }
}

@media (min-width: 992px) {
    #subscribe {
        padding: 60px 50px;
    }

    #subscribe h3 {
        font-size: 3.5rem;
    }

    #subscribe p {
        font-size: 1.4rem;
    }

    #subscribe .form-inline {
        max-width: 700px;
    }

    #subscribe .form-group input[type="email"] {
        max-width: 500px;
    }
}

.alert {
        display: inline-block; /* Shrinks the background to fit the text */
        padding: 10px 20px; /* Adjust padding for better appearance */
        margin: 0 auto; /* Center the alert within its container */
        text-align: center;
    }

/* ket features sectiokn */
.key-features-section {
        background-color: #e8d8c3; /* Blue background */
        color: #44321a; /* White text color */
        padding: 60px 0;
        border-radius: 10px;
    }

    .key-features-section h4 {
        font-size: 1.5rem;
        font-weight: 600;
    }

    .key-features-section p {
        font-size: 1rem;
        color: #6e521a;
    }

    .key-features-section i {
        color: #44321a;
        margin-bottom: 15px;
    }

    @media (max-width: 767.98px) {
        .key-features-section .col-sm-6 {
            margin-bottom: 30px;
        }
    }    
/* CSS HEX 
--silver-rust: #c8bdb4;
--iron: #dadee0;
--limed-spruce: #304745;
--cement: #8f7964;
--shadow: #816a52;
--donkey-brown: #a18e78;
--gumbo: #7c9fa5;
--zorba: #a19b92;
--shingle-fawn: #6a5039;
--periglacial-blue: #e1e4d3;*/
</style>

<body class="index-page">

    
<!--------------------------Home page------------------------------------>

   <!-- Welcome Section -->
<div class="blank-section">
    <div id="text-section">
        <h1>Discover our best sellers</h1>
        <p class="hollow-text">Find Your Next Great Read!</p>
        <a href="shop.php" class="cta-btn">Browse Best Sellers</a> <!-- CTA Button -->
    </div>
</div>
    
    <!-------------Category section------------->
<section class="book-categories">
    <div class="container">
        <h2>Shop By Category</h2>
        <div class="categories-wrapper">
            <div class="categories-container" id="categories-container">
                <!-- Categories will be dynamically inserted here -->
            </div>
            <button class="scroll-btn right">&#8250;</button>
            <button class="scroll-btn left">&#8249;</button> <!-- Added for scrolling left -->
        </div>
    </div>
</section>


   <!-----comment section------>

    <section class="comments-section">
        <div class="comment-container">
            <h2>What Our Readers Say</h2>
            <div class="comments">
                <?php 
                // Check if reviews are fetched
                if ($reviews_result === false) {
                    echo "<p>Error fetching reviews.</p>";
                } else {
                    while ($review = $reviews_result->fetch_assoc()) { 
                        // Fetch profile picture or use default icon if not available
                        $profile_picture = $review['user_profile_picture'];
                        $rating = $review['rating'];
                ?>
                <div class="comment">
                    <div class="comment-header">
                        <?php if (!empty($profile_picture)) { ?>
                            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%;">
                        <?php } else { ?>
                            <i class="bi bi-person-circle" style="font-size: 50px;"></i>
                        <?php } ?>
                        <p class="comment-author"><?php echo htmlspecialchars($review['user_name']); ?></p>
                    </div>
                    <p class="comment-text"><?php echo htmlspecialchars($review['comment']); ?></p>
                    <div class="star-rating">
                        <?php
                        // Display star rating
                        for ($i = 0; $i < 5; $i++) {
                            if ($i < floor($rating)) {
                                echo '<i class="fa fa-star"></i>';
                            } elseif ($i == ceil($rating) - 1 && $rating - floor($rating) > 0) {
                                echo '<i class="fa fa-star-half"></i>';
                            } else {
                                echo '<i class="fa fa-star-o"></i>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php } } ?>
            </div>
        </div>
    </section>

<!-----------shop and accessories container
    <div class="container-section">
        <div class="book-container" id="book-container">
            <h4>Shop Books</h4>
            <?php if (!empty($books)): ?>
                <?php foreach ($books as $index => $book): ?>
                    <div class="book-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($book['product_image']); ?>" alt="<?php echo htmlspecialchars($book['product_name']); ?>" onerror="this.src='assets/img/placeholder.jpg';">
                        <div class="book-info">
                            <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 5px;" ><?php echo htmlspecialchars($book['product_name']); ?></h3>
                            <p>Price: PKR<?php echo number_format($book['product_price'], 2); ?></p>
                            <p>Category: <?php echo htmlspecialchars($book['product_category']); ?></p>
                        </div>
                        <a href="shop.php" class="discover-container">
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No books found.</p>
            <?php endif; ?>
        </div>

        <div class="book-container" id="accessories-container">
            <h4>Shop Accessories</h4>
            <?php if (!empty($accessories)): ?>
                <?php foreach ($accessories as $index => $accessory): ?>
                    <div class="book-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($accessory['product_image']); ?>" alt="<?php echo htmlspecialchars($accessory['product_name']); ?>" onerror="this.src='assets/img/placeholder.jpg';">
                        <div class="book-info">
                            <h3 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 5px;" ><?php echo htmlspecialchars($accessory['product_name']); ?></h3>
                            <p>Price: $<?php echo number_format($accessory['product_price'], 2); ?></p>
                            <p>Category: <?php echo htmlspecialchars($accessory['product_category']); ?></p>
                        </div>
                        <a href="accessories.php" class="discover-container">
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No accessories found.</p>
            <?php endif; ?>
        </div>
    </div>
    ------------>


   <!-- Featured Section sci-fi-->
<section id="featuredd" class="my-5 pb-5">
    <div class="container text-center mt-0 py-0">
        <h3>Featured Science Fiction and Fantasy Books</h3>
        <hr class="mx-auto">
        <p>Explore our handpicked selection of the best Science Fiction and Fantasy books for your home library.</p>
        <!-- View More Link -->
        <div class="text-end">
            <a href="shop.php?product_category=Science Fiction and Fantasy" class="btn view-more-btn">
                View More
                <i class="bi bi-arrow-right-circle"></i> <!-- Bootstrap icon for the arrow -->
            </a>
        </div>
    </div>
    <div class="row mx-auto container-fluid">
        <?php 
        // Include the file that queries featured products
        include('server/get_featured_products.php'); 

        // Display each featured product
        while($row = $featured_products->fetch_assoc()) { ?>
                    <div class="col-lg-1-5 col-md-2-4 col-sm-3-3 mb-4"> <!-- Adjusted column classes for 8 items per row -->
                    <div class="card h-100 text-center">
                    <img class="card-img-top img-fluid" src="assets/imgs/<?php echo $row['product_category']; ?>/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo $row['product_name']; ?></h5>
                        <p class="card-text">Rs<?php echo $row['product_price']; ?></p>
                        <a href="single-product.php?product_id=<?php echo $row['product_id']; ?>" class="featuredd-btn btn-outline-dark mt-auto">Buy Now</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>


<!-- Featured Section grafic novels-->
<section id="featuredd" class="my-5 pb-5">
    <div class="container text-center mt-0 py-0">
        <h3>Featured Graphic Novels and Comics</h3>
        <hr class="mx-auto">
        <p>Dive into our curated collection of top Graphic Novels and Comics, perfect for adding a touch of adventure and artistry to your bookshelf.</p>
        <!-- View More Link -->
        <div class="text-end">
            <a href="shop.php?product_category=Graphic Novels and Comics" class="btn view-more-btn">
                View More
                <i class="bi bi-arrow-right-circle"></i> <!-- Bootstrap icon for the arrow -->
            </a>
        </div>
    </div>
    <div class="row mx-auto container-fluid">
        <?php 
        // Include the file that queries featured products
        include('server/get_featured_novels.php'); 

        // Display each featured product
        while($row = $featured_products->fetch_assoc()) { ?>
                    <div class="col-lg-1-5 col-md-2-4 col-sm-3-3 mb-4"> <!-- Adjusted column classes for 8 items per row -->
                    <div class="card h-100 text-center">
                    <img class="card-img-top img-fluid" src="assets/imgs/<?php echo $row['product_category']; ?>/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo $row['product_name']; ?></h5>
                        <p class="card-text">Rs<?php echo $row['product_price']; ?></p>
                        <a href="single-product.php?product_id=<?php echo $row['product_id']; ?>" class="featuredd-btn btn-outline-dark mt-auto">Buy Now</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>


    <!-----------Banner------------->
<section id="banner" class="my-5 py-5" style="background-image: url('assets/imgs/pakistan_flag_banner.jpg'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="banner-content">
            <div class="text-section">
                <h4>AZADI SALE</h4>
                <h1>Celebrate with Us<br>UP to 30% OFF</h1>
                <button class="text-uppercase">Shop Now</button>
            </div>
            <div class="images-section">
                <img src="assets/imgs/Poetry/Alif Allah.jpg" alt="Book Cover 1" class="book-cover">
                <img src="assets/imgs/Biographies and Memoirs/In The Line Of Fire.jpg" alt="Book Cover 2" class="book-cover">
                <img src="assets/imgs/Biographies and Memoirs/Tippoo Sultaun by Philip Meadows Taylor.webp" alt="Book Cover 3" class="book-cover">
            </div>
        </div>
    </div>
</section>


    <!-- PopUp 
<div class="popup hide-popup">
  <div class="popup-content">
    <div class="popup-close">
      <i class='bx bx-x'></i>
    </div>
    <div class="popup-left">
      <div class="popup-img-container">
        <img class="popup-img" src="./images/popup.jpg" alt="popup">
      </div>
    </div>
    <div class="popup-right">
      <div class="right-content">
        <h1>Get Discount <span>50%</span> Off</h1>
        <p>Sign up to our newsletter and save 30% on your next purchase. No spam, we promise!</p>
        <form id="popup-form">
          <input type="email" placeholder="Enter your email..." class="popup-form" required>
          <button type="submit">Subscribe</button>
        </form>
      </div>
    </div>
  </div>
</div>-->


 <!------------Promo swction-------------->
    <section class="promo-section">
        <div class="promo-container">
            <div class="promo-wrapper">
                <div class="promo-item" style="background-color: #9BBDBE;">
                    <h3>New Arrival</h3>
                    <a href="/shop.php?product_category=Children’s Books+Books" class="btn promo-btn" id="promo-btn" >Explore</a>
                    <img src="assets/imgs/Education and Textbooks/MY MARVELLOUS MIND.webp" alt="New Arrival">
                </div>
                <div class="promo-item" style="background-color: #D1B8D9;">
                    <h3>Top Rated</h3>
                    <a href="/shop.php?product_category=Children’s Books+Books" class="btn promo-btn" id="promo-btn" >Explore</a>
                    <img src="assets/imgs/Children’s Books/The BFG.jpg" alt="Top Rated">
                </div>
                <!-----<div class="promo-item" style="background-color: #F1CB8F;">
                    <h3>Get 15% Off</h3>
                    <a href="accessories.php" class="btn promo-btn">Shop Now</a>
                    <img src="assets/imgs/Accessories/Cozy Pot Reading Book.webp" alt="Discount">
                </div>---->
            </div>
        </div>
    </section>


 <!-----------Key features section---------->             
 <section class="key-features-section mt-5 py-5 text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3 col-sm-6 mb-4">
                <i class="fas fa-truck fa-3x mb-3"></i>
                <h4 class="mb-2">Quick Delivery</h4>
                <p class="text-muted">Fast and reliable shipping to your doorstep.</p>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <i class="fas fa-hand-holding-usd fa-3x mb-3"></i>
                <h4 class="mb-2">Cash on Delivery</h4>
                <p class="text-muted">We offer COD as the only payment method for trust and convenience.</p>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <i class="fas fa-star fa-3x mb-3"></i>
                <h4 class="mb-2">Best Quality</h4>
                <p class="text-muted">Handpicked selection of top-quality products.</p>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <i class="fas fa-headset fa-3x mb-3"></i>
                <h4 class="mb-2">Great Customer Service</h4>
                <p class="text-muted">24/7 support and assistance for all your queries.</p>
            </div>
        </div>
    </div>
</section>


<!-- Subscribe for Newsletters Section -->
<section id="subscribe" class="my-5 py-5 text-center">
    <div class="newsletter-container">
        <h3 class="mb-4" style="font-size: 2.5rem;">Subscribe to Our Newsletter</h3>
        <p>Stay updated with our latest releases, special offers, and more. Sign up now!</p>
        
        <!-- Subscription Form -->
        <form action="subscribe.php" method="POST" class="form-inline d-flex justify-content-center mt-4">
            <div class="form-group mb-2">
                <label for="email" class="sr-only">Email</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn btn-sub mb-2 ms-2">Subscribe</button>
        </form>

        <!-- Success/Error Messages -->
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success mt-4" role="alert">
                Thank you for subscribing to our newsletter!
            </div>
        <?php elseif(isset($_GET['error']) && $_GET['error'] == 1): ?>
            <div class="alert alert-danger mt-4" role="alert">
                There was an error. Please try again later.
            </div>
        <?php elseif(isset($_GET['error']) && $_GET['error'] == 2): ?>
            <div class="alert alert-warning mt-4" role="alert">
                You have already subscribed to our newsletter!
            </div>
        <?php endif; ?>
    </div>
</section>



<?php include 'footer.php'; ?>

 <!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!---<script>
    $(document).ready(function() {
    let currentIndex = 0;
    const items = $('#top-section .top-image-item');
    const totalItems = items.length;

    function showNextItem() {
        $(items[currentIndex]).removeClass('active').addClass('inactive');
        currentIndex = (currentIndex + 1) % totalItems;
        $(items[currentIndex]).removeClass('inactive').addClass('active');
    }

    setInterval(showNextItem, 3000); // Change items every 3 seconds
});
</script> --->   

<script>
  $(document).ready(function() {
            function startSlider(containerId) {
                let currentIndex = 0;
                const items = $(`#${containerId} .book-item`);
                const totalItems = items.length;

                function showNextItem() {
                    $(items[currentIndex]).removeClass('active');
                    currentIndex = (currentIndex + 1) % totalItems;
                    $(items[currentIndex]).addClass('active');
                }

                setInterval(showNextItem, 3000); // Change items every 3 seconds
            }

            startSlider('book-container');
            startSlider('accessories-container');
        });
</script>

<script>
        // Category section
        const categories = [
            'Non-fiction', 'Fiction', 'Romance', 'Self-Help', 'Cookbooks', 'Travel', 'Poetry',
        ];

        // Define background colors for each category
        const categoryBackgroundColors = {
            'Non-fiction': '#9998A9',
            'Fiction': '#BF9599',
            'Romance': '#D1B8D9',
            'Self-Help': '#99B68D',
            'Cookbooks': '#D2B48C',
            'Travel': '#9BBDBE',
            'Poetry': '#F1CB8F',
        };

        // Define image paths for each category
        const categoryImages = {
            'Non-fiction': ['Outliers.jpg'],
            'Fiction': ['Harry Potter And The Deathly Hallows.jpg'],
            'Romance': ['After Ever Happy.jpg'],
            'Self-Help': ['Dirty Laundry.jpg'],
            'Cookbooks': ['Beyond Measure Pakistani Cooking by Feel with GoldenGully.jpg'],
            'Travel': ['Slow Trains To Istanbul - ... And Back.jpg'],
            'Poetry': ['Rumi- Poems.jpg'],
        };

        const container = document.getElementById('categories-container');

        // Create category elements
        categories.forEach(category => {
            const categoryElement = createCategoryElement(category);
            container.appendChild(categoryElement);
        });

        function createCategoryElement(category) {
            const categoryDiv = document.createElement('div');
            categoryDiv.className = 'category';
            
            const categoryName = document.createElement('div');
            categoryName.className = 'category-name';
            categoryName.innerText = category;
            
            const img = document.createElement('img');
            const imgPath = `assets/imgs/${category.replace(/ /g, '-')}/` + getRandomImageFromCategory(category);
            img.src = imgPath;
            
            const bgColor = categoryBackgroundColors[category] || '#FFFFFF';
            categoryDiv.style.backgroundColor = bgColor;

            categoryDiv.appendChild(categoryName);
            categoryDiv.appendChild(img);
            
            categoryDiv.addEventListener('click', () => {
                window.location.href = `shop.php?category=${category}`;
            });

            return categoryDiv;
        }

        function getRandomImageFromCategory(category) {
            const images = categoryImages[category] || [];
            return images[Math.floor(Math.random() * images.length)];
        }

        // Improved scroll functionality
        let scrollInterval;

        function startScrolling(speed) {
            scrollInterval = setInterval(() => {
                container.scrollBy({ left: speed, behavior: 'smooth' });
            }, 10); // Adjust the interval for faster or slower scrolling
        }

        function stopScrolling() {
            clearInterval(scrollInterval);
        }

        // Scroll left and right with faster speed
        document.querySelector('.scroll-btn.left').addEventListener('mouseenter', function() {
            startScrolling(-400); // Faster scroll to the left
        });

        document.querySelector('.scroll-btn.left').addEventListener('mouseleave', stopScrolling);

        document.querySelector('.scroll-btn.right').addEventListener('mouseenter', function() {
            startScrolling(400); // Faster scroll to the right
        });

        document.querySelector('.scroll-btn.right').addEventListener('mouseleave', stopScrolling);

        // Clone elements when reaching the end
        container.addEventListener('scroll', function() {
            if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 300) {
                const originalCategories = document.querySelectorAll('.category');
                originalCategories.forEach(category => {
                    const clone = category.cloneNode(true);
                    clone.addEventListener('click', () => {
                        const categoryName = category.querySelector('.category-name').innerText;
                        window.location.href = `shop.php?category=${categoryName}`;
                    });
                    container.appendChild(clone);
                });
            }
        });
</script>



<script>
//popup
document.addEventListener("DOMContentLoaded", function() {
  const popup = document.querySelector(".popup");
  const closePopup = document.querySelector(".popup-close");
  const form = document.getElementById("popup-form");

  // Show popup after 1 second
  setTimeout(() => {
    if (!localStorage.getItem("popupClosed")) {
      popup.classList.add("show");
    }
  }, 1000);

  // Close popup when clicking on the close button
  closePopup.addEventListener("click", () => {
    popup.classList.remove("show");
    localStorage.setItem("popupClosed", "true");
  });

  // Close popup and store the event when the form is submitted
  form.addEventListener("submit", (event) => {
    event.preventDefault(); // Prevent actual form submission
    alert("Thank you for subscribing!");
    popup.classList.remove("show");
    localStorage.setItem("popupClosed", "true");
  });
});


</script>

<script>
    setTimeout(function() {
        var alertContainer = document.getElementById('alert-container');
        if (alertContainer) {
            alertContainer.style.transition = 'opacity 0.5s ease';
            alertContainer.style.opacity = '0';
            setTimeout(function() {
                alertContainer.style.display = 'none';
            }, 500); // Match this timeout with the transition duration
        }
    }, 5000); // Hide after 5 seconds
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hollowText = document.querySelector('.hollow-text');

    // Function to restart animation
    function restartAnimation() {
        hollowText.style.animation = 'none'; // Stop current animation
        hollowText.offsetHeight; // Trigger a reflow to reset the animation
        hollowText.style.animation = ''; // Restart animation
    }

    // Restart animation every 30 seconds
    setInterval(restartAnimation, 10000); // 30000 ms = 30 seconds
});
</script>

</body>
</html>


