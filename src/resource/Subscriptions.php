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
	final class Subscriptions extends Base {

		/**
		 * This method can be used to retrieve subscription details.
		 * @param String $sSubscriptionId_ The subscription identifier.
		 * @param array $aDetails_ Array that gets filled with additional subscription details.
		 * @return \cardgate\api\Subscription
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function get( $sSubscriptionId_, &$aDetails_ = NULL ) {
			if ( ! is_string( $sSubscriptionId_ ) ) {
				throw new \cardgate\api\Exception( 'Subscription.Id.Invalid', 'invalid subscription id: ' . $sSubscriptionId_ );
			}

			$sResource = "subscription/{$sSubscriptionId_}/";

			$aResult = $this->_oClient->doRequest( $sResource, NULL, 'GET' );

			if ( empty( $aResult['subscription'] ) ) {
				throw new \cardgate\api\Exception( 'Subscription.Details.Invalid', 'invalid subscription data returned' );
			}

			if ( ! is_null( $aDetails_ ) ) {
				$aDetails_ = array_merge( $aDetails_, $aResult['subscription'] );
			}

			$oSubscription = new \cardgate\api\Subscription( $this->_oClient, (int)$aResult['subscription']['site_id'], (int)$aResult['subscription']['amount'], $aResult['subscription']['currency_id'] );
			$oSubscription
				->setId( $aResult['subscription']['id'] )
				->setDescription( $aResult['subscription']['description'] )
				->setReference( $aResult['subscription']['reference'] )
				->setPaymentMethod( $aResult['subscription']['option'] )
			;

			// TODO set consumer?

			return $oSubscription;
		}

		/**
		 * This method can be used to create a new subscription.
		 * @param Integer $iSiteId_ Site id to create subscription for.
		 * @param Integer $iAmount_ The amount of the subscription in cents.
		 * @param String $sCurrency_ Currency (ISO 4217)
		 * @return \cardgate\api\Subscription
		 * @throws Exception
		 * @access public
		 * @api
		 */
		public function create( $iSiteId_, $iAmount_, $sCurrency_ = 'EUR' ) {
			return new \cardgate\api\Subscription( $this->_oClient, $iSiteId_, $iAmount_, $sCurrency_ );
		}

	}

}
