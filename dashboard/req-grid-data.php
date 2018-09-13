<?php
/* Database connection start */
include "../shared/authorization.php";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scis_requisition_db";

$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
/* Database connection end */


// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;
//echo var_dump($requestData['order'][0]['column']);
$columns = array( 
// datatable column index  => database column name
	0 =>'rs_no', 
	1 => 'date_needed',
	2 => 'purpose',
    3 => 'requested_by',
    4 => 'status',
    5 => 'type'
);

// getting total number records without any search
$sql = "SELECT rs_no, date_needed, purpose, requested_by, status, type "; 
$sql .= " FROM request_slip ";
$nosearch_query=mysqli_query($conn, $sql) or die(mysqli_error($conn));
$totalData = mysqli_num_rows($nosearch_query);

$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT 
    rs.id,
    rs_no,
    requested_by,
    date_needed,
    purpose,
    rs.status,
    type,
    srv.description as serviceDes,
    po.description as poDes,
    npo.description as npoDes FROM
    request_slip rs
        LEFT JOIN
    services srv ON rs.id = srv.requestID
        LEFT JOIN
    purchase_order pur ON rs.id = pur.request_id
        LEFT JOIN
    itemspo po ON po.poid = pur.id
        LEFT JOIN
    itemsnotpo npo ON npo.request_slip_no = rs.id";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

    $sql .= " WHERE rs_no LIKE '%?%' "; 
    $sql .= " OR requested_by LIKE '%?%' ";
    $sql .= " OR purpose LIKE '%?%' ";
    $sql .= " OR rs.status LIKE '%?%' ";
    $sql .= " OR rs.type LIKE '%?%' ";
    $sql .= " OR rs.date_needed LIKE '%?%' ";
    $sql .= " OR srv.description LIKE '%?%' ";
    $sql .= " OR srv.remarks LIKE '%?%' ";
    $sql .= " OR srv.service_provider LIKE '%?%' ";
    $sql .= " OR po.description LIKE '%?%' ";
    $sql .= " OR po.remarks LIKE '%?%' ";
    $sql .= " OR po.supplier_po LIKE '%?%' ";
    $sql .= " OR npo.description LIKE '%?%' ";
    $sql .= " OR npo.supplier LIKE '%?%' ";
    $sql .= " OR npo.remarks LIKE '%?%' group by rs_no ";
	
}
$stmt=$conn->prepare($sql);
$stmt->execute();
mysqli_stmt_store_result($stmt);
$totalFiltered = mysqli_stmt_num_rows($stmt); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
// $totalData = $totalFiltered;
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($conn, $sql) or die(mysqli_error($conn));

$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 

	$nestedData[] = $row["rs_no"];
	$nestedData[] = $row["date_needed"];
	$nestedData[] = $row["purpose"];
	$nestedData[] = $row["requested_by"];
    $nestedData[] = $row["status"];
    $nestedData[] = $row["type"];
    
	$data[] = $nestedData;
    
}



$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format
//echo var_dump(json_encode($json_data));
?>
