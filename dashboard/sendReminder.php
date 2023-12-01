<?php
/*
Author: Barbara Emke
Date:   November 14, 2023
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once('../utility/database.php');

$employee_id = filter_input(INPUT_POST, 'employee_id');

//get the date in question
if(isset($_SESSION['date'])){
    $dateString = $_SESSION['date'];
  	$date = new DateTime($dateString);
    $date = $date->format("F j, Y");
}
else{
    $date = date("F j, Y");
}

try{
    $query = 'SELECT email
            FROM employee
            WHERE employee_id = :employee_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':employee_id', $employee_id);
    $statement->execute();
    $email = $statement->fetch();
    $statement->closeCursor();
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

//sets email variables
$to = "recipient@example.com";
$subject = "Timesheet Reminder";
$message = "This is your reminder to submit your timesheet for ". $date .".\n\n-- Management";
$headers = "From: admin@igenterprises.ca";

// Send email
$mailSuccess = mail($email['email'], $subject, $message, $headers);

//notifies user of outcome
if ($mailSuccess) {
    $_SESSION['confirmation'] = "Reminder sent successfuly.";
} else {
    $_SESSION['confirmation'] = "Reminder failed to send.";
}

header("Location: view_dashboard.php");

?>