<?php 
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once('../utility/database.php');

$email = $_POST['email'];
$pass = $_POST['pass'];

try{
	//checks if email and password combo exists in database
	$query = "SELECT * FROM employee WHERE email = :email AND password = :pass";
	$statement = $db->prepare($query);
	$statement->bindValue(':email', $email);
	$statement->bindValue(':pass', $pass);
	$statement->execute();
	
	$user = $statement->fetch();
	$statement->closeCursor();
} catch (PDOException $e) {
	// Handle database error
	$error_message = $e->getMessage();
	include('../utility/database_error.php');
	exit();
}

//checks if successful if not creates error message
if ($user) {
	$_SESSION['user'] = $user;
    if($_SESSION['user']['is_admin'] != 0){
        //if the user is an admin they are brought to the admin dashboard
        header("Location: http://igenterprises.ca/dashboard/view_dashboard.php");
      exit;
    }
    else{
        //if the user is a normal user they are brought to the page to view and submit thier timesheets
        header("Location: https://igenterprises.ca/user_timesheets/view_timesheets.php");
    }
} else {
    // Authentication failed, display an error message
    $_SESSION['login_error'] = 'Invalid email or password';
    header("Location: https://igenterprises.ca/index.php");  //brings user back to previous page
}

?>