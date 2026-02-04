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

namespace cardgate\api\resource {

    use cardgate\api\Exception;
    use cardgate\api\Subscription;

    /**
     * CardGate resource object.
     */
    final class Subscriptions extends Base
    {
        /**
         * This method can be used to retrieve subscription details.
         *
         * @param string $subscriptionId The subscription identifier.
         * @param array|null $details Array that gets filled with additional subscription details.
         *
         * @return Subscription
         * @throws Exception
         * @access public
         * @api
         */
        public function get(string $subscriptionId, array &$details = null): Subscription
        {
            if (! is_string($subscriptionId)) {
                throw new Exception('Subscription.Id.Invalid', 'invalid subscription id: ' . $subscriptionId);
            }

            $sResource = "subscription/{$subscriptionId}/";

            $aResult = $this->client->doRequest($sResource, null, 'GET');

            if (empty($aResult['subscription'])) {
                throw new Exception('Subscription.Details.Invalid', 'invalid subscription data returned');
            }

            if (! is_null($details)) {
                $details = array_merge($details, $aResult['subscription']);
            }

            $oSubscription = new Subscription(
                $this->client,
                (int) $aResult['subscription']['site_id'],
                (int) $aResult['subscription']['period'],
                $aResult['subscription']['period_type'],
                (int) $aResult['subscription']['period_price']
            );
            $oSubscription->setId($aResult['subscription']['nn_id']);
            if (! empty($aResult['subscription']['description'])) {
                $oSubscription->setDescription($aResult['subscription']['description']);
            }
            if (! empty($aResult['subscription']['reference'])) {
                $oSubscription->setReference($aResult['subscription']['reference']);
            }
            if (! empty($aResult['subscription']['start_date'])) {
                $oSubscription->setStartDate($aResult['subscription']['start_date']);
            }
            if (! empty($aResult['subscription']['end_date'])) {
                $oSubscription->setEndDate($aResult['subscription']['end_date']);
            }
            // TODO: map other subscription fields? method_id can't be used in client::Method currently...
            /*
            if ( ! empty( $aResult['subscription']['code'] ) ) {
                $oSubscription->setCode( $aResult['subscription']['code'] );
            }
            if ( ! empty( $aResult['subscription']['payment_type_id'] ) ) {
                $oSubscription->setPaymentMethod( $aResult['subscription']['payment_type_id'] );
            }
            if ( ! empty( $aResult['subscription']['last_payment_date'] ) ) {
                $oSubscription->setPaymentMethod( $aResult['subscription']['last_payment_date'] );
            }
            */

            return $oSubscription;
        }

        /**
         * This method can be used to create a new subscription.
         *
         * @param int $siteId Site id to create the subscription for.
         * @param int $period The period length of the subscription.
         * @param string $periodType The period type of the subscription (e.g., day, week, month, year).
         * @param int $periodAmount The period amount of the subscription in cents.
         * @param string $currency Currency (ISO 4217)
         *
         * @throws Exception
         * @access public
         * @api
         */
        public function create(
            int $siteId,
            int $period,
            string $periodType,
            int $periodAmount,
            string $currency = 'EUR'
        ): Subscription
        {
            return new Subscription($this->client, $siteId, $period, $periodType, $periodAmount, $currency);
        }
    }

}
