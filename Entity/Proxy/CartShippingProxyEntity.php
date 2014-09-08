<?php

/**
 * @name        CartProxyEntity
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

namespace BiberLtd\Bundle\ShoppingCartBundle\Entity\Proxy;

class CartShipmentProxyEntity {

    public $amount;
    public $discount;
    public $totalAmount;

    /*     * ****************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     * **************************************************************** */

    /**
     * @name            getAmount()
     *                  Gets amount property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->amount
     */
    public function getAmount() {
        return $this->amount;
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
     * @name                  setAmount ()
     * Sets the amount property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $amount
     *
     * @return          object                $this
     */
    public function setAmount($amount) {
        $this->amount = $amount;
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

    /**
     * @name                  calculateTotal ()
     * Calculates the total amount.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @uses    $this->productPrice     Price of item.
     * @uses    $this->discount         Amount of item's discount.
     * @uses    $this->shipment         Amount of item's shipment.
     * @uses    $this->tax              Amount of item's tax.
     *
     */
    public function calculateTotal() {
        $totalAmount = 0;
        if (!is_null($this->percent) && !is_null($this->totalAmount)) {
            $totalAmount += (($this->amount) * $this->percent) / 100;
        }
        return $totalAmount;
    }

}

/**
 * Change Log:
 * * * **********************************
 * v1.0.1                      Said İmamoğlu
 * 29.01.2014
 * **************************************
 * A getAmount()
 * A getDiscount()
 * A getTotalAmount()
 * 
 * A setAmount()
 * A setDiscount()
 * A setTotalAmount()
 *
 */