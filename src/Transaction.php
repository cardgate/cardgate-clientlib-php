<?php
/**
 * Copyright (c) 2016 CardGate B.V.
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
	 * Transaction instance.
	 */
	final class Transaction {

		/**
		 * The client associated with this transaction.
		 * @var Client
		 * @access private
		 */
		private $_oClient;

		/**
		 * The transaction id.
		 * @var String
		 * @access private
		 */
		private $_sId;

		/**
		 * The transaction site id.
		 * @var Integer
		 * @access private
		 */
		private $_iSiteId;

		/**
		 * The transaction amount in cents.
		 * @var Integer
		 * @access private
		 */
		private $_iAmount;

		/**
		 * The transaction currency (ISO 4217).
		 * @var String
		 * @access private
		 */
		private $_sCurrency;

		/**
		 * The description for the transaction.
		 * @var String
		 * @access private
		 */
		private $_sDescription;

		/**
		 * A reference for the transaction.
		 * @var String
		 * @access private
		 */
		private $_sReference;

		/**
		 * The payment method for the transaction.
		 * @var Method
		 * @access private
		 */
		private $_oPaymentMethod = NULL;

		/**
		 * The payment method issuer for the transaction.
		 * @var String
		 * @access private
		 */
		private $_sIssuer = NULL;

		/**
		 * The customer for the transaction.
		 * @var Customer
		 * @access private
		 */
		private $_oCustomer = NULL;

		/**
		 * The cart for the transaction.
		 * @var Cart
		 * @access private
		 */
		private $_oCart = NULL;

		/**
		 * The URL to send payment callback updates to.
		 * @var String
		 * @access private
		 */
		private $_sCallbackUrl = NULL;

		/**
		 * The URL to redirect to on success.
		 * @var String
		 * @access private
		 */
		private $_sSuccessUrl = NULL;

		/**
		 * The URL to redirect to on failre.
		 * @var String
		 * @access private
		 */
		private $_sFailureUrl = NULL;

		/**
		 * The URL to redirect to on pending.
		 * @var String
		 * @access private
		 */
		private $_sPendingUrl = NULL;

		/**
		 * The URL to redirect to after initial transaction register.
		 * @var String
		 * @access private
		 */
		private $_sActionUrl = NULL;

		/**
		 * The constructor.
		 * @param Client $oClient_ The client associated with this transaction.
		 * @param Integer $iSiteId_ Site id to create transaction for.
		 * @param Integer $iAmount_ The amount of the transaction in cents.
		 * @param String $sCurrency_ Currency (ISO 4217)
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		function __construct( Client $oClient_, $iSiteId_, $iAmount_, $sCurrency_ = 'EUR' ) {
			$this->_oClient = $oClient_;
			$this->setSiteId( $iSiteId_ )->setAmount( $iAmount_ )->setCurrency( $sCurrency_ );
		}

		/**
		 * Set the transaction id.
		 * @param String $sId_ Transaction id to set.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setId( $sId_ ) {
			if (
				! is_string( $sId_ )
				|| empty( $sId_ )
			) {
				throw new Exception( 'Transaction.Id.Invalid', 'invalid id: ' . $sId_ );
			}
			$this->_sId = $sId_;
			return $this;
		}

		/**
		 * Get the transaction id associated with this transaction.
		 * @return String The transaction id associated with this transaction.
		 * @access public
		 * @api
		 */
		public function getId() {
			if ( empty( $this->_sId ) ) {
				throw new Exception( 'Transaction.Not.Initialized', 'invalid transaction state' );
			}
			return $this->_sId;
		}

		/**
		 * Configure the transaction object with a site id.
		 * @param Integer $iSiteId_ Site id to set.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setSiteId( $iSiteId_ ) {
			if ( ! is_integer( $iSiteId_ ) ) {
				throw new Exception( 'Transaction.SiteId.Invalid', 'invalid site: ' . $iSiteId_ );
			}
			$this->_iSiteId = $iSiteId_;
			return $this;
		}

		/**
		 * Get the site id associated with this transaction.
		 * @return Integer The site id associated with this transaction.
		 * @access public
		 * @api
		 */
		public function getSiteId() {
			return $this->_iSiteId;
		}

		/**
		 * Configure the transaction object with an amount.
		 * @param Integer $iAmount_ Amount in cents to set.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setAmount( $iAmount_ ) {
			if ( ! is_integer( $iAmount_ ) ) {
				throw new Exception( 'Transaction.Amount.Invalid', 'invalid amount: ' . $iAmount_ );
			}
			$this->_iAmount = $iAmount_;
			return $this;
		}

		/**
		 * Get the amount of the transaction.
		 * @return Integer The amount of the transaction.
		 * @access public
		 * @api
		 */
		public function getAmount() {
			return $this->_iAmount;
		}

		/**
		 * Configure the transaction currency.
		 * @param String $sCurrency_ The currency to set.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setCurrency( $sCurrency_ ) {
			if ( ! is_string( $sCurrency_ ) ) {
				throw new Exception( 'Transaction.Currency.Invalid', 'invalid currency: ' . $sCurrency_ );
			}
			$this->_sCurrency = $sCurrency_;
			return $this;
		}

		/**
		 * Get the currency of the transaction.
		 * @return String The currency of the transaction.
		 * @access public
		 * @api
		 */
		public function getCurrency() {
			return $this->_sCurrency;
		}

		/**
		 * Configure the description for the transaction.
		 * @param String $sDescription_ The description to set.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setDescription( $sDescription_ ) {
			if ( ! is_string( $sDescription_ ) ) {
				throw new Exception( 'Transaction.Description.Invalid', 'invalid description: ' . $sDescription_ );
			}
			$this->_sDescription = $sDescription_;
			return $this;
		}

		/**
		 * Get the description for the transaction.
		 * @return String The description of the transaction.
		 * @access public
		 * @api
		 */
		public function getDescription() {
			return $this->_sDescription;
		}

		/**
		 * Configure the reference for the transaction.
		 * @param String $sReference_ The reference to set.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setReference( $sReference_ ) {
			if ( ! is_string( $sReference_ ) ) {
				throw new Exception( 'Transaction.Reference.Invalid', 'invalid reference: ' . $sReference_ );
			}
			$this->_sReference = $sReference_;
			return $this;
		}

		/**
		 * Get the reference for the transaction.
		 * @return String The reference of the transaction.
		 * @access public
		 * @api
		 */
		public function getReference() {
			return $this->_sReference;
		}

		/**
		 * Set the payment method to use for the transaction.
		 * @param Mixed $mPaymentMethod_ The payment method to use for the transaction. Can be one of the
		 * consts defined in {@link Method} or a {@link Method} instance.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setPaymentMethod( $mPaymentMethod_ ) {
			if ( $mPaymentMethod_ instanceof Method ) {
				$this->_oPaymentMethod = $mPaymentMethod_;
			} elseif ( is_string( $mPaymentMethod_ ) ) {
				$this->_oPaymentMethod = new Method( $this->_oClient, $mPaymentMethod_ );
			} else {
				throw new Exception( 'Transaction.PaymentMethod.Invalid', 'invalid payment method: ' . $mPaymentMethod_ );
			}
			return $this;
		}

		/**
		 * Get the payment method that will be used for the transaction.
		 * @return Method The payment method that will be used for the transaction.
		 * @access public
		 * @api
		 */
		public function getPaymentMethod() {
			return $this->_oPaymentMethod;
		}

		/**
		 * Set the optional payment method issuer to use for the transaction.
		 * @param String $sIssuer_ The payment method issuer to use for the transaction.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setIssuer( $sIssuer_ ) {
			if (
				empty( $this->_oPaymentMethod )
				|| ! is_string( $sIssuer_ )
			) {
				throw new Exception( 'Transaction.Issuer.Invalid', 'invalid issuer: ' . $sIssuer_ );
			}
			$this->_sIssuer = $sIssuer_;
			return $this;
		}

		/**
		 * Get the optional payment method issuer that will be used for the transaction.
		 * @return Issuer The payment method issuer that will be used for the transaction.
		 * @access public
		 * @api
		 */
		public function getIssuer() {
			return $this->_oIssuer;
		}

		/**
		 * Set the customer for the transaction.
		 * @param Customer $oCustomer_ The customer for the transaction.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setCustomer( Customer $oCustomer_ ) {
			$this->_oCustomer = $oCustomer_;
			return $this;
		}

		/**
		 * Get the customer for the transaction.
		 * @return Customer The customer for the transaction.
		 * @access public
		 * @api
		 */
		public function getCustomer() {
			if ( empty( $this->_oCustomer ) ) {
				$this->_oCustomer = new Customer();
			}
			return $this->_oCustomer;
		}

		/**
		 * Set the cart for the transaction.
		 * @param Cart $oCart_ The cart for the transaction.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setCart( Cart $oCart_ ) {
			$this->_oCart = $oCart_;
			return $this;
		}

		/**
		 * Get the cart for the transaction.
		 * @return Cart The cart for the transaction.
		 * @access public
		 * @api
		 */
		public function getCart() {
			if ( empty( $this->_oCart ) ) {
				$this->_oCart = new Cart();
			}
			return $this->_oCart;
		}

		/**
		 * Set the callback URL.
		 * @param String $sUrl_ The URL to send callbacks to.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setCallbackUrl( $sUrl_ ) {
			if ( FALSE === filter_var( $sUrl_, FILTER_VALIDATE_URL ) ) {
				throw new Exception( 'Transaction.CallbackUrl.Invalid', 'invalid url: ' . $sUrl_ );
			}
			$this->_sCallbackUrl = $sUrl_;
			return $this;
		}

		/**
		 * Get the callbacl URL.
		 * @return String The URL callbacks are being sent to.
		 * @access public
		 * @api
		 */
		public function getCallbackUrl() {
			return $this->_sCallbackUrl;
		}

		/**
		 * Set the success URL.
		 * @param String $sUrl_ The URL to send successful transaction redirects.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setSuccessUrl( $sUrl_ ) {
			if ( FALSE === filter_var( $sUrl_, FILTER_VALIDATE_URL ) ) {
				throw new Exception( 'Transaction.SuccessUrl.Invalid', 'invalid url: ' . $sUrl_ );
			}
			$this->_sSuccessUrl = $sUrl_;
			return $this;
		}

		/**
		 * Get the success URL.
		 * @return String The URL successful transactions are being redirected to.
		 * @access public
		 * @api
		 */
		public function getSuccessUrl() {
			return $this->_sSuccessUrl;
		}

		/**
		 * Set the failure URL.
		 * @param String $sUrl_ The URL to send failed transaction redirects.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setFailureUrl( $sUrl_ ) {
			if ( FALSE === filter_var( $sUrl_, FILTER_VALIDATE_URL ) ) {
				throw new Exception( 'Transaction.FailureUrl.Invalid', 'invalid url: ' . $sUrl_ );
			}
			$this->_sFailureUrl = $sUrl_;
			return $this;
		}

		/**
		 * Get the failure URL.
		 * @return String The URL failed transactions are being redirected to.
		 * @access public
		 * @api
		 */
		public function getFailureUrl() {
			return $this->_sFailureUrl;
		}

		/**
		 * Set the failure URL.
		 * @param String $sUrl_ The URL to send failed transaction redirects.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setPendingUrl( $sUrl_ ) {
			if ( FALSE === filter_var( $sUrl_, FILTER_VALIDATE_URL ) ) {
				throw new Exception( 'Transaction.PendingUrl.Invalid', 'invalid url: ' . $sUrl_ );
			}
			$this->_sPendingUrl = $sUrl_;
			return $this;
		}

		/**
		 * Use this method to set the url for success, failure and pending all at once.
		 * @param String $sUrl_ The URL to use for success, failure and pending.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setRedirectUrl( $sUrl_ ) {
			$this->setSuccessUrl( $sUrl_ )->setFailureUrl( $sUrl_ )->setPendingUrl( $sUrl_ );
			return $this;
		}

		/**
		 * Get the pending URL.
		 * @return String The URL pending transactions are being redirected to.
		 * @access public
		 * @api
		 */
		public function getPendingUrl() {
			return $this->_sPendingUrl;
		}

		/**
		 * Get the redirect URL after transaction register.
		 * @return String The URL to redirect to after register.
		 * @access public
		 * @api
		 */
		public function getActionUrl() {
			return $this->_sActionUrl;
		}

		/**
		 * Registers the transaction with the cardgate payment gateway.
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function register() {
			$aData = [
				'site'			=> $this->_iSiteId,
				'amount'		=> $this->_iAmount,
				'currency_id'	=> $this->_sCurrency,
				'url_callback'	=> $this->_sCallbackUrl,
				'url_success'	=> $this->_sSuccessUrl,
				'url_failure'	=> $this->_sFailureUrl,
				'url_pending'	=> $this->_sPendingUrl,
				'description'	=> $this->_sDescription,
				'reference'		=> $this->_sReference
			];
			if ( ! is_null( $this->_oCustomer ) ) {
				$aData['email'] = $this->_oCustomer->getEmail();
				$aData['phone'] = $this->_oCustomer->getPhone();
				$aData = array_merge( $aData, $this->_oCustomer->address()->getData() );
				$aData = array_merge( $aData, $this->_oCustomer->shippingAddress()->getData( 'shipto_' ) );
			}
			if ( ! is_null( $this->_oCart ) ) {
				$aData['cartitems'] = $this->_oCart->getData();
			}

			$sResource = 'payment/';
			if ( ! empty( $this->_oPaymentMethod ) ) {
				$sResource .= $this->_oPaymentMethod->getId() . '/';
				$aData['issuer'] = $this->_sIssuer;
			}

			$aData = array_filter( $aData ); // remove NULL values
			$aResult = $this->_oClient->doRequest( $sResource, $aData, 'POST' );

			if (
				empty( $aResult['payment'] )
				|| empty( $aResult['payment']['transaction'] )
			) {
				throw new Exception( 'Transaction.Request.Invalid', 'invalid payment data returned' );
			}
			$this->_sId = $aResult['payment']['transaction'];
			if (
				isset( $aResult['payment']['action'] )
				&& 'redirect' == $aResult['payment']['action']
			) {
				$this->_sActionUrl = $aResult['payment']['url'];
			}

			return $this;
		}

		/**
		 * This method can be used to determine if this transaction can be refunded.
		 * @param Boolean $iRemainder_ Will be set to the amount that can be refunded.
		 * refunds are supported.
		 * @return Boolean
		 * @throws Exception
		 * @access public
		 */
		public function canRefund( &$iRemainder_ = NULL ) {
			$sResource = "transaction/{$this->_sId}/";

			$aResult = $this->_oClient->doRequest( $sResource, NULL, 'GET' );

			if ( empty( $aResult['transaction'] ) ) {
				throw new \cardgate\api\Exception( 'Transaction.Details.Invalid', 'invalid transaction data returned' );
			}

			$iRemainder_ = (integer)@$aResult['transaction']['refund_remainder'];

			return !!@$aResult['transaction']['can_refund'];
		}

		/**
		 * This method can be used to (partially) refund a transaction.
		 * @param Integer $iAmount_
		 * @return Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function refund( $iAmount_ = NULL ) {
			if (
				! is_null( $iAmount_ )
				&& ! is_integer( $iAmount_ )
			) {
				throw new Exception( 'Transaction.Amount.Invalid', 'invalid amount: ' . $iAmount_ );
			}

			$aData = [
				'site'			=> $this->_iSiteId,
				'amount'		=> is_null( $iAmount_ ) ? $this->_iAmount : $iAmount_,
				'currency_id'	=> $this->_sCurrency
			];

			$sResource = "refund/{$this->_sId}/";

			$aData = array_filter( $aData ); // remove NULL values
			$aResult = $this->_oClient->doRequest( $sResource, $aData, 'POST' );

			if (
				empty( $aResult['refund'] )
				|| empty( $aResult['refund']['transaction'] )
			) {
				throw new Exception( 'Transaction.Request.Invalid', 'invalid payment data returned' );
			}

			return $this;
		}

	}

}
