<?php
// include '../shared/authorization.php';
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


	$POquery = "SELECT itemsnotpo.description, quantity, date_accomplished, amount, serial_number, model from itemsnotpo 
				union 
				select itemspo.description, quantity, date_complete, amount, serial_number, model from itemspo order by 1";
	$poQstmt = $conn -> prepare($POquery);
	$poQstmt -> execute();
	$POquery_result = $poQstmt -> get_result();

	$objSheet->getCell('B10')->setValue('Item Name');
	$objSheet->getCell('C10')->setValue('Quantity');
	$objSheet->getCell('D10')->setValue('Date Delivered');
	$objSheet->getCell('E10')->setValue('Amount');
	$objSheet->getCell('F10')->setValue('Serial Number');
	$objSheet->getCell('G10')->setValue('Model');
	$objSheet->getCell('A9')->setValue('Items');

	for($i=1; $table = $POquery_result -> fetch_array(MYSQLI_ASSOC); $i++){
		
		$objSheet->getCell('B1'.$i)->setValue($table['description']);
		$objSheet->getCell('C1'.$i)->setValue($table['quantity']);
		$objSheet->getCell('D1'.$i)->setValue($table['date_accomplished']); //date delivered dapat
		$objSheet->getCell('E1'.$i)->setValue($table['amount']); 
		$objSheet->getCell('F1'.$i)->setValue($table['serial_number']);
		$objSheet->getCell('G1'.$i)->setValue($table['model']);
	}



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
header("Content-Disposition: attachment;filename=All_Reports.xlsx");
header('Cache-Control: max-age=0');

$objWriter->save('php://output');

/* If you want to save the file on the server instead of downloading, replace the last 4 lines by 
	$objWriter->save('test.xlsx');
*/

?>
