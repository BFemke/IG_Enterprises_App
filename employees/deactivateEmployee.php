<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/

session_start();
$employee_id = isset($_POST['employee_id']) ? filter_input(INPUT_POST, 'employee_id') : null;

//data for event log
$admin_id = $_SESSION['user']['employee_id'];
$event_type = "Employee Deactivated";
$description = "Employee with ID " . $employee_id . " was deactivated.";

require_once('../utility/database.php');

try{
    //deactivates employee
    $query = "UPDATE employee 
        SET password = null
        WHERE employee_id = :employee_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':employee_id', $employee_id);
    $statement->execute();
    $statement->closeCursor();

    //creates log of event in event_log table
    $query = "INSERT INTO event_log (admin_id, event_type, description)
                VALUES (:admin_id, :event_type, :description)";
    $statement = $db->prepare($query);
    $statement->bindValue(':admin_id', $admin_id);
    $statement->bindValue(':event_type', $event_type);
    $statement->bindValue(':description', $description);
    $statement->execute();
    $statement->closeCursor();
    
    } catch (PDOException $e) {
        // Handle database error
        $error_message = $e->getMessage();
        include('../utility/database_error.php');
        exit();
    
}

//informs employee of account deactivation
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
$subject = "Account Deactivated";
$message = "Your account has been deactivated.\n\n-- Management";
$headers = "From: admin@igenterprises.ca";

// Send email
$mailSuccess = mail($email['email'], $subject, $message, $headers);

header("Location: view_employees.php");

?>