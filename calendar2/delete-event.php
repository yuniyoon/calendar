<?php
if(!empty($_POST['id'])){
	include_once 'dbconnect.php'; // DB 연결

	$id = $_POST['id'];
	$sqlDelete = "DELETE from tbl_events WHERE id=".$id;
	$result = mysqli_query($dbconn, $sqlDelete);
	if($result){
		echo 1;
	} else {
		echo 0;
	}
	mysqli_close($dbconn);
}
?>