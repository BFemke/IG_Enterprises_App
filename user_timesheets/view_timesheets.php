<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/

session_start();

include '../utility/loginRequired.php';

require_once('../utility/database.php');

//gets ID of current user
$userID = $_SESSION['user']['employee_id'];

try{
    $query = 'SELECT *
            FROM timesheet t
			JOIN unit_work u ON u.slip_id = t.slip_id
			WHERE t.employee_id = :userID
			ORDER BY t.work_date DESC';
    $statement = $db->prepare($query);
	$statement->bindValue(':userID', $userID);
    $statement->execute();
    $timesheets = $statement->fetchAll();
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
			WHERE employee_id = :userID
			ORDER BY work_date ASC
            LIMIT 1';
    $statement = $db->prepare($query);
	$statement->bindValue(':userID', $userID);
    $statement->execute();
    $oldestDate = $statement->fetch();
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

    <script async src="userFunctions.js"></script>

    <div class="filter_elements">
        <div class="flex_field">
            <label for="from_date"><p>From:&nbsp;&nbsp;</p></label>
            <input type="date"  name="from_date" id="from_date" onChange="filterTimesheets()" value="<?php echo $oldestDate['work_date']; ?>"/>
        </div>

        <div class="flex_field">
            <label for="to_date"><p>To:&nbsp;&nbsp;</p></label>
            <input type="date"  name="to_date" id="to_date" onChange="filterTimesheets()" value="<?php echo date("Y-m-d"); ?>"/>
        </div>
    </div>

    <div class="center-container no-print">
        <button class="btn" type="button" onclick="navigateToForm()">New Timesheet</button>
    </div>

    <h2 class="no_results" id="no-results" style="display:none">There are no timesheets for this date range..</h2>

    <?php if(!$timesheets) : ?>
        <h2 class="no_results">You have not yet submitted any timesheets. Click "New Timesheet" to submit your first timesheet.</h2>
    <?php endif; ?>

    <!-- Prints each timesheet record from the timesheet database -->
	<?php foreach($timesheets as $timesheet) : ?>
	<div class="timesheet_record" id="<?php echo $timesheet['slip_id']; ?>">
		<div class="info">
            <p class="heading"><?php 
                $dtempDate = $timesheet['work_date'];
                $date = new DateTime($dtempDate); // Create a DateTime object
                $formattedDate = $date->format('F d, Y');
                echo $formattedDate;
            ?></p>
            <p class="details"><?php echo $timesheet['location_id'] . ", " . $timesheet['unit_id'];?>
                <?php if(isset($timesheet["unit_id2"])){ echo ", ". $timesheet["unit_id2"];}?></p>
		</div>	
        <p class="sub_date"><?php echo $timesheet['submission_date']; ?></p>
	</div>
	<form id="form<?php echo $timesheet['slip_id']; ?>" action="showTimesheet.php" method="post">
        <input type="hidden" name="slip_id" value="<?php echo $timesheet['slip_id']; ?>">
  	</form>
	<?php endforeach; ?>

	<script>
      	<?php foreach($timesheets as $timesheet){ 
      		echo 'document.addEventListener("DOMContentLoaded", function() {';
            echo '  var submitButton = document.getElementById("' . $timesheet['slip_id'] . '");';
            echo '  submitButton.addEventListener("click", function() {';
            echo '    var form = document.getElementById("form' . $timesheet['slip_id'] . '");';
            echo '    form.submit();';
            echo '  });';
            echo '});';
      	} ?>
	</script>

<?php include '../utility/footer.php'; ?>