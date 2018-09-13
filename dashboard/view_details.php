<?php
include "../shared/authorization.php";
include "../shared/connection.php";

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

if (!$_GET){
  header("Location: error.php");
}
if(isset($_GET['request_id'])){
  $request_id= $_GET['request_id'];
  $requestSlip = "SELECT * from request_slip where id=?";
  $requestSlipQry=$conn->prepare($requestSlip);
  $requestSlipQry->bind_param('i', $request_id);
  $requestSlipQry->execute();
  mysqli_stmt_store_result($requestSlipQry);
  $reqRows = mysqli_stmt_num_rows($requestSlipQry);
  // var_dump($reqRows);
  $requestSlipQry->execute();
  $reqSlipArr=$requestSlipQry->get_result()->fetch_array(MYSQLI_ASSOC);
  $requestSlipQry->close();

if($reqRows > 0){
if($reqSlipArr['type'] == 'PO'){
  $po = "SELECT * from purchase_order where request_id =?";
  $poQry = $conn->prepare($po);
  $poQry->bind_param('i', $request_id);
  $poQry->execute();
  $poArr=$poQry->get_result()->fetch_array(MYSQLI_ASSOC);
  

  $items = "SELECT iditemspo,description,supplier_po,quantity,Location,itemspostatus,date_complete,remarks, iditemspo, qty_delivered_po, serial_number, model, amount from itemspo where poid = ?";
  $itemsQry=$conn->prepare($items);
  $itemsQry->bind_param('i', $poArr['id']);
  // var_dump($poArr['id']);


}
else if($reqSlipArr['type'] == 'ItemsNoPO'){
  $items = "SELECT id,description,quantity,supplier,date_accomplished,(quantity*supplier) as amount,itemStatus,remarks, qty_delivered_nopo, serial_number, model from itemsnotpo where request_slip_no = ?";
  $itemsQry=$conn->prepare($items);
  $itemsQry->bind_param('i', $request_id);

}
else{
  $items = "SELECT idServices,description,service_provider,status,date_completed,remarks from services where requestID = ?";
  $itemsQry=$conn->prepare($items);
  $itemsQry->bind_param('i', $request_id);
}
$itemsQry->execute() or die($itemsQry->error);

?>


<!DOCTYPE html>
<html>
<head>
  <title>Details of Request</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap -->
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
     <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/font-awesome.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="../js/ajaxReq.js"></script>
</head>
<body onload = 'prepareAmount()'>
  <?php
                include "../header.php";
    ?>
<div>
<section class="menu-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse ">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a href="dashboard.php"><i class="glyphicon glyphicon-home"></i> Dashboard</a></li>
                            <li><a href="addrequest.php"><i class="glyphicon glyphicon-plus"></i> Add New Request</a></li>
                            <li><a data-toggle="modal" data-target="#changepass"><i class="glyphicon glyphicon-lock"></i> Change password</a></li>
                            <li><a href="aboutUs.php"><i class="fa fa-info-circle"></i> About Us</a></li>
                            <li><a href="../logout.php">Log out</a></li>
                            

                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>
    </div>
    
    <div class="wrapper">
        <div class="content">
            <div class="details">
                <div class="requester">
<form method='POST' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);?>">
<div class="row">

                    <div class="col-md-12">
                        <h4 class="page-head-line">Request Information</h4>
                        <h4> <?php echo 'Request was created at (y-m-d): '. $reqSlipArr['created_at'];?> </h4> </div>
                </div>
                    
        <div class="row">
            <table style='border-width: 7px'; class='table table-striped table-bordered table-hover table-condensed table-responsive'>
            <tr>
                <th><h4><strong>Items For Request No.</strong></h4></th>
                <td><h4><?php echo $reqSlipArr['rs_no'] ?></h4></td>
            </tr>
            <tr>
                <th><h4><strong>Requested By:</strong></h4></th>
                <td><h4><?php echo $reqSlipArr['requested_by'] ?></h4></td>
            </tr>
            <tr>
                <th><h4><strong>Date Needed:</strong></h4></th>
                <td><h4 id ='dateNeeded'><?php echo $reqSlipArr['date_needed'] ?></h4></td>
            </tr>
            <tr>
                <th><h4><strong>Time Needed:</strong></h4></th>
                <td><h4 id ='timeNeeded'><?php echo $reqSlipArr['time_needed'] ?></h4></td>
            </tr>
            <tr>
                <th><h4 ><strong>Purpose</strong></h4></th>
                <td><span id="purpose"><h4><?php echo $reqSlipArr['purpose'] ?></h4></span></td>
            </tr>
            <tr>
                <th><h4><strong>Type:</strong></h4></th>
                <td><h4><strong><?php 
                      if($reqSlipArr['type'] == 'PO'){
                        echo "<h4>For Purchase Order</h4>";
                      }
                      else if($reqSlipArr['type'] == 'ItemsNoPO'){
                        echo "<h4>Not For Purchase Order</h4>";
                      }
                      else {
                        echo "Service";
                      } 
                                  ?>
                                  </strong></h4></td>
            </tr>
            <tr>
                <th><h4><strong>Status:</strong></h4></th>
                <td><span id ='slipStatus'><h4 id='status_edit'><?php echo $reqSlipArr['status'] ?></h4></td>
            </tr>
   <?php 
        if ($reqSlipArr['type'] == 'PO') { 
    ?>
            <tr>
                <th><h4><strong>Total Amount</strong></h4></th>
                <td><span><h4><p id ="totalAmt"></p></h4></span></td>
            </tr>
            <?php } ?>
                    

  <?php
  if($reqSlipArr['type'] == 'ItemsNoPO' || $reqSlipArr['type'] == 'Service'){
            echo "<tr>";
            echo "<th><h4><strong>Care Of:</strong></h4></th>";
            echo "<td><span id='careOF'><h4>" . $reqSlipArr['ConcernedOffice'] . "</h4><span></td>";
            echo "</tr>";
            echo "</table>";
            echo "</div>";

  }
  else{
        echo "<tr>";
        echo "<th><h4><strong>Purchase Order Number</strong></h4></th>";
        echo "<td><span id='poNum'><h4>" .$poArr['po_no']. "</h4><span></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<th><h4><strong>Date of Purchase Order:</strong></h4></th>";
        echo "<td><span id='poDate'><h4>" .$poArr['date_of_po']. "</h4><span></td>";
        echo "</tr>";
      
        echo "<tr>";
        echo "<th><h4><strong>Supplier:</strong></h4></th>";
        echo "<td><span id='poSupp'><h4>" .$poArr['supplier']. "</h4><span></td>";
        echo "</tr>";
        echo "</table>";
        echo "</div>";
      
  }
  $inc = 0;
  ?>
            
            
            
            </form>

                    
                    <br><br>
<?php
if($reqSlipArr['status']=='Canceled'){
  $conn->close();
  include "../shared/connection.php";
  echo "<h1> This Request is now Canceled </h1>";
  echo "<form method ='POST'  onsubmit='return delConfirm(\"".$request_id."\")' >";
  echo "<td><button type='submit' class ='btn btn-danger' name ='requestDel' value='". $request_id . "'>Delete Request</button></td></tr></form>";
  echo "<script>
  function delConfirm(rsnNum){
    var conf = confirm('Are you sure You want to delete?');
    if( conf == true){

        return true;
    }
    else{
        
        return false;
    }
  }</script>"; 

  if(isset($_POST['requestDel'])){
  if($reqSlipArr['type'] == 'ItemsNoPO'){
        $delQueryStmt="DELETE FROM itemsnotpo WHERE request_slip_no = ?";
        $delQuerybind=$conn->prepare($delQueryStmt);
        $delQuerybind->bind_param('s', $reqSlipArr['id']);
        $delQuerybind->execute();
    }
    else if($reqSlipArr['type'] == 'PO'){
        $queryStmt1 = "SELECT * FROM purchase_order where request_id = ?";
        $query1=$conn->prepare($queryStmt1);
        $query1->bind_param('s', $reqSlipArr['id']);
        $query1->execute();
        $poArray=$query1->get_result()->fetch_array(MYSQLI_ASSOC);
        

        $poID = $poArray['id'];
        $delPO = "DELETE FROM itemspo WHERE poid = ?";
        $delPOres=$conn->prepare($delPO);
        $delPOres->bind_param('s', $poID);
        $delPOres->execute();
        $delPOres->close();


        $delPOstmt="DELETE FROM purchase_order WHERE request_id = ?";
        $delPOstmtres=$conn->prepare($delPOstmt);
        $delPOstmtres->bind_param('s', $reqSlipArr['id']);
        $delPOstmtres->execute();
        $delPOstmtres->close();
    }
    else{
        $delservstmt="DELETE FROM services WHERE requestID= ?";
        $delservstmtres=$conn->prepare($delservstmt);
        var_dump($conn->error);
        $delservstmtres->bind_param('s', $reqSlipArr['id']);
        $delservstmtres->execute();
        $delservstmtres->close();
       
    }
    $delrequest = "DELETE FROM request_slip WHERE id = ?";
    $delrequestres=$conn->prepare($delrequest);
    $delrequestres->bind_param('s', $reqSlipArr['id']);
    $delrequestres->execute();
    $delrequestres->close();

    $deldeliver ="DELETE FROM delivered_items where rs_item_no=?";
    $deldeliverres=$conn->prepare($deldeliver);
    $deldeliverres->bind_param('s', $reqSlipArr['id']);
    $deldeliverres->execute();
    $deldeliverres->close();
         echo "<html><head><script> var div = document.createElement('div');
              div.setAttribute('class', 'alert');
              var span = document.createElement('button');
              span.setAttribute('class', 'closebtn');
              span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
              var spanTxt = document.createTextNode('Done')
              span.appendChild(spanTxt);
              var pTxt = document.createTextNode('Successfully Deleted!');
              var p = document.createElement('p');
              p.appendChild(pTxt);
              p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
              div.appendChild(span);
              
              document.body.appendChild(div); setTimeout(function(){location.href='dashboard.php'} , 1100); </script></head></html>";
}
  exit();

}else if($reqSlipArr['status']=='Completed'){
  $conn->close();
  include "../shared/connection.php";
  echo "<h1> This Request is now Completed </h1>";
  echo "<form method ='POST'  onsubmit='return delConfirm(\"".$request_id."\")' >";
  echo "<td><button type='submit' class ='btn btn-danger' name ='requestDel' value='". $request_id . "'>Delete Request</button></td></tr></form>";
  echo "<script>
  function delConfirm(rsnNum){
    var conf = confirm('Are you sure You want to delete?');
    if( conf == true){

        return true;
    }
    else{
        
        return false;
    }
  }</script>"; 

  if(isset($_POST['requestDel'])){
  if($reqSlipArr['type'] == 'ItemsNoPO'){
        $delQueryStmt="DELETE FROM itemsnotpo WHERE request_slip_no = ?";
        $delQuerybind=$conn->prepare($delQueryStmt);
        $delQuerybind->bind_param('s', $reqSlipArr['id']);
        $delQuerybind->execute();
    }
    else if($reqSlipArr['type'] == 'PO'){
        $queryStmt1 = "SELECT * FROM purchase_order where request_id = ?";
        $query1=$conn->prepare($queryStmt1);
        $query1->bind_param('s', $reqSlipArr['id']);
        $query1->execute();
        $poArray=$query1->get_result()->fetch_array(MYSQLI_ASSOC);
        

        $poID = $poArray['id'];
        $delPO = "DELETE FROM itemspo WHERE poid = ?";
        $delPOres=$conn->prepare($delPO);
        $delPOres->bind_param('s', $poID);
        $delPOres->execute();
        $delPOres->close();


        $delPOstmt="DELETE FROM purchase_order WHERE request_id = ?";
        $delPOstmtres=$conn->prepare($delPOstmt);
        $delPOstmtres->bind_param('s', $reqSlipArr['id']);
        $delPOstmtres->execute();
        $delPOstmtres->close();
    }
    else{
        $delservstmt="DELETE FROM services WHERE requestID= ?";
        $delservstmtres=$conn->prepare($delservstmt);
        var_dump($conn->error);
        $delservstmtres->bind_param('s', $reqSlipArr['id']);
        $delservstmtres->execute();
        $delservstmtres->close();
       
    }
    $delrequest = "DELETE FROM request_slip WHERE id = ?";
    $delrequestres=$conn->prepare($delrequest);
    $delrequestres->bind_param('s', $reqSlipArr['id']);
    $delrequestres->execute();
    $delrequestres->close();

    $deldeliver ="DELETE FROM delivered_items where rs_item_no=?";
    $deldeliverres=$conn->prepare($deldeliver);
    $deldeliverres->bind_param('s', $reqSlipArr['id']);
    $deldeliverres->execute();
    $deldeliverres->close();
    echo "<html><head><script>alert('Successfully Deleted Request No:".$reqSlipArr['rs_no']."'); window.location = 'dashboard.php'</script></head></html>";
}
exit();
}

?>
<!-- ITEMS TABLE -->
<fieldset>
  <legend>
    Items  &emsp; <a id='additional_id' class='btn btn-primary' data-toggle="modal" data-target="#additional"><i class="glyphicon glyphicon-plus"></i> Add Additional Details</a></li>
  </legend>
</fieldset>
<!-- CAPITAL -->
<?php
if($reqSlipArr['type'] == 'PO'){
  $itemsQry->bind_result($iditemspo,$description, $supplier_po, $quantity, $location, $itemspostatus, $date_complete, $remarks, $iditemspo, $qty_delivered_po, $serial_number, $model, $amount);

    echo "<div>";
    echo "<table style='border-width: 7px'; class='table table-striped table-bordered table-hover table-condensed table-responsive'>";

    echo "<tr>";
    echo "<th><h4><strong>Item Name:</strong></h4></th>";
    echo "<th><h4><strong>Unit Cost:</strong></h4></th>";
    echo "<th><h4><strong>Quantity:</strong></h4></th>";
    echo "<th><h4><strong>Location:</strong></h4></th>";
    // echo "<th><h4><strong>Unit Price:</strong></h4></th>";
    echo "<th><h4><strong>Amount  </strong></h4></th>";
    echo "<th><h4><strong>Item Status:  </strong></h4></th>";
    echo "<th><h4><strong>Date Y/mm/D </strong></h4></th>";
    echo "<th><h4><strong>Remarks </strong></h4></th>";
    echo "<th><h4><strong>Quantity Delivered </strong></h4>";
    echo "<th><h4><strong>Serial Number </strong></h4>";
    echo "<th><h4><strong>Model </strong></h4>";
    echo "</tr>";
  while($itemsQry->fetch()){
    //update if delivered all
    // $numrows = count($itemspostatus);
    // $countrows = 0;
    // if($itemspostatus=='Delivered'){
    //   $countrows++;
    //   if($countrows==$numrows){
    //     $conn->close();
    //     include "../shared/connection.php";
    //     $update = "UPDATE request_slip set status='Completed' where id=?";
    //     $update_r=$conn->prepare($update);
    //     var_dump($conn->error);
    //     $update_r->bind_param('i', $request_id);
    //     $update_r->execute();
    //   }
    // } 
      
    echo "<tr id ='".$description."First'>";
    echo "<td><span id ='desc".$inc."'><h4>".$description."</h4></span></td>";
    echo "<td><span id ='supplierForPO".$inc."'><h4>".$supplier_po."</h4></span></td>";
    echo "<td><span id ='quantity".$inc."'><h4>".$quantity."</h4></span></td>";
    echo "<td><span id ='loc".$inc."'><h4>".$location."</h4></span></td>";
    // echo "<td><span id ='uPrice".$inc."'><h4>".$price."</h4></span></td>";
    echo "<td><h4><span class ='amount'>".$amount."</span></h4></td>";
    echo "<td><span id ='iStat".$inc."'><h4>".$itemspostatus."</h4></span></td>";
    echo "<td><h4><span id='date_deli".$inc."'>".$date_complete."</span></h4></td>";
    echo "<td><h4><span id ='remark".$inc."'>".$remarks."</span></h4></td>";
    echo "<td><h4><span id ='qty_delivered_po'>".$qty_delivered_po."</span></h4></td>";
    echo "<td><h4><span id ='serial_no'>".$serial_number."</span></h4></td>";
    echo "<td><h4><span id ='model'>".$model."</span></h4></td>";
    echo "</tr>";
    echo "<p style ='display:none' id = 'save".$inc."'>".$iditemspo."</p>";

    $inc++;
  }
  echo "</div>";
  echo "</table>";
}
// PROGRAM
else if($reqSlipArr['type'] == 'ItemsNoPO'){
  $itemsQry->bind_result($id,$description, $quantity, $supplier, $date_accomplished, $amount, $itemStatus, $remarks, $qty_delivered_nopo, $serial_number, $model);

    echo "<div>"; 
    echo "<table style='border-width: 7px'; class='table table-striped table-bordered table-hover table-condensed table-responsive'>";

    echo "<tr>";
    echo "<th><h4><strong>Item Name:</strong></h4></th>";
    echo "<th><h4><strong>Quantity:</strong></h4></th>";
    echo "<th><h4><strong>Supplier:</strong></h4></th>";
    echo "<th><h4><strong>Date</strong></h4></th>";
    echo "<th><h4><strong>Amount:</strong></h4></th>";
    echo "<th><h4><strong>Item Status:</strong></h4></th>";
    echo "<th><h4><strong>Remarks:</strong></h4></th>";
    echo "<th><h4><strong>Quantity Delivered</strong>";
    echo "<th><h4><strong>Serial Number</strong>";
    echo "<th><h4><strong>Model</strong>";
    echo "</tr>";

    while($itemsQry->fetch()){

    echo "<tr id ='".$description."First'>";
    echo "<td><span id ='desc".$inc."'><h4>".$description."</h4></span></td>";
    echo "<td><span id ='quantity".$inc."'><h4>".$quantity."</h4></span></td>";
    echo "<td><span id ='supplier".$inc."'><h4>".$supplier."</h4></span></td>";
    echo "<td><span id ='dateAccomp".$inc."'><h4>".$date_accomplished."</h4></span></td>";
    echo "<td><span id ='amount".$inc."'><h4 class = 'amount'>".$amount."</h4></span></td>";
    echo "<td><span id ='iStat".$inc."'><h4>".$itemStatus."</h4></span></td>";
    echo "<td><span id ='remark".$inc."'><h4>".$remarks."</h4></span></td>";
    echo "<td><span id ='qty_delivered_nopo'>".$qty_delivered_nopo."</h4></span></td>";
    echo "<td><span id ='qty_delivered_nopo'>".$serial_number."</h4></span></td>";
    echo "<td><span id ='qty_delivered_nopo'>".$model."</h4></span></td>";
    echo "</tr>";
    echo "<p style ='display:none' id = 'save".$inc."'>".$id."</p>";
    $inc++;
  }

  echo "</table>";    
  echo "</div>";
}
// OTHERS
else{
  $itemsQry->bind_result($idServices,$description, $service_provider, $status, $date_completed, $remarks);

    echo "<div>";
    echo "<table style='border-width: 7px'; class='table table-striped table-bordered table-hover table-condensed table-responsive'>";
    echo "<tr>";
    echo "<th><h4><strong>Service Name:  </strong></h4></th>";
    echo "<th><h4><strong>Service Provider:  </strong></h4></th>";
    echo "<th><h4><strong>Service Status  </strong></h4></th>";
    echo "<th><h4><strong>Date Completed:  </strong></h4></th>";
    echo "<th><h4><strong>Remarks:</strong></h4></th>";
    echo "</tr>";

    while($itemsQry->fetch()){

    echo "<tr>";
    echo "<td><h4><span id ='desc".$inc."'>".$description."</span></h4></td>";
    echo "<td><h4><span id ='sprovider".$inc."'>".$service_provider."</span></h4></td>";
    echo "<td><h4><span id ='iStat".$inc."'><h4>".$status."</h4></span></h4></td>";
    echo "<td><h4><span id ='dateComplete".$inc."'>".$date_completed."</span></h4></td>";
    echo "<td><h4><span id ='remark".$inc."'>".$remarks."</span></h4></td>";
    echo "</tr>";
    echo "<p style ='display:none' id ='save".$inc."'>".$idServices."</p>";    
    $inc++;
  }
  echo "</table>";   
  echo "</div>";

}

?>

<!-- MODAL PARA SA ADDITIONAL DETAILS -->
 <div class="modal fade" id="additional" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Additional Details</h4>
        </div>  
          <div class="modal-body">
          <label class='control-label'> Item Name : &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; </label>
         

          <form onsubmit="return confirm('Are you sure? this action cannot be undone');" method="post" class='form-horizontal' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);?>">
           <select id='dropdown_additional' name="items_additional">

            <?php 
            if($reqSlipArr['type']=='PO'){
              $optionselect="SELECT description FROM itemspo Where poid =  $poArr[id] AND (remarks is NULL OR Location is NULL)";
              $optionselect_r=mysqli_query($conn, $optionselect);
            }else if($reqSlipArr['type']=='ItemsNoPO'){
              $optionselect="SELECT description FROM itemsnotpo Where request_slip_no =  $request_id AND (remarks='None' OR amount is NULL)";
              $optionselect_r=mysqli_query($conn, $optionselect);
            }else{
              $optionselect="SELECT description from services where requestID=$request_id AND remarks is NULL";
              $optionselect_r=mysqli_query($conn, $optionselect);
            }
            
            while($option_array=mysqli_fetch_array($optionselect_r)){
            echo "<option value='".$option_array[0]."'>".$option_array[0]."</option><br>";
            }
           
            ?>

          </select>

          <?php if($reqSlipArr['type']=='ItemsNoPO'){ ?>
           <div class='row'>
            <label class='col-lg-4 control-label'>Date completed</label> 
            <div class='col-lg-7'>
            <input class='form-control' type="date" name="date_completed_nopo" maxlength="30" required>
            </div>
          </div>
          <div class='row'>
            <label class='col-lg-4 control-label'>Serial Number </label> 
            <div class='col-lg-7'>
            <input class='form-control' type="text" name="serial_no_po" maxlength="30" required>
            </div>
          </div>
          <div class='row'>
            <label class='col-lg-4 control-label'>Model </label> 
            <div class='col-lg-7'>
            <input class='form-control' type="text" name="model_no_po" maxlength="30" required>
            </div>
          </div>
          <div class='row'> 
            <!-- <label class='col-lg-4 control-label'>Amount of Item Selected (Peso) </label>  -->
            <div class='col-lg-7'>
            <!-- <input class='form-control' type="number" name="amount" max="999999" required> -->
            </div>
          </div><?php echo "<br>"; } ?>
          <?php if($reqSlipArr['type']=='PO'){ ?> 
          <div class='row'>
            <label class='col-lg-4 control-label'>Date completed</label> 
            <div class='col-lg-7'>
            <input class='form-control' type="date" name="date_completed_po" maxlength="30" required>
            </div>
          </div>
            <div class='row'>
            <label class='col-lg-4 control-label'>Serial Number </label> 
            <div class='col-lg-7'>
            <input class='form-control' type="text" name="serial_po" maxlength="30" required>
            </div>
          </div>
          <div class='row'>
            <label class='col-lg-4 control-label'>Model </label> 
            <div class='col-lg-7'>
            <input class='form-control' type="text" name="model_po" maxlength="30" required>
            </div>
          </div>
          <div class='row'>
             <label class='col-lg-4 control-label'>Location of PO: </label>
             <div class='col-lg-7'>
             <input class='form-control' type="text" name="location" maxlength="50" required><br>
             </div>
          </div>
      
          <?php echo "<br>"; } 
          else if($reqSlipArr['type']=='Service'){
            echo "<div class='row'>
            <label class='col-lg-4 control-label'>Date completed</label> 
            <div class='col-lg-7'>
            <input class='form-control' type='date' name='date_completed_serv' maxlength='30' required>
            </div>
            </div>";
            }
            ?> 
          <div class='row'>
          <label class='col-lg-4 control-label' required> Remarks: </label>
          <div class='col-lg-7'>
          <input class='form-control' type="text" name="itemremarks"><br>
          </div>
          </div>
          <input class='btn btn-primary' type="submit" name="addition_items"> 
          </form>
          </div>
      </div>
    </div>
  </div>

<?php
// print_r($_POST);

// Additional Item function
if(isset($_POST['addition_items'])){

  if($reqSlipArr['type']=='PO'){
    $items_additional=$_POST['items_additional'];
    $itemremarks=validateInput($_POST['itemremarks']);
    $location=validateInput($_POST['location']);
    // $itemprice = $_POST['itemprice'];
    $model_po = validateInput($_POST['model_po']);
    $serial_po = validateInput($_POST['serial_po']);

   
    $date_completed_po= date('Y-m-d', strtotime($_POST['date_completed_po']));


    $additionalPO = "UPDATE itemspo set Location=?, remarks=?, serial_number=?, model=?, date_complete=? where poid=? AND description=?";
    $additionalPO_r=$conn->prepare($additionalPO);
    $additionalPO_r->bind_param('sssssis', $location, $itemremarks, $serial_po, $model_po, $date_completed_po, $poArr['id'], $items_additional);
    $additionalPO_r->execute();

    var_dump($items_additional);
   echo "<html><head><script> var div = document.createElement('div');
              div.setAttribute('class', 'alert');
              var span = document.createElement('button');
              span.setAttribute('class', 'closebtn');
              span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
              var spanTxt = document.createTextNode('Done')
              span.appendChild(spanTxt);
              var pTxt = document.createTextNode('Successfully Added Detail!');
              var p = document.createElement('p');
              p.appendChild(pTxt);
              p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
              div.appendChild(span);
              
              document.body.appendChild(div); </script></head></html>";
    echo "<meta http-equiv='refresh' content='1.1'>";

  }else if($reqSlipArr['type']=='ItemsNoPO'){
    $itemremarks=validateInput($_POST['itemremarks']);
    $items_additional=validateInput($_POST['items_additional']);
    $amount=validateInput($_POST['amount']);
    $serial_no_po = validateInput($_POST['serial_no_po']);
    $model_no_po = validateInput($_POST['model_no_po']);
    $date_completed_nopo = date('Y-m-d', strtotime($_POST['date_completed_nopo']));

    $additionalNOPO = "UPDATE itemsnotpo set remarks=?, amount=?, serial_number=?, model=?, date_accomplished=? where request_slip_no=? AND description=?";
    $additionalNOPO_r=$conn->prepare($additionalNOPO);
    // var_dump($conn);
    $additionalNOPO_r->bind_param('sisssis', $itemremarks, $amount, $serial_no_po, $model_no_po, $date_completed_nopo, $request_id, $items_additional);
    $additionalNOPO_r->execute();

     echo "<html><head><script> var div = document.createElement('div');
              div.setAttribute('class', 'alert');
              var span = document.createElement('button');
              span.setAttribute('class', 'closebtn');
              span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
              var spanTxt = document.createTextNode('Done')
              span.appendChild(spanTxt);
              var pTxt = document.createTextNode('Successfully Added Detail!');
              var p = document.createElement('p');
              p.appendChild(pTxt);
              p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
              div.appendChild(span);
              
              document.body.appendChild(div); </script></head></html>";
    echo "<meta http-equiv='refresh' content='1.1'>";

  }else{
    $itemremarks=$_POST['itemremarks'];
    $items_additional=$_POST['items_additional'];
    $date_completed_serv=date('Y-m-d', strtotime($_POST['date_completed_serv']));
    $additionalservice = "UPDATE services set remarks=?, date_completed=? where requestID=? AND description=?";
    $additionalservice_r=$conn->prepare($additionalservice);
    $additionalservice_r->bind_param('ssss', $itemremarks, $date_completed_serv, $request_id, $items_additional);
    $additionalservice_r->execute();

    echo "<html><head><script> var div = document.createElement('div');
              div.setAttribute('class', 'alert');
              var span = document.createElement('button');
              span.setAttribute('class', 'closebtn');
              span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
              var spanTxt = document.createTextNode('Done')
              span.appendChild(spanTxt);
              var pTxt = document.createTextNode('Successfully Added Detail!');
              var p = document.createElement('p');
              p.appendChild(pTxt);
              p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
              div.appendChild(span);
              
              document.body.appendChild(div); </script></head></html>";
    echo "<meta http-equiv='refresh' content='1.1'>";

  }
}else{
  // echo "no selected item";
}
?>

<br>
  <fieldset>
  <legend>Add Delivery
    <span style='font-size: 13px;'>&emsp;
    <form onsubmit="return confirm('Are you sure? this action cannot be undone');" method='POST' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);?>">
        <input type='submit' name='deliver_all' class='btn btn-success' value='Deliver All Items'><input name='cancel_all' type='submit' value='Cancel All items' class='btn btn-danger'>
    </form>
    </span>
  </legend>
  </fieldset>
<?php
// Update pag completed/canceled lahat
if(isset($_POST['deliver_all'])){

  if($reqSlipArr['type']=='ItemsNoPO'){
    $nopoupdate = "UPDATE itemsnotpo set itemStatus ='Delivered' where request_slip_no=?";
    $nopoupdateRs = $conn->prepare($nopoupdate);
    $nopoupdateRs->bind_param('i', $request_id);
    $nopoupdateRs->execute();

     $deliver = "UPDATE request_slip set status='Completed' where id=?";
    $deliverRslt = $conn->prepare($deliver);
    $deliverRslt->bind_param('i', $request_id);
    $deliverRslt->execute();
    $deliverRslt->close();

  }else if($reqSlipArr['type']=='PO'){

    $poupdate = "UPDATE itemspo set itemspostatus ='Delivered' where poid=?";
    $poupdateRs = $conn->prepare($poupdate);
    $poupdateRs->bind_param('i', $poArr['id']);
    $poupdateRs->execute();
     $deliver = "UPDATE request_slip set status='Completed' where id=?";
    $deliverRslt = $conn->prepare($deliver);
    $deliverRslt->bind_param('i', $request_id);
    $deliverRslt->execute();
    $deliverRslt->close();
  }else if($reqSlipArr['type']=='Service'){
    $serviceupdate = "UPDATE services set status='Delivered' where requestID = ?";
    $serviceupdaterslt = $conn->prepare($serviceupdate);
    $serviceupdaterslt->bind_param('i', $request_id);
    $serviceupdaterslt->execute();
     $deliver = "UPDATE request_slip set status='Completed' where id=?";
    $deliverRslt = $conn->prepare($deliver);
    $deliverRslt->bind_param('i', $request_id);
    $deliverRslt->execute();
    $deliverRslt->close();
  }
  echo "<meta http-equiv='refresh' content='0'>";
}
if(isset($_POST['cancel_all'])){
 $deliver2 = "UPDATE request_slip set status='Canceled' where id=?";
  $deliverRslt2 = $conn->prepare($deliver2);
  $deliverRslt2->bind_param('i', $request_id);
  $deliverRslt2->execute();
  $deliverRslt2->close();


  if($reqSlipArr['type']=='ItemsNoPO'){
    $nopoupdate2 = "UPDATE itemsnotpo set itemStatus ='Canceled' where request_slip_no=?";
    $nopoupdateRs2 = $conn->prepare($nopoupdate);
    $nopoupdateRs2->bind_param('i', $request_id);
    $nopoupdateRs2->execute();
  }else if($reqSlipArr['type']=='PO'){

    $poupdate2 = "UPDATE itemspo set itemspostatus ='Canceled' where poid=?";
    $poupdateRs2 = $conn->prepare($poupdate);
    $poupdateRs2->bind_param('i', $poArr['id']);
    $poupdateRs2->execute();
  }else if($reqSlipArr['type']=='Service'){
    $serviceupdate2 = "UPDATE services set status='Canceled' where requestID = ?";
    $serviceupdaterslt2 = $conn->prepare($serviceupdate2);
    $serviceupdaterslt2->bind_param('i', $request_id);
    $serviceupdaterslt2->execute();
  }
  echo "<meta http-equiv='refresh' content='0'>";
}


?>
<table style='border-width: 7px'; class='table table-striped table-bordered table-hover table-condensed table-responsive'>
<tr>
  <th><h4> <?php if($reqSlipArr['type']!='Service'){?>Item Name <?php }else{ echo "Service Name";}?></h4></th>
  <th><h4><?php if($reqSlipArr['type']=='Service'){}else{?> Quantity left To Be Delivered</h4></th><?php } ?>
  <th><h4><?php if($reqSlipArr['type']=='Service'){}else{?> Quantity </h4></th><?php } ?>
  <th><h4><?php if($reqSlipArr['type']=='Service'){}else{?> Date </h4></th><?php } ?>
  <th><h4> Action </h4></th>
</tr>
<tr>
  <td>
        <select name='mgaGamit'>
    <?php

      if($reqSlipArr['type']=='PO'){
        $poOrder = "SELECT * from purchase_order where request_id =?";
        $poOrderQry = $conn->prepare($poOrder);
        $poOrderQry->bind_param('s', $request_id);
        $poOrderQry->execute();
        $poOrderArr=$poOrderQry->get_result()->fetch_array(MYSQLI_ASSOC);


      $itemStmt = "SELECT description from itemspo where poid = '$poOrderArr[id]'  AND itemspostatus = 'Pending' ";
      $itemStmtRslt = mysqli_query($conn, $itemStmt);
        
      }else if($reqSlipArr['type'] == 'ItemsNoPO'){
       $itemStmt = "SELECT description from itemsnotpo where request_slip_no = '$request_id' AND itemStatus= 'Pending' ";
      $itemStmtRslt = mysqli_query($conn, $itemStmt); 
      }else if($reqSlipArr['type']=='Service'){
      $itemStmt = "SELECT description from services where requestID = '$request_id' and status='Pending'";
      $itemStmtRslt = mysqli_query($conn, $itemStmt); 
      }else{
        echo "No items found";
      }

      while($itemStmtvalue = mysqli_fetch_array($itemStmtRslt)){
        echo "<option value='".$itemStmtvalue[0]."'>".$itemStmtvalue[0]."</option>";
         
      }
    ?>
        </select>
  </td>
  <td id ="quantity_left_tobe"></td>
  <td> <?php if($reqSlipArr['type']=='Service'){}else{?><input id ="delivered_quantity" type='number' min="1"> </td><?php } ?>
  <td> <?php if($reqSlipArr['type']=='Service'){}else{?><input id ="delivered_date" type='date'> </td><?php } ?>
  <td> <?php if($reqSlipArr['type']=='Service'){}else{?><button id='deliv_but' class='btn btn-success'>Add </button><?php } ?>&emsp; &emsp; <button id='cancel_but' class='btn btn-danger'> Cancel Item </button> &emsp; <button class='btn btn-primary' id='full_deliver'>Full Deliver </td>
</tr> 
</table>
<br>
<fieldset>
<legend>
<!-- Delivered Items Table -->
<?php
if($reqSlipArr['type']!='Service'){
  echo "Delivered Items";
}else{
  echo "Current Service/s";
}
?>
  
</legend>
</fieldset>
<?php
if($reqSlipArr['type']=='ItemsNoPO'){

  $delItemsQry = "select description, quantity, qty_delivered_nopo, itemStatus from itemsnotpo where request_slip_no=?";
  $delItemsResult=$conn->prepare($delItemsQry);
  $delItemsResult->bind_param('i', $request_id);
  $delItemsResult->execute();
  $delItemsResult->bind_result($it_name, $qty, $qty_tot, $it_status);
}else if($reqSlipArr['type']=='PO'){
   $delItemsQry = "SELECT description, quantity,qty_delivered_po, itemspostatus FROM itemspo Where poid = ?;";
  $delItemsResult=$conn->prepare($delItemsQry);
  $delItemsResult->bind_param('i', $poArr['id']);
  $delItemsResult->execute();
  $delItemsResult->bind_result($it_name, $qty, $qty_tot, $it_status);
}else if($reqSlipArr['type']=='Service'){
  $delItemsQry = "SELECT description, idServices, requestID, status from services where requestID = ?";
   $delItemsResult=$conn->prepare($delItemsQry);
  $delItemsResult->bind_param('i', $request_id);
  $delItemsResult->execute();
  $delItemsResult->bind_result($it_name, $qty, $qty_tot, $it_status);
}
  // $delItemsResult->bind_result($it_name, $qty, $qty_tot, $it_status);
?>
<table style='border-width: 7px'; class='table table-striped table-bordered table-hover table-condensed table-responsive'>
 <tbody id= "Delivered_Table"> 
  <tr>
      <th><?php if($reqSlipArr['type']=='Service'){echo "<h4> Service Name </h4>";}else{?><h4> Item Name </h4></th>  <?php } ?>
      <th><?php if($reqSlipArr['type']=='Service'){}else{?><h4> Total Quantity </h4></th><?php } ?>
      <th><?php if($reqSlipArr['type']=='Service'){}else{?><h4> Total Delivered </h4></th><?php } ?>
      <th><?php if($reqSlipArr['type']=='Service'){echo "<h4> Service Status </h4>";}else{?><h4> Item Status </h4></th>  <?php } ?>
  </tr>
  <?php while($delItemsResult->fetch()){?>
  <tr id ="<?php echo $it_name; ?>">
      <td><?php echo $it_name; ?></td>
      <td><?php if($reqSlipArr['type']=='Service'){}else{ echo $qty;} ?></td>
      <td><?php if($reqSlipArr['type']=='Service'){}else{ echo $qty_tot;} ?> </td>
      <td><?php echo $it_status; ?></td>
  </tr>
  <?php }?>
 </tbody>
</table>
<!-- <button onclick='editInfo()' class="btn btn-lg btn-lg btn-default" name = 'editInfo'>Edit</button>
 -->
<?php
//  if($reqSlipArr['type'] == 'PO'){

//   echo "<button onclick = 'sendfirstPartForPO(\"".$reqSlipArr['rs_no']."\")' style= 'display:none' class='btn btn-lg btn-default' name = 'saveInfo'>Save</button>";

// }
// else if($reqSlipArr['type'] == 'ItemsNoPO'){

//   echo "<button onclick = 'sendfirstPartNotForPo(\"".$reqSlipArr['rs_no']."\")' style= 'display:none' class='btn btn-lg btn-default' name = 'saveInfo'>Save</button>";

// }
// else{
//   echo "<button onclick = 'sendfirstPartServices(\"".$reqSlipArr['rs_no']."\")' style= 'display:none' class='btn btn-lg btn-default' name = 'saveInfo'>Save</button>";
    
// }
echo "<div id='cancel'> </div> <br>";
echo "<a class='btn btn-primary' href='download_xls.php?request_id=[" . $request_id . "]'>Generate Excel File </a> <br> <br>";

echo "<form method ='POST'  onsubmit='return delConfirm(\"".$request_id."\")' >";
echo "<td><button type='submit' class ='btn btn-danger' name ='requestDel' value='". $request_id . "'>Delete Request</button></td></tr></form>";
// DELETE REQUEST
// var_dump($reqSlipArr);
if(isset($_POST['requestDel'])){
  $deleteDel = "DELETE FROM delivered_items where rs_item_no=?";
  $deleteDel_r = $conn->prepare($deleteDel);
  $deleteDel_r ->bind_param('i', $reqSlipArr['id']);
  $deleteDel_r ->execute();
  if($reqSlipArr['type'] == 'ItemsNoPO'){
        $delQueryStmt="DELETE FROM itemsnotpo WHERE request_slip_no = ?";
        $delQuerybind=$conn->prepare($delQueryStmt);
        $delQuerybind->bind_param('s', $reqSlipArr['id']);
        $delQuerybind->execute();
    }
    else if($reqSlipArr['type'] == 'PO'){
        $queryStmt1 = "SELECT * FROM purchase_order where request_id = ?";
        $query1=$conn->prepare($queryStmt1);
        $query1->bind_param('s', $reqSlipArr['id']);
        $query1->execute();
        $poArray=$query1->get_result()->fetch_array(MYSQLI_ASSOC);
        

        $poID = $poArray['id'];
        $delPO = "DELETE FROM itemspo WHERE poid = ?";
        $delPOres=$conn->prepare($delPO);
        $delPOres->bind_param('s', $poID);
        $delPOres->execute();
        $delPOres->close();


        $delPOstmt="DELETE FROM purchase_order WHERE request_id = ?";
        $delPOstmtres=$conn->prepare($delPOstmt);
        $delPOstmtres->bind_param('s', $reqSlipArr['id']);
        $delPOstmtres->execute();
        $delPOstmtres->close();
    }
    else{
        $delservstmt="DELETE FROM services WHERE requestID= ?";
        $delservstmtres=$conn->prepare($delservstmt);
        var_dump($conn->error);
        $delservstmtres->bind_param('s', $reqSlipArr['id']);
        $delservstmtres->execute();
        $delservstmtres->close();
       
    }
    $delrequest = "DELETE FROM request_slip WHERE id = ?";
    $delrequestres=$conn->prepare($delrequest);
    $delrequestres->bind_param('s', $reqSlipArr['id']);
    $delrequestres->execute();
    $delrequestres->close();

    $deldeliver ="DELETE FROM delivered_items where rs_item_no=?";
    $deldeliverres=$conn->prepare($deldeliver);
    $deldeliverres->bind_param('s', $reqSlipArr['id']);
    $deldeliverres->execute();
    $deldeliverres->close();
    echo "<html><head><script> var div = document.createElement('div');
              div.setAttribute('class', 'alert');
              var span = document.createElement('button');
              span.setAttribute('class', 'closebtn');
              span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
              var spanTxt = document.createTextNode('Done')
              span.appendChild(spanTxt);
              var pTxt = document.createTextNode('Successfully Deleted!');
              var p = document.createElement('p');
              p.appendChild(pTxt);
              p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
              div.appendChild(span);
              
              document.body.appendChild(div); setTimeout(function(){location.href='dashboard.php'} , 1100); </script></head></html>";
}
?>

</div>


            </div>
        </div>
    </div>

    <div id="putHere"></div>
<div>
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
        ?>

         <form class='form-horizontal requestForm' onsubmit="return confirm('Are you sure? this action cannot be undone');" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);?>">
        <?php
      
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
                // print_r($newpass);   
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
  <form class='form-horizontal requestForm' onsubmit="return confirm('Are you sure? this action cannot be undone');" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);?>">
    <input class='btn btn-primary' name='completeReq' type='submit' value='Mark Request as Completed'> <input name='cancelReq' class='btn btn-danger' type='submit' value='Mark Request as Canceled'>
  </form>
  <?php
  if(isset($_POST['completeReq'])){
    $completePO = "UPDATE request_slip set status='Completed' where id=?";
    $completePO_r = $conn->prepare($completePO);
    $completePO_r->bind_param('i', $request_id);
    $completePO_r->execute();
     echo "<meta http-equiv='refresh' content='1'>";
  }else if(isset($_POST['cancelReq'])){
    $cancelPO = "UPDATE request_slip set status='Canceled' where id=?";
    $cancelPO_r=$conn->prepare($cancelPO);
    $cancelPO_r->bind_param('i', $request_id);
    $cancelPO_r->execute();
     echo "<meta http-equiv='refresh' content='1'>";
  }


  ?>
</div>

                </div>


<script>
if($('#dropdown_additional').val()==null){
  document.getElementById('additional_id').removeAttribute('class');
  document.getElementById('additional_id').removeAttribute("data-toggle");
  document.getElementById('additional_id').removeAttribute("data-target");
  document.getElementById('additional_id').setAttribute("style", "text-decoration: line-through;color:Red");


}



function delConfirm(rsnNum){
    var conf = confirm("Are you sure You want to delete?");
    if( conf == true){

        return true;
    }
    else{
        
        return false;
    }
}
  

function editInput(){
  var count = '<?php echo $inc ?>'

  for(var index = 0; index < count; index++){
    var description = document.getElementById('desc'+index);
    var descText = description.textContent;

    var stat = document.getElementById('iStat'+index);

    if(stat.textContent == "Pending"){
      var statIndex = 0;
    }else if(stat.textContent == "Delivered" || stat.textContent == "Completed"){
      var statIndex = 1;
    }
    else{
      var statIndex = 2;
    }


    var statText = stat.value;

    var remarks = document.getElementById('remark'+index);
    var remarksText = remarks.textContent;

    var quantity;
    var quantityText;

    var location;
    var locationText;

    var unitPrice;
    var unitPriceText;

    var amount;
    var amountText;

    var dateAccomp;
    var dateAccompText;

    var dateComp;
    var dateCompText;

    var dateDel;
    var dateDelText;

    var dateDeli;
    var dateDeliText;

    var supplier;
    var supplierText;

    var servProvider;
    var servProviderText;

    var supplierPO;
    var supplierPOText;


    var opPend = document.createElement('option');
    var opDel = document.createElement('option');
    var opCan = document.createElement('option');
    var opComp = document.createElement('option');

    opPend.setAttribute('value','Pending');
    opDel.setAttribute('value', 'Delivered');
    opCan.setAttribute('value', 'Canceled');
    opComp.setAttribute('value','Completed');

    opPend.textContent = "Pending";
    opDel.textContent = "Delivered";
    opCan.textContent = "Canceled";
    opComp.textContent = "Completed";

    if(document.getElementById('quantity'+index)){
      quantity = document.getElementById('quantity'+index);
      quantityText = quantity.textContent;
      
    }

    if(document.getElementById('loc'+index)){
      location = document.getElementById('loc'+index);
      locationText = location.textContent;
    }

    if(document.getElementById('uPrice'+index)){
      unitPrice = document.getElementById('uPrice'+index);
      unitPriceText  = unitPrice.textContent;
    }

     if(document.getElementById('amount'+index)){
      amount = document.getElementById('amount'+index);
      amountText = amount.textContent;
    }


    if(document.getElementById('dateAccomp'+index)){
      dateAccomp = document.getElementById('dateAccomp'+index);
      dateAccompText = dateAccomp.textContent;
    }

    if(document.getElementById('dateComplete'+index)){
      dateComp = document.getElementById('dateComplete'+index);
      dateCompText = dateComp.textContent;
    }

    if(document.getElementById('date_deli'+index)){
      dateDeli = document.getElementById('date_deli'+index);
      dateDeliText = dateDeli.textContent;
    }

    if(document.getElementById('supplier'+index)){
      supplier = document.getElementById('supplier'+index);
      supplierText = supplier.textContent;
    }

    if(document.getElementById('sprovider'+index)){
      servProvider = document.getElementById('sprovider'+index);
      servProviderText = servProvider.textContent;
    }

    if(document.getElementById('supplierForPO'+index)){
      supplierPO = document.getElementById('supplierForPO'+index);
      supplierPOText = supplierPO.textContent;
    }



    if(quantity != undefined && location != undefined && unitPrice != undefined && dateDeli != undefined){

      // description.innerHTML = "<input id = 'descri"+index+"' name = 'description' type = 'text' value= '"+descText+"'>";
      // quantity.innerHTML = "<input id = 'quantit"+index+"' name = 'quantity' type = 'number' min = '1' value = '"+quantityText+"'>";

      <?php
          $supmanQueryStmt = "SELECT DISTINCT supplier_po FROM itemspo where supplier_po IS NOT NULL";
          $supmanQuery = mysqli_query($conn, $supmanQueryStmt);
        ?>

      location.innerHTML = "<input id = 'loca"+index+"' name = 'location' type = 'text' value = '"+locationText+"'>";
      unitPrice.innerHTML = "<input id = 'unitPrice"+index+"' type = 'number' min = '1' step='0.01' name = 'unitPrice' type = 'text' value = '"+unitPriceText+"'>";
      // dateDeli.innerHTML = "<input id = 'dateDeliv"+index+"' name ='dateDeli' type ='date' value='"+dateDeliText+"'>";
      // stat.innerHTML = "<select id = 'status"+index+"' name = 'status'></select>";
      
      // document.getElementById("status"+index).appendChild(opPend);
      // document.getElementById("status"+index).appendChild(opDel);
      // document.getElementById("status"+index).appendChild(opCan);
      // document.getElementById("status"+index)[statIndex].setAttribute('selected','selected');

      remarks.innerHTML = "<input id= 'remarks"+index+"' name = 'remarks' type = 'text' value = '"+remarksText+"'>";
    }
    else if(quantity != undefined && dateAccomp != undefined && amount != undefined){

      description.innerHTML = "<input id = 'descri"+index+"' name = 'description' type = 'text' value= '"+descText+"'>";
      // quantity.innerHTML = "<input id = 'quantit"+index+"' name = 'quantity' type = 'number' min='1' value= '"+quantityText+"'>";

      <?php
        $suppliNotPOStmt = "SELECT DISTINCT supplier FROM itemsnotpo where supplier IS NOT NULL";
        $suppliNotPOquery = mysqli_query($conn, $suppliNotPOStmt);
      ?>
      supplier.innerHTML = "<input list ='suppliNotPO' id = 'suppli"+index+"' name = 'supplierNoPO' type = 'text' value = '"+supplierText+"'><datalist id ='suppliNotPO'><?php while($supplinotpo = mysqli_fetch_array($suppliNotPOquery)){ echo '<option value ='.$supplinotpo[0].'>';} ?> </datalist>";

      dateAccomp.innerHTML = "<input id = 'dateAccompl"+index+"' name = 'dateDel' type = 'date' value= '"+dateAccompText+"'>";
      amount.innerHTML = "<input id = 'amt"+index+"' name = 'amount' type = 'number' min = '1' step='0.01' value= '"+amountText+"'>";
      stat.innerHTML = "<select id = 'status"+index+"' name = 'status'></select>";
      
      document.getElementById("status"+index).appendChild(opPend);
      document.getElementById("status"+index).appendChild(opDel);
      document.getElementById("status"+index).appendChild(opCan);
      document.getElementById("status"+index)[statIndex].setAttribute('selected','selected');

      remarks.innerHTML = "<input id = 'remarks"+index+"' name = 'remarks' type = 'text' value= '"+remarksText+"'>";

    }
    else if(dateComp != undefined){

      description.innerHTML = "<input id = 'descri"+index+"' name = 'description' type = 'text' value= '"+descText+"'>";

      <?php
        $serviceProviderQStmt = "SELECT DISTINCT service_provider FROM services where service_provider IS NOT NULL";
        $serviceProviderQuery = mysqli_query($conn, $serviceProviderQStmt);
      ?>
      servProvider.innerHTML = "<input list = 'atYourService' id = 'serviceprov"+index+"' name = 'serviceprovider' type = 'text' value = '"+servProviderText+"'> <datalist id ='atYourService'> <?php while($serviceProvArr = mysqli_fetch_array($serviceProviderQuery)){ echo '<option value ='.$serviceProvArr[0].'>';} ?> </datalist>";
      dateComp.innerHTML = "<input id = 'dateComple"+index+"' name ='dateComp' type = 'date' value= '"+dateCompText+"'>";


      stat.innerHTML = "<select id = 'status"+index+"' name = 'status'></select>";
      
      document.getElementById("status"+index).appendChild(opPend);
      document.getElementById("status"+index).appendChild(opComp);
      document.getElementById("status"+index).appendChild(opCan);
      document.getElementById("status"+index)[statIndex].setAttribute('selected','selected');

      remarks.innerHTML = "<input id= 'remarks"+index+"' name ='remarks' type = 'text' value= '"+remarksText+"'>";
    }


  }
}


function editInfo(){


  if(document.getElementById('poNum')){
    var poNumber = document.getElementById('poNum');
    var poNumberText = poNumber.textContent;
    poNumber.innerHTML = "<input type='text' name='poNum' value='"+poNumberText+"'>";

  }

  if(document.getElementById('poDate')){
    var poDate = document.getElementById('poDate');
    var poDateText = poDate.textContent;
    poDate.innerHTML = "<input type='text' placeholder = 'yy-mm-dd' name='poDate' value='"+poDateText+"'>";

  }



  var edBut = document.querySelector('button[name=editInfo]');
  edBut.style.display = 'none';

  var svBut = document.querySelector('button[name=saveInfo]');
  svBut.style.display = '';
editInput();

  var cancelbtn = document.createElement('button');
  cancelbtn.setAttribute('id', 'cancelbtn');
  cancelbtn.setAttribute('class', 'btn btn-lg btn-lg btn-default');
  cancelbtn.innerHTML = "Cancel";
  document.getElementById('cancel').appendChild(cancelbtn);


  document.getElementById('cancelbtn').onclick = function(){

    location.reload();


  }



} 

function prepareAmount(){

    if(document.getElementsByClassName('amount').length >= 0){

      amount = document.getElementsByClassName('amount');
      var quantity;
      var quantityText;
      var unitPrice;
      var unitPriceText;
      var sum;
      var totalAmount = 0;

      for(var i = 0; i < amount.length; i++){
        if(document.getElementById('quantity'+i)){
          quantity = document.getElementById('quantity'+i);
          quantityText = quantity.textContent;
          
        }

        if(document.getElementById('uPrice'+i)){
          unitPrice = document.getElementById('uPrice'+i);
          unitPriceText  = unitPrice.textContent;
        }


        if(unitPriceText != '' && unitPriceText != null && unitPriceText != undefined && quantityText !='' && quantityText != null && quantityText != undefined){
          if(!isNaN(unitPriceText) && !isNaN(quantityText)){
            sum = unitPriceText * quantityText;
            amount[i].textContent = sum;
          }
        }

      }
    
    }
    prepareTotalAmount();
}

function prepareTotalAmount(){

  if(document.getElementsByClassName('amount').length >= 0){
    
    var amountElem = document.getElementsByClassName('amount');
    var totalAmt;

    for(var i = 0; i < amountElem.length; i++){
      var amountVal = amountElem[i].textContent;

      if(amountVal != '' && amountVal != null && amountVal != undefined){
        if(!isNaN(amountVal)){
          if(totalAmt == undefined){
            totalAmt = parseFloat(amountVal);
          }
          else{
            totalAmt = parseFloat(totalAmt)+parseFloat(amountVal);
          }
            
        }     
      }

    }

    if(totalAmt != undefined){
      document.getElementById("totalAmt").textContent = totalAmt;

        if('<?php echo $reqSlipArr['type']; ?>' == 'PO'){
        var xhttp = new XMLHttpRequest();

        var temp = window.location.href;
        var num = temp.indexOf("?");
        var requestId = temp.substring(num+1);

        xhttp.open("POST", "totalAmt.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(requestId+"&total="+totalAmt);
      }
    }   
  }

}
    //cancel per item
    document.getElementById('cancel_but').onclick = function(){
      var ows = confirm('Are you sure you want to cancel?');
      if(ows){
         $.post("itemCancelDeliver.php", {itemName2:$("[name=mgaGamit]").val(),type2: "<?php echo $reqSlipArr['type'] ?>", req_id2: "<?php echo $request_id ?>"})
          .done(function(data) {
            if(data=='walanaman'){
                var div = document.createElement('div');
                div.setAttribute('class', 'alert');
                var span = document.createElement('button');
                span.setAttribute('class', 'closebtn');
                span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
                var spanTxt = document.createTextNode('Done')
                span.appendChild(spanTxt);
                var pTxt = document.createTextNode('Sorry all items are done, please set the status of this request');
                var p = document.createElement('p');
                p.appendChild(pTxt);
                p.setAttribute('class', 'alertContent');
                div.appendChild(p);
                div.appendChild(span);
                document.body.appendChild(div);
            }else{

                var div = document.createElement('div');
                div.setAttribute('class', 'alert');
                var span = document.createElement('button');
                span.setAttribute('class', 'closebtn');
                span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
                var spanTxt = document.createTextNode('Done')
                span.appendChild(spanTxt);
                var pTxt = document.createTextNode('Successfully Canceled Item!');
                var p = document.createElement('p');
                p.appendChild(pTxt);
                p.setAttribute('class', 'alertContent');
                div.appendChild(p);
                div.appendChild(span);
                document.body.appendChild(div);
                setTimeout(function(){location.reload()} , 1000); 
            }

          });
      }else{
        //do nothing
      }
     
    }
    // end cancel per item

    //full deliver/item
     document.getElementById('full_deliver').onclick = function(){
      var ows = confirm('Are you sure you want to deliver all?');
      if(ows){
         $.post("itemCancelDeliver.php", {itemName3:$("[name=mgaGamit]").val(),type3: "<?php echo $reqSlipArr['type'] ?>", req_id3: "<?php echo $request_id ?>"})
          .done(function(data) {
            if(data=='walanaman'){
                var div = document.createElement('div');
                div.setAttribute('class', 'alert');
                var span = document.createElement('button');
                span.setAttribute('class', 'closebtn');
                span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
                var spanTxt = document.createTextNode('Done')
                span.appendChild(spanTxt);
                var pTxt = document.createTextNode('Sorry all items are done, please set the status of this request!');
                var p = document.createElement('p');
                p.appendChild(pTxt);
                p.setAttribute('class', 'alertContent');
                div.appendChild(p);
                div.appendChild(span);
                document.body.appendChild(div);
            }else{

                var div = document.createElement('div');
                div.setAttribute('class', 'alert');
                var span = document.createElement('button');
                span.setAttribute('class', 'closebtn');
                span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
                var spanTxt = document.createTextNode('Done')
                span.appendChild(spanTxt);
                var pTxt = document.createTextNode('Successfully Delivered Item!');
                var p = document.createElement('p');
                p.appendChild(pTxt);
                p.setAttribute('class', 'alertContent');
                div.appendChild(p);
                div.appendChild(span);
                document.body.appendChild(div);
                setTimeout(function(){location.reload()} , 1000); 
            }
          });
      }else{
        //do nothing
      }
     
    }
    //end full deliver/item

    function itemQuantLeft(){
      var tobeRemoved = $("[name=mgaGamit]").val();
      $.post("itemQuantityGetter.php", {itemName:$("[name=mgaGamit]").val(),type: "<?php echo $reqSlipArr['type'] ?>", req_id: "<?php echo $request_id ?>"})
      .done(function(data) {
        if(data != 0){
          $deliv_quant = $("#delivered_quantity").attr("max",data);
          $("#quantity_left_tobe").text(data);
        }
        else{
          $("[name='mgaGamit'] option[value='"+tobeRemoved+"']").remove();
          $.post("itemQuantityGetter.php", {itemName:$("[name=mgaGamit]").val(),type: "<?php echo $reqSlipArr['type'] ?>", req_id: "<?php echo $request_id ?>"})
          .done(function(data){
             $deliv_quant = $("#delivered_quantity").attr("max",data);
            $("#quantity_left_tobe").text(data);
          });


        }




      });
      
    }

 
$(document).ready(function(){
  itemQuantLeft();


 $("[name=mgaGamit]").on('change',function(){itemQuantLeft()});


  $("#deliv_but").click(function(){
    var confm = confirm("Are you sure?");

      if(confm == true){
        $item_desc = $("[name=mgaGamit]").val();
        $deliv_quant = $("#delivered_quantity").val();
        $deliv_date = $("#delivered_date").val();
        $type = "<?php echo $reqSlipArr['type'] ?>";
        $reqst_id =  "<?php echo $request_id ?>";

        $.post("addDeliverItem.php", {req_id: $reqst_id, reqType: $type, itemName: $item_desc ,itemQuant: $deliv_quant,itemDelDate: $deliv_date}, function(data) {
            if(data.state == "error"){
              eval(data.msg);

            }
            else{
              var div = document.createElement('div');
              div.setAttribute('class', 'alert');
              var span = document.createElement('button');
              span.setAttribute('class', 'closebtn');
              span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
              var spanTxt = document.createTextNode('Done')
              span.appendChild(spanTxt);
              var pTxt = document.createTextNode('Successfully Delivered Item');
              var p = document.createElement('p');
              p.appendChild(pTxt);
              p.setAttribute('class', 'alertContent');
              div.appendChild(p);
              div.appendChild(span);
              document.body.appendChild(div); 

              if(document.getElementById(data.itemName)){
                $("tr[id ='"+data.itemName+"'] td:nth-child(3)").text(data.QuantityDelivered);
                $("tr[id ='"+data.itemName+"'] td:nth-child(4)").text(data.ItemStatus);
                

              }else{
                $("#Delivered_Table").append('<tr id = '+data.itemName+'><td>'+data.itemName+'</td><td>'+data.totalQuantity+'</td><td>'+data.QuantityDelivered+'</td><td>'+data.ItemStatus+'</td></tr>');           
              }

              if(document.getElementById("qty_delivered_nopo")){
                $("tr[id='"+data.itemName+"First'] td:nth-child(8) span").text(data.QuantityDelivered);
              }
              else{
                $("tr[id='"+data.itemName+"First'] td:nth-child(10) span").text(data.QuantityDelivered);

              }

              itemQuantLeft();

              if(data.ItemStatus == "Delivered"){
                  if(document.getElementById("qty_delivered_nopo")){
                    $("tr[id='"+data.itemName+"First'] td:nth-child(6) span h4").text("Delivered");
                  }
                  else{
                      $("tr[id='"+data.itemName+"First'] td:nth-child(7) span h4").text("Delivered");
                  }

                if(data.RequestStatus == "Completed"){
                      $("#status_edit").text("Delivered");
                      location.reload();
                  }
              }
            
            }
        },"json");
    }

  });




});


</script>

</body>

</html>

<?php
}
else{

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Oooopss</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="stylesheet" href="../css/error.css">
</head>
<body>
<div class="wrap">
  <div class="banner">
    <img src="../img/banner.png" alt="" />
  </div>
  <div class="page">
    <h2>Sorry, we can't find that page!</h2>
        <h2>Please return to the<a  href="../dashboard/dashboard.php"><i class="glyphicon"></i> Dashboard</a></h2>
  </div>
</div>
</body>
</html>
<?php
}
}

?>