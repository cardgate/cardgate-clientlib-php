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

    use ReflectionException;

    /**
     * Cart instance.
     */
    final class Cart
    {
        /**
         * The items in this cart.
         * @var Client
         * @access private
         */
        private $items = [];

        /**
         * Add a cart item to the cart.
         *
         * @param int $iType_ The cart item type.
         * @param string $sSKU_ The SKU of the cart item.
         * @param string $sName_ The product name of the cart item.
         * @param int $iPrice_ The price of the cart item.
         * @param string | null $sLink_ An optional link to the product.
         *
         * @return Item Returns the item that was added.
         * @throws Exception|ReflectionException
         * @access public
         * @api
         */
        public function addItem(int $type, string $sku, string $name, int $quantity, int $price, ?string $link = null): Item
        {
            $item         = new Item($type, $sku, $name, $quantity, $price, $link);
            $this->items[] = $item;
            return $item;
        }

        /**
         * @return array
         */
        public function getData(): array
        {
            $data = [];
            foreach ($this->items as $item) {
                $data[] = $item->getData();
            }
            return $data;
        }
    }

}
