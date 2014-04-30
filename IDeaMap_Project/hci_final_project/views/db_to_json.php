<?php
/* script to read from database and return string in JSON needed for d3 layout */
function getJSON($db)
{
$masterString = main($db);
$filename = 'TEST_FLARE.json';

//Check file exist
if (is_writable($filename)) {
    if (!$handle = fopen($filename, 'w')) {
         echo 'Cannot open file ($filename)';
         exit;
    }
    // Write $somecontent to our opened file.
    if (fwrite($handle, $masterString) === FALSE) {
        echo 'Cannot write to file ($filename)';
        exit;
    }
    fclose($handle);
} 
else {
    echo 'The file $filename is not writable';
}

}//end main func
function main($db){
	$ms = "{\"name\": \"Projects\",\n";
	$ms = $ms . "\"children\":[\n";  //START main Project and Children tags
	
	//GET main categories
	$q = "SELECT c.cname, c.cdescription FROM category c";
	$stmt = simple_query($db, $q, array());
	$catNames = $stmt->fetchAll(); //get all results that query returned	
	
		//loop through each category
		foreach ($catNames as $catName){
			$ms = $ms . "{\n\"name\": \"$catName[0]\", \"description\":\"$catName[1]\",\n";
			$ms = $ms . "\"children\": [\n"; //start children for categories
			
			//GET sub-categories
			$q1 = "SELECT s.subname FROM sub_category s, category c WHERE s.catId = c.cat_id AND c.cname = '$catName[0]'";
			$stmt1 = simple_query($db, $q1, array());
			$subNames = $stmt1->fetchAll(); //get all results that query returned

			//loop through each sub-category
			foreach($subNames as $subName){
				$sub_cat = $subName[0];
				$ms = $ms . "{";	//start sub_cat curly
				$ms = $ms . "\"name\":\"$sub_cat\",\n";
				$ms = $ms . "\"children\":[\n";
				
					//GET proj-titles
					$q2 = "SELECT p.pname, p.pdescription FROM project p, sub_category s WHERE s.sub_id = p.subId AND s.subname = '$sub_cat'";
					$stmt2 = simple_query($db, $q2, array());
					$projNames = $stmt2->fetchAll(); //get all results that query returned

					//loop through each project
					foreach($projNames as $projName){
						$pname = $projName[0];
						$pdescription = $projName[1];
						$ms = $ms . "{\"name\": \"$pname\",\n";
						$ms = $ms . "\"description\":\"$pdescription\",\n";
						$ms = $ms . "\"children\":[\n"; //start children for Students
					
						//GET students
						$q3 = "SELECT st.fname, st.email FROM student st, works_on w, project p WHERE w.st_id = st.student_id AND w.p_id = p.proj_id AND p.pname = '$pname'";
						$stmt3 = simple_query($db, $q3, array());
						$students = $stmt3->fetchAll(); //get all results that query returned
					
						//loop through each student
						foreach($students as $student){
							$stName = $student[0];
							$stEmail = $student[1];
							if($student == end($students)){
								$ms = $ms . "{\"name\":\"$stName\", \"email\":\"$stEmail\"}";
							}
							else{
								$ms = $ms . "{\"name\":\"$stName\", \"email\":\"$stEmail\"},";
							}
							
						}//END foreach--student loop
						
						//check for END project
						if($projName == end($projNames)){
						$ms = $ms . "]\n"; 
						$ms = $ms . "}\n";						
						}
						else
						{
						$ms = $ms . "]\n"; 
						$ms = $ms . "},\n";						
						}
						
					}//END foreach--project loop
					
					//check for END sub-cat
					if($subName == end($subNames))
					{
					$ms = $ms . "]\n";
					$ms = $ms ."}\n";
					}
					else
					{
					$ms = $ms . "]\n";
					$ms = $ms ."},\n";					
					}

			}//END foreach--sub-category loop
			
			//check for END category
			if($catName == end($catNames))
			{
			$ms = $ms . "]\n";
			$ms = $ms . "}\n";				
			}
			else
			{
			$ms = $ms . "]\n";
			$ms = $ms . "},\n";			
			}
		}//END foreach--category loop
	
	//INCLUDE CLOSING END TAGS for entire json file
	$ms = $ms . "]}";
	
	//return string TEST
	return $ms;
}
?>