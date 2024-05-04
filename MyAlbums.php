<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
include("./common/header.php");
include_once "Functions.php";
include_once "EntityClassLib.php";
echo "<pre>";
echo $_SESSION["user_id"];
echo "</pre>";

$dbConnection = parse_ini_file("Lab5.ini");


extract($dbConnection);
$albums = getAlbum($_SESSION["user_id"]);

$errors = [];

if (!(isset($_SESSION["login"]) && $_SESSION["login"] == "OK")) {
    header("Location: Login.php");
    exit();
}

$userId = $_SESSION["user_id"];
$userName = getStudentName($userId);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["saveBtn"])) {
        foreach ($_POST["accessibility"] as $albumId => $selectedAccessibilityCode) {
        updateAlbum( $selectedAccessibilityCode, $albumId);
    }

    header("Location: MyAlbums.php");
    exit();
}

// Check if delete request is received
if (isset($_GET["deleteAlbumId"])) {
    foreach($albums as $album) {
        deleteAlbum($album['Album_Id']);
    }
    header("Location: MyAlbums.php");
    exit();
}
$accessibilityOptions = getAccessibility();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Albums</title>
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
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
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

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>My Albums</h1>
        <p>Welcome <?php echo $userName; ?> (<a href="Logout.php">not you? change user here</a>)</p>

        <a href="AddAlbum.php">Create a New Album</a>

        <form action="MyAlbums.php" method="post">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Number of Pictures</th>
                        <th>Accessibility</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($albums as $album) : ?>
                    <tr>
                        <td><?php echo $album['Title']; ?></td>

                        <?php 
                        $pictureCount = getPictureNumber($album['Album_Id']);
                        ?>
                        <td><?php echo $pictureCount; ?></td>

                        <td>
                            <select name="accessibility[<?php echo $album['Album_Id']; ?>]">
                                <?php foreach ($accessibilityOptions as $option) : ?>
                                    <option value="<?php echo $option['Accessibility_Code']; ?>" <?php echo ($album['Accessibility_Code'] === $option['Accessibility_Code']) ? 'selected' : ''; ?>>
                                        <?php echo $option['Description']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <a href="MyAlbums.php?deleteAlbumId=<?php echo $album['Album_Id']; ?>" onclick="return confirm('Are you sure you want to delete this album?')">delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
            <br>
            <input type="submit" name="saveBtn" value="Save">
            
        </form>
    </div>
</body>

<?php include('./common/footer.php'); ?>

</html>

<?php

?>
