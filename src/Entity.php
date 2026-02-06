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

    use ReflectionClass;

    /**
     * CardGate client object.
     */
    abstract class Entity
    {
        /**
         * @ignore
         * @internal The data property holds the data of the entity.
         */
        protected $data = [];

        /**
         * @ignore
         * @internal To make the data in an Entity available through magic functions setName, getAmount, unsetName,
         * hasCart populate the fields property below. To make autocompletion work in Zend, use the @method phpdoc
         * directive like this: @method null setId( int $iId ).
         * Example: [ 'MerchantId' => 'merchant_id', 'Name' => 'name' ]
         */
        protected static $fields = [];

        /**
         * This method can be used to retrieve all the data of the instance.
         * @param string|null $prefix Optionally prefix all the data entries.
         * @return array Returns an array with the data in the entity.
         */
        public function getData(?string $prefix = null): array
        {
            if (is_string($prefix)) {
                $result = [];
                foreach ($this->data as $key => $value) {
                    $result[$prefix . $key] = $value;
                }
                return $result;
            } else {
                return $this->data;
            }
        }

        /**
         * @return $this|mixed|bool Return $this on set and unset, mixed on get and bool
         * @throws Exception
         *@ignore
         * @internal The __call method translates get-, set-, unset- and has-methods to their configured fields.
         */
        public function __call($method, $args)
        {
            $className = ( new ReflectionClass($this) )->getShortName();
            switch (substr($method, 0, 3)) {
                case 'get':
                    $key = substr($method, 3);
                    if (isset(static::$fields[$key])) {
                        return $this->data[ static::$fields[ $key ] ] ?? null;
                    }
                    break;
                case 'set':
                    $key = substr($method, 3);
                    if (isset(static::$fields[$key])) {
                        if (isset($args[0])) {
                            if (
                                is_scalar($args[0])
                                && (
                                    ! is_string($args[0])
                                    || strlen($args[0]) > 0
                                )
                            ) {
                                $this->data[static::$fields[$key]] = $args[0];
                                return $this; // makes the call chainable
                            } else {
                                throw new Exception("$className.Invalid.Method", "invalid value for $method");
                            }
                        } else {
                            throw new Exception("$className.Invalid.Method", "missing parameter 1 for $method");
                        }
                    }
                    break;
                case 'uns':
                    $key = substr($method, 5);
                    if (isset(static::$fields[$key])) {
                        unset($this->data[static::$fields[$key]]);
                        return $this; // makes the call chainable
                    }
                    break;
                case 'has':
                    $key = substr($method, 3);
                    if (isset(static::$fields[$key])) {
                        return isset($this->data[static::$fields[$key]]);
                    }
                    break;
            }
            throw new Exception("$className.Invalid.Method", "call to undefined method $method");
        }
    }

}
