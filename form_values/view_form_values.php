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
            FROM unit
			ORDER BY unit_type ASC, unit_id ASC';
    $statement = $db->prepare($query);
    $statement->execute();
    $units = $statement->fetchAll();
    $statement->closeCursor();
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

try{
    $query = 'SELECT *
            FROM location
			ORDER BY location_id ASC';
    $statement = $db->prepare($query);
    $statement->execute();
    $locations = $statement->fetchAll();
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
        <h2 class="heading">Timesheet Input Variables</h2>
        <div class="content">
            <div class="col1-2" style="border-right: 1px solid #000000;">
                <h3>Locations</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Location Code</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($locations as $location) : ?>
                            <tr>
                                <td class="table-text"><?php echo $location['location_id']; ?></td>
                                <form action="deleteLocation.php" method="post">
                                    <td class="table-btn save-btn no-print" >
                                        <input type="hidden" name="location_id"
                                            value="<?php echo $location['location_id']; ?>">
                                        <input class="table-btn save-btn" type="submit" value="Delete">
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                            <tr class="no-print">
                                <form action="addLocation.php" method="post">
                                    <td><input type="text" class="table-text tbl-input bordered" name="location" required /></td>
                                    <td class="table-btn view-btn" >
                                        <input class="table-btn view-btn" type="submit" value="Add">
                                    </td>
                                </form>
                            </tr>
                    </tbody>
                </table>
              	<?php if(isset($_SESSION['location_error'])){ 
				echo "<p class=\"error\">".$_SESSION['location_error']."</p>";
				unset($_SESSION['location_error']); 
				} ?>   
            </div>
            <div class="col1-2">
                <h3>Unit Numbers</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($units as $unit) : ?>
                            <tr>
                                <td class="table-text"><?php echo $unit['unit_id']; ?></td>
                                <td class="table-text"><?php echo $unit['unit_type']; ?></td>
                                <form action="deleteUnit.php" method="post">
                                    <td class="table-btn save-btn no-print" >
                                        <input type="hidden" name="unit_id"
                                            value="<?php echo $unit['unit_id']; ?>">
                                        <input class="table-btn save-btn" type="submit" value="Delete">
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                            <tr class="no-print">
                            	<form action="addUnit.php" method="post">
                                  <td><input type="text" class="table-text tbl-input bordered" name="unit_id" required/></td>
                                  <td><input type="text" class="table-text tbl-input bordered" name="unit_type" required/></td>
                                  <td class="table-btn view-btn" >
                                      <input class="table-btn view-btn" type="submit" value="Add">
                                  </td>
                              </form>
                            </tr>
                    </tbody>
                </table>
              	<?php if(isset($_SESSION['unit_error'])){ 
				echo "<p class=\"error\">".$_SESSION['unit_error']."</p>";
				unset($_SESSION['unit_error']); 
				} ?> 
            </div>
        </div>
    </div>

<?php include '../utility/footer.php'; ?>