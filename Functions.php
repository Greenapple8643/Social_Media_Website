<?php
include_once 'EntityClassLib.php';

function getPDO()
{
    $dbConnection = parse_ini_file("LAB5.ini");
    extract($dbConnection);
    return new PDO($dsn, $scriptUser, $scriptPassword);  
}

function getUserByIdAndPassword($userId, $password)
{
    $pdo = getPDO();
    
    // Use prepared statement to prevent SQL Injection
    $sql = "SELECT UserId, Name, Phone, Password FROM user WHERE UserId = :userId";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        // Verify the password against the hashed password in the database
        if (password_verify($password, $row['Password'])) {
            return new User($row['UserId'], $row['Name'], $row['Phone']);
        }
    }
    
    return null; // Either the user wasn't found, or the password didn't match
}



function addNewUser($userId, $name, $phone, $password)
{
   $pdo = getPDO();
     
    $sql = "INSERT INTO user VALUES(:userId, :name, :phone, :password)";

    try {
        $pdoStmt = $pdo->prepare($sql);
        
        $pdoStmt->bindParam(':userId', $userId, PDO::PARAM_STR);
        $pdoStmt->bindParam(':name', $name, PDO::PARAM_STR);
        $pdoStmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $pdoStmt->bindParam(':password', $password, PDO::PARAM_STR);
        
        $pdoStmt->execute();
        if (!$pdoStmt) {
            // Handle error - the insert failed
            $errorInfo = $pdo->errorInfo();
            echo "Database error: " . $errorInfo[2];
            return false; // Indicate failure
        }
        return true; // Indicate success
    } catch (PDOException $e) {
        echo "Exception: " . $e->getMessage();
        return false; // Indicate failure
    }
}

function getStudentName($userId) {
    $pdo = getPDO();
    $sql = "SELECT Name FROM user WHERE userId = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        return $result['Name'];
    } else {
        return null;
    }
}


function getAlbum($userId) {
    $pdo = getPDO();
    $sql = "SELECT Album_Id, Title, Description, Accessibility_Code "
            . "FROM Album WHERE Owner_id = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":userId", $userId, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result ? $result : [];
            
}

function insertAlbum($title, $description, $userId, $accessibilityCode) {
    $pdo = getPDO();
    $insertAlbumQuery = "INSERT INTO Album (Title, Description, Owner_Id, Accessibility_Code) 
                         VALUES (:title, :description, :ownerId, :accessibilityCode)";
    $stmt = $pdo->prepare($insertAlbumQuery);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':ownerId', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':accessibilityCode', $accessibilityCode, PDO::PARAM_STR);
    if ($stmt->execute()) {
            return true; // Indicate success
    } else {
        // Handle error - the insert failed
        $errorInfo = $stmt->errorInfo();
        echo "Database error: " . $errorInfo[2];
        return false; // Indicate failure
    }
}

function getAccessibility(){
    $pdo = getPDO();
    $sql = "SELECT * FROM accessibility";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result ? $result : [];
}

function getPictureNumber($album_id) {
    $pdo = getPDO();
    $sql = "SELECT COUNT(Picture_Id) AS PictureNumber FROM picture WHERE Album_id = :album_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":album_id", $album_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['PictureNumber'] : 0;
}

function deleteAlbum($album_id) {
    $pdo = getPDO();
    $sql = "DELETE FROM album WHERE Album_id= :album_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":album_id", $album_id, PDO::PARAM_STR);
    $stmt->execute();
}

function updateAlbum($accessibility, $album_id) {
    $pdo = getPDO();
    $sql = "UPDATE album SET Accessibility_Code = :accessibility WHERE Album_id = :album_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":accessibility", $accessibility, PDO::PARAM_STR);
    $stmt->bindParam(":album_id", $album_id, PDO::PARAM_STR);
    $stmt->execute();
}

function uploadPicture ($albumId, $fileName, $title, $description) {
    $pdo = getPDO();
    $sql = "INSERT INTO picture(Album_Id, File_name, Title, Description) "
            . "VALUES(:albumId, :fileName, :title, :description)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":albumId", $albumId, PDO::PARAM_STR);
    $stmt->bindParam(":fileName", $fileName, PDO::PARAM_STR);
    $stmt->bindParam(":title", $title, PDO::PARAM_STR);
    $stmt->bindParam(":description", $description, PDO::PARAM_STR);
    $stmt->execute();
}

function getPicturesByAlbumId($albumId) {
    $pdo = getPDO(); // Function to get PDO connection
    $sql = "SELECT * FROM picture WHERE Album_Id = :albumId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":albumId", $albumId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFriendAlbum($friendId) {
    $pdo = getPDO();
    $sql = "SELECT DISTINCT Album_Id, Title, Description, Accessibility_Code FROM Album "
         . "JOIN friendship ON (friendship.friend_requesteeid = Album.owner_id OR friendship.friend_requesterid = Album.owner_id) "
         . "WHERE ((friendship.friend_requesteeid = :friendId AND Album.Accessibility_Code = 'shared') "
         . "OR (friendship.friend_requesterid = :friendId AND Album.Accessibility_Code = 'shared')) "
         . "AND friendship.status = 'accepted'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam("friendId", $friendId, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result ? $result : [];
}

//function save_uploaded_file($destinationPath)
//{
//    if (!file_exists($destinationPath))
//    {
//        mkdir($destinationPath);
//    }
//
//    $tempFilePath = $_FILES['txtUpload']['tmp_name'];
//    $filePath = $destinationPath."/".$_FILES['txtUpload']['name'];
//
//    $pathInfo = pathinfo($filePath);
//    $dir = $pathInfo['dirname'];
//    $fileName = $pathInfo['filename'];
//    $ext = $pathInfo['extension'];
//
//    //make sure not to overwrite existing files 
//    $i="";
//    while (file_exists($filePath))
//    {	
//            $i++;
//            $filePath = $dir."/".$fileName."_".$i.".".$ext;
//    }
//    move_uploaded_file($tempFilePath, $filePath);
//
//    return $filePath;
//}

function save_uploaded_file($destinationPath, $fileIndex)
{
    if (!file_exists($destinationPath))
    {
        mkdir($destinationPath);
    }

    $tempFilePath = $_FILES['txtUpload']['tmp_name'][$fileIndex];
    $originalFileName = $_FILES['txtUpload']['name'][$fileIndex];
    $filePath = $destinationPath . "/" . $originalFileName;

    $pathInfo = pathinfo($filePath);
    $dir = $pathInfo['dirname'];
    $fileName = $pathInfo['filename'];
    $ext = $pathInfo['extension'];

    // Make sure not to overwrite existing files 
    $i = "";
    while (file_exists($filePath))
    {	
        $i++;
        $filePath = $dir . "/" . $fileName . "_" . $i . "." . $ext;
    }
    move_uploaded_file($tempFilePath, $filePath);

    return $filePath;
}


function resamplePicture($filePath, $destinationPath, $maxWidth, $maxHeight)
{
    if (!file_exists($destinationPath))
    {
            mkdir($destinationPath);
    }

    $imageDetails = getimagesize($filePath);

    $originalResource = null;
    if ($imageDetails[2] == IMAGETYPE_JPEG) 
    {
            $originalResource = imagecreatefromjpeg($filePath);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_PNG) 
    {
            $originalResource = imagecreatefrompng($filePath);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_GIF) 
    {
            $originalResource = imagecreatefromgif($filePath);
    }
    $widthRatio = $imageDetails[0] / $maxWidth;
    $heightRatio = $imageDetails[1] / $maxHeight;
    $ratio = max($widthRatio, $heightRatio);

    $newWidth = $imageDetails[0] / $ratio;
    $newHeight = $imageDetails[1] / $ratio;

    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    $success = imagecopyresampled($newImage, $originalResource, 0, 0, 0, 0, 
                                    $newWidth, $newHeight, $imageDetails[0], $imageDetails[1]);

    if (!$success)
    {
            imagedestroy($newImage);
            imagedestroy($originalResource);
            return "";
    }
    $pathInfo = pathinfo($filePath);
    $newFilePath = $destinationPath."/".$pathInfo['filename'];
    if ($imageDetails[2] == IMAGETYPE_JPEG) 
    {
            $newFilePath .= ".jpg";
            $success = imagejpeg($newImage, $newFilePath, 100);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_PNG) 
    {
            $newFilePath .= ".png";
            $success = imagepng($newImage, $newFilePath, 0);
    } 
    elseif ($imageDetails[2] == IMAGETYPE_GIF) 
    {
            $newFilePath .= ".gif";
            $success = imagegif($newImage, $newFilePath);
    }

    imagedestroy($newImage);
    imagedestroy($originalResource);

    if (!$success)
    {
            return "";
    }
    else
    {
            return $newFilePath;
    }
}



