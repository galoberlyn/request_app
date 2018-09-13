<?php
error_reporting(E_ALL);
include '../shared/authorization.php';
include '../shared/connection.php';
$user = $_SESSION['username'];
$username=$_SESSION['username'];
$userdetails = "SELECT * from users inner join user_details on users.id=user_details.user_id where username=?";
$userdetailsqry = $conn->prepare($userdetails);
$userdetailsqry->bind_param('s', $username);
$userdetailsqry->execute();


$userArray = $userdetailsqry-> get_result() -> fetch_array(MYSQLI_ASSOC);
$firstname =  $userArray['firstname'];
$lastname =  $userArray['lastname'];
function validateDate($date)
{	
	echo "<script> console.log('".$date."');</script>";
		$d = DateTime::createFromFormat('Y-n-j', $date);
		$d2 = DateTime::createFromFormat('Y-m-d', $date);
	    return ($d && $d->format('Y-n-j') === $date) || ($d2 && $d2->format('Y-m-d') === $date);	
    
}

function validateInput($input){
	$input = trim($input);
	$input = stripslashes($input);

	if(!empty($input)){
		$input = htmlspecialchars($input);
		return $input;
	}
	else{
		return false;
	}
	
}

function validateMaxLength($input, $maxLength){
	if(strlen($input) <= $maxLength){
		return $input;
	}
	else {
		return false;
	}
}

function validateNumber($input, $max){
	if(is_numeric($input)){
		if($input < $max){
			if($input < 0){
				return "negative";
			}
			else{
				return "positive";
			}
		}else{
			return "max";
		}
	}else{
		return "notNumeric";
	}
}


if(isset($_POST['type'])){
	$type = $_POST['type'];
	$_SESSION['type'] = $type;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Request</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- styles -->
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/font-awesome.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="../Jquery/jquery.are-you-sure.js"> </script>
        <script src="../Jquery/ays-beforeunload-shim.js"></script>
        <script>
      	$(function() {
			 $('.requestForm').areYouSure();

		});
		

        </script>
</head>
<body>
    <?php
                include "../header.php";
    ?>
   
    <section class="menu-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse ">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a class="" href="dashboard.php"><i class="glyphicon glyphicon-home"></i> Dashboard</a></li>
                            <li><a href="addrequest.php"><i class="glyphicon glyphicon-plus"></i> Add New Request</a></li>
                            <li><a href="changePassword.php"><i class="glyphicon glyphicon-lock"></i> Change password</a></li>
                            <li><a href="../dashboard/aboutUs.php"><i class="fa fa-info-circle"></i> About Us</a></li>
                            <li><a href="../logout.php">Log out</a></li>
                            

                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section> 
    
    
    <div class="wrapper">
    <div class="content">
            <div class="details">
                <div class="requester">
                   
                    <div class="panel-body">
                        <div class="col-md-14">
                            <h4 class="page-head-line">Add New Request</h4> 
                        </div>
                    </div>
                        <ul class="nav nav-tabs">
    <li id='tab1' class="active"><a id='nopo' data-toggle="tab" href="#home">Capital Expenditure<!-- Not for PO --></a></li>
    <li id='tab2'><a id='po' data-toggle="tab" href="#menu1">Program-Based Expenditure<!-- For PO --></a></li>
    <li id='tab3'><a id='serv' data-toggle="tab" href="#menu2">Other Requests<!-- For Services --></a></li>
  </ul>
                   
                    <div class="tab-content">
    <div id="home" class="tab-pane fade in active">


<?php
//Input validation sa no po Updated by Galo 7/10/17
    if(isset($_POST['requestNoPO'])){
            $noPOerror = false;

            	//$qty = htmlspecialchars($_POST['quantity'], ENT_QUOTES);
				$req_no = validateInput($_POST['req_no']); // request slip
				if($req_no === false){
					$noPOerror = true;
					echo "<script>alert('Data inputted on the Request Number field is blank')</script>";
					echo "<p style = 'color:red'>Data inputted on the Request Number field is blank</p>";

				}
				else{
					if(validateMaxLength($req_no, 13) === false){
						$noPOerror = true;
						echo "<script>alert('Data inputted on the Request Number field is too long')</script>";
						echo "<p style = 'color:red'>Data inputted on the Request Number field is too long</p>";
					}
				}



				$concerned_office = validateInput($_POST['concerned_office']);
				if($concerned_office === false){
					$noPOerror = true;
					echo "<script>alert('Data inputted on the Concerned Office field is blank')</script>";
					echo "<p style = 'color:red'>Data inputted on the Concerned Office field is blank</p>";
				}
				else{
					if(validateMaxLength($concerned_office, 40) === false){
						$noPOerror = true;
						echo "<script>alert('Data inputted on the Concerned Office field is too long')</script>";
						echo "<p style = 'color:red'>Data inputted on the Concerned Office field is too long</p>";
					}
				}



				$date_needed = $_POST['date_needed']; // request slip
				if(!validateDate($date_needed) && strtoupper($date_needed) != 'ASAP' ){
					$noPOerror = true;
					echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('The Date Needed Field must be in the format of Y-m-d or ASAP');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							document.body.appendChild(div); </script>"; 
					echo "<p style = 'color:red'>The Date Needed Field must be in the format of Y-m-d or ASAP</p>";


					$description_array = $_POST['description'];
					$quant_array = $_POST['quantity'];
					$supp_array = $_POST['supplier_nopo'];
					$descarr_ctr = count($description_array);
					
					echo "<script> var descarr_ctr = " . $descarr_ctr . ";</script>";
					
				//save input hehehehehehehe
				echo "<script>window.onload = function(){
					document.getElementById('req_nopo').setAttribute('value','".$_POST['req_no']."');
					document.getElementById('co_nopo').setAttribute('value','".$_POST['concerned_office'] ."');
					document.getElementById('purpose_nopo').innerHTML = '".$_POST['purpose']."';
					document.getElementById('time_nopoID').setAttribute('value','".$_POST['time_nopo']."');";

					for($i = 0 ; $i < $descarr_ctr; $i++){

						echo "document.getElementsByClassName('desc_nopo')[".$i."].setAttribute('value', '".$description_array[$i]."');";
						echo "document.getElementsByClassName('quant_nopo')[".$i."].setAttribute('value', '".$quant_array[$i]."');";
						echo "document.getElementsByClassName('supp_nopo')[".$i."].setAttribute('value', '".$supp_array[$i]."');";
						if($i < $descarr_ctr-1 ){
						echo "addItem();";
						}
						
						

					}

					echo "document.getElementById('NOPOform').reset();";
					echo "}</script> ";



				
				}else{
					$currDate = date('Y-m-d');
	  				$givenDate = date($date_needed);
	  				if($givenDate < $currDate){
	  					$noPOerror = true;
	  					echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('The inputted date in the Date Needed Field must be greater than the date today');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							document.body.appendChild(div); </script>";
	  					echo "<p style = 'color:red'>The inputted date in the Date Needed Field must be greater than the date today</p>";
	  					$description_array = $_POST['description'];
					$quant_array = $_POST['quantity'];
					$supp_array = $_POST['supplier_nopo'];
					$descarr_ctr = count($description_array);
					
					echo "<script> var descarr_ctr = " . $descarr_ctr . ";</script>";
					
				//save input hehehehehehehe
				echo "<script>window.onload = function(){
					document.getElementById('req_nopo').setAttribute('value','".$_POST['req_no']."');
					document.getElementById('co_nopo').setAttribute('value','".$_POST['concerned_office'] ."');
					document.getElementById('purpose_nopo').innerHTML = '".$_POST['purpose']."';
					document.getElementById('time_nopoID').setAttribute('value','".$_POST['time_nopo']."');";

					for($i = 0 ; $i < $descarr_ctr; $i++){

						echo "document.getElementsByClassName('desc_nopo')[".$i."].setAttribute('value', '".$description_array[$i]."');";
						echo "document.getElementsByClassName('quant_nopo')[".$i."].setAttribute('value', '".$quant_array[$i]."');";
						echo "document.getElementsByClassName('supp_nopo')[".$i."].setAttribute('value', '".$supp_array[$i]."');";
						if($i < $descarr_ctr-1 ){
						echo "addItem();";
						}
						
						

					}

					echo "document.getElementById('NOPOform').reset();";
					echo "}</script> ";
	  				}
				}

				$purpose = validateInput($_POST['purpose']); // request slip
				if($purpose === false){
					$noPOerror = true;
					echo "<script>alert('Data inputted on the Purpose field is blank')</script>";
					echo "<p style = 'color:red'>Data inputted on the Purpose field is blank</p>";

				}
				else{
					if(validateMaxLength($purpose, 80) === false){
						$noPOerror = true;
						echo "<script>alert('Data inputted on the Description field is too long')</script>";
						echo "<p style = 'color:red'>Data inputted on the Description field is too long</p>";
					}
				}

				//Item Validation
				$description = $_POST['description']; // array per item boyyy
				$quantity = $_POST['quantity']; // array per item boyyy
				$supplier_nopo = $_POST['supplier_nopo'];
				

				$noPOItemserror = 0;
				for($i = 0 ; $i < count($description); $i++){
						$desc = validateInput($description[$i]);
						$quant = validateInput($quantity[$i]);
						$supp = validateInput($supplier_nopo[$i]);
						$itemNo = $i+1;

						//Description
						if($desc === false){
							$noPOItemserror++;
							echo "<script>alert('Data inputted on Item No.".$itemNo." of the ItemName field is blank')</script>";
							echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the ItemName field is blank</p>";

						}
						else{
							if(validateMaxLength($desc, 20) === false){
								$noPOItemserror++;
								echo "<script>alert('Data inputted on Item No.".$itemNo." of the ItemName field is too long')</script>";
								echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the ItemName field is too long</p>";
							}
						}

						//Quantity
						if($quant === false){
								$noPOItemserror++;
								echo "<script>alert('Data inputted on Item No.".$itemNo." of the Quantity field is blank')</script>";
								echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Quantity field is blank</p>";

						}
						else{
							if(validateNumber($quant, 1000) != "positive"){
								$noPOItemserror++;
								if(validateNumber($quant, 1000) === "max"){
									echo "<script>alert('Data inputted on Item No.".$itemNo." of the Quantity field exceeded the maximum quantity allowed')</script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Quantity field exceeded the maximum quantity allowed</p>";		
								}
								else if(validateNumber($quant, 1000 ) === "notNumeric"){
									echo "<script>alert('Data inputted on Item No.".$itemNo." of the Quantity field is not a number')</script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Quantity field is not a number</p>";	
								}
								else {
									echo "<script>alert('Data inputted on Item No.".$itemNo." of the Quantity field is lesser or equal to 0')</script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Quantity field is lesser or equal to 0</p>";	
								}
							}
						}

						//UnitCost
						if($supp === false){
							$noPOItemserror++;
								echo "<script>alert('Data inputted on Item No.".$itemNo." of the Unit Cost field is blank')</script>";
								echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Unit Cost field is blank</p>";						
						}
						else{
							if(validateNumber($supp, 100000) != "positive"){
								$noPOItemserror++;
								if(validateNumber($supp, 100000) === "max"){
									echo "<script>alert('Data inputted on Item No.".$itemNo." of the Unit Cost field exceeded the maximum cost')</script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Unit Cost field exceeded the maximum cost</p>";		
								}
								else if(validateNumber($supp, 100000 ) === "notNumeric"){
									echo "<script>alert('Data inputted on Item No.".$itemNo." of the Unit Cost field is not a number')</script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Unit Cost field is not a number</p>";	
								}
								else {
									echo "<script>alert('Data inputted on Item No.".$itemNo." of the Unit Cost field is lesser or equal to 0')</script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Unit Cost field is lesser or equal to 0</p>";	
								}
							}
							
						}
						

				}


			$actno = validateInput($_POST['time_nopo']);
			if($actno === false){
				$noPOerror = true;
  				echo "<script>alert('The inputted data in the Activity Number Field is blank')</script>";
  				echo "<p style = 'color:red'>The inputted data in the Activity Number Field is blank</p>";
			}else{
						if(validateNumber($actno, 1000000) != "positive"){
								
								$noPOerror = true;

								if(validateNumber($actno, 1000000) === "max"){
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Activity Number field should not exceed the maximum');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on Activity Number field should be a positive number</p>";

								}
								else if(validateNumber($actno, 1000000 ) === "notNumeric"){
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Activity Number field is not a number');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on the Activity Number field is not a number </p>";
								}
								else {
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Activity Number field should be a positive number');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on Activity Number field should be a positive number</p>";		
								}
							}
			}


			$name = $firstname . " " . $lastname;

		    $timeFinal;
		if($noPOerror === false && $noPOItemserror === 0){
			//Activity No. Validation
			


		  		$reqCE = "CE_".$req_no;
				$reqno = "INSERT into request_slip (rs_no, requested_by, date_needed, time_needed, created_at,updated_at,purpose,status,type,ConcernedOffice) VALUES 
				(?, ?, ?, ?, now(),now(), ?, 'Pending', 'ItemsNoPO',?)";
				$reqQry=$conn->prepare($reqno);
				$reqQry->bind_param('ssssss', $reqCE, $name, $date_needed, $actno, $purpose, $concerned_office );
				$reqQry->execute();
				// var_dump($reqQry->error);
				if(strpos($reqQry->error, 'Duplicate') === 0){
					$noPOerror=true;
				$reqQry->close();
					echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Request number ". $req_no ." Already exists!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							document.body.appendChild(div); </script>";
					echo "<p style = 'color:red'>Request number ". $req_no ." Already exists!</p>";

					$description_array = $_POST['description'];
					$quant_array = $_POST['quantity'];
					$supp_array = $_POST['supplier_nopo'];
					$descarr_ctr = count($description_array);
					
					echo "<script> var descarr_ctr = " . $descarr_ctr . ";</script>";
					
				//save input hehehehehehehe
				echo "<script>window.onload = function(){
					document.getElementById('co_nopo').setAttribute('value','".$_POST['concerned_office'] ."');
					document.getElementById('purpose_nopo').innerHTML = '".$_POST['purpose']."';
					document.getElementById('time_nopoID').setAttribute('value','".$_POST['time_nopo']."');";

					for($i = 0 ; $i < $descarr_ctr; $i++){

						echo "document.getElementsByClassName('desc_nopo')[".$i."].setAttribute('value', '".$description_array[$i]."');";
						echo "document.getElementsByClassName('quant_nopo')[".$i."].setAttribute('value', '".$quant_array[$i]."');";
						echo "document.getElementsByClassName('supp_nopo')[".$i."].setAttribute('value', '".$supp_array[$i]."');";
						if($i < $descarr_ctr-1 ){
						echo "addItem();";
						}
						
						

					}

					echo "document.getElementById('NOPOform').reset();";
					echo "}</script> ";
				}

			

			if ($reqQry) {

				$request_form_id2 = mysqli_insert_id($conn);

				function filter(&$value) {
				  		$value = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
				}
				array_walk_recursive($description, "filter");
				array_walk_recursive($supplier_nopo, "filter");

				for($i = 0; $i < count($quantity); $i++){
					$delStmt1 = "DELETE FROM itemsnotpo WHERE request_slip_no = ?";
					$delStmt2 = "DELETE FROM request_slip WHERE id = ?";

					if(empty($description[$i])){
						$stmt1 = $conn->prepare($delStmt1);
						$stmt1->bind_param('s', $reqno);
						$stmt1->execute();
						$stmt2 = $conn->prepare($delStmt2);
						$stmt2->bind_param('s', $request_form_id2);
						$stmt2->execute();

					
						$noPOerror = true;

						echo "<script> alert('Invalid input on the Item Field of item:".$description[$i]."(Data entered is blank)'); </script>";
						echo "<p style = 'color:red'>Invalid input on the Item Field of item:".$description[$i]."(Data entered is blank)</p>";

					}
					if(is_numeric($quantity[$i])){
						if($quantity[$i] < 1){
							$stmt1 = $conn->prepare($delStmt1);
						$stmt1->bind_param('s', $reqno);
						$stmt1->execute();
						$stmt2 = $conn->prepare($delStmt2);
						$stmt2->bind_param('s', $request_form_id2);
						$stmt2->execute();
							$noPOerror = true;

							echo "<script> alert('Invalid input on the Quantity Field of item:".$description[$i]."(Data entered must be greater than 0)'); </script>";
							echo "<p style = 'color:red'>Invalid input on the Quantity Field of item:".$description[$i]."(Data entered must be greater than 0)</p>";

						}
					}
					if(empty($supplier_nopo[$i])){
						$stmt1 = $conn->prepare($delStmt1);
						$stmt1->bind_param('s', $reqno);
						$stmt1->execute();
						$stmt2 = $conn->prepare($delStmt2);
						$stmt2->bind_param('s', $request_form_id2);
						$stmt2->execute();
						$noPOerror = true;

						echo "<script> alert('Invalid input on the Supplier Field of item:".$description[$i]."(Data entered is blank)'); </script>";
						echo "<p style = 'color:red'>Invalid input on the Supplier Field of item:".$description[$i]."(Data entered is blank)</p>";

					}
					
					if($noPOerror === false){
						$amount_nopo[$i] = $quantity[$i] * $supplier_nopo[$i];
						$itemspo = "INSERT INTO itemsnotpo (quantity, description, request_slip_no, supplier, amount) VALUES (?, ?, ?, ?, ?);";

						$itemsresult=$conn->prepare($itemspo);
						$itemsresult->bind_param('sssss', $quantity[$i], $description[$i], $request_form_id2, $supplier_nopo[$i], $amount_nopo[$i]);
						$itemsresult->execute();

						
						$noDelQty= 0;

						$rpoDelQry="INSERT INTO delivered_items (item_name, rs_item_no, quantity, qty_delivered, delivered_items.date, created_at, updated_at) values (?, ?, ?, ?, NOW(), NOW(), NOW())";
						$rpoInsQry=$conn->prepare($rpoDelQry);
						$rpoInsQry->bind_param('siii', $description[$i], $request_form_id2, $quantity[$i], $noDelQty);
						$rpoInsQry->execute();
						if(strpos($itemsresult->error, 'description')){
							$noPOerror = true;
							$stmt1 = $conn->prepare($delStmt1);
						$stmt1->bind_param('s', $reqno);
						$stmt1->execute();
						$stmt2 = $conn->prepare($delStmt2);
						$stmt2->bind_param('s', $request_form_id2);
						$stmt2->execute();
							echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Invalid input on the Item Field of item:".$description[$i]."(Data entered is too long)');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							document.body.appendChild(div); </script>";
							echo "<p style = 'color:red'>Invalid input on the Item Field of item:".$description[$i]."(Data entered is too long)</p>";
						}
						
						if(strpos($itemsresult->error, 'supplier')){
							$noPOerror = true;
							$stmt1 = $conn->prepare($delStmt1);
						$stmt1->bind_param('s', $reqno);
						$stmt1->execute();
						$stmt2 = $conn->prepare($delStmt2);
						$stmt2->bind_param('s', $request_form_id2);
						$stmt2->execute();
							echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Invalid input on the Supplier of item:".$description[$i]."(Data entered is too long)');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							document.body.appendChild(div); </script>";
							echo "<p style = 'color:red'>Invalid input on the Supplier Field of item:".$description[$i]."(Data entered is too long)</p>";
						}
						
						if (strpos($itemsresult->error, 'integer')){
							$noPOerror = true;
							$stmt1 = $conn->prepare($delStmt1);
						$stmt1->bind_param('s', $reqno);
						$stmt1->execute();
						$stmt2 = $conn->prepare($delStmt2);
						$stmt2->bind_param('s', $request_form_id2);
						$stmt2->execute();
							echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Invalid input on the Quantity Field of item:".$description[$i]."(Data entered is not a number)');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
							echo "<p style = 'color:red'>Invalid input on the Quantity Field of item:".$description[$i]."(Data entered is not a number)</p>";
						}
					}
					else{
						break;
					}

				}
				if($noPOerror === false){
					echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('". $req_no . " Successfully created!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
				}
			} 

		}
				// echo "<script> window.location='dashboard.php'; </script>";
			
    }
?>
        
        
<h3><!-- NOT FOR PO -->Capital Expenditure</h3>
<form onsubmit="return confirm('Are you sure data is correct?');" class='requestForm' method='POST' id="NOPOform" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	
	
        <div class='row'>
        <div class='col-lg-3'>
        <strong>CE PRF NO.<!-- Request Number --></strong>
        </div>
        <div class='col-md-5'>
        <input type='text' id='req_nopo' name='req_no' maxlength="13" required>
        </div>
        </div>
    
        <div class='row'>
        <div class='col-lg-3'>
        <strong>Concerned Office</strong>
        </div>
        <div class='col-md-5'>
        <input list = 'conOffNOPO' id='co_nopo' type='text' maxlength="40" name='concerned_office' required>
        <datalist id = 'conOffNOPO'>
		<?php
			$concernedOffStmt = "SELECT DISTINCT ConcernedOffice from request_slip where ConcernedOffice IS NOT NULL";
			$concernedOffQuery = mysqli_query($conn, $concernedOffStmt);

			while($noPOconOff = mysqli_fetch_array($concernedOffQuery)){
				echo "<option value='".$noPOconOff[0]."'>";
			}
		?>
        </datalist>
        </div>
        </div>
		
				
	
	    <div class="row">
        <div class="col-lg-3">
            <strong>Date <!-- Needed -->Requested</strong>
        </div>
        <div class="col-md-5">
            <input type="date" id='date_nopo' name="date_needed" required>
        </div>    
	    </div>
	    <div class='row'>
	     <div class="col-lg-3">
            <strong><!-- Time needed< -->Activity Number</strong>
        </div>
        <div class="col-md-5">
            <input type='number' name='time_nopo' id='time_nopoID' min="0" required>
        </div>    
	    </div>
	    <div class="row">
        <div class="col-lg-3">
            <strong>Description</strong>
        </div>
        <div class="col-md-5">
            <textarea rows='2' cols='50' id='purpose_nopo' name='purpose' placeholder="What is this request for?.." maxlength="80" required></textarea>
        </div>    
	    </div>
	
	<table class="table table-hover custab">
				<thead>
					<tr>
					<?php
				    	
				    	
					    	echo "<th>Item</th>";
					    	echo "<th>Quantity</th>";
					    	// echo "<th>Supplier</th>"
					    	echo "<th> Unit Cost </th>";
				    	?>
				  	</tr>
			  	</thead>
	  		  	<tbody id="items">
				  	<tr>
				  		<td><input type='text' class='desc_nopo' name='description[]' placeholder='itemname(pcs/box/rim)' maxlength="20" required></td>
                        <?php
					  			echo "<td><input type='number' class='quant_nopo' name='quantity[]' min ='1' max='100000' required></td>";

				  		?>
				  		<td><input list ='suppNoPO' type='number' class='supp_nopo' min="0" name='supplier_nopo[]' maxlength="20" required></td>
				  		
				  		
				  		<td><button style="display: none;" type="button" onclick="event.srcElement.parentElement.parentElement.remove();rmv()" class='btn btn-danger' >Delete</button></td>
				  	</tr>
			  	</tbody>
			</table>
			<button type="button" class='btn btn-info' onclick="addItem()">Add another Item</button>
		
		<button type="submit" class='btn btn-info'  name='requestNoPO'>Submit</button>

</form>
    </div>
    <div id="menu1" class="tab-pane fade">
<?php
//input validation sa po Updated by Galo 7/10/2017
   	if(isset($_POST['requestPO'])){
   		$POerror = false;

			$req_no = validateInput($_POST['req_no']); // request slip
			if($req_no === false){
				$POerror = true;
				echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Data inputted on the Request Number field is blank!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
				echo "<p style = 'color:red'>Data inputted on the Request Number field is blank</p>";							
			}
			else{
					if(validateMaxLength($req_no, 13) === false){
						$POerror = true;
				echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Data inputted on the Request Number field is too long!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
				echo "<p style = 'color:red'>Data inputted on the Request Number field is too long</p>";
					}
				}




			$supplierMain =	validateInput($_POST['supplierOfpo']);
			if($supplierMain === false){
				$POerror = true;
				echo "<script>alert('Data inputted on the Supplier field is blank')</script>";
				echo "<p style = 'color:red'>Data inputted on the Supplier field is blank</p>";
			}
			else{
					if(validateMaxLength($supplierMain, 40) === false){
						$POerror = true;
						echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Data inputted on the Supplier field is too long!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
						echo "<p style = 'color:red'>Data inputted on the Supplier field is too long</p>";
					}
				}



			$date_needed = $_POST['date_needed']; // request slip
			if(!validateDate($date_needed) && strtoupper($date_needed) != 'ASAP' ){
				$POerror = true;
				echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('The Date Needed Field must be in the format of Y-m-d or ASAP!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							document.body.appendChild(div); </script>";
				echo "<p style = 'color:red'>The Date Needed Field must be in the format of Y-m-d or ASAP</p>";
				$description_array = $_POST['description'];
				$quant_array = $_POST['quantity'];
				$supp_array = $_POST['supplierOfpo'];
				$descarr_ctr = count($description_array); 

				echo "<script>window.onload = function(){
					document.getElementById('req_po').setAttribute('value','".$_POST['req_no']."');
					document.getElementById('supplierPo').setAttribute('value','".$_POST['supplierOfpo'] ."');
					document.getElementById('purpose_po').innerHTML = '".$_POST['purpose']."';";
					for($i = 0 ; $i < $descarr_ctr; $i++){


						echo "document.getElementsByClassName('desc_po')[".$i."].setAttribute('value', '".$description_array[$i]."');";
						echo "document.getElementsByClassName('qty_po')[".$i."].setAttribute('value', '".$quant_array[$i]."');";
						echo "document.getElementsByClassName('supp_po')[".$i."].setAttribute('value', '".$supp_array[$i]."');";
						if($i < $descarr_ctr-1 ){
						echo "addItem1();";
						}
						

					}
					// echo "$('#menu2').load(location.href + '#menu2');";
					echo "$('.nav-tabs li:eq(1) a').tab('show');";
					echo "document.getElementById('POform').reset();";
					echo "};</script> ";

			}else{
				$currDate = date('Y-m-d');
  				$givenDate = date($date_needed);
  				if($givenDate < $currDate){
  					$POerror = true;
  					echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('The inputted date in the Date Needed Field must be greater than the date today');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							document.body.appendChild(div); </script>";
  					echo "<p style = 'color:red'>The inputted date in the Date Needed Field must be greater than the date today</p>";
  					$description_array = $_POST['description'];
				$quant_array = $_POST['quantity'];
				$supp_array = $_POST['supplierOfpo'];
				$descarr_ctr = count($description_array); 

				echo "<script>window.onload = function(){
					document.getElementById('req_po').setAttribute('value','".$_POST['req_no']."');
					document.getElementById('supplierPo').setAttribute('value','".$_POST['supplierOfpo'] ."');
					document.getElementById('purpose_po').innerHTML = '".$_POST['purpose']."';";
					for($i = 0 ; $i < $descarr_ctr; $i++){


						echo "document.getElementsByClassName('desc_po')[".$i."].setAttribute('value', '".$description_array[$i]."');";
						echo "document.getElementsByClassName('qty_po')[".$i."].setAttribute('value', '".$quant_array[$i]."');";
						echo "document.getElementsByClassName('supp_po')[".$i."].setAttribute('value', '".$supp_array[$i]."');";
						if($i < $descarr_ctr-1 ){
						echo "addItem1();";
						}
						

					}
					// echo "$('#menu2').load(location.href + '#menu2');";
					echo "$('.nav-tabs li:eq(1) a').tab('show');";
					echo "document.getElementById('POform').reset();";
					echo "};</script> ";
  				}
			}


			$purpose = validateInput($_POST['purpose']); // request slip
			if($purpose === false){
				$POerror = true;
				echo "<script>alert('Data inputted on the Purpose field is blank')</script>";
				echo "<p style = 'color:red'>Data inputted on the Purpose field is blank</p>";
			}
			else{
					if(validateMaxLength($purpose, 80) === false){
						$POerror = true;
						echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Data inputted on the Description field is too long!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
						echo "<p style = 'color:red'>Data inputted on the Description field is too long</p>";
					}
				}

			//Item Validation
			$description = $_POST['description']; // array per item boyyy
			$quantity = $_POST['quantity']; // array per item boyyy
			$supplier_po = $_POST['supplier_po']; // array per item boyyy
			
				//Item Validation
				$POItemserror = 0;
				for($i = 0 ; $i < count($description); $i++){
						$desc = validateInput($description[$i]);
						$quant = validateInput($quantity[$i]);
						$supp = validateInput($supplier_po[$i]);
						$itemNo = $i+1;

						//Description
						if($desc === false){
							$POItemserror++;
							echo "<script> var div = document.createElement('div');
								div.setAttribute('class', 'alert');
								var span = document.createElement('button');
								span.setAttribute('class', 'closebtn');
								span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
								var spanTxt = document.createTextNode('Done')
								span.appendChild(spanTxt);
								var pTxt = document.createTextNode('Data inputted on Item No.".$itemNo." of the Itemname field is blank');
								var p = document.createElement('p');
								p.appendChild(pTxt);
								p.setAttribute('class', 'alertContent');
                            	div.appendChild(p);
								div.appendChild(span);
							
								document.body.appendChild(div); </script>";
						echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Itemname field is blank</p>";

						}
						else{
							if(validateMaxLength($desc, 20) === false){
								$POItemserror++;
								echo "<script> var div = document.createElement('div');
								div.setAttribute('class', 'alert');
								var span = document.createElement('button');
								span.setAttribute('class', 'closebtn');
								span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
								var spanTxt = document.createTextNode('Done')
								span.appendChild(spanTxt);
								var pTxt = document.createTextNode('Data inputted on Item No.".$itemNo." of the Itemname field is too long');
								var p = document.createElement('p');
								p.appendChild(pTxt);
								p.setAttribute('class', 'alertContent');
                            	div.appendChild(p);
								div.appendChild(span);
							
								document.body.appendChild(div); </script>";
								echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Itemname field is too long</p>";
							}
						}

						//Quantity
						if($quant === false){
								$POItemserror++;

								echo "<script> var div = document.createElement('div');
								div.setAttribute('class', 'alert');
								var span = document.createElement('button');
								span.setAttribute('class', 'closebtn');
								span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
								var spanTxt = document.createTextNode('Done')
								span.appendChild(spanTxt);
								var pTxt = document.createTextNode('Data inputted on Item No.".$itemNo." of the Quantity field is blank');
								var p = document.createElement('p');
								p.appendChild(pTxt);
								p.setAttribute('class', 'alertContent');
                            	div.appendChild(p);
								div.appendChild(span);
							
								document.body.appendChild(div); </script>";
							echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Quantity field is blank</p>";


						}
						else{
							if(validateNumber($quant, 1000) != "positive"){
								$POItemserror++;
								if(validateNumber($quant, 1000) === "max"){
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Item No.".$itemNo." of the Quantity field should be lower than or equal to 1000');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Quantity field should be lower than or equal to 1000</p>";

								}
								else if(validateNumber($quant, 1000 ) === "notNumeric"){
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Item No.".$itemNo." of the Quantity field is not a number');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Quantity field is not a number </p>";
								}
								else {
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Item No.".$itemNo." of the Quantity field is less than 0');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Quantity field is is less than 0 </p>";		
								}
							}
						}

						//UnitCost
						if($supp === false){
							$POItemserror++;
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Item No.".$itemNo." of the Unit Cost field is blank');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." Unit Cost field is blank</p>";				
						}
						else{
							if(validateNumber($supp, 500000) != "positive"){
								$POItemserror++;
								if(validateNumber($supp, 500000) === "max"){
									echo "<script>alert('Data inputted on Item No.".$itemNo." of the Unit Cost field exceeded the maximum cost')</script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Unit Cost field exceeded the maximum cost</p>";		
								

									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Item No.".$itemNo." of the Unit Cost field should be 1 - 500000');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "Data inputted on Item No.".$itemNo." of the Unit Cost field should be 1 - 500000";		

								}
								else if(validateNumber($supp, 500000 ) === "notNumeric"){
									echo "<script>alert('Data inputted on Item No.".$itemNo." of the Unit Cost field is not a number')</script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Unit Cost field exceeded the maximum cost</p>";		
								

									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Item No.".$itemNo." of the Unit Cost field is not a number');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "Data inputted on Item No.".$itemNo." of the Unit Cost field is not a";								}
								else {
									echo "<script>alert('Data inputted on Item No.".$itemNo." of the Unit Cost field should be 1 - 500000')</script>";
									echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Unit Cost field should be 1 - 500000</p>";		
								

									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Item No.".$itemNo." of the Unit Cost field should be 1 - 500000');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "Data inputted on Item No.".$itemNo." of the Unit Cost field should be 1 - 500000";
								}
							}
							
						}
						

				}

				
			$timepo = validateInput($_POST['time_po']);
			if($timepo === false){
				$POerror = true;
  				echo "<script>alert('The inputted data in the Activity Number Field is blank')</script>";
  				echo "<p style = 'color:red'>The inputted data in the Activity Number Field is blank</p>";
			}else{
						if(validateNumber($timepo, 1000000) != "positive"){
								
								$POerror = true;

								if(validateNumber($timepo, 1000000) === "max"){
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Activity Number field should not exceed the maximum');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on Activity Number field should be a positive number</p>";

								}
								else if(validateNumber($timepo, 1000000 ) === "notNumeric"){
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Activity Number field is not a number');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on the Activity Number field is not a number </p>";
								}
								else {
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Activity Number field should be a positive number');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on Activity Number field should be a positive number</p>";		
								}
							}
			}




			$name = $firstname . " " . $lastname;

			if($POerror === false && $POItemserror === 0){		  	
				$reqno = "INSERT into request_slip (rs_no, requested_by, date_needed, time_needed, created_at,updated_at,purpose,status,type) VALUES 
				(?, ?, ?, ?, now(),now(), ?, 'Pending', 'PO')";
				$reqpbe="PBE_".$req_no;
				$reqQry=$conn->prepare($reqno);
				$reqQry->bind_param('sssss', $reqpbe, $name, $date_needed, $timepo, $purpose);
				$reqQry->execute();
				// $reqQry->close();
				
				if(strpos($reqQry->error, 'Duplicate') === 0){
					echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Request number ". $req_no ." Already exists!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							document.body.appendChild(div); </script>";
					echo "<p style = 'color:red'>Request number ". $req_no ." Already exists!</p>";
					$POerror = true;
					$description_array = $_POST['description'];
				$quant_array = $_POST['quantity'];
				$supp_array = $_POST['supplierOfpo'];
				$descarr_ctr = count($description_array); 

				echo "<script>window.onload = function(){
					document.getElementById('supplierPo').setAttribute('value','".$_POST['supplierOfpo'] ."');
					document.getElementById('purpose_po').innerHTML = '".$_POST['purpose']."';";
					for($i = 0 ; $i < $descarr_ctr; $i++){


						echo "document.getElementsByClassName('desc_po')[".$i."].setAttribute('value', '".$description_array[$i]."');";
						echo "document.getElementsByClassName('qty_po')[".$i."].setAttribute('value', '".$quant_array[$i]."');";
						echo "document.getElementsByClassName('supp_po')[".$i."].setAttribute('value', '".$supp_array[$i]."');";
						if($i < $descarr_ctr-1 ){
						echo "addItem1();";
						}
						

					}
					// echo "$('#menu2').load(location.href + '#menu2');";
					echo "$('.nav-tabs li:eq(1) a').tab('show');";
					echo "document.getElementById('POform').reset();";
					echo "};</script> ";
				}

				if(strpos($reqQry->error, 'rs_no') === 0){
					echo "<script> alert('Invalid input on the Request Number Field(Data entered is too long)'); </script>";
					echo "<p style = 'color:red'>Invalid input on the Request Number Field(Data entered is too long)</p>";
				}
				

				if(strpos($reqQry->error, "purpose") === 0){
					echo "<script> alert('Invalid input on the Purpose Field(Data entered is too long)'); </script>";
					echo "<p style = 'color:red'>Invalid input on the Purpose Field(Data entered is too long)</p>";
				}


				if ($reqQry) {

					$request_form_id = mysqli_insert_id($conn);

					$poqry = "INSERT into purchase_order (request_id,supplier) VALUES (?,?)";
					$poResult = $conn->prepare($poqry);
					$poResult->bind_param('ss', $request_form_id, $supplierMain);
					$poResult->execute();
					// $poResult->close();

					if(strpos($poResult->error, "supplier") === 0){
						echo "<script> alert('Invalid input on the Supplier Field(Data entered is too long)'); </script>";
						echo "<p style = 'color:red'>Invalid input on the Supplier Field(Data entered is too long)</p>";
					}

					if($poResult){

						$request_form_id2 = mysqli_insert_id($conn);

						function filter(&$value) {
							  		$value = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
						}
						array_walk_recursive($description, "filter");
						array_walk_recursive($supplier_po, "filter");

						for($i = 0; $i < count($quantity); $i++){
							$deloStmt1 = "DELETE FROM itemspo where poid = ?";
							$deloStmt2 = "DELETE FROM purchase_order where id = ?";
							$deloStmt3 = "DELETE FROM request_slip WHERE id = ?";

							if(empty($description[$i])){
								$stmt1 = $conn->prepare($deloStmt1);
								$stmt1->bind_param('s', $request_form_id2);
								$stmt1->execute();
								$stmt1->close();

								$stmt2 = $conn->prepare($deloStmt2);
								$stmt2->bind_param('s', $request_form_id2);
								$stmt2->execute();
								$stmt2->close();

								$stmt3 = $conn->prepare($deloStmt3);
								$stmt3->bind_param('s', $request_form_id2);
								$stmt3->execute();
								$stmt3->close();
							
									$POerror = true;

									echo "<script> alert('Invalid input on the Item Field of item:".$description[$i]."(Data entered is blank)'); </script>";
									echo "<p style = 'color:red'>Invalid input on the Item Field of item:".$description[$i]."(Data entered is blank)</p>";

								}

							if(is_numeric($quantity[$i])){
								if($quantity[$i] < 1){
									$stmt1 = $conn->prepare($deloStmt1);
								$stmt1->bind_param('s', $request_form_id2);
								$stmt1->execute();
								$stmt1->close();

								$stmt2 = $conn->prepare($deloStmt2);
								$stmt2->bind_param('s', $request_form_id2);
								$stmt2->execute();
								$stmt2->close();

								$stmt3 = $conn->prepare($deloStmt3);
								$stmt3->bind_param('s', $request_form_id2);
								$stmt3->execute();
								$stmt3->close();
									$POerror = true;

									echo "<script> alert('Invalid input on the Quantity Field of item:".$description[$i]."(Data entered must be greater than 0)'); </script>";
									echo "<p style = 'color:red'>Invalid input on the Quantity Field of item:".$description[$i]."(Data entered must be greater than 0)</p>";

								}
							}

							if(empty($supplier_po[$i])){
								$stmt1 = $conn->prepare($deloStmt1);
								$stmt1->bind_param('s', $request_form_id2);
								$stmt1->execute();
								$stmt1->close();

								$stmt2 = $conn->prepare($deloStmt2);
								$stmt2->bind_param('s', $request_form_id2);
								$stmt2->execute();
								$stmt2->close();

								$stmt3 = $conn->prepare($deloStmt3);
								$stmt3->bind_param('s', $request_form_id2);
								$stmt3->execute();
								$stmt3->close();
								$POerror = true;

								echo "<script> alert('Invalid input on the Supplier Field of item:".$description[$i]."(Data entered is blank)'); </script>";
								echo "<p style = 'color:red'>Invalid input on the Supplier Field of item:".$description[$i]."(Data entered is blank)</p>";

							}


							if($POerror === false){
								$amount_po[$i] = $quantity[$i] * $supplier_po[$i]; 
								$itemspo = "INSERT INTO itemspo (quantity, description, poid, supplier_po, amount) VALUES (?, ?, ?, ?, ?);";
								
								$itemsresult = $conn->prepare($itemspo);
								$itemsresult->bind_param('sssss', $quantity[$i], $description[$i], $request_form_id2, $supplier_po[$i], $amount_po[$i]);
								$itemsresult->execute();
								//var_dump($itemsresult->error);
								// $itemsresult->close();
								$sel = "SELECT max(id) as id from request_slip";
								$rs_mod = $conn->prepare($sel);
								$rs_mod->execute();
								$rs_mod_arr = $rs_mod->get_result()->fetch_array(MYSQLI_ASSOC);
								$noDelQty= 0;

								$poDelQry="INSERT INTO delivered_items (item_name, rs_item_no, quantity, qty_delivered, delivered_items.date, created_at, updated_at) values (?, ?, ?, ?, NOW(), NOW(), NOW())";
								$poInsQry=$conn->prepare($poDelQry);
								$poInsQry->bind_param('siii', $description[$i], $rs_mod_arr['id'], $quantity[$i], $noDelQty);
								$poInsQry->execute() or die($conn->error);
								if(strpos($itemsresult->error, 'description')){
									$stmt1 = $conn->prepare($deloStmt1);
								$stmt1->bind_param('s', $request_form_id2);
								$stmt1->execute();
								$stmt1->close();

								$stmt2 = $conn->prepare($deloStmt2);
								$stmt2->bind_param('s', $request_form_id2);
								$stmt2->execute();
								$stmt2->close();

								$stmt3 = $conn->prepare($deloStmt3);
								$stmt3->bind_param('s', $request_form_id2);
								$stmt3->execute();
								$stmt3->close();
									$POerror = true;

									echo "<script> alert('Invalid input on the Item Field of item:".$description[$i]."(Data entered is too long)'); </script>";
									echo "<p style = 'color:red'>Invalid input on the Item Field of item:".$description[$i]."(Data entered is too long)</p>";
								}
								
								if(strpos($itemsresult->error, 'supplier') === 0){
									$stmt1 = $conn->prepare($deloStmt1);
								$stmt1->bind_param('s', $request_form_id2);
								$stmt1->execute();
								$stmt1->close();

								$stmt2 = $conn->prepare($deloStmt2);
								$stmt2->bind_param('s', $request_form_id2);
								$stmt2->execute();
								$stmt2->close();

								$stmt3 = $conn->prepare($deloStmt3);
								$stmt3->bind_param('s', $request_form_id2);
								$stmt3->execute();
								$stmt13->close();
									$POerror = true;

									echo "<script> alert('Invalid input on the Supplier Field of item:".$description[$i]."(Data entered is too long)'); </script>";
									echo "<p style = 'color:red'>Invalid input on the Supplier Field of item:".$description[$i]."(Data entered is too long)</p>";
								}
								
								if (strpos($itemsresult->error, 'integer') === 0){
									$stmt1 = $conn->prepare($deloStmt1);
								$stmt1->bind_param('s', $request_form_id2);
								$stmt1->execute();
								$stmt1->close();

								$stmt2 = $conn->prepare($deloStmt2);
								$stmt2->bind_param('s', $request_form_id2);
								$stmt2->execute();
								$stmt2->close();

								$stmt3 = $conn->prepare($deloStmt3);
								$stmt3->bind_param('s', $request_form_id2);
								$stmt3->execute();
								$stmt13->close();
									$POerror = true;

									echo "<script> alert('Invalid input on the Quantity Field of item:".$description[$i]."(Data entered is not a number)'); </script>";
									echo "<p style = 'color:red'>Invalid input on the Quantity Field of item:".$description[$i]."(Data entered is not a number)</p>";
								}
			      
							}
							else{
								break;
							}

			            }

			            if($POerror === false){
							echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Request No.". $req_no . " Successfully Created!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";


			            }
			        }
		            // echo "<script> window.location='dashboard.php'; </script>";
	       		}
	       	}
    }

        ?>
        
<h3> <!-- For PO -->Program-Based Expenditure</h3>
<form onsubmit="return confirm('Are you sure data is correct?');" class='requestForm' method='POST' id="POform" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

        <div class='row'>
        <div class='col-lg-3'>
        <strong>PBE PRF No.<!-- Request Number --></strong>
        </div>
        <div class='col-md-5'>
        <input type='text' id='req_po' name='req_no' maxlength="13" required>
        </div>
        </div>
    
        <div class='row'>
        <div class='col-lg-3'>
        <strong>Supplier</strong>
        </div>	
        <div class='col-md-5'>
        <input list = 'mainSuppPO' id='supplierPo' type='text' maxlength='40' name='supplierOfpo' required>
        <datalist id ='mainSuppPO'>
<?php
		$mainSuppStmt = "SELECT DISTINCT supplier FROM purchase_order where supplier IS NOT NULL";
		$mainSuppQuery = mysqli_query($conn, $mainSuppStmt);

		while($maunSupp = mysqli_fetch_array($mainSuppQuery)){
			echo "<option value ='".$maunSupp[0]."'>";
		}
?>
        </datalist>
        </div>
        </div>
    
	
	<div class="row">
        <div class="col-lg-3">
            <strong>Date <!-- Needed -->Requested</strong>
        </div>
        <div class="col-md-5">
            <input type="date" id="date_po" name="date_needed" required>
        </div>    
	    </div>
	    <div class='row'>
	     <div class="col-lg-3">
            <strong><!-- Time needed --> Activity Number</strong>
        </div>
        <div class="col-md-5">
            <input type='number' name='time_po' id='time_poID' min="0" required>
        </div>    
	    </div>
	    
	    <div class="row">
        <div class="col-lg-3">
            <strong><!-- Purpose -->Description</strong>
        </div>
        <div class="col-md-5">
            <textarea rows='2' cols='50' id="purpose_po" name='purpose' maxlength="80" placeholder="What is this request for?.." required></textarea>
        </div>    
	    </div>
	
	<table class="table table-hover custab">
				<thead>
					<tr>
					<?php
				    	
					    	echo "<th>Item</th>";
					    	echo "<th>Quantity</th>"; 
					    	// echo "<th>Supplier</th>";
					    	echo "<th>Unit Cost</";
				    	?>
				  	</tr>
			  	</thead>
			  	<tbody id="items1">
				  	<tr>
				  		<td><input type='text' class='desc_po' name='description[]' maxlength="20" placeholder='itemname(pcs/box/rim)' required></td>
                        <?php
					  			echo "<td><input type='number' class='qty_po' name='quantity[]' min ='1' max='100000' required></td>";

				  		?>
				  		<td><input list = 'suppapaPO' type='number'  min="0" class='supp_po' name='supplier_po[]' maxlength="20" required></td>
				  		<datalist id = 'suppapaPO'>
<?php
						$suppanPOStmt = "SELECT DISTINCT supplier_po FROM itemspo where supplier_po IS NOT NULL";
						$suppanPOQuery = mysqli_query($conn, $suppanPOStmt);

						while($suppanPOArr = mysqli_fetch_array($suppanPOQuery)){
							echo "<option value ='".$suppanPOArr[0]."'>";
						}
?>
				  		</datalist>

				  		<td><button style="display: none;" type="button" onclick="event.srcElement.parentElement.parentElement.remove();rmv()" class='btn btn-danger' >Delete</button></td>
				  	</tr>
			  	</tbody>
			</table>
			<button type="button" class='btn btn-info' onclick="addItem1()">Add another Item</button>
			
			<button type="submit" class='btn btn-info'  name='requestPO'>Submit</button>

</form>
    </div>
    <div id="menu2" class="tab-pane fade">
        
 <?php
 //input validation sa service Updated by Galo 7/10/2017
    if(isset($_POST['requestService'])){
    	$serviceError = false;

			$req_no = validateInput($_POST['req_no']); // request slip
			if($req_no === false){
				$serviceError = true;
				echo "<script>alert('Data inputted on the Request Number field is blank')</script>";
				echo "<p style = 'color:red'>Data inputted on the Request Number field is blank</p>";
			}else{
					if(validateMaxLength($req_no, 13) === false){
						$serviceError = true;;
						echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Data inputted on the Request Number field is too long!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
						echo "<p style = 'color:red'>Data inputted on the Request Number field is too long</p>";
					}
				}


			$concerned_office = validateInput($_POST['concerned_office']);
			if($concerned_office === false){
				$serviceError = true;
				echo "<script>alert('Data inputted on the Concerned Office field is blank')</script>";
				echo "<p style = 'color:red'>Data inputted on the Concerned Office field is blank</p>";
			}
			else{
					if(validateMaxLength($concerned_office, 40) === false){
						$serviceError = true;;
						echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Data inputted on the Concerned Office field is too long!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
						echo "<p style = 'color:red'>Data inputted on the Concerned Office field is too long</p>";
					}
				}


			$date_needed = $_POST['date_needed']; // request slip
			if(!validateDate($date_needed) && strtoupper($date_needed) != 'ASAP' ){
				$serviceError = true;
				echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('The Date Needed Field must be in the format of Y-m-d or ASAP');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
				echo "<p style = 'color:red'>The Date Needed Field must be in the format of Y-m-d or ASAP</p>";
				$description_array = $_POST['description'];
				$supp_array = $_POST['supplier_srv'];
				$descarr_ctr = count($description_array); 

				echo "<script>window.onload = function(){
					document.getElementById('service_req').setAttribute('value','".$_POST['req_no']."');
					document.getElementById('service_co').setAttribute('value','".$_POST['concerned_office'] ."');
					document.getElementById('purpose_serv').innerHTML = '".$_POST['purpose']."';
					document.getElementById('time_serv').setAttribute('value','" . $_POST['time_srv'] . "');";

					for($i = 0 ; $i < $descarr_ctr; $i++){


						echo "document.getElementsByClassName('desc_serv')[".$i."].setAttribute('value', '".$description_array[$i]."');";
						echo "document.getElementsByClassName('supp_serv')[".$i."].setAttribute('value', '".$supp_array[$i]."');";
						if($i < $descarr_ctr-1 ){
						echo "addItem2();";
						}
						

					}
					// echo "$('#menu2').load(location.href + '#menu2');";
					echo "$('.nav-tabs li:eq(2) a').tab('show'); ";
					echo "document.getElementById('ServiceForm').reset();";
					echo "};</script> ";

			}else{
				$currDate = date('Y-m-d');
  				$givenDate = date($date_needed);
  				if($givenDate < $currDate){
  					$serviceError = true;
  					echo "
  					";
  					echo "<p style = 'color:red'>The inputted date in the Date Needed Field must be greater than the date today</p>";
  					$description_array = $_POST['description'];
				$supp_array = $_POST['supplier_srv'];
				$descarr_ctr = count($description_array); 

				echo "<script>window.onload = function(){
					document.getElementById('service_req').setAttribute('value','".$_POST['req_no']."');
					document.getElementById('service_co').setAttribute('value','".$_POST['concerned_office'] ."');
					document.getElementById('purpose_serv').innerHTML = '".$_POST['purpose']."';
					document.getElementById('time_serv').setAttribute('value','" . $_POST['time_srv'] . "');";

					for($i = 0 ; $i < $descarr_ctr; $i++){


						echo "document.getElementsByClassName('desc_serv')[".$i."].setAttribute('value', '".$description_array[$i]."');";
						echo "document.getElementsByClassName('supp_serv')[".$i."].setAttribute('value', '".$supp_array[$i]."');";
						if($i < $descarr_ctr-1 ){
						echo "addItem2();";
						}
						

					}
					// echo "$('#menu2').load(location.href + '#menu2');";
					echo "$('.nav-tabs li:eq(2) a').tab('show'); ";
					echo "document.getElementById('ServiceForm').reset();";
					echo "};</script> ";
  				}
			}



			$act_no = validateInput($_POST['time_srv']);
			if($act_no === false){
				$serviceError = true;
  				echo "<script>alert('The inputted data in the Activity Number Field is blank')</script>";
  				echo "<p style = 'color:red'>The inputted data in the Activity Number Field is blank</p>";
			}else{
						if(validateNumber($act_no, 1000000) != "positive"){
								
								$serviceError = true;

								if(validateNumber($act_no, 1000000) === "max"){
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Activity Number field has exceed the max input');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on Activity Number field should be a positive number</p>";

								}
								else if(validateNumber($act_no, 1000000) === "notNumeric"){
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Activity Number field is not a number');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on the Activity Number field is not a number </p>";
								}
								else {
									echo "<script> var div = document.createElement('div');
									div.setAttribute('class', 'alert');
									var span = document.createElement('button');
									span.setAttribute('class', 'closebtn');
									span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
									var spanTxt = document.createTextNode('Done')
									span.appendChild(spanTxt);
									var pTxt = document.createTextNode('Data inputted on Activity Number field should be a positive number');
									var p = document.createElement('p');
									p.appendChild(pTxt);
									p.setAttribute('class', 'alertContent');
	                            	div.appendChild(p);
									div.appendChild(span);
								
									document.body.appendChild(div); </script>";
									echo "<p style = 'color:red'>Data inputted on Activity Number field should be a positive number</p>";		
								}
							}
			}

			$purpose = validateInput($_POST['purpose']); // request slip
			if($purpose === false){
				$serviceError = true;
				echo "<script>alert('Data inputted on the Purpose field is blank')</script>";
				echo "<p style = 'color:red'>Data inputted on the Purpose field is blank</p>";
			}
						else{
					if(validateMaxLength($purpose, 80) === false){
						$serviceError = true;;
						echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Data inputted on the Description field is too long!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
						echo "<p style = 'color:red'>Data inputted on the Description field is too long</p>";
					}
				}

			//Item Validation
			$description = $_POST['description']; // array per item boyyy
			$supplier_srv = $_POST['supplier_srv']; // array per item boyyy

				//Item Validation
				$serviceItemserror = 0;
				for($i = 0 ; $i < count($description); $i++){
						$desc = validateInput($description[$i]);
						$supplierSrv = validateInput($supplier_srv[$i]);
						$itemNo = $i+1;

						//Requested Item Services
						if($desc === false){
							$serviceItemserror++;
							echo "<script>alert('Data inputted on Item No.".$itemNo." of the Item/Service Name field is blank')</script>";
							echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Item/Service Name field is blank</p>";

						}
						else{
							if(validateMaxLength($desc, 20) === false){
								$serviceItemserror++;
								echo "<script>alert('Data inputted on Item No.".$itemNo." of the Item/Service Name field is too long')</script>";
								echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Item/Service Name field is too long</p>";
							}
						}

						//ServiceItemProvider
						if($supplierSrv === false){
							$noPOItemserror++;
							echo "<script>alert('Data inputted on Item/Service No.".$itemNo." of the Service Item/Provider field is blank')</script>";
							echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Service Item/Provider field is blank</p>";

						}
						else{
							if(validateMaxLength($desc, 20) === false){
								$serviceItemserror++;
								echo "<script>alert('Data inputted on Item No.".$itemNo." of the Service Item/Provider field is too long')</script>";
								echo "<p style = 'color:red'>Data inputted on Item No.".$itemNo." of the Service Item/Provider field is too long</p>";
							}
						}

				}



			$name = $firstname . " " . $lastname;

		if($serviceError === false && $serviceItemserror === 0){

		  	$reqser = "Others_".$req_no;
			$reqno = "INSERT into request_slip (rs_no, requested_by, date_needed, time_needed, created_at,updated_at,purpose,status,type,ConcernedOffice) VALUES 
			(?, ?, ?, ?, now(),now(), ?, 'Pending', 'Service',?)";
			$reqQry=$conn->prepare($reqno);
			$reqQry->bind_param('ssssss', $reqser, $name, $date_needed, $act_no, $purpose, $concerned_office );
			$reqQry->execute();
			// var_dump($reqQry->error);
			// $reqQry->close();

			if(strpos($reqQry->error, 'Duplicate') === 0){
				$serviceError = true;
				echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Request number ". $req_no ." Already Exists!
								Try another number');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
				echo "<p style = 'color:red'>Request number ". $req_no ." Already Exists!..</p>";
				$description_array = $_POST['description'];
				$supp_array = $_POST['supplier_srv'];
				$descarr_ctr = count($description_array); 

				echo "<script>window.onload = function(){
		
					document.getElementById('service_co').setAttribute('value','".$_POST['concerned_office'] ."');
					document.getElementById('purpose_serv').innerHTML = '".$_POST['purpose']."';
					document.getElementById('time_serv').setAttribute('value','" . $_POST['time_srv'] . "');";

					for($i = 0 ; $i < $descarr_ctr; $i++){


						echo "document.getElementsByClassName('desc_serv')[".$i."].setAttribute('value', '".$description_array[$i]."');";
						echo "document.getElementsByClassName('supp_serv')[".$i."].setAttribute('value', '".$supp_array[$i]."');";
						if($i < $descarr_ctr-1 ){
						echo "addItem2();";
						}
						

					}
					// echo "$('#menu2').load(location.href + '#menu2');";
					echo "$('.nav-tabs li:eq(2) a').tab('show'); ";
					echo "document.getElementById('ServiceForm').reset();";
					echo "};</script> ";
			}

			if(strpos($reqQry->error, 'rs_no')===0){
				echo "<script> alert('Invalid input on the Request Number Field(Data entered is too long)'); </script>";
				echo "<p style = 'color:red'>Invalid input on the Request Number Field(Data entered is too long)</p>";
			}
			
			if(strpos($reqQry->error, "purpose")===0){
				echo "<script> alert('Invalid input on the Purpose Field(Data entered is too long)'); </script>";
				echo "<p style = 'color:red'>Invalid input on the Purpose Field(Data entered is too long)</p>";
			}
			
			if(strpos($reqQry->error, "ConcernedOffice")===0){
				echo "<script> alert('Invalid input on the Concerned Office Field(Data entered is too long)'); </script>";
				echo "<p style = 'color:red'>Invalid input on the Concerned Office Field(Data entered is too long)</p>";
			}
			



			if ($reqQry) {
				$request_form_id =mysqli_insert_id($conn);

				function filter(&$value) {
					  		$value = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
				}
				array_walk_recursive($description, "filter");
				array_walk_recursive($supplier_srv, "filter");

				for($x = 0; $x < count($description); $x++){

					$delServStmt1 = "DELETE FROM services WHERE requestID = ?";
					$delServStmt2 = "DELETE FROM request_slip WHERE id = ?";
					
					if(empty($description[$x])){
						$statement1=$conn->prepare($delServStmt1);
						$statement1->bind_param('s', $request_form_id);
						$statement1->execute();
						$statement1->close();
						
						$statement2=$conn->prepare($delServStmt2);
						$statement2->bind_param('s', $request_form_id);
						$statement2->execute();
						$statement2->close();
						$serviceError = true;

						echo "<script> alert('Invalid input on the Service Field of Service:".$description[$x]."(Data entered is blank)'); </script>";
						echo "<p style = 'color:red'>Invalid input on the Service Field of Service:".$description[$x]."(Data entered is blank)</p>";

					}

					if(empty($supplier_srv[$x])){
						$statement1=$conn->prepare($delServStmt1);
						$statement1->bind_param('s', $request_form_id);
						$statement1->execute();
						$statement1->close();
						
						$statement2=$conn->prepare($delServStmt2);
						$statement2->bind_param('s', $request_form_id);
						$statement2->execute();
						$statement2->close();
						$serviceError = true;

						echo "<script> alert('Invalid input on the 	Service Provider Field of Service:".$description[$x]."(Data entered is blank)'); </script>";
						echo "<p style = 'color:red'>Invalid input on the 	Service Provider Field of Service:".$description[$x]."(Data entered is blank)</p>";

					}

					if($serviceError === false){

						$description1 = "INSERT INTO services (description, requestID, status, service_provider) VALUES (?, ?, 'Pending', ?);";
						$descresult = $conn->prepare($description1);
						$descresult->bind_param('sss', $description[$x], $request_form_id, $supplier_srv[$x]);
						$descresult->execute();
						$del=1;
						$nodel1=0;
						
						$rpoDelQry="INSERT INTO delivered_items (item_name, rs_item_no, quantity, qty_delivered, delivered_items.date, created_at, updated_at) values (?, ?, ?, ?, NOW(), NOW(), NOW())";
						$rpoInsQry=$conn->prepare($rpoDelQry);
						$rpoInsQry->bind_param('siii', $description[$x], $request_form_id, $del, $nodel1);
						$rpoInsQry->execute() or die($conn->error);

						if(strpos(mysqli_error($conn), 'description')){
							$statement1=$conn->prepare($delServStmt1);
						$statement1->bind_param('s', $request_form_id);
						$statement1->execute();
						$statement1->close();
						
						$statement2=$conn->prepare($delServStmt2);
						$statement2->bind_param('s', $request_form_id);
						$statement2->execute();
						$statement2->close();
							$serviceError = true;

							echo "<script> alert('Invalid input on the Service Field of Service:".$description[$x]."(Data entered is too long)'); </script>";
							echo "<p style = 'color:red'>Invalid input on the Service Field of Service:".$description[$x]."(Data entered is too long)</p>";
						}

						if(strpos(mysqli_error($conn), 'service_provider')){
							$statement1=$conn->prepare($delServStmt1);
						$statement1->bind_param('s', $request_form_id);
						$statement1->execute();
						$statement1->close();
						
						$statement2=$conn->prepare($delServStmt2);
						$statement2->bind_param('s', $request_form_id);
						$statement2->execute();
						$statement2->close();
							$serviceError = true;

							echo "<script> alert('Invalid input on the Service Provider Field of Service:".$description[$x]."(Data entered is too long)'); </script>";
							echo "<p style = 'color:red'>Invalid input on the Service Provider Field of Service:".$description[$x]."(Data entered is too long)</p>";
						}

					}
					else{
						break;
					}
				}

				if($serviceError === false){
					echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Request No.". $req_no . " Successfully Created!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
				}



				// echo "<script> window.location='dashboard.php'; </script>";
			}
		}
	}
        ?>
<h3> <!-- Services -->Other Requests</h3>
<form onsubmit="return confirm('Are you sure data is correct?');" class='requestForm' method='POST' id='ServiceForm' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	
    
        <div class='row'>
        <div class='col-lg-3'>
        <strong>Request Number</strong>
        </div>
        <div class='col-md-5'>
        <input id='service_req' type='text' name='req_no' maxlength="13" required>
        </div>
        </div>
    
        <div class='row'>
        <div class='col-lg-3'>
        <strong>Concerned Office</strong>
        </div>
        <div class='col-md-5'>
        <input list ='ServiceConOff' type='text' id='service_co' name='concerned_office' maxlength="40" required>
        <datalist id ='ServiceConOff'>
<?php
		$concernedOffStmtServ = "SELECT DISTINCT ConcernedOffice from request_slip where ConcernedOffice IS NOT NULL";
		$concernedOffQuerys = mysqli_query($conn, $concernedOffStmtServ);

		while($serviceconOff = mysqli_fetch_array($concernedOffQuerys)){
			echo "<option value='".$serviceconOff[0]."'>";
		}
?>
        </datalist>
        </div>
        </div>
			
	
	
	 <div class="row">
        <div class="col-lg-3">
            <strong>Date <!-- Needed -->Requested</strong>
        </div>
        <div class="col-md-5">
            <input type="date" name="date_needed" id='date_needed_serv' required>
        </div>    
	    </div>

	    <div class="row">
        <div class="col-lg-3">
            <strong>Activity Number</strong>
        </div>
        <div class="col-md-5">
            <input type='number' name='time_srv' min='0' id='time_serv' required>
        </div>    
	    </div>               
				  		

	    <div class="row">
        <div class="col-lg-3">
            <strong>Description</strong>
        </div>
        <div class="col-md-5">
            <textarea rows='2' cols='50' maxlength="80" id='purpose_serv' name='purpose' placeholder="What is this request for?.." required></textarea>
        </div>    
	    </div>
	    
	    <table class="table table-hover custab">
				<thead>
					<tr>
					<?php
				    	
				    	
					    	echo "<th>Requested Item/Service</th>";
					    	echo "<th>Service Item/Provider</th>";
					    
				    	?>
				  	</tr>
			  	</thead>
			  	<tbody id="items2">
				  	<tr>
				  		<td><input type='text' class='desc_serv' name='description[]' maxlength="20" required></td>
				  		<td><input name='supplier_srv[]' list ='suppserve' class='supp_serv' type='text' maxlength="20" required></td>
				  		<datalist id = 'suppserve'>
<?php
						$suppserveStmt = "SELECT DISTINCT service_provider FROM services where service_provider IS NOT NULL";
						$suppserveQuery = mysqli_query($conn, $suppserveStmt);

						while($suppserveArr = mysqli_fetch_array($suppserveQuery)){
							echo "<option value ='".$suppserveArr[0]."'>";
						}
?>
				  		</datalist>

				  		
				  		<td><button style="display: none;" type="button" onclick="event.srcElement.parentElement.parentElement.remove();rmv()" class='btn btn-danger' >Delete</button></td>
				  	</tr>
			  	</tbody>
			</table>
			<button type="button" class='btn btn-info' onclick="addItem2()">Add another Item</button>
		<button type="submit" class='btn btn-info'  name='requestService'>Submit</button>

</form>

			

    </div>
  </div>
                    </div>
               
           </div>
        </div>
    </div>
    </div>
    
        <div class="container">

  <!-- Modal -->
  <div class="modal fade" id="changepass" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Change Password</h4>
        </div>	
        <div class="modal-body">
          <?php
            $user_pass = "Select password from users where username = ?;";     
        $user_passQ = $conn->prepare($user_pass);
        $user_passQ->bind_param('s', $username);
        $user_passQ->execute();
    
        if (isset($_POST['newpass']) && isset($_POST['connewpass']) && isset($_POST['oldpass'])){
            $newpass = $_POST['newpass'];
            $connewpass = $_POST['connewpass'];
            $oldpass = $_POST['oldpass'];
        }
        
        $user=$user_passQ->get_result()->fetch_array(MYSQLI_ASSOC);
        
        echo "<form method='POST' class='form-horizontal requestForm' action=" .htmlspecialchars($_SERVER["PHP_SELF"]). ">";
           
        echo "<div class='row'>";    
        echo "<label class='col-lg-4 control-label'>Current Password </label>";
        echo "<div class='col-lg-7'>";
        echo "<input type='password' name='oldpass' class='form-control'>";
        echo "</div>";
        echo "</div>";
        echo "<br>";
            
        echo "<div class='row'>";   
        echo "<label class='col-lg-4 control-label'>New Password </label>";
        echo "<div class='col-lg-7'>";
        echo "<input type='password' name='newpass' class='form-control'>";
        echo "</div>";
        echo "</div>";
                    
        echo "<div class='row'>";   
        echo "<label class='col-lg-4 control-label'>Confirm New Password </label>";
        echo "<div class='col-lg-7'>";
        echo "<input type='password' name='connewpass' class='form-control'>";
        echo "</div>";
        echo "</div>";
        echo "</div>";     
            
        echo "<div class='modal-footer'>";
        echo "<input type='submit' name='change'  class='btn btn-primary'value='Change'>";
            
        echo "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>";  
        echo "</div>";
            
        echo "</form>";
        
        if(isset($_POST['change'])){
    
            if ($newpass == $connewpass &&
            password_verify($oldpass,$user['password'])) {
                
                $newpass = password_hash($_POST['newpass'],PASSWORD_DEFAULT);
                $updatepass = "UPDATE users SET password = ? where username = ?";
                $upadatepassQ = $conn->prepare($updatepass);
                $upadatepassQ->bind_param('ss', $newpass,$username);
                $upadatepassQ->execute();
                echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Successfully changed password, please log in again');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
                session_unset();
                session_destroy();
               	echo "<script> window.location='../index.php'; </script>";
                exit;

            } else {
            	
                echo "<script> var div = document.createElement('div');
							div.setAttribute('class', 'alert');
							var span = document.createElement('button');
							span.setAttribute('class', 'closebtn');
							span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
							var spanTxt = document.createTextNode('Done')
							span.appendChild(spanTxt);
							var pTxt = document.createTextNode('Incorrect password or Password does not match the confirm password!');
							var p = document.createElement('p');
							p.appendChild(pTxt);
							p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
							div.appendChild(span);
							
							document.body.appendChild(div); </script>";
            }
        }
            ?>
      </div>
      
    </div>
  </div>
  
</div>
                </div>
                
    <footer>
         <div class="container">
         
            <div class="copy text-center">
               Saint Louis University SCIS
            </div>
            
         </div>
      </footer
</body>
<script type="text/javascript">
(function() {
   // your page initialization code here
   // the DOM will be available here

})();



function addItem(){
	var tablebody = document.getElementById('items');
	if(tablebody.rows.length == 1){
		tablebody.rows[0].cells[tablebody.rows[0].cells.length-1].children[0].style.display="";
	}


	var tablebody = document.getElementById('items');
	var iClone = tablebody.children[0].cloneNode(true);
	for(var i = 0; i< iClone.cells.length; i++){
		iClone.cells[i].children[0].value ="";
	}

	tablebody.appendChild(iClone);
}

function rmv(){
	var tabRow = document.getElementById("items");
	if(tabRow.rows.length == 1){
		tabRow.rows[0].cells[tabRow.rows[0].cells.length-1].children[0].style.display="none";
	}
	else{
		tabRow.rows[0].cells[tabRow.rows[0].cells.length-1].children[0].style.display="";
	}
}

</script>
    <script type="text/javascript">
(function() {
   // your page initialization code here
   // the DOM will be available here

})();



function addItem1(){
	var tablebody1 = document.getElementById('items1');
	var tablebody1 = document.getElementById('items1');
	if(tablebody1.rows.length == 1){
		tablebody1.rows[0].cells[tablebody1.rows[0].cells.length-1].children[0].style.display="";
	}

    var tablebody1 = document.getElementById('items1');
	var iClone1 = tablebody1.children[0].cloneNode(true);
	for(var i = 0; i< iClone1.cells.length; i++){
		iClone1.cells[i].children[0].value ="";
	}

	tablebody1.appendChild(iClone1);
}

function rmv(){
	var tabRow1 = document.getElementById("items1");
	if(tabRow1.rows.length == 1){
		tabRow1.rows[0].cells[tabRow1.rows[0].cells.length-1].children[0].style.display="none";
	}
	else{
		tabRow1.rows[0].cells[tabRow1.rows[0].cells.length-1].children[0].style.display="";
	}
}

</script>
    <script type="text/javascript">
(function() {
   // your page initialization code here
   // the DOM will be available here

})();



function addItem2(){
	var tablebody2 = document.getElementById('items2');
	var tablebody2 = document.getElementById('items2');
	if(tablebody2.rows.length == 1){
		tablebody2.rows[0].cells[tablebody2.rows[0].cells.length-1].children[0].style.display="";
	}

    var tablebody2 = document.getElementById('items2');
	var iClone2 = tablebody2.children[0].cloneNode(true);
	for(var i = 0; i< iClone2.cells.length; i++){
		iClone2.cells[i].children[0].value ="";
	}

	tablebody2.appendChild(iClone2);
}

function rmv(){
	var tabRow2 = document.getElementById("items2");
	if(tabRow2.rows.length == 1){
		tabRow2.rows[0].cells[tabRow2.rows[0].cells.length-1].children[0].style.display="none";
	}
	else{
		tabRow2.rows[0].cells[tabRow2.rows[0].cells.length-1].children[0].style.display="";
	}
}
//Dirty forms code Galo Berlyn Garlejo ---->
var defpo = $("#POform").serialize();
var defnopo = $("#NOPOform").serialize();
var defserv = $("#ServiceForm").serialize();

// // To select the first tab
// $('.nav-tabs li:first-child a').tab('show'); 
// // To select the second tab
// $('.nav-tabs li:eq(1) a').tab('show'); 
// // 3rd naman
// $('.nav-tabs li:eq(2) a').tab('show'); 


	document.getElementById("po").onclick = function(){
		if(defnopo != $("#NOPOform").serialize() || defserv != $("#ServiceForm").serialize()){
		var x = confirm("Leave Tab without Saving?");
			if(x){
		
				document.getElementById("NOPOform").reset(); //reset form
				document.getElementById("ServiceForm").reset();
				document.getElementById("POform").reset();
				document.getElementById("nopo").setAttribute('data-toggle', "tab");
				document.getElementById("serv").setAttribute('data-toggle', "tab");
				document.getElementById("po").setAttribute('data-toggle', "tab");

			}else{
				document.getElementById("nopo").removeAttribute('data-toggle');
				document.getElementById("serv").removeAttribute('data-toggle');
				document.getElementById("po").removeAttribute('data-toggle');

				
			}
		}
	}
	document.getElementById("nopo").onclick = function(){
		if(defpo != $("#POform").serialize() || defserv != $("#ServiceForm").serialize()){
		var x = confirm("Leave Tab without Saving?");
			if(x){
		
				document.getElementById("POform").reset(); //reset form
				document.getElementById("ServiceForm").reset();
				document.getElementById("NOPOform").reset();
				document.getElementById("nopo").setAttribute('data-toggle', "tab");
				document.getElementById("serv").setAttribute('data-toggle', "tab");
				document.getElementById("po").setAttribute('data-toggle', "tab");


			}else{
				document.getElementById("nopo").removeAttribute('data-toggle');
				document.getElementById("serv").removeAttribute('data-toggle');
				document.getElementById("po").removeAttribute('data-toggle');

			}
			
	}
	}
	document.getElementById("serv").onclick = function(){
		if(defpo != $("#POform").serialize() || defnopo != $("#NOPOform").serialize()){
		var x = confirm("Leave Tab without Saving?");
			if(x){
		
				document.getElementById("POform").reset(); //reset form
				document.getElementById("NOPOform").reset();
				document.getElementById("ServiceForm").reset();
				document.getElementById("nopo").setAttribute('data-toggle', "tab");
				document.getElementById("serv").setAttribute('data-toggle', "tab");
				document.getElementById("po").setAttribute('data-toggle', "tab");

			}else{
				document.getElementById("nopo").removeAttribute('data-toggle');
				document.getElementById("po").removeAttribute('data-toggle');
				document.getElementById("serv").removeAttribute('data-toggle');

			}

	}
	}

</script>
</html>