<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/
session_start();

$userPass = $_SESSION['user']['password'];
$userID = $_SESSION['user']['employee_id'];

require_once('../utility/database.php');

$old_pass = filter_input(INPUT_POST, 'old_pass');
$new_pass = filter_input(INPUT_POST, 'new_pass');
$new_pass2 = filter_input(INPUT_POST, 'new_pass2');

//if old password matches the password on file and both new passwords are the same, the password is updated
if($old_pass === $userPass && $new_pass === $new_pass2){

    // Check if the password is at least 6 characters long
    if (strlen($new_pass) < 6) {
        $_SESSION['change_error'] = "The password must be at least 6 characters long.";
        header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
        exit();
    }
    //Check if the password contains at least  one number
    else if (!preg_match('/[0-9]/', $new_pass)) {
        $_SESSION['change_error'] = "The password must have at least 1 number.";
        header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
        exit();
    }
    //Check if the password contains at least one special character
    else if(!preg_match('/[!@#$%^&*?]/', $new_pass)){
        $_SESSION['change_error'] = "The password must include at least 1 special character from (!@#$%^&*?).";
        header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
        exit();
    }
    //Check if the password exceeds 20 characters which is the maximum
    else if(strlen($new_pass) > 20){
        $_SESSION['change_error'] = "The password cannot be longer than 20 characters.";
        header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
        exit();
    }

    //updates the password in the database
    try{
        $query = "UPDATE employee 
            SET password = :new_pass
            WHERE employee_id = :employee_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':employee_id', $userID);
        $statement->bindValue(':new_pass', $new_pass);
        $statement->execute();
        $statement->closeCursor();

        $_SESSION['confirmation'] = "Password has been changed successfully!";
        $_SESSION['user']['password'] = $new_pass;

        //Goes back to previous page
        header("Location: {$_SERVER['HTTP_REFERER']}");
        
        } catch (PDOException $e) {
            // Handle database error
            $error_message = $e->getMessage();
            include('../utility/database_error.php');
            exit();
        
    }
}
else if($old_pass !== $userPass){
    // Change password failed old password does not match
    $_SESSION['change_error'] = 'Incorrect Password.';
    header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
}
else if($new_pass !== $new_pass2){
    // Change password failed new passwords do not match
    $_SESSION['change_error'] = 'Both new passwords must match.';
    header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
}
else{
    // Change password failed new passwords do not match
    $_SESSION['change_error'] = 'Error please try again..';
    header("Location: ".$_SERVER['HTTP_REFERER']);  //brings user back to previous page
}


?>