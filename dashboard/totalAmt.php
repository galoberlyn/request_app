<?php
include '../shared/connection.php';
include '../shared/authorization.php';

if(isset($_POST['total']) && isset($_POST['request_id'])){
	$tot = $_POST['total'];
	$reqId = $_POST['request_id'];
	if(is_numeric($tot)){
		$tootalstmt = "UPDATE purchase_order SET totalamt=? WHERE request_id=?";
		$querS = $conn -> prepare($tootalstmt);
		$querS -> bind_param("ss",$tot,$reqId);
		$querS -> execute();
		echo "Nice Job";
	}
}



?>