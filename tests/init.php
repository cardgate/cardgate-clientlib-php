<?php
require_once dirname( __FILE__ ) . '/../src/Autoloader.php';

cardgate\api\Autoloader::register();

$oCardGate = new cardgate\api\Client( 1066, 'TuZFZq6K2bPBi7VTkQNYM3JFlD21wz8tQ0sNCqODTZxJWKScRlVm366GaRoIlIdQ', TRUE );
$oCardGate->setSiteId( 1593 );
$oCardGate->setSiteKey( 'sfnvfanvf' );
$oCardGate->setIp( $_SERVER['REMOTE_ADDR'] );
$oCardGate->setLanguage( 'nl' );
$oCardGate->version()->setPlatformName( 'PHP' );
$oCardGate->version()->setPlatformVersion( phpversion() );
$oCardGate->version()->setPluginName( 'Custom Implementation' );
$oCardGate->version()->setPluginVersion( '0.0.1' );

//$iSiteId = 44;
//$sSiteKey = '<optional site key>';
