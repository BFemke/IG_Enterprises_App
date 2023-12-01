<?php 
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/
session_start();

require_once('../utility/database.php');

$company_id = filter_input(INPUT_POST, 'company_id');
$status = filter_input(INPUT_POST, 'is_active');

$newStatus = $status ? 0 : 1;

try{
    $query = "UPDATE company 
        SET is_active = :newStatus 
        WHERE company_id = :company_id";
    $statement = $db->prepare($query);
	$statement->bindValue(':company_id', $company_id);
    $statement->bindValue(':newStatus', $newStatus);
    $statement->execute();
    $statement->closeCursor();

    //event log data
    $admin_id = $_SESSION['user']['employee_id'];
    $event_type = "Company ".($status ? "Deactivated" : "Reactivated");
    $description = "Company with ID ".$company_id." has been ".($status ? "deactivated." : "reactivated.");

    //creates log of event in event_log table
    $query = "INSERT INTO event_log (admin_id, event_type, description)        
        VALUES (:admin_id, :event_type, :description)";
    $statement = $db->prepare($query);
    $statement->bindValue(':admin_id', $admin_id);
    $statement->bindValue(':event_type', $event_type);
    $statement->bindValue(':description', $description);
    $statement->execute();
    $statement->closeCursor();

    header("Location: view_companies.php");

} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

?>