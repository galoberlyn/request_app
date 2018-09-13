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
$error = false;
$itemsId = $_POST['save'];		
$request_id = $_POST['request_id'];
		$ismt = "SELECT description,rs_no FROM  services INNER JOIN request_slip on requestID = id WHERE idServices = ?";
		$firstStmt = $conn -> prepare($ismt); 
		$firstStmt -> bind_param("s" ,$itemsId);
		$firstStmt -> execute();
		$ismtArr = $firstStmt -> get_result() ->fetch_array(MYSQLI_NUM);

		if(isset($_POST['description'])){
			$filteredInput = validateInput($_POST['description']);
			if($filteredInput != false){
				$descstmt = "UPDATE services SET description=? WHERE idServices=?";
				
				$secondStmt = $conn -> prepare($descstmt); 
				$secondStmt -> bind_param("ss" ,$filteredInput,$itemsId);
				$secondStmt -> execute();

				if(strpos(mysqli_error($conn), 'too long')){
      				$error = true;
      				echo "alert('Invalid Input on the Service Name Field of Service: $ismtArr[0](Data entered is too long)');";
      			}
			}
			else{
	  			$error = true;
	  			echo "alert('Invalid Input on the Service Name Field of Service: $ismtArr[0](Please enter data');";
			}
		}

		if(isset($_POST['serviceprovider'])){
			$filteredInput = validateInput($_POST['serviceprovider']);

			if($filteredInput != false){
				$spstmt = "UPDATE services SET service_provider=? WHERE idServices=?";

				$thirdStmt = $conn -> prepare($spstmt); 
				$thirdStmt -> bind_param("ss" ,$filteredInput,$itemsId);
				$thirdStmt -> execute();


				if(strpos(mysqli_error($conn), 'too long')){
      				$error = true;
      				echo "alert('Invalid Input on the Service Provider Field of Service: $ismtArr[0](Data entered is too long)');";
      			}
			}
			else{
				$error = true;
	  			echo "alert('Invalid Input on the Service Provider Field of Service: $ismtArr[0](Please enter data)');";
			}


		}


		if(isset($_POST['dateComp']) && $_POST['dateComp'] != '' && $_POST['dateComp'] != ' ' ){
			if(validateDate($_POST['dateComp'])){
				$currPODate = date('Y-m-d');
				$givenPODate = date($_POST['dateComp']);
				if($givenPODate > $currPODate){
					$dateCmpstmt = "UPDATE services SET date_completed =? WHERE idServices=?";
					$fourthStmt = $conn -> prepare($dateCmpstmt); 
					$fourthStmt -> bind_param("ss" ,$_POST['dateComp'],$itemsId);
					$fourthStmt -> execute();

				}
				else{
					$error = true;
		  			echo "alert('Invalid Input on the Date Completed Field of Service: $ismtArr[0](The inputted must be greater than the date today)');";
				}
			}
			else{
				echo "alert('Invalid Input on the Date Completed Field of Service: $ismtArr[0](Date format must be Y-m-d)');";
			}

		}
		if(isset($_POST['theTime'])){
			$stmt = "UPDATE services SET `time`='$_POST[theTime]' WHERE idServices='$itemsId'";
			mysqli_query($conn, $stmt);

		}


		if(isset($_POST['status'])){
			$sts = htmlspecialchars($_POST['status'], ENT_QUOTES);
			$statstmt = "UPDATE services SET status=? WHERE idServices=?";

			$fifthStmt = $conn -> prepare($statstmt); 
			$fifthStmt -> bind_param("ss" ,$sts,$itemsId);
			$fifthStmt -> execute();
			
		}

		if(isset($_POST['remarks'])){
			$filteredInput = validateInput($_POST['remarks']);

			if($filteredInput != false){
				$remstmt = "UPDATE services SET remarks=? WHERE idServices=?";


				$sixthStmt = $conn -> prepare($remstmt); 
				$sixthStmt -> bind_param("ss" ,$filteredInput,$itemsId);
				$sixthStmt -> execute();

				if(strpos(mysqli_error($conn), 'too long')){
      				$error = true;
      				echo "alert('Invalid Input on the Remarks Field of Service: $ismtArr[0](Data entered is too long)');";
      			}
			}
			else{
				$error = true;
	  			echo "alert('Invalid Input on the Remarks Field of Service: $ismtArr[0](Only White Spaces are Detected)');";
			}

			
		}
		$totalInd = $_POST['totalIndex'] -1;
		if(!$error && $_POST['firstPartStat'] == 'noError' && $totalInd == $_POST['currIndex']){
			echo 'alert("Successfully Request No: '.$ismtArr[1].' ");
                    setTimeout(function() {location.href="view_details.php?request_id='. $request_id.'"},0);
                    </script>';
        }
        else{
        	echo '</script>';
        }
 ?>