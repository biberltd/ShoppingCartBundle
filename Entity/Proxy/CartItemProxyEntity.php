<?php

/**
 * @name        CartItemProxyEntity
 * @package	BiberLtd\Core\ShoppingCartBundle
 *
 * @author	Said İmamoğlu
 *
 * @version     1.0.3
 * @date        01.07.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */

namespace BiberLtd\Bundle\ShoppingCartBundle\Entity\Proxy;

use \Doctrine\Common\Collections\ArrayCollection,
    BiberLtd\Bundle\TaxManagementBundle\Entity as TaxEntity,
    \BiberLtd\Bundle\ShoppingCartBundle\Entity as SCBEntity;

class CartItemProxyEntity {


    /**
     *
     * @var type integer
     */
    public $id;

    /**
     *
     * @var type integer
     */
    public $coupon;

    /**
     *
     * @var type integer
     */
    public $discount;
    /**
     *
     * @var type integer
     */
    public $discountedPrice;

    /**
     *
     * @var type object
     */
    public $member;

    /**
     *
     * @var type object
     */
    public $memberGroup;

    /**
     *
     * @var type object
     */
    public $price;

    /**
     *
     * @var type integer
     */
    public $product;

    /**
     *
     * @var type string
     */
    public $productCategories;

    /**
     *
     * @var type integer
     */
    public $quantity;

    /**
     *
     * @var type object
     */
    public $shipping;

    /**
     *
     * @var type string
     */
    public $sku;

    /**
     *
     * @var type object
     */
    public $taxRate;
    /**
     *
     * @var type object
     */
    public $taxAmount;

    /**
     *
     * @var type float
     */
    public $totalAmount;

    /**
     *
     * @var type array
     */
    public $boxType;
    /**
     *
     * @var type array
     */
    public $boxCount;

    public function __construct() {
        $this->quantity = 1;
        $this->totalAmount = 0;
    }

    /**     * **************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     * **************************************************************** */


    /**
     * @name            getBoxType()
     *                  Gets boxType property.
     * .
     * @author          Said İmamoğlu
     * @since		    1.0.1
     * @version         1.0.1
     *
     * @return          string          $this->boxType
     */
    public function getBoxType() {
        return $this->boxType;
    }

    /**
     * @name            getBoxCount()
     *                  Gets boxCount property.
     * .
     * @author          Said İmamoğlu
     * @since		    1.0.3
     * @version         1.0.3
     *
     * @return          string          $this->boxType
     */
    public function getBoxCount() {
        return $this->boxCount;
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
        return floatval($this->discount);
    }
    /**
     * @name            getDiscountedPrice()
     *                  Gets discountedPrice property.
     * .
     * @author          Said İmamoğlu
     * @since		    1.0.4
     * @version         1.0.4
     *
     * @return          string          $this->discount
     */
    public function getDiscountedPrice() {
        return floatval($this->discountedPrice);
    }
    /**
     * @name            getId()
     *                  Gets Id property.
     * .
     * @author          Said İmamoğlu
     * @since		    1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @name            getMember()
     *                  Gets member property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->member
     */
    public function getMember() {
        return $this->member;
    }

    /**
     * @name            getMemberGroup()
     *                  Gets memberGroup property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->memberGroup
     */
    public function getMemberGroup() {
        return $this->memberGroup;
    }

    /**
     * @name            getProduct()
     *                  Gets product property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * @name            getPrice()
     *                  Gets price property.
     * .
     * @author          Said İmamoğlu
     * @since		    1.0.2
     * @version         1.0.2
     *
     * @return          string          $this->price
     */
    public function getPrice() {
        return floatval($this->price);
    }

    /**
     * @name            getProductCategories()
     *                  Gets productCategories property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->productCategories
     */
    public function getProductCategories() {
        $categories = json_decode($this->productCategories);
        return !is_array($categories) ? array() : $categories;
    }

    /**
     * @name            getQuantity()
     *                  Gets quantity property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->quantity
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @name            getShipping()
     *                  Gets shipping property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->shipping
     */
    public function getShipping() {
        return $this->shipping;
    }

    /**
     * @name            getSku()
     *                  Gets sku property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->sku
     */
    public function getSku() {
        return $this->sku;
    }

    /**
     * @name            getTax()
     *                  Gets taxRate property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->tax
     */
    public function getTaxRate() {
        return $this->taxRate;
    }
    /**
     * @name            getTaxAmount()
     *                  Gets taxAmount property.
     * .
     * @author          Said İmamoğlu
     * @since		1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->tax
     */
    public function getTaxAmount() {
        return $this->taxAmount;
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
        return floatval($this->totalAmount);
    }

    /**
     * @name                  setBoxType ()
     * Sets the $boxType property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.1
     * @version         1.0.1
     *
     *
     *
     * @param           mixed $boxType
     *
     * @return          object                $this
     */
    public function setBoxType($boxType) {
        $this->boxType = $boxType;
        return $this;
    }
    /**
     * @name                  setBoxCount ()
     * Sets the boxCount property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.3
     * @version         1.0.3
     *
     *
     *
     * @param           mixed $boxCount
     *
     * @return          object                $this
     */
    public function setBoxCount($boxCount) {
        $this->boxCount = $boxCount;
        return $this;
    }
    /**
     * @name                  setCoupon ()
     * Sets the $coupon property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $coupon
     *
     * @return          object                $this
     */
    public function setCoupon($coupon) {
        $this->coupon = $coupon;
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
     * @param           float $discount
     *
     * @return          object                $this
     */
    public function setDiscount($discount) {
        $this->discount = floatval($discount);
        return $this;
    }
    /**
     * @name                setDiscountedPrice ()
 *                          Sets the discount property.
     *
     * @author              Said İmamoğlu
     *
     * @since               1.0.4
     * @version             1.0.4
     *
     *
     *
     * @param               float $discountedPrice
     *
     * @return              object                $this
     */
    public function setDiscountedPrice($discountedPrice) {
        $this->discountedPrice = floatval($discountedPrice);
        return $this;
    }
    /**
     * @name                  setBoxType ()
     * Sets the type property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     * @return          object                $this
     */
    public function getCoupon() {
        return $this->coupon;
    }
    /**
     * @name                  setId ()
     * Sets the id property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $id
     *
     * @return          object                $this
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @name                  setMember ()
     * Sets the $member property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $member
     *
     * @return          object                $this
     */
    public function setMember($member) {
        $this->member = $member;
        return $this;
    }

    /**
     * @name                  setMemberGroup ()
     * Sets the $memberGroup property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $memberGroup
     *
     * @return          object                $this
     */
    public function setMemberGroup($memberGroup) {
        $this->memberGroup = $memberGroup;
        return $this;
    }

    /**
     * @name                  setPrice ()
     * Sets the $price property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.2
     * @version         1.0.2
     *
     *
     *
     * @param           mixed $price
     *
     * @return          object                $this
     */
    public function setPrice($price) {
        $this->price = floatval($price);
        return $this;
    }
    /**
     * @name                  setProduct ()
     * Sets the $product property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $product
     *
     * @return          object                $this
     */
    public function setProduct($product) {
        $this->product = $product;
        return $this;
    }

    /**
     * @name                  setProductCategories ()
     * Sets the $productCategories property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $productCategories
     *
     * @return          object                $this
     */
    public function setProductCategories($productCategories) {
        $this->productCategories = json_encode($productCategories);
        return $this;
    }

    /**
     * @name                  setPercent ()
     * Sets the $quantity property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $quantity
     *
     * @return          object                $this
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @name                  setShipping ()
     * Sets the $shipping property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $shipping
     *
     * @return          object                $this
     */
    public function setShipping(CartItemShippingProxyEntity $shipping) {
        $this->shipping = $shipping;
        return $this;
    }

    /**
     * @name                  setSku ()
     * Sets the $sku property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           mixed $sku
     *
     * @return          object                $this
     */
    public function setSku($sku) {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @name                  setTaxRate ()
     *                          Sets the $taxRate property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           float $taxRate
     *
     * @return          object                $this
     */
    public function setTaxRate($taxRate) {
        $this->taxRate = $taxRate;
        return $this;
    }
    /**
     * @name                  setTaxAmount ()
     *                          Sets the $taxAmount property.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     *
     *
     * @param           float $taxAmount
     *
     * @return          object                $this
     */
    public function setTaxAmount($taxAmount) {
        $this->taxAmount = $taxAmount;
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
        $this->totalAmount = floatval($totalAmount);
        return $this;
    }

    /**
     * @name updateProductPrice()
     * 
     * @author Said İmamoğlu
     * @version 1.0.0
     * @since   1.0.0
     * 
     * @param float $price 
     * 
     * @uses    $this->setProductPrice()
     * 
     * @return  mixed
     */
    public function updateProductPrice($price) {
        if (is_float($price)) {
            $this->setProductPrice($price);
        }
        return $this;
    }

    /**
     * @name                  calculateTotalTax ()
     * Calculates the total amount.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @uses    $this->getTax()              Amount of item's tax.
     *
     * @return float
     */
    public function calculateTotalTax() {
        (float) $totalTax = 0.00;

        if ($this->getTaxRate() instanceof TaxEntity\TaxRate) {
            if (!is_null($this->getTax()->getRate()) && $this->getProduct()->getPrice() !== null) {
                $totalTax += (($this->getProduct()->getPrice() * $this->getTax()->getRate()) / 100);
            }
        }
        return $totalTax;
    }

    /**
     * @name                  calculateTotalShipping ()
     * Calculates the total amount of shipping.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @uses    $this->getShipping()
     *
     * @return float
     */
    public function calculateTotalShipping() {
        $totalShipping = 0.00;
        if ($this->getShipping() instanceof CartItemShippingProxyEntity) {
            if (!is_null($this->getShipping()->getPrice())) {
                $totalShipping = $this->getShipping()->getPrice();
            }
            if (!is_null($this->getShipping()->getDiscount())) {
                $totalShipping -= $this->getShipping()->getDiscount();
            }
        }
        
        return (float) $totalShipping;
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
     * @uses    $this->getProduct()     
     * @uses    $this->calculateTotalTax()
     * @uses    $this->calculateTotalShipping()
     *
     * @return          object                $this
     */
    public function calculateTotal() {
        $totalAmount = 0;
        if (!is_null($this->getProduct()->getPrice())) {
            $totalAmount += $this->getProduct()->getPrice() + $this->calculateTotalTax() + $this->calculateTotalShipping();
        }
        return $totalAmount;
    }

}

/**
 * Change Log:
 * * * **********************************
 * v1.0.4                      Said İmamoğlu
 * 04.07.2014
 * **************************************
 * A discountedPrice
 * A getDiscountedPrice()
 * A getDiscountedPrice()
 * * * **********************************
 * v1.0.3                      Said İmamoğlu
 * 01.07.2014
 * **************************************
 * A boxCount
 * A getBoxCount()
 * A setBoxCount()
 * * * **********************************
 * v1.0.2                      Said İmamoğlu
 * 14.05.2014
 * **************************************
 * A getPrice()
 * A setPrice()
 * D getProductCategory()
 * D setProductCategory()
 * A getProductCategories()
 * A setProductCategories()
 *
 * * * **********************************
 * v1.0.1                      Said İmamoğlu
 * 30.01.2014
 * **************************************
 * A getMember()
 * A getMemberGroup()
 * A getProduct()
 * A getProductCategory()
 * A getQuantity()
 * A setMember()
 * A setMemberGroup()
 * A setProduct()
 * A setProductCategory()
 * A setQuantity()
 * A calculateTotalTax()
 * A calculateTotalShipping()
 * A calculateTotal()
 * 
 * D getProductName()
 * D getProductPrice()
 * * * **********************************
 * v1.0.1                      Said İmamoğlu
 * 29.01.2014
 * **************************************
 * A getCoupon()
 * A getDiscount()
 * A getProductName()
 * A getProductPrice()
 * A getShipping()
 * A getSku()
 * A getTax()
 * A getTotalAmount()
 * A setCoupon()
 * A setDiscount()
 * A setProductName()
 * A setProductPrice()
 * A setShipping()
 * A setSku()
 * A setTax()
 * A setTotalAmount()
 * A updateProductPrice()
 *
 */