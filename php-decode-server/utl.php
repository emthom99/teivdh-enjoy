<?php
	
    function  getRemotePage($req_url, $req_method, $req_params, $req_user_agent, $req_cookies, $req_referer, &$res_status, &$res_cookies, &$res_content) {
        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
        /* curl_setopt($ch, CURLOPT_CRLF, true); */
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_URL, $req_url);
        if ($req_method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req_params);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $req_user_agent);
        curl_setopt($ch, CURLOPT_COOKIE, $req_cookies);
        if ($req_referer <> '')
            curl_setopt($ch, CURLOPT_REFERER, $req_referer);

        // grab URL and pass it to the browser
        list( $header, $res_content ) = preg_split( '/([\r\n][\r\n])\\1/', curl_exec($ch), 2 );
        $res_status = curl_getinfo($ch);
        preg_match_all('/^Set-Cookie: (.*?);/m', $header, $cookies);
        $res_cookies = count($cookies) > 0 ? implode('; ', $cookies[1]) : '';
        $res_cookies = implode('; ', $cookies[1]);
        // close cURL resource, and free up system resources
        curl_close($ch);
        
        if($res_status['http_code']==301){
            preg_match('#Location: (.*)#', $header, $matches);
            if(isset($matches[1])){
                self::getRemotePage($matches[1], $req_method, $req_params, $req_user_agent, $req_cookies, $req_referer, $res_status, $res_cookies, $res_content);
            }
        }
    }	
?>