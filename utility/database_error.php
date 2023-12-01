<?php 	
/*
    Author: Barbara Emke
    Date:   November 14, 2023
*/
	session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

	include '../utility/loginRequired.php'; 
?>

<?php include '../utility/header.php'; ?>

<link href="../utility/styles.css" rel="stylesheet" />

    <main>
        <div class="content_box">
            <h1>Database Error</h1>
            <p class="hyperlink"><a href="<?php echo$_SERVER['HTTP_REFERER'];?>";>Back</a></p>
            <p>An error occurred while attempting to work with the database.</p>
            <p>Message: <?php echo $error_message; ?></p>
            <p>&nbsp;</p>
        </div>
    </main>

<?php include '../utility/footer.php'; ?>