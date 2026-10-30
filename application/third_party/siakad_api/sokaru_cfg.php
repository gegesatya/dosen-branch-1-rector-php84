<?php
$Consumer_Key 		= 'WjCl2E90wPPxbCUuh0geFeIxbJua2OTD';
$Consumer_Secret 	= 'RA2copnFg3cjhkhdkBqaLXBmDszX7xVW';
//===========================================================
function SokaruGenerateToken($Consumer_Key, $Consumer_Secret) {
	$url_token = 'https://oauth2.sokaru.com/server/token.php';
    $data = array(
		'client_id' 		=> $Consumer_Key,
		'client_secret' 	=> $Consumer_Secret,
		'grant_type' 		=> 'client_credentials'
	);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url_token);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $json = json_decode($result, true);
	if (array_key_exists('access_token', $json)) {
		$accesstoken = $json['access_token'];
	} else { $accesstoken = 'tidak_ada_token'; }
    return $accesstoken;
}
function SokaruGenerateSignature($Consumer_Key, $token, $timestamp, $payload) {
	if(is_null($payload)) { $payloads = "clientid=$Consumer_Key&token=Bearer $token&timestamp=$timestamp&body="; }
		else { $payloads = "clientid=$Consumer_Key&token=Bearer $token&timestamp=$timestamp&body=$payload"; }    
    $signPayload = hash_hmac('sha256', $payloads, $token, true);
    return base64_encode($signPayload);
}
function kirim($Consumer_Key, $Consumer_Secret, $mode, $datas) {
	$path = '/server/respon.php';
	$urlPost = 'https://oauth2.sokaru.com'.$path;
	$token = SokaruGenerateToken($Consumer_Key, $Consumer_Secret);
	$timestamp = gmdate("Y-m-d\TH:i:s.000\Z");
	$datas['mode'] = $mode;
	$payload = json_encode($datas, true);
	$base64sign = SokaruGenerateSignature($Consumer_Key, $token, $timestamp, $payload);
	$request_headers = array(
        "Content-Type:"."application/json",
        "Authorization:Bearer " . $token,
        "SOKARU-Timestamp:" . $timestamp,
        "SOKARU-Signature:" . $base64sign
    );	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $urlPost);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);	
	$result = curl_exec($curl);
    $httpCodePost = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
	return json_decode($result, true);
}
