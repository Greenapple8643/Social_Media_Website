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
$name = "";
$phone = "";
$password = "";
$passwordAgain = "";
$userIdError = "";
$nameError = "";
$phoneError = "";
$passwordError = "";
$passwordAgainError = "";
$userExistsError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["regSubmit"])) {
    $userId = trim($_POST["user_id"]);
    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);
    $password = trim($_POST["password"]);
    $passwordAgain = trim($_POST["password_again"]);


    if (empty($userId)) {
        $userIdError = "User ID is required.";
    } 

    if (empty($name)) {
        $nameError = "Name is required.";
    }

    if (empty($phone)) {
        $phoneError = "Phone is required.";
    } elseif (!preg_match('/^\d{3}-\d{3}-\d{4}$/', $phone)) {
        $phoneError = "Phone must be in the format nnn-nnn-nnnn.";
    }

    if (empty($password)) {
        $passwordError = "Password is required.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/', $password)) {
        $passwordError = "Password must be at least 6 characters long and contain at least one uppercase letter, one lowercase letter, and one digit.";
    }

    if (empty($passwordAgain)) {
        $passwordAgainError = "Password Again is required.";
    } elseif ($passwordAgain !== $password) {
        $passwordAgainError = "Passwords do not match.";
    }
    
    if(empty($nameError) && empty($phoneError) && empty($passwordError) && empty($passwordAgainError) && empty($userExistsError)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            addNewUser($userId, $name, $phone, $hashedPassword);
            $_SESSION["login"] = "OK";
            $_SESSION["user_id"] = $userId;
            header("Location: MyAlbums.php");
            exit();
        } 
         catch (Exception $e) {
            error_log($e->getMessage()); 
            die("An error occurred: " . $e->getMessage()); 
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New User Registration</title>
    <!-- Add your CSS or other head elements here -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        form {
            max-width: 400px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        h2 {
            color: #333;
        }

        p {
            color: #555;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }

        input[type="submit"],
        input[type="reset"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
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
    <form action="NewUser.php" method="post">
        <h2>New User Registration</h2>
        <p>All fields are required</p>

        <div>
            <label for="user_id">User ID:</label>
            <input type="text" id="user_id" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
            <?php if (!empty($userIdError)) echo '<p class="error-message">' . $userIdError . '</p>'; ?>
            <?php if (!empty($userExistsError)) echo '<p class="error-message">' . $userExistsError . '</p>'; ?>
        </div>

        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
            <?php if (!empty($nameError)) echo '<p class="error-message">' . $nameError . '</p>'; ?>
        </div>

        <div>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
            <?php if (!empty($phoneError)) echo '<p class="error-message">' . $phoneError . '</p>'; ?>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>">
            <?php if (!empty($passwordError)) echo '<p class="error-message">' . $passwordError . '</p>'; ?>
        </div>

        <div>
            <label for="password_again">Password Again:</label>
            <input type="password" id="password_again" name="password_again" value="<?php echo isset($_POST['password_again']) ? htmlspecialchars($_POST['password_again']) : ''; ?>">
            <?php if (!empty($passwordAgainError)) echo '<p class="error-message">' . $passwordAgainError . '</p>'; ?>
        </div>

        <div>
            <input type="submit" name='regSubmit' value="Submit">
            <input type="reset" value="Clear">
        </div>
    </form>
</body>

<?php include('./common/footer.php'); ?>

</html>
