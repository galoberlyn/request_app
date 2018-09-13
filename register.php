<?php
	require('/shared/connection.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
    <!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
    <!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
    <!--[if IE 9]> <html class="no-js ie9 oldie" lang="en"> <![endif]-->
    <meta charset="utf-8">
    <!-- Set the viewport width to device width for mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="Coming soon, Bootstrap, Bootstrap 3.0, Free Coming Soon, free coming soon, free template, coming soon template, Html template, html template, html5, Code lab, codelab, codelab coming soon template, bootstrap coming soon template">
    <title>Register</title>
    <!-- ============ Google fonts ============ -->
    <link href='http://fonts.googleapis.com/css?family=EB+Garamond' rel='stylesheet'
        type='text/css' />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300,800'
        rel='stylesheet' type='text/css' />
    <!-- ============ Add custom CSS here ============ -->
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
      <link href="css/style.css" rel="stylesheet" type="text/css" />    
   
    <link href="css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="css/placing.css" rel="stylesheet" type="text/css" />

</head>
<body>
        <div class="placing">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="registrationform">
            <form method="POST" class="form-horizontal">
                <fieldset>
                    <legend>Registration Form <a href="../requetfinals/index.php"class="fa fa-pencil pull-right" "glyphicon glyphicon-remove-circle"></a></legend>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">
                            Username</label>
                        <div class="col-lg-10">
                            <input type="text" class="form-control" name="username" required >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">
                        Password</label>
                        <div class="col-lg-10">
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">
                            Confirm Password</label>
                        <div class="col-lg-10">
                            <input type="password" class="form-control" name="password2" required >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">
                            Firstname</label>
                        <div class="col-lg-10">
                            <input type="firstname" class="form-control" name="firstname" required >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" required>
                            Lastname</label>
                        <div class="col-lg-10">
                            <input type="lastname" class="form-control" name="lastname" required >
                        </div>
                    </div>
                    
<!--
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">
                            Selects</label>
                        <div class="col-lg-10">
                            <select class="form-control" id="select">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                           
                        </div>
                    </div>
-->
                    <div class="form-group">
                        <div class="col-lg-10 col-lg-offset-2">
                            <button type="reset" class="btn btn-warning">
                                Clear</button>
                            <button type="submit" class="btn btn-primary" name="submit">
                                Register</button>
                        </div>
                    </div>
                </fieldset>
            </form>
         </div>


            </div>
         </div>
       
        <script src="js/jquery.js" type="text/javascript"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery.backstretch.js" type="text/javascript"></script>
		<?php
			if(isset($_POST['username'])){
				if(isset($_POST['submit'])){
					$userName = $_POST['username'];
					$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
					$firstName = $_POST['firstname'];
					$lastName = $_POST['lastname'];	
					if($_POST['password'] == $_POST['password2']){
						$userSql = $conn->prepare('INSERT into users(username, password, created_at, updated_at) values (?, ?, NOW(), NOW())');
                        $userSql->bind_param('ss',$userName,$password);
						if($userSql->execute()){
							$user_id = mysqli_insert_id($conn);
							$userDetSql = $conn->prepare('INSERT into user_details(user_id, firstname, lastname, created_at, updated_at) values (?, ?, ?, NOW(), NOW())');
                            $userDetSql->bind_param('iss',$user_id,$firstName,$lastName);
							if($userDetSql->execute()){ 	
									echo "<script type='text/javascript'>alert('You have successfully registered! Please log in to continue...');
									window.location = 'index.php';</script>";	
							}						
						}else{
							$error = mysqli_error($conn);
                    

		                    if(strpos($error, 'Username') !== false){
		                        echo "<script type='text/javascript'>
		                        alert('Username already exists, please choose another one')</script>";

		                    }
						}
					}else {
	        			echo "<script>alert('Password does not match!')</script>";
	    			}
	    // 			if(isset($_GET['error']) && $_GET['error'] == 'usernameTaken'){
					// echo "<script>alert('Username already taken')</script>";
					// }
				}
			}
		?>
	</body>
</html>	