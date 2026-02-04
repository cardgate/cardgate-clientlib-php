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
    use ReflectionException;

    /**
     * Item instance.
     *
     * @method Item setSKU( string $sku ) Sets the sku.
     * @method string getSKU() Returns the sku.
     * @method bool hasSKU() Checks for the existence of sku.
     * @method Item unsetSKU() Unsets the sku.
     *
     * @method Item setName( string $name ) Sets the name.
     * @method string getName() Returns the name.
     * @method bool hasName() Checks for the existence of name.
     * @method Item unsetName() Unsets the name.
     *
     * @method Item setLink( string $link ) Sets the link.
     * @method string getLink() Returns the link.
     * @method bool hasLink() Checks for the existence of a link.
     * @method Item unsetLink() Unsets the link.
     *
     * @method Item setQuantity( string $quantity ) Sets the quantity.
     * @method string getQuantity() Returns the quantity.
     * @method bool hasQuantity() Checks for the existence of quantity.
     * @method Item unsetQuantity() Unsets the quantity.
     *
     * @method Item setPrice( int $price ) Sets the price.
     * @method int getPrice() Returns the price.
     * @method bool hasPrice() Checks for the existence of price.
     * @method Item unsetPrice() Unsets the price.
     *
     * @method string getType() Returns the type.
     * @method bool hasType() Checks for the existence of a type.
     * @method Item unsetType() Unsets the type.
     *
     * @method float getVat() Returns the vat.
     * @method bool hasVat() Checks for the existence of vat.
     * @method Item unsetVat() Unsets the vat.
     *
     * @method bool getVatIncluded() Returns the vat included flag.
     * @method bool hasVatIncluded() Checks for the existence of vat included flag.
     * @method Item unsetVatIncluded() Unsets the flag vat included.
     *
     * @method float getVatAmount() Returns the vat amount.
     * @method bool hasVatAmount() Checks for the existence of vat amount.
     * @method Item unsetVatAmount() Unsets the vat amount.
     *
     * @method float getStock() Returns the stock.
     * @method bool hasStock() Checks for the existence of stock.
     * @method Item unsetStock() Unsets the stock.
     */
    final class Item extends Entity
    {
        /**
         * Product.
         */
        public const TYPE_PRODUCT = 1;

        /**
         * Shipping Costs
         */
        public const TYPE_SHIPPING = 2;

        /**
         * Payment Costs
         */
        public const TYPE_PAYMENT = 3;

        /**
         * Discount
         */
        public const TYPE_DISCOUNT = 4;

        /**
         * Handling fees
         */
        public const TYPE_HANDLING = 5;

        /**
         * Correction
         */
        public const TYPE_CORRECTION = 6;

        /**
         * VAT Correction
         */
        public const TYPE_VAT_CORRECTION = 7;

        /**
         * @ignore
         * @internal The methods these fields expose are configured in the class phpdoc.
         */
        protected static $fields = [
            'SKU'           => 'sku',
            'Name'          => 'name',
            'Link'          => 'link',
            'Quantity'      => 'quantity',
            'Price'         => 'price',
            'Type'          => 'type',
            'Vat'           => 'vat',
            'VatIncluded'   => 'vat_inc',
            'VatAmount'     => 'vat_amount',
            'Stock'         => 'stock'
        ];

        /**
         * The constructor.
         *
         * @param int $type The cart item type.
         * @param string $sku The SKU of the cart item.
         * @param string $name The name of the cart item (product name).
         * @param int $quantity
         * @param int $price The price of the cart item.
         * @param string|null $link An optional link to the product.
         *
         * @throws Exception
         * @throws ReflectionException
         * @access public
         * @api
         */
        public function __construct(int $type, string $sku, string $name, int $quantity, int $price, ?string $link)
        {
            $this->setType($type)->setSKU($sku)->setName($name)->setQuantity($quantity)->setPrice($price);
            if (! is_null($link)) {
                $this->setLink($link);
            }
        }

        /**
         * Sets the type (must be one of the {@link Item::TYPE_*}) constants.
         *
         * @param int $type The cart item type to set.
         *
         * @return Item Returns this, makes the call chainable.
         * @throws Exception|ReflectionException
         * @access public
         * @api
         */
        public function setType(int $type): Item
        {
            if (
               ! in_array($type, ( new ReflectionClass('\cardgate\api\Item') )->getConstants())
            ) {
                throw new Exception( 'Item.Type.Invalid', 'invalid cart item type: ' . $type );
            }
            return parent::setType($type);
        }

        /**
         * Sets the vat.
         * @param float $vat The vat to set.
         * @return Item Returns this, makes the call chainable.
         * @throws Exception
         * @access public
         * @api
         */
        public function setVat(float $vat): Item
        {
            if (! is_numeric($vat)) {
                throw new Exception('Item.Vat.Invalid', 'invalid vat: ' . $vat);
            }
            return parent::setVat($vat);
        }

        /**
         * Sets the vat included flag.
         * @param bool $vatIncluded Set the flag vat included.
         * @return Item Returns this, makes the call chainable.
         * @access public
         * @api
         */
        public function setVatIncluded(bool $vatIncluded): Item
        {
            return parent::setVatIncluded(!! $vatIncluded);
        }

        /**
         * Sets the vat amount.
         * @param float $vatAmount The vat amount to set.
         * @return Item Returns this, makes the call chainable.
         * @throws Exception
         * @access public
         * @api
         */
        public function setVatAmount(float $vatAmount): Item
        {
            if (! is_numeric($vatAmount)) {
                throw new Exception('Item.Vat.Amount.Invalid', 'invalid vat amount: ' . $vatAmount);
            }
            return parent::setVatAmount($vatAmount);
        }

        /**
         * Sets the stock.
         * @param float $stock The stock to set.
         * @return Item Returns this, makes the call chainable.
         * @throws Exception
         * @access public
         * @api
         */
        public function setStock(float $stock): Item
        {
            if (! is_numeric($stock)) {
                throw new Exception('Item.Stock.Invalid', 'invalid stock: ' . $stock);
            }
            return parent::setStock($stock);
        }
    }

}
