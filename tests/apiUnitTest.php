<?php
class apiUnitTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		parent::setUp();
	}

	public function testCreateClientInstance() {
		$oCardGate = new cardgate\api\Client( 1, 'fake_key', TRUE );
		$oCardGate->setLanguage( 'nl' );
		$oCardGate->version()->setPlatformName( 'PHP' );
		$oCardGate->version()->setPlatformVersion( phpversion() );
		$oCardGate->version()->setPluginName( 'Test Implementation' );
		$oCardGate->version()->setPluginVersion( '0.0.1' );
	}

}
