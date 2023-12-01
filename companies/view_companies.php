<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/

session_start();

include '../utility/loginRequired.php';

require_once('../utility/database.php');

try{
    $query = 'SELECT *
            FROM company
			ORDER BY company_name ASC';
    $statement = $db->prepare($query);
    $statement->execute();
    $companies = $statement->fetchAll();
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

    <div class="content_box" >
        <h2 class="heading">Companies</h2>
        <div class="content" style="display:block">
            <table>
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Status</th>
                        <th>&nbsp;</th>
                     </tr>
                </thead>
                <tbody>
                    <?php foreach($companies as $company) : ?>
                        <tr>
                            <td class="table-text"><?php echo $company['company_name']; ?></td>
                            <td class="table-text"><?php echo $company['is_active'] ? "Active" : "Inactive"; ?></td>
                            <form action="deactivateCompany.php" method="post">
                                <td class="table-btn save-btn no-print" >
                                    <input type="hidden" name="company_id"
                                        value="<?php echo $company['company_id']; ?>">
                                    <input type="hidden" name="is_active"
                                        value="<?php echo $company['is_active']; ?>">
                                    <input class="table-btn save-btn" type="submit" value="<?php echo $company['is_active'] ? "Deactivate" : "Activate"; ?>">
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="no-print">
                        <form action="addCompany.php" method="post">
                            <td colspan="2"><input type="text" class="table-text tbl-input bordered" name="company_name" required /></td>
                            <td class="table-btn view-btn" >
                                <input class="table-btn view-btn" type="submit" value="Add">
                            </td>
                        </form>
                     </tr>
                 </tbody>
            </table>
          	<?php if(isset($_SESSION['error'])){ 
				echo "<p class=\"error\">".$_SESSION['error']."</p>";
				unset($_SESSION['error']); 
				} ?>  
        </div>
    </div>
<?php include '../utility/footer.php'; ?>