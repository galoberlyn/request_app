<?php
	try{
		if($conn = mysqli_connect("localhost", "root","","scis_requisition_db")){
			// echo "success";
		}else{
			// throw new Exception('Unable to connect');
			header("Location: shared/sorry.html");
		}
	}catch(Exception $e){
	
	}


?>