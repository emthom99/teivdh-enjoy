<?php
	require_once("config.php");
	require_once("hdvietParser.php");

	try {
		$con_db=new PDO($connect_string,DB_USER,DB_PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	} catch (Exception $e) {
		die('khong mo dc database'. $e->getMessage());
	}

	$xml=$_POST['xml_data'];
	$clientId=$_POST['client_id'];
	$xmlId=$_POST["xml_id"];

	$xml=Rc4::decode(KEY_XML, $xml);
	$xml=addVipInfo($xml);
	$filmInfo=getFilmInfo($xml);
	addFilmInfo2DB($filmInfo,$xmlId,$_SERVER['REMOTE_ADDR'],$clientId);
	$xml=Rc4::encode(KEY_XML,$xml);

	$sql="INSERT INTO movie(xml_id, ip, client_id, xml_data) VALUES (:xml_id, :ip, :client_id, :data)";
	$stm=$con_db->prepare($sql);
	$stm->execute(array(
		':xml_id'=>$xmlId,
		':ip'=>$_SERVER['REMOTE_ADDR'],
		':client_id'=>$clientId,
		':data'=>$xml
	));

	makeTrackingPage($filmInfo);


	function makeTrackingPage($filmInfo){
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
			<title>Tracking Movie Content</title>
			<script type="text/javascript">
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			  ga('create', 'UA-41124233-2', 'auto');
			  ga('send', 'pageview');
			  ga('send','event','Video','Play','<?=$filmInfo["name"]?>');
			</script>
		</head>
		<body>
			
		</body>
		</html>
		<?php
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
