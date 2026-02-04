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
     * Subscription instance.
     */
    final class Subscription extends Transaction
    {
        /**
         * The subscription id.
         * @var string
         * @access private
         */
        private $id;

        /**
         * The length of the subscription period.
         * @var int
         * @access private
         */
        private $period;

        /**
         * The type of period (ie day, week, month, year)
         * @var string
         * @access private
         */
        private $periodType;

        /**
         * The price per period in cents
         * @var int
         * @access private
         */
        private $periodPrice;

        /**
         * The amount of the initial (first) payment, used only when the first payment is different from the monthly costs.
         * @var int
         * @access private
         */
        private $initialPayment;

        /**
         * The length of the trial period.
         * @var int
         * @access private
         */
        private $trialPeriod;

        /**
         * The type of trial period (ie day, week, month, year)
         * @var string
         * @access private
         */
        private $trialPeriodType;

        /**
         * The price for the trial period in cents
         * @var int
         * @access private
         */
        private $trialPeriodPrice;

        /**
         * The start date (UTC) of the subscription in YYYY-MM-DD hh:mm:ss format.
         * If none is given the current date will be used.
         * @var string
         * @access private
         */
        private $startDate;

        /**
         * The end date (UTC) of the subscription in YYYY-MM-DD hh:mm:ss format.
         * If none is given the subscription will never end.
         * @var string
         * @access private
         */
        private $endDate;

        /**
         * The status of the subscription.
         * @var string
         * @access private
         */
        private $status;

        /**
         * The constructor.
         *
         * @param Client $client The client associated with this subscription.
         * @param int $siteId Site id to create the subscription for.
         * @param int $period The period length of the subscription.
         * @param string $periodType The period type of the subscription (e.g. day, week, month, year).
         * @param int $periodAmount The period amount of the subscription in cents.
         * @param string $currency Currency (ISO 4217)
         *
         * @throws Exception
         * @access public
         * @api
         */
        public function __construct(
            Client $client,
            int $siteId,
            int $period,
            string $periodType,
            int $periodAmount,
            string $currency = 'EUR'
        ) {
            $this->client = $client;
            $this->setSiteId($siteId)
                 ->setPeriod($period)
                 ->setPeriodType($periodType)
                 ->setPeriodPrice($periodAmount)
                 ->setCurrency($currency);
        }

        /**
         * Set the subscription id.
         *
         * @param string $id Subscription id to set.
         *
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setId(string $id): Transaction {
            if (
                empty($id)
            ) {
                throw new Exception('Subscription.Id.Invalid', 'invalid id: ' . $id);
            }
            $this->id = $id;
            return $this;
        }

        /**
         * Get the subscription id associated with this subscription.
         * @return string The subscription id associated with this subscription.
         * @throws Exception
         * @access public
         * @api
         */
        public function getId(): string {
            if (empty($this->id)) {
                throw new Exception('Subscription.Not.Initialized', 'invalid subscription state');
            }
            return $this->id;
        }

        /**
         * Configure the subscription object with a period.
         *
         * @param int $iPeriod_ Period length to set.
         *
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setPeriod(int $iPeriod_): Subscription
        {
            if (! is_integer($iPeriod_)) {
                throw new Exception('Subscription.Period.Invalid', 'invalid period: ' . $iPeriod_);
            }
            $this->period = $iPeriod_;
            return $this;
        }

        /**
         * Get the period of the subscription.
         * @return int The period of the subscription.
         * @access public
         * @api
         */
        public function getPeriod(): int {
            return $this->period;
        }

        /**
         * Configure the subscription object with a period type.
         *
         * @param string $sPeriodType_ Period type to set.
         *
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setPeriodType(string $sPeriodType_): Subscription
        {
            if (
                ! in_array($sPeriodType_, [ 'day', 'week', 'month', 'year' ])
            ) {
                throw new Exception('Subscription.Period.Type.Invalid', 'invalid period type: ' . $sPeriodType_);
            }
            $this->periodType = $sPeriodType_;
            return $this;
        }

        /**
         * Get the period type of the subscription.
         * @return string The period type of the subscription.
         * @access public
         * @api
         */
        public function getPeriodType(): string {
            return $this->periodType;
        }

        /**
         * Configure the subscription object with a period price.
         * @param int $iPeriod_ Period price to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setPeriodPrice($iPeriodPrice_)
        {
            if (! is_integer($iPeriodPrice_)) {
                throw new Exception('Subscription.Period.Price.Invalid', 'invalid period price: ' . $iPeriodPrice_);
            }
            $this->periodPrice = $iPeriodPrice_;
            return $this;
        }

        /**
         * Get the period price of the subscription.
         * @return int The period price of the subscription.
         * @access public
         * @api
         */
        public function getPeriodPrice()
        {
            return $this->periodPrice;
        }

        /**
         * Configure the subscription initial payment amount.
         * @param int $iInitialPayment_ The initial payment amount to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setInitialPayment($iAmount_)
        {
            if (! is_integer($iAmount_)) {
                throw new Exception('Subscription.Initial.Payment.Invalid', 'invalid initial payment amount: ' . $iAmount_);
            }
            $this->initialPayment = $iAmount_;
            return $this;
        }


        /**
         * Configure the subscription object with a trial period.
         * @param int $iPeriod_ Trial period length to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setTrialPeriod($iTrialPeriod_)
        {
            if (! is_integer($iTrialPeriod_)) {
                throw new Exception('Subscription.Period.Invalid', 'invalid trial period: ' . $iTrialPeriod_);
            }
            $this->trialPeriod = $iTrialPeriod_;
            return $this;
        }

        /**
         * Get the trial period of the subscription.
         * @return int The trial period of the subscription.
         * @access public
         * @api
         */
        public function getTrialPeriod()
        {
            return $this->trialPeriod;
        }

        /**
         * Configure the subscription object with a trial period type.
         * @param string $sTrialPeriodType_ Trial Period type to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setTrialPeriodType($sTrialPeriodType_)
        {
            if (
                ! is_string($sTrialPeriodType_)
                || ! in_array($sTrialPeriodType_, [ 'day', 'week', 'month', 'year' ])
            ) {
                throw new Exception('Subscription.Period.Type.Invalid', 'invalid trial period type: ' . $sTrialPeriodType_);
            }
            $this->trialPeriodType = $sTrialPeriodType_;
            return $this;
        }

        /**
         * Get the trial period type of the subscription.
         * @return string The trial period type of the subscription.
         * @access public
         * @api
         */
        public function getTrialPeriodType()
        {
            return $this->trialPeriodType;
        }

        /**
         * Configure the subscription object with a trial period price.
         * @param int $iPeriod_ Trial period price to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setTrialPeriodPrice($iTrialPeriodPrice_)
        {
            if (! is_integer($iTrialPeriodPrice_)) {
                throw new Exception('Subscription.Period.Price.Invalid', 'invalid trial period price: ' . $iTrialPeriodPrice_);
            }
            $this->trialPeriodPrice = $iTrialPeriodPrice_;
            return $this;
        }

        /**
         * Get the period price of the subscription.
         * @return int The period price of the subscription.
         * @access public
         * @api
         */
        public function getTrialPeriodPrice()
        {
            return $this->trialPeriodPrice;
        }

        /**
         * Configure the subscription date on which it should start.
         * @param string $sStartDate_ The start date to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setStartDate($sStartDate_)
        {
            if (! is_string($sStartDate_)) {
                throw new Exception('Subscription.Date.Start.Invalid', 'invalid start date: ' . $sStartDate_);
            }
            $this->startDate = $sStartDate_;
            return $this;
        }

        /**
         * Get the start date of the subscription.
         * @return string The start date of the subscription.
         * @access public
         * @api
         */
        public function getStartDate()
        {
            return $this->startDate;
        }

        /**
         * Configure the date on which the subscription should end.
         * @param string $sEndDate_ The end date to set.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setEndDate($sEndDate_)
        {
            if (! is_string($sEndDate_)) {
                throw new Exception('Subscription.Date.End.Invalid', 'invalid end date: ' . $sEndDate_);
            }
            $this->endDate = $sEndDate_;
            return $this;
        }

        /**
         * Get the end date of the subscription.
         * @return string The end date of the subscription.
         * @access public
         * @api
         */
        public function getEndDate()
        {
            return $this->endDate;
        }

        /**
         * Get the status of the subscription.
         * @return string The end date of the subscription.
         * @access public
         * @api
         */
        public function getStatus()
        {
            return $this->status;
        }

        /**
         * Registers the subscription with the CardGate payment gateway.
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function register(): Transaction {
            $aData = [
                'site_id'               => $this->siteId,
                'currency_id'           => $this->currency,
                'url_callback'          => $this->callbackUrl,
                'url_success'           => $this->successUrl,
                'url_failure'           => $this->failureUrl,
                'url_pending'           => $this->pendingUrl,
                'description'           => $this->description,
                'reference'             => $this->reference,
                'recurring'             => true,
                'period'                => $this->period,
                'period_type'           => $this->periodType,
                'period_price'          => $this->periodPrice,
                'initial_payment'       => $this->initialPayment,
                'trial_period'          => $this->trialPeriod,
                'trial_period_type'     => $this->trialPeriodType,
                'trial_period_price'    => $this->trialPeriodPrice,
                'start_date'            => $this->startDate,
                'end_date'              => $this->endDate,
            ];
            if (! is_null($this->consumer)) {
                $aData['email'] = $this->consumer->getEmail();
                $aData['phone'] = $this->consumer->getPhone();
                $aData['consumer'] = array_merge(
                    $this->consumer->address()->getData(),
                    $this->consumer->shippingAddress()->getData('shipto_')
                );
                $aData['country_id'] = $this->consumer->address()->getCountry();
            }
            if (! is_null($this->cart)) {
                $aData['cartitems'] = $this->cart->getData();
            }

            $sResource = 'subscription/register/';

            if (! empty($this->paymentMethod)) {
                $aData['pt'] = $this->paymentMethod->getId();
                $aData['issuer'] = $this->issuer;
            }

            $aData = array_filter($aData); // remove NULL values
            $aResult = $this->client->doRequest($sResource, $aData, 'POST');

            if (
                empty($aResult)
                || empty($aResult['subscription'])
            ) {
                throw new Exception('Subscription.Request.Invalid', 'unexpected result: ' . $this->client->getLastResult() . $this->client->getDebugInfo(true, false));
            }
            $this->id = $aResult['subscription'];
            if (
                isset($aResult['subscription']['action'])
                && 'redirect' == $aResult['subscription']['action']
            ) {
                $this->actionUrl = $aResult['subscription']['url'];
            }

            return $this;
        }

        /**
         * Change the subscription status.
         * @return bool Whether the status change succeeded.
         * @throws Exception
         * @access public
         * @api
         */
        public function changeStatus($sStatus_)
        {

            if (empty($this->id)) {
                throw new Exception('Subscription.Request.Invalid', 'invalid subscription id');
            }

            if (! in_array($sStatus_, [ 'reactivate' , 'suspend', 'cancel', 'deactivate' ])) {
                throw new Exception('Subscription.Status.Invalid', 'invalid subscription status provided: '.$sStatus_);
            }

            $aData = [
                'subscription_id'       => $this->id,
                'description'           => $this->description,
            ];

            $sResource = 'subscription/' . $sStatus_ . '/';

            $aData = array_filter($aData); // remove NULL values
            $aResult = $this->client->doRequest($sResource, $aData, 'POST');

            if (false == $aResult['success']) {
                // client will have thrown an error if there was a proper error from the API, so this is weird!
                throw new Exception('Subscription.Request.Invalid', 'unexpected result: ' . $this->client->getLastResult() . $this->client->getDebugInfo(true, false));
            }

            return true;
        }
    }

}
