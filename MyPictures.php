<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
include("./common/header.php");
include_once "Functions.php";
include_once "EntityClassLib.php";

if (!(isset($_SESSION["login"]) && $_SESSION["login"] == "OK")) {
    header("Location: Login.php");
    exit();
}

$destination = "./images";
$destination_thumbnails ="./thumbnails";
$files = scandir($destination);
$thumbnails = scandir($destination_thumbnails);

$userId = $_SESSION["user_id"];
$albumId = $_GET["Album_Id"] ?? null;
$pictures = $albumId ? getPicturesByAlbumId($albumId) : [];

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>MyPictures</title>
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
        
        #thumbnailContainer img.thumbnail {
        width: 100px;
        height: auto;
        margin: 5px;
        cursor: pointer;
        }
        
        #mainImage {
            width: 75%;
            height: auto;
            display: block;
            margin-bottom: 20px;
        }
         .thumbnail {
            width: 100px;
            height: auto;
            cursor: pointer;
            margin: 5px;
        }
            
        .image-description-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .image-description {
            margin-left: 20px;
        }
        .comments-section {
            border-top: 1px solid #ccc;
            margin-top: 20px;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Pictures</h1>
         
        <!-- Album Selection -->
        <select id="albumSelection" name="albumSelection">
            <option value="">Select an Album</option>
            <?php
            foreach (getAlbum($userId) as $option) :
                $selected = ($option['Album_Id'] == $albumId) ? 'selected' : '';
            ?>
                <option value="<?php echo $option['Album_Id']; ?>" <?php echo $selected; ?>>
                    <?php echo $option['Title']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php if ($albumId && count($pictures) > 0): ?>
            <div class="image-description-container">
                <img id="mainImage" src="./images/<?php echo $pictures[0]['File_Name']; ?>" alt="Main Image" />
                <div id="imageDescription" class="image-description">
                    <h3 id="imageTitle"><?php echo $pictures[0]['Title']; ?></h3>
                    <p><?php echo $pictures[0]['Description']; ?></p>
                </div>
            </div>
            <div class="comments-section">
                <h3>Comments</h3>
                <form>
                    <textarea name="comment" placeholder="Add a comment..." rows="4" style="width: 100%;"></textarea>
                    <br>
                    <input type="submit" value="Add Comment">
                </form>
             </div>

            <!-- Thumbnails -->
            <div id="thumbnailContainer">
                <?php 
                foreach ($pictures as $picture) {
                    echo "<img class='thumbnail' data-title='" . htmlspecialchars($picture['Title'], ENT_QUOTES) . "' data-description='" . htmlspecialchars($picture['Description'], ENT_QUOTES) . "' src='./images/{$picture['File_Name']}' alt='Thumbnail' />";
                }
                ?>
            </div>
            
        <?php endif; ?>
    </div>


    <script>
        document.getElementById('albumSelection').addEventListener('change', function(event) {
            var albumId = event.target.value;
            window.location.href = '?Album_Id=' + albumId;
        });

        document.getElementById('thumbnailContainer').addEventListener('click', function(event) {
        if (event.target.classList.contains('thumbnail')) {
            var newSrc = event.target.src;
            var newDesc = event.target.getAttribute('data-description');
            var newTitle = event.target.getAttribute('data-title'); 

            document.getElementById('mainImage').src = newSrc;
            document.getElementById('imageDescription').innerHTML = '<h3>' + newTitle + '</h3><p>' + newDesc + '</p>'; // Update both title and description
        }
    });
    </script>

    <?php include('./common/footer.php'); ?>
</body>
</html>