<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'dbforum';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $recaptchaResponse = $_POST["g-recaptcha-response"]; 


       $secretKey = "6LdofsgnAAAAAMaiht4p5Prd9mnDaczPRJiagQNs";

    
    $recaptchaUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$recaptchaResponse";
    $recaptchaResponseData = file_get_contents($recaptchaUrl);
    $recaptchaResult = json_decode($recaptchaResponseData);

    if ($recaptchaResult->success) {
        $sql = "SELECT id, password FROM user_data WHERE username = '$username'";
        $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            $_SESSION["user_id"] = $row["id"];
            header("Location: asg_to_uploads.php"); 
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }
}
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
        background-color: #ffffff;
        border-radius: 8px;
        padding: 150px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error {
            color: #ff5c5c;
            font-size: 14px;
            margin-top: 10px;
        }

        .register-link {
            text-align: center;
            margin-top: 10px;
        }
    </style>

 <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="container">
        <h2>Login Form</h2>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" required><br>
          
            <div class="g-recaptcha" data-sitekey="6LdofsgnAAAAAIV_4pCOvHtgji_FmKzsbn2on11u"></div>
            <input type="submit" value="Login">
        </form>
        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>


