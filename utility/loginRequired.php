<?php
/*
Author: Barbara Emke
Date:   November 14, 2023
*/

if(!isset($_SESSION['user'])){
    header("Location: https://igenterprises.ca");
    exit();
}

?>