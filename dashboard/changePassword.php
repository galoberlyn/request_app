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
                $nothing = 1;
    ?>
  <section class="menu-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse ">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a href="dashboard.php"><i class="glyphicon glyphicon-home"></i> Dashboard</a></li>
                            <li><a href="addrequest.php"><i class="glyphicon glyphicon-plus"></i> Add New Request</a></li>
                            <li><a class="menu-top-active" href="changePassword.php"><i class="glyphicon glyphicon-lock"></i> Change password</a></li>
                            <li><a href="../dashboard/aboutUs.php"><i class="fa fa-info-circle"></i> About Us</a></li>
                            <li><a href="../logout.php">Log out</a></li>


                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>
 

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
    
        echo "<div class='wrapperChangePassword'>";
        echo "<div class='password'>";
        echo "<div class='panel-body'>";
        echo "<div class='col-md-14'>";
        echo "<h4 class='page-head-line'>Change Password</h4> ";
        echo "</div>";
        echo "</div>";
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
        echo "<div class='changePasswordButton'>";
        echo "<input type='submit' name='change'  class='btn btn-primary'value='Change'>";
            
        echo "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>";  
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "<br>";
    
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
</body>
</html>