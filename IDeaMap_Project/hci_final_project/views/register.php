<?php
 include 'myLib.php';
 $fError = NULL;
 $lError = NULL;
 $pError = NULL;
 $cpError = NULL;
 $emError = NULL;
 $err = NULL;
 
 $db = connect_pdo($hostname, $username, $password, $dbname);
 
	if (array_key_exists('submit', $_POST)) {
		$ctr = 0;
		
		if(empty($_POST['first_name'])){
			$fError = "*";
			$ctr++;
		}
		if(empty($_POST['last_name'])){
			$lError = "*";
			$ctr++;
		}
		if(empty($_POST['password'])){
			$pError = "*";
			$ctr++;
		}
		else{
			if(strlen($_POST['password'])>10)
			{
				$pError = "exceed maximum characters(10)";
				$ctr++;
			}
		}
		if(empty($_POST['re_password']))
		{
			$cpError = "*";
			$ctr++;
		}
		else
		{
			if($_POST['password'] != $_POST['re_password']){
				$cpError ="passwords must match";
				$ctr++;
				}
		}
		if(empty($_POST['email']))
		{
			$emError = "*";
			$ctr++;
		}
		else
		{
			$email = $_POST['email'];
			if(doesExist($db, $email)){
				$emError = "email exist";
				$ctr++;
			}
			else
			{
				if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
					$emError = "invalid email address";
					$ctr++;
				}
			}
		}
		if($ctr == 0)
		{
			submitStudent($db);
			setcookie('userEmail', $_POST['email']);
		}
		else
			$err = "* required";
	}
	else{}
	
	function doesExist($db, $email)
	{
		$checkQuery = "SELECT lname FROM student WHERE email = '$email'";
		$result = simple_query($db, $checkQuery, array());
		$ret = $result->fetchAll();
		if(count($ret) > 0)
			return true;
		else
			return false;
	}
	
	function submitStudent($db)
	{
		$fname = $_POST['first_name'];
		$lname = $_POST['last_name'];
		$pswd = $_POST['password'];
		$email = $_POST['email'];
		
		$insertQuery = "INSERT into student(fname, lname, email, password) VALUES(?,?,?,?)";
		
		$stmt = $db->prepare($insertQuery);

		if ($stmt == FALSE) {
			$errarray = $db->errorInfo();
			$errmsg = $errarray[2];  
			die();
		}

		$queryargs = array($fname, $lname, $email, $pswd);

		$ret = $stmt->execute($queryargs);

		if ($ret == FALSE) {
			$errarray = $stmt->errorInfo();
			$errmsg = $errarray[2];  
			print("<b>$errmsg</b><p>\n");
			$fail=1;
		} else {
			$stmt->closeCursor();
		}
		redirect();
	}
	function redirect(){
		echo "<script language='javascript' type='text/javascript'>
					window.location=\"http://localhost/hci_proj/views/ideamap.php\"
				</script>";
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd"
    >
<html lang="en">
<head>
<link rel="stylesheet" type="text/css" href="styles/register.css"></link>
    <title>Register | IDea-Map</title>

</head>
<style type="text/css">

</style>
<body>
    <div id="wrapper">
	<?php
//function printform(){	
print <<<FORM
        <form id='form' action="" method="post">
            <fieldset>
                <legend>Register Form</legend>
                <div>
                    <input type="text" name="first_name" placeholder="First Name"/>
					<label><font color="red"> $fError</font></label>
                </div>
                <div>
                    <input type="text" name="last_name" placeholder="Last Name"/>
					<label><font color="red"> $lError</font></label>
                </div>
                <div>
                    <input type="password" name="password" placeholder="Password"/>
					<label><font color="red"> $pError</font></label>
                </div>
                <div>
                    <input type="password" name="re_password" placeholder="Confirm"/>
					<label><font color="red"> $cpError</font></label>
                </div>
                <div>
                    <input type="text" name="email" placeholder="Email"/>
					<label><font color="red"> $emError</font></label>
                </div>
				<div><label><font color="red">$err</font></label></div>
                <div>				
                </br><input type="submit" name="submit" value="Submit"/>  <a href="sign_in.php">Login</a>
            </fieldset>    
        </form>
FORM;
?>
    </div>
</body>
</html>
