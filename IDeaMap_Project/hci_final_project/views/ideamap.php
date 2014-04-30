<?php
include 'myPassword.php';
include 'myLib.php';
include 'db_to_json.php';

//create db connection
$db = connect_pdo($hostname, $username, $password, $dbname);

//get data for visual
getJSON($db);


$category = getCategories($db);
$cat_menu = selectBox($category, "category");

$subcategory = getSubs($db);
$sub_menu = selectBox($subcategory, "subcategory");

	if (array_key_exists('submit', $_POST)) {
		$allProjectNames = getAllProjectNames($db);
		$outputValues = array();
		$ctr = 0;

		if(empty($_POST['ptitle'])){
			//echo 'inside firstname empty';
			$tError = "*";
			$ctr++;
			$outputValues['tError'] = $tError;
		}
		else if(in_array($_POST['ptitle'], $allProjectNames)){
			$tError = "project title already exist";
			$ctr++;
			$outputValues['tError'] = $tError;
		}			
		else
			$outputValues['ptitle'] = $_POST['ptitle'];
		if(empty($_POST['category']) || empty($_POST['subcategory'])){
			$cError = "*";
			$ctr++;
			$outputValues['cError'] = $cError;		
		}
		else{
			$outputValues['category'] = $_POST['category'];
			$outputValues['subcategory'] = $_POST['subcategory'];
			}
		if(empty($_POST['pdescription'])){
			$dError = "*";
			$ctr++;
			$outputValues['dError'] = $dError;
		}
		else
			$outputValues['pdescription'] = $_POST['pdescription'];
		
		if($ctr ==0){
			$outputValues['user_email'] = $_COOKIE['userEmail'];
			//var_dump($outputValues);
			postProj($db, $outputValues);
			appendStudent($db, $outputValues['user_email'], $outputValues['ptitle']);
			//document.write();
			redirect();
		}
		else
			var_dump($outputValues);
	}
	else if (array_key_exists('join', $_POST))
	{
		echo 'enter join loop';
		$pname = $_POST['pname'];
		echo $pname;
		onJoin($db, $pname);
		redirect();
	}
	else{
	}
	
	function redirect(){
		echo "<script language='javascript' type='text/javascript'>
					window.location=\"http://localhost/hci_proj/views/ideamap.php\"
				</script>";
	}	
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>IdeaMap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!--bootstrap-->
	<link rel="stylesheet" type="text/css" href="bootstrap1/css/bootstrap-responsive.css"/>
	<link rel="stylesheet" type="text/css" href="bootstrap1/css/bootstrap.css"/>	
	<link rel="stylesheet" type="text/css" href="bootstrap1/css/bootstrap.min.css"/>	
	<link rel="stylesheet" type="text/css" href="bootstrap1/css/bootstrap-responsive.min.css"/>
	<link type="text/css" rel="stylesheet" href="styles/moreStyles.css"/>
	
	<script src="custom-tooltip.js"></script>
	
	<script src="http://code.jquery.com/jquery.min.js"></script>
    <script type="text/javascript" src="d3/d3.js"></script>
    <script type="text/javascript" src="d3/d3.layout.js"></script>	
	<script type="text/javascript" src="bootstrap1/js/bootstrap.js"></script>
	<link href="styles/inspiritas.css" rel="stylesheet">
	
<!-- d3 tree layout specific styles -->	
<style type="text/css">
.node circle {
  cursor: pointer;
  fill: #003CFF;
  stroke: #024D04;
  stroke-width: 1.5px;
}

.node text {
  font-size: 18px;
  stroke: black;
}

path.link {
  fill: none;
  stroke: #FF00BF;
  stroke-width: 1.5px;
}

#clicker
{
	font-size:20px;
	cursor:pointer;
}
</style>
</head>

<body>

<!-- Navbar
  ================================================== -->
<div class="navbar navbar-static-top navbar-inverse">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand" href="#">IDea-Map</a>
      <span class="tagline">Welcome to IDea-Map. <a href="#">Read more.</a></span>

      <div class="nav-collapse collapse" id="main-menu">
        <div class="auth pull-right">
			<p class="navbar-text pull-right"><a href="#" style="font-size: 16px;">Sign-out</a></p><p class="navbar-text pull-right">&nbsp;&nbsp;&nbsp;&nbsp;</p><p class="navbar-text pull-right"><i class="icon-user icon-white"></i>
            <span id="userEmail" class="name" style="font-size: 16px;"></span><br/><!-- session id name -->
			
        </div>
      </div>
    </div>
  </div>
</div>

<div style="padding-left:15px; width:65%;" class="container">
    <div class="row-fluid">
        <div style="width:100%;" id="content-wrapper">
            <div style="background-image:url('images/grey.png');"id="content">
                <section id="stats">
                  <header>
                    <div class="pull-right">
                        <a href="#postForm" role="button" class="btn btn-large" data-toggle="modal">Post</a>
                    </div>
                    <h1>COMP 351 Human Computer Interaction</h1>
                  </header>
                </section>
                <!-- The Visualization
                ================================================== -->
               <section id="forms">
				
                  <div class="sub-header">
                    <h2 style="font-size: 18px;">Project Ideas</h2>
                  </div>
                    <div id="myGraph" style="width:100%;">
							<script src="main_test.js"></script>
                    </div>
			</section>
            </div>
        </div>
    </div>
	
</div><!-- /container -->

	<!-- Project Modal-->
    <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel"></h3>
		</div>
		<div class="modal-body">
			<div id="projContent"></div>
			<?php
			$t = "";
			print <<<BUTTON
			<form action="" method="post">
			<div><button type="submit" name="join" class="btn btn-primary">Join Group</button></div>
			<div><input id="text" type="text" name="pname" value=$t readonly></input></div>
			</form>
BUTTON;
?>
		</div>
		<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		</div>
    </div>	

	<!-- Email Modal-->
    <div id="emailForm" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Email</h3>
		</div>
		<div class="modal-body">
			
			<?php
			$uemail = $_COOKIE['userEmail'];
			$re = "";
print <<<FORM
	<form action="mailto:$re" method="POST" enctype="text/plain">
	<p>Name: <input type="text" name="name" /></p>
	<p>Email: <input type="text" name="email" value=$uemail></p>
	To: <input id="rec" type="text" name="toEmail"></input>
	<p>Comments:<br /><br />
	<textarea cols="50" rows="10" name="comments"></textarea></p>
	<p><input type="submit" name="submit" value="Send" />
	<input type="reset" name="reset" value="Clear Form" /></p>
	</form>
FORM;
?>

		</div>
		<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		</div>
    </div>		
	
	<!-- PostIdea Modal-->
    <div id="postForm" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Post an Idea</h3>
		</div>
		<div class="modal-body">
			<div id="projInfo">
			<?php
print <<<FORM
    <form action="" method="post">
    <label>Title *</label><input name="ptitle" type="text" placeholder="Type something…">
	<label>Category</label>$cat_menu
	<label>Subcategory</label>$sub_menu
	<label>Description</label><textarea name="pdescription" rows="5"></textarea></br>
	<button type="submit" name="submit" class="btn btn-primary">Post</button>
    </form>
FORM;
?>
			</div>
		</div>
		<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		</div>
    </div>
	
	<!-- javascript COOKIE functionality-->
	<script>
	var userEmail = getCookie('userEmail');
	var t = document.getElementById('userEmail');
	t.innerHTML = t.innerHTML+userEmail;

	var v = getCookie('userEmail');
	checkCookie();
	
	function getCookie(c_name)
	{
		var c_value = document.cookie;
		var c_start = c_value.indexOf(" " + c_name + "=");
		if (c_start == -1)
		{
			c_start = c_value.indexOf(c_name + "=");
		}
		
		if (c_start == -1)
		{
			c_value = null;
		}
		
		else
		{
			c_start = c_value.indexOf("=", c_start) + 1;
			var c_end = c_value.indexOf(";", c_start);
			
			if (c_end == -1)
			{
				c_end = c_value.length;
			}
		
			c_value = unescape(c_value.substring(c_start,c_end));
		}
		return c_value;
	}

	function checkCookie()
	{
		var useremail=getCookie("userEmail");
		if (useremail!=null && useremail!="")
		{
			//alert("USER EMAIL " + useremail);
		}
		else
			alert ('COOKIE NOT FOUND');
	}
	</script>
  </body>
</html>