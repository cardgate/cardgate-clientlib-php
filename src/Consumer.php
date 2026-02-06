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
     * Consumer instance.
     *
     * @method Consumer setEmail( string $email )
     * @method string getEmail()
     * @method bool hasEmail()
     * @method Consumer unsetEmail()
     *
     * @method Consumer setPhone( string $phone )
     * @method string getPhone()
     * @method bool hasPhone()
     * @method Consumer unsetPhone()
     */
    final class Consumer extends Entity
    {
        /**
         * @ignore
         * @internal The methods these fields expose are configured in the class phpdoc.
         */
        protected static $fields = [
            'Email'         => 'email',
            'Phone'         => 'phone'
        ];

        /**
         * The bill-to address.
         * @var Address
         * @access private
         */
        private $address = null;

        /**
         * The ship-to address.
         * @var Address
         * @access private
         */
        private $shippingAddress = null;

        /**
         * Accessor for the bill-to address.
         * @return Address
         * @access public
         * @api
         */
        public function address(): Address
        {
            if (null == $this->address) {
                $this->address = new Address();
            }
            return $this->address;
        }

        /**
         * Accessor for the ship-to address.
         * @access public
         * @api
         */
        public function shippingAddress(): Address
        {
            if (null == $this->shippingAddress) {
                $this->shippingAddress = new Address();
            }
            return $this->shippingAddress;
        }
    }

}
