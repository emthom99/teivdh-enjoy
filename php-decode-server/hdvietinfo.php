<?php
	require_once "hdvietParser.php";
    
	if(isset($_GET['xmlcode'])){
		$xmlcode=str_replace('.xml', '', basename($_GET['xmlcode']));
		if(isset($_GET['view'])){
			infoXMLView($xmlcode);
		}
		else{
			echo downloadXML($xmlcode,isset($_GET['encode'])&&$_GET['encode']=='true');
		}
	} 
	else if(isset($_GET['m3u8'])){
		echo downloadM3U8($_GET['m3u8']);
	}
	else
		inputXMLView();

	function inputXMLView(){
?>
<html>
	<head>
		<title></title>
	</head>
	<body>
		<form action="" method="GET">
			<label for="xmlcode">film id: </label><input type="text" name="xmlcode" value=""></input>
			<input type="hidden" name="view" value="view"></input>
			<input type="submit"></input>
		</form>
	</body>
</html>

<?php
	}

	function infoXMLView($xmlcode){
?>
<html>
	<head>
    	<meta charset="UTF-8">
    	<title>info hd viet</title>
	</head>
	<body>
<?php
		$xml=downloadXML($xmlcode,false);
		preg_match('/name="(.*?)".*playlist source="(.*?)"/', $xml, $matchsInfo);
		//var_dump($matchs);die();
		//name
		echo '<p>'.$matchsInfo[1].'</p>';
		//description
		preg_match('/description="(.*?)"/', $xml, $matchsDes);
		echo '<p>'.$matchsDes[1].'</p>';
		//playlist
		$baseM3u8=downloadM3U8($matchsInfo[2]);
		preg_match_all('#(.*\.m3u8)#', $baseM3u8,$m3u8Matchs,PREG_SET_ORDER);
		echo '<p>';
		foreach ($m3u8Matchs as $m3u8) {
			$urlM3u8=dirname($matchsInfo[2]).'/'.$m3u8[1];
			echo "<a href=\"".basename(__FILE__)."?m3u8=$urlM3u8\">".basename($urlM3u8)."</a><br>";
		}
		echo '</p>';
		//subtitle
		echo '<p>';
		$aSub=downloadSubtilte($xml);
		foreach ($aSub as $subKey => $subUrl) {
			echo "<a href=\"$subUrl\">$subKey</a><br>";
		}
		echo '</p>';
		echo '</body></html>';
	}