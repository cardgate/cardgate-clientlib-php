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
    use cardgate\api\Transaction;
    use ReflectionException;

    /**
     * CardGate resource object.
     */
    final class Transactions extends Base
    {
        /**
         * This method can be used to retrieve transaction details.
         *
         * @param string $transactionId The transaction identifier.
         * @param array|null $details array that gets filled with additional transaction details.
         *
         * @return Transaction
         * @throws Exception|ReflectionException
         * @access public
         * @api
         */
        public function get(string $transactionId, array &$details = null): Transaction
        {
            if (! is_string($transactionId)) {
                throw new Exception('Transaction.Id.Invalid', 'invalid transaction id: ' . $transactionId);
            }

            $resource = "transaction/$transactionId/";

            $result = $this->client->doRequest($resource, null, 'GET');

            if (empty($result['transaction'])) {
                throw new Exception('Transaction.Details.Invalid', 'invalid transaction data returned' . $this->client->getDebugInfo());
            }

            if (! is_null($details)) {
                $details = array_merge($details, $result['transaction']);
            }

            $transaction = new Transaction(
                $this->client,
                (int) $result['transaction']['site_id'],
                (int) $result['transaction']['amount'],
                $result['transaction']['currency_id']
            );
            $transaction->setId($result['transaction']['id']);
            if (! empty($result['transaction']['description'])) {
                $transaction->setDescription($result['transaction']['description']);
            }
            if (! empty($result['transaction']['reference'])) {
                $transaction->setReference($result['transaction']['reference']);
            }
            if (! empty($result['transaction']['option'])) {
                $transaction->setPaymentMethod($result['transaction']['option']);
            }

            return $transaction;
        }

        /**
         * This method can be used to retrieve a transaction status.
         *
         * @param string $transactionId The transaction identifier.
         *
         * @return string
         * @throws Exception
         * @access public
         * @api
         */
        public function status(string $transactionId): string
        {
            if (! is_string($transactionId)) {
                throw new Exception('Transaction.Id.Invalid', 'invalid transaction id: ' . $transactionId);
            }

            $resource = "status/$transactionId/";

            $result = $this->client->doRequest($resource, null, 'GET');

            if (
                empty($result['status'])
                || ! is_string($result['status'])
            ) {
                throw new Exception('Transaction.Status.Invalid', 'invalid transaction status returned' . $this->client->getDebugInfo());
            }

            return $result['status'];
        }

        /**
         * This method can be used to create a new transaction.
         *
         * @param int $siteId Site id to create transaction for.
         * @param int $amount The amount of the transaction in cents.
         * @param string $currency Currency (ISO 4217)
         *
         * @return Transaction
         * @throws Exception
         * @access public
         * @api
         */
        public function create(int $siteId, int $amount, string $currency = 'EUR'): Transaction
        {
            return new Transaction($this->client, $siteId, $amount, $currency);
        }

        /**
         * This method can be used to verify a callback for a transaction.
         *
         * @param array $data The callback data (usually $_GET) to use for verification.
         * @param string|null $siteKey The site key used to verify hash. Leave empty to check hash with the
         * use of the merchant key only (otherwise both are checked).
         *
         * @return bool Returns TRUE if the callback is valid or FALSE if not.
         * @throws Exception
         * @access public
         * @api
         */
        public function verifyCallback(array $data, string $siteKey = null): bool
        {
            foreach ([ 'transaction', 'currency', 'amount', 'reference', 'code', 'hash', 'status' ] as $requiredKey) {
                if (! isset($data[$requiredKey])) {
                    throw new Exception('Transaction.Callback.Missing', 'missing callback data: ' . $requiredKey);
                }
            }

            $prefix = empty($data['testmode']) ? '': 'TEST';

            return (
                (
                    null !== $siteKey
                    && md5(
                           $prefix
                           . $data['transaction']
                           . $data['currency']
                           . $data['amount']
                           . $data['reference']
                           . $data['code']
                           . $siteKey
                    ) == $data['hash']
                )
                || md5(
                       $prefix
                       . $data['transaction']
                       . $data['currency']
                       . $data['amount']
                       . $data['reference']
                       . $data['code']
                       . $this->client->getKey()
                ) == $data['hash']
            );
        }
    }

}
