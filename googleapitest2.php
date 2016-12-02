<?php
session_start();
require_once dirname(__FILE__).'/GoogleClientApi/Google_Client.php';
require_once dirname(__FILE__).'/GoogleClientApi/contrib/Google_Oauth2Service.php';

$scriptUri = "http://".$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'];

$client = new Google_Client();
$client->setAccessType('online'); // default: offline
$client->setApplicationName('glpi-150907');
$client->setClientId('680873363959-i3rhet8s1psarm4nh1l95rnin0k7mt53.apps.googleusercontent.com');
$client->setClientSecret('MNxJxxwmiX8z4FAYY0-MvUOD');
$client->setRedirectUri('http://localhost/asset_glpi/front/central.php');
$client->setDeveloperKey('AIzaSyAwrWGvFnQyfxVqn34DXuUluMc1GDUjFpc'); // API key

// $service implements the client interface, has  to be set before auth call
$service = new Google_Oauth2Service($client);

if (isset($_GET['logout'])) { // logout: destroy token
	unset($_SESSION['token']);
	die('Logged out.');
}

if (isset($_GET['code'])) { // we received the positive auth callback, get the token and store it in session
	$client->authenticate();
	$_SESSION['token'] = $client->getAccessToken();
}

if (isset($_SESSION['token'])) { // extract token from session and configure client
	$token = $_SESSION['token'];
	$client->setAccessToken($token);
}

if (!$client->getAccessToken()) { // auth call to google
	$authUrl = $client->createAuthUrl();
	header("Location: ".$authUrl);
	die;
}
echo 'Hello, world.';