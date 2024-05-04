<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
include_once "Functions.php";
include_once "EntityClassLib.php";

// Include database connection and common header
include("./common/header.php");

$accessibilityOptions = getAccessibility();

if (!(isset($_SESSION["login"]) && $_SESSION["login"] == "OK")) {
    header("Location: Login.php");
    exit();
}

$userId = $_SESSION["user_id"];
$userName = isset($_SESSION["user_name"]) ? $_SESSION["user_name"] : '';


$title = isset($_POST['title']) ? $_POST['title'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addSubmit"])) {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $accessibilityCode = $_POST["accessibility"];

    insertAlbum($title, $description, $userId, $accessibilityCode);
    // Redirect to a success page or perform other actions
    header("Location: MyAlbums.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Album</title>
    <!-- Add your CSS or other head elements here -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        h1 {
            color: #333;
        }

        p {
            color: #555;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
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
    <div class="container">
        <h1>Create New Album</h1>
        <p>Welcome <?php echo $userName; ?> (<a href="Logout.php">not you? change user here</a>)</p>

        <form action="AddAlbum.php" method="post">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>

            <label for="accessibility">Accessibility:</label>
            <select id="accessibility" name="accessibility" required>
                <?php

                foreach ($accessibilityOptions as $option) :
                ?>
                    <option value="<?php echo $option['Accessibility_Code']; ?>">
                        <?php echo $option['Description']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($description); ?></textarea>

            <input type="submit" name="addSubmit" value="Submit">
            <input type="reset" value="Clear">
        </form>
    </div>
</body>

<?php include('./common/footer.php'); ?>

</html>