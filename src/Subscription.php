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
	 * Subscription instance.
	 */
	final class Subscription {

		/**
		 * The client associated with this subscription.
		 * @var Client
		 * @access private
		 */
		private $_oClient;

		/**
		 * The subscription id.
		 * @var String
		 * @access private
		 */
		private $_sId;

		/**
		 * The subscription site id.
		 * @var Integer
		 * @access private
		 */
		private $_iSiteId;

		/**
		 * The subscription amount in cents.
		 * @var Integer
		 * @access private
		 */
		private $_iAmount;

		/**
		 * The subscription currency (ISO 4217).
		 * @var String
		 * @access private
		 */
		private $_sCurrency;

		/**
		 * The constructor.
		 * @param Client $oClient_ The client associated with this subscription.
		 * @param Integer $iSiteId_ Site id to create subscription for.
		 * @param Integer $iAmount_ The amount of the subscription in cents.
		 * @param String $sCurrency_ Currency (ISO 4217)
		 * @return Subscription
		 * @throws Exception
		 * @access public
		 * @api
		 */
		function __construct( Client $oClient_, $iSiteId_, $iAmount_, $sCurrency_ = 'EUR' ) {
			$this->_oClient = $oClient_;
			$this->setSiteId( $iSiteId_ )->setAmount( $iAmount_ )->setCurrency( $sCurrency_ );
		}

		/**
		 * Set the subscription id.
		 * @param String $sId_ Subscription id to set.
		 * @return Subscription
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setId( $sId_ ) {
			if (
				! is_string( $sId_ )
				|| empty( $sId_ )
			) {
				throw new Exception( 'Subscription.Id.Invalid', 'invalid id: ' . $sId_ );
			}
			$this->_sId = $sId_;
			return $this;
		}

		/**
		 * Get the subscription id associated with this subscription.
		 * @return String The subscription id associated with this subscription.
		 * @access public
		 * @api
		 */
		public function getId() {
			if ( empty( $this->_sId ) ) {
				throw new Exception( 'Subscription.Not.Initialized', 'invalid subscription state' );
			}
			return $this->_sId;
		}

		/**
		 * Configure the subscription object with a site id.
		 * @param Integer $iSiteId_ Site id to set.
		 * @return Subscription
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setSiteId( $iSiteId_ ) {
			if ( ! is_integer( $iSiteId_ ) ) {
				throw new Exception( 'Subscription.SiteId.Invalid', 'invalid site: ' . $iSiteId_ );
			}
			$this->_iSiteId = $iSiteId_;
			return $this;
		}

		/**
		 * Get the site id associated with this subscription.
		 * @return Integer The site id associated with this subscription.
		 * @access public
		 * @api
		 */
		public function getSiteId() {
			return $this->_iSiteId;
		}

		/**
		 * Configure the subscription object with an amount.
		 * @param Integer $iAmount_ Amount in cents to set.
		 * @return Subscription
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setAmount( $iAmount_ ) {
			if ( ! is_integer( $iAmount_ ) ) {
				throw new Exception( 'Subscription.Amount.Invalid', 'invalid amount: ' . $iAmount_ );
			}
			$this->_iAmount = $iAmount_;
			return $this;
		}

		/**
		 * Get the amount of the subscription.
		 * @return Integer The amount of the subscription.
		 * @access public
		 * @api
		 */
		public function getAmount() {
			return $this->_iAmount;
		}

		/**
		 * Configure the subscription currency.
		 * @param String $sCurrency_ The currency to set.
		 * @return Subscription
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setCurrency( $sCurrency_ ) {
			if ( ! is_string( $sCurrency_ ) ) {
				throw new Exception( 'Subscription.Currency.Invalid', 'invalid currency: ' . $sCurrency_ );
			}
			$this->_sCurrency = $sCurrency_;
			return $this;
		}

		/**
		 * Get the currency of the subscription.
		 * @return String The currency of the subscription.
		 * @access public
		 * @api
		 */
		public function getCurrency() {
			return $this->_sCurrency;
		}

		/**
		 * Registers the subscription with the cardgate payment gateway.
		 * @return Subscription
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function register() {
			$aData = [
				'site'			=> $this->_iSiteId,
				'amount'		=> $this->_iAmount,
				'currency_id'	=> $this->_sCurrency
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
				|| empty( $aResult['payment']['subscription'] )
			) {
				throw new Exception( 'Subscription.Request.Invalid', 'invalid payment data returned' );
			}
			$this->_sId = $aResult['payment']['subscription'];
			if (
				isset( $aResult['payment']['action'] )
				&& 'redirect' == $aResult['payment']['action']
			) {
				$this->_sActionUrl = $aResult['payment']['url'];
			}

			return $this;
		}

	}

}
