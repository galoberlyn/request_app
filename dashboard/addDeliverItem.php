<?php
include "../shared/authorization.php";
include "../shared/connection.php";

if(isset($_POST['itemName']) AND !empty($_POST['itemQuant']) AND !empty($_POST['itemDelDate'])){
	if($_POST['itemQuant'] > 0){
		$request_id = $_POST['req_id'];
		$request_type = $_POST['reqType'];

		$itemDesc = $_POST['itemName'];
		$itemDeliveredQuantity = $_POST['itemQuant'];
		$itemDeliveryDate = $_POST['itemDelDate'];
	


		if($request_type == 'PO'){
			$overallStatus = "Pending";
			$poIDstmt = "Select quantity,poid,qty_delivered_po from purchase_order INNER JOIN itemspo ON id = poid where request_id = ? AND description = ?";
	 		$poIDstmtQuery = $conn -> prepare($poIDstmt);
	 		$poIDstmtQuery->bind_param('ss', $request_id,$itemDesc);
		  	$poIDstmtQuery->execute();
		  	$poIDstmtArray = $poIDstmtQuery->get_result()->fetch_array(MYSQLI_NUM);
		  	

		  	$totQuant = $poIDstmtArray[0];
		  	$itemDeliveredQuantity += $poIDstmtArray[2];
		  	$poID = $poIDstmtArray[1];

		  	if($itemDeliveredQuantity > $totQuant){	
		  		echo json_encode(array("state"=>"error","msg"=>"alert('The delivered quantity entered for item: ".$itemDesc.", is greater than the expected quantity')"));
		  	}
		  	else{

			  	$stmt = "UPDATE itemspo SET qty_delivered_po = ? where poid = ? AND description = ?";
		 		$stmtQuery = $conn -> prepare($stmt);
		 		$stmtQuery->bind_param('sss', $itemDeliveredQuantity, $poID, $itemDesc);
			  	$stmtQuery->execute();


			  	$stmt1 = "UPDATE delivered_items SET qty_delivered = ?, `date` = ?, updated_at = NOW() where rs_item_no = ? AND item_name = ?";
		 		$stmt1Query = $conn -> prepare($stmt1);
		 		$stmt1Query->bind_param('ssss',$itemDeliveredQuantity, $itemDeliveryDate, $poID, $itemDesc);
			  	$stmt1Query->execute();

			  	if($itemDeliveredQuantity == $totQuant){
			  		$stmt2 = "UPDATE itemspo SET itemspostatus = 'Delivered', date_complete=now() where poid = ? AND description = ?";
			 		$stmt2Query = $conn -> prepare($stmt2);
			 		$stmt2Query->bind_param('ss', $poID, $itemDesc);
				  	$stmt2Query->execute();


				  	$checkDeliveredPO = "SELECT description from itemspo where poid = ?  AND itemspostatus = 'Pending' ";
					$checkDeliveredPOQuery = $conn -> prepare($checkDeliveredPO);
					$checkDeliveredPOQuery -> bind_param('s', $poID);
					$checkDeliveredPOQuery -> execute();
					$checkDeliveredPOArr = $checkDeliveredPOQuery -> get_result() -> fetch_array(MYSQLI_NUM);

					if(count($checkDeliveredPOArr) == 0 ){
						$reqDel1 = "UPDATE request_slip SET status = 'Completed' where  id = ?";
				  		$reqDel1Query = $conn -> prepare($reqDel1);
				  		$reqDel1Query -> bind_param('s', $request_id);
				  		$reqDel1Query -> execute();
				  		$overallStatus = "Completed";
					}

			  	}

			  	$showTab = "SELECT description, quantity, qty_delivered_po, itemspostatus from itemspo where poid = ? AND description = ? AND qty_delivered_po > 0";
			  	$showTabQ = $conn -> prepare($showTab);
			  	$showTabQ -> bind_param('ss',$poID,$itemDesc);
			  	$showTabQ -> execute();
			  	$showTabArr = $showTabQ -> get_result() -> fetch_array(MYSQLI_NUM);
			  	echo json_encode(array("itemName" => $showTabArr[0], "totalQuantity" => $showTabArr[1], "QuantityDelivered" => $showTabArr[2],"ItemStatus" => $showTabArr[3], "RequestStatus" => $overallStatus));
		  	}
		}
		else if($request_type == 'ItemsNoPO'){
			$overallStatus = "Pending";

			$noPOIDstmt = "SELECT quantity,qty_delivered_nopo from itemsnotpo where request_slip_no = ?  AND description = ?  ";
			$noPOIDstmtQuery = $conn -> prepare($noPOIDstmt);
			$noPOIDstmtQuery -> bind_param('ss', $request_id, $itemDesc);
			$noPOIDstmtQuery -> execute();
			$noPOIDArray = $noPOIDstmtQuery -> get_result() -> fetch_array(MYSQLI_NUM);

			$itemDeliveredQuantity += $noPOIDArray[1];
			$totalQuantity = $noPOIDArray[0];

			if($itemDeliveredQuantity > $totalQuantity){
		  		echo json_encode(array("state"=>"error","msg"=>"alert('The delivered quantity entered for item: ".$itemDesc.", is greater than the expected quantity')"));
			}
			else{


				$upitemsNoPO = "UPDATE itemsnotpo SET qty_delivered_nopo = ?, date_accomplished=now() where request_slip_no = ? AND description = ?";
				$upitemsNoPOQuery = $conn -> prepare($upitemsNoPO);
				$upitemsNoPOQuery -> bind_param('sss', $itemDeliveredQuantity, $request_id, $itemDesc);
				$upitemsNoPOQuery -> execute();

				$upDelItems = "UPDATE delivered_items SET qty_delivered = ?, `date` = ?, updated_at = NOW() where rs_item_no = ? AND item_name = ?";
		 		$upDelItemsQuery = $conn -> prepare($upDelItems);
		 		$upDelItemsQuery->bind_param('ssss',$itemDeliveredQuantity, $itemDeliveryDate, $request_id, $itemDesc);
			  	$upDelItemsQuery->execute();

			  	if($itemDeliveredQuantity == $totalQuantity){
			  		$upStat = "UPDATE itemsnotpo SET itemStatus = 'Delivered' where  request_slip_no = ? AND description = ?";
			  		$upStatQuery = $conn -> prepare($upStat);
			  		$upStatQuery -> bind_param('ss', $request_id, $itemDesc);
			  		$upStatQuery -> execute();

			  		$checkDelivered = "SELECT description from itemsnotpo where request_slip_no = ?  AND itemStatus = 'Pending'  ";
					$checkDeliveredQuery = $conn -> prepare($checkDelivered);
					$checkDeliveredQuery -> bind_param('s', $request_id);
					$checkDeliveredQuery -> execute();
					$checkDeliveredArr = $checkDeliveredQuery -> get_result() -> fetch_array(MYSQLI_NUM);

					if(count($checkDeliveredArr) == 0){
						$reqDel = "UPDATE request_slip SET status = 'Completed' where  id = ?";
				  		$reqDelQuery = $conn -> prepare($reqDel);
				  		$reqDelQuery -> bind_param('s', $request_id);
				  		$reqDelQuery -> execute();
				  		$overallStatus = "Completed";

					}

			  	}

			  	$showRes = "SELECT description, quantity, qty_delivered_nopo, itemStatus from itemsnotpo where request_slip_no = ? AND description = ?";
		  		$showResQ = $conn -> prepare($showRes);
		  		$showResQ -> bind_param('ss', $request_id, $itemDesc);
		  		$showResQ -> execute();

		  		$showResArr = $showResQ -> get_result() -> fetch_array(MYSQLI_NUM);
		  		echo json_encode(array("itemName" => $showResArr[0], "totalQuantity" => $showResArr[1], "QuantityDelivered" => $showResArr[2],"ItemStatus" => $showResArr[3],"RequestStatus" => $overallStatus));

			}
		}
	}
	else{
		echo json_encode(array("state"=>"error","msg"=>"alert('The delivered quantity entered for item: ".$_POST['itemName']."is equal to or less than 0')"));
	}
}
else{

		echo json_encode(array("state"=>"error","msg"=>"alert('Please Fill up all the necessary information')"));
}
?>
