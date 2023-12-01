<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/
session_start();

include '../utility/loginRequired.php';

require_once('../utility/database.php');

$slip_id = filter_input(INPUT_POST, 'slip_id');

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
?>

<?php include '../utility/header.php'; ?>

    <link href="../utility/styles.css" rel="stylesheet" />

        <div class="content_box static-form">

            <div class="form_heading">
                <h2>Timesheet</h2>
                <div class="flex_field" style="justify-content: space-between">
                    <label for="date"><p>Date:&nbsp;&nbsp;</p></label>
                    <p type="date"  name="date" id="date"><?php echo $timesheet['work_date']; ?></p>
                </div>
            </div>


                <div class="flex_field" style="justify-content: space-between">
                  	<label><p><b>Shift:</b></p></label>
                    <?php echo "<p>". ($timesheet['is_dayshift'] === 0) ? "NightShift" : "Dayshift" . "</p>";?>
                </div>

                <div class="flex_field" style="justify-content: space-between">
                  	<label for="location"><p><b>Location:</b></p></label>
                    <p><?php echo $timesheet['location_id'];?></p>
                </div>

                <div class="flex_field" style="justify-content: space-between">
                  	<label for="t_hours" required><p><b>Total Hours:</b></p></label>
                    <p type="number"  name="t_hours" id="t_hours"><?php echo $timesheet['total_hours'] ;?></p>
                </div>

              	<?php if($timesheet['service_hours'] != 0) : ?>
                <div class="flex_field" style="justify-content: space-between">
                  	<label for="s_hours"><p><b>Service Hours:</b></p></label>
                    <p type="number"  name="s_hours" id="s_hours"><?php echo $timesheet['service_hours']; ?></p>
                </div>
              	<?php endif; ?>

              	<?php if($timesheet['repair_hours'] != 0) : ?>
                <div class="flex_field" style="justify-content: space-between">
                  	<label for="r_hours"><p><b>Repair Hours:</b></p></label>
                    <p type="number"  name="r_hours" id="r_hours"><?php echo $timesheet['repair_hours']; ?></p>
                </div>
              	<?php endif; ?>

              	<?php if($timesheet['hour_meter_reading'] !== "") : ?>
                <div class="flex_field" style="justify-content: space-between">
                  	<label for="hmr"><p><b>Hour Meter Reading:</b></p></label>
                    <p type="text"  name="hmr" id="hmr"><?php echo $timesheet['hour_meter_reading']; ?></p>
                </div>
              	<?php endif; ?>

              	<?php if($timesheet['work_type'] !== "") : ?>
                <div class="flex_field" style="justify-content: space-between">
                  <label for="work_type"><p><b>Type of Work:</b></p></label>
                    <p type="text"  name="work_type" id="work_type"><?php echo $timesheet['work_type'];?></p>
                </div>
              	<?php endif; ?>



                
                  
                  	<?php if($timesheet['hyd_oil'] !== 0) : ?>
                    <div class="flex_field" style="justify-content: space-between" style="text-align:right">
                      <label for="h_oil"><p><b>Hyd Oil:</b></p></label>
                        <p type="number" name="h_oil" id="h_oil" ><?php echo $timesheet['hyd_oil']; ?></p>
                    </div>
                  	<?php endif; ?>
                  
                  	<?php if($timesheet['engine_oil'] !== 0) : ?>
                    <div class="flex_field" style="justify-content: space-between" style="text-align:right">
                      <label for="e_oil"><p><b>Engine Oil:</b></p></label>
                        <p type="number" name="e_oil" id="e_oil"><?php echo $timesheet['engine_oil']; ?></p>
                    </div>
                  	<?php endif; ?>
                
                
                  
                  	<?php if($timesheet['antifreeze'] !== 0) : ?>
                    <div class="flex_field" style="justify-content: space-between" style="text-align:right">
                      <label for="antifreeze"><p><b>Antifreeze:</b></p></label>
                        <p type="number" name="antifreeze" id="antifreeze"><?php echo $timesheet['antifreeze']; ?></p>
                    </div>
                  	<?php endif; ?>
                  
                  	<?php if($timesheet['def'] !== 0) : ?>
                    <div class="flex_field" style="justify-content: space-between" style="text-align:right">
                      <label for="def"><p><b>DEF:</b></p></label>
                        <p type="number"name="def" id="def"><?php echo $timesheet['def']; ?></p>
                    </div>
                  	<?php endif; ?>
                

                <div class="flex_field" style="justify-content: space-between">
                  <label for="unit1"><p><b>Unit #:</b></p></label>
                    <p><?php echo $timesheet['unit_id']; ?></p>
                </div>

              	<?php if($timesheet['operating_hours'] != 0) : ?>
                <div class="flex_field" id="op_hours1_box" style="justify-content: space-between">
                  <label for="op_hours1"><p><b>Operator Hours:</b></p></label>
                    <p type="number"  name="op_hours1" id="op_hours1"><?php echo $timesheet['operating_hours']; ?></p>
                </div>
              	<?php endif; ?>

              	<?php if($timesheet['unit_id2'] != null) : ?>
                <div class="flex_field" id="unit2_box" style="justify-content: space-between">
                  <label for="unit2"><p><b>Unit 2 #:</b></p></label>
                    <p><?php echo $timesheet['unit_id2']; ?></p>
                </div>
              	<?php endif; ?>

              	<?php if($timesheet['operating_hours2'] != 0) : ?>
                <div class="flex_field" id="op_hours2_box" style="justify-content: space-between">
                  <label for="op_hours2"><p><b>Operator Hours:</b></p></label>
                    <p type="number"  name="op_hours2" id="op_hours2"><?php echo $timesheet['operating_hours2']; ?></p>
                </div>
              	<?php endif; ?>

              	<?php if($timesheet['stems'] != 0) : ?>
                <div class="flex_field" id="stems_box" style="justify-content: space-between">
                  <label for="stems"><p><b>Stems:</b></p></label>
                    <p type="number"  name="stems" id="stems"><?php echo $timesheet['stems']; ?></p>
                </div>
              	<?php endif; ?>

              	<?php if($timesheet['meters'] != 0) : ?>
                <div class="flex_field" id="meters_box" style="justify-content: space-between">
                  <label for="meters"><p><b>Meters:</b></p></label>
                    <p type="number"  name="meters" id="meters"><?php echo $timesheet['meters']; ?></p>
                </div>
              	<?php endif; ?>

              	<?php if($timesheet['loads'] != 0) : ?>
                <div class="flex_field" id="loads_box" style="justify-content: space-between">
                  <label for="loads"><p><b>Loads:</b></p></label>
                    <p type="number"  name="loads" id="loads" ><?php echo $timesheet['loads']; ?></p>
                </div>
              	<?php endif; ?>

              	<?php if($timesheet['decking'] != 0) : ?>
                <div class="flex_field" id="decking_box" style="justify-content: space-between">
                  <label for="decking"><p><b>Decking:</b></p></label>
                    <p type="number"  name="decking" id="decking"><?php echo $timesheet['decking']; ?></p>
                </div>
              	<?php endif; ?>

              	<?php if($timesheet['hoe_chucking'] != 0) : ?>
                <div class="flex_field" id="hoe_box" style="justify-content: space-between">
                  <label for="hoe"><p><b>Hoe Chucking:</b></p></label>
                    <p type="number"  name="hoe" id="hoe"><?php echo $timesheet['hoe_chucking']; ?></p>
                </div>
              	<?php endif; ?>

              	<?php if($timesheet['tops_piling'] != 0) : ?>
                <div class="flex_field" id="tops_box" style="justify-content: space-between">
                  <label for="tops"><p><b>Tops Piling:</b></p></label>
                    <p type="number"  name="tops" id="tops"><?php echo $timesheet['tops_piling']; ?></p>
                </div>
              	<?php endif; ?>


            <div class="center-container">
                <div styles="flex:1">
                  <label for="comments"><p><b>Notes / Repairs/ Ticket # / etc.</b></p></label>
                    <p id="comments" name="comments" rows="4"><?php echo $timesheet['comment']; ?></p>
                </div>
            </div>
            <?php if(isset($_SESSION['error'])){ 
			echo "<p class=\"error\">".$_SESSION['error']."</p>";
			unset($_SESSION['error']); 
			} ?>
        </div>

        <div class="btns">
            <button class="btn" type="button" onClick="closeForm()">Close</button>
        </div>

    <script>

        //prefills the date input with the current date
        document.getElementById('date').valueAsDate = new Date();

        //navigates back to time sheet records page without saving timesheet
        function closeForm(){
            window.location.href = 'view_timesheets.php';
        }
    </script>

<?php include '../utility/footer.php'; ?>