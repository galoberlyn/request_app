<?php
 session_start();
 require('connection.php');

if(!isset($_SESSION['username'])) {
                die('<h1 class ="redirect">Unauthorized user... please log in to continue</h1>
                    <script type="text/javascript">
                    setTimeout(function() {location.href="../index.php"},1200);
                    </script>');
}

?>