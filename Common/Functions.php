<?php
include_once './EntityClassLib.php';

// DB connection
function connect(){
    // Read database connection parameters from an ini file
    $dbConnection = parse_ini_file('db_connection.ini');

    // Extract parameters from the parsed ini file
    extract($dbConnection);

    // Attempt to establish a MySQL database connection
    $link = mysqli_connect($host, $username, $password, $dbName);

    // Check if the connection was successful
    if (!$link){
        // If the connection fails, return NULL
        return NULL;
    }

    // If the connection is successful, return the connection link
    return $link;
}

// Function to close a database connection
function close($link){
    // Close the MySQL database connection
    mysqli_close($link);
}

// Function to execute a query on the database
function query($link, $query){
    // Execute the provided SQL query using the given database connection
    return mysqli_query($link, $query);
}




// function for user
function blankError($in){
    return $in . " can not be blank";
}

function ValidateName($name){
    $err = "";
    if (!isset($name) || trim($name) == "")
        $err = blankError("Name");
    return $err;
}

function ValidateSid($sid){
    if (!isset($sid) || trim($sid) == "") {
        return blankError("User ID");
    }
    if (strlen($sid) > 16){
        return "Your user ID is too long";
    }
    $link = connect();
    if (!$link) {
        return "The system is not available, try again later.";
    }
    $query = "SELECT * FROM user WHERE userid = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 's', $sid);
    if (mysqli_stmt_execute($stmt)){
        mysqli_stmt_store_result($stmt);
        $row_nums = mysqli_stmt_num_rows($stmt);
        close($link);
        if($row_nums > 0){
            return "A user with this ID has already signed up";
        }
        return "";
    } else {
        close($link);
        return "The system is not available, try again later.";
    }
    
}

function ValidateFid($fid, $sid){
    if (!isset($fid) || trim($fid) == "") {
        return blankError("ID");
    }
    if ($fid == $sid){
        return "You cannot send friend request to yourself";
    }
    $link = connect();
    if (!$link) {
        return "The system is not available, try again later.";
    }
    $query = "SELECT * FROM user WHERE userid = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 's', $fid);
    if (mysqli_stmt_execute($stmt))
    {
        mysqli_stmt_store_result($stmt);
        $row_nums = mysqli_stmt_num_rows($stmt);
        close($link);
        if($row_nums > 0){
            return "";
        }
        else {
            return "The user $fid does not exist";
        }
    } else {
        close($link);
        return "The system is not available, try again later.";
    }
    
}

function ValidateBlank($str, $type){
    if (!isset($str) || trim($str) == "") 
    {
        return blankError($type);
    }
    return "";
}

function ValidatePassword($pwd){
    if (!isset($pwd) || trim($pwd) == "")
    {      return "Password can not be blank";
    }
    elseif(strlen($pwd) < 6 
            || !preg_match('@[A-Z]@', $pwd) 
            || !preg_match('@[a-z]@', $pwd) 
            || !preg_match('@[0-9]@', $pwd))
    {
         return "Password must be at least 6 characters long, contains at least one upper case, one lowercase and one digit. ";
    }
}

function ValidatePhone($phone){
    if (!isset($phone) || trim($phone) == "" || !preg_match("/[2-9][0-9]{2}-[2-9][0-9]{2}-[0-9]{4}/", trim($phone)))
        return "Phone number is not in correct format";
    return "";
}
