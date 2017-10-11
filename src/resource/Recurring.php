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
namespace cardgate\api\resource {

	/**
	 * CardGate resource object.
	 */
	final class Recurring extends Base {

		/**
		 * This method can be used to retrieve transaction details.
		 * @param String $sTransactionId_ The transaction identifier.
		 * @param array $aDetails_ Array that gets filled with additional transaction details.
		 * @return \cardgate\api\Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function get( $sTransactionId_, &$aDetails_ = NULL ) {
			if ( ! is_string( $sTransactionId_ ) ) {
				throw new \cardgate\api\Exception( 'Transaction.Id.Invalid', 'invalid transaction id: ' . $sTransactionId_ );
			}

			$sResource = "transaction/{$sTransactionId_}/";

			$aResult = $this->_oClient->doRequest( $sResource, NULL, 'GET' );

			if ( empty( $aResult['transaction'] ) ) {
				throw new \cardgate\api\Exception( 'Transaction.Details.Invalid', 'invalid transaction data returned' );
			}

			if ( ! is_null( $aDetails_ ) ) {
				$aDetails_ = array_merge( $aDetails_, $aResult['transaction'] );
			}

			$oTransaction = new \cardgate\api\Recurring( $this->_oClient, (int)$aResult['transaction']['site_id'], (int)$aResult['transaction']['amount'], $aResult['transaction']['currency_id'] );
			$oTransaction
				->setId( $aResult['transaction']['id'] )
				->setDescription( $aResult['transaction']['description'] )
				->setReference( $aResult['transaction']['reference'] )
				->setPaymentMethod( $aResult['transaction']['option'] )
			;

			// TODO set consumer?

			return $oTransaction;
		}

		/**
		 * This method can be used to create a new transaction.
		 * @param Integer $iSiteId_ Site id to create transaction for.
		 * @param Integer $iAmount_ The amount of the transaction in cents.
		 * @param String $sCurrency_ Currency (ISO 4217)
		 * @return \cardgate\api\Transaction
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function create( $iSiteId_, $iAmount_, $sCurrency_ = 'EUR' ) {
			return new \cardgate\api\Recurring( $this->_oClient, $iSiteId_, $iAmount_, $sCurrency_ );
		}

		/**
		 * This method can be used to verify a callback for a transaction.
		 * @param Array $aData_ The callback data (usually $_GET) to use for verification.
		 * @param String $sSiteKey_ The site key used to verify hash. Leave empty to check hash with the
		 * use of the merchant key only (otherwise both are checked).
		 * @return Boolean Returns TRUE if the callback is valid or FALSE if not.
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function verifyCallback( $aData_, $sSiteKey_ = NULL ) {
			foreach( [ 'transaction', 'currency', 'amount', 'reference', 'code', 'hash', 'status' ] as $sRequiredKey ) {
				if ( ! isset( $aData_[$sRequiredKey] ) ) {
					throw new \cardgate\api\Exception( 'Transaction.Callback.Missing', 'missing callback data: ' . $sRequiredKey );
				}
			}
			$sPrefix = '';
			if ( ! empty( $aData_['testmode'] ) ) {
				$sPrefix = 'TEST';
			}
			return (
				(
					NULL !== $sSiteKey_
					&& md5(
						$sPrefix
						. $aData_['transaction']
						. $aData_['currency']
						. $aData_['amount']
						. $aData_['reference']
						. $aData_['code']
						. $sSiteKey_
					) == $aData_['hash']
				)
				|| md5(
					$sPrefix
					. $aData_['transaction']
					. $aData_['currency']
					. $aData_['amount']
					. $aData_['reference']
					. $aData_['code']
					. $this->_oClient->getKey()
				) == $aData_['hash']
			);
		}

	}

}
