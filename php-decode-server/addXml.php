<?php
	require_once("config.php");

	try {
		$con_db=new PDO($connect_string,DB_USER,DB_PASSWORD);
	} catch (Exception $e) {
		die('khong mo dc database'. $e->getMessage());
	}

	$sql="INSERT INTO movie(xml_id, ip, client_id, xml_data) VALUES (:xml_id, :ip, :client_id, :data)";
	$stm=$con_db->prepare($sql);
	$stm->execute(array(
		':xml_id'=>$_POST["xml_id"],
		':ip'=>$_SERVER['REMOTE_ADDR'],
		':client_id'=>$_POST['client_id'],
		':data'=>$_POST['xml_data']
	));
