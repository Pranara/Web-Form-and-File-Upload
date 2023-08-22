<?php
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
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $role = $_POST["role"];


    $checkUsernameQuery = "SELECT id FROM user_data WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($checkUsernameQuery);

    if ($result->num_rows > 0) {
        echo "Username or email already exists. Please choose a different username or email.";
    } else {
        $sql = "INSERT INTO user_data (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";

        if ($conn->query($sql) === true) {
            header("Location: index.php");
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
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
        input[type="password"],
        input[type="email"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
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

        .center-text {
            text-align: center;
            margin-top: 10px;
        }

        .center-text a {
            color: #007bff;
            text-decoration: none;
        }

        .center-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registration Form</h2>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            
            <label for="email">Email:</label>
            <input type="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            
            <label for="role">Select your role:</label>
            <select id="role" name="role" required>
                <option value="Role_A">A</option>
                <option value="Role_B">B</option>
            </select>
            
            <input type="submit" value="Register">
        </form>
        <div class="center-text">
            Already have an account? <a href="index.php">Login here</a>
        </div>
    </div>
</body>
</html>
