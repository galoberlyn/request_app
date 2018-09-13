<?php
include('login.php');
?>
<?php

if(isset($_SESSION['username'])) {
  header("Location: dashboard/dashboard.php");
}
?>
<!DOCTYPE html>
<html >
<head>
<link rel="stylesheet" href="../assets/css/style.css">

  <meta charset="UTF-8">
  <title>Log In</title>



  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

  
      <link rel="stylesheet" href="index/css/style.css">

  
</head>

<body>
  <div class="login">
  <header class="header">
    <span class="text">LOGIN</span>
    <span class="loader"></span>
  </header>
  <form class="form" method="POST">  
    <input class="input" name='myusername' type="text", placeholder="User" required>
    <input class="input" name='mypassword' type="password" placeholder="Password" required>
    <button class="btn" type="submit" name="submit"></button>
  </form>
</div>
<div style='text-align: center;font-size:3em;margin-top: 35%;'> <a style='text-decoration: none; color:black;' href='register.php'> Register </a> </div> 
<div style='text-align: center; font-size:1em;margin-top: 2%;'><p> NOTE: Please enable popups & JavaScript to experience the full features of the application </p> </div>


  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

    <script src="index/js/index.js"></script>

</body>
</html>
