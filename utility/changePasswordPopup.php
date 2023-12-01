<!--
    Author: Barbara Emke
    Date:   November 17, 2023
-->

    <!-- Popup form change users password -->
	<div class="modal-container" id="change_password_form">
		<div class="form_popup" style="height:550px">
			<form class="form-container" action="../utility/updatePassword.php" method="post">
				<p class="close_button" onclick="closePasswordForm()">X</p>
				<h3>Change Password</h3>

				<div class="field">
					<label for="old_pass"><p>Old Password:</p></label>
					<input type="password" name="old_pass" id="old_pass" required />
				</div>
				
				<div class="field">
					<label for="new_pass"><p>New Password:</p></label>
					<input type="password" name="new_pass" id="new_pass" required />
				</div>

				<div class="field">
					<label for="new_pass2"><p>New Password:</p></label>
					<input type="password" name="new_pass2" id="new_pass2" required />
				</div>

				<?php if(isset($_SESSION['change_error'])){ 
				echo "<script> document.getElementById(\"change_password_form\").style.display = \"block\"; </script>";
				echo "<p class=\"error\">".$_SESSION['change_error']."</p>";
				unset($_SESSION['change_error']); 
				} ?>   
				<div class="btns">
					<button type="submit" class="btn" id="submit">Change Password</button>
				</div>
			</form>
		</div>
	</div>
	<script>
      	function closePasswordForm(){
          	var form = document.getElementById('change_password_form');
          	form.style.display = "none";
        }
	</script>