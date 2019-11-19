<?php
require 'config.php';

$code = isset($_POST['code']) ? $_POST['code'] : NULL;
if (!empty($code)) {
	//
	// more info
	//
	$apple_public_keys = json_decode(file_get_contents('https://appleid.apple.com/auth/keys'), true);
	$public_key_info = $apple_public_keys['keys'][0];
        $parsedPublicKey= \Firebase\JWT\JWK::parseKey($public_key_info);
        $publicKeyDetails = openssl_pkey_get_details($parsedPublicKey);
	$apple_public_key_pem = $publicKeyDetails['key'];
	$apple_public_key_alg = $public_key_info['alg'];

	echo "<pre>\n";
	echo "oepnssl = ".OPENSSL_VERSION_TEXT."\n";
	echo "jwt_header = ".print_r($jwt_header, true)."\n";
	echo "jwt_payload = ".print_r($jwt_payload, true)."\n";
	echo "apple_public_keys = ".print_r($apple_public_keys, true)."\n";
	echo "apple_public_key_pem = \n\n$apple_public_key_pem\n\n";
	echo "apple_public_key_alg = $apple_public_key_alg\n\n";
	echo "</pre>\n";

	//$payload_receive = JWT::decode($id_token, $apple_public_key_pem, [$$apple_public_key_alg]);

	$jwt_list = array();
	//
	// Use Lcobucci\JWT:
	//
	if (true) {
		$signer = new Lcobucci\JWT\Signer\Ecdsa\Sha256();
		$key = new Lcobucci\JWT\Signer\Key($key_path);
		$builder = new Lcobucci\JWT\Builder();
		$builder->sign($signer, $key);
		foreach($jwt_header as $key => $value)
			$builder->withHeader($key, $value);

		foreach($jwt_payload as $key => $value)
			$builder->withClaim($key, $value);

		$jwt_token = $builder->getToken();
		//print_r($jwt_token);
		$jwt = (string)$jwt_token;
		$jwt_list['Lcobucci\JWT'] = $jwt;
	}

	//
	// Use Firebase\JWT;
	//
	if (true) {
		$key_data = openssl_pkey_get_private($key_content, '');
		$details = openssl_pkey_get_details($key_data);
		//$key_data = $key_content;
		$jwt = \Firebase\JWT\JWT::encode($jwt_payload, $key_data, 'ES256', null, $jwt_header);
		$jwt_list['Firebase\JWT'] = $jwt;

		$jwt_handle = explode('.', $jwt);

		$MultibyteStringConverter = new \Lcobucci\JWT\Signer\Ecdsa\MultibyteStringConverter();
		$jwt_handle[2] = \Firebase\JWT\JWT::urlsafeB64Encode(
				$MultibyteStringConverter->fromAsn1(
					\Firebase\JWT\JWT::urlsafeB64Decode($jwt_handle[2]), 
					64
				)
		);
		$jwt_list['Firebase\JWT and \Lcobucci\JWT\Signer\Ecdsa\MultibyteStringConverter'] = implode('.', $jwt_handle);

		//
		// using PEM content
		//
		$jwt = \Firebase\JWT\JWT::encode($jwt_payload, $key_content, 'ES256', null, $jwt_header);
		$jwt_handle = explode('.', $jwt);

		$MultibyteStringConverter = new \Lcobucci\JWT\Signer\Ecdsa\MultibyteStringConverter();
		$jwt_handle[2] = \Firebase\JWT\JWT::urlsafeB64Encode(
				$MultibyteStringConverter->fromAsn1(
					\Firebase\JWT\JWT::urlsafeB64Decode($jwt_handle[2]), 
					64
				)
		);
		$jwt_list['Firebase\JWT without openssl_pkey_get_private and \Lcobucci\JWT\Signer\Ecdsa\MultibyteStringConverter'] = implode('.', $jwt_handle);
	}
	
	foreach($jwt_list as $from => $jwt ) {
	
		$auth_token_post_data = [
			'client_id' => $settings['CLIENT_ID'],
			'client_secret' => $jwt,
			'code' => $code,
			'grant_type' => 'authorization_code',
			'redirect_uri' => $settings['REDIRECT_URI'],
		];
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://appleid.apple.com/auth/token');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_token_post_data));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		$response = curl_exec($ch);
		curl_close ($ch);
	
		echo "<hr>\n";
		echo "<strong>[$from]</strong>\n";
		echo "<pre>\n";
		echo "request data = ".print_r($auth_token_post_data, true)."\n\n";
		echo "response = ".print_r(json_decode($response, true), true)."\n\n";
		echo "</pre>\n";
	}
}
