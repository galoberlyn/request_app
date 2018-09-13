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


echo '<script id ="yeah" type="text/javascript">';
$itemsId = $_POST['save'];		
$request_id = $_POST['request_id'];
$error = false;

		$ismt = "SELECT description, rs_no FROM  itemsnotpo INNER JOIN request_slip ON request_slip_no = request_slip.id where itemsnotpo.id = ? ";
		$firstStmt = $conn -> prepare($ismt);
		$firstStmt -> bind_param("s" ,$itemsId);
		$firstStmt -> execute();
		$ismtArr = $firstStmt -> get_result() ->fetch_array(MYSQLI_NUM);


		if(isset($_POST['description'])){
			$desc1 = validateInput($_POST['description']);
			if($desc1 != false){
				$descStmt = "UPDATE itemsnotpo SET description=? where id=?";
				$secondStmt = $conn -> prepare($descStmt);
				$secondStmt -> bind_param("ss" ,$desc1,$itemsId);
				$secondStmt -> execute();

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

		if(isset($_POST['supplierNoPO'])){
			$supp = validateInput($_POST['supplierNoPO']);
			if($supp != false){
				$suppStmt = "UPDATE itemsnotpo SET supplier=? WHERE id=?";
				$thirdStmt = $conn -> prepare($suppStmt);
				$thirdStmt -> bind_param("ss" ,$supp,$itemsId);
				$thirdStmt -> execute();

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
			$qtyy = is_numeric($_POST['quantity']);

			if($qtyy){
				if(intval($_POST['quantity']) > 0){
					$quantys = intval($_POST['quantity']);
					$quantstmt = "UPDATE itemsnotpo SET quantity=? WHERE id=?";
					$fourthStmt = $conn -> prepare($quantstmt);
					$fourthStmt -> bind_param("ss" ,$quantys,$itemsId);
					$fourthStmt -> execute();
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


		if(isset($_POST['dateDel'])){
			$dateDel = validateInput($_POST['dateDel']);

			if($dateDel != false){
				if(validateDate($dateDel)){
					$currDate = date('Y-m-d');
	  				$givenDate = date($dateDel);

	  				if($givenDate > $currDate){
						$datedelstmt = "UPDATE itemsnotpo SET date_accomplished=? WHERE id=?";		
						$fourthStmt = $conn -> prepare($datedelstmt);
						$fourthStmt -> bind_param("ss" ,$dateDel,$itemsId);
						$fourthStmt -> execute();
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
				echo "alert('Invalid Input on the Date Delivered Field of Item: $ismtArr[0](Please enter data)');";
			}


		}

		if(isset($_POST['amount'])){
			 if(is_numeric($_POST['amount'])){
			 	if(floatval($_POST['amount']) > 0){
			 		$filteredAmount = round($_POST['amount'],2);

			 		$amtstmt = "UPDATE itemsnotpo SET amount=? WHERE id=?";
					$fifthStmt = $conn -> prepare($amtstmt);
					$fifthStmt -> bind_param("ss" ,$filteredAmount,$itemsId);
					$fifthStmt -> execute();
			 	}
			 	else{
			 		$error = true;
					echo "alert('Invalid Input on the Amount Field of Item: $ismtArr[0](The inputted unit price must be greater than 0)');";
			 	}
			 }
			 else{
			 	$error = true;
				echo "alert('Invalid Input on the Amount Field of Item: $ismtArr[0](Not a Number)');";
			 } 

		}

		if(isset($_POST['status'])){
			$status = htmlspecialchars($_POST['status'], ENT_QUOTES);
			$statstmt = "UPDATE itemsnotpo SET itemStatus=? WHERE id=?";
			$sixthStmt = $conn -> prepare($statstmt);
			$sixthStmt -> bind_param("ss" ,$status,$itemsId);
			$sixthStmt -> execute();
		}

		if(isset($_POST['remarks'])){
			$remrks = validateInput($_POST['remarks']);
			if($remrks != false){
				$remstmt = "UPDATE itemsnotpo SET remarks=? WHERE id=?";

				$seventhStmt = $conn -> prepare($remstmt);
				$seventhStmt -> bind_param("ss" ,$remrks,$itemsId);
				$seventhStmt -> execute();


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
		$totalInd = $_POST['totalIndex'] -1;
		if(!$error && $_POST['firstPartStat'] == 'noError' && $totalInd == $_POST['currIndex']){
					echo 'alert("Successfully Edited Request No: '.$ismtArr[1].'");
					
                    setTimeout(function() {location.href="view_details.php?request_id='. $request_id.'"},2000);</script>';
		}
		else{
			echo '</script>';
		}
 ?>
