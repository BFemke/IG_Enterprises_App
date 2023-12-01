<?php 
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/

session_start();

require_once('../utility/database.php');

$slip_id = filter_input(INPUT_POST, 'slip_id');
$t_hours = filter_input(INPUT_POST, 't_hours');

//checks if total hours is valid
if($t_hours <= 0){
 	$_SESSION['error'] = "Total Hours must be greater than 0.";
  	header("Location: view_dashboard.php");
  	exit();
}
$comments = filter_input(INPUT_POST, 'comments');

try{
    //gets current employee information from database to find changes
    $query = "SELECT *
        FROM timesheet 
        WHERE slip_id = :slip_id
        LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':slip_id', $slip_id);
    $statement->execute();
    $timesheet = $statement->fetch();
    $statement->closeCursor();

    //sets event log data
    $admin_id = $_SESSION['user']['employee_id'];
    $event_type = "Edit Timesheet";
    $description = "Timesheet with ID " . $slip_id . " edited: ";
    if($timesheet['total_hours'] != $t_hours){ $description .= "(Total Hours:".$timesheet['total_hours']."->".$t_hours.")"; }
    if($timesheet['comment'] !== $comments){ $description .= "(Comment:".$timesheet['comment']."->".$comments.")"; }

    //Makes timesheet changes in the database
    $query = "UPDATE timesheet 
    SET total_hours = :t_hours, comment = :comments 
    WHERE slip_id = :slip_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':slip_id', $slip_id);
    $statement->bindValue(':t_hours', $t_hours);
    $statement->bindValue(':comments', $comments);
    $statement->execute();
    $timesheets = $statement->fetchAll();
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

    $_SESSION['confirmation'] = "Timesheet updated successfully!";

} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

//emails the employee regarding chnages to thier timesheet
try{
    $query = 'SELECT email, work_date
            FROM employee e
            JOIN timesheet t ON t.employee_id = e.employee_id
            WHERE t.slip_id = :slip_id
            LIMIT 1';
    $statement = $db->prepare($query);
    $statement->bindValue(':slip_id', $slip_id);
    $statement->execute();
    $info = $statement->fetch();
    $statement->closeCursor();
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

//sets email variables
$to = "recipient@example.com";
$subject = "Timesheet Edited";
$message = "Your timesheet for ". $info['work_date'] ." has been edited with the following:\n\n".$description."\n\n-- Management";
$headers = "From: admin@igenterprises.ca";

// Send email
 mail($info['email'], $subject, $message, $headers);

header("Location: view_dashboard.php");
?>