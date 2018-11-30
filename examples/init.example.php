<?php
require_once dirname( __FILE__ ) . '/../src/Autoloader.php';

cardgate\api\Autoloader::register();

try {
	$oCardGate = new cardgate\api\Client(1, '<api key>', TRUE );
	$oCardGate->setIp( $_SERVER['REMOTE_ADDR'] );
	$oCardGate->setLanguage( 'nl' );
	$oCardGate->version()->setPlatformName( 'PHP' );
	$oCardGate->version()->setPlatformVersion( phpversion() );
	$oCardGate->version()->setPluginName( 'Custom implementation' );
	$oCardGate->version()->setPluginVersion( '0.0.1' );
	// Add extra debug info during development
	$oCardGate->setDebugLevel( $oCardGate::DEBUG_RESULTS );
} catch ( \cardgate\api\Exception $oException_ ) {
	echo 'something went wrong: ' . $oException_->getCode() . ': ' . $oException_->getMessage();
}
//$iSiteId = <optional site id>;
//$sSiteKey = '<optional site key>';
