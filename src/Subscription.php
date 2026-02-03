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
        private $sId;

        /**
         * The length of the subscription period.
         * @var int
         * @access private
         */
        private $iPeriod;

        /**
         * The type of period (ie day, week, month, year)
         * @var string
         * @access private
         */
        private $sPeriodType;

        /**
         * The price per period in cents
         * @var int
         * @access private
         */
        private $iPeriodPrice;

        /**
         * The amount of the initial (first) payment, used only when the first payment is different from the monthly costs.
         * @var int
         * @access private
         */
        private $iInitialPayment;

        /**
         * The length of the trial period.
         * @var int
         * @access private
         */
        private $iTrialPeriod;

        /**
         * The type of trial period (ie day, week, month, year)
         * @var string
         * @access private
         */
        private $sTrialPeriodType;

        /**
         * The price for the trial period in cents
         * @var int
         * @access private
         */
        private $iTrialPeriodPrice;

        /**
         * The start date (UTC) of the subscription in YYYY-MM-DD hh:mm:ss format.
         * If none is given the current date will be used.
         * @var string
         * @access private
         */
        private $sStartDate;

        /**
         * The end date (UTC) of the subscription in YYYY-MM-DD hh:mm:ss format.
         * If none is given the subscription will never end.
         * @var string
         * @access private
         */
        private $sEndDate;

        /**
         * The status of the subscription.
         * @var string
         * @access private
         */
        private $sStatus;

        /**
         * The constructor.
         *
         * @param Client $oClient_ The client associated with this subscription.
         * @param int $iSiteId_ Site id to create the subscription for.
         * @param int $iPeriod_ The period length of the subscription.
         * @param string $sPeriodType_ The period type of the subscription (e.g. day, week, month, year).
         * @param int $iPeriodAmount_ The period amount of the subscription in cents.
         * @param string $sCurrency_ Currency (ISO 4217)
         *
         * @throws Exception
         * @access public
         * @api
         */
        public function __construct(
            Client $oClient_,
            int $iSiteId_,
            int $iPeriod_,
            string $sPeriodType_,
            int $iPeriodAmount_,
            string $sCurrency_ = 'EUR'
        ) {
            $this->oClient = $oClient_;
            $this->setSiteId($iSiteId_)
                 ->setPeriod($iPeriod_)
                 ->setPeriodType($sPeriodType_)
                 ->setPeriodPrice($iPeriodAmount_)
                 ->setCurrency($sCurrency_);
        }

        /**
         * Set the subscription id.
         *
         * @param string $sId_ Subscription id to set.
         *
         * @return $this
         * @throws Exception
         * @access public
         * @api
         */
        public function setId(string $sId_): Transaction {
            if (
                empty($sId_)
            ) {
                throw new Exception('Subscription.Id.Invalid', 'invalid id: ' . $sId_);
            }
            $this->sId = $sId_;
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
            if (empty($this->sId)) {
                throw new Exception('Subscription.Not.Initialized', 'invalid subscription state');
            }
            return $this->sId;
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
            $this->iPeriod = $iPeriod_;
            return $this;
        }

        /**
         * Get the period of the subscription.
         * @return int The period of the subscription.
         * @access public
         * @api
         */
        public function getPeriod(): int {
            return $this->iPeriod;
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
            $this->sPeriodType = $sPeriodType_;
            return $this;
        }

        /**
         * Get the period type of the subscription.
         * @return string The period type of the subscription.
         * @access public
         * @api
         */
        public function getPeriodType(): string {
            return $this->sPeriodType;
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
            $this->iPeriodPrice = $iPeriodPrice_;
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
            return $this->iPeriodPrice;
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
            $this->iInitialPayment = $iAmount_;
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
            $this->iTrialPeriod = $iTrialPeriod_;
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
            return $this->iTrialPeriod;
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
            $this->sTrialPeriodType = $sTrialPeriodType_;
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
            return $this->sTrialPeriodType;
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
            $this->iTrialPeriodPrice = $iTrialPeriodPrice_;
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
            return $this->iTrialPeriodPrice;
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
            $this->sStartDate = $sStartDate_;
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
            return $this->sStartDate;
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
            $this->sEndDate = $sEndDate_;
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
            return $this->sEndDate;
        }

        /**
         * Get the status of the subscription.
         * @return string The end date of the subscription.
         * @access public
         * @api
         */
        public function getStatus()
        {
            return $this->sStatus;
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
                'site_id'               => $this->iSiteId,
                'currency_id'           => $this->sCurrency,
                'url_callback'          => $this->sCallbackUrl,
                'url_success'           => $this->sSuccessUrl,
                'url_failure'           => $this->sFailureUrl,
                'url_pending'           => $this->sPendingUrl,
                'description'           => $this->sDescription,
                'reference'             => $this->sReference,
                'recurring'             => true,
                'period'                => $this->iPeriod,
                'period_type'           => $this->sPeriodType,
                'period_price'          => $this->iPeriodPrice,
                'initial_payment'       => $this->iInitialPayment,
                'trial_period'          => $this->iTrialPeriod,
                'trial_period_type'     => $this->sTrialPeriodType,
                'trial_period_price'    => $this->iTrialPeriodPrice,
                'start_date'            => $this->sStartDate,
                'end_date'              => $this->sEndDate,
            ];
            if (! is_null($this->oConsumer)) {
                $aData['email'] = $this->oConsumer->getEmail();
                $aData['phone'] = $this->oConsumer->getPhone();
                $aData['consumer'] = array_merge(
                    $this->oConsumer->address()->getData(),
                    $this->oConsumer->shippingAddress()->getData('shipto_')
                );
                $aData['country_id'] = $this->oConsumer->address()->getCountry();
            }
            if (! is_null($this->oCart)) {
                $aData['cartitems'] = $this->oCart->getData();
            }

            $sResource = 'subscription/register/';

            if (! empty($this->oPaymentMethod)) {
                $aData['pt'] = $this->oPaymentMethod->getId();
                $aData['issuer'] = $this->sIssuer;
            }

            $aData = array_filter($aData); // remove NULL values
            $aResult = $this->oClient->doRequest($sResource, $aData, 'POST');

            if (
                empty($aResult)
                || empty($aResult['subscription'])
            ) {
                throw new Exception('Subscription.Request.Invalid', 'unexpected result: ' . $this->oClient->getLastResult() . $this->oClient->getDebugInfo(true, false));
            }
            $this->sId = $aResult['subscription'];
            if (
                isset($aResult['subscription']['action'])
                && 'redirect' == $aResult['subscription']['action']
            ) {
                $this->sActionUrl = $aResult['subscription']['url'];
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

            if (empty($this->sId)) {
                throw new Exception('Subscription.Request.Invalid', 'invalid subscription id');
            }

            if (! in_array($sStatus_, [ 'reactivate' , 'suspend', 'cancel', 'deactivate' ])) {
                throw new Exception('Subscription.Status.Invalid', 'invalid subscription status provided: '.$sStatus_);
            }

            $aData = [
                'subscription_id'       => $this->sId,
                'description'           => $this->sDescription,
            ];

            $sResource = 'subscription/' . $sStatus_ . '/';

            $aData = array_filter($aData); // remove NULL values
            $aResult = $this->oClient->doRequest($sResource, $aData, 'POST');

            if (false == $aResult['success']) {
                // oClient will have thrown an error if there was a proper error from the API, so this is weird!
                throw new Exception('Subscription.Request.Invalid', 'unexpected result: ' . $this->oClient->getLastResult() . $this->oClient->getDebugInfo(true, false));
            }

            return true;
        }
    }

}
