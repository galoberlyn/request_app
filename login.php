
<?php
 session_start();
    include('shared/connection.php');
    if(!empty($_POST['myusername']) && !empty($_POST['mypassword'])){
        if(isset($_POST['submit'])){

            
                if($stmt = $conn->prepare("SELECT id,username, password FROM users where username = ?")){

                    $stmt->bind_param("s", $username);
                }else{
                    echo "Developer: may mali sa query mo or di maka connect sa database";
                }
                $username = $_POST['myusername'];
                $password = $_POST['mypassword'];
                $stmt->execute();
                $stmt->bind_result($id, $user, $pass);
                $stmt->fetch();
                    if($user == $username AND password_verify($password, $pass)){
                        
                        $_SESSION['username'] = $user;
                        $_SESSION['id'] = $id;
                    
                        $message = "Login Success!";
                        echo "<script type='text/javascript'>
                        alert('$message');
                        
                        </script>";
                        header("Location: dashboard/dashboard.php");
                    }else{
                       echo "<script> var div = document.createElement('div');
                            div.setAttribute('class', 'alert');
                            var span = document.createElement('button');
                            span.setAttribute('class', 'closebtn');
                            span.setAttribute('onclick', 'this.parentElement.style.display=\'none\';');
                            var spanTxt = document.createTextNode('Done')
                            span.appendChild(spanTxt);
                            var pTxt = document.createTextNode('Wrong Credentials!');
                            var p = document.createElement('p');
                            p.appendChild(pTxt);
                            p.setAttribute('class', 'alertContent');
                            div.appendChild(p);
                            div.appendChild(span);
                            div.setAttribute('style', 'z-index:3');
                            document.body.appendChild(div); </script>";

                    }
                
                $stmt->close(); 
                $conn->close();

            

                
        } else {
                header("Location: index.php");
                exit;
        }


    }
?>