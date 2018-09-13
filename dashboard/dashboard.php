<?php
error_reporting(E_ALL);
include '../shared/connection.php';
include '../shared/authorization.php';
$user = $_SESSION['username'];
$username=$_SESSION['username'];

$stmt = $conn -> prepare("SELECT * from users inner join user_details on users.id=user_details.id where username= ?");
$stmt -> bind_param("s" ,$username);
$stmt -> execute();
$queryArr = $stmt -> get_result() ->fetch_array(MYSQLI_ASSOC);

$queryCompleted = "SELECT COUNT(*) FROM `request_slip` WHERE `status` = 'Completed'";
$querycomplete = mysqli_query($conn, $queryCompleted) or die(mysqli_error($conn));
$wow = mysqli_fetch_array($querycomplete);

$queryPending = "SELECT COUNT(*) FROM `request_slip` WHERE `status` = 'Pending'";
$querypend = mysqli_query($conn, $queryPending) or die(mysqli_error($conn));
$wow1 = mysqli_fetch_array($querypend);

$queryCancel = "SELECT COUNT(*) FROM `request_slip` WHERE `status` = 'Canceled'";
$querycanc = mysqli_query($conn, $queryCancel) or die(mysqli_error($conn));
$wow2 = mysqli_fetch_array($querycanc);

$queryAllreq = "SELECT COUNT(*) FROM `request_slip`";
$queryreq = mysqli_query($conn, $queryAllreq) or die(mysqli_error($conn));
$wow3 = mysqli_fetch_array($queryreq);


?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>SCIS Requisition</title>
<!-- <link rel="stylesheet" id="font-awesome-style-css" href="http://phpflow.com/code/css/bootstrap3.min.css" type="text/css" media="all"> -->
<script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.1.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css"/>
<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/font-awesome.css">

<script type="text/javascript" src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>


<body>
	   <?php
                include "../header.php";
                $nothing = 1;
    ?>

    <!-- LOGO HEADER END-->
    <section class="menu-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse ">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a class="menu-top-active" href="dashboard.php"><i class="glyphicon glyphicon-home"></i> Dashboard</a></li>
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
    <div class="page-content">

           <div class="row">
                <div class="col-md-12">
                    <?php echo"<h1 class='page-head-line'>Welcome, ".$queryArr['firstname'] . ' ' . $queryArr['lastname'] . '!</h1>';?>

                </div>


            </div>
                <div class="content-box-large">
                    <div class="row">
                 <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="dashboard-div-wrapper bk-clr-one">
                        <i  class="fa fa-check dashboard-div-icon" ></i>

                         <h5>Completed </h5>
                         <h5><?php echo "<h1>". $wow[0] ."</h1>";?></h5>
                    </div>
                </div>
                 <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="dashboard-div-wrapper bk-clr-one">
                        <i  class="fa fa-clock-o dashboard-div-icon" ></i>
                         <h5>Pending </h5>
                         <h5><?php echo "<h1>". $wow1[0] ."</h1>";?></h5>
                    </div>
                </div>
                 <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="dashboard-div-wrapper bk-clr-two">
                        <i class="fa fa-close dashboard-div-icon" ></i>
                         <h5>Cancelled </h5>
                         <h5><?php echo "<h1>". $wow2[0] ."</h1>";?></h5>
                    </div>
                </div>
                 <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="dashboard-div-wrapper bk-clr-three">
                        <i  class="fa fa-exclamation dashboard-div-icon" ></i>
                         <h5>Total Number of Transactions </h5>
                         <h5><?php echo "<h1>". $wow3[0] ."</h1>";?></h5>
                    </div>
                </div>

</div>
	<div class="container">
      <div class="">
        <br>
        <div class="">
		<table id="employee_grid" class="display" width="100%" cellspacing="0">
        <thead>

            <tr>
                <th> id </th>
                <th>Request Slip No.</th>
                <th>Date Needed (Y-m-d)</th>
				        <th>Description of Request</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Request Type </th>
                <th>Mark for Download / Details</th>
            </tr>
        </thead>
 
        <tfoot>
            <tr>
                <th> id </th>
               <th>Request Slip No.</th>
                <th>Date Needed</th>
                <th>Description of Request</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Request Type </th>
                <th>Mark for Download / Details</th>
                

                
            </tr>
        </tfoot>
    </table>
    </div>
      </div>

    </div>
    <br>
     <div><p class='text-primary'> NOTE: Enable Pop ups to download all selected Request items </p> 
             <p class='text-muted'><button type='button' class='btn btn-warning' onclick='markAll()'> Mark/Unmark All</button> <button type='button' class='btn btn-primary' onclick='getCheckboxValues()' >Download Selected Items</button> 
             <button type='button' class='btn btn-danger' onclick='all_report()' > Download All Reports</button> 

             </p>
      </div>
      
</body>
<script type="text/javascript">

$( document ).ready(function() {
$('#employee_grid').DataTable({
				 "bProcessing": true,
         "serverSide": true,
         "order": [[ 0, "desc"]],
         "ajax":{
            url :"response.php", // json datasource
            type: "post",  // type of method  ,GET/POST/DELETE
            error: function(){
              $("#employee_grid_processing").css("display","none");
            }
          },
           "columnDefs": [ {
            "targets": -1,
            "data": null,
            // "defaultContent": "<button>View Details</button>  <input type='checkbox'>"
             "render": function ( data, type, row ) {
                    // return "" +' ('+ row[0]+')';
                    return "<div style='text-align:center'><input type='checkbox' value="+row[0]+" class='downloadxls'> &nbsp;&nbsp;&nbsp; <a href=view_details.php?request_id="+row[0]+" class='btn btn-primary'> View Details </a>"
                },
        },
        { "visible": false,  "targets": [ 0 ] }
         ]

        });   
});
function getCheckboxValues(){
        var chekboxes = document.getElementsByClassName('downloadxls');
        var reqs = new Array();
        var reqsJSON;

        for (var i = 0; i < chekboxes.length; i++) {
            if(chekboxes[i].checked){
                reqs.push(chekboxes[i].value);
            }
        }
        reqsJSON = JSON.stringify(reqs);



  window.open("download_xls.php?request_id="+reqsJSON);

}
function all_report(){

  window.open("download_all_xls.php");

}
function markAll(){
  var check = document.getElementsByClassName('downloadxls');
  if(check[0].checked==true){
    for(var i=0; i<check.length; i++){
      check[i].checked = false;
    }
  }else{
    for(var i=0; i<check.length; i++){
      check[i].checked = true;
    }
  }
}

</script>
