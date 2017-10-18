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
	 * Paymentmethod instance.
	 */
	final class Method {

		/**
		 * iDeal.
		 */
		const IDEAL = 'ideal';

		/**
		 * iDeal (legacy).
		 */
		const IDEALPRO = 'idealpro';

		/**
		 * BanContact.
		 */
		const BANCONTACT = 'bancontact';

		/**
		 * MisterCash (legacy)
		 */
		const MISTERCASH = 'mistercash';

		/**
		 * CreditCard.
		 */
		const CREDITCARD = 'creditcard';

		/**
		 * Afterpay.
		 */
		const AFTERPAY = 'afterpay';

		/**
		 * Giropay.
		 */
		const GIROPAY = 'giropay';

		/**
		 * Giropay.
		 */
		const BANKTRANSFER = 'banktransfer';

		/**
		 * Bitcoins.
		 */
		const BITCOIN = 'bitcoin';

		/**
		 * DirectDebit.
		 */
		const DIRECTDEBIT = 'directdebit';

		/**
		 * Klarna.
		 */
		const KLARNA = 'klarna';

		/**
		 * PayPal.
		 */
		const PAYPAL = 'paypal';

		/**
		 * Przelewy24.
		 */
		const PRZELEWY24 = 'przelewy24';

		/**
		 * SofortBanking.
		 */
		const SOFORTBANKING = 'sofortbanking';

		/**
         * Paysafecard
         */
		const PAYSAFECARD = 'paysafecard';

		/**
		 * The client associated with this payment method.
		 * @var Client
		 * @access private
		 */
		private $_oClient;

		/**
		 * The payment method.
		 * @var String
		 * @access private
		 */
		private $_sId;

		/**
		 * The constructor.
		 * @param Client $oClient_ The client associated with this transaction.
		 * @param String $sId_ The payment method identifier to create a method instance for.
		 * @return Method
		 * @throws Exception
		 * @access public
		 * @api
		 */
		function __construct( Client $oClient_, $sId_ ) {
			$this->_oClient = $oClient_;
			if ( ! in_array( $sId_, ( new \ReflectionClass( '\cardgate\api\Method' ) )->getConstants() ) ) {
				throw new Exception( 'Method.PaymentMethod.Invalid', 'invalid payment method: ' . $sId_ );
			}
			$this->setId( $sId_ );
		}

		/**
		 * Set the method id.
		 * @param String $sId_ Method id to set.
		 * @return Method
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function setId( $sId_ ) {
			if (
				! is_string( $sId_ )
				|| empty( $sId_ )
			) {
				throw new Exception( 'Method.Id.Invalid', 'invalid id: ' . $sId_ );
			}
			$this->_sId = $sId_;
			return $this;
		}

		/**
		 * Get the payment method id.
		 * @return String The payment method id for this instance.
		 * @access public
		 * @api
		 */
		public function getId() {
			return $this->_sId;
		}

		/**
		 * This method returns all the issuers available for the current payment method.
		 * @return Array An array with {@link \cardgate\api\Issuer} instances.
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function getIssuers() {
			$sResource = $this->_sId . '/issuers/';

			$aResult = $this->_oClient->doRequest( $sResource, NULL, 'GET' );

			if ( empty( $aResult['issuers'] ) ) {
				throw new Exception( 'Method.Issuers.Invalid', 'invalid issuer data returned' );
			}

			return $aResult['issuers'];
		}

	}

}
