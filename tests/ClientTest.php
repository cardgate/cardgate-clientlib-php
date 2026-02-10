<?php

namespace cardgate\api\tests;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for cardgate\api\Client covering all public methods without performing network I/O.
 */
class ClientTest extends TestCase
{
    private function newClient($testmode = true)
    {
        return new \cardgate\api\Client(123, 'secret_key', $testmode);
    }

    public function testConstructorAndBasicGetters()
    {
        $c = $this->newClient(true);
        $this->assertTrue($c->getTestmode());
        $this->assertSame(123, $c->getMerchantId());
        $this->assertSame('secret_key', $c->getKey());
        $this->assertSame('', $c->getLastRequest());
        $this->assertSame('', $c->getLastResult());
    }

    public function testSetAndGetTestmode()
    {
        $c = $this->newClient(false);
        $this->assertFalse($c->getTestmode());
        $c->setTestmode(true);
        $this->assertTrue($c->getTestmode());
    }

    public function testSetTestmodeInvalidThrows()
    {
        $this->expectException(\cardgate\api\Exception::class);
        $c = $this->newClient();
        /** @noinspection PhpParamsInspection */
        $c->setTestmode('yes');
    }

    public function testDebugLevelAndInfo()
    {
        $c = $this->newClient();
        $this->assertSame(\cardgate\api\Client::DEBUG_NONE, $c->getDebugLevel());
        $this->assertSame('', $c->getDebugInfo());

        $c->setDebugLevel(\cardgate\api\Client::DEBUG_RESULTS);
        $info = $c->getDebugInfo(false, false);
        // Last request/result are empty strings until a request is made; just check prefix when enabled
        $this->assertStringStartsWith('Request: ', $info);

        $info2 = $c->getDebugInfo(true, true);
        $this->assertStringContainsString("\nRequest: ", $info2);
        $this->assertStringContainsString("\nResult: ", $info2);
    }

    public function testSetAndGetMerchantId()
    {
        $c = $this->newClient();
        $c->setMerchantId(456);
        $this->assertSame(456, $c->getMerchantId());
    }

    public function testSetMerchantIdInvalidThrows()
    {
        $this->expectException(\cardgate\api\Exception::class);
        $c = $this->newClient();
        /** @noinspection PhpParamsInspection */
        $c->setMerchantId('not-int');
    }

    public function testSetAndGetKey()
    {
        $c = $this->newClient();
        $c->setKey('new_key');
        $this->assertSame('new_key', $c->getKey());
    }

    public function testSetKeyInvalidThrows()
    {
        $this->expectException(\cardgate\api\Exception::class);
        $c = $this->newClient();
        /** @noinspection PhpParamsInspection */
        $c->setKey(100);
    }

    public function testSetAndGetIp()
    {
        $c = $this->newClient();
        $c->setIp('127.0.0.1');
        $this->assertSame('127.0.0.1', $c->getIp());

        // IPv6 should also be accepted
        $c->setIp('2001:0db8:85a3:0000:0000:8a2e:0370:7334');
        $this->assertSame('2001:0db8:85a3:0000:0000:8a2e:0370:7334', $c->getIp());
    }

    public function testSetIpInvalidThrows()
    {
        $this->expectException(\cardgate\api\Exception::class);
        $c = $this->newClient();
        $c->setIp('999.999.999.999');
    }

    public function testSetAndGetLanguage()
    {
        $c = $this->newClient();
        $c->setLanguage('nl');
        $this->assertSame('nl', $c->getLanguage());
    }

    public function testSetLanguageInvalidThrows()
    {
        $this->expectException(\cardgate\api\Exception::class);
        $c = $this->newClient();
        /** @noinspection PhpParamsInspection */
        $c->setLanguage(123);
    }

    public function testGetUrlRespectsTestmodeAndOverride()
    {
        $c = $this->newClient(false);
        $this->assertSame(\cardgate\api\Client::URL_PRODUCTION, $c->getUrl());

        $c->setTestmode(true);
        $this->assertSame(\cardgate\api\Client::URL_STAGING, $c->getUrl());

        // Override via server var
        $_SERVER['CG_API_URL'] = 'https://example.test/api/';
        $this->assertSame('https://example.test/api/', $c->getUrl());
        unset($_SERVER['CG_API_URL']);
    }

    public function testPullConfigInvalidTokenTypeThrows()
    {
        $this->expectException(\cardgate\api\Exception::class);
        $c = $this->newClient();
        /** @noinspection PhpParamsInspection */
        $c->pullConfig(123); // non-string token should throw before any network call
    }

    public function testResourceAccessorsReturnExpectedTypesAndAreSingletons()
    {
        $c = $this->newClient();
        $this->assertInstanceOf(\cardgate\api\resource\Version::class, $c->version());
        $this->assertSame($c->version(), $c->version());

        $this->assertInstanceOf(\cardgate\api\resource\Transactions::class, $c->transactions());
        $this->assertSame($c->transactions(), $c->transactions());

        $this->assertInstanceOf(\cardgate\api\resource\Subscriptions::class, $c->subscriptions());
        $this->assertSame($c->subscriptions(), $c->subscriptions());

        $this->assertInstanceOf(\cardgate\api\resource\Consumers::class, $c->consumers());
        $this->assertSame($c->consumers(), $c->consumers());

        $this->assertInstanceOf(\cardgate\api\resource\Methods::class, $c->methods());
        $this->assertSame($c->methods(), $c->methods());
    }

    public function testDoRequestParameterValidationWithoutNetwork()
    {
        $c = $this->newClient();
        // Invalid HTTP method
        $this->expectException(\cardgate\api\Exception::class);
        $c->doRequest('status/', null, 'PUT');
    }

    public function testDoRequestDataValidationWithoutNetwork()
    {
        $c = $this->newClient();
        // Invalid data (non-array, non-null)
        $this->expectException(\cardgate\api\Exception::class);
        /** @noinspection PhpParamsInspection */
        $c->doRequest('status/', 'not-an-array', 'GET');
    }

    public function testDebugInfoDoesNotLeakSecretInDebugDump()
    {
        $c = $this->newClient();
        $arr = $c->__debugInfo();
        $this->assertIsArray($arr);
        foreach (['Version','Testmode','DebugLevel','merchantId','API_URL','LastRequest','LastResult'] as $key) {
            $this->assertArrayHasKey($key, $arr);
        }
        // Ensure the raw secret key is not directly present in debug info values
        $this->assertFalse(in_array('secret_key', $arr, true));
    }
}
