<?php
session_start();

if (isset($_POST['inputValue'])) {
    $inputValue = $_POST['inputValue'];

    // Save the value to the session
    $_SESSION['date'] = $inputValue;

    header("Location: view_dashboard.php");
} 
?>
