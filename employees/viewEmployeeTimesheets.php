<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/

session_start();

include '../utility/loginRequired.php';

require_once('../utility/database.php');

$employee_id = filter_input(INPUT_POST, 'employee_id');
$_SESSION['path'] = "../employees/viewEmployeeTimesheets.php"; 

if(isset($_SESSION['id'])){
 	$employee_id = $_SESSION['id'];
  	unset($_SESSION['id']);
}

try{
    $query = 'SELECT t.*, e.last_name, e.first_name, u.unit_id, u.unit_id2
            FROM timesheet t
            JOIN employee e ON t.employee_id = e.employee_id
            JOIN unit_work u ON t.slip_id = u.slip_id
            WHERE t.employee_id = :employee_id
			ORDER BY work_date DESC';
    $statement = $db->prepare($query);
    $statement->bindValue(':employee_id', $employee_id);
    $statement->execute();
    $records = $statement->fetchAll();
    $statement->closeCursor();
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

try{
    $query = 'SELECT work_date
            FROM timesheet 
			WHERE employee_id = :employee_id
			ORDER BY work_date ASC
            LIMIT 1';
    $statement = $db->prepare($query);
	$statement->bindValue(':employee_id', $employee_id);
    $statement->execute();
    $oldestDate = $statement->fetch();
    $statement->closeCursor();
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

try{
    $query = 'SELECT first_name, last_name
            FROM employee 
			WHERE employee_id = :employee_id
            LIMIT 1';
    $statement = $db->prepare($query);
	$statement->bindValue(':employee_id', $employee_id);
    $statement->execute();
    $employee = $statement->fetch();
    $statement->closeCursor();
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}
?>

<?php include '../utility/header.php'; ?>

    <link href="../utility/styles.css" rel="stylesheet" />

    <script async src="manageEmployeeFunctions.js"></script>

    <div class="content_box not-mobile">
        <div class="spaced-flex">
            <div>
                <h2 class="heading"><?php echo $employee['first_name'] . " " . $employee['last_name']; ?></h2>
                <p class="hyperlink no-print" onClick="goBack()">Back</p>
            </div>

            <div class="flex_field no print">
                <div class="flex_field" style="margin-right: 10px;">
                    <label for="from_date"><p>From:&nbsp;&nbsp;</p></label>
                    <input type="date"  name="from_date" id="from_date" onChange="filterTimesheets()" value="<?php echo $oldestDate['work_date']; ?>"/>
                </div>

                <div class="flex_field">
                    <label for="to_date"><p>To:&nbsp;&nbsp;</p></label>
                    <input type="date"  name="to_date" id="to_date" onChange="filterTimesheets()" value="<?php echo date("Y-m-d"); ?>"/>
                </div>
            </div>
            
        </div>

        <div class="content" style="display: block">
            <table class="with-borders">
                <thead>
                    <?php if($records) :?>
                        <tr>
                            <th>Date</th>
                            <th>Slip ID</th>
                            <th>Location</th>
                            <th>Unit #</th>
                            <th>Total Hours</th>
                            <th>Service Hours</th>
                            <th>Repair Hours</th>
                            <th>Comments</th>
                            <th class="no-print">&nbsp;</th>
                        </tr>
                    <?php endif; ?>
                </thead>
                <tbody>
                    <?php foreach($records as $record) : ?>
                        <tr class="employee_timesheet">
                            <td><p class="table-text date"><?php echo $record['work_date']; ?></p></td>
                            <td><p class="table-text"><?php echo $record['slip_id']; ?></p></td>
                            <td><p class="table-text"><?php echo $record['location_id']; ?></p></td>
                            <td><p class="table-text"><?php echo isset($record['unit_id2']) ? "{$record['unit_id']}, {$record['unit_id2']}" : "{$record['unit_id']}"; ?></p></td>
                            <td><p class="table-text"><?php echo $record['total_hours']; ?></p></td>
                            <td><p class="table-text"><?php echo $record['service_hours']; ?></p></td>
                            <td><p class="table-text"><?php echo $record['repair_hours']; ?></p></td>
                            <td><p class="table-text"><?php echo $record['comment']; ?></p></td>
                            <form action="../utility/showTimesheet.php" method="post">
                                <td class="table-btn save-btn no-print" >
                                    <input type="hidden" name="slip_id"
                                        value="<?php echo $record['slip_id']; ?>">
                                  	<input type="hidden" name="employee_id"
                                        value="<?php echo $employee_id; ?>">
                                    <input class="table-btn save-btn" type="submit" value="More Details">
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(!$records) :?>
                        <h2 class="no_results" style="font-size: 12pt" >This employee has not submitted any timesheets.</h2>
                    <?php endif; ?>
                </tbody>
            </table>
            <h2 class="no_results" id="no-results" style="font-size: 12pt; display: none" >There are no timesheets for this date range.</h2>
        </div>
    </div>
		<script>
             // JavaScript to change the min-width of the header
             document.addEventListener('DOMContentLoaded', function () {
                  var header = document.getElementById('myHeader');
                  header.style.minWidth = '720px'; 
             });
          	// JavaScript to change the min-width of the header
             document.addEventListener('DOMContentLoaded', function () {
                  var header = document.getElementById('base_footer');
                  header.style.minWidth = '720px'; 
             });
        </script>


<?php include '../utility/footer.php'; ?>