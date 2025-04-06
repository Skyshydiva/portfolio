<?php
	$fildir='grading/';
	$page='Grading';
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
