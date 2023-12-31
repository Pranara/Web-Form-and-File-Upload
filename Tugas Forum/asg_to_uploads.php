<?php
define("ROLE_A", "Role_A");
define("ROLE_B", "Role_B");

$host = 'localhost';  
$username = 'root'; 
$password = ''; 
$dbname = 'dbforum';   

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function hasRoleA($userRole) {
    return $userRole === ROLE_A;
}

function hasRoleB($userRole) {
    return $userRole === ROLE_B;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Your File</title>
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

    h1 {
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

    input[type="email"],
    input[type="file"],
    select {
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

    .success {
        color: #2ecc71;
        font-size: 14px;
        margin-top: 10px;
    }

    select {
        background-color: #f7f7f7;
    }

    input[type="file"] {
        background-color: #f7f7f7;
    }

</style>


</head>
<body>
    <div class="container">
        <h1>Upload Your File</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <label for="userEmail">Email:</label>
            <input type="email" id="userEmail" name="userEmail" required>

            <label for="userRole">Select your role:</label>
            <select id="userRole" name="userRole" required>
                <option value="<?php echo ROLE_A; ?>">Role A</option>
                <option value="<?php echo ROLE_B; ?>">Role B</option>
            </select>

            <label for="uploadFile">Select a JPEG or PNG file:</label>
            <input type="file" id="uploadFile" name="uploadFile" accept=".jpg, .jpeg, .png" required>

            <input type="submit" value="Submit">
        </form>

        <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["userEmail"]) && isset($_POST["userRole"])) {
        $userEmail = $_POST["userEmail"];
        $userRole = $_POST["userRole"];

        $stmt = $db->prepare("SELECT role FROM user_data WHERE email = :email");
        $stmt->bindParam(':email', $userEmail);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            $dbUserRole = $userData['role'];

            if ($dbUserRole === ROLE_A && hasRoleA($userRole)) {
                if ($_FILES["uploadFile"]["error"] == UPLOAD_ERR_NO_FILE) {
                    echo "<p class='error'>Please select a file.</p>";
                } elseif (in_array($_FILES["uploadFile"]["type"], ['image/jpeg', 'image/png'])) {
                    $targetDirectory = "dump_file/";
                    $targetFile = $targetDirectory . basename($_FILES["uploadFile"]["name"]);

                    if (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $targetFile)) {
                        $fileContent = file_get_contents($targetFile);

                        try {
                            $stmt = $db->prepare("INSERT INTO uploaded_files (file_data, file_path) VALUES (:file_data, :file_path)");
                            $stmt->bindParam(':file_data', $fileContent, PDO::PARAM_LOB);
                            $stmt->bindParam(':file_path', $targetFile);
                            $stmt->execute();
                            echo "<p class='success'>File uploaded successfully and data stored in the database.</p>";
                        } catch (PDOException $e) {
                            echo "<p class='error'>Error storing data in the database: " . $e->getMessage() . "</p>";
                        }
                    } else {
                        echo "<p class='error'>Error uploading file.</p>";
                    }
                } else {
                    echo "<p class='error'>Only JPEG and PNG files are allowed.</p>";
                }
            } elseif ($dbUserRole === ROLE_B && hasRoleB($userRole)) {
                if ($_FILES["uploadFile"]["error"] == UPLOAD_ERR_NO_FILE) {
                    echo "<p class='error'>Please select a file.</p>";
                } elseif (in_array($_FILES["uploadFile"]["type"], ['image/jpeg', 'image/png'])) {
                    $targetDirectory = "dump_file/";
                    $targetFile = $targetDirectory . basename($_FILES["uploadFile"]["name"]);

                    if (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $targetFile)) {
                        $fileContent = file_get_contents($targetFile);

                        try {
                            // Store file data in the database
                            $stmt = $db->prepare("INSERT INTO uploaded_files (file_data, file_path) VALUES (:file_data, :file_path)");
                            $stmt->bindParam(':file_data', $fileContent, PDO::PARAM_LOB);
                            $stmt->bindParam(':file_path', $targetFile);
                            $stmt->execute();
                            echo "<p class='success'>File uploaded successfully and data stored in the database.</p>";
                        } catch (PDOException $e) {
                            echo "<p class='error'>Error storing data in the database: " . $e->getMessage() . "</p>";
                        }
                    } else {
                        echo "<p class='error'>Error uploading file.</p>";
                    }
                } else {
                    echo "<p class='error'>Only JPEG and PNG files are allowed.</p>";
                }
            } else {
                echo "<p class='error'>You don't have permission to upload files or incorrect role selection.</p>";
            }
        } else {
            echo "<p class='error'>User not found or incorrect role.</p>";
        }
    }
}
?>
    </div>
</body>
</html>