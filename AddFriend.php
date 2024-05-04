<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
include("./common/header.php");
include_once "Functions.php";
include_once "EntityClassLib.php";
include_once "Common/Functions.php";

$userId = $_SESSION["user_id"];
$userName = getStudentName($userId);

if (!(isset($_SESSION["login"]) && $_SESSION["login"] == "OK")) {
    header("Location: Login.php");
    exit();
}


// variable
$txtFid = "";
$fidErr = "";
$err = "";
$success = false;

extract($_POST);

if (isset($btnSubmit)){
    $fidErr = ValidateFid($fid, $_SESSION['user_id']);
    if ($fidErr == ""){
        $success = true;
    }
    if ($success){
        $link = connect();
        if ($link){
            $fid = mysqli_real_escape_string($link, $fid);
            $query = "SELECT name FROM user WHERE UserId = '$fid'";
            $result = query($link, $query);
            $fn = mysqli_fetch_assoc($result);
            $fName = $fn['name'];
            $query = "SELECT status FROM friendship WHERE friend_requesterid = '$fid' && friend_requesteeid = '$userId'";
            $result = query($link, $query);
            $row_nums = mysqli_num_rows($result);
            $status = mysqli_fetch_assoc($result);
            if ($row_nums > 0){
                if ($status['status'] == "request"){
                    $query = "UPDATE friendship SET status = 'accepted' WHERE friend_requesterid = '$fid' && friend_requesteeid = '$userId'";
                    query($link, $query);
                    $err = "You and $fName (ID: $fid) now are friends. From now, you are able to view $fName shared albums.";
                } else {
                    $err = "You and $fName (ID: $fid) have already been friends.";
                }
            }
            else {
                $query = "SELECT status FROM friendship WHERE friend_requesteeid = '$fid' && friend_requesterid = '$userId'";
                $result = query($link, $query);
                $row_nums = mysqli_num_rows($result);
                if ($row_nums > 0) {
                    $err = "You already sent request to $fName.";
                }
                else {
                    $query = "INSERT INTO friendship VALUES('$userId', '$fid', 'request')";
                    query($link, $query);
                    $err = "Your request has sent to $fName (ID: $fid). Once $fName accepts your request, you and $fName will be friends and be able to view each other's shared albums.";
                }  
            }
        }
        else {
            $err = "The system is not available, try again later.";
        }
        close($link);
    }
}

include 'Common/Header.php';
?>

<div class="container">
    <div class="row">
        <h1 class="text-center col-sm-6">Add Friend</h1>
    </div>
    <p>Welcome <b><?php echo $userName ?></b>! (not you?, change user <a href="Login.php">here</a>)</p>
    <p>Enter the ID of the user you want to be friend with</p>
    <p class="text-danger"><?php echo $err; ?></p>
    <form method="POST" action="AddFriend.php">
        <div class="form-group row">
            <label class="col-form-label col-sm-1" for="fid">ID:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="fid" name=fid value="<?php echo $txtFid ?>">
            </div>
            <button type="submit" name="btnSubmit" class="btn btn-primary">Send Friend Request</button>
            <span class="col-sm-6 error"><?php echo $fidErr ?></span>
        </div>
        
    </form>
</div>

<?php include 'Common/Footer.php'; ?>