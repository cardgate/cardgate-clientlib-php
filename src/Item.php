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
namespace cardgate\api {

	/**
	 * Item instance.
	 *
	 * @method Item setSKU( string $sSKU_ ) Sets the sku.
	 * @method string getSKU() Returns the sku.
	 * @method boolean hasSKU() Checks for existence of sku.
	 * @method Address unsetSKU() Unsets the sku.
	 *
	 * @method Item setName( string $sName_ ) Sets the name.
	 * @method string getName() Returns the name.
	 * @method boolean hasName() Checks for existence of name.
	 * @method Address unsetName() Unsets the name.
	 *
	 * @method Item setLink( string $sLink_ ) Sets the link.
	 * @method string getLink() Returns the link.
	 * @method boolean hasLink() Checks for existence of link.
	 * @method Address unsetLink() Unsets the link.
	 *
	 * @method Item setQuantity( string $sQuantity_ ) Sets the quantity.
	 * @method string getQuantity() Returns the quantity.
	 * @method boolean hasQuantity() Checks for existence of quantity.
	 * @method Address unsetQuantity() Unsets the quantity.
	 *
	 * @method Item setPrice( string $sPrice_ ) Sets the price.
	 * @method string getPrice() Returns the price.
	 * @method boolean hasPrice() Checks for existence of price.
	 * @method Address unsetPrice() Unsets the price.
	 *
	 * @method string getType() Returns the type.
	 * @method boolean hasType() Checks for existence of type.
	 * @method Address unsetType() Unsets the type.
	 */
	final class Item extends Entity {

		/**
		 * Product.
		 */
		const TYPE_PRODUCT = 1;

		/**
		 * Shipping Costs
		 */
		const TYPE_SHIPPING = 2;

		/**
		 * Payment Costs
		 */
		const TYPE_PAYMENT = 3;

		/**
		 * Discount
		 */
		const TYPE_DISCOUNT = 4;

		/**
		 * Handling fees
		 */
		const TYPE_HANDLING = 5;

		/**
		 * @ignore
		 * @internal The methods these fields expose are configured in the class phpdoc.
		 */
		static $_aFields = [
			'SKU'			=> 'sku',
			'Name'			=> 'name',
			'Link'			=> 'link',
			'Quantity'		=> 'quantity',
			'Price'			=> 'price',
			'Type'			=> 'type',
			'Vat'			=> 'vat',
			'VatIncluded'	=> 'vat_inc',
			'VatAmount'		=> 'vat_amount',
		];

		/**
		 * The constructor.
		 * @param integer $iType_ The cart item type.
		 * @param string $sSKU_ The SKU of the cart item.
		 * @param string $sName_ The name of the cart item (productname).
		 * @param string $iPrice_ The price of the cart item.
		 * @param string $sLink_ An optional link to the product.
		 * @return Item
		 * @throws Exception
		 * @access public
		 * @api
		 */
		function __construct( $iType_, $sSKU_, $sName_, $iQuantity_, $iPrice_, $sLink_ = NULL ) {
			$this->setType( $iType_ )->setSKU( $sSKU_ )->setName( $sName_ )->setQuantity( $iQuantity_ )->setPrice( $iPrice_ );
			if ( ! is_null( $sLink_ ) ) {
				$this->setLink( $sLink_ );
			}
		}

		/**
		 * Set's the type (must be one of the {@link \cardgate\api\Item::TYPE_*}} constants.
		 * @param integer $iType_ The cart item type to set.
		 * @return Item Returns this, makes the call chainable.
		 * @throws Exception
		 * @access public
		 * @api
		 */
		function setType( $iType_ ) {
			if (
				! is_integer( $iType_ )
				|| ! in_array( $iType_, ( new \ReflectionClass( '\cardgate\api\Item' ) )->getConstants() )
			) {
				throw new Exception( 'Item.Type.Invalid', 'invalid cart item type: ' . $iType_ );
			}
			return parent::setType( $iType_ );
		}

	}

}
