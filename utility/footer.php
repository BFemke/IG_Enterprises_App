<!--
    Author: Barbara Emke
    Date:   November 14, 2023
   --> 
   </main>
   <script>
      window.onload = function() {
        // Use setTimeout to ensure the alert is shown after a short delay
        setTimeout(function() {
            <?php if(isset($_SESSION['confirmation'])){ 
                echo "showAlert('{$_SESSION['confirmation']}');";
                unset($_SESSION['confirmation']);
            }?>
        }, 500);
        }
        function showAlert(message){
            alert(message);
        }
    </script>
    <footer id="base_footer" class="no-print">
		<p id="copyright">&copy;2023 IG Enterprises Ltd.<br/> All Rights Reserved </p>
	</footer>
</body>
	
</html>