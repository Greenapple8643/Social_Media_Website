<?php
// Start the session to access session variables
session_start();

// Destroy the current session
session_destroy();

// Redirect the user to the Home.php page
header("Location: Home.php");
exit();
?>