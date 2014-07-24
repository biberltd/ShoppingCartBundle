<?php

/**
 * @name        CartItemShippingProxyEntity
 * @package	BiberLtd\Core\ShoppingCartBundle
 *
 * @author	Said İmamoğlu
 *
 * @version     1.0.1
 * @date        29.01.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */

namespace BiberLtd\Core\Bundles\ShoppingCartBundle\Entity\Proxy;

class CartItemShippingProxyEntity {

    public $price;
    public $discount;
    public $totalAmount;

    /* ****************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     * ****************************************************************/
    public function __construct() {
        (float) $this->price=0.00;
        (float) $this->discount=0.00;
        (float) $this->totalAmount=0.00;
    }
    /**
     * @name            getPrice()
     *                  Gets amount property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->price
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @name            getDiscount()
     *                  Gets discount property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->discount
     */
    public function getDiscount() {
        return $this->discount;
    }

    /**
     * @name            getTotalAmount()
     *                  Gets totalAmount property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->totalAmount
     */
    public function getTotalAmount() {
        return $this->totalAmount;
    }

    /**
     * @name                  setPrice ()
     * Sets the amount property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $price
     *
     * @return          object                $this
     */
    public function setPrice($price) {
        $this->price = $price;
        return $this;
    }

    /**
     * @name                  setDiscount ()
     * Sets the discount property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $discount
     *
     * @return          object                $this
     */
    public function setDiscount($discount) {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @name                  setTotalAmount ()
     * Sets the totalAmount property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $totalAmount
     *
     * @return          object                $this
     */
    public function setTotalAmount($totalAmount) {
        $this->totalAmount = $totalAmount;
        return $this;
    }

}


/**
 * Change Log:
 * * * **********************************
 * v1.0.1                      Said İmamoğlu
 * 29.01.2014
 * **************************************
 * A getPrice()
 * A getDiscount()
 * A getTotalAmount()
 * 
 * A setPrice()
 * A setDiscount()
 * A setTotalAmount()
 *
 */