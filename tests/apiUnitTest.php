<?php
class apiUnitTest extends \PHPUnit\Framework\TestCase {

	private $_oClient;

	public function testCreateClientInstance() {
		$this->_oClient = new cardgate\api\Client( 1, 'fake_key', TRUE );
		$this->_oClient->setLanguage( 'nl' );
		$this->assertEquals( 'nl', $this->_oClient->getLanguage(), 'language not set' );
		$this->_oClient->version()->setPlatformName( 'PHP' );
		$this->assertEquals( 'PHP', $this->_oClient->version()->getPlatformName(), 'platform name not set' );
		$this->_oClient->version()->setPlatformVersion( phpversion() );
		$this->assertEquals( phpversion(), $this->_oClient->version()->getPlatformVersion(), 'platform version not set' );
		$this->_oClient->version()->setPluginName( 'Test Implementation' );
		$this->assertEquals( 'Test Implementation', $this->_oClient->version()->getPluginName(), 'plugin name not set' );
		$this->_oClient->version()->setPluginVersion( '0.0.1' );
		$this->assertEquals( '0.0.1', $this->_oClient->version()->getPluginVersion(), 'plugin version not set' );
		$this->_oClient = NULL;
	}

}
