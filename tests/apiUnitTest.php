<?php

namespace cardgate\api\tests;

class ApiUnitTest extends \PHPUnit\Framework\TestCase
{
    private $oClient;

    public function testCreateClientInstance()
    {
        $this->oClient = new \cardgate\api\Client(1, 'fake_key', true);
        $this->oClient->setLanguage('nl');
        $this->assertEquals('nl', $this->oClient->getLanguage(), 'language not set');
        $this->oClient->version()->setPlatformName('PHP');
        $this->assertEquals('PHP', $this->oClient->version()->getPlatformName(), 'platform name not set');
        $this->oClient->version()->setPlatformVersion(phpversion());
        $this->assertEquals(phpversion(), $this->oClient->version()->getPlatformVersion(), 'platform version not set');
        $this->oClient->version()->setPluginName('Test Implementation');
        $this->assertEquals('Test Implementation', $this->oClient->version()->getPluginName(), 'plugin name not set');
        $this->oClient->version()->setPluginVersion('0.0.1');
        $this->assertEquals('0.0.1', $this->oClient->version()->getPluginVersion(), 'plugin version not set');
        $this->oClient = null;
    }
}
