<?php
    $name  = $_POST['name'];
    $groupNumber = $_POST['groupNumber'];
    $vdate    = $_POST['vdate'];
    $state    = $_POST['state'];
    $place    = $_POST['fplace'];
    $rating   = $_POST['rating'];
    $destination_email = "RITISTprofessor@gmail.com, smc9253@rit.edu";
    $email_subject = "Baltimore, MD.  - visitor $name";
    $email_body = "Visitor name:  $name\n";
    $email_body .= "Group Size: $groupNumber\n";
    $email_body .= "Date Visited: $vdate\n";
    $email_body .= "Visitor's Home State: $state\n";
    $email_body .= "Favorite Place: $place ";

    $email_body .= "\nRating -> $rating\n";

    $email_body .= "\n Updated December 2023 \n";
    mail($destination_email, $email_subject, $email_body);
?>

<?php
	$fildir='feedback/';
	$page='Results';
	$path='../';
	require ($path.'assets/inc/header.php');
?>

<?php
	include($path.'assets/inc/nav.php');
?>
	<?php

		require $path.'../../dbConnect.inc'; 
	
	//internal CSS
	$sql = "SELECT CSS_Internal FROM modularSite where page='$page'";
	$result = $mysqli->query($sql);

	if($result->num_rows > 0){
		//output the data for each row
		while ($row = $result->FETCH_ASSOC()) {
			echo $row['CSS_Internal'];
		}
	}

		$sql = "SELECT content FROM modularSite where page='$page'";
		$result = $mysqli->query($sql);

		if($result->num_rows > 0){
			//output the data for each row
			while ($row = $result->FETCH_ASSOC()) {
				echo $row['content'];
			}
		}else{
			echo "0 results, did something wrong!";
		}
	?>
<?php
    require($path.'assets/inc/footer.php');
     mysqli_close($mysqli);

?>