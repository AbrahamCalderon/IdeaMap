<?php
 include 'myLib.php';
 $db = connect_pdo($hostname, $username, $password, $dbname);
 $errorMsg = "";

 if (array_key_exists('submit', $_POST)){
	if(!empty($_POST['email']) && !empty($_POST['password'])){
		$email = $_POST['email'];
		$pswrd = $_POST['password'];
		$exist = authenticate($db, $email, $pswrd);
		echo $exist;
		if($exist == false){
			$errorMsg = "Invalid Password or Email";
			redirect("http://localhost/hci_proj/views/sign_in.php");
			}
		else
			setcookie('userEmail', $_POST['email']);
			redirect("http://localhost/hci_proj/views/ideamap.php");
	}
}
 
function authenticate($db, $email, $pswrd)
{
	$existsQuery = "SELECT fname FROM student WHERE email = '$email' AND password = '$pswrd'";
	$result = simple_query($db, $existsQuery, array());
	$ret = $result->fetchAll();
	if(count($ret) > 0)
		return true;
	else
		return false;
}

function redirect($page){
	echo "<script language='javascript' type='text/javascript'>
				window.location=\"$page\"
			</script>";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd"
    >
<html lang="en">
<head>
<link rel="stylesheet" type="text/css" href="styles/sign_in.css"></link>
    <title>Sign-in | IDea-Map</title>
</head>
<body>
    <div id="wrapper">
  <?php
	print <<<FORM
  <form action="" method="post">
            <fieldset>
                <legend>Login</legend>
                <div>
                    <input type="text" name="email" placeholder="Email"/>
                </div>				
                <div>
                    <input type="password" name="password" placeholder="Password"/>
                </div>
                <input type="submit" name="submit" value="Login"/>
				<label value='$errorMsg'/>
            </fieldset>    
        </form>
FORM;
?>
    </div>
</body>
</html>
