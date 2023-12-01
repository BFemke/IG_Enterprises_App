<?php
/*
Author: Barbara Emke
Date:   November 14, 2023
*/

session_start();

$employee_id = filter_input(INPUT_POST, 'employee_id');

$_SESSION['recipient'] = $employee_id;

header("Location: view_employees.php");

?>