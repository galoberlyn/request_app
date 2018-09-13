<?php
include "../shared/authorization.php";
include "../shared/connection.php";
if(isset($_POST['saveInfo'])){


		echo '<script type="text/javascript">
				alert("haha");
                </script>';

$reqID = $reqSlipArr['id'];

	if(isset($_POST['dateNeeded'])){
		$stmt = "UPDATE request_slip SET date_needed='$_POST[dateNeeded]', updated_at=NOW() WHERE id='$reqID'";
		mysqli_query($conn,$stmt) or die(mysqli_error($conn));

	}

	if(isset($_POST['purpose'])){
		$stmt = "UPDATE request_slip SET purpose='$_POST[purpose]', updated_at=NOW() WHERE id='$reqID'";
		mysqli_query($conn,$stmt) or die(mysqli_error($conn));

	}

	if(isset($_POST['slipStatus'])){
		$stmt = "UPDATE request_slip SET status='$_POST[slipStatus]', updated_at=NOW() WHERE id='$reqID'";
		mysqli_query($conn,$stmt) or die(mysqli_error($conn));

	}	

	if($reqSlipArr['type'] == 'PO'){

		if(isset($_POST['poNum'])){
			$stmt = "UPDATE purchase_order SET po_no='$_POST[poNum]' WHERE request_id='$reqID'";
			mysqli_query($conn,$stmt) or die(mysqli_error($conn));
		}

		if(isset($_POST['poDate'])){
			$stmt = "UPDATE purchase_order SET date_of_po='$_POST[poDate]' WHERE request_id='$reqID'";
			mysqli_query($conn,$stmt) or die(mysqli_error($conn));
		}

		if(isset($_POST['poSupp'])){
			$stmt = "UPDATE purchase_order SET supplier='$_POST[poSupp]' WHERE request_id='$reqID'";
			mysqli_query($conn,$stmt) or die(mysqli_error($conn));
		}
	}
	else{
		if(isset($_POST['careOF'])){
			$stmt = "UPDATE request_slip SET ConcernedOffice='$_POST[careOF]' WHERE request_id='$reqID'";
			mysqli_query($conn,$stmt) or die(mysqli_error($conn));
		}
	}


		echo '<script type="text/javascript">
                    setTimeout(function() {location.href="view_details.php?request_id='. $request_id.'"},0);
                    </script>';
}


?>