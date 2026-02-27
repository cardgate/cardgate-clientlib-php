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

    use ReflectionException;

    /**
     * Transaction instance.
     */
    class Transaction
    {
        /**
         * The client associated with this transaction.
         * @var Client
         * @access protected
         */
        protected $client;

        /**
         * The transaction id.
         * @var string
         * @access private
         */
        private $id;

        /**
         * The site id to use for payments.
         * @var int
         * @access protected
         */
        protected $siteId;

        /**
         * The site key to use for payments.
         * @var string
         * @access protected
         */
        protected $siteKey;

        /**
         * The transaction amount in cents.
         * @var int
         * @access protected
         */
        protected $amount;

        /**
         * The transaction currency (ISO 4217).
         * @var string
         * @access protected
         */
        protected $currency;

        /**
         * The description for the transaction.
         * @var string
         * @access protected
         */
        protected $description;

        /**
         * A reference for the transaction.
         * @var string
         * @access protected
         */
        protected $reference;

        /**
         * The payment method for the transaction.
         * @var Method
         * @access protected
         */
        protected $paymentMethod = null;

        /**
         * The payment method issuer for the transaction.
         * @var string
         * @access protected
         */
        protected $issuer = null;

        /**
         * The recurring flag
         * @var bool
         * @access private
         */
        private $recurring = false;

        /**
         * The consumer for the transaction.
         * @var Consumer
         * @access protected
         */
        protected $consumer = null;

        /**
         * The cart for the transaction.
         * @var Cart
         * @access protected
         */
        protected $cart = null;

        /**
         * The URL to send payment callback updates to.
         * @var string
         * @access protected
         */
        protected $callbackUrl = null;

        /**
         * The URL to redirect to on success.
         * @var string
         * @access protected
         */
        protected $successUrl = null;

        /**
         * The URL to redirect to on failure.
         * @var string
         * @access protected
         */
        protected $failureUrl = null;

        /**
         * The URL to redirect to on pending.
         * @var string
         * @access protected
         */
        protected $pendingUrl = null;

        /**
         * The URL to redirect to after the initial transaction register.
         * @var string
         * @access protected
         */
        protected $actionUrl = null;

        /**
         * The constructor.
         *
         * @param Client $client The client associated with this transaction.
         * @param int $siteId Site id to create transaction for.
         * @param int $amount The amount of the transaction in cents.
         * @param string $currency Currency (ISO 4217)
         *
         * @throws Exception
         * @access public
         * @api
         */
        public function __construct(Client $client, int $siteId, int $amount, string $currency = 'EUR')
        {
            $this->client = $client;
            $this->setSiteId($siteId)->setAmount($amount)->setCurrency($currency);
        }

        /**
         * Set the transaction id.
         *
         * @param string $id Transaction id to set.
         *
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setId(string $id): Transaction
        {
            if (
                empty($id)
            ) {
                throw new Exception('Transaction.Id.Invalid', 'invalid id: ' . $id);
            }
            $this->id = $id;
            return $this;
        }

        /**
         * Get the transaction id associated with this transaction.
         * @return string The transaction id associated with this transaction.
         * @throws Exception
         * @access public
         * @api
         */
        public function getId(): string
        {
            if (empty($this->id)) {
                throw new Exception('Transaction.Not.Initialized', 'invalid transaction state');
            }
            return $this->id;
        }

        /**
         * Configure the transaction object with a site id.
         *
         * @param int $siteId Site id to set.
         *
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setSiteId(int $siteId): Transaction
        {
            if (! is_integer($siteId)) {
                throw new Exception( 'Transaction.SiteId.Invalid', 'invalid site: ' . $siteId );
            }
            $this->siteId = $siteId;
            return $this;
        }

        /**
         * Get the site id associated with this transaction.
         * @return int The site id associated with this transaction.
         * @access public
         * @api
         */
        public function getSiteId(): int
        {
            return $this->siteId;
        }

        /**
         * Set the Site key to authenticate the hash in the request.
         * @param string $siteKey The site key to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setSiteKey(string $siteKey): Transaction
        {
            if (! is_string($siteKey)) {
                throw new Exception('Client.SiteKey.Invalid', 'invalid site key: ' . $siteKey);
            }
            $this->siteKey = $siteKey;
            return $this;
        }

        /**
         * Get the Merchant API key to authenticate the transaction request with.
         * @return string The merchant API key.
         * @access public
         * @api
         */
        public function getSiteKey(): string
        {
            return $this->siteKey;
        }

        /**
         * Configure the transaction object with an amount.
         *
         * @param int $amount Amount in cents to set.
         *
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setAmount(int $amount): Transaction
        {
            if (! is_integer($amount)) {
                throw new Exception('Transaction.Amount.Invalid', 'invalid amount: ' . $amount);
            }
            $this->amount = $amount;
            return $this;
        }

        /**
         * Get the amount of the transaction.
         * @return int The amount of the transaction.
         * @access public
         * @api
         */
        public function getAmount(): int
        {
            return $this->amount;
        }

        /**
         * Configure the transaction currency.
         * @param string $currency The currency to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setCurrency(string $currency): Transaction
        {
            if (! is_string($currency)) {
                throw new Exception('Transaction.Currency.Invalid', 'invalid currency: ' . $currency);
            }
            $this->currency = $currency;
            return $this;
        }

        /**
         * Get the currency of the transaction.
         * @return string The currency of the transaction.
         * @access public
         * @api
         */
        public function getCurrency(): string
        {
            return $this->currency;
        }

        /**
         * Configure the description for the transaction.
         * @param string $description The description to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setDescription(string $description): Transaction
        {
            if (! is_string($description)) {
                throw new Exception('Transaction.Description.Invalid', 'invalid description: ' . $description);
            }
            $this->description = $description;
            return $this;
        }

        /**
         * Get the description for the transaction.
         * @return string The description of the transaction.
         * @access public
         * @api
         */
        public function getDescription(): string
        {
            return $this->description;
        }

        /**
         * Configure the reference for the transaction.
         * @param string $reference The reference to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setReference(string $reference): Transaction
        {
            if (! is_string($reference)) {
                throw new Exception('Transaction.Reference.Invalid', 'invalid reference: ' . $reference);
            }
            $this->reference = $reference;
            return $this;
        }

        /**
         * Get the reference for the transaction.
         * @return string The reference of the transaction.
         * @access public
         * @api
         */
        public function getReference(): string
        {
            return $this->reference;
        }

        /**
         * Set the payment method to use for the transaction.
         *
         * @param Mixed $paymentMethod The payment method to use for the transaction. Can be one of the
         * consists defined in {@link Method} or a {@link Method} instance.
         *
         * @return $this
         * @throws Exception|ReflectionException
         * @access public
         * @api
         */
        public function setPaymentMethod($paymentMethod): Transaction
        {
            if ( $paymentMethod instanceof Method) {
                $this->paymentMethod = $paymentMethod;
            } elseif (is_string($paymentMethod)) {
                $this->paymentMethod = new Method($this->client, $paymentMethod, $paymentMethod);
            } else {
                throw new Exception('Transaction.PaymentMethod.Invalid', 'invalid payment method: ' . $paymentMethod);
            }
            return $this;
        }

        /**
         * Get the payment method that will be used for the transaction.
         * @return Method The payment method that will be used for the transaction.
         * @access public
         * @api
         */
        public function getPaymentMethod(): Method
        {
            return $this->paymentMethod;
        }

        /**
         * Set the optional payment method issuer to use for the transaction.
         * @param string $issuer The payment method issuer to use for the transaction.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setIssuer(string $issuer): Transaction
        {
            if (
                empty($this->paymentMethod)
                || ! is_string($issuer)
            ) {
                throw new Exception('Transaction.Issuer.Invalid', 'invalid issuer: ' . $issuer);
            }
            $this->issuer = $issuer;
            return $this;
        }

        /**
         * Get the optional payment method issuer that will be used for the transaction.
         * @return string The payment method issuer that will be used for the transaction.
         * @access public
         * @api
         */
        public function getIssuer(): string
        {
            return $this->issuer;
        }

        /**
         * Set the recurring flag on the transaction.
         * @param bool $recurring Tests if this transaction can be used for recurring.
         * @return $this
         * @access public
         * @api
         */
        public function setRecurring($recurring): Transaction
        {
            $this->recurring = (bool) $recurring;
            return $this;
        }

        /**
         * Get the recurring flag of the transaction.
         * @return bool tests if this transaction can be used for recurring.
         * @access public
         * @api
         */
        public function getRecurring(): bool
        {
            return $this->recurring;
        }

        /**
         * Set the consumer for the transaction.
         * @param Consumer $consumer The consumer for the transaction.
         * @return $this
         * @access public
         * @api
         */
        public function setConsumer(Consumer $consumer): Transaction
        {
            $this->consumer = $consumer;
            return $this;
        }

        /**
         * Get the consumer for the transaction.
         * @return Consumer The consumer for the transaction.
         * @access public
         * @api
         */
        public function getConsumer(): Consumer
        {
            if (empty($this->consumer)) {
                $this->consumer = new Consumer();
            }
            return $this->consumer;
        }

        /**
         * Set the cart for the transaction.
         * @param Cart $cart The cart for the transaction.
         * @return $this
         * @access public
         * @api
         */
        public function setCart(Cart $cart): Transaction
        {
            $this->cart = $cart;
            return $this;
        }

        /**
         * Get the cart for the transaction.
         * @return Cart The cart for the transaction.
         * @access public
         * @api
         */
        public function getCart(): Cart
        {
            if (empty($this->cart)) {
                $this->cart = new Cart();
            }
            return $this->cart;
        }

        /**
         * Set the callback URL.
         * @param string $url The URL to send callbacks to.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setCallbackUrl(string $url): Transaction
        {
            if (false === filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception('Transaction.CallbackUrl.Invalid', 'invalid url: ' . $url);
            }
            $this->callbackUrl = $url;
            return $this;
        }

        /**
         * Get the callback URL.
         * @return string The URL callbacks are being sent to.
         * @access public
         * @api
         */
        public function getCallbackUrl(): string
        {
            return $this->callbackUrl;
        }

        /**
         * Set the success URL.
         * @param string $url The URL to send a successful transaction redirects.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setSuccessUrl(string $url): Transaction
        {
            if (false === filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception('Transaction.SuccessUrl.Invalid', 'invalid url: ' . $url);
            }
            $this->successUrl = $url;
            return $this;
        }

        /**
         * Get the success URL.
         * @return string The URL successful transactions are being redirected to.
         * @access public
         * @api
         */
        public function getSuccessUrl(): string
        {
            return $this->successUrl;
        }

        /**
         * Set the failure URL.
         * @param string $url The URL to send the failed transaction redirects.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setFailureUrl(string $url): Transaction
        {
            if (false === filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception('Transaction.FailureUrl.Invalid', 'invalid url: ' . $url);
            }
            $this->failureUrl = $url;
            return $this;
        }

        /**
         * Get the failure URL.
         * @return string The URL failed transactions are being redirected to.
         * @access public
         * @api
         */
        public function getFailureUrl(): string
        {
            return $this->failureUrl;
        }

        /**
         * Set the failure URL.
         * @param string $url The URL to send the failed transaction redirects.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setPendingUrl(string $url): Transaction
        {
            if ( filter_var($url, FILTER_VALIDATE_URL) === false ) {
                throw new Exception('Transaction.PendingUrl.Invalid', 'invalid url: ' . $url);
            }
            $this->pendingUrl = $url;
            return $this;
        }

        /**
         * Use this method to set the url for success, failure, or pending all at once.
         * @param string $url The URL to use for success, failure, and pending.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setRedirectUrl(string $url): Transaction
        {
            $this->setSuccessUrl($url)->setFailureUrl($url)->setPendingUrl($url);
            return $this;
        }

        /**
         * Get the pending URL.
         * @return string The URL pending transactions are being redirected to.
         * @access public
         * @api
         */
        public function getPendingUrl(): string
        {
            return $this->pendingUrl;
        }

        /**
         * Get the redirect URL after transaction register.
         * @return string The URL to redirect to after register.
         * @access public
         * @api
         */
        public function getActionUrl(): string
        {
            return $this->actionUrl;
        }

        /**
         * Registers the transaction with the cardgate payment gateway.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function register(): Transaction
        {
            $data = [
                'site_id'       => $this->siteId,
                'amount'        => $this->amount,
                'currency_id'   => $this->currency,
                'url_callback'  => $this->callbackUrl,
                'url_success'   => $this->successUrl,
                'url_failure'   => $this->failureUrl,
                'url_pending'   => $this->pendingUrl,
                'description'   => $this->description,
                'reference'     => $this->reference,
                'recurring'     => $this->recurring ? '1' : '0'
            ];
            if (! is_null($this->consumer)) {
                $data['email'] = $this->consumer->getEmail();
                $data['phone'] = $this->consumer->getPhone();
                $data['consumer'] = array_merge(
                    $this->consumer->address()->getData(),
                    $this->consumer->shippingAddress()->getData('shipto_')
                );
                $data['countryid'] = $this->consumer->address()->getCountry();
            }
            if (! is_null($this->cart)) {
                $data['cartitems'] = $this->cart->getData();
            }

            $resource = 'payment/';
            if (! empty($this->paymentMethod)) {
                $resource .= $this->paymentMethod->getId() . '/';
                $data['issuer'] = $this->issuer;
            }

            $data = array_filter($data); // remove NULL values
            $result = $this->client->doRequest($resource, $data);

            if (
                empty($result['payment'])
                || empty($result['payment']['transaction'])
            ) {
                throw new Exception('Transaction.Request.Invalid', 'unexpected result: ' . $this->client->getLastResult() . $this->client->getDebugInfo(true, false));
            }
            $this->id = $result['payment']['transaction'];
            if (
                isset($result['payment']['action'])
                && 'redirect' == $result['payment']['action']
            ) {
                $this->actionUrl = $result['payment']['url'];
            }

            return $this;
        }

        /**
         * This method can be used to determine if this transaction can be refunded.
         * @param bool $remainder Will be set to the amount that can be refunded.
         * refunds are supported.
         * @return bool
         * @throws Exception
         * @access public
         */
        public function canRefund(&$remainder = null): bool
        {
            $sResource = "transaction/{$this->id}/";

            $aResult = $this->client->doRequest($sResource, null, 'GET');

            if (empty($aResult['transaction'])) {
                throw new Exception('Transaction.CanRefund.Invalid', 'unexpected result: ' . $this->client->getLastResult() . $this->client->getDebugInfo(true, false));
            }

            $remainder = (int) @$aResult['transaction']['refund_remainder'];

            return !empty($aResult['transaction']['can_refund']);
        }

        /**
         * This method can be used to (partially) refund a transaction.
         * @param int $amount
         * @return Transaction The new (refund) transaction.
         * @throws Exception
         * @access public
         * @api
         */
        public function refund(?int $amount = null, ?string $description = null)
        {
            if (
                ! is_null($amount)
                && ! is_integer($amount)
            ) {
                throw new Exception('Transaction.Amount.Invalid', 'invalid amount: ' . $amount);
            }

            $data = [
                'amount'        => is_null($amount) ? $this->amount : $amount,
                'currency_id'   => $this->currency,
                'description'   => $description
            ];

            $resource = "refund/{$this->id}/";

            $data = array_filter($data); // remove NULL values
            $result = $this->client->doRequest($resource, $data);

            if (
                empty($result['refund'])
                || empty($result['refund']['transaction'])
            ) {
                throw new Exception('Transaction.Refund.Invalid', 'unexpected result: ' . $this->client->getLastResult() . $this->client->getDebugInfo(true, false));
            }

            // This is a bit unlogical! Why not leave this to the callee?
            return $this->client->transactions()->get($result['refund']['transaction']);
        }

        /**
         * This method can be used to recur a transaction.
         * @param int $iAmount_
         * @param string $sReference_ Optional reference for the recurring transaction.
         * @param string $sDescription_ Optional description for the recurring transaction.
         * @return Transaction The new (recurred) transaction.
         * @throws Exception
         * @access public
         * @api
         */
        public function recur($amount, $reference = null, $description = null)
        {
            if (! is_integer($amount)) {
                throw new Exception('Transaction.Amount.Invalid', 'invalid amount: ' . $amount);
            }

            $data = [
                'amount'        => $amount,
                'currency_id'   => $this->currency,
                'reference'     => $reference,
                'description'   => $description
            ];

            $resource = "recurring/{$this->id}/";

            $data = array_filter($data); // remove NULL values
            $result = $this->client->doRequest($resource, $data);

            if (
                empty($result['recurring'])
                || empty($result['recurring']['transaction_id'])
            ) {
                throw new Exception('Transaction.Recur.Invalid', 'unexpected result: ' . $this->client->getLastResult() . $this->client->getDebugInfo(true, false));
            }

            // Same unlogical stuff as the method above! Why not leave this to the callee?
            return $this->client->transactions()->get($result['recurring']['transaction_id']);
        }
    }

}
