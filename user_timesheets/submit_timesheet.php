<?php 
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../utility/database.php');

//gets required information from form for table and validates types
$userID = $_SESSION['user']['employee_id'];
$work_date = filter_input(INPUT_POST, 'date');

//validates user has not submitted a timesheet for the same date
try{
	//checks if email and password combo exists in database
	$query = "SELECT * FROM timesheet WHERE employee_id = :userID AND work_date = :work_date";
	$statement = $db->prepare($query);
	$statement->bindValue(':userID', $userID);
	$statement->bindValue(':work_date', $work_date);
	$statement->execute();
	
	$timesheet = $statement->fetch();
	$statement->closeCursor();
} catch (PDOException $e) {
	// Handle database error
	$error_message = $e->getMessage();
	include('../utility/database_error.php');
	exit();
}

if ($timesheet) {
    $_SESSION['error'] = "You have already submitted a timesheet for the entered date. If you need to make any changes to the given date's 
        timesheet please reach out to management for assistance. If the wrong date was entered by accident please enter the correct date.";
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
}

if (isset($_POST["shift"])) {
    $shiftStr= filter_input(INPUT_POST, 'shift');
    if($shiftStr === "day"){
        $shift = 1;
    } else {
        $shift = 0;
    }
} else {
    $_SESSION['error'] = "You must select either Dayshift or Nightshift.";
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
}

$dateAdded = date('Y-m-d');  //gets current date
$location = filter_input(INPUT_POST, 'location');

$tempUnit1 = filter_input(INPUT_POST, 'unit1');
$parts = explode('|', $tempUnit1);
$unit1 = $parts[0];

$comments = filter_input(INPUT_POST, 'comments');

$t_hours = filter_input(INPUT_POST, 't_hours');
if($t_hours <= 0){
    $_SESSION['error'] = "Your total hours must be greater than 0.";
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
}

//gets optional information from form for table and checks if set if not sets value as null
$r_hours = isset($_POST["r_hours"]) ? filter_input(INPUT_POST, 'r_hours') : null;
$s_hours = isset($_POST["s_hours"]) ? filter_input(INPUT_POST, 's_hours') : null;
$hmr = isset($_POST["hmr"]) ? filter_input(INPUT_POST, 'hmr') : null;
$work_type = isset($_POST["work_type"]) ? filter_input(INPUT_POST, 'work_type') : null;
$h_oil = isset($_POST["h_oil"]) ? filter_input(INPUT_POST, 'h_oil') : null;
$e_oil = isset($_POST["e_oil"]) ? filter_input(INPUT_POST, 'e_oil') : null;
$antifreeze = isset($_POST["antifreeze"]) ? filter_input(INPUT_POST, 'antifreeze') : null;
$def = isset($_POST["def"]) ? filter_input(INPUT_POST, 'def') : null;
$unit2 = isset($_POST["unit2"]) && $_POST["unit2"] !== "" ? filter_input(INPUT_POST, 'unit2') : null;
$stems = isset($_POST["stems"]) ? filter_input(INPUT_POST, 'stems') : null;
$meters = isset($_POST["meters"]) ? filter_input(INPUT_POST, 'meters') : null;
$loads = isset($_POST["loads"]) ? filter_input(INPUT_POST, 'loads') : null;
$decking = isset($_POST["decking"]) ? filter_input(INPUT_POST, 'decking') : null;
$hoe = isset($_POST["hoe"]) ? filter_input(INPUT_POST, 'hoe') : null;
$tops = isset($_POST["tops"]) ? filter_input(INPUT_POST, 'tops') : null;

if ($unit2 != null && $unit2 != "") {
    $parts = explode('|', $unit2);
    $unit2 = $parts[0];

    $op_hours1 = isset($_POST["op_hours1"]) ? filter_input(INPUT_POST, 'op_hours1') : null;
    $op_hours2 = isset($_POST["op_hours2"]) ? filter_input(INPUT_POST, 'op_hours2') : null;

    if ($op_hours1 == null || $op_hours2 == null) {
        $_SESSION['error'] = "All Operator hours must be filled in.";
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    }
}


//Inserts the data into timesheet table
try{
    //inserts new row into timesheet table
    $query = "INSERT INTO timesheet (employee_id, location_id, work_date, submission_date, total_hours, is_dayshift, comment";

    //completes addition values to insert
    if($r_hours !== null){ $query .= ", repair_hours";}
    if($s_hours !== null){ $query .= ", service_hours";}
    if($hmr !== null){ $query .= ", hour_meter_reading";}
    if($work_type !== null){ $query .= ", work_type";}
    if($h_oil !== null){ $query .= ", hyd_oil";}
    if($e_oil !== null){ $query .= ", engine_oil";}
    if($antifreeze !== null){ $query .= ", antifreeze";}
    if($def !== null){ $query .= ", def";}

    //Adds value binding values
    $query .= ") VALUES (:userID, :location, :work_date, :dateAdded, :t_hours, :shift, :comments";
    if($r_hours !== null){ $query .= ", :repair_hours";}
    if($s_hours !== null){ $query .= ", :service_hours";}
    if($hmr !== null){ $query .= ", :hour_meter_reading";}
    if($work_type !== null){ $query .= ", :work_type";}
    if($h_oil !== null){ $query .= ", :hyd_oil";}
    if($e_oil !== null){ $query .= ", :engine_oil";}
    if($antifreeze !== null){ $query .= ", :antifreeze";}
    if($def !== null){ $query .= ", :def";}
    $query .= ")";

    $statement = $db->prepare($query);
    $statement->bindValue(':userID', $userID);
    $statement->bindValue(':location', $location);
    $statement->bindValue(':work_date', $work_date);
    $statement->bindValue(':dateAdded', $dateAdded);
    $statement->bindValue(':t_hours', $t_hours);
    $statement->bindValue(':shift', $shift);
    $statement->bindValue(':comments', $comments);

    //prepare relevant optional bindings
    if($r_hours !== null){ $statement->bindValue(':repair_hours', $r_hours);}
    if($s_hours !== null){ $statement->bindValue(':service_hours', $s_hours);}
    if($hmr !== null){ $statement->bindValue(':hour_meter_reading', $hmr);}
    if($work_type !== null){ $statement->bindValue(':work_type', $work_type);}
    if($h_oil !== null){ $statement->bindValue(':hyd_oil', $h_oil);}
    if($e_oil !== null){ $statement->bindValue(':engine_oil', $e_oil);}
    if($antifreeze !== null){ $statement->bindValue(':antifreeze', $antifreeze);}
    if($def !== null){ $statement->bindValue(':def', $def);}

    $statement->execute();

    $slip_id = $db->lastInsertId();
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

//Inserts the data into unit_work table
try{
    //inserts new row into timesheet table
    $query = "INSERT INTO unit_work (slip_id, unit_id";

    //completes addition values to insert
    if($stems !== null){ $query .= ", stems";}
    if($meters !== null){ $query .= ", meters";}
    if($decking !== null){ $query .= ", decking";}
    if($hoe !== null){ $query .= ", hoe_chucking";}
    if($loads !== null){ $query .= ", loads";}
    if($tops !== null){ $query .= ", tops_piling";}
    if($unit2 !== null){ 
        $query .= ", unit_id2";
        $query .= ", operating_hours";
        $query .= ", operating_hours2";
    }

    //Adds value binding values
    $query .= ") VALUES (:slip_id, :unit_id";
    if($stems !== null){ $query .= ", :stems";}
    if($meters !== null){ $query .= ", :meters";}
    if($decking !== null){ $query .= ", :decking";}
    if($hoe !== null){ $query .= ", :hoe";}
    if($loads !== null){ $query .= ", :loads";}
    if($tops !== null){ $query .= ", :tops";}
    if($unit2 !== null){ 
        $query .= ", :unit2";
        $query .= ", :op_hours1";
        $query .= ", :op_hours2";
    }
    $query .= ")";

    //binds mandatory values
    $statement = $db->prepare($query);
    $statement->bindValue(':slip_id', $slip_id);
    $statement->bindValue(':unit_id', $unit1);

    //prepare relevant optional bindings
    if($stems !== null){ $statement->bindValue(':stems', $stems);}
    if($meters !== null){ $statement->bindValue(':meters', $meters);}
    if($decking !== null){ $statement->bindValue(':decking', $decking);}
    if($hoe !== null){ $statement->bindValue(':hoe', $hoe);}
    if($loads !== null){ $statement->bindValue(':loads', $loads);}
    if($tops !== null){ $statement->bindValue(':tops', $tops);}
    if($unit2 !== null){ 
        $statement->bindValue(':unit2', $unit2);
        $statement->bindValue(':op_hours1', $op_hours1);
        $statement->bindValue(':op_hours2', $op_hours2);
    }

    $statement->execute();
    $statement->closeCursor();
    header("Location: view_timesheets.php");
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

?>