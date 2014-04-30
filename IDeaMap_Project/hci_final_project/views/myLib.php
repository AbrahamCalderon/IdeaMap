<?php
include 'myPassword.php';

function connect_pdo($hostname, $username, $password, $database) {
    try {
    	$dbconn = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    /*** echo a message saying we have connected ***/
    //	echo 'Connected to database<p>';
	return $dbconn;
    }
    catch(PDOException $e) {
    	echo $e->getMessage();
	return null;
    }
}

function getStudentID($db, $userEmail)
{
	$getID = "SELECT student_id FROM student WHERE email = '$userEmail'";

	$result = simple_query($db, $getID, array());
	$id = $result->fetchAll(PDO::FETCH_COLUMN);
	
	return $id;
}

function getProjectID($db, $pname)
{
	$id = array();
	$pID = "SELECT p.proj_id FROM project p WHERE p.pname = '$pname'";

	unset($result);
	
	$result = simple_query($db, $pID, array());
	$id = $result->fetchAll(PDO::FETCH_COLUMN);
	
	return $id;
}

function simple_query($db, $query, $queryargs)
{
	$stmt = $db->prepare($query);

	if ($stmt == FALSE) {
		$errarray = $db->errorInfo(); // error would live in $db
		$errmsg = $errarray[2];  
		
		$error = "Prepare-statement error for query \"$query\": error is $errmsg";
		return $error;
	}

	$ret = $stmt->execute($queryargs);	// $stmt is the PDOStmt object

	if ($ret == FALSE) {
		print("execution of query not successful: \"$query\"<p>\n");
		$errarray = $stmt->errorInfo();
		$errmsg = $errarray[2];  
		$error = "query execution unsuccessful: \"$query\"; error is $errmsg";
		print $error;
		return $error;
	} 
	return $stmt;
}

function getCategories($db)
{
	$query = "SELECT cat_id, cname FROM category";
	$result = simple_query($db, $query, array());
	$cats = $result->fetchAll();
	return $cats;
}
function getSubs($db)
{
	$query = "SELECT sub_id, subname FROM sub_category";
	$result = simple_query($db, $query, array());
	$subs = $result->fetchAll();
	return $subs;	
}
function selectBox($names, $menuName)
{
	$menu = "<select name=\"$menuName\">\n";
	$menu = $menu . "<option value=0 selected>Select</option>\n";
	foreach ($names as $name) {
		$menu = $menu . "<option value=\"$name[0]\">$name[1]</option>\n";
	}
	$menu = $menu . "</select>";
	return $menu;
}


 function onJoin($db, $pn)
 {
	$p_id = getProjectID($db,$pn);
	$projID = $p_id[0];
	
	$userEmail = $_COOKIE['userEmail'];
	$sID = getStudentID($db, $userEmail);
	//echo $sID;
	$stID = $sID[0];
	
	$joinQuery = "INSERT INTO works_on (p_id, st_id) VALUES(?, ?)";
		
	$stmt = $db->prepare($joinQuery);
	if ($stmt == FALSE) {
		$errarray = $db->errorInfo();
		$errmsg = $errarray[2];  
		die();
	}

	$queryargs = array($projID, $stID);
	$ret = $stmt->execute($queryargs);

	if ($ret == FALSE) {
		$errarray = $stmt->errorInfo();
		$errmsg = $errarray[2];  
		print("<b>$errmsg</b><p>\n");
		$fail=1;
	} else {
		$stmt->closeCursor();
	}
}

function postProj($db, $outputValues)
{
	$ptitle = $outputValues['ptitle'];
	$pdescription = $outputValues['pdescription'];
	$category = $outputValues['category'];
	$subcategory = $outputValues['subcategory'];
	
	$email = $outputValues['user_email'];
	$sId = getStudentID($db, $email);
	//var_dump($sId);
	//$id = $sId[0][0];

	//send as transaction
	$Insert = "INSERT INTO project (pname, pdescription, subId) VALUES (?, ?, ?)";
	
	$stmt = $db->prepare($Insert);

	if ($stmt == FALSE) {
		print("failed to prepare statement: \"$Insert\"<p>\n");
		$errarray = $db->errorInfo();
		$errmsg = $errarray[2];  
		print("<b>Prepare error: $errmsg</b><p>\n");	// error would live in $db
		die();
	}

	$queryargs = array($ptitle, $pdescription, $subcategory);

	$ret = $stmt->execute($queryargs);

	if ($ret == FALSE) {
		echo '<font color="red"><b>insert failed : </b></font>';
		$errarray = $stmt->errorInfo();
		$errmsg = $errarray[2];  
		print("<b>$errmsg</b><p>\n");
		$fail=1;
	} else {
		$stmt->closeCursor();
		echo '<font color="green"><b>insert successful</font></br>';
	}
}

function appendStudent($db, $someEmail, $someTitle){
	unset($pId);
	unset($stId);
	
	$stId= getStudentID($db, $someEmail);
	$studentId = $stId[0];
	
	$pId = getProjectID($db, $someTitle);
	$projectId = $pId[0];
	
	$Insert1 = "INSERT INTO works_on (p_id, st_id) VALUES (?, ?)";
	
	$stmt1 = $db->prepare($Insert1);

	if ($stmt1 == FALSE) {
		print("failed to prepare statement: \"$Insert1\"<p>\n");
		$errarray = $db->errorInfo();
		$errmsg = $errarray[2];  
		print("<b>Prepare error: $errmsg</b><p>\n");	// error would live in $db
		die();
	}

	$queryargs1 = array($projectId, $studentId);

	$ret1 = $stmt1->execute($queryargs1);

	if ($ret1 == FALSE) {
		echo '<font color="red"><b>insert failed : </b></font>';
		$errarray = $stmt1->errorInfo();
		$errmsg = $errarray[2];  
		print("<b>$errmsg</b><p>\n");
		$fail=1;
	} else {
		$stmt1->closeCursor();
		echo '<font color="green"><b>insert successful</font></br>';
	}
}
function getAllProjectNames($db){
	$projQuery = 'select p.pname from project p';
	$res = simple_query($db, $projQuery, array());
	$ret = $res->fetchAll(PDO::FETCH_COLUMN);
	return $ret;
}
?>