<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
include("./common/header.php");
include_once "Functions.php";
include_once "Common/Functions.php";


$userId = $_SESSION["user_id"];
$userName = getStudentName($userId);

if (!(isset($_SESSION["login"]) && $_SESSION["login"] == "OK")) {
    header("Location: Login.php");
    exit();
}

$err = "";
extract($_POST);

if (isset($unfriendSubmit)){
    if (!empty($unfriends)){
        $link = connect();
        foreach ($unfriends as $uf){
            $uf = mysqli_real_escape_string($link, $uf);
            $query = "DELETE FROM friendship "
                    . "WHERE ((friend_requesterid = '$userId' "
                    . "AND friend_requesteeid = '$uf') "
                    . "OR (friend_requesteeid = '$userId' "
                    . "AND friend_requesterid = '$uf')) "
                    . "AND status = 'accepted'";
            query($link, $query);
        }
        close($link);
    }
    else {
        $err = "You did not select any friend to unfriend";
    }
}

if (isset($acceptSubmit)){
    if (!empty($friendRequests)){
        $link = connect();
        foreach ($friendRequests as $fr){
            $fr = mysqli_real_escape_string($link, $fr);
            $query = "UPDATE friendship "
                    . "SET status = 'accepted' "
                    . "WHERE (friend_requesteeid = '$userId' "
                    . "AND friend_requesterid = '$fr') AND status = 'request'";
            query($link, $query);
        }
        close($link);
    }
    else {
        $err = "You did not select any friend to accept the request";
    }
}

if (isset($denySubmit)){
    if (!empty($friendRequests)){
        $link = connect();
        foreach ($friendRequests as $fr){
            $fr = mysqli_real_escape_string($link, $fr);
            $query = "DELETE FROM friendship WHERE (friend_requesteeid = '$userId' AND friend_requesterid = '$fr') AND status = 'request'";
            query($link, $query);
        }
        close($link);
    }
    else {
        $err = "You did not select any friend to deny the request";
    }
}


include 'Common/Header.php';
?>

<div class="container">
    <div class="row">
        <h1 class="text-center">My Friends</h1>
    </div>
    <p>Welcome <b><?php echo $userName ?></b>! (not you?, change user <a href="Login.php">here</a>)</p>
    <p class="text-danger"><?php echo $err; ?></p>
    
    
    <a class="pull-right" href="AddFriend.php">Add Friends</a>
    <form method="POST" action="MyFriends.php">
        <h3>Friends:</h3>
        <table class="table">
            <thead>
                <th>Name</th>
                <th>Shared Albums</th>
                <th>Unfriend</th>
            </thead>
            <tbody>
                <?php 
                    $link = connect();
                    if ($link){
                        $query = "SELECT userid, name, COUNT(album_id) as sharedalbums FROM friendship LEFT JOIN user a ON (userid = friend_requesteeid OR userid = friend_requesterid) AND userid <> '$userId' LEFT JOIN album ON userid = owner_id AND accessibility_code = 'shared' WHERE (friend_requesteeid = '$userId' OR friend_requesterid = '$userId') AND status = 'accepted' GROUP BY userid, name";

                        $result = query($link, $query);
                        while ($r = mysqli_fetch_assoc($result)){
                            echo "<tr><td><a href='FriendPictures.php?id={$r['userid']}&name={$r['name']}'>{$r['name']}</a></td><td>{$r['sharedalbums']}</td><td><input type='checkbox' name='unfriends[]' value='{$r['userid']}' ></td></tr>";
                        }


                        if (mysqli_num_rows($result) == 0){
                            echo "<tr><td colspan='3' class='text-center text-danger'><b>No Friend Found!</b></td></tr>";
                        }
                    }
                    else {
                        echo "<tr><td colspan='3' class='text-center text-danger'><b>No Friend Found!</b></td></tr>";
                    }
                ?>
            </tbody>
        </table>
        <div class='row text-right'>
            <button type="submit" name="unfriendSubmit" class="btn btn-primary" onclick="return confirmUnfriend();">Unfriend Selected</button>
        </div>
        
        <br>
        <h3>Friend Requests:</h3>
        <table class='table'>
            <thead>
                <th>Name</th>
                <th>Accept or Deny</th>
            </thead>
            <tbody>
                <?php 
                    $query = "SELECT userid, name FROM friendship LEFT JOIN user ON userid = friend_requesterid WHERE status = 'request' AND friend_requesteeid = '$userId'";
                    $result = query($link, $query);
                    while ($r = mysqli_fetch_assoc($result)){
                        echo "<tr><td>{$r['name']}</td><td><input type='checkbox' name='friendRequests[]' value='{$r['userid']}' ></td></tr>";
                    }
                    if (mysqli_num_rows($result) == 0){
                        echo "<tr><td colspan='2' class='text-center text-danger'><b>No Friend Request Found!</b></td></tr>";
                    }
                    close($link);
                ?>
            </tbody>
        </table>
        <div class='row text-right'>
            <button type="submit" name="acceptSubmit" class="btn btn-primary">Accept Selected</button>
            <button type="submit" name="denySubmit" class="btn btn-primary" onclick="return confirmDeny();">Deny Selected</button>
        </div>
        
    </form>
</div>

<!-- JavaScript function to confirm unfriending -->
<script>
    function confirmUnfriend() {
        // Check if at least one checkbox is checked
        var checkboxes = document.querySelectorAll('input[name="unfriends[]"]:checked');
        if (checkboxes.length === 0) {
            alert("Please check at least one friend to unfriend.");
            return false; // Prevent form submission
        }
        return confirm('The selected friends will be unfriended!');
    }

    // JavaScript function to confirm denying friend requests
    function confirmDeny() {
        // Check if at least one checkbox is checked
        var checkboxes = document.querySelectorAll('input[name="friendRequests[]"]:checked');
        if (checkboxes.length === 0) {
            alert("Please check at least one friend to deny friend requests.");
            return false; // Prevent form submission
        }
        return confirm('The selected requests will be denied!');
    }
</script>


<?php include 'Common/Footer.php'; ?>