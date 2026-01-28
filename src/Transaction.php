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
        protected $oClient;

        /**
         * The transaction id.
         * @var string
         * @access private
         */
        private $sId;

        /**
         * The site id to use for payments.
         * @var int
         * @access protected
         */
        protected $iSiteId;

        /**
         * The site key to use for payments.
         * @var string
         * @access protected
         */
        protected $sSiteKey;

        /**
         * The transaction amount in cents.
         * @var int
         * @access protected
         */
        protected $iAmount;

        /**
         * The transaction currency (ISO 4217).
         * @var string
         * @access protected
         */
        protected $sCurrency;

        /**
         * The description for the transaction.
         * @var string
         * @access protected
         */
        protected $sDescription;

        /**
         * A reference for the transaction.
         * @var string
         * @access protected
         */
        protected $sReference;

        /**
         * The payment method for the transaction.
         * @var Method
         * @access protected
         */
        protected $oPaymentMethod = null;

        /**
         * The payment method issuer for the transaction.
         * @var string
         * @access protected
         */
        protected $sIssuer = null;

        /**
         * The recurring flag
         * @var bool
         * @access private
         */
        private $bRecurring = false;

        /**
         * The consumer for the transaction.
         * @var Consumer
         * @access protected
         */
        protected $oConsumer = null;

        /**
         * The cart for the transaction.
         * @var Cart
         * @access protected
         */
        protected $oCart = null;

        /**
         * The URL to send payment callback updates to.
         * @var string
         * @access protected
         */
        protected $sCallbackUrl = null;

        /**
         * The URL to redirect to on success.
         * @var string
         * @access protected
         */
        protected $sSuccessUrl = null;

        /**
         * The URL to redirect to on failure.
         * @var string
         * @access protected
         */
        protected $sFailureUrl = null;

        /**
         * The URL to redirect to on pending.
         * @var string
         * @access protected
         */
        protected $sPendingUrl = null;

        /**
         * The URL to redirect to after the initial transaction register.
         * @var string
         * @access protected
         */
        protected $sActionUrl = null;

        /**
         * The constructor.
         * @param Client $oClient_ The client associated with this transaction.
         * @param int $iSiteId_ Site id to create transaction for.
         * @param int $iAmount_ The amount of the transaction in cents.
         * @param string $sCurrency_ Currency (ISO 4217)
         * @throws Exception
         * @access public
         * @api
         */
        public function __construct(Client $oClient_, int $iSiteId_, int $iAmount_, string $sCurrency_ = 'EUR')
        {
            $this->oClient = $oClient_;
            $this->setSiteId($iSiteId_)->setAmount($iAmount_)->setCurrency($sCurrency_);
        }

        /**
         * Set the transaction id.
         * @param string $sId_ Transaction id to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setId(string $sId_): Transaction
        {
            if (
                empty($sId_)
            ) {
                throw new Exception('Transaction.Id.Invalid', 'invalid id: ' . $sId_);
            }
            $this->sId = $sId_;
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
            if (empty($this->sId)) {
                throw new Exception('Transaction.Not.Initialized', 'invalid transaction state');
            }
            return $this->sId;
        }

        /**
         * Configure the transaction object with a site id.
         * @param int $iSiteId_ Site id to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setSiteId(int $iSiteId_): Transaction
        {
            if (! is_integer($iSiteId_)) {
                throw new Exception( 'Transaction.SiteId.Invalid', 'invalid site: ' . $iSiteId_ );
            }
            $this->iSiteId = $iSiteId_;
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
            return $this->iSiteId;
        }

        /**
         * Set the Site key to authenticate the hash in the request.
         * @param string $sSiteKey_ The site key to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setSiteKey(string $sSiteKey_): Transaction
        {
            if (! is_string($sSiteKey_)) {
                throw new Exception('Client.SiteKey.Invalid', 'invalid site key: ' . $sSiteKey_);
            }
            $this->sSiteKey = $sSiteKey_;
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
            return $this->sSiteKey;
        }

        /**
         * Configure the transaction object with an amount.
         * @param int $iAmount_ Amount in cents to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setAmount(int $iAmount_): Transaction
        {
            if (! is_integer($iAmount_)) {
                throw new Exception('Transaction.Amount.Invalid', 'invalid amount: ' . $iAmount_);
            }
            $this->iAmount = $iAmount_;
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
            return $this->iAmount;
        }

        /**
         * Configure the transaction currency.
         * @param string $sCurrency_ The currency to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setCurrency(string $sCurrency_): Transaction
        {
            if (! is_string($sCurrency_)) {
                throw new Exception('Transaction.Currency.Invalid', 'invalid currency: ' . $sCurrency_);
            }
            $this->sCurrency = $sCurrency_;
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
            return $this->sCurrency;
        }

        /**
         * Configure the description for the transaction.
         * @param string $sDescription_ The description to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setDescription(string $sDescription_): Transaction
        {
            if (! is_string($sDescription_)) {
                throw new Exception('Transaction.Description.Invalid', 'invalid description: ' . $sDescription_);
            }
            $this->sDescription = $sDescription_;
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
            return $this->sDescription;
        }

        /**
         * Configure the reference for the transaction.
         * @param string $sReference_ The reference to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setReference(string $sReference_): Transaction
        {
            if (! is_string($sReference_)) {
                throw new Exception('Transaction.Reference.Invalid', 'invalid reference: ' . $sReference_);
            }
            $this->sReference = $sReference_;
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
            return $this->sReference;
        }

        /**
         * Set the payment method to use for the transaction.
         *
         * @param Mixed $mPaymentMethod_ The payment method to use for the transaction. Can be one of the
         * consists defined in {@link Method} or a {@link Method} instance.
         *
         * @return $this
         * @throws Exception|ReflectionException
         * @access public
         * @api
         */
        public function setPaymentMethod($mPaymentMethod_): Transaction
        {
            if ($mPaymentMethod_ instanceof Method) {
                $this->oPaymentMethod = $mPaymentMethod_;
            } elseif (is_string($mPaymentMethod_)) {
                $this->oPaymentMethod = new Method($this->oClient, $mPaymentMethod_, $mPaymentMethod_);
            } else {
                throw new Exception('Transaction.PaymentMethod.Invalid', 'invalid payment method: ' . $mPaymentMethod_);
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
            return $this->oPaymentMethod;
        }

        /**
         * Set the optional payment method issuer to use for the transaction.
         * @param string $sIssuer_ The payment method issuer to use for the transaction.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setIssuer(string $sIssuer_): Transaction
        {
            if (
                empty($this->oPaymentMethod)
                || ! is_string($sIssuer_)
            ) {
                throw new Exception('Transaction.Issuer.Invalid', 'invalid issuer: ' . $sIssuer_);
            }
            $this->sIssuer = $sIssuer_;
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
            return $this->sIssuer;
        }

        /**
         * Set the recurring flag on the transaction.
         * @param bool $bRecurring_ Tests if this transaction can be used for recurring.
         * @return $this
         * @access public
         * @api
         */
        public function setRecurring( $bRecurring_): Transaction
        {
            $this->bRecurring = (bool) $bRecurring_;
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
            return $this->bRecurring;
        }

        /**
         * Set the consumer for the transaction.
         * @param Consumer $oConsumer_ The consumer for the transaction.
         * @return $this
         * @access public
         * @api
         */
        public function setConsumer(Consumer $oConsumer_): Transaction
        {
            $this->oConsumer = $oConsumer_;
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
            if (empty($this->oConsumer)) {
                $this->oConsumer = new Consumer();
            }
            return $this->oConsumer;
        }

        /**
         * Get the consumer for the transaction.
         * @return Consumer The consumer for the transaction.
         * @access public
         * @api
         * @deprecated Will be removed in v2.0.0.
         */
        public function getCustomer(): Consumer
        {
            return $this->getConsumer();
        }

        /**
         * Set the cart for the transaction.
         * @param Cart $oCart_ The cart for the transaction.
         * @return $this
         * @access public
         * @api
         */
        public function setCart(Cart $oCart_): Transaction
        {
            $this->oCart = $oCart_;
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
            if (empty($this->oCart)) {
                $this->oCart = new Cart();
            }
            return $this->oCart;
        }

        /**
         * Set the callback URL.
         * @param string $sUrl_ The URL to send callbacks to.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setCallbackUrl(string $sUrl_): Transaction
        {
            if (false === filter_var($sUrl_, FILTER_VALIDATE_URL)) {
                throw new Exception('Transaction.CallbackUrl.Invalid', 'invalid url: ' . $sUrl_);
            }
            $this->sCallbackUrl = $sUrl_;
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
            return $this->sCallbackUrl;
        }

        /**
         * Set the success URL.
         * @param string $sUrl_ The URL to send a successful transaction redirects.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setSuccessUrl(string $sUrl_): Transaction
        {
            if (false === filter_var($sUrl_, FILTER_VALIDATE_URL)) {
                throw new Exception('Transaction.SuccessUrl.Invalid', 'invalid url: ' . $sUrl_);
            }
            $this->sSuccessUrl = $sUrl_;
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
            return $this->sSuccessUrl;
        }

        /**
         * Set the failure URL.
         * @param string $sUrl_ The URL to send the failed transaction redirects.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setFailureUrl(string $sUrl_): Transaction
        {
            if (false === filter_var($sUrl_, FILTER_VALIDATE_URL)) {
                throw new Exception('Transaction.FailureUrl.Invalid', 'invalid url: ' . $sUrl_);
            }
            $this->sFailureUrl = $sUrl_;
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
            return $this->sFailureUrl;
        }

        /**
         * Set the failure URL.
         * @param string $sUrl_ The URL to send the failed transaction redirects.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setPendingUrl(string $sUrl_): Transaction
        {
            if ( filter_var($sUrl_, FILTER_VALIDATE_URL) === false ) {
                throw new Exception('Transaction.PendingUrl.Invalid', 'invalid url: ' . $sUrl_);
            }
            $this->sPendingUrl = $sUrl_;
            return $this;
        }

        /**
         * Use this method to set the url for success, failure, or pending all at once.
         * @param string $sUrl_ The URL to use for success, failure, and pending.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setRedirectUrl(string $sUrl_): Transaction
        {
            $this->setSuccessUrl($sUrl_)->setFailureUrl($sUrl_)->setPendingUrl($sUrl_);
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
            return $this->sPendingUrl;
        }

        /**
         * Get the redirect URL after transaction register.
         * @return string The URL to redirect to after register.
         * @access public
         * @api
         */
        public function getActionUrl(): string
        {
            return $this->sActionUrl;
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
            $aData = [
                'site_id'       => $this->iSiteId,
                'amount'        => $this->iAmount,
                'currency_id'   => $this->sCurrency,
                'url_callback'  => $this->sCallbackUrl,
                'url_success'   => $this->sSuccessUrl,
                'url_failure'   => $this->sFailureUrl,
                'url_pending'   => $this->sPendingUrl,
                'description'   => $this->sDescription,
                'reference'     => $this->sReference,
                'recurring'     => $this->bRecurring ? '1' : '0'
            ];
            if (! is_null($this->oConsumer)) {
                $aData['email'] = $this->oConsumer->getEmail();
                $aData['phone'] = $this->oConsumer->getPhone();
                $aData['consumer'] = array_merge(
                    $this->oConsumer->address()->getData(),
                    $this->oConsumer->shippingAddress()->getData('shipto_')
                );
                $aData['countryid'] = $this->oConsumer->address()->getCountry();
            }
            if (! is_null($this->oCart)) {
                $aData['cartitems'] = $this->oCart->getData();
            }

            $sResource = 'payment/';
            if (! empty($this->oPaymentMethod)) {
                $sResource .= $this->oPaymentMethod->getId() . '/';
                $aData['issuer'] = $this->sIssuer;
            }

            $aData = array_filter($aData); // remove NULL values
            $aResult = $this->oClient->doRequest($sResource, $aData);

            if (
                empty($aResult['payment'])
                || empty($aResult['payment']['transaction'])
            ) {
                throw new Exception('Transaction.Request.Invalid', 'unexpected result: ' . $this->oClient->getLastResult() . $this->oClient->getDebugInfo(true, false));
            }
            $this->sId = $aResult['payment']['transaction'];
            if (
                isset($aResult['payment']['action'])
                && 'redirect' == $aResult['payment']['action']
            ) {
                $this->sActionUrl = $aResult['payment']['url'];
            }

            return $this;
        }

        /**
         * This method can be used to determine if this transaction can be refunded.
         * @param bool $iRemainder_ Will be set to the amount that can be refunded.
         * refunds are supported.
         * @return bool
         * @throws Exception
         * @access public
         */
        public function canRefund(&$iRemainder_ = null): bool
        {
            $sResource = "transaction/{$this->sId}/";

            $aResult = $this->oClient->doRequest($sResource, null, 'GET');

            if (empty($aResult['transaction'])) {
                throw new Exception('Transaction.CanRefund.Invalid', 'unexpected result: ' . $this->oClient->getLastResult() . $this->oClient->getDebugInfo(true, false));
            }

            $iRemainder_ = (int) @$aResult['transaction']['refund_remainder'];

            return !empty($aResult['transaction']['can_refund']);
        }

        /**
         * This method can be used to (partially) refund a transaction.
         * @param int $iAmount_
         * @return Transaction The new (refund) transaction.
         * @throws Exception
         * @access public
         * @api
         */
        public function refund($iAmount_ = null, string $sDescription_ = null)
        {
            if (
                ! is_null($iAmount_)
                && ! is_integer($iAmount_)
            ) {
                throw new Exception('Transaction.Amount.Invalid', 'invalid amount: ' . $iAmount_);
            }

            $aData = [
                'amount'        => is_null($iAmount_) ? $this->iAmount : $iAmount_,
                'currency_id'   => $this->sCurrency,
                'description'   => $sDescription_
            ];

            $sResource = "refund/{$this->sId}/";

            $aData = array_filter($aData); // remove NULL values
            $aResult = $this->oClient->doRequest($sResource, $aData);

            if (
                empty($aResult['refund'])
                || empty($aResult['refund']['transaction'])
            ) {
                throw new Exception('Transaction.Refund.Invalid', 'unexpected result: ' . $this->oClient->getLastResult() . $this->oClient->getDebugInfo(true, false));
            }

            // This is a bit unlogical! Why not leave this to the callee?
            return $this->oClient->transactions()->get($aResult['refund']['transaction']);
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
        public function recur($iAmount_, $sReference_ = null, $sDescription_ = null)
        {
            if (! is_integer($iAmount_)) {
                throw new Exception('Transaction.Amount.Invalid', 'invalid amount: ' . $iAmount_);
            }

            $aData = [
                'amount'        => $iAmount_,
                'currency_id'   => $this->sCurrency,
                'reference'     => $sReference_,
                'description'   => $sDescription_
            ];

            $sResource = "recurring/{$this->sId}/";

            $aData = array_filter($aData); // remove NULL values
            $aResult = $this->oClient->doRequest($sResource, $aData);

            if (
                empty($aResult['recurring'])
                || empty($aResult['recurring']['transaction_id'])
            ) {
                throw new Exception('Transaction.Recur.Invalid', 'unexpected result: ' . $this->oClient->getLastResult() . $this->oClient->getDebugInfo(true, false));
            }

            // Same unlogical stuff as the method above! Why not leave this to the callee?
            return $this->oClient->transactions()->get($aResult['recurring']['transaction_id']);
        }
    }

}
