<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/
session_start();

include '../utility/loginRequired.php';

require_once('../utility/database.php');

try{
    $query = 'SELECT e.*, p.last_name, p.first_name
            FROM event_log e
            JOIN employee p ON admin_id = employee_id
			ORDER BY date DESC';
    $statement = $db->prepare($query);
    $statement->execute();
    $events = $statement->fetchAll();
    $statement->closeCursor();
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

try{
    $query = 'SELECT date
            FROM event_log 
			ORDER BY date ASC
            LIMIT 1';
    $statement = $db->prepare($query);
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

    <script async src="logFunctions.js"></script>

    <div class="content_box not-mobile">
        <h2 class="heading">Event Log</h2>
        <div class="content" style="display: block">
            <div class="spaced-flex">
                <div class="flex_field" style="align-items: center">
                    <label for="event_type"><p>Event Type:&nbsp;&nbsp;</p></label>
                    <select id="event_type" name="event_type" onChange="filterLogs()">
                        <option value="">Filter by event..</option>
                        <option value="Add Company">Add Company</option>
                        <option value="Add Employee">Add Employee</option>
                        <option value="Company Deactivated">Company Deactivated</option>
                        <option value="Company Reactivated">Company Reactivated</option>
                        <option value="Edit Employee">Edit Employee</option>
                        <option value="Edit Timesheet">Edit Timesheet</option>
                        <option value="Employee Deactivated">Employee Deactivated</option>
                        <option value="Employee Reactivated">Employee Reactivated</option>
                    </select>
                </div>

                <div class="flex_field">
                    <div class="flex_field" style="margin-right: 10px;">
                        <label for="from_date"><p>From:&nbsp;&nbsp;</p></label>
                        <input type="date" name="from_date" id="from_date" onChange="filterLogs()" value="<?php  $dateString = $oldestDate['date']; $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateString); echo $dateTime->format('Y-m-d'); ?>" />

                    </div>

                    <div class="flex_field">
                        <label for="to_date"><p>To:&nbsp;&nbsp;</p></label>
                        <input type="date"  name="to_date" id="to_date" onChange="filterLogs()" value="<?php echo date("Y-m-d"); ?>"/>
                    </div>
                </div>

            </div>
            <table class="with-borders">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Admin</th>
                     </tr>
                </thead>
                <tbody>
                    <?php foreach($events as $event) : ?>
                        <tr class="event_record">
                            <td class="table-text"><?php echo $event['log_id']; ?></td>  
                            <td class="table-text date"><?php echo $event['date']; ?></td>
                            <td class="table-text eventType"><?php echo $event['event_type'] ?></td>
                            <td class="table-text"><?php echo $event['description'] ?></td>
                            <td class="table-text"><?php echo $event['first_name'] . " " . $event['last_name']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                 </tbody>
            </table>
            <h2 class="no_results" id="no-results" style="display:none" style="font-size: 12pt" >No logs exist with these filters.</h2>
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