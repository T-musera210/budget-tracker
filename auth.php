<?php
session_start();
$host = "localhost";
$user = "root"; // Change if necessary
$pass = ""; // Change if necessary
$db = "budget_tracker"; // Change if necessary

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Register User
if (isset($_POST["register"])) {
    $username = trim($_POST["reg_username"]);
    $email = trim($_POST["reg_email"]);
    $password = password_hash($_POST["reg_password"], PASSWORD_DEFAULT);

    if (!empty($username) && !empty($email) && !empty($_POST["reg_password"])) {
        // Ensure email is unique
        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();
        
        if ($checkEmail->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful! You can now log in.');</script>";
            } else {
                echo "<script>alert('Error: Could not register user.');</script>";
            }
        } else {
            echo "<script>alert('Email already registered. Use a different one.');</script>";
        }
    } else {
        echo "<script>alert('Please fill all fields.');</script>";
    }
}

// Login User (Accepts Username OR Email)
if (isset($_POST["login"])) {
    $userInput = trim($_POST["login_user_email"]);
    $password = $_POST["login_password"];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $userInput, $userInput);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $username;

            header("Location: index.php"); // Redirect to index.php after successful login
            exit();
        } else {
            echo "<script>alert('Invalid password. Try again!');</script>";
        }
    } else {
        echo "<script>alert('User not found. Please register first.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Style block goes here */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('image/budget.jpg') no-repeat center center/cover; 
            background-size: cover;
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        h2 {
            color: #FF6F61;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .form-toggle button {
            padding: 10px 15px;
            margin: 10px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 30px;
            background-color: #06D6A0;
            color: white;
        }

        .form-toggle button:hover {
            background: #04B58A;
            transform: scale(1.05);
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        input:focus {
            border-color: #06D6A0;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #FFB6C1;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        button:hover {
            background: #FF9DAD;
            color: white;
            transform: scale(1.05);
        }

        .form-container {
            margin-bottom: 20px;
        }

        .cta-text {
            font-size: 1rem;
            color: #333;
            margin-top: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Welcome to Budget Tracker</h2>

        <div class="form-toggle">
            <button onclick="showLogin()">Login</button>
            <button onclick="showRegister()">Register</button>
        </div>

        <!-- Login Form -->
        <form id="loginForm" method="POST">
            <div class="form-container">
                <h3>Login</h3>
                <input type="text" name="login_user_email" placeholder="Username or Email" required>
                <input type="password" name="login_password" placeholder="Password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>

        <!-- Register Form -->
        <form id="registerForm" method="POST" style="display: none;">
            <div class="form-container">
                <h3>Register</h3>
                <input type="text" name="reg_username" placeholder="Username" required>
                <input type="email" name="reg_email" placeholder="Email" required>
                <input type="password" name="reg_password" placeholder="Password" required>
            </div>
            <button type="submit" name="register">Register</button>
        </form>

        <div class="cta-text">
            <p>Let's start tracking your expenses!</p>
        </div>
    </div>

    <script>
        function showLogin() {
            document.getElementById("loginForm").style.display = "block";
            document.getElementById("registerForm").style.display = "none";
        }

        function showRegister() {
            document.getElementById("registerForm").style.display = "block";
            document.getElementById("loginForm").style.display = "none";
        }
    </script>
</body>
</html>
