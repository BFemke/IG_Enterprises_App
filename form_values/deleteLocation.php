<?php 
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/
session_start();

require_once('../utility/database.php');

$location_id = filter_input(INPUT_POST, 'location_id');

try{
    $query = "DELETE FROM location 
        WHERE location_id = :location_id";
    $statement = $db->prepare($query);
	$statement->bindValue(':location_id', $location_id);
    $statement->execute();
    $statement->closeCursor();

    header("Location: view_form_values.php");

} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

?>