 <?php
session_start();

if(isset($_SESSION['username'])){
	session_unset();
 	session_destroy();
 	header("Location: index.php");
}
else {
    die('<h1 class ="redirect">Unauthorized user... please log in to continue</h1>
    		<script type="text/javascript">
    		setTimeout(function() {location.href="index.php"},1200);
    		</script>');
}
 
 ?>
