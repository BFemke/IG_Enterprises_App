<?php
/*
    Author: Barbara Emke
    Date:   November 14, 
*/
session_start();

include '../utility/loginRequired.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../utility/database.php');

$_SESSION['path'] = "../dashboard/view_dashboard.php";

$setDate = date("Y-m-d");

if(isset($_SESSION['date'])){
    $setDate = $_SESSION['date'];
}

try{
    $query = 'SELECT t.*, e.last_name, e.first_name, password
            FROM timesheet t
            JOIN employee e ON t.employee_id = e.employee_id
			WHERE work_date = :setDate
			ORDER BY last_name ASC';
    $statement = $db->prepare($query);
	$statement->bindValue(':setDate', $setDate);
    $statement->execute();
    $timesheets = $statement->fetchAll();
    $statement->closeCursor();
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

try{$query = "SELECT e.*, t.work_date
    FROM employee e
    LEFT JOIN timesheet t ON e.employee_id = t.employee_id AND t.work_date = :setDate
    WHERE t.employee_id IS NULL AND password IS NOT NULL
    ORDER BY e.last_name DESC";
    $statement = $db->prepare($query);
	$statement->bindValue(':setDate', $setDate);
    $statement->execute();
    $employees = $statement->fetchAll();
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

    <div class="content_box">
        <h2 class="heading">Daily Timesheets</h2>
        <div class="flex_field" style="justify-content:center">
                <label for="date"><p>Date:&nbsp;&nbsp;</p></label>
                <input type="date"  name="date" id="date" onChange="refresh()" value="<?php 
                            echo isset($_SESSION['date']) ? $_SESSION['date'] : date("Y-m-d"); ?>"/>
        </div>
        <div class="content">
            <div class="col1-3" >
                <h3>Missing Timesheets</h3>
                <table>
                    <tbody>
                        <?php foreach($employees as $employee) : ?>
                            <tr style="border:none;">
                                <td class="col2-3" style="padding: 0px"><p class="table-text"><?php echo $employee['first_name'] . " " . $employee['last_name']; ?></p></td>
                                <form action="sendReminder.php" method="post">
                                    <td class="table-btn col1-3 no-print" style="border: none">
                                        <input type="hidden" name="employee_id"
                                            value="<?php echo $employee['employee_id']; ?>">
                                        <input class="table-btn" type="submit" value="Remind">
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(!$employees) :?>
                            <h2 class="no_results" style="font-size: 12pt" >No employees have missed their submissions for this day.</h2>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        
            <div class="col2-3">
                <h3>Submitted Timesheets</h3>
                <table class="with-borders">
                    <thead>
                        <?php if($timesheets) :?>
                            <tr>
                                <th>Name</th>
                                <th>Total Hours</th>
                                <th>Comments</th>
                                <th class="no-print">&nbsp;</th>
                                <th class="no-print">&nbsp;</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php foreach($timesheets as $timesheet) : ?>
                            <tr>
                                <form action="saveEdits.php" method="post">
                                    <td><p class="table-text"><?php echo $timesheet['first_name'] . " " . $timesheet['last_name']; ?></p></td>
                                    <td><input type="number" class="table-text tbl-input"contenteditable="true" step="0.1" name="t_hours" value="<?php echo $timesheet['total_hours']; ?>" required /></td>
                                    <td style="word-wrap: normal"><input type="text" class="table-text tbl-input"contenteditable="true" name="comments" value="<?php echo $timesheet['comment']; ?>"/></td>
                                    <td class="table-btn save-btn no-print" >
                                        <input type="hidden" name="slip_id"
                                            value="<?php echo $timesheet['slip_id']; ?>">
                                        <input class="table-btn save-btn" type="submit" value="Save">
                                    </td>
                                </form>
                                <form action="../utility/showTimesheet.php" method="post">
                                    <td class="table-btn view-btn no-print" >
                                        <input type="hidden" name="slip_id"
                                            value="<?php echo $timesheet['slip_id']; ?>">
                                        <input class="table-btn view-btn" type="submit" value="View">
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                      	<?php if(isset($_SESSION['error'])){ 
                        echo "<p class=\"error\">".$_SESSION['error']."</p>";
                        unset($_SESSION['error']); 
                        } ?>   
                        <?php if(!$timesheets) :?>
                            <h2 class="no_results" style="font-size: 12pt" >No employees have submitted their timesheets for this day.</h2>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>

        function refresh(){
            const dateStr = document.getElementById('date').value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "save_to_session.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("inputValue=" + dateStr);

            //reloads page with new date set
            location.reload(); 
        }
    </script>

<?php include '../utility/footer.php'; ?>