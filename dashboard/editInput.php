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

if(isset($_POST['request_id'])){
$error = false;
$type = $_POST['type'];

echo "<script id ='yeah' script>";

  $reqID = $_POST['request_id'];

  if(isset($_POST['dateNeeded'])){
  		$validatedInput = validateInput($_POST['dateNeeded']);
	  	if($validatedInput != false){
	  		if($validatedInput == 'ASAP' || validateDate($validatedInput)){
	  			$currDate = date('Y-m-d');
	  			$givenDate = date($validatedInput);

	  			
	  				$dNeedstmt = "UPDATE request_slip SET date_needed=?, updated_at=NOW() WHERE id=?";
	   	 			$myFirstStmt = $conn -> prepare($dNeedstmt);
	   	 			$myFirstStmt -> bind_param("ss",$validatedInput ,$reqID);
					$myFirstStmt -> execute();
	   	 		
	  		}
	  		else{
	  			$error = true;
	  			echo "alert('Invalid Input on the Date Needed Field(Date format must be Y-m-d or ASAP)');";
	  		}


	  	}
	  	else{
	  		$error = true;
	  		echo "alert('Invalid Input on the Date Needed Field (Please input the date!)');";
	  	}


  }

  if(isset($_POST['theTimeNeeded'])){
    $checker = validateInput($_POST['theTimeNeeded']);

    if($checker != false){
      $timeSplitted = explode(":",$_POST['theTimeNeeded']);
      $timeFinal;
      if($timeSplitted[0] >= 12){
              if($timeSplitted[0] > 12){
                $timeSplitted[0] -= 12;
          }
              $timeFinal = $timeSplitted[0].':'.$timeSplitted[1]." PM";

      }
      else{
              if($timeSplitted[0] == 0){
                $timeSplitted[0] = 12;
                $timeFinal = $timeSplitted[0].':'.$timeSplitted[1]." AM";
        }
        else{
              $timeFinal = $timeSplitted[0].':'.$timeSplitted[1]." AM";
        }
        
      }
      $time_stmt = "UPDATE request_slip SET time_needed=?, updated_at=NOW() WHERE id=?";
 		$mySecondStmt = $conn -> prepare($time_stmt);
		$mySecondStmt -> bind_param("ss",$timeFinal ,$reqID);
		$mySecondStmt -> execute();
    }
    else{
      $error = true;
      echo "alert('Invalid Input on the Time Needed Field(No time indicated)');";
    }
}

  if(isset($_POST['purpose'])){

  	$filteredInput = validateInput($_POST['purpose']);


  	if($filteredInput != false){
    	$purp_stmt = "UPDATE request_slip SET purpose=?, updated_at=NOW() WHERE id=?";
    	$myThirdStmt = $conn -> prepare($purp_stmt);
		$myThirdStmt -> bind_param("ss",$filteredInput ,$reqID);
		$myThirdStmt -> execute();

      	if(strpos(mysqli_error($conn), 'too long')){
      		$error = true;
      		echo "alert('Invalid Input on the Purpose Field(Data entered is too long)');";
      	}
    }
    else{
    	$error = true;
	  	echo "alert('Invalid Input on the Purpose Field(Only White Spaces Detected)');";
    }
  }

  if(isset($_POST['slipStatus'])){
    $slips_stmt = "UPDATE request_slip SET status=?, updated_at=NOW() WHERE id=?";
	
	$myThirdStmt = $conn -> prepare($slips_stmt);
	$myThirdStmt -> bind_param("ss",$_POST['slipStatus'] ,$reqID);
	$myThirdStmt -> execute();
  } 

  if($type == 'PO'){


    if(isset($_POST['poNum'])){
      $filteredInputPONum = validateInput($_POST['poNum']);
      if($filteredInputPONum != false){
      	$ponum_stmt = "UPDATE purchase_order SET po_no=? WHERE request_id=?";
  		
  		$myFourthStmt = $conn -> prepare($ponum_stmt);
		$myFourthStmt -> bind_param("ss",$filteredInputPONum ,$reqID);
		$myFourthStmt -> execute();

      	if(strpos(mysqli_error($conn), 'too long')){
      		$error = true;
      		echo "alert('Invalid Input on the Purchase Number Field(Data entered is too long)');";
      	}
      	
      }
      else{
		$error = true;
	  	echo "alert('Invalid Input on the Purchase Order Number Field(Please enter data)');";
      }

    }

    if(isset($_POST['poDate'])){
      $filteredInputPODate = validateInput($_POST['poDate']);

      if($filteredInputPODate != false){

      	if(validateDate($filteredInputPODate)){

      $currPODate = date('Y-m-d');
			$givenPODate = date($filteredInputPODate);

			if($givenPODate > $currPODate){
	      		$poDate_stmt = "UPDATE purchase_order SET date_of_po=? WHERE request_id=?";
	      		
	      		$myFifthStmt = $conn -> prepare($poDate_stmt);
				$myFifthStmt -> bind_param("ss",$filteredInputPODate ,$reqID);
				$myFifthStmt -> execute();
	 		}
	 		else{
				$error = true;
				echo "alert('Invalid Input on the Purchase Order Date Field(Date Inputted must be greater than the date today)');";
	 		}

      	}
      	else{
      		$error = true;
	  		echo "alert('Invalid Input on the Purchase Order Date Field(Date format must be Y-m-d or ASAP)');";
      	}

      }
      else{
      	$error = true;
	  	echo "alert('Invalid Input on the Purchase Order Date Field(Please Input data)');";
      }

    }

    if(isset($_POST['poSupp'])){
      $filteredInputPONSupp = validateInput($_POST['poSupp']);
      if($filteredInputPONSupp != false){
      	$poSupp_stmt = "UPDATE purchase_order SET supplier=? WHERE request_id=?";

  		$mySixthStmt = $conn -> prepare($poSupp_stmt);
		$mySixthStmt -> bind_param("ss",$filteredInputPONSupp ,$reqID);
		$mySixthStmt -> execute();

      	    if(strpos(mysqli_error($conn), 'too long')){
      		$error = true;
      		echo "alert('Invalid Input on the Supplier Field(Data entered is too long)');";
      	}
      }
      else{
      	$error = true;
	  	echo "alert('Invalid Input on the Purchase Order Supplier Field(Please enter data)');";
      }


    }
  }
  else{

    if(isset($_POST['careOF'])){
      $filteredInputCareOf = validateInput($_POST['careOF']);

      if($filteredInputCareOf != false){
      	$care_stmt = "UPDATE request_slip SET ConcernedOffice=? WHERE id=?";
  		$mySebenthStmt = $conn -> prepare($care_stmt);
		$mySebenthStmt -> bind_param("ss",$filteredInputCareOf ,$reqID);
		$mySebenthStmt -> execute();


      	     if(strpos(mysqli_error($conn), 'too long')){
      			$error = true;
      			echo "alert('Invalid Input on the Care Of Field(Data entered is too long)');";
      		}
      }
      else{
      	$error = true;
	  	echo "alert('Invalid Input on the Care Of Field(Please enter data)');";
      }
    }
  }


  if(!$error){
      if($type == 'PO'){
        echo 'sendReqForPO("noError")';
      }
      else if($type == 'ItemsNoPO'){
        echo 'sendReqNotForPO("noError")';
      }
      else{
        echo 'sendReqService("noError")';
      }
  }else{
      if($type == 'PO'){
        echo 'sendReqForPO("Error")';
      }
      else if($type == 'ItemsNoPO'){
        echo 'sendReqNotForPO("Error")';

      }
      else{
        echo 'sendReqService("Error")';
      }
  }
}
?>