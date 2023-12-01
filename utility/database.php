<?php
 /*
 Author: Barbara Emke
 Date:   November 14, 2023
*/
    $dsn = 'mysql:host=db5014812224.hosting-data.io;dbname=dbs12307187';
    $username = 'dbu1548260';
    $password = 'h7nVdURDrPqa-aZ';

    try {
        $db = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include('database_error.php');
        exit();
    }
    
?>