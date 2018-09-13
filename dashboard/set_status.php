<?php
include "../shared/authorization.php";
include "../shared/connection.php";
$request_id = $_GET['request_id'];
$heading = "SELECT rs_no from request_slip where id='$request_id'";
$headingQry = mysqli_query($conn,$heading);
$headingArr = mysqli_fetch_array($headingQry);

if(isset($_POST['submit'])){
	$rs_status = $_POST['rs_status'];
	$update = "UPDATE request_slip set status='$rs_status' where id='$request_id'";
	$updateQry = mysqli_query($conn,$update);
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Set RS No.</title>
</head>
<body>
<form method='POST'>
Reqeust Slip Status: 
<select name='rs_status'>
	<option value='Pending'> Pending </option> 
	<option value='Cancelled'> Cancelled </option> 
	<option value='For PO'> For PO </option> 
	<option value='Delivered'> Delivered </option> 
	<option value='In-Progress'> In-Progress </option> 
	<option value='Completed'> Completed </option> 
</select>
<input type='submit' name='submit'>
</form>
</body>
</html>