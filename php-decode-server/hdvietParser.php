<?php
	require_once 'Rc4.php';
    require_once 'utl.php';
    require_once 'config.php';

    function addVipInfo($xml){
    	$xml=preg_replace('#<user.*?/>#', '<user hd="true" adver="false" vippass="true" p4="Pduccui" i="I1|I2|I3" />', $xml);
	    $xml=preg_replace('#source="http://movies.hdviet.com/(.*?)"#', 'source="http://'.$_SERVER['SERVER_NAME'].'/\1"', $xml);
	    return $xml;
    }
    function getFilmInfo($xml){
    	$matched=preg_match('$<info\s+name="(?P<name>.*?)"\s+image="(?P<image>.*?)"\s+description="(?P<description>.*?)"$', $xml,$result);
    	return $result;
    }
    function modifyXML($xml,$bNeedEncode){
    	$xml=Rc4::decode(KEY_XML, $xml);
    	$xml=addVipInfo($xml);
	   	if($bNeedEncode)
	   		$xml=Rc4::encode(KEY_XML,$xml);

		return $xml;
    }

	function downloadXML($xmlCode,$bNeedEncode=true){
		$url='http://movies.hdviet.com/'.$xmlCode.'.xml';
		getRemotePage($url,'GET',array(),USER_AGENT,
			'',
			'http://movies.hdviet.com/phim-quyet-dau-a-fighting-man.html', 
			$res_status, $res_cookies, $res_content);

		return modifyXML($res_content,$bNeedEncode);
	}

	function downloadM3U8($url, $bUpdateStaticLink=true){
		getRemotePage($url,'GET',array(),USER_AGENT,
			'__gads=ID=c0a71f050ab74e45:T=1393988681:S=ALNI_MaTl6sWFY_8PdOyfEy8_U4GI3wEFA; __auc=ed91794b144d01b427804a87d7a; __utma=121279313.1313720351.1392873137.1397368529.1400254520.5; __utmz=121279313.1400254520.5.5.utmcsr=movies.hdviet.com|utmccn=(referral)|utmcmd=referral|utmcct=/tim-kiem.html; _a3rd1400119902=0-9; ADB3rdCookie1377682362=1; _a3rd1401346900=0-7; ADB3rdCookie1378368040=2; loc={%22province%22:29%2C%22zone%22:1%2C%22country%22:%22vn%22%2C%22ip%22:%22115.76.76.243%22}; movie_autoplay=%2C7363; vnhd_sessionhash=b9q6q0q45o35p211ts3j0i6j91; _ga=GA1.2.1313720351.1392873137; __utma=34337085.1313720351.1392873137.1401797732.1405012440.63; __utmb=34337085.18.7.1405012485948; __utmc=34337085; __utmz=34337085.1399295653.59.7.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided); ADB3rdCookie1369025971=1; fosp_gender=3',
			'http://movies.hdviet.com/phim-quyet-dau-a-fighting-man.html', 
			$res_status, $res_cookies, $res_content);
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