<?php
if(!empty($_POST['title']) && !empty($_POST['year']) && !empty($_POST['month']) && !empty($_POST['day'])){
	extract($_POST);
	$start = date("Y-m-d", mktime(0, 0, 0, $month, $day, $year));
	$end = date("Y-m-d", mktime(0, 0, 0, $month, $day, $year));

	include_once 'dbconnect.php'; // DB 연결
	$sql = "INSERT INTO tbl_events (title,start,end) VALUES ('".$title."','".$start."','".$end ."')";
	$result = mysqli_query($dbconn, $sql);
	if($result){
		echo 1;
	} else {
		echo 0;
	}
} else {
	echo -1;
}

?>