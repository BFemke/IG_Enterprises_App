<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/
session_start();

include '../utility/loginRequired.php';

require_once('../utility/database.php');

$slip_id = filter_input(INPUT_POST, 'slip_id');
$employee_id = filter_input(INPUT_POST, 'employee_id');

$_SESSION['id'] = $employee_id;
$_SESSION['slip_id'] = $slip_id;

try{
    $query = 'SELECT t.*, u.*
            FROM timesheet t
            JOIN unit_work u ON t.slip_id = u.slip_id
            WHERE t.slip_id = :slip_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':slip_id', $slip_id);
    $statement->execute();
    $timesheet = $statement->fetch();
    $statement->closeCursor();
} catch (PDOException $e) {
    // Handle database error
    $error_message = $e->getMessage();
    include('../utility/database_error.php');
    exit();
}

$formattedDate = date('Y-m-d', strtotime($timesheet['work_date']));
echo $formattedDate;
?>

<?php include '../utility/header.php'; ?>

    <link href="../utility/styles.css" rel="stylesheet" />

    <form class="form_timesheet" id="t_form" action="submitEditedTimesheet.php" method="post">
        <div class="content_box">

            <div class="form_heading">
                <h2>Timesheet</h2>
                <div class="flex_field">
                    <label for="date"><p>Date:&nbsp;&nbsp;</p></label>
                    <input type="date"  name="date" id="date" value="<?php echo $formattedDate;?>"/>
                </div>
            </div>

            <div class="col_1-2">

                <div class="flex_field" styles="justify-content:center">
                    <input type="radio" name="shift" value="day" style="flex: 1" <?php echo ($timesheet['is_dayshift'] === 1) ? "checked" : ""; ?>>Dayshift
                    <input type="radio" name="shift" value="night" style="flex: 1" <?php echo ($timesheet['is_dayshift'] === 0) ? "checked" : ""; ?>>Nighshift
                </div>

                <div class="flex_field" >
                    <label for="location"><p>*Location:</p></label>
                    <select id="location" name="location" required>
                        <option value="">Select a location..</option>
                        <?php 
                            // Fetch data from the database
                            $query = "SELECT location_id FROM location";
                            $statement = $db->prepare($query);
                            $statement->execute();
                            $locations = $statement->fetchAll();
                            $statement->closeCursor();

                            // Populate the options dynamically
                            foreach ($locations as $location) : 
                                $selected = "";
                                if($location['location_id'] === $timesheet['location_id']){
                                    $selected = "selected";
                                }
                                echo "<option value=\"{$location['location_id']}\" {$selected} }>{$location['location_id']}</option>";
                            endforeach; 
                        ?>

    
                    </select>
                </div>

                <div class="flex_field">
                    <label for="t_hours" required><p>*Total Hours:</p></label>
                    <input type="number" step="0.1" name="t_hours" id="t_hours" value="<?php echo $timesheet['total_hours'] ?>"/>
                </div>

                <div class="flex_field">
                    <label for="s_hours"><p>Service Hours:</p></label>
                    <input type="number" step="0.1" name="s_hours" id="s_hours" value="<?php echo $timesheet['service_hours'] ?>" />
                </div>

                <div class="flex_field">
                    <label for="r_hours"><p>Repair Hours:</p></label>
                    <input type="number" step="0.1" name="r_hours" id="r_hours"  value="<?php echo $timesheet['repair_hours'] ?>"/>
                </div>

                <div class="flex_field">
                    <label for="hmr"><p>Hour Meter Reading:</p></label>
                    <input type="text"  name="hmr" id="hmr"  value="<?php echo isset($timesheet['hour_meter_reading']) ? $timesheet['hour_meter_reading'] : "" ?>"/>
                </div>

                <div class="flex_field">
                    <label for="work_type"><p>Type of Work:</p></label>
                    <input type="text"  name="work_type" id="work_type" value="<?php echo isset($timesheet['work_type']) ? $timesheet['work_type'] : "" ?>" />
                </div>

            </div>

            <div class="col_2-2" id="col2">

                <div class="flex_field">
                    <div class="field">
                        <label for="h_oil"><p>Hyd Oil:</p></label>
                        <input type="number" style="width:80%" name="h_oil" id="h_oil" value="<?php echo $timesheet['hyd_oil'] ?>" />
                    </div>
                    <div class="field">
                        <label for="e_oil"><p>Engine Oil:</p></label>
                        <input type="number"  style="width:80%" name="e_oil" id="e_oil" value="<?php echo $timesheet['engine_oil'] ?>" />
                    </div>
                </div>
                <div class="flex_field">
                    <div class="field">
                        <label for="antifreeze"><p>Antifreeze:</p></label>
                        <input type="number"  style="width:80%" name="antifreeze" id="antifreeze" value="<?php echo $timesheet['antifreeze'] ?>" />
                    </div>
                    <div class="field">
                        <label for="def"><p>DEF:</p></label>
                        <input type="number"  style="width:80%" name="def" id="def" value="<?php echo $timesheet['def'] ?>" />
                    </div>
                </div>

                <div class="flex_field" >
                    <label for="unit1"><p>*Unit #:</p></label>
                    <select id="unit1" name="unit1" required>
                        <option value="">Select a unit #..</option>
                        <?php 
                            // Fetch data from the database
                            $query = "SELECT unit_id, unit_type FROM unit";
                            $statement = $db->prepare($query);
                            $statement->execute();
                            $units = $statement->fetchAll();
                            $statement->closeCursor();

                            // Populate the options dynamically
                            foreach ($units as $unit) : 
                                $selected = "";
                                if($unit['unit_id'] === $timesheet['unit_id']){
                                    $selected = "selected";
                                }
                                echo "<option value=\"{$unit['unit_id']}|{$unit['unit_type']}\" {$selected} >{$unit['unit_id']}</option>";
                            endforeach; 
                        ?>
                    </select>
                </div>

                <div class="flex_field" id="op_hours1_box" >
                    <label for="op_hours1"><p>*Operator Hours:</p></label>
                    <input type="number"step="0.1"  name="op_hours1" id="op_hours1" value="<?php echo $timesheet['operating_hours']; ?>"/>
                </div>

                <div class="flex_field" id="unit2_box" >
                    <label for="unit2"><p>Unit #:</p></label>
                    <select id="unit2" name="unit2">
                        <option value="">Select a unit #..</option>
                        <?php 
                            // Fetch data from the database
                            $query = "SELECT unit_id, unit_type FROM unit";
                            $statement = $db->prepare($query);
                            $statement->execute();
                            $units = $statement->fetchAll();
                            $statement->closeCursor();

                            // Populate the options dynamically
                            foreach ($units as $unit) : 
                                $selected = "";
                                if($unit['unit_id'] === $timesheet['unit_id2']){
                                    $selected = "selected";
                                }
                                echo "<option value=\"{$unit['unit_id']}|{$unit['unit_type']}\" {$selected} >{$unit['unit_id']}</option>";
                            endforeach; 
                        ?>
                    </select>
                </div>

                <div class="flex_field" id="op_hours2_box" >
                    <label for="op_hours2"><p>*Operator Hours:</p></label>
                    <input type="number" step="0.1" name="op_hours2" id="op_hours2" value="<?php echo $timesheet['operating_hours2']; ?>"/>
                </div>

                <div class="flex_field" id="stems_box">
                    <label for="stems"><p>Stems:</p></label>
                    <input type="number" step="0.1" name="stems" id="stems"  value="<?php echo $timesheet['stems'] ?>" />
                </div>

                <div class="flex_field" id="meters_box">
                    <label for="meters"><p>Meters:</p></label>
                    <input type="number"step="0.1"  name="meters" id="meters" value="<?php echo $timesheet['meters'] ?>"  />
                </div>

                <div class="flex_field" id="loads_box">
                    <label for="loads"><p>Loads:</p></label>
                    <input type="number" step="0.1" name="loads" id="loads" value="<?php echo $timesheet['loads'] ?>"  />
                </div>

                <div class="flex_field" id="decking_box">
                    <label for="decking"><p>Decking:</p></label>
                    <input type="number" step="0.1" name="decking" id="decking" value="<?php echo $timesheet['decking'] ?>"  />
                </div>

                <div class="flex_field" id="hoe_box">
                    <label for="hoe"><p>Hoe Chucking:</p></label>
                    <input type="number" step="0.1" name="hoe" id="hoe" value="<?php echo $timesheet['hoe_chucking'] ?>"  />
                </div>

                <div class="flex_field" id="tops_box">
                    <label for="tops"><p>Tops Piling:</p></label>
                    <input type="number" step="0.1" name="tops" id="tops" value="<?php echo $timesheet['tops_piling'] ?>"  />
                </div>

            </div>

            <div class="center-container">
                <div styles="flex:1">
                    <label for="comments"><p>Notes / Repairs/ Ticket # / etc.</p></label>
                    <textarea id="comments" name="comments" rows="4" cols="50" ><?php echo $timesheet['comment'] ?></textarea>
                </div>
            </div>
            <?php if(isset($_SESSION['error'])){ 
			echo "<p class=\"error\">".$_SESSION['error']."</p>";
			unset($_SESSION['error']); 
			} ?>
        </div>

        <div class="btns">
            <button class="btn" type="submit">Update</button>
            <button class="btn" type="button" onClick="closeForm()">Back</button>
        </div>

    </form>

    <script>

      	function closeForm(){
          	window.location.href = document.referrer;
        }
    </script>

<?php include '../utility/footer.php'; ?>