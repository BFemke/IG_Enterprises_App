<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$employee_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

$_SESSION['id'] = $employee_id;

//Gets admin ID for event log
$admin_id = $_SESSION['user']['employee_id'];

require_once('../utility/database.php');

//gets form information
$first_name = filter_input(INPUT_POST, 'first_name');
if(strlen($first_name) > 15){
  	$_SESSION['error'] = "The first name cannot be longer than 15 characters.";
    header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
    exit();
}

$last_name = filter_input(INPUT_POST, 'last_name');
if(strlen($last_name) > 15){
  	$_SESSION['error'] = "The last name cannot be longer than 15 characters.";
    header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
    exit();
}

$company_id = filter_input(INPUT_POST, 'company');

$email = filter_input(INPUT_POST, 'email');
if(strlen($email) > 30){
  	$_SESSION['error'] = "The email cannot be longer than 30 characters.";
    header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
    exit();
}

$pass = filter_input(INPUT_POST, 'pass');


$is_admin = isset($_POST["is_admin"]) ? 1 : 0;

// Check if the password is at least 6 characters long
if (strlen($pass) < 6) {
    $_SESSION['error'] = "The password must be at least 6 characters long.";
    header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
    exit();
}
//Check if the password contains at least  one number
else if (!preg_match('/[0-9]/', $pass)) {
    $_SESSION['error'] = "The password must have at least 1 number.";
    header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
    exit();
}
//Check if the password contains at least one special character
else if(!preg_match('/[!@#$%^&*?]/', $pass)){
    $_SESSION['error'] = "The password must include at least 1 special charcter from (!@#$%^&*?).";
    header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
    exit();
}
//Check if the password exceeds 20 characters which is the maximum
else if(strlen($pass) > 20){
    $_SESSION['error'] = "The password cannot be longer than 20 characters.";
    header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
    exit();
}

//Ensures no employee accounts are made or changed to have the same email address
try{
    $query = "SELECT COUNT(*) AS email_count 
    FROM employee 
    WHERE email = :email";
    $statement = $db->prepare($query);
    $statement->bindValue(':email', $email);
    $statement->execute();
    $result = $statement->fetch();
    $statement->closeCursor();

    if ($result['email_count'] > 0 && $employee_id === null) {
        $_SESSION['error'] = "An employee already exists with this email.";
        header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
        exit();
    }

} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

//adds new employee to the database
if($employee_id === null){
    try{

        $query = "INSERT INTO employee (first_name, last_name, company_id, email, password, is_admin)
            VALUES (:first_name, :last_name, :company_id, :email, :pass, :is_admin)";
        $statement = $db->prepare($query);
        $statement->bindValue(':first_name', $first_name);
        $statement->bindValue(':last_name', $last_name);
        $statement->bindValue(':company_id', $company_id);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':pass', $pass);
        $statement->bindValue(':is_admin', $is_admin);
        $statement->execute();
        $lastInsertedId = $db->lastInsertId();
        $statement->closeCursor();

        //sets event log data
        $event_type = "Add Employee";
        $description = "New employee with ID " . $lastInsertedId . " added.";
    
    } catch (PDOException $e) {
        // Handle database error
        $error_message = $e->getMessage();
        include('../utility/database_error.php');
        exit();
    }
  
    //emails new employee login info
    $subject = "Account Created";
    $message = "Your account has been created, you may now submit your timesheets electronically on www.igenterprises.ca. Your login details are:\n\nEmail: ".$email."\nPassword: ".$pass."\n\n-- Management";
    $headers = "From: admin@igenterprises.ca";

    // Send email
    mail($email, $subject, $message, $headers);
}
else{
    
    try{
        //gets current employee information from database to find changes
        $query = "SELECT *
            FROM employee 
            WHERE employee_id = :employee_id
            LIMIT 1";
        $statement = $db->prepare($query);
        $statement->bindValue(':employee_id', $employee_id);
        $statement->execute();
        $employee = $statement->fetch();
        $statement->closeCursor();

        //sets event log data
        $event_type = "Edit Employee";
        $description = "Employee with ID " . $employee_id . " edited: ";

        if($employee['first_name'] !== $first_name){ $description .= "(First Name:".$employee['first_name']."->".$first_name.")"; }
        if($employee['last_name'] !== $last_name){ $description .= "(Last Name:".$employee['last_name']."->".$last_name.")"; }
        if($employee['company_id'] != $company_id){ $description .= "(Company ID:".$employee['company_id']."->".$company_id.")"; }
        if($employee['email'] !== $email){ $description .= "(Email:".$employee['email']."->".$email.")"; }
        if($employee['password'] !== $pass){ 
          	$description .= "(Password:".$employee['password']."->".$pass.")"; 
        	if($employee['password'] === null){
             	 $event_type = "Employee Reactivated";
            }
        }
        if($employee['is_admin'] !== $is_admin){ $description .= "(Is Admin:".($employee['is_admin'] === 0 ? "False" : "True")."->".($is_admin === 0 ? "False" : "True").")"; }

        //event data if employee was reactivated
        if($employee['password'] === "" && $pass !== "" ){
            $event_type = "Employee Reactivation";
            $description = "Employee with ID " . $employee_id . " was reactivated.";
        }

        //makes employee chnages to database
        $query = "UPDATE employee 
            SET first_name = :first_name, last_name = :last_name, company_id = :company_id, email = :email, password = :pass, is_admin = :is_admin  
            WHERE employee_id = :employee_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':employee_id', $employee_id);
        $statement->bindValue(':first_name', $first_name);
        $statement->bindValue(':last_name', $last_name);
        $statement->bindValue(':company_id', $company_id);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':pass', $pass);
        $statement->bindValue(':is_admin', $is_admin);
        $statement->execute();
        $statement->closeCursor();
    
    } catch (PDOException $e) {
        // Handle database error
        $error_message = $e->getMessage();
        include('../utility/database_error.php');
        exit();
    }
  
  	//employee notified of changes
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
  $message = $description .".\n\n-- Management";
  $headers = "From: admin@igenterprises.ca";

  // Send email
  mail($email['email'], $event_type, $message, $headers);
}

try{
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

unset($_SESSION['id']);

//employee notified of changes

header("Location: view_employees.php");

?>