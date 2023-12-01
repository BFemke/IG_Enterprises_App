<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/

session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();


// Redirect to the given page 
header("Location: ../index.php");
exit();

?>