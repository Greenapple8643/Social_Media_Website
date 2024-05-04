<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
include("./common/header.php");
include_once "Functions.php";
include_once "EntityClassLib.php";

$dbConnection = parse_ini_file("LAB5.ini");

if ($dbConnection === false) {
    die("Failed to read the INI file: " . error_get_last()["message"]);
}



$userId = "";
$password = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btnLogin"])) {
    $userId = $_POST["user_id"];
    $password = $_POST["password"];

    // Validation
    if (empty($userId)) {
        $errors['user_id'] = "User ID is required.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }
    
    if(empty($errors)) {
        $user = getUserByIdAndPassword($userId, $password);
        if($user) {
            // Set session variable on successful login
            $_SESSION["login"] = "OK";
            $_SESSION["user_id"] = $userId; // Or any other user information you need

            // Redirect to CourseSelection page
            header("Location: MyAlbums.php");
            exit;
        } else {
            $errorMessageWrongId = "Incorrect Student ID and/or Password";
        }
    }
    

    
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Log In</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        p {
            text-align: center;
            color: #555;
            margin-bottom: 20px;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            text-align: center;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"],
        input[type="reset"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        input[type="submit"]:hover,
        input[type="reset"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Log In</h1>
        <p>You need to <a href="NewUser.php">sign up</a> if you are a new user</p>

        <?php
        if (isset($errors['common'])) {
            echo '<div class="error-message"><p>' . $errors['common'] . '</p></div>';
        }
        ?>

        <form action="Login.php" method="post">
            <label for="user_id">User ID:</label>
            <input type="text" id="user_id" name="user_id" value="<?php echo htmlspecialchars($userId); ?>"><br>
            <?php if (isset($errors['user_id'])) echo '<p class="error-message">' . $errors['user_id'] . '</p>'; ?>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password"><br>
            <?php if (isset($errors['password'])) echo '<p class="error-message">' . $errors['password'] . '</p>'; ?>

            <input type="submit" name="btnLogin" value="Submit">
            <input type="reset" value="Clear">
        </form>
    </div>

    <?php include('./common/footer.php'); ?>

</body>

</html>
