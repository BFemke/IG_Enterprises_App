<?php
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/

session_start();

include '../utility/loginRequired.php';

require_once('../utility/database.php');
?>

<?php include '../utility/header.php'; ?>

    <link href="../utility/styles.css" rel="stylesheet" />

    <form class="form_timesheet" id="t_form" action="submit_timesheet.php" method="post">
        <div class="content_box">

            <div class="form_heading">
                <h2>Timesheet</h2>
                <div class="flex_field">
                    <label for="date"><p>Date:&nbsp;&nbsp;</p></label>
                    <input type="date"  name="date" id="date" value="<?php echo date("Y-m-d") ?>"/>
                </div>
            </div>

            <div class="col_1-2">

                <div class="flex_field" styles="justify-content:center">
                    <input type="radio" name="shift" value="day" style="flex: 1" checked>Dayshift
                    <input type="radio" name="shift" value="night" style="flex: 1">Nighshift
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
                                echo "<option value=\"{$location['location_id']}\">{$location['location_id']}</option>";
                            endforeach; 
                        ?>

    
                    </select>
                </div>

                <div class="flex_field">
                    <label for="t_hours"><p>*Total Hours:</p></label>
                    <input type="number"step="0.1" name="t_hours" id="t_hours" required />
                </div>

                <div class="flex_field">
                    <label for="s_hours"><p>Service Hours:</p></label>
                    <input type="number" step="0.1" name="s_hours" id="s_hours" />
                </div>

                <div class="flex_field">
                    <label for="r_hours"><p>Repair Hours:</p></label>
                    <input type="number" step="0.1" name="r_hours" id="r_hours" />
                </div>

                <div class="flex_field">
                    <label for="hmr"><p>Hour Meter Reading:</p></label>
                    <input type="text"  name="hmr" id="hmr" />
                </div>

                <div class="flex_field">
                    <label for="work_type"><p>Type of Work:</p></label>
                    <input type="text"  name="work_type" id="work_type" />
                </div>

            </div>

            <div class="col_2-2" id="col2">

                <div class="flex_field">
                    <div class="field">
                        <label for="h_oil"><p>Hyd Oil:</p></label>
                        <input type="number" style="width:80%" name="h_oil" id="h_oil" />
                    </div>
                    <div class="field">
                        <label for="e_oil"><p>Engine Oil:</p></label>
                        <input type="number"  style="width:80%" name="e_oil" id="e_oil" />
                    </div>
                </div>
                <div class="flex_field">
                    <div class="field">
                        <label for="antifreeze"><p>Antifreeze:</p></label>
                        <input type="number"  style="width:80%" name="antifreeze" id="antifreeze" />
                    </div>
                    <div class="field">
                        <label for="def"><p>DEF:</p></label>
                        <input type="number"  style="width:80%" name="def" id="def" />
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
                                echo "<option value=\"{$unit['unit_id']}|{$unit['unit_type']}\">{$unit['unit_id']}</option>";
                            endforeach; 
                        ?>
                    </select>
                </div>

                <div class="flex_field" id="op_hours1_box" style="display:none">
                    <label for="op_hours1"><p>*Operator Hours:</p></label>
                    <input type="number" step="0.1" name="op_hours1" id="op_hours1"/>
                </div>

                <div class="flex_field" id="unit2_box" style="display:none">
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
                                echo "<option value=\"{$unit['unit_id']}|{$unit['unit_type']}\">{$unit['unit_id']}</option>";
                            endforeach; 
                        ?>
                    </select>
                </div>

                <div class="flex_field" id="op_hours2_box" style="display: none">
                    <label for="op_hours2"><p>*Operator Hours:</p></label>
                    <input type="number" step="0.1" name="op_hours2" id="op_hours2" />
                </div>

                <div class="flex_field" id="stems_box" style="display: none">
                    <label for="stems"><p>Stems:</p></label>
                    <input type="number" step="0.1" name="stems" id="stems" />
                </div>

                <div class="flex_field" id="meters_box" style="display: none">
                    <label for="meters"><p>Meters:</p></label>
                    <input type="number" step="0.1" name="meters" id="meters" />
                </div>

                <div class="flex_field" id="loads_box" style="display: none">
                    <label for="loads"><p>Loads:</p></label>
                    <input type="number" step="0.1" name="loads" id="loads" />
                </div>

                <div class="flex_field" id="decking_box" style="display: none">
                    <label for="decking"><p>Decking:</p></label>
                    <input type="number" step="0.1" name="decking" id="decking" />
                </div>

                <div class="flex_field" id="hoe_box" style="display: none">
                    <label for="hoe"><p>Hoe Chucking:</p></label>
                    <input type="number" step="0.1" name="hoe" id="hoe" />
                </div>

                <div class="flex_field" id="tops_box" style="display: none">
                    <label for="tops"><p>Tops Piling:</p></label>
                    <input type="number" step="0.1" name="tops" id="tops" />
                </div>

            </div>

            <div class="center-container">
                <div styles="flex:1">
                    <label for="comments"><p>Notes / Repairs/ Ticket # / etc.</p></label>
                    <textarea id="comments" name="comments" rows="4"></textarea>
                </div>
            </div>
            <?php if(isset($_SESSION['error'])){ 
			echo "<p class=\"error\">".$_SESSION['error']."</p>";
			unset($_SESSION['error']); 
			} ?>
        </div>

        <div class="btns">
            <button class="btn" type="submit">Submit</button>
            <button class="btn" type="button" onClick="closeForm()">Cancel</button>
        </div>

    </form>

    <script>

        //navigates back to time sheet records page without saving timesheet
        function closeForm(){
            window.location.href = 'view_timesheets.php';
        }

        //Dynamically adds appropriate additional fields based on unit1
        document.getElementById("unit1").addEventListener("change", function() {
            var selectedOption = this.value;
            var selector_box = document.getElementById("unit2_box");

            //resets options fields
            hideAllFields()

            //Calls function to make proper fields visable
            if(selectedOption !== ""){
                var parts = selectedOption.split('|');
                var type = parts[1];
                selector_box.style.display = 'flex';
                showDynamicFields(type);
                console.log(type);
            }

            //ensure secondary fields are not removed
            var selector = document.getElementById("unit2");
            var option = selector.options[selector.selectedIndex];
            var value = option.value;
            if(value !== ""){
                var parts = value.split('|');
                var type = parts[1];
                showDynamicFields(type);
                showOperatorHours();
            }
            
        });

        //Dynamically adds appropriate additional fields based on unit2
        document.getElementById("unit2").addEventListener("change", function() {
            var selectedOption = this.value;

            //resets options fields
            hideAllFields()

            //Calls function to make proper fields visable
            if(selectedOption !== ""){
                var parts = selectedOption.split('|');
                var type = parts[1];
                showDynamicFields(type);
            }

            //ensure primary fields are not removed
            var selector = document.getElementById("unit1");
            var option = selector.options[selector.selectedIndex];
            var value = option.value;
            if(value !== ""){
                var parts = value.split('|');
                var type = parts[1];
                showDynamicFields(type);
                showOperatorHours();
            }
            
        });

        //Changes display of optional fields to none
        function hideAllFields(){
            var f1 = document.getElementById('stems_box');
            f1.style.display = 'none';
            var f2 = document.getElementById('meters_box');
            f2.style.display = 'none';
            var f3 = document.getElementById('loads_box');
            f3.style.display = 'none';
            var f4 = document.getElementById('decking_box');
            f4.style.display = 'none';
            var f5 = document.getElementById('hoe_box');
            f5.style.display = 'none';
            var f6 = document.getElementById('tops_box');
            f6.style.display = 'none';
            var f7 = document.getElementById('op_hours1_box');
            f7.style.display = 'none';
          	f7.setAttribute("required", "false");
            var f8 = document.getElementById('op_hours2_box');
            f8.style.display = 'none';
          	f8.setAttribute("required", "false");
        }

        //changes display of relevant fields to flex absed on unit type
        function showDynamicFields(type){
            console.log(type.toLowerCase());

            if(type.toLowerCase() === "processor"){
                var f1 = document.getElementById('stems_box');
                f1.style.display = 'flex';
                var f2 = document.getElementById('meters_box');
                f2.style.display = 'flex';
            }
            else if(type.toLowerCase() === "loader"){
                var f3 = document.getElementById('loads_box');
                f3.style.display = 'flex';
                var f4 = document.getElementById('decking_box');
                f4.style.display = 'flex';
                var f5 = document.getElementById('hoe_box');
                f5.style.display = 'flex';
                var f6 = document.getElementById('tops_box');
                f6.style.display = 'flex';
            }
        }

        //Changes both operator hours fields display to flex
        function showOperatorHours(){
            var op1 = document.getElementById('op_hours1_box');
            op1.style.display = 'flex';
          	op1.setAttribute("required", "true");
            var op2 = document.getElementById('op_hours2_box');
            op2.style.display = 'flex';
          	op2.setAttribute("required", "true");
        }
    </script>

<?php include '../utility/footer.php'; ?>