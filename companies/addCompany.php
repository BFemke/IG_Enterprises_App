<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/

session_start();

require_once('../utility/database.php');

//validates input
$company_name = filter_input(INPUT_POST, 'company_name');
if (strlen($company_name) > 30) {
  	$_SESSION['error'] = "The company name cannot be longer than 30 characters.";
    header("Location: view_companies.php");
    exit();
}

try{
    $query = "INSERT INTO company (company_name)
        VALUES (:company_name)";
    $statement = $db->prepare($query);
	$statement->bindValue(':company_name', $company_name);
    $statement->execute();
    $lastInsertedId = $db->lastInsertId();
    $statement->closeCursor();

    //event log data
    $admin_id = $_SESSION['user']['employee_id'];
    $event_type = "Add Company";
    $description = "Company ".$company_name." with ID ".$lastInsertedId." added.";

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