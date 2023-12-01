<!--
    Author: Barbara Emke
    Date:   November 14, 2023
-->

<?php
session_start();
?>

<?php include '../utility/header.php'; ?>

    <link href="../utility/styles.css" rel="stylesheet" />

    <div class="content_box">
        <p class="larger">Welcome to the Timesheet Management Portal,</p>
        <p class="base">This is your hub for managing your work hours and submissions. Here, you can conveniently submit your daily work logs, review your timesheets, and access 
            important information.</p>
        <p class="base">We're here to make your workday easier and ensure everything is properly documented. Just log in with the credentials provided to submit 
            your timesheet.</p>
        <div class="center-container">
            <button class="btn" type="button" onclick="<?php echo 'openLoginForm()';  ?>">Log In</button>
        </div>
    </div>
    <div class="modal-container" id="login_form">
        <div class="form_popup">
            <form class="form_container" action="login.php" method="post">
                <p class="close_button" onclick="closeLoginForm()">X</p>
                <h3>Log In</h3>

                <div class="field">
                    <label for="email"><p>*Email</p></label>
                    <input type="email" placeholder="Email" name="email" id="email" required />
                </div>

                <div class="field">
                    <label for="pass"><p>*Password</p></label>
                    <input type="password" placeholder="Password" name="pass" id="pass" required></input>
                </div>
                
                <?php if(isset($_SESSION['login_error'])){ 
                echo "<script> document.getElementById(\"login_form\").style.display = \"block\"; </script>";
                echo "<p class=\"error\">".$_SESSION['login_error']."</p>";
                unset($_SESSION['login_error']); 
                } ?>
                <div class="center-container">
                    <button type="submit" class="btn" >Log In</button>
                </div>

                <p class="tip">Check your email for your login credentials. If you can not find it talk to management about getting your credentials set up.</p>
                
            </form>
        </div>
    </div>

    <script>
        //JS function to show login pop up form
        function openLoginForm() {
            var form = document.getElementById('login_form');
            if(form){
                form.style.display = 'block';
            }
        }

           //JS function to hide login pop up form
           function closeLoginForm() {
            var form = document.getElementById('login_form');
            if(form){
                form.style.display = 'none';
            }
        }
    </script>

<?php include '../utility/footer.php'; ?>