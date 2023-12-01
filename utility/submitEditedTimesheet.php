<?php 
/* 
    Author: Barbara Emke
    Date:   November 14, 2023
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once('../utility/database.php');

$path = $_SESSION['path'];

//gets required information from form for table and validates types
$slip_id = $_SESSION['slip_id'];
$userID = $_SESSION['id'];
$work_date = filter_input(INPUT_POST, 'date');

//validates user has not submitted a timesheet for the same date
try{
	//checks if email and password combo exists in database
	$query = "SELECT * FROM timesheet WHERE employee_id = :userID AND work_date = :work_date AND slip_id != :slip_id";
	$statement = $db->prepare($query);
	$statement->bindValue(':userID', $userID);
	$statement->bindValue(':work_date', $work_date);
  	$statement->bindValue(':slip_id', $slip_id);
	$statement->execute();
	
	$timesheet = $statement->fetch();
	$statement->closeCursor();

    $query = "SELECT t.*, u.*
        FROM timesheet t 
        JOIN unit_work u ON u.slip_id = t.slip_id
        WHERE t.slip_id = :slip_id
        LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':slip_id', $slip_id);
    $statement->execute();
    $record = $statement->fetch();
    $statement->closeCursor();

} catch (PDOException $e) {
	// Handle database error
	$error_message = $e->getMessage();
	include('../utility/database_error.php');
	exit();
}

if ($timesheet) {
    $_SESSION['error'] = "This employee has already submitted a timesheet for the entered date.";
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

$location = filter_input(INPUT_POST, 'location');

$tempUnit1 = filter_input(INPUT_POST, 'unit1');
$parts = explode('|', $tempUnit1);
$unit1 = $parts[0];

$comments = filter_input(INPUT_POST, 'comments');

$t_hours = filter_input(INPUT_POST, 't_hours');
if($t_hours <= 0){
    $_SESSION['error'] = "The total hours must be greater than 0.";
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
if(isset($_POST["unit2"]) && $_POST["unit2"] !== ""){
 	$tempUnit2 = filter_input(INPUT_POST, 'unit2');
    $parts = explode('|', $tempUnit2);
    $unit2 = $parts[0]; 
}
else{ $unit2 = null;}
$stems = isset($_POST["stems"]) ? filter_input(INPUT_POST, 'stems') : null;
$meters = isset($_POST["meters"]) ? filter_input(INPUT_POST, 'meters') : null;
$loads = isset($_POST["loads"]) ? filter_input(INPUT_POST, 'loads') : null;
$decking = isset($_POST["decking"]) ? filter_input(INPUT_POST, 'decking') : null;
$hoe = isset($_POST["hoe"]) ? filter_input(INPUT_POST, 'hoe') : null;
$tops = isset($_POST["tops"]) ? filter_input(INPUT_POST, 'tops') : null;

$op_hours1 = isset($_POST["op_hours1"]) ? filter_input(INPUT_POST, 'op_hours1') : null;
$op_hours2 = isset($_POST["op_hours2"]) ? filter_input(INPUT_POST, 'op_hours2') : null;


//sets event log data
$admin_id = $_SESSION['user']['employee_id'];
$event_type = "Edit Timesheet";
$description = "Timesheet with ID " . $slip_id . " edited: ";


//Inserts the data into timesheet table
try{
    //updates record of timesheet table
    $query = "UPDATE timesheet SET ";

    //completes addition values to update
    $first = true;
    if($location !== $record['location_id']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "location_id = :location_id";
        $description .= "(Location:".$record['location_id']."->".$location.")";
    }
  	if($comments !== $record['comment']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "comment = :comment";
        $description .= "(Comment:".$record['comment']."->".$comments.")";
    }
    if($t_hours != $record['total_hours']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "total_hours = :total_hours";
        $description .= "(Total Hours:".$record['total_hours']."->".$t_hours.")";
    }
    if($work_date != $record['work_date']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "work_date = :work_date";
        $description .= "(Work Date:".$record['work_date']."->".$work_date.")";
    }
    if($r_hours != $record['repair_hours']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "repair_hours = :repair_hours";
        $description .= "(Repair Hours:".$record['repair_hours']."->".$r_hours.")";
    }
    if($s_hours != $record['service_hours']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "service_hours = :service_hours";
        $description .= "(Service Hours:".$record['service_hours']."->".$s_hours.")";
    }
    if($hmr !== $record['hour_meter_reading']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "hour_meter_reading = :hour_meter_reading";
        $description .= "(Hour Meter Reading:".$record['hour_meter_reading']."->".$hmr.")";
    }
  	if($shift !== $record['is_dayshift']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "is_dayshift = :is_dayshift";
        $description .= "(Is Dayshift:".$record['is_dayshift']."->".$shift.")";
    }
    if($work_type !== $record['work_type']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "work_type = :work_type";
        $description .= "(Work Type:".$record['work_type']."->".$work_type.")";
    }
    if($h_oil != $record['hyd_oil']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "hyd_oil = :hyd_oil";
        $description .= "(Hyd Oil:".$record['hyd_oil']."->".$h_oil.")";
    }
    if($e_oil != $record['engine_oil']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "engine_oil = :engine_oil";
        $description .= "(Engine Oil:".$record['engine_oil']."->".$e_oil.")";
    }
    if($antifreeze != $record['antifreeze']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "antifreeze = :antifreeze";
        $description .= "(Antifreeze:".$record['antifreeze']."->".$antifreeze.")";
    }
    if($def != $record['def']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "def = :def";
        $description .= "(DEF:".$record['def']."->".$def.")";
    }
  
  	//checks if any parameters were added
  	if($query !== "UPDATE timesheet SET "){

      //finish query
      $query .= " WHERE slip_id = :slip_id;";

      $statement = $db->prepare($query);

      //prepare relevant bindings
      if($location !== $record['location_id']){ 
          $statement->bindValue(':location_id', $location);
      }
      if($shift !== $record['is_dayshift']){ 
          $statement->bindValue(':is_dayshift', $shift);
      }
      if($comments !== $record['comment']){ 
          $statement->bindValue(':comment', $comments);
      }
      if($t_hours != $record['total_hours']){ 
          $statement->bindValue(':total_hours', $t_hours);
      }
      if($work_date != $record['work_date']){ 
          $statement->bindValue(':work_date', $work_date);
      }
      if($r_hours != $record['repair_hours']){ 
          $statement->bindValue(':repair_hours', $r_hours);
      }
      if($s_hours != $record['service_hours']){ 
          $statement->bindValue(':service_hours', $s_hours);
      }
      if($hmr !== $record['hour_meter_reading']){ 
          $statement->bindValue(':hour_meter_reading', $hmr);
      }
      if($work_type !== $record['work_type']){ 
          $statement->bindValue(':work_type', $work_type);
      }
      if($h_oil != $record['hyd_oil']){ 
          $statement->bindValue(':hyd_oil', $h_oil);
      }
      if($e_oil != $record['engine_oil']){ 
          $statement->bindValue(':engine_oil', $e_oil);
      }
      if($antifreeze != $record['antifreeze']){ 
          $statement->bindValue(':antifreeze', $antifreeze);
      }
      if($def != $record['def']){ 
          $statement->bindValue(':def', $def);
      }
      $statement->bindValue(':slip_id', $slip_id);

      $statement->execute();

      $slip_id = $db->lastInsertId();
    }
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

//Inserts the data into unit_work table
try{
    //inserts new row into timesheet table
    $query = "UPDATE unit_work SET ";
  	$first = true;

    //completes addition values to insert
    if($unit1 !== $record['unit_id']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "unit_id = :unit_id";
        $description .= "(Unit ID:".$record['unit_id']."->".$unit1.")";
    }
    if($stems != $record['stems']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "stems = :stems";
        $description .= "(Stems:".$record['stems']."->".$stems.")";
    }
    if($meters != $record['meters']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "meters = :meters";
        $description .= "(Meters:".$record['meters']."->".$meters.")";
    }
    if($decking != $record['decking']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "decking = :decking";
        $description .= "(Decking:".$record['decking']."->".$decking.")";
    }
    if($hoe != $record['hoe_chucking']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "hoe_chucking = :hoe_chucking";
        $description .= "(Hoe Chucking:".$record['hoe_chucking']."->".$hoe.")";
    }
    if($loads != $record['loads']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "loads = :loads";
        $description .= "(Loads:".$record['loads']."->".$loads.")";
    }
    if($tops != $record['tops_piling']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "tops_piling = :tops_piling";
        $description .= "(Tops Piling:".$record['tops_piling']."->".$tops.")";
    }
    if($unit2 != $record['unit_id2']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "unit_id2 = :unit_id2";
        $description .= "(Unit ID 2:".$record['unit_id2']."->".$unit2.")";
    }
    if($op_hours1 != $record['operating_hours']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "operating_hours = :operating_hours";
        $description .= "(Operating Hours:".$record['operating_hours']."->".$op_hours1.")";
    }
    if($op_hours2 != $record['operating_hours2']){ 
        if($first === false){
            $query .= ", ";
        }
        else{ $first = false; }
        $query .= "operating_hours2 = :operating_hours2";
        $description .= "(Operating Hours 2:".$record['operating_hours2']."->".$op_hours2.")";
    }
  
  	if($query !== "UPDATE unit_work SET "){

      //finish query
      $query .= " WHERE slip_id = :slip_id;";
      $statement = $db->prepare($query);

      //binds relevant values
      if($unit1 !== $record['unit_id']){ 
          $statement->bindValue(':unit_id', $unit1);
      }
      if($stems != $record['stems']){ 
          $statement->bindValue(':stems', $stems);
      }
      if($meters != $record['meters']){ 
          $statement->bindValue(':meters', $meters);
      }
      if($decking != $record['decking']){ 
          $statement->bindValue(':decking', $decking);
      }
      if($hoe != $record['hoe_chucking']){ 
          $statement->bindValue(':hoe_chucking', $hoe);
      }
      if($loads != $record['loads']){ 
          $statement->bindValue(':loads', $loads);
      }
      if($tops != $record['tops_piling']){ 
          $statement->bindValue(':tops_piling', $tops);
      }
      if($unit2 != $record['unit_id2']){ 
          $statement->bindValue(':unit_id2', $unit2);
      }
      if($op_hours1 != $record['operating_hours']){ 
          $statement->bindValue(':operating_hours', $op_hours1);
      }
      if($op_hours2 != $record['operating_hours2']){ 
          $statement->bindValue(':operating_hours2', $op_hours2);
      }
      $statement->bindValue(':slip_id', $slip_id);

      $statement->execute();
      $statement->closeCursor();
    }
  	if($description !== "Timesheet with ID " . $slip_id . " edited: "){
     	//creates log of event in event_log table
        $query = "INSERT INTO event_log (admin_id, event_type, description)
                    VALUES (:admin_id, :event_type, :description)";
        $statement = $db->prepare($query);
        $statement->bindValue(':admin_id', $admin_id);
        $statement->bindValue(':event_type', $event_type);
        $statement->bindValue(':description', $description);
        $statement->execute();
        $statement->closeCursor();

        $_SESSION['confirmation'] = "Timesheet updated successfully!";
    }
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

$slip_id = $_SESSION['slip_id'];

//emails the employee regarding chnages to thier timesheet
try{
    $query = 'SELECT email, work_date
            FROM employee e
            JOIN timesheet t ON t.employee_id = e.employee_id
            WHERE t.slip_id = :slip_id
            LIMIT 1';
    $statement = $db->prepare($query);
    $statement->bindValue(':slip_id', $slip_id);
    $statement->execute();
    $info = $statement->fetch();
    $statement->closeCursor();
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

//sets email variables
$subject = "Timesheet Edited";
$message = "Your timesheet for ". $work_date ." has been edited with the following:\n\n".$description."\n\n-- Management";
$headers = "From: admin@igenterprises.ca";

// Send email
 mail($info['email'], $subject, $message, $headers);

header("Location: {$path}");
?>