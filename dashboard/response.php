
<?php
	//include connection file 
	include '../shared/connection.php';
	include '../shared/authorization.php';
	 
	// initilize all variable
	$params = $columns = $totalRecords = $data = array();

	$params = $_REQUEST;

	//define index of column
	$columns = array( 
		0 => 'id',
		1 => 'rs_no',
		2 => 'date_needed', 
		3 => 'purpose',
		4 => 'requested_by',
		5 => 'status',
		6 => 'type'

	);

	$where = $sqlTot = $sqlRec = "";

	// check search value exist
	if( !empty($params['search']['value']) ) {   

		$where .=" WHERE ";
		$where .=" ( rs_no LIKE '%".$params['search']['value']."%' ";    
		$where .=" OR rs.date_needed LIKE '%".$params['search']['value']."%' ";
		$where .=" OR srv.description LIKE '%".$params['search']['value']."%' ";
		$where .=" OR po.description LIKE '%".$params['search']['value']."%' ";
		$where .=" OR npo.description LIKE '%".$params['search']['value']."%' ";
		$where .=" OR rs.requested_by LIKE '%".$params['search']['value']."%' ";
		$where .=" OR rs.status LIKE '%".$params['search']['value']."%' ";
		$where .=" OR rs.purpose LIKE '%".$params['search']['value']."%' ";
		$where .=" OR rs.type LIKE '%".$params['search']['value']."%' )";


	}

	// getting total number records without any search
	$sql = "SELECT distinct rs.id, rs.rs_no, rs.date_needed, rs.purpose, rs.requested_by, rs.status, rs.type FROM request_slip rs LEFT JOIN
    services srv ON rs.id = srv.requestID
        LEFT JOIN
    purchase_order pur ON rs.id = pur.request_id
        LEFT JOIN
    itemspo po ON po.poid = pur.id
        LEFT JOIN
    itemsnotpo npo ON npo.request_slip_no = rs.id";
	$sqlTot .= $sql;
	$sqlRec .= $sql;
	
	//concatenate search sql if value exist
	if(isset($where) && $where != '') {

		$sqlTot .= $where;
		$sqlRec .= $where;
	}


 	$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]."   ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";

	$queryTot = mysqli_query($conn, $sqlTot) or die("database error:". mysqli_error($conn));


	$totalRecords = mysqli_num_rows($queryTot);

	$queryRecords = mysqli_query($conn, $sqlRec) or die("error to fetch requests data");

	//iterate on results row and create new index array of data
	while( $row = mysqli_fetch_row($queryRecords) ) { 
		$data[] = $row;
	}	

	$json_data = array(
			"draw"            => intval( $params['draw'] ),   
			"recordsTotal"    => intval( $totalRecords ),  
			"recordsFiltered" => intval($totalRecords),
			"data"            => $data   // total data array
			);

	echo json_encode($json_data);  // send data as json format
?>
	