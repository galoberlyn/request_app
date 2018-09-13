<?php
include '../shared/authorization.php';
include '../shared/connection.php';
// itemname 3 == full deliver
// itemname 2 == full cancel
if(isset($_POST['itemName2']) AND !empty($_POST['itemName2'])){
		echo $_POST['type2'];
	if($_POST['type2']=='ItemsNoPO'){
		$update = "UPDATE itemsnotpo set ItemStatus='Canceled' where request_slip_no=? AND description=?";
		$updaterslt=$conn->prepare($update);
		$updaterslt->bind_param('ss', $_POST['req_id2'], $_POST['itemName2']);
		$updaterslt->execute() or die($conn->error);

	}else if($_POST['type2']=='PO'){
		$check = "SELECT id from purchase_order where request_id  = ?";
		$check_r = $conn->prepare($check);
		$check_r->bind_param('i', $_POST['req_id2']);
		$check_r->execute();
		$checkarr2=$check_r->get_result()->fetch_array(MYSQLI_ASSOC);

		$update = "UPDATE itemspo set itemspostatus='Canceled' where poid=? AND description=?";
		$updaterslt=$conn->prepare($update);
		$updaterslt->bind_param('ss', $checkarr2['id'], $_POST['itemName2']);
		$updaterslt->execute() or die($conn->error);
		echo "Bat ayaw";

	}else if($_POST['type2']=='Service'){
		$update = "UPDATE services set status='Canceled' where requestID=? AND description=?";
		$updaterslt=$conn->prepare($update);
		$updaterslt->bind_param('ss', $_POST['req_id2'], $_POST['itemName2']);
		$updaterslt->execute() or die($conn->error);

	}else{
		echo 'error';
	}

}else{
	echo "walanaman";
}

if(isset($_POST['itemName3']) AND !empty($_POST['itemName3'])){
		echo $_POST['type3'];
	if($_POST['type3']=='ItemsNoPO'){
		$modify = "SELECT quantity from delivered_items where item_name=? AND rs_item_no=?";
		$modify_rslt=$conn->prepare($modify);
		$modify_rslt->bind_param('ss', $_POST['itemName3'], $_POST['req_id3']);
		$modify_rslt->execute();
		$modify_val=$modify_rslt->get_result()->fetch_array(MYSQLI_ASSOC);

		$update = "UPDATE itemsnotpo set ItemStatus='Delivered', qty_delivered_nopo=?, date_accomplished=now()  where request_slip_no=? AND description=?";
		$updaterslt=$conn->prepare($update);
		$updaterslt->bind_param('sss', $modify_val['quantity'], $_POST['req_id3'], $_POST['itemName3']);
		$updaterslt->execute() or die($conn->error);

		$update2="UPDATE delivered_items set qty_delivered=?, updated_at=now() where item_name=? AND rs_item_no=?";
		$update2rslt=$conn->prepare($update2);
		$update2rslt->bind_param('sss', $modify_val['quantity'],$_POST['itemName3'], $_POST['req_id3']);
		$update2rslt->execute() or die($conn->error);		
	}else if($_POST['type3']=='PO'){
		$check = "SELECT id from purchase_order where request_id  = ?";
		$check_r = $conn->prepare($check);
		$check_r->bind_param('i', $_POST['req_id3']);
		$check_r->execute();
		$checkarr2=$check_r->get_result()->fetch_array(MYSQLI_ASSOC);

		$update = "UPDATE itemspo set itemspostatus='Delivered', date_complete=now() where poid=? AND description=?";
		$updaterslt=$conn->prepare($update);
		$updaterslt->bind_param('ss', $checkarr2['id'], $_POST['itemName3']);
		$updaterslt->execute() or die($conn->error);

		$modify = "SELECT quantity from delivered_items where item_name=? AND rs_item_no=?";
		$modify_rslt=$conn->prepare($modify);
		$modify_rslt->bind_param('ss', $_POST['itemName3'], $_POST['req_id3']);
		$modify_rslt->execute();
		$modify_val=$modify_rslt->get_result()->fetch_array(MYSQLI_ASSOC);

		$update2="UPDATE delivered_items set qty_delivered=?, updated_at=now() where rs_item_no=?";
		$update2rslt=$conn->prepare($update2);
		$update2rslt->bind_param('ss', $modify_val['quantity'], $_POST['req_id3']);
		$update2rslt->execute() or die($conn->error);	

		$update3="UPDATE itemspo set qty_delivered_po=? where poid =? and description=?";
		$update3r=$conn->prepare($update3);
		var_dump($conn->error);
		$update3r->bind_param('iis', $modify_val['quantity'], $checkarr2['id'], $_POST['itemName3']);
		$update3r->execute() or die($conn->error);	

	}else if($_POST['type3']=='Service'){
		$update = "UPDATE services set status='Delivered', date_completed=now() where requestID=? AND description=?";
		$updaterslt=$conn->prepare($update);
		var_dump($conn->error);
		$updaterslt->bind_param('ss', $_POST['req_id3'], $_POST['itemName3']);
		$updaterslt->execute() or die($conn->error);

		$modify = "SELECT quantity from delivered_items where item_name=? AND rs_item_no=?";
		$modify_rslt=$conn->prepare($modify);
		$modify_rslt->bind_param('ss', $_POST['itemName3'], $_POST['req_id3']);
		$modify_rslt->execute();
		$modify_val=$modify_rslt->get_result()->fetch_array(MYSQLI_ASSOC);

		$update2="UPDATE delivered_items set qty_delivered=?, updated_at=now() where rs_item_no=?";
		$update2rslt=$conn->prepare($update2);
		$update2rslt->bind_param('ss', $modify_val['quantity'], $_POST['req_id3']);
		$update2rslt->execute() or die($conn->error);		

	}else{
		echo 'walanaman';
	}

}

?>