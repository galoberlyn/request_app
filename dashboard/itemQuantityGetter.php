<?php
include "../shared/authorization.php";
include "../shared/connection.php";
	if(isset($_POST['itemName'])){

		if($_POST['type'] == 'PO'){
			$stmt = "SELECT quantity, qty_delivered_po from purchase_order INNER JOIN itemspo ON  id = poid where request_id =? AND description = ?";
	 		$stmtQuery = $conn -> prepare($stmt);
	 		$stmtQuery->bind_param('ss',$_POST['req_id'], $_POST['itemName']);
		  	$stmtQuery->execute();
		  	$stmtArray = $stmtQuery->get_result()->fetch_array(MYSQLI_BOTH);


		  	echo $stmtArray[0] - $stmtArray[1];
	  	}
	  	else if($_POST['type'] == 'ItemsNoPO'){
	  		$stmt = "SELECT quantity, qty_delivered_nopo from itemsnotpo where request_slip_no =? AND description = ?";
	 		$stmtQuery = $conn -> prepare($stmt);
	 		$stmtQuery->bind_param('ss',$_POST['req_id'], $_POST['itemName']);
		  	$stmtQuery->execute();
		  	$stmtArray = $stmtQuery->get_result()->fetch_array(MYSQLI_BOTH);

		  	echo $stmtArray[0] - $stmtArray[1];
	  	}else if($_POST['type']=='Service'){
	  		echo '.';
	  	
	  	}
	}

?>