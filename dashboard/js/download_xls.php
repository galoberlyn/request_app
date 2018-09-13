<?php
$request_id = $_GET['request_id'];
include '../shared/auth.php';
include '../shared/connection.php';
/** Set default timezone (will throw a notice otherwise) */
date_default_timezone_set('Asia/Kolkata');

// include PHPExcel
require('../dashboard/PHPExcel.php');

// create new PHPExcel object
$objPHPExcel = new PHPExcel;

// set default font
$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');

// set default font size
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

// create the writer
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

 

/**

 * Define currency and number format.

 */

// currency format, € with < 0 being in red color
$currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';

// number format, with thousands separator and two decimal points.
$numberFormat = '#,#0.##;[Red]-#,#0.##';

 

// writer already created the first sheet for us, let's get it
$objSheet = $objPHPExcel->getActiveSheet();

// rename the sheet
$objSheet->setTitle('Summarized Report');

 

// let's bold and size the header font and write the header
// as you can see, we can specify a range of cells, like here: cells from A1 to A4
$objSheet->getStyle('A1:A7')->getFont()->setBold(true)->setSize(12);
$objSheet->getStyle('B10:I10')->getFont()->setBold(true)->setSize(12);

 
$query_check = "SELECT rs_no,type, requested_by,date_needed,purpose,ConcernedOffice,status from request_slip where request_slip.id = '$request_id'";
$query_result = mysqli_query($conn, $query_check) or die(mysqli_error($conn));
$query_check_arr = mysqli_fetch_array($query_result);
//query na kung type
if($query_check_arr['type'] == 'PO'){
	// $POquery = "SELECT * FROM request_slip INNER JOIN purchase_order ON request_slip.id = request_id Inner JOIN itemspo ON purchase_order.id = poid where request_slip.id='$request_id'";
	$POquery = "SELECT description,quantity,amount,remarks,date_complete,status, supplier_po, Location FROM request_slip INNER JOIN purchase_order ON request_slip.id = request_id Inner JOIN itemspo ON purchase_order.id = poid where request_slip.id = '$request_id';";
	$POquery_result = mysqli_query($conn, $POquery) or die(mysqli_error($conn));

	$objSheet->getCell('B10')->setValue('Item Name');
	$objSheet->getCell('C10')->setValue('Quantity');
	$objSheet->getCell('D10')->setValue('Date Delivered');
	$objSheet->getCell('E10')->setValue('Amount');
	$objSheet->getCell('F10')->setValue('Item Status');
	$objSheet->getCell('G10')->setValue('Remarks');
	$objSheet->getCell('H10')->setValue('Supplier');
	$objSheet->getCell('I10')->setValue('Location');
	$objSheet->getCell('A9')->setValue('Items');

	for($i=1; $table = mysqli_fetch_array($POquery_result); $i++){
		
		$objSheet->getCell('B1'.$i)->setValue($table['description']);
		$objSheet->getCell('C1'.$i)->setValue($table['quantity']);
		$objSheet->getCell('D1'.$i)->setValue($table['date_complete']); //date delivered dapat
		$objSheet->getCell('E1'.$i)->setValue($table['amount']); 
		$objSheet->getCell('F1'.$i)->setValue($table['status']);
		$objSheet->getCell('G1'.$i)->setValue($table['remarks']);
		$objSheet->getCell('H1'.$i)->setValue($table['supplier_po']);
		$objSheet->getCell('I1'.$i)->setValue($table['Location']);
	}
}else if($query_check_arr['type']=='Service'){
	$POquery = "SELECT * FROM `services` inner join request_slip on id=requestID where requestID='$request_id';";
	$POquery_result = mysqli_query($conn, $POquery) or die(mysqli_error($conn));

	$objSheet->getCell('B10')->setValue('Service Name');
	$objSheet->getCell('C10')->setValue('Canceled');
	$objSheet->getCell('D10')->setValue('Date Completed');
	$objSheet->getCell('E10')->setValue('Remarks');
	$objSheet->getCell('F10')->setValue('Service Provider');
	$objSheet->getCell('A9')->setValue('Services');

	for($i=1; $table = mysqli_fetch_array($POquery_result); $i++){
		
		$objSheet->getCell('B1'.$i)->setValue($table['description']);
		$objSheet->getCell('C1'.$i)->setValue($table['status']);
		$objSheet->getCell('D1'.$i)->setValue($table['date_completed']); //date delivered dapat
		$objSheet->getCell('E1'.$i)->setValue($table['remarks']); 
		$objSheet->getCell('F1'.$i)->setValue($table['service_provider']);
	}
}else if($query_check_arr['type']=='ItemsNoPO'){
	$POquery = "SELECT * FROM `itemsnotpo` inner join request_slip on request_slip.id=request_slip_no where request_slip_no='$request_id'";
	$POquery_result = mysqli_query($conn, $POquery) or die(mysqli_error($conn));
	$objSheet->getCell('A9')->setValue('Items');

	$objSheet->getCell('B10')->setValue('Item Name');
	$objSheet->getCell('C10')->setValue('Quantity');
	$objSheet->getCell('D10')->setValue('Date Delivered');
	$objSheet->getCell('E10')->setValue('Amount');
	$objSheet->getCell('F10')->setValue('Item Status');
	$objSheet->getCell('G10')->setValue('Remarks');
	$objSheet->getCell('H10')->setValue('Supplier');

	for($i=1; $table = mysqli_fetch_array($POquery_result); $i++){
		
		$objSheet->getCell('B1'.$i)->setValue($table['description']);
		$objSheet->getCell('C1'.$i)->setValue($table['quantity']);
		$objSheet->getCell('D1'.$i)->setValue($table['date_accomplished']); //date delivered dapat
		$objSheet->getCell('E1'.$i)->setValue($table['amount']); 
		$objSheet->getCell('F1'.$i)->setValue($table['status']);
		$objSheet->getCell('G1'.$i)->setValue($table['remarks']);
		$objSheet->getCell('H1'.$i)->setValue($table['supplier']);
	}
}
// write header

$objSheet->getCell('A1')->setValue('Request Number');
$objSheet->getCell('A2')->setValue('Requested By:');
$objSheet->getCell('A3')->setValue('Date Needed');
$objSheet->getCell('A4')->setValue('Purpose of Request');
$objSheet->getCell('A5')->setValue('Type Of Request');
$objSheet->getCell('A6')->setValue('Care Of');
$objSheet->getCell('A7')->setValue('Status');




// we could get this data from database, but here we are writing for simplicity

$objSheet->getCell('B1')->setValue($query_check_arr['rs_no']);
$objSheet->getCell('B2')->setValue($query_check_arr['requested_by']);
$objSheet->getCell('B3')->setValue($query_check_arr['date_needed']);
$objSheet->getCell('B4')->setValue($query_check_arr['purpose']);
$objSheet->getCell('B5')->setValue($query_check_arr['type']);
$objSheet->getCell('B6')->setValue($query_check_arr['ConcernedOffice']);
$objSheet->getCell('B7')->setValue($query_check_arr['status']);





// autosize the columns
$objSheet->getColumnDimension('A')->setAutoSize(true);
$objSheet->getColumnDimension('B')->setAutoSize(true);
$objSheet->getColumnDimension('C')->setAutoSize(true);
$objSheet->getColumnDimension('D')->setAutoSize(true);
$objSheet->getColumnDimension('E')->setAutoSize(true);
$objSheet->getColumnDimension('F')->setAutoSize(true);
$objSheet->getColumnDimension('G')->setAutoSize(true);


//Setting the header type
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=". $query_check_arr['rs_no'] . "_request_slip.xlsx");
header('Cache-Control: max-age=0');

$objWriter->save('php://output');

/* If you want to save the file on the server instead of downloading, replace the last 4 lines by 
	$objWriter->save('test.xlsx');
*/

?>
