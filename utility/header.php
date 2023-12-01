<?php 
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/


if(isset($_SESSION['user'])){
   $name = $_SESSION['user']['first_name']." ".$_SESSION['user']['last_name'];
}

?>

<html lang="en">
<head>
   <meta charset="utf-8" />
   <meta name="keywords" content="logging, IG, timesheets, employee, admin" />
   <meta name="viewport" content= "width= device-width, initial-scale=1" /> 

   <title>IG Enterprises Ltd.</title>

</head>

<body id="base_body">
	<header id="myHeader">
		<div id="logo">
			<img src="../images/ig_logo.jpg" alt="logo"/>
		</div>
        <h1 class="header_title">IG Enterprises Ltd.</h1>
        <div class="user_info no-print">
            <?php if(isset($name)){
                echo "<p class=\"name\">{$name}</p>";
                echo "<p class=\"hyperlink\" onClick=\"openPasswordForm()\">Change Password</p>";
                echo "<form class=\"form-container\" action=\"../utility/logout.php\" method=\"POST\">";
                echo "<button class=\"logout-btn\" type=\"submit\">Logout</button>";
                echo "</form>";

                //inserts script for changing password
                echo "<script>
                        function openPasswordForm(){
                            const popup = document.getElementById(\"change_password_form\");
                            popup.style.display = \"block\";
                        }
                        function closeForm(){
                            const popup = document.getElementById(\"change_password_form\");
                            popup.style.display = \"none\";
                        }
                    </script>";
            }
            ?>
        </div>
        <?php if(isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == TRUE) : ?>
                <img  class="no-print" id="menu" src="../images/menu_icon.png" alt="menu" onmouseover="toggleMenu()" onmouseout="toggleMenu()"/>
            
                <nav class="horizontal no-print" id="horizontal" onmouseover="toggleMenu()" onmouseout="toggleMenu()">
                    <ul class="mainmenu no-print" id="mainmenu">
                        <li class="no-print"><a href="../dashboard/view_dashboard.php">Home</a></li>
                        <li class="no-print"><a href="../form_values/view_form_values.php">Form Inputs</a></li>
                        <li class="no-print"><a href="../companies/view_companies.php">Companies</a></li>
                        <li class="no-print"><a href="../employees/view_employees.php">Employees</a></li>
                        <li class="no-print"><a href="../payroll/view_payroll.php">Payroll</a></li>
                        <li class="no-print"><a href="../event_log/view_event_log.php">Event Logs</a></li>
                        <li class="no-print"><a href="../user_timesheets/view_timesheets.php">Submit Timesheet</a></li>
                    </ul>
                </nav>
        <?php endif; ?>
      	<script>
          	function toggleMenu() {
                var dropdown = document.getElementById("horizontal");
                dropdown.classList.toggle("show");
             }
      	</script>
        
    </header>
    <main id="main">
    <?php include '../utility/changePasswordPopup.php'; ?>
