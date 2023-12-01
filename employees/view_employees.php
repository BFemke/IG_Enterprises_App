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
            JOIN company c ON c.company_id = e.company_id
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

if(isset($_SESSION['recipient'])){
	$recipient =  $_SESSION['recipient'];
}

?>

<?php include '../utility/header.php'; ?>

<?php include '../employees/messageFormPopup.php'; ?>

    <link href="../utility/styles.css" rel="stylesheet" />

    <script async src="manageEmployeeFunctions.js"></script>

	    <!-- Popup form change users password -->
	<div class="modal-container" id="message_form">
		<div class="form_popup" style="height:490px">
			<form class="form-container" action="sendMessage.php" method="post">
				<p class="close_button" onclick="closeForm()">X</p>
				<h3>Message:</h3>
				<input type="hidden" name="employee_id" value="<?php echo $recipient; ?>">
              	<div class="field">
					<label for="title"><p>Title:</p></label>
					<input type="text" name="title" required />
				</div>
              	<div class="field">
                    <label for="message"><p>Message:</p></label>
					<textarea name="message" rows="4" cols="38" required ></textarea>
                 </div>

				<div class="btns">
					<button type="submit" class="btn" >Send</button>
				</div>
			</form>
		</div>
	</div>

    <div class="content_box not-mobile">
        <h2 class="heading">Employees</h2>
        <div class="content" style="display: block">
            <div class="spaced-flex">
                <div class="flex_field no-print" style="align-items: center">
                    <label for="company"><p>Filter:&nbsp;&nbsp;</p></label>
                    <select id="company" name="company" onChange="filterEmployees();">
                        <option value="">Select a company..</option>
                        <?php 
                            // Fetch data from the database
                            $query = "SELECT company_name FROM company ORDER BY company_name ASC";
                            $statement = $db->prepare($query);
                            $statement->execute();
                            $names = $statement->fetchAll();
                            $statement->closeCursor();

                            // Populate the options dynamically
                            foreach ($names as $name) : 
                                echo "<option value=\"{$name['company_name']}\">{$name['company_name']}</option>";
                            endforeach; 
                        ?>
                    </select>
                    <input type="checkbox" id="show_deactivated" style="margin-left: 10px;" onChange="filterEmployees()">Show Deactivated</input>
                </div>
                <form class="no-print" action="editAddEmployee.php" method="post">
                    <button class="slim" type="submit">New Employee</button>
                </form>
            </div>
            <table class="with-borders">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th class="no-print" colspan="3">&nbsp;</th>
                     </tr>
                </thead>
                <tbody>
                    <?php foreach($employees as $employee) : ?>
                        <tr class="employee-record" <?php echo $employee['password'] === null ? "style=\"display:none\"" : ""; ?>>
                            <td class="pass" style="display: none"><?php echo $employee['password']; ?></td>  
                            <td class="table-text"><?php echo $employee['employee_id']; ?></td>  
                            <td class="table-text"><?php echo $employee['first_name'] . " " . $employee['last_name']; ?></td>
                            <td class="table-text company_name"><?php echo $employee['company_name'] ?></td>
                            <td class="table-text"><?php echo $employee['password'] === null ? "Inactive" : "Active"; ?></td>

                            <form action="viewEmployeeTimesheets.php" method="post">
                                <td class="table-btn save-btn no-print" >
                                    <input type="hidden" name="employee_id"
                                        value="<?php echo $employee['employee_id']; ?>">
                                    <input class="table-btn save-btn" type="submit" value="Timesheets">
                                </td>
                            </form>

                            <form action="openMessageForm.php" method="post">
                                <td class="table-btn view-btn no-print" >
                                    <input type="hidden" name="employee_id"
                                        value="<?php echo $employee['employee_id']; ?>">
                                    <input class="table-btn view-btn" type="submit" value="Message">
                                </td>
                            </form>

                            <form action="editAddEmployee.php" method="post">
                                <td class="table-btn no-print" >
                                    <input type="hidden" name="employee_id"
                                        value="<?php echo $employee['employee_id']; ?>">
                                    <input class="table-btn " type="submit" value="Edit">
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                 </tbody>
            </table>
            <h2 class="no_results" id="no-results" style="display:none" style="font-size: 12pt" >No employees exist from this company.</h2>
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
          
          	<?php 
            if(isset($_SESSION['recipient'])){
				unset($_SESSION['recipient']);
                echo "const popup = document.getElementById('message_form');";
                //makes popup visible
                echo "popup.style.display = \"block\"; ";
            }

            ?>
        </script>
<?php include '../utility/footer.php'; ?>