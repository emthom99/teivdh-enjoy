<?php
	include 'Rc4.php';
    include 'utl.php';
    
	define('USER_AGENT','Mozilla/5.0 (iPad; U; CPU iPhone OS 3_2 like Mac OS X; en-US) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7D11');
	define('KEY_M3U8','hdviet123#@!'); //m3u8
    define('KEY_XML','4357530a069e040078da8cbdc9ae6ec9'); //xml


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

	function downloadXML($xmlCode,$bNeedEncode=true){
		$url='http://movies.hdviet.com/'.$xmlCode.'.xml';
		getRemotePage($url,'GET',array(),'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36','__gads=ID=02c7a9c323b9231a:T=1396710393:S=ALNI_MZZidxh6cZXnOKSZp-7TYw1cAKsFQ; ADB3rdCookie1378368040=2; ADB3rdCookie1377682362=2; loc={%22province%22:29%2C%22zone%22:1%2C%22country%22:%22vn%22%2C%22ip%22:%22118.68.73.214%22}; movie_autoplay=%2C6427; vnhd_sessionhash=bv92soiketlbblqfj6c9q0coi0; ebNewBandWidth_.movies.hdviet.com=2579%3A1404474520821; __RC=5; __R=3; __uif=__uid%3A1396709051984186838; _ga=GA1.2.734416084.1396710407; __utma=34337085.734416084.1396710407.1403075088.1404474510.6; __utmb=34337085.10.9.1404474562073; __utmc=34337085; __utmz=34337085.1401330776.3.3.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided); fosp_gender=3','http://movies.hdviet.com/duccui', $res_status, $res_cookies, $res_content);
		//var_dump($res_content);exit;
		$res_content=Rc4::decode(KEY_XML, $res_content);
		$res_content=preg_replace('#<user.*?/>#', '<user hd="true" adver="false" vippass="true" p4="Pduccui" i="I1" />', $res_content);
	    $res_content=preg_replace('#source="http://movies.hdviet.com/(.*?)"#', 'source="http://'.$_SERVER['SERVER_NAME'].'/\1"', $res_content);
	   	if($bNeedEncode)
	   		$res_content=Rc4::encode(KEY_XML,$res_content);

		return $res_content;
	}

	function downloadM3U8($url, $bUpdateStaticLink=true){
		getRemotePage($url,'GET',array(),'','',USER_AGENT, $res_status, $res_cookies, $res_content);
		$res_content=Rc4::decode(KEY_M3U8, $res_content);
		if($bUpdateStaticLink){
			$baseLink=dirname($url).'/';
			$res_content=preg_replace('#(.*\.ts)#', $baseLink.'$1', $res_content);
		}

		return $res_content;
	}

	function downloadSubtilte($xml){
		$subtitles=array();
		preg_match_all('#subtitle lable="(.*?)" source="(.*?)" ( s3="(.*?)" s4="(.*?)")? />#', $xml, $matchs,PREG_SET_ORDER);
		foreach ($matchs as $subtitle) {
			$subType=$subtitle[1];
			$filepath=$subtitle[2];

			if(isset($subtitle[3])){
				//download file
				getRemotePage($subtitle[2],'GET',array(),'','',USER_AGENT, $res_status, $res_cookies, $res_content);
				//create key
				$keys=array();
				$delta=4;$s3=$subtitle[4];$s4=intval($subtitle[5]);
				if($delta<strlen($s3)) {
					$keylen=$delta+$s4;
					if($keylen>=strlen($s3))
						$keylen=$keylen-strlen($s3);
					$keys[]=substr($s3,$keylen);
				}
				$keys[]=substr($s3,$s4);
				//decode file
				foreach ($keys as $key) {
					$res_content=Rc4::decode($key,$res_content);
					if(strpos($res_content,'-->')!==false) break;
				}
				$filepath='subtiles/'.time().'.srt';
				file_put_contents(dirname(__FILE__).'/'.$filepath, $res_content);
			}

			$subtitles[$subType]=$filepath;
		}
		return $subtitles;
	}
?>