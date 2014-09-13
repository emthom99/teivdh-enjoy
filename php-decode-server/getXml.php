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
			$xml=$result[0][0];

			$xml=Rc4::decode(KEY_XML, $xml);
	    	$xml=addVipInfo($xml);
	    	$filmInfo=getFilmInfo($xml);
	    	addFilmInfo2DB($filmInfo,$_GET["xml_id"],$_SERVER['REMOTE_ADDR'],$_GET['client_id']);
			
			echo Rc4::encode(KEY_XML,$xml);
			exit;
		}
		sleep(8);
	}
	
	function addFilmInfo2DB($filmInfo, $xmlId, $ip, $clientID){
		if (isset($filmInfo["name"])){
			global $con_db;
			$sql="INSERT INTO log(xml_id, name, description, image_url, ip, client_id) VALUES (:xml_id, :name, :description, :image_url, :ip, :client_id)";
			$stm=$con_db->prepare($sql);
			$stm->execute(array(
				':xml_id'=>$xmlId,
				':name'=>$filmInfo["name"],
				':description'=>$filmInfo["description"],
				':image_url'=>$filmInfo["image"],
				':ip'=>$_SERVER['REMOTE_ADDR'],
				':client_id'=>$clientID
			));
		}
	}