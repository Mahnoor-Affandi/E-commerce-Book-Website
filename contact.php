
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - أساس الحكمة</title>
    <?php include 'header.php'; ?>

    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(to right, #cfd8dc, #90a4ae); /* Bluish-grey gradient background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        header {
            text-align: center;
        }
        header h1 {
            font-size: 1.2rem;
            color: #444;
            font-weight: bold;
        }
        header p {
            margin-top: 120px;
            font-size: 1.2rem;
            color: #666;
            font-weight: bold;
            align-items: center;
            justify-content: center;
        }

        form {
            background: rgba(255, 255, 255, 0.2); /* semi-transparent background */
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            align-items: center;
            margin-top: 100px;

        }
        h4{
            margin-bottom: 10px;
            font-weight: bold;
            text-align: center;
        }
        form .form-group {
            margin-bottom: 15px;
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input, form textarea {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.5);
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        form textarea {
            border-radius: 20px; /* Less rounded corners for textarea */
        }
        #submit{
            display: block; /* make button block-level element */
            margin: 20px auto 0; /* center button horizontally */
            padding: 10px 15px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 500px;

        }
        #submit:hover {
            background-color: #555;
        }
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.7);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            text-align: center;
            max-width: 400px;
            width: 80%;
        }
        .popup button {
            padding: 10px 15px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .popup button:hover {
            background-color: #555;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 500;
        }

          /* Responsive Styles */
          @media (max-width: 768px) {
            .container {
                margin: 30px 15px;
                padding: 15px;
            }

            header h1 {
                font-size: 1.8rem;
            }

            header p {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                margin: 20px 10px;
                padding: 10px;
            }

            header h1 {
                font-size: 1.5rem;
            }

            header p {
                font-size: 0.8rem;
            }

            .form-group label, .form-group input, .form-group textarea, button[type="submit"] {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
        <form id="contactForm" action="process_contact.php" method="post">
            <h4>contact us</h4>
            
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" id="submit" >Submit</button>
        </form>

    <div class="overlay" id="overlay"></div>
    <div class="popup" id="popup">
        <p>Thank you for contacting us! We will get back to you shortly.</p>
        <button onclick="window.location.href='index.php'">Back to Home</button>
    </div>

    <script>
        document.getElementById('contactForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            
            fetch('process_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('overlay').style.display = 'block';
                document.getElementById('popup').style.display = 'block';
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
