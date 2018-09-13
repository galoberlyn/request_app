<?php
include '../shared/connection.php';
include '../shared/authorization.php';
$username=$_SESSION['username'];
$user = $_SESSION['username'];
$query = "SELECT * from users inner join user_details on users.id=user_details.id where username= ?";
$stmt = $conn -> prepare($query);
$stmt -> bind_param("s",$username);
$stmt -> execute();
$queryArr = $stmt -> get_result() -> fetch_array();

if (isset($_POST['searchBar']) || isset($_POST['dateTo']) || isset($_POST['dateFrom'])) {
	$searchBar = $_POST['searchBar'];
	$dateFrom = $_POST['dateFrom'];
	$dateTo = $_POST['dateTo'];

}

?>
<!DOCTYPE html>
<html>
<head>
	<title>REQUISITION MONITORING/TRACKING</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
        <!-- styles -->
        <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../css/styleas.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
       <?php
                include "../header.php";
    ?>
    
    <!-- LOGO HEADER END-->
    <section class="menu-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse ">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a  href="dashboard.php"><i class="glyphicon glyphicon-home"></i> Dashboard</a></li>
                            <li><a href="addrequest.php"><i class="glyphicon glyphicon-plus"></i> Add New Request</a></li>
                            <li><a href="changePassword.php"><i class="glyphicon glyphicon-lock"></i> Change password</a></li>
                            <li><a class="menu-top-active" href="../dashboard/aboutUs.php"><i class="fa fa-info-circle"></i> About Us</a></li>
                            <li><a href="../logout.php">Log out</a></li>
                            

                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <br>
           <h1 class='text-center team'>Our Amazing Team</h1><br>
  
    
    
	<div id="body">
		
		<ul>
           <!--  <h2>2 years ago, a group of Web Developers went on an adventure. They met in #37 Scout Barrio and thus, the friendship began. At first they called themselves Kingsmen. Shortly thereafter, they changed their name to Chabaliers. But one day, a loner IT student called them "Cool Kids" instead of being frustrated, they changed their name and thus KOOL KIDZ was born.</h2> -->
			<li>
<!--
                    <img src="../images/geloas.png" alt="" height='300px' style="border-radius: 50%;" onmouseover="this.src='../images/angelo.jpg'" onmouseout="this.src='../images/geloas.png'" /><br>
					<span>Angelo Araos</span><br>
                    <span>Back-End Developer</span>
-->
                  <div class="col-sm-6">
 
                    <!-- colored -->
                    <div class="ih-item circle colored effect13 from_left_and_right"><a href="#">
                        <div class="img"><img src="../images/gelo.JPG" alt="img"></div>
                        <div class="info">
                          <div class="info-back">
                            <h3>Angelo Araos</h3>
                            <p>Back End Developer<br> 0905 368 0799</p>
                          </div>
                        </div></a></div>
                    <!-- end colored -->

                  </div>
			</li>
            <li>
                  <div class="col-sm-6">

                    <!-- colored -->
                    <div class="ih-item circle colored effect13 top_to_bottom"><a href="#">
                        <div class="img"><img src="../images/galo.jpg" alt="img"></div>
                        <div class="info">
                          <div class="info-back">
                            <h3>Galo Berlyn Garlejo</h3>
                            <p>Back End Developer<br> 0906 359 1886</p>
                          </div>
                        </div></a></div>
                    <!-- end colored -->

                  </div>

			</li>
            <li>
                <div class="col-sm-6">
 
                    <!-- colored -->
                    <div class="ih-item circle colored effect13 from_left_and_right"><a href="#">
                        <div class="img"><img src="../images/danzel.JPG" alt="img"></div>
                        <div class="info">
                          <div class="info-back">
                            <h3>Danzel Taccayan</h3>
                            <p>Back End Developer<br> 0906 172 8207</p>
                          </div>
                        </div></a></div>
                    <!-- end colored -->

                  </div> 
				
			</li>
            <li>

                <div class="col-sm-6">
 
                    <!-- colored -->
                    <div class="ih-item circle colored effect13 from_left_and_right"><a href="#">
                        <div class="img"><img src="../images/clint.jpg" alt="img"></div>
                        <div class="info">
                          <div class="info-back">
                            <h3>Clint Dalayoan</h3>
                            <p>Front End Developer<br> 0995 816 7067</p>
                          </div>
                        </div></a></div>
                    <!-- end colored -->

                  </div>
				
			</li>
            <li>
                  <div class="col-sm-6">

                    <!-- colored -->
                    <div class="ih-item circle colored effect17"><a href="#">
                        <div class="img"><img src="../images/teo.jpg" alt="img"></div>
                        <div class="info">
                          <h3>Teodoro Delson Jr.</h3>
                          <p>Front End Developer<br> 0905 326 4839</p>
                        </div></a></div>
                    <!-- end colored -->

                  </div>
				
			</li>
            <li>
                <div class="col-sm-6">
 
                    <!-- colored -->
                    <div class="ih-item circle colored effect13 from_left_and_right"><a href="#">
                        <div class="img"><img src="../images/Yuki.jpg" alt="img"></div>
                        <div class="info">
                          <div class="info-back">
                            <h3>Yuki Marfil</h3>
                            <p>Back End Developer<br> 0926 002 3117</p>
                          </div>
                        </div></a></div>
                    <!-- end colored -->

                  </div> 
				
			</li>
			<li>
                <div class="col-sm-6">
 
                    <!-- colored -->
                    <div class="ih-item circle colored effect13 from_left_and_right"><a href="#">
                        <div class="img"><img src="../images/miguel.jpg" alt="img"></div>
                        <div class="info">
                          <div class="info-back1">
                            <h3>Juan Delos Santos</h3>
                            <p>Back End Developer<br> 0917 475 1624</p>
                          </div>
                        </div></a></div>
                    <!-- end colored -->

                  </div>                
				
			</li>
			<li>
                  <div class="col-sm-6">

                    <!-- colored -->
                    <div class="ih-item circle colored effect13 bottom_to_top"><a href="#">
                        <div class="img"><img src="../images/jl.JPG" alt="img"></div>
                        <div class="info">
                          <div class="info-back">
                            <h3>Jan Lorenz Aurelio</h3>
                            <p>Front End Developer<br> 0926 953 4385</p>
                          </div>
                        </div></a></div>
                    <!-- end colored -->

                  </div>
				
			</li>
            <li>
                <div class="col-sm-6">
 
                    <!-- colored -->
                    <div class="ih-item circle colored effect13 from_left_and_right"><a href="#">
                        <div class="img"><img src="../images/chabal.jpg" alt="img"></div>
                        <div class="info">
                          <div class="info-back">
                            <h3>Randall Caballar</h3>
                            <p>Front End Developer<br> 0995 293 3268</p>
                          </div>
                        </div></a></div>
                    <!-- end colored -->

                  </div> 
				
			</li>    
		</ul>   
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
            $user_pass = "Select password from users where username = '$username';";     
        $user_passQ = mysqli_query($conn,$user_pass) or die(mysqli_error($conn));
    
        if (isset($_POST['newpass']) && isset($_POST['connewpass']) && isset($_POST['oldpass'])){
            $newpass = $_POST['newpass'];
            $connewpass = $_POST['connewpass'];
            $oldpass = $_POST['oldpass'];
        }
        
        $user = mysqli_fetch_array($user_passQ);
        
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
                echo "<script> alert('Successfully changed password, please login again');</script>";
                session_unset();
                session_destroy();
                echo "<script> window.location='../index.php'; </script>";
                exit;

            } else {
                echo "<script> alert('Incorrect password or Password does not match the confirm password!'); </script>";
            }
        }
            ?>
      </div>
      
    </div>
  </div>
  
</div>
                </div>
                <footer>
  <p style='color:white;text-align: center'>If you have concerns regarding the application, Our contact info is in our photos!</p>
  <p style='color:white;text-align: center'> Or Contact Us Through: <a href="mailto:someone@example.com">gbgarlejo@gmail.com</a></p>
</footer>
</body>
</html>
    
