<?php

/**
 * @name        CartProxyEntity
 * @package        BiberLtd\Core\ShoppingCartBundle
 *
 * @author        Said İmamoğlu
 *
 * @version         1.0.1
 * @date        29.01.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */

namespace BiberLtd\Core\Bundles\ShoppingCartBundle\Entity\Proxy;

use Doctrine\Common\Collections\ArrayCollection,
    \BiberLtd\Core\Bundles\ShoppingCartBundle\Entity as SCBEntity,
    \BiberLtd\Core\Bundles\MemberManagementBundle\Entity as MMBEntity;

class CartProxyEntity
{
    /**
     *
     * @var type integer
     */
    public $id;
    /**
     *
     * @var type
     */
    public $cartToOrderDate;

    /**
     *
     * @var type
     */
    public $code;

    /**
     *
     * @var type object
     */
    public $coupons;
    /**
     *
     * @var array object
     */
    public $couponAppliedItems;
    /**
     *
     * @var type decimal/float
     */
    public $date;
    /**
     *
     * @var type decimal/float
     */
    public $discount;

    /**
     *
     * @var type
     */
    public $initializedSession;

    /**
     *
     * @var type
     */
    public $items;

    /**
     *
     * @var type
     */
    public $lastUpdatedDate;

    /**
     *
     * @var type
     */
    public $ownerId;

    /**
     *
     * @var type
     */
    public $quantity;

    /**
     *
     * @var type
     */
    public $shipment;
    /**
     *
     * @var type
     */
    public $saveCookie;

    /**
     *
     * @var type
     */
    public $totalAmount;

    /**
     *
     * @var type
     */
    public $totalDiscount;

    /**
     *
     * @var type
     */
    public $totalTax;

    /**
     *
     * @var type DateTime
     */
    public $dateCreated;

    /**
     *
     * @var type DateTime
     */
    public $dateUpdated; 
    /**
     *
     * @var type DateTime
     */
    public $dateCancelled;


    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

    public function __construct($kernel)
    {
        $this->kernel = $kernel;
        $this->items = new ArrayCollection();
        $this->coupons = new ArrayCollection();
        $this->couponAppliedItems = new ArrayCollection();
        $this->discount = 0.00;
        $this->date = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
    }


    /**
     * @name            getId ()
     *                  Gets Id property.
     * .
     * @author          Said İmamoğlu
     * @since        1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @name            getCartToOrderDate ()
     *                  Gets cartToOrderDate property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          string          $this->cartToOrderDate
     */
    public function getCartToOrderDate()
    {
        return $this->cartToOrderDate;
    }

    /**
     * @name            getCode ()
     *                  Gets code property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          string          $this->code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @name            getCoupons ()
     *                  Gets coupon property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          string          $this->coupon
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * @name            getCouponAppliedItems ()
     *                  Gets couponAppliedItems property.
     * .
     * @author               Said İmamoğlu
     * @since                1.0.0
     * @version             1.0.0
     *
     * @return          string          $this->coupon
     */
    public function getCouponAppliedItems()
    {
        return $this->couponAppliedItems;
    }

    /**
     * @name            getDateCreated ()
     *                  Gets dateCreated property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          string          $this->dateCreated
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @name            getDateUpdated ()
     *                  Gets dateUpdated property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          string          $this->dateUpdated
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }
    /**
     * @name            getDateCancelled ()
     *                  Gets dateCancelled property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          string          $this->dateCancelled
     */
    public function getDateCancelled()
    {
        return $this->dateCancelled;
    }

    /**
     * @name            getDiscount ()
     *                  Gets discount property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          string          $this->discount
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @name            getInitializedSession ()
     *                  Gets initializedSession property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          id          $this->initializedSession
     */
    public function getInitializedSession()
    {
        return $this->initializedSession;
    }

    /**
     * @name            getItems ()
     *                  Gets items property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          array          $this->items
     */
    public function getItems($assArray = false)
    {
        if ($assArray) {
            $newCollection = array();
            foreach ($this->items as $item) {
                $newCollection[] = $item;
            }
            return $newCollection;
        }
        return $this->items;
    }

    /**
     * @name            getTotalQuantity ()
     *                  Gets items property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          id          $this->items
     */
    public function getQuantity()
    {
        $this->quantity = $this->calculateTotalQuantity();
        return $this->quantity;
    }

    /**
     * @name            calculateTotalQuantity ()
     *                  Calculates total quantity of items.
     * .
     * @author               Said İmamoğlu
     * @since                1.0.0
     * @version             1.0.0
     *
     * @return          id          $this->items
     */
    public function calculateTotalQuantity()
    {
        $quantity = 0;
        foreach ($this->items as $item) {
            $quantity += $item->getQuantity();
        }
        return $quantity;
    }

    /**
     * @name            calculateTotalQuantity ()
     *                  Calculates total amount of items.
     * .
     * @author               Said İmamoğlu
     * @since                1.0.0
     * @version             1.0.0
     *
     * @return          id          $this->items
     */
    public function calculateTotalAmount()
    {
        $amount = 0;
        foreach ($this->items as $item) {
            $amount += $item->getTotalAmount();
        }
        return $amount;
    }

    /**
     * @name            calculateTotalTax ()
     *                  Calculates total tax of items.
     * .
     * @author               Said İmamoğlu
     * @since                1.0.0
     * @version             1.0.0
     *
     * @return          id          $tax
     */
    public function calculateTotalTax()
    {
        $tax = 0;
        foreach ($this->items as $item) {
            $tax += ($item->getTaxRate() * $item->getPrice() / 100);
        }
        return $tax;
    }
    /**
     * @name            calculateTotalDiscount ()
     *                  Calculates total discount of items.
     * .
     * @author               Said İmamoğlu
     * @since                1.0.0
     * @version             1.0.0
     *
     * @return          id          $tax
     */
    public function calculateTotalDiscount()
    {
        $amount = 0;
        foreach ($this->items as $item) {
            $amount += $item->getDiscount();
        }
        return $amount;
    }

    /**
     * @name            getLastUpdatedDate ()
     *                  Gets lastUpdatedDate property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          string          $this->lastUpdatedDate
     */
    public function getLastUpdatedDate()
    {
        return $this->lastUpdatedDate;
    }

    /**
     * @name            getOwnerId ()
     *                  Gets ownerId property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          integer          $this->ownerId
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * @name            getShipment ()
     *                  Gets shipment property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          object          $this->shipment
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * @name            getSaveCookie ()
     *                  Gets saveCookie property.
     * .
     * @author               Said İmamoğlu
     * @since                1.0.2
     * @version             1.0.2
     *
     * @return          object          $this->saveCookie
     */
    public function getSaveCookie()
    {
        return $this->saveCookie;
    }

    /**
     * @name            getTotalAmount ()
     *                  Gets totalAmount property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          integer          $this->totalAmount
     */
    public function getTotalAmount()
    {
        return floatval($this->calculateTotalAmount());
    }

    /**
     * @name            getTotalDiscount ()
     *                  Gets totalDiscountAmount property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          integer          $this->totalDiscountAmount
     */
    public function getTotalDiscount()
    {
        return floatval($this->totalDiscount);
    }

    /**
     * @name            getTotalTax ()
     *                  Gets totalTaxAmount property.
     * .
     * @author               Said İmamoğlu
     * @since        1.0.0
     * @version             1.0.0
     *
     * @return          string          $this->totalTaxAmount
     */
    public function getTotalTax()
    {
        return floatval($this->calculateTotalTax());
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
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @name                  setCartToOrderDate ()
     * Sets the code property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $cartToOrderDate
     *
     * @return          object                $this
     */
    public function setCartToOrderDate($cartToOrderDate)
    {
        $this->cartToOrderDate = $cartToOrderDate;
        return $this;
    }

    /**
     * @name                  setCode ()
     * Sets the code property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $code
     *
     * @return          object                $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @name                  setCoupons ()
     * Sets the $coupons property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $coupons
     *
     * @return          object                $this
     */
    public function setCoupons($coupons)
    {
        $this->coupons = $coupons;
        return $this;
    }

    /**
     * @name                  setCouponAppliedItems ()
     * Sets the $coupons property.
     *
     * @author              Said İmamoğlu
     *
     * @since               1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $couponAppliedItems
     *
     * @return          object                $this
     */
    public function setCouponAppliedItems($couponAppliedItems)
    {
        $this->couponAppliedItems = $couponAppliedItems;
        return $this;
    }

    /**
     * @name                  setDateCreated ()
     * Sets the $dateCreated property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $dateCreated
     *
     * @return          object                $this
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @name                  setDateUpdated ()
     * Sets the $dateUpdated property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $dateUpdated
     *
     * @return          object                $this
     */
    public function setDateUpdated($dateUpdated)
    {
        if (is_null($dateUpdated)) {
            $dateUpdated = $this->date;
        }
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @name                  setDateCancelled ()
     * Sets the $dateCancelled property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $dateCancelled
     *
     * @return          object                $this
     */
    public function setDateCancelled($dateCancelled)
    {
        if (is_null($dateCancelled)) {
            $dateCancelled = $this->date;
        }
        $this->dateCancelled = $dateCancelled;
    }

    /**
     * @name                  setDiscount ()
     * Sets the $discount property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $discount
     *
     * @return          object                $this
     */
    public function setDiscount($discount)
    {
        $this->discount = floatval($discount);
        return $this;
    }

    /**
     * @name                  setInitializedSession ()
     * Sets the initializedSession property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $initalizedSession
     *
     * @return          object                $this
     */
    public function setInitializedSession($initalizedSession)
    {
        $this->initializedSession = $initalizedSession;
        return $this;
    }

    /**
     * @name                  setITems ()
     * Sets the items property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $items
     *
     * @return          object                $this
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @name                  setLastUpdatedDate ()
     * Sets the lastUpdatedDate property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $lastUpdatedDate
     *
     * @return          object                $this
     */
    public function setLastUpdatedDate($lastUpdatedDate)
    {
        $this->lastUpdatedDate = $lastUpdatedDate;
        return $this;
    }

    /**
     * @name                  setOwnerId ()
     * Sets the ownerId property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $ownerId
     *
     * @return          object                $this
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        return $this;
    }

    /**
     * @name                  setShipment ()
     * Sets the $shipment property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $shipment
     *
     * @return          object                $this
     */
    public function setShipment($shipment)
    {
        $this->shipment = $shipment;
        return $this;
    }

    /**
     * @name                  setQuantity ()
     * Sets the $quantity property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $quantity
     *
     * @return          object                $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @name                  setSaveCookie ()
     *                          Sets the $saveCookie property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.2
     * @version             1.0.2
     *
     *
     *
     * @param           mixed $saveCookie
     *
     * @return          object                $this
     */
    public function setSaveCookie($saveCookie)
    {
        $this->saveCookie = $saveCookie;
        return $this;
    }

    /**
     * @name                  setTotalAmount ()
     * Sets the $totalAmount property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $totalAmount
     *
     * @return          object                $this
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = floatval($totalAmount);
        return $this;
    }

    /**
     * @name                  setTotalDiscount ()
     * Sets the $totalDiscount property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $totalDiscount
     *
     * @return          object                $this
     */
    public function setTotalDiscount($totalDiscount)
    {
        $this->totalDiscount = floatval($totalDiscount);
        return $this;
    }

    /**
     * @name                  setTotalTax ()
     * Sets the $totalTax property.
     *
     * @author               Said İmamoğlu
     *
     * @since                 1.0.0
     * @version             1.0.0
     *
     *
     *
     * @param           mixed $totalTax
     *
     * @return          object                $this
     */
    public function setTotalTax($totalTax)
    {
        $this->totalTax = $totalTax;
        return $this;
    }

    /**
     * @name    addItem ()
     *
     * @author       Said Imamoglu
     * @since         1.0.0
     * @version     1.0.0
     *
     * @param   CartItemProxyEntity $item item to be added
     *
     * @uses    $this->hasItem()
     * @uses    $this->mergeItem()
     *
     * @return          object                $this
     */
    public function addItem(CartItemProxyEntity $item)
    {
        if (!$this->hasItem($item)) {
            $this->setDateUpdated($this->date);
            $this->items->add($item);
        } else {
            $this->mergeItem($item);
        }
        return $this;
    }

    /**
     * @name $this ->hasItem()
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param CartItemProxyEntity $item
     *
     * @return mixed
     *
     */
    public function hasItem(CartItemProxyEntity $item)
    {
        if ($this->items->contains($item)) {
            return true;
        }
        return false;
    }

    /**
     * @name    $this ->merge()
     *
     * @author       Said İmamoğlu
     * @version     1.0.0
     * @since         1.0.0
     *
     *
     * @return mixed $this
     *
     */
    public function mergeItem()
    {
        $this->quantity += 1;
    }

    /**
     * @name applyCoupon ()
     * Applies one coupon.
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param SCBEntity\Coupon $coupon
     *
     * @uses $this->applyCoupon()
     *
     * @return boolean
     *
     */
    public function applyCoupon(SCBEntity\Coupon $coupon)
    {
        return $this->applyCoupons(array($coupon));
    }

    /**
     * @name applyCoupons ()
     * Applies one or more coupons.
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     *
     * @uses $this->hasCoupon()
     * @uses $this->updateProductPrice()
     *
     * @return boolean
     *
     */
    public function applyCoupons()
    {
        $totalDiscount = 0;
        foreach ($this->getCoupons() as $coupon) {
            if (!$coupon instanceof SCBEntity\Coupon) {
                return array('err' => 'This is not a Coupon entity.');
            }
            if (!$this->hasCoupon($coupon)) {
                return array('err' => 'Coupon can not found coupons array. Please add this coupon first.');
            }
            if (!$this->checkMemberCanUseCoupon($coupon)) {
                return array('err', 'Only specified members can use this coupon');
            }
            if (!$this->checkMemberGroupCanUseCoupon($coupon)) {
                return array('err', 'Only specified member groups can use this coupon');
            }
            if ($coupon->getCountRedeemed() >= $coupon->getLimitRedeem()) {
                return array('err' => 'This coupon cant be used more them MaxRedeem');
            }
            if (!$this->checkCouponExpired($coupon)) {
                return array('err' => 'This coupon is expired at');
            }
            if (!$this->checkCouponPublished($coupon)) {
                return array('err' => 'This coupon is not published yet. ');
            }
            foreach ($this->getItems() as $item) {
                if (!$this->checkCouponType($coupon, $item)) {
                    return array('err' => 'This coupon can not be used for multiple items.');
                }
                if (!$this->checkProductCanUseCoupon($coupon, $item)) {
                    return array('err', 'If coupon\'s product defined, only this product can used in this coupon');
                }
                if (!$this->checkProductCategoryCanUseCoupon($coupon, $item)) {
                    return array('err', 'If coupon\'s product defined, only this products in this category can used in this coupon');
                }
            }

            //applying coupon
            if ($coupon->getType() == 'a') {
                $totalDiscount += $coupon->getDiscount();
            }
        }

        return $totalDiscount;
    }

    /**
     * @name checkCouponMember ()
     * If coupon's member defined, only this member can use this coupon
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param SCBEntity\Coupon $coupon
     *
     * @uses $this->addCoupons()
     *
     * @return boolean
     *
     */
    public function checkMemberCanUseCoupon(SCBEntity\Coupon $coupon)
    {
        if (null !== $coupon->getMember() && null !== $this->getMember()) {
            if (!in_array($this->getMember()->getId(), $coupon->getMember())) {
                return false;
            }
        }
        return true;
    }

    /**
     * @name checkCouponMember ()
     * If coupon's member defined, only this member can use this coupon
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param SCBEntity\Coupon $coupon Coupon item
     *
     * @uses $this->getMemberGroup()
     *
     * @return boolean
     *
     */
    public function checkMemberGroupCanUseCoupon(SCBEntity\Coupon $coupon)
    {
        if (null !== $coupon->getMemberGroup() && null !== $this->getMemberGroup()) {
            if ($this->getMemberGroup()->getId() !== $coupon->getMemberGroup()->getId()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @name checkCouponProduct ()
     * If coupon's product defined, only this product can used in this coupon
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param SCBEntity\Coupon $coupon Coupon item
     * @param SCBEntity\ShoppingCartItem $cartItem Coupon item
     *
     * @uses $this->getProduct()
     *
     * @return boolean
     *
     */
    public function checkProductCanUseCoupon(SCBEntity\Coupon $coupon, SCBEntity\ShoppingCartItem $cartItem)
    {
        if ((null !== $coupon->getProduct()) && (null !== $cartItem->getProduct())) {
            if ($cartItem->getProduct()->getId() !== $coupon->getProduct()->getId()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @name checkCouponProductCategory ()
     * If coupon's product defined, only this products in this category can used in this coupon
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param SCBEntity\Coupon $coupon Coupon item
     * @param SCBEntity\ShoppingCartItem $cartItem Coupon item
     *
     * @uses $this->getProductCategory()
     *
     * @return boolean
     *
     */
    public function checkProductCategoryCanUseCoupon(SCBEntity\Coupon $coupon, SCBEntity\ShoppingCartItem $cartItem)
    {
        if ((null !== $coupon->getProductCategory()) && (null !== $cartItem->getProductCategory())) {
            if ($cartItem->getProductCategory()->getId() !== $coupon->getProductCategory()->getId()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @name checkCouponType ()
     * If coupon's type is single usage then coupon can not be used for multiple cart items.
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param SCBEntity\Coupon $coupon Coupon item
     * @param  SCBEntity\ShoppingCartItem $cartItem Coupon item
     *
     * @uses $this->getProduct()
     *
     * @return boolean
     *
     */
    public function checkCouponType(SCBEntity\Coupon $coupon, SCBEntity\ShoppingCartItem $cartItem)
    {
        if ($coupon->getTypeUsage() == 's') {
            if ($coupon->getQuantity() > 1) {
                return false;
            }
        }
        return true;
    }

    /**
     * @name checkCouponProductCategory ()
     * If coupon's product defined, only this products in this category can used in this coupon
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param SCBEntity\Coupon $coupon $coupon Coupon item
     *
     * @uses $this->getProduct()
     *
     * @return boolean
     *
     */
    public function checkCouponExpired(SCBEntity\Coupon $coupon)
    {
        $now = (new \DateTime("now"));
        $date = $now->format('d.m.Y h:i:s');
        if ((strtotime($coupon->getDateUnpublished()->format('d.m.Y h:i:s')) - strtotime($date) <= 0)) {
            return false;
        }
        return true;
    }

    /**
     * @name checkCouponPublished ()
     * If coupon's product defined, only this products in this category can used in this coupon
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param SCBEntity\Coupon $coupon $coupon Coupon item
     *
     * @uses DateTime
     *
     * @return boolean
     *
     */
    public function checkCouponPublished(SCBEntity\Coupon $coupon)
    {
        $now = (new \DateTime("now"));
        $date = $now->format('d.m.Y h:i:s');
        if ((strtotime($coupon->getDatePublished()->format('d.m.Y h:i:s')) - strtotime($date) > 0)) {
            return false;
        }
        return true;
    }

    /**
     * @name addCoupon ()
     * Adds one coupon
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param SCBEntity\Coupon $coupon Coupon item
     *
     * @uses $this->addCoupons()
     *
     * @return array()
     *
     */
    public function addCoupon($coupon)
    {
        return $this->addCoupons(array($coupon));
    }

    /**
     * @name addCoupons ()
     * Adds one or more coupons.
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param array $coupons Coupon item
     *
     * @uses $this->hasCoupon()
     * @uses $ArrayCollection::contains()
     *
     * @return mixed $this
     *
     */
    public function addCoupons(array $coupons)
    {
        $this->setDateUpdated($this->date);
        foreach ($coupons as $coupon) {
            if (!$this->hasCoupon($coupon)) {
                $this->getCoupons()->add($coupon);
            } else {
                $this->updateCoupon($coupon);
            }
        }
        return $this;
    }

    /**
     * @name updateCoupon ()
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param SCBEntity\Coupon $coupon Coupon item
     *
     * @uses $this->hasCoupon()
     * @uses $ArrayCollection::contains()
     *
     * @return mixed $this
     *
     */
    public function updateCoupon(SCBEntity\Coupon $coupon)
    {
        return $this->removeCoupon($coupon)->addCoupon($coupon);
    }

    /**
     * @name hasCoupon ()
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param  $coupon
     *
     * @uses $ArrayCollection::contains()
     *
     * @return mixed $this
     *
     */
    function hasCoupon($coupon)
    {
        if ($this->coupons->contains($coupon)) {
            return true;
        }
        return false;
    }

    /**
     * @name removeCoupon ()
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param object $coupon Coupon item
     *
     * @uses $ArrayCollection::contains()
     *
     * @return mixed $this
     *
     */
    function removeCoupon($coupon)
    {
        $this->coupons->removeElement($coupon);
        return $this;
    }

    /**
     * @name addItemToCouponAppliedItem ()
     * Adds one item to collection of items
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param  $item CouponAppliedItems item
     *
     * @uses $this->addCouponAppliedItems()
     *
     * @return array()
     *
     */
    public function addItemToCouponAppliedItem($item)
    {
        return $this->addItemToCouponAppliedItems(array($item));
    }

    /**
     * @name addItemToCouponAppliedItems ()
     * Adds one or more coupons.
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param array $items CouponAppliedItems item
     *
     * @uses $this->hasCouponAppliedItems()
     * @uses $ArrayCollection::contains()
     *
     * @return mixed $this
     *
     */
    public function addItemToCouponAppliedItems(array $items)
    {
        foreach ($items as $item) {
            if (!$this->hasCouponAppliedItems($item)) {
                $this->getCouponAppliedItems()->add($item);
            } else {
                $this->updateCouponAppliedItems($item);
            }
        }
        return $this;
    }

    /**
     * @name updateCouponAppliedItems ()
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param SCBEntity\CouponAppliedItems $item CouponAppliedItems item
     *
     * @uses $this->hasCouponAppliedItems()
     * @uses $ArrayCollection::contains()
     *
     * @return mixed $this
     *
     */
    public function updateCouponAppliedItems($item)
    {
        return $this->removeItemFromCouponAppliedItems($item)->addItemToCouponAppliedItems($item);
    }

    /**
     * @name hasCouponAppliedItems ()
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param $item
     *
     * @uses $ArrayCollection::contains()
     *
     * @return mixed $this
     *
     */
    function hasCouponAppliedItems($item)
    {
        if ($this->coupons->contains($item)) {
            return true;
        }
        return false;
    }

    /**
     * @name removeCouponAppliedItems ()
     *
     * @author      Said İmamoğlu
     * @version     1.0.0
     * @since       1.0.0
     *
     * @param object $item CouponAppliedItems item
     *
     * @uses $ArrayCollection::contains()
     *
     * @return mixed $this
     *
     */
    function removeItemFromCouponAppliedItems($item)
    {
        $this->coupons->removeElement($item);
        return $this;
    }

}

/**
 * Change Log:
 * * * **********************************
 * v1.0.2                      Said İmamoğlu
 * 20.06.2014
 * **************************************
 * A saveCookie()
 * * * **********************************
 * v1.0.1                      Said İmamoğlu
 * 31.01.2014
 * **************************************
 * A addItem()
 * A hasItem()
 * A mergeItem()
 * A addCoupon()
 * A addCoupons()
 * A hasCoupon()
 * A removeCoupon()
 * A updateCoupon()
 * A applyCoupon()
 * A applyCoupons()
 * A checkCouponExpired()
 * A checkCouponPublished()
 * A checkCouponType()
 * A checkMemberCanUseCoupon()
 * A checkMemberGroupCanUseCoupon()
 * A checkProductCanUseCoupon()
 * A checkProductCategoryUseCoupon()
 *
 *
 * * * **********************************
 * v1.0.1                      Said İmamoğlu
 * 29.01.2014
 * **************************************
 * A getCartToOrderDate()
 * A getCode()
 * A getCoupon()
 * A getCratedDate()
 * A getInitializedSession()
 * A getLastUpdatedDate()
 * A getOwnerId()
 * A getShipment()
 * A getTotalAmount()
 * A getTotalDiscount()
 * A getTotalTax()
 * A setCartToOrderDate()
 * A setCode()
 * A setCoupon()
 * A setCratedDate()
 * A setInitializedSession()
 * A setLastUpdatedDate()
 * A setOwnerId()
 * A setShipment()
 * A setTotalAmount()
 * A setTotalDiscount()
 * A setTotalTax()
 *
 *
 */