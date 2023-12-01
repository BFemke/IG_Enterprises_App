<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/

session_start();

include '../utility/loginRequired.php';

require_once('../utility/database.php');

try{
    $query = 'SELECT e.*, c.company_name
            FROM employee e
            JOIN company c ON e.company_id = c.company_id
            WHERE password IS NOT NULL
			ORDER BY last_name ASC';
    $statement = $db->prepare($query);
    $statement->execute();
    $employees = $statement->fetchAll();
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

    <script async src="payPeriodFunctions.js"></script>

    <div class="content_box">
        <h2 class="heading">Pay Period Summaries</h2>
        <div class="content" style="display: block">
            <form id="generate_all">
                <div class="spaced-flex">

                    <div class="flex_field">
                        <div class="flex_field" style="margin-right: 10px;">
                            <label for="from_date"><p>Pay Period:&nbsp;&nbsp;</p></label>
                            <input type="date" name="from_date" id="from_date" />
                        </div>

                        <div class="flex_field">
                            <label for="to_date"><p>To:&nbsp;&nbsp;</p></label>
                            <input type="date"  name="to_date" id="to_date" />
                        </div>
                    </div>

                    <button class="slim" type="submit">Generate All</button>

                </form>
            </div>
            <table class="with-borders">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Company</th>
                        <th>&nbsp;</th>
                     </tr>
                </thead>
                <tbody>
                    <?php foreach($employees as $employee) : ?>
                        <tr class="event_record">
                            <td class="table-text"><?php echo $employee['first_name'] . " " . $employee['last_name'];  ?></td>  
                            <td class="table-text"><?php echo $employee['company_name']; ?></td>
                            <form id="<?php echo $employee['employee_id']; ?>" method="post">
                                <td class="table-btn " >
                                    <input type="hidden" name="employee_id"
                                        value="<?php echo $employee['employee_id']; ?>">
                                    <input class="table-btn " type="submit" value="PDF">
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                 </tbody>
            </table>
            <script>
                document.getElementById('generate_all').addEventListener('submit', function(event) {
                        event.preventDefault();

                        //gets date ranges
                        var to_date = document.getElementById('to_date').value;
                        var from_date = document.getElementById('from_date').value;
                        
                        //generate pay report for specified employee
                        generateAllReports(from_date, to_date);
                    });

                <?php foreach($employees as $employee) : ?>
                    document.getElementById('<?php echo $employee['employee_id']; ?>').addEventListener('submit', function(event) {
                        event.preventDefault();

                        //gets date ranges
                        var to_date = document.getElementById('to_date').value;
                        var from_date = document.getElementById('from_date').value;
                        
                        //generate pay report for specified employee
                        var employee_id = "<?php echo $employee['employee_id']; ?>";
                        generateEmployeeReport(employee_id, from_date, to_date);
                    });
                <?php endforeach; ?>
            </script>
            <h2 class="no_results" id="no-results" style="display:none" style="font-size: 12pt" >No logs exist with these filters.</h2>
        </div>
<?php include '../utility/footer.php'; ?>