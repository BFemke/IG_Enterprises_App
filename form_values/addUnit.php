<?php 

/*  
    Author: Barbara Emke
    Date:   November 14, 2023
*/
session_start();

require_once('../utility/database.php');

$unit_id = filter_input(INPUT_POST, 'unit_id');
//validates input
if(strlen($unit_id) > 10){
 	$_SESSION['unit_error'] = "The unit ID cannot be greater than 10 characters.";
    header("Location: view_form_values.php");
    exit();
}
$unit_type = filter_input(INPUT_POST, 'unit_type');
if(strlen($unit_type) > 15){
 	$_SESSION['unit_error'] = "The unit type cannot be greater than 15 characters.";
    header("Location: view_form_values.php");
    exit();
}

try{
    $query = "INSERT INTO unit 
        VALUES (:unit_id, :unit_type)";
    $statement = $db->prepare($query);
	$statement->bindValue(':unit_id', $unit_id);
    $statement->bindValue(':unit_type', $unit_type);
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