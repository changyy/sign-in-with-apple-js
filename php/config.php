<?php
// https://developer.apple.com/documentation/signinwithapplerestapi
$settings = json_decode(file_get_contents('settings.json'), true);
foreach(array( 'CLIENT_ID', 'SCOPES', 'REDIRECT_URI', 'STATE', 'Key_P8_PATH' ) as $field)
	if (!isset($settings[$field]))
		$settings[$field] = '';
/*
 https://developer.apple.com/documentation/signinwithapplerestapi/generate_and_validate_tokens

Creating the Client Secret
The client_secret is a JSON object that contains a header and payload. The header contains:

alg
The algorithm used to sign the token.

kid
A 10-character key identifier obtained from your developer account.

In the claims payload of the token, include:

iss
The issuer registered claim key, which has the value of your 10-character Team ID, obtained from your developer account.

iat
The issued at registered claim key, the value of which indicates the time at which the token was generated, in terms of the number of seconds since Epoch, in UTC.

exp
The expiration time registered claim key, the value of which must not be greater than 15777000 (6 months in seconds) from the Current Unix Time on the server.

aud
The audience registered claim key, the value of which identifies the recipient the JWT is intended for. Since this token is meant for Apple, use https://appleid.apple.com.

sub
The subject registered claim key, the value of which identifies the principal that is the subject of the JWT. Use the same value as client_id as this token is meant for your application.

After creating the token, sign it using the Elliptic Curve Digital Signature Algorithm (ECDSA) with the P-256 curve and the SHA-256 hash algorithm. Specify the value ES256 in the algorithm header key. Specify the key identifier in the kid attribute.
 */

date_default_timezone_set('UTC');

$current_time = time();
$expired_Time = time()+3600;

$key_path = NULL;
if (!strncmp('file://', $settings['Key_P8_PATH'], 7)) 
	$key_path = $settings['Key_P8_PATH'];
else if (!strncmp('/', $settings['Key_P8_PATH'], 1))
	$key_path = 'file://' . $settings['Key_P8_PATH'];
else
	$key_path = 'file://' . __DIR__ . '/'.$settings['Key_P8_PATH'];

$key_content = file_get_contents($key_path);

$jwt_header = array(
	'typ' => 'JWT',
	'alg' => 'ES256',
	'kid' => $settings['KID'],
);

$jwt_payload = array(
	"iss" => $settings['TEAM_ID'],
	"iat" => $current_time,
	"exp" => $expired_Time,
	"aud" => "https://appleid.apple.com",
	"sub" => $settings['CLIENT_ID'],
);

require 'vendor/autoload.php';
