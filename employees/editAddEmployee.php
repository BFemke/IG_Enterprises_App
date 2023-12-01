<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/

session_start();

include '../utility/loginRequired.php';

$employee_id = isset($_POST['employee_id']) ? filter_input(INPUT_POST, 'employee_id') : null;

$_SESSION['id'] = $employee_id;

require_once('../utility/database.php');

if($employee_id !== null){
    try{
        //checks if email and password combo exists in database
        $query = "SELECT * FROM employee WHERE employee_id = :employee_id LIMIT 1";
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
}
?>

<?php include '../utility/header.php'; ?>

    <link href="../utility/styles.css" rel="stylesheet" />
    <script async src="manageEmployeeFunctions.js"></script>

    <div class="content_box">
        <h2 class="heading"><?php echo $employee_id === null ? "Add Employee" : "Edit Employee"; ?></h2>
        <form action="submitEmployeeChanges.php" method="post" style="margin: 0px">
            <div class="content">
                <div class="col_1-2">

                    <?php echo $employee_id !== null ? "<input type=\"hidden\" name=\"employee_id\" id=\"employee_id\" value=\"" . $employee_id . "\"/>" : ""; ?>

                    <div class="flex_field">
                        <label for="first_name"><p>First Name:</p></label>
                        <input type="text" name="first_name" id="first_name" required <?php echo "value=\"" . (isset($employee) ? $employee['first_name'] : "") . "\""; ?> />
                    </div>

                    <div class="flex_field">
                        <label for="last_name"><p>Last Name:</p></label>
                        <input type="text" name="last_name" id="last_name" required <?php echo "value=\"" . (isset($employee) ? $employee['last_name'] : "") . "\""; ?>/>
                    </div>

                    <div class="flex_field" >
                        <label for="company"><p>Company:</p></label>
                        <select id="company" name="company" required>
                            <option value="">Select a company..</option>
                            <?php 
                                // Fetch data from the database
                                $query = "SELECT * FROM company WHERE is_active = 1";
                                $statement = $db->prepare($query);
                                $statement->execute();
                                $companies = $statement->fetchAll();
                                $statement->closeCursor();

                                // Populate the options dynamically
                                foreach ($companies as $company) : 
                                    echo "<option value=\"{$company['company_id']}\"" . ($employee['company_id'] === $company['company_id'] ? "selected" : "") . ">{$company['company_name']}</option>";
                                endforeach; 
                            ?>

        
                        </select>
                    </div>

                </div>
                <div class="col_2-2" id="col2">

                    <div class="flex_field">
                        <label for="email"><p>Email:</p></label>
                        <input type="email" name="email" id="email" required <?php echo "value=\"" . (isset($employee) ? $employee['email'] : "") . "\""; ?>/>
                    </div>

                    <div class="flex_field">
                        <label for="pass"><p>Password:</p></label>
                        <input type="text" name="pass" id="pass" required <?php echo "value=\"" . (isset($employee) ? $employee['password'] : "") . "\""; ?>/>
                    </div>

                    <div>
                        <input type="checkbox" name="is_admin" value="admin" <?php if(isset($employee)) { echo $employee['is_admin'] ? "checked" : ""; }?>>Is Administrator
                    </div>

                </div>
            </div>
            <!-- displays form errors if they exist -->
            <?php if(isset($_SESSION['error'])){ 
				echo "<p class=\"error\">".$_SESSION['error']."</p>";
				unset($_SESSION['error']); 
				} ?>  
            <div class="btns">
                <button class="btn" type="submit">Submit</button>
                <button class="btn" type="button" onClick="goBack()">Cancel</button>
            </div>
        </form>
        <?php if($employee_id !== null){
            echo "<form action=\"deactivateEmployee.php\" class=\"flex_field\" method=\"post\" style=\"justify-content: center\">";
            echo "<input type=\"hidden\" name=\"employee_id\" value=\"" . $employee_id . "\"/>";
            echo "<button class=\"slim\" style=\"background-color: gray\"type=\"submit\">Deactivate</button>";
            echo "</form>"; 
        }
        ?>
    </div>

<?php include '../utility/footer.php'; ?>