<?php
// include '../shared/authorization.php';
include '../shared/connection.php';
if(isset($_GET['request_id'])){
$request_id = json_decode($_GET['request_id']);;


/** Set default timezone (will throw a notice otherwise) */
date_default_timezone_set('Asia/Kolkata');

// include PHPExcel
require('../dashboard/PHPExcel.php');

$length = sizeof($request_id);
$fileArr = array();

//Generate Excel files for the requested RequestNumbers
for($numReq_id = 0; $numReq_id < $length; $numReq_id++){


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

 
$query_check = "SELECT rs_no,type, requested_by,date_needed,purpose,ConcernedOffice,status from request_slip where request_slip.id = ?";
$querStmt = $conn -> prepare($query_check);
$querStmt -> bind_param("s",$request_id[$numReq_id]);
$querStmt -> execute();
$query_check_arr = $querStmt -> get_result() -> fetch_array(MYSQLI_ASSOC);

//query na kung type
if($query_check_arr['type'] == 'PO'){
	// $POquery = "SELECT * FROM request_slip INNER JOIN purchase_order ON request_slip.id = request_id Inner JOIN itemspo ON purchase_order.id = poid where request_slip.id='$request_id'";
	$POquery = "SELECT description,quantity,amount,remarks,date_complete,status, supplier_po, Location FROM request_slip INNER JOIN purchase_order ON request_slip.id = request_id Inner JOIN itemspo ON purchase_order.id = poid where request_slip.id = ?;";
	$poQstmt = $conn -> prepare($POquery);
	$poQstmt -> bind_param("s",$request_id[$numReq_id]);
	$poQstmt -> execute();
	$POquery_result = $poQstmt -> get_result();

	$objSheet->getCell('B10')->setValue('Item Name');
	$objSheet->getCell('C10')->setValue('Quantity');
	$objSheet->getCell('D10')->setValue('Date Delivered');
	$objSheet->getCell('E10')->setValue('Amount');
	$objSheet->getCell('F10')->setValue('Item Status');
	$objSheet->getCell('G10')->setValue('Remarks');
	$objSheet->getCell('H10')->setValue('Supplier');
	$objSheet->getCell('I10')->setValue('Location');
	$objSheet->getCell('A9')->setValue('Items');

	for($i=1; $table = $POquery_result -> fetch_array(MYSQLI_ASSOC); $i++){
		
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
	$POquery = "SELECT * FROM `services` inner join request_slip on id=requestID where requestID=?;";
	$poQstmt = $conn -> prepare($POquery);
	$poQstmt -> bind_param("s",$request_id[$numReq_id]);
	$poQstmt -> execute();
	$POquery_result = $poQstmt -> get_result();

	$objSheet->getCell('B10')->setValue('Service Name');
	$objSheet->getCell('C10')->setValue('Canceled');
	$objSheet->getCell('D10')->setValue('Date Completed');
	$objSheet->getCell('E10')->setValue('Remarks');
	$objSheet->getCell('F10')->setValue('Service Provider');
	$objSheet->getCell('A9')->setValue('Services');

	for($i=1; $table = $POquery_result -> fetch_array(MYSQLI_ASSOC); $i++){
		
		$objSheet->getCell('B1'.$i)->setValue($table['description']);
		$objSheet->getCell('C1'.$i)->setValue($table['status']);
		$objSheet->getCell('D1'.$i)->setValue($table['date_completed']); //date delivered dapat
		$objSheet->getCell('E1'.$i)->setValue($table['remarks']); 
		$objSheet->getCell('F1'.$i)->setValue($table['service_provider']);
	}
}else if($query_check_arr['type']=='ItemsNoPO'){
	$POquery = "SELECT * FROM `itemsnotpo` inner join request_slip on request_slip.id=request_slip_no where request_slip_no=?";
	$poQstmt = $conn -> prepare($POquery);
	$poQstmt -> bind_param("s",$request_id[$numReq_id]);
	$poQstmt -> execute();
	$POquery_result = $poQstmt -> get_result();


	$objSheet->getCell('A9')->setValue('Items');

	$objSheet->getCell('B10')->setValue('Item Name');
	$objSheet->getCell('C10')->setValue('Quantity');
	$objSheet->getCell('D10')->setValue('Date Delivered');
	$objSheet->getCell('E10')->setValue('Amount');
	$objSheet->getCell('F10')->setValue('Item Status');
	$objSheet->getCell('G10')->setValue('Remarks');
	$objSheet->getCell('H10')->setValue('Supplier');

	for($i=1; $table = $POquery_result -> fetch_array(MYSQLI_ASSOC); $i++){
		
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

/**
//Setting the header type
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=". $query_check_arr['rs_no'] . "_request_slip.xlsx");
header('Cache-Control: max-age=0');

$objWriter->save('php://output'); 
**/
	$objWriter->save(str_replace(__FILE__,"excelFiles/".$query_check_arr['rs_no'] . "_request_slip.xlsx",__FILE__));
	array_push($fileArr,"./excelFiles/".$query_check_arr['rs_no']. "_request_slip.xlsx");

}
/* If you want to save the file on the server instead of downloading, replace the last 4 lines by 
	$objWriter->save('test.xlsx');
*/

//Creating a ZipFile For the Excel
$zip = new ZipArchive;
$filename = './excelFiles/GeneratedRequestSlip.zip';

$zip->open($filename, ZipArchive::CREATE);


foreach ($fileArr as $file) {
  $zip->addFile($file,substr($file,13));
}
$zip->close();


$zipFile = basename($filename);


if(file_exists($filename)){
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=$zipFile");
    header("Content-Length: " . filesize($filename));

    readfile($filename);
}



//Deleting the Files in excelFilesFolder
	//The name of the folder.
	$folder = 'excelFiles';

	//Get a list of all of the file names in the folder.
	$files = glob($folder . '/*');

	//Loop through the file list.
	foreach($files as $file){
	    //Make sure that this is a file and not a directory.
	    if(is_file($file)){
	        //Use the unlink function to delete the file.
	        unlink($file);
	    }
	}

}
?>
