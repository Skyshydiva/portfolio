<?php
//   remove jrhicsa and insert your username twice
//   in between ' ' place your Database Password you found in .my.cnf
//                                      user      passwd    database
$mysqli = mysqli_connect("localhost", 'smc9253', 'Shagged9%disgusted',  'smc9253');
if (mysqli_connect_errno()) {
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
	}
?>