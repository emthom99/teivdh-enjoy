<?php
	require_once("config.php");
	require_once("hdvietParser.php");

	try {
		$con_db=new PDO($connect_string,DB_USER,DB_PASSWORD);
	} catch (Exception $e) {
		die('khong mo dc database'. $e->getMessage());
	}
	for ($i=0; $i < 3; $i++) { 
		$sql="SELECT xml_data FROM movie WHERE xml_id=:xmlId AND ip=:ip AND client_id=:clientId";
		$stm=$con_db->prepare($sql);
		$stm->execute(array(
			':xmlId'=>$_GET["xml_id"],
			':ip'=>$_SERVER['REMOTE_ADDR'],
			':clientId'=>$_GET['client_id']
		));
		$result=$stm->fetchAll();

		if(isset($result[0][0])){
			$sql="DELETE FROM movie WHERE xml_id=:xmlId AND ip=:ip AND client_id=:clientId";
			$stm=$con_db->prepare($sql);
			$stm->execute(array(
				':xmlId'=>$_GET["xml_id"],
				':ip'=>$_SERVER['REMOTE_ADDR'],
				':clientId'=>$_GET['client_id']
			));
			echo modifyXML($result[0][0],true);
			exit;
		}
		sleep(8);
	}
	