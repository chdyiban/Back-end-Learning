<?php
/**
 * 易班授权应用移植
 * @author:Yang yjy@chd.edu.cn
 */

function yb_get_token(){
	$APPID = "800ccc66f475a8c1";   //在open.yiban.cn管理中心的AppID
	$APPSECRET = "55cb33a2a62250af08393cb393bba7d5"; //在open.yiban.cn管理中心的AppSecret
	$CALLBACK = "http://f.yiban.cn/iapp190389";  //在open.yiban.cn管理中心的oauth2.0回调地址
	
	if(isset($_GET["code"])){   //用户授权后跳转回来会带上code参数，此处code非access_token，需调用接口转化。
	    $getTokenApiUrl = "https://oauth.yiban.cn/token/info?code=".$_GET['code']."&client_id={$APPID}	&client_secret={$APPSECRET}&redirect_uri={$CALLBACK}";
	    $res = sendRequest($getTokenApiUrl);
	    if(!$res){
	        throw new Exception('Get Token Error');
	    }
	    $userTokenInfo = json_decode($res);
	    $access_token = $userTokenInfo["access_token"];
	}else{
	    $postStr = pack("H*", $_GET["verify_request"]);
	    if(strlen($APPID) == '16') {
	        $postInfo = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $APPSECRET, $postStr, MCRYPT_MODE_CBC, $APPID);
	    }else {
	        $postInfo = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $APPSECRET, $postStr, MCRYPT_MODE_CBC, $APPID);
	    }
	    $postInfo = rtrim($postInfo);
	    $postArr = json_decode($postInfo, true);
	    if(!$postArr['visit_oauth']){  //说明该用户未授权需跳转至授权页面
	        header("Location: https://openapi.yiban.cn/oauth/authorize?client_id={$APPID}&redirect_uri={$CALLBACK}	&display=web");
	        die;
	    }
	    $access_token = $postArr['visit_oauth']['access_token'];
	}
	return $access_token;
}

function yb_api_user_me($access_token){

	return sendRequest("https://openapi.yiban.cn/user/me?access_token={$access_token}");
}

function yb_get_user_head(){
	//for test
    $token = yb_get_token();
    $me = json_decode(yb_api_user_me($token),true);
    if($me['status'] == 'success'){
    	return $me['info'];
    }
}

function sendRequest($uri){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Yi OAuth2 v0.1');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array());
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    $response = curl_exec($ch);
    return $response;
}

