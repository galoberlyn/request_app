<?php
include '../shared/connection.php';
include '../shared/authorization.php';

function validateInput($input){
	$input = trim($input);
	if(!empty($input)){
		$input = htmlspecialchars($input, ENT_QUOTES);
		return $input;
	}
	else{
		return false;
	}
	
}

function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

echo "<script id = 'yeah'>";
$itemsId = $_POST['save'];		
$request_id = $_POST['request_id'];
$error = false;
		$ismt = "SELECT description,rs_no FROM itemspo INNER JOIN purchase_order ON poid = purchase_order.id INNER JOIN request_slip ON request_id = request_slip.id  where iditemspo = ?";
		$fixedStmt = $conn -> prepare($ismt);
		$fixedStmt -> bind_param("s",$itemsId);
		$fixedStmt -> execute();
		$ismtArr = $fixedStmt -> get_result() -> fetch_array(MYSQLI_NUM);

                    
		if(isset($_POST['description'])){
			$desc = validateInput($_POST['description']);

			if($desc != false){
				$stmt = "UPDATE itemspo SET description=? WHERE iditemspo=?";
				$frstStmt = $conn -> prepare($stmt);
				$frstStmt -> bind_param("ss",$desc,$itemsId);
				$frstStmt -> execute();

				if(strpos(mysqli_error($conn), 'too long')){
      				$error = true;
      				echo "alert('Invalid Input on the Item Name Field of Item: $ismtArr[0](Data entered is too long)');";
      			}
			}
			else{
				$error = true;
	  			echo "alert('Invalid Input on the Item Name Field of Item: $ismtArr[0](Please enter data)');";
			}
			
		}

		if(isset($_POST['spo'])){
			$spo = validateInput($_POST['spo']);

			if($spo != false){
				$spoStmt = "UPDATE itemspo SET supplier_po=? WHERE iditemspo=?";
				$scndStmt = $conn -> prepare($spoStmt);
				$scndStmt -> bind_param("ss",$spo,$itemsId);
				$scndStmt -> execute();

				if(strpos(mysqli_error($conn), 'too long')){
      				$error = true;
      				echo "alert('Invalid Input on the Supplier Field of Item: $ismtArr[0](Data entered is too long)');";
      			}

			}
			else{
				$error = true;
				echo "alert('Invalid Input on the Supplier Field of Item: $ismtArr[0](Please enter data)');";
			}
			
		}

		if(isset($_POST['quantity'])){
			$qty = is_numeric($_POST['quantity']);
			if($qty){
				if(intval($_POST['quantity']) > 0){
						$quantStmt = "UPDATE itemspo SET quantity=? WHERE iditemspo=?";
						$thrdStmt = $conn -> prepare($quantStmt);
						$thrdStmt -> bind_param("ss",$_POST['quantity'],$itemsId);
						$thrdStmt -> execute();
				}
				else{
					$error = true;
					echo "alert('Invalid Input on the Quantity Field of Item: $ismtArr[0](Input must be greater than 0)');";
				}

			}
			else{
				$error = true;
				echo "alert('Invalid Input on the Quantity Field of Item: $ismtArr[0](Not a Number)');";
			}


		}

		if(isset($_POST['location'])){
			$loc = validateInput($_POST['location']);

			if($loc != false){
				$locStmt = "UPDATE itemspo SET Location=? WHERE iditemspo=?";
				$fourthStmt = $conn -> prepare($locStmt);
				$fourthStmt -> bind_param("ss",$loc,$itemsId);
				$fourthStmt -> execute();

				if(strpos(mysqli_error($conn), 'too long')){
      				$error = true;
      				echo "alert('Invalid Input on the Location Field of Item: $ismtArr[0](Data entered is too long)');";
      			}
			}
			else{
				$error = true;
				echo "alert('Invalid Input on the Location Field of Item: $ismtArr[0](Please enter data)');";
			}
			
		}

		if(isset($_POST['unitPrice'])){

			$price = is_numeric($_POST['unitPrice']);
			if($price){
				if(floatval($_POST['unitPrice']) > 0){
					$filteredPrice = round($_POST['unitPrice'],2);
					$prStmt = "UPDATE itemspo SET unitprice=? WHERE iditemspo=?";
					$fifthStmt = $conn -> prepare($prStmt);
					$fifthStmt -> bind_param("ss",$filteredPrice,$itemsId);
					$fifthStmt -> execute();
				}
				else{
					$error = true;
					echo "alert('Invalid Input on the Unit Price Field of Item: $ismtArr[0](The inputted unit price must be greater than 0)');";
				}

			}
			else{
				$error = true;
				echo "alert('Invalid Input on the Unit Price Field of Item: $ismtArr[0](Not a Number)');";
			}
			
			
		}

		if(isset($_POST['status'])){
			$status = htmlspecialchars($_POST['status'], ENT_QUOTES);
			$statStmt = "UPDATE itemspo SET itemspostatus=? WHERE iditemspo=?";
			$fifthStmt = $conn -> prepare($statStmt);
			$fifthStmt -> bind_param("ss",$status,$itemsId);
			$fifthStmt -> execute();
		}

		if(isset($_POST['remarks'])){
			$rmrks = validateInput($_POST['remarks']);

			if($rmrks != false){
				$remStmt = "UPDATE itemspo SET remarks=? WHERE iditemspo=?";
				$sixthStmt = $conn -> prepare($remStmt);
				$sixthStmt -> bind_param("ss",$rmrks,$itemsId);
				$sixthStmt -> execute();
				
				if(strpos(mysqli_error($conn), 'too long')){
      				$error = true;
      				echo "alert('Invalid Input on the Remarks Field of Item: $ismtArr[0](Data entered is too long)');";
      			}	
			}
			else{
				$error = true;
				echo "alert('Invalid Input on the Remarks Field of Item: $ismtArr[0](Please enter data)');";
			}
			
		}

		if(isset($_POST['dateDeli'])){
			$dateDel = validateInput($_POST['dateDeli']);
			if($dateDel != false){
				if(validateDate($dateDel)){
					$currDate = date('Y-m-d');
	  				$givenDate = date($dateDel);

	  				if($givenDate > $currDate){
	  					$deliStmt = "UPDATE itemspo SET date_complete=? WHERE iditemspo=?";
						$seventhStmt = $conn -> prepare($deliStmt);
						$seventhStmt -> bind_param("ss",$dateDel,$itemsId);
						$seventhStmt -> execute();
	  				}
	  				else{
	  					$error = true;
	  					echo "alert('Invalid Input on the Date Delivered Field of Item: $ismtArr[0](Date Inputted must be greater than the date today)');";
	  				}
				}
				else{
					$error = true;
					echo "alert('Invalid Input on the Date Delivered Field of Item: $ismtArr[0](Date format must be Y-m-d)');";
				}

				
			}
			else{
				$error = true;
				echo "alert('Invalid Input on the Date Delivered Field of Item: $ismtArr[0](Please Input date!)');";
			}

		}

		if(isset($_POST['quantity']) && isset($_POST['unitPrice'])){
			$quantity = htmlspecialchars($_POST['quantity'], ENT_QUOTES);
			$uPrice = htmlspecialchars($_POST['unitPrice'], ENT_QUOTES); 

			if(is_numeric($quantity) && is_numeric($uPrice)){
				$yeahAmt = $quantity * $uPrice;
				$totalAmt = round($yeahAmt, 2);
				$totStmt = "UPDATE itemspo SET amount=? WHERE iditemspo=?";
				$eightStmt = $conn -> prepare($totStmt);
				$eightStmt -> bind_param("ss",$totalAmt,$itemsId);
				$eightStmt -> execute();
			}
		}
		$totalInd = $_POST['totalIndex'] -1;
		if(!$error && $_POST['firstPartStat'] == 'noError' && $totalInd == $_POST['currIndex']){
			echo 'alert("Successfully Request No:'.$ismtArr[1].'");
            setTimeout(function() {window.location.href="view_details.php?request_id='. $request_id.'"},0);
            </script>';
		}
		else{
			echo '</script>';
		}


 
 ?>