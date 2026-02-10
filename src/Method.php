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

namespace cardgate\api;

use ReflectionClass;

/**
 * Paymentmethod instance.
 */
final class Method
{
    /**
     * iDeal.
     */
    public const IDEAL = 'ideal';

    /**
     * iDeal (legacy).
     */
    public const IDEALPRO = 'idealpro';

    /**
     * BanContact.
     */
    public const BANCONTACT = 'bancontact';

    /**
     * MisterCash (legacy)
     */
    public const MISTERCASH = 'mistercash';

    /**
     * CreditCard.
     */
    public const CREDITCARD = 'creditcard';

    /**
     * Afterpay.
     */
    public const AFTERPAY = 'afterpay';

    /**
     * Giropay.
     */
    public const GIROPAY = 'giropay';

    /**
     * Giropay.
     */
    public const BANKTRANSFER = 'banktransfer';

    /**
     * Bitcoins.
     */
    public const BITCOIN = 'bitcoin';

    /**
     * DirectDebit.
     */
    public const DIRECTDEBIT = 'directdebit';

    /**
     * Klarna.
     */
    public const KLARNA = 'klarna';

    /**
     * PayPal.
     */
    public const PAYPAL = 'paypal';

    /**
     * Przelewy24.
     */
    public const PRZELEWY24 = 'przelewy24';

    /**
     * SofortBanking.
     */
    public const SOFORTBANKING = 'sofortbanking';

    /**
     * Paysafecard
     */
    public const PAYSAFECARD = 'paysafecard';

    /**
     * Billink
     */
    public const BILLINK = 'billink';

    /**
     * IDEALQR
     */
    public const IDEALQR = 'idealqr';

    /**
     * Paysafecash
     */
    public const PAYSAFECASH = 'paysafecash';

    /**
     * OnlineUberweisen
     */
    public const ONLINEUEBERWEISEN = 'onlineueberweisen';

    /**
     * Gift Card
     */
    public const GIFTCARD = 'giftcard';

    /**
     * EPS
     */
    public const EPS = 'eps';

    /**
     * SprayPay
     */
    public const SPRAYPAY = 'spraypay';

    /**
     * Crypto
     */
    public const CRYPTO = 'crypto';

    /**
     * The client associated with this payment method.
     * @var Client
     * @access private
     */
    private $client;

    /**
     * The payment method.
     * @var string
     * @access private
     */
    private $id;

    /**
     * The payment method name.
     * @var string
     * @access private
     */
    private $name;

    /**
     * The constructor.
     *
     * @param Client $client The client associated with this transaction.
     * @param string $id The payment method identifier to create a method instance for.
     * @param string $name The payment method name.
     *
     * @throws Exception
     * @access public
     * @api
     */
    public function __construct(Client $client, string $id, string $name)
    {
        static $validMethods; // use static cache for this

        if (! isset($validMethods)) {
            $validMethods  = (new ReflectionClass('\cardgate\api\Method'))->getConstants();
        }
        $this->client = $client;
        if (! in_array($id, $validMethods)) {
            throw new Exception('Method.PaymentMethod.Invalid', 'invalid payment method: ' . $id);
        }
        $this->setId($id);
        $this->setName($name);
    }

    /**
     * Set the method id.
     *
     * @param string $id Method id to set.
     *
     * @return $this
     * @throws Exception
     * @access public
     * @api
     */
    public function setId(string $id): Method
    {
        if (empty($id)) {
            throw new Exception('Method.Id.Invalid', 'invalid id: ' . $id);
        }
        $this->id = $id;
        return $this;
    }

    /**
     * Get the payment method id.
     * @return string The payment method id for this instance.
     * @access public
     * @api
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the method name.
     *
     * @param string $name Method name to set.
     *
     * @return $this
     * @throws Exception
     * @access public
     * @api
     */
    public function setName(string $name): Method
    {
        if (empty($name)) {
            throw new Exception('Method.Name.Invalid', 'invalid name: ' . $name);
        }
        $this->name = $name;
        return $this;
    }

    /**
     * Get the payment method name.
     * @return string The payment method name for this instance.
     * @access public
     * @api
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * This method returns all the issuers available for the current payment method.
     * @return array An array with issuers
     * @access public
     * @api
     */
    public function getIssuers(): array
    {
        return [ 0 => [ "id" => "ZERO", "name" => "Deprecated"]]; //Deprecated since iDEAL2
    }
}
