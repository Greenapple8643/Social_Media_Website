<?php
session_start();
//define constants for convenience
define("ORIGINAL_IMAGE_DESTINATION", "./originals"); 

define("IMAGE_DESTINATION", "./images"); 
define("IMAGE_MAX_WIDTH", 800);
define("IMAGE_MAX_HEIGHT", 600);

define("THUMB_DESTINATION", "./thumbnails");  
define("THUMB_MAX_WIDTH", 100);
define("THUMB_MAX_HEIGHT", 100);

//Use an array to hold supported image types for convenience
$supportedImageTypes = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);

include_once "Functions.php";
include_once "EntityClassLib.php";
$userId = $_SESSION["user_id"];
if (!(isset($_SESSION["login"]) && $_SESSION["login"] == "OK")) {
    header("Location: Login.php");
    exit();
}

$error = "";
$title = "";
$description = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btnUpload'])) 
{
    if (isset($_FILES['txtUpload'])) {
        $selectedAlbumId = $_POST['uploadToAlbum'];
        foreach ($_FILES['txtUpload']['name'] as $key => $name) {
        
	if ($_FILES['txtUpload']['error'][$key] == 0)
	{ 	
		$filePath = save_uploaded_file(ORIGINAL_IMAGE_DESTINATION, $key);
                $originalFileName = $_FILES['txtUpload']['name'][$key];

		$imageDetails = getimagesize($filePath);
		
		if ($imageDetails && in_array($imageDetails[2], $supportedImageTypes))
		{
			resamplePicture($filePath, IMAGE_DESTINATION, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT);
			
			resamplePicture($filePath, THUMB_DESTINATION, THUMB_MAX_WIDTH, THUMB_MAX_HEIGHT);
                        
                        uploadPicture($selectedAlbumId, $originalFileName, $_POST["title"], $_POST["description"]);  
                        
		}
		else
		{
			$error = "Uploaded file is not a supported type"; 
			unlink($filePath);
		}
	}
	elseif ($_FILES['txtUpload']['error'] == 1)
	{
		$error = "Upload file is too large"; 
	}
	elseif ($_FILES['txtUpload']['error'] == 4)
	{
		$error = "No upload file specified"; 
	}
	else
	{
		$error  = "Error happened while uploading the file. Try again late"; 
	}
        }
    }
}
include "./Common/Header.php"
?>
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
    <h1>Upload Pictures</h1>
    <p>Accepted picture types: JPEG, GIF, and PNG</p>
    <span class="text-danger"><?php echo $error;?></span>
  
    <form action="UploadPictures.php" method="post"  enctype="multipart/form-data">    
     <div class="row form-group">
         <div>
             <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
         </div>
         <div>
             <label for="uploadToAlbum">Upload to Album:</label>
            <select id="uploadToAlbum" name="uploadToAlbum" required>
                <?php

                foreach (getAlbum($userId) as $option) :
                ?>
                    <option value="<?php echo $option['Album_Id']; ?>">
                        <?php echo $option['Title']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
         </div>
       <div class="col-md-4"> 
       <input type="file" name="txtUpload[]" id="txtUpload" class="form-control" multiple/>
       </div> 
     </div>
        <div>
            <label for="description">Description:</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        <div class="row form-group">
             <div class="col-md-1"> 
                <input type="submit" name="btnUpload" value="Upload" class="btn btn-primary"/>
             </div>
             <div class="col-md-1">
                <input type="reset" name="btnReset" value="Reset" class="btn btn-secondary"/>
             </div>
        </div>
     </div>
   </form> 
</div>
</body>
<?php
include "./Common/Footer.php"
?>   