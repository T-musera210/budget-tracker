<?php
session_start();

// If user is already logged in, redirect to index.php
if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Budget Tracker</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Ensure the body and html take up the full height of the window */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('image/budget.jpg') no-repeat center center/cover; /* Full-page background image */
            background-size: cover;  /* Make sure the image covers the entire screen */
        }

        /* Full-screen container for landing content */
        .landing-container {
            background: rgba(255, 255, 255, 0.85); /* Semi-transparent background to allow image to show through */
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 500px; /* Maximum width for the content */
            position: relative; /* So it can stack content on top of it */
            z-index: 1;  /* Make sure content is above the background */
        }

        /* Large heading */
        h1 {
            font-size: 2.8rem;
            color: #FF6F61;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        /* Paragraph text */
        p {
            margin: 15px 0;
            font-size: 1.3rem;
            line-height: 1.6;
            color: #333; /* Ensure readability on the semi-transparent background */
        }
        
        /* CTA buttons */
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: bold;
        }
        
        .login-btn {
            background: #FFB6C1;
            color: white;
        }
        
        .login-btn:hover {
            background: #FF9DAD;
        }
        
        .register-btn {
            background: #FFD166;
            color: #333;
        }
        
        .register-btn:hover {
            background: #FFAB00;
        }

        /* Additional text styling */
        .cta-text {
            color: #333;
            font-size: 1.2rem;
            margin-top: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <h1>Welcome to Budget Tracker</h1>
        <p>Track your expenses and manage your finances in a cute, easy way!</p>
        
        <!-- Buttons for Login and Register -->
        <a href="auth.php"><button class="btn login-btn">Login / Register</button></a>
        
        <div class="cta-text">
            <p>Your wallet will thank you later!</p>
        </div>
    </div>
</body>
</html>
