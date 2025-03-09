<?php
	$fildir='comments/';
	$page='Comments';
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

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    if ($mysqli) {
        if (!empty($_POST['name']) && !empty($_POST['comment'])) {
    
            $name = test_input($_POST['name']);
            $comment = test_input($_POST['comment']);
            $date = date("h: i A / m-d-Y", time());
    
            $stmt = $mysqli->prepare("INSERT INTO comments (name, comment) values (?, ?)");
            $stmt->bind_param("ss", $name, $comment);
            $stmt->execute();
            $stmt->close();
        }
    }
    
        $sql = 'SELECT name, comment, date FROM comments';
        $res = $mysqli->query($sql);
        
    ?>

    <div id="list">
            <?php
                if(mysqli_num_rows($res) > 0){
                    echo "<table>
                        <tr>
                            <th>Name</th>
                            <th>Comments</th>
                            <th>Date</th>
                        </tr>";
                    while($row = $res->fetch_assoc()) {
                        echo "<tr> 
                        <td>" . $row['name'] . "</td>
                        <td>" . $row['comment'] . "</td>
                        <td>" . $row['date'] . "</td> </tr>";
                    }
                    echo "</table>";
                    $res->free();
                }
                ?>
        </div>
        </div> <!-- end of left-center class -->
    </div> <!-- end of wrapper tag -->
<?php
    require($path.'assets/inc/footer.php');
     mysqli_close($mysqli);

?>
