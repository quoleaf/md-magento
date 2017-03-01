<?php
require 'app/Mage.php';
Mage::app('admin')->setUseSessionInUrl(false);
error_reporting(E_ALL | E_STRICT);
Mage::getSingleton('core/session', array('name' => 'adminhtml'))->start();

require_once BP.'/lib/extendware/ewgadashboard/Google/autoload.php';
$scriptUri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

$client = new Google_Client();
$client->setAccessType('offline');
$client->setAuthConfig(Extendware_EWGADashboard_Helper_Api::getAuthConfig());
$client->setScopes(array('https://www.googleapis.com/auth/analytics'));

$service = new Google_Service_Analytics($client);

$token = null;
if (Extendware_EWGADashboard_Helper_Api::getPersistentAccessToken()) { // extract token from session and configure client
	$token = Extendware_EWGADashboard_Helper_Api::getPersistentAccessToken();
	$client->setAccessToken($token);
}

if (isset($_GET['code']) and $client->isAccessTokenExpired()) { // we received the positive auth callback, get the token and store it in session
	$client->authenticate($_GET['code']);
    Extendware_EWGADashboard_Helper_Api::setPersistentAccessToken($client->getAccessToken());
    if ($client->getRefreshToken()) {
    	Extendware_EWGADashboard_Helper_Api::setPersistentRefreshToken($client->getRefreshToken());
    }
}

$url = Mage::getSingleton('adminhtml/session')->getData('ewgadashboard_current_url');
if ($url) {
	header ('Location: ' . $url);
	exit;
}

echo 'Please go back to your administrative dashboard.';