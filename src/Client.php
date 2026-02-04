<?php

/**
 * Copyright (c) 2018 CardGate B.V.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @license     The MIT License (MIT) https://opensource.org/licenses/MIT
 * @author      CardGate B.V.
 * @copyright   CardGate B.V.
 * @link        https://www.cardgate.com
 */

namespace cardgate\api {

    /**
     * CardGate client object.
     */
    final class Client
    {
        /**
         * Client version.
         */
        public const CLIENT_VERSION = "1.1.24";

        /**
         * Url to use for production.
         */
        public const URL_PRODUCTION = 'https://secure.curopayments.net/rest/v1/curo/';

        /**
         * Url to use for testing.
         */
        public const URL_STAGING = 'https://secure-staging.curopayments.net/rest/v1/curo/';

        /**
         * Toggle testmode variable.
         * @var bool
         * @access private
         */
        private $testmode;

        /**
         * The merchant id to use for authentication.
         * @var int
         * @access private
         */
        private $merchantId;

        /**
         * The secret key to use for authentication.
         * @var string
         * @access private
         */
        private $key;

        /**
         * The consumer IP address associated with the client.
         * @var string
         * @access private
         */
        private $ip = null;

        /**
         * The language to use when communicating with the API.
         * @var string
         * @access private
         */
        private $language = null;

        /**
         * The version resource.
         * @var resource\Version
         * @access private
         */
        private $version = null;

        /**
         * The transactions resource.
         * @var resource\Transactions
         * @access private
         */
        private $transactions = null;

        /**
         * The subscriptions resource.
         * @var resource\Subscriptions
         * @access private
         */
        private $subscriptions = null;

        /**
         * The consumers resource.
         * @var resource\Consumers
         * @access private
         */
        private $consumers = null;

        /**
         * The methods resource.
         * @var resource\Methods
         * @access private
         */
        private $methods = null;

        /**
         * Debug level. 0 = None, 1 = Include result in errors, 2 = Verbose CURL calls.
         * @var int
         * @access private
         */
        public const DEBUG_NONE    = 0;
        public const DEBUG_RESULTS = 1;
        public const DEBUG_VERBOSE = 2;

        private $debugLevel = 0;

        /**
         * Last request and result for debugging.
         * @var string
         * @access private
         */
        private $lastRequest = null;
        private $lastResult = null;

        /**
         * The constructor.
         *
         * @param int $merchantId The merchant id for the client.
         * @param string $key The merchant API key for the client.
         * @param bool $testmode Toggle testmode for the client.
         *
         * @throws Exception
         * @access public
         * @api
         */
        public function __construct(int $merchantId, string $key, bool $testmode = false)
        {
            $this->setMerchantId($merchantId)->setKey($key)->setTestmode($testmode);
        }

        /**
         * Prevent leaking info in dumps.
         * @ignore
         */
        public function __debugInfo()
        {
            return [
                'Version'       => $this->version,
                'Testmode'    => $this->testmode,
                'DebugLevel'  => $this->debugLevel,
                'merchantId' => $this->merchantId,
                'API_URL'     => $this->getUrl(),
                'LastRequest' => $this->lastRequest,
                'LastResult'  => $this->lastResult
            ];
        }

        /**
         * Toggle testmode.
         *
         * @param bool $testmode Enable or disable testmode for this client.
         *
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setTestmode(bool $testmode): Client
        {
            if (! is_bool($testmode)) {
                throw new Exception('Client.Testmode.Invalid', 'invalid testmode: ' . $testmode);
            }
            $this->testmode = $testmode;
            return $this;
        }

        /**
         * Get currenct testmode setting.
         * @return bool The current testmode setting
         * @access public
         * @api
         */
        public function getTestmode(): bool
        {
            return $this->testmode;
        }

        /**
         * Set debug level.
         *
         * @param int $level Level: 0 = None, 1 = Include request/resule in errors, 2 = Verbose cURL calls.
         *
         * @return $this
         * @access public
         * @api
         */
        public function setDebugLevel(int $level): Client
        {
            $this->debugLevel = $level;
            return $this;
        }

        /**
         * Get current debug level setting.
         * @return int The current debug level.
         * @access public
         * @api
         */
        public function getDebugLevel(): int
        {
            return $this->debugLevel;
        }

        /**
         * Get debug information according to debug level.
         * @param bool $startWithNewLine Optional flag to indicate the info should start with a new-line.
         * @param bool $addResult Optional flag to indicate the result should be included too.
         * @return string Debug info or empty if level = 0.
         * @access public
         * @api
         */
        public function getDebugInfo(bool $startWithNewLine = true, bool $addResult = true): string
        {
            if ($this->getDebugLevel() > self::DEBUG_NONE) {
                $result = ( $startWithNewLine ? PHP_EOL : '' );
                $result .= 'Request: ' . $this->getLastRequest();
                if ($addResult) {
                    $result .= PHP_EOL . 'Result: ' . $this->getLastResult();
                }
                return $result;
            } else {
                return '';
            }
        }

        /**
         * Configure the client object with a merchant id.
         *
         * @param int $merchantId Merchant id to set.
         *
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setMerchantId( int $merchantId)
        {
            if ( ! is_integer($merchantId)) {
                throw new Exception('Client.MerchantId.Invalid', 'invalid merchant: ' . $merchantId);
            }

            $this->merchantId = $merchantId;
            return $this;
        }

        /**
         * Get the merchant id associated with this client.
         * @return int The merchant id associated with this client
         * @access public
         * @api
         */
        public function getMerchantId()
        {
            return $this->merchantId;
        }

        /**
         * Set the Merchant API key to authenticate the transaction request with.
         * @param string $key The merchant API key to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setKey($sKey_)
        {
            if (! is_string($sKey_)) {
                throw new Exception('Client.Key.Invalid', 'invalid merchant key: ' . $sKey_);
            }
            $this->key = $sKey_;
            return $this;
        }

        /**
         * Get the Merchant API key to authenticate the transaction request with.
         * @return string The merchant API key.
         * @access public
         * @api
         */
        public function getKey()
        {
            return $this->key;
        }

        /**
         * Set the IP address.
         * @param string The IP address of the consumer.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setIp($ip)
        {
            if (
                ! is_string($ip)
                || false === filter_var($ip, FILTER_VALIDATE_IP) // NOTE ipv6
            ) {
                throw new Exception('Client.Ip.Invalid', 'invalid IP address: ' . $ip);
            }
            $this->ip = $ip;
            return $this;
        }

        /**
         * Get the IP address.
         * @return string The consumer IP address.
         * @access public
         * @api
         */
        public function getIp()
        {
            return $this->ip;
        }

        /**
         * Configure the language to use.
         * @param string $language The language to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setLanguage($language)
        {
            if (! is_string($language)) {
                throw new Exception('Client.Language.Invalid', 'invalid language: ' . $language);
            }
            $this->language = $language;
            return $this;
        }

        /**
         * Get the language the client is configured with.
         * @return string The language the client is configured with.
         * @access public
         * @api
         */
        public function getLanguage()
        {
            return $this->language;
        }

        /**
         * Get the URL to use with this connection, depending on testmode settings.
         * @return string The URL to use
         * @access public
         * @api
         */
        public function getUrl()
        {
            if (! empty($_SERVER['CG_API_URL'])) {
                return $_SERVER['CG_API_URL'];
            } else {
                return ( $this->getTestmode() ? self::URL_STAGING : self::URL_PRODUCTION );
            }
        }

        /**
         * Get the last request sent to the API.
         * @return string The request string.
         * @access public
         * @api
         */
        public function getLastRequest()
        {
            return (string) $this->lastRequest;
        }

        /**
         * Get the last result from an API call.
         * @return string The result string.
         * @access public
         * @api
         */
        public function getLastResult()
        {
            return (string) $this->lastResult;
        }

        /**
         * Pull the config from the API using a token provided by the site setup button in the backoffice.
         * @return array Returns an array with settings.
         * @throws Exception
         * @access public
         * @api
         */
        public function pullConfig($token)
        {
            if (! is_string($token)) {
                throw new Exception('Client.Token.Invalid', 'invalid token for settings pull: ' . $token);
            }
            $sResource = "pullconfig/{$token}/";
            return $this->doRequest($sResource);
        }

        /**
         * Accessor for the versioning resource.
         * @return resource\Version
         * @access public
         * @api
         */
        public function version()
        {
            if (null == $this->version) {
                $this->version = new resource\Version();
            }
            return $this->version;
        }

        /**
         * Accessor for the transactions resource.
         * @return resource\Transactions
         * @access public
         * @api
         */
        public function transactions()
        {
            if (null == $this->transactions) {
                $this->transactions = new resource\Transactions($this);
            }
            return $this->transactions;
        }

        /**
         * Accessor for the subscriptions resource.
         * @return resource\Subscriptions
         * @access public
         * @api
         */
        public function subscriptions()
        {
            if (null == $this->subscriptions) {
                $this->subscriptions = new resource\Subscriptions($this);
            }
            return $this->subscriptions;
        }

        /**
         * Accessor for the consumers resource.
         * @return resource\Consumers
         * @access public
         * @api
         */
        public function consumers()
        {
            if (null == $this->consumers) {
                $this->consumers = new resource\Consumers($this);
            }
            return $this->consumers;
        }

        /**
         * Accessor for the payment methods resource.
         * @return resource\Methods
         * @access public
         * @api
         */
        public function methods()
        {
            if (null == $this->methods) {
                $this->methods = new resource\Methods($this);
            }
            return $this->methods;
        }

        /**
         * Send a request to the CardGate API.
         * @param string $resource The resource to call.
         * @param array $data Optional data to use for the call.
         * @param string $httpMethod The http method to use (GET or POST, which is the default).
         * @return array An array with request results.
         * @throws Exception
         */
        public function doRequest($resource, $data = null, $httpMethod = 'POST')
        {

            if (! in_array($httpMethod, [ 'GET', 'POST' ])) {
                throw new Exception('Client.HttpMethod.Invalid', 'invalid http method: ' . $httpMethod);
            }

            $url = $this->getUrl() . $resource;
            if (is_array($data)) {
                $data['ip'] = $this->getIp();
                $data['language_id'] = $this->getLanguage();
            } elseif (is_null($data)) {
                $data = [ 'ip' => $this->getIp(), 'language_id' => $this->getLanguage() ];
            } else {
                throw new Exception('Client.Data.Invalid', 'invalid data: ' . $data);
            }

            if (null !== $this->version) {
                $data = array_merge($data, $this->version->getData());
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->merchantId . ':' . $this->key);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            if ($this->testmode) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            } else {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // verify SSL peer
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // check for valid common name and verify host
            }

            if ('POST' == $httpMethod) {
                $this->lastRequest = json_encode($data, JSON_PARTIAL_OUTPUT_ON_ERROR);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->lastRequest);
                $this->lastRequest = "[POST $url] " . $this->lastRequest;
            } else {
                $this->lastRequest = $url
                                     . ( false === strchr($url, '?') ? '?' : '&' )
                                     . http_build_query($data)
                ;
                curl_setopt($ch, CURLOPT_URL, $this->lastRequest);
            }

            if (self::DEBUG_VERBOSE == $this->debugLevel) {
                curl_setopt($ch, CURLOPT_VERBOSE, true);
            }

            $this->lastResult = curl_exec($ch);
            if (false == $this->lastResult) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new Exception('Client.Request.Curl.Error', $error);
            } else {
                curl_close($ch);
            }
            if (null === ( $results = json_decode($this->lastResult, true) )) {
                throw new Exception('Client.Request.JSON.Invalid', 'remote gave invalid JSON: ' . $this->lastResult);
            }
            if (isset($results['error'])) {
                throw new Exception( 'Client.Request.Remote.' . $results['error']['code'], $results['error']['message'] . $this->getDebugInfo());
            }

            return $results;
        }
    }

}
