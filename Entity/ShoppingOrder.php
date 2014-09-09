<?php
/**
 * @name        ShoppingOrder
 * @package		BiberLtd\Bundle\CoreBundle\ShoppingCartBundle
 *
 * @author      Can Berkol
 * @author		Murat Ünal
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
namespace BiberLtd\Bundle\ShoppingCartBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="shopping_order",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_shopping_order_date_created", columns={"date_created"}),
 *         @ORM\Index(name="idx_n_shopping_order_date_updated", columns={"date_updated"}),
 *         @ORM\Index(name="idx_n_shopping_order_date_purchased", columns={"date_purchased"}),
 *         @ORM\Index(name="idx_n_shopping_order_date_cancelled", columns={"date_cancelled"}),
 *         @ORM\Index(name="idx_n_shopping_order_date_returned", columns={"date_returned"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_u_shopping_order_id", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idx_u_shopping_order_number", columns={"order_number"})
 *     }
 * )
 */
class ShoppingOrder extends CoreEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=15)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_created;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_purchased;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_cancelled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_returned;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $count_items;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false)
     */
    private $subtotal;

    /**
     * @ORM\Column(type="decimal", length=7, nullable=false)
     */
    private $total_amount;

    /**
     * @ORM\Column(type="decimal", nullable=false)
     */
    private $total_shipment;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false)
     */
    private $total_tax;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false)
     */
    private $total_discount;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $billing;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $shipping;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $instructions;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $flag;

    /**
     * @ORM\Column(type="integer", unique=true, length=20, nullable=false)
     */
    private $order_number;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="purchaser", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $purchaser;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrderStatus")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=false)
     */
    private $order_status;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingCart")
     * @ORM\JoinColumn(name="cart", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $shopping_cart;

    /**
     * 
     * 
     */
    private $cart;

    /**
     * 
     * 
     */
    private $status;

    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

    /**
     * @name            getId()
     *  				Gets $id property.
     * .
     * @author          Murat Ünal
     * @since			1.0.0
     * @version         1.0.0
     *
     * @return          string          $this->id
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @name            setOrderNumber ()
     *                  Sets the order_number property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.3
     * @version         1.0.3
     *
     * @use             $this->setModified()
     *
     * @param           mixed                   $order_number
     *
     * @return          object                  $this
     */
    public function setOrderNumber($order_number) {
        if($this->setModified('order_number', $order_number)->isModified()) {
            $this->order_number = $order_number;
        }

        return $this;
    }

    /**
     * @name            getOrderNumber ()
     *                  Returns the value of order_number property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.3
     * @version         1.0.3
     *
     * @return          mixed           $this->order_number
     */
    public function getOrderNumber() {
        return $this->order_number;
    }

    /**
     * @name                  setBilling ()
     *                                   Sets the billing property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $billing
     *
     * @return          object                $this
     */
    public function setBilling($billing) {
        if(!$this->setModified('billing', $billing)->isModified()) {
            return $this;
        }
		$this->billing = $billing;
		return $this;
    }

    /**
     * @name            getBilling ()
     *                             Returns the value of billing property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->billing
     */
    public function getBilling() {
        return $this->billing;
    }

    /**
     * @name                  setCountItems()
     *                                 Sets the count_items property.
     *                                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_items
     *
     * @return          object                $this
     */
    public function setCountItems($count_items) {
        if(!$this->setModified('count_items', $count_items)->isModified()) {
            return $this;
        }
		$this->count_items = $count_items;
		return $this;
    }

    /**
     * @name            getCountItems()
     *                           Returns the value of count_items property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_items
     */
    public function getCountItems() {
        return $this->count_items;
    }

    /**
     * @name                  setDateCancelled ()
     *                                         Sets the date_cancelled property.
     *                                         Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_cancelled
     *
     * @return          object                $this
     */
    public function setDateCancelled($date_cancelled) {
        if(!$this->setModified('date_cancelled', $date_cancelled)->isModified()) {
            return $this;
        }
		$this->date_cancelled = $date_cancelled;
		return $this;
    }

    /**
     * @name            getDateCancelled ()
     *                                   Returns the value of date_cancelled property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_cancelled
     */
    public function getDateCancelled() {
        return $this->date_cancelled;
    }

    /**
     * @name                  setDateCreated ()
     *                                       Sets the date_created property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_created
     *
     * @return          object                $this
     */
    public function setDateCreated($date_created) {
        if(!$this->setModified('date_created', $date_created)->isModified()) {
            return $this;
        }
		$this->date_created = $date_created;
		return $this;
    }

    /**
     * @name            getDateCreated ()
     *                                 Returns the value of date_created property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_created
     */
    public function getDateCreated() {
        return $this->date_created;
    }

    /**
     * @name                  setDatePurchased ()
     *                                         Sets the date_purchased property.
     *                                         Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_purchased
     *
     * @return          object                $this
     */
    public function setDatePurchased($date_purchased) {
        if(!$this->setModified('date_purchased', $date_purchased)->isModified()) {
            return $this;
        }
		$this->date_purchased = $date_purchased;
		return $this;
    }

    /**
     * @name            getDatePurchased ()
     *                                   Returns the value of date_purchased property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_purchased
     */
    public function getDatePurchased() {
        return $this->date_purchased;
    }

    /**
     * @name                  setDateReturned ()
     *                                        Sets the date_returned property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_returned
     *
     * @return          object                $this
     */
    public function setDateReturned($date_returned) {
        if(!$this->setModified('date_returned', $date_returned)->isModified()) {
            return $this;
        }
		$this->date_returned = $date_returned;
		return $this;
    }

    /**
     * @name            getDateReturned ()
     *                                  Returns the value of date_returned property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_returned
     */
    public function getDateReturned() {
        return $this->date_returned;
    }

    /**
     * @name                  setFlag ()
     *                                Sets the flag property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $flag
     *
     * @return          object                $this
     */
    public function setFlag($flag) {
        if(!$this->setModified('flag', $flag)->isModified()) {
            return $this;
        }
		$this->flag = $flag;
		return $this;
    }

    /**
     * @name            getFlag ()
     *                          Returns the value of flag property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->flag
     */
    public function getFlag() {
        return $this->flag;
    }

    /**
     * @name                  set İnstructions()
     *                            Sets the instructions property.
     *                            Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $instructions
     *
     * @return          object                $this
     */
    public function setInstructions($instructions) {
        if(!$this->setModified('instructions', $instructions)->isModified()) {
            return $this;
        }
		$this->instructions = $instructions;
		return $this;
    }

    /**
     * @name            get İnstructions()
     *                      Returns the value of instructions property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->instructions
     */
    public function getInstructions() {
        return $this->instructions;
    }

    /**
     * @name           setPurchaser()
     *                 Sets the member property.
     *                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $member
     *
     * @return          object                $this
     */
    public function setPurchaser($member) {
        if(!$this->setModified('purchaser', $member)->isModified()) {
            return $this;
        }
		$this->purchaser = $member;
		return $this;
    }

    /**
     * @name            getMember ()
     *                  Returns the value of member property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->member
     */
    public function getPurchaser() {
        return $this->purchaser;
    }

    /**
     * @name                  setShipping ()
     *                                    Sets the shipping property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $shipping
     *
     * @return          object                $this
     */
    public function setShipping($shipping) {
        if(!$this->setModified('shipping', $shipping)->isModified()) {
            return $this;
        }
		$this->shipping = $shipping;
		return $this;
    }

    /**
     * @name            getShipping ()
     *                              Returns the value of shipping property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->shipping
     */
    public function getShipping() {
        return $this->shipping;
    }

    /**
     * @name                  setCart ()
     *                                        Sets the cart property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $cart
     *
     * @return          object                $this
     */
    public function setCart($cart) {
        if(!$this->setModified('cart', $cart)->isModified()) {
            return $this;
        }
		$this->cart = $cart;
		return $this;
    }

    /**
     * @name            getCart ()
     *                                  Returns the value of cart property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->cart
     */
    public function getCart() {
        return $this->cart;
    }

    /**
     * @name            setShoppingOrderStatus ()
     *                  Sets the status property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $status
     *
     * @return          object                $this
     */
    public function setStatus($status) {
        if(!$this->setModified('status', $status)->isModified()) {
            return $this;
        }
		$this->status = $status;
		return $this;
    }

    /**
     * @name            getShoppingOrderStatus ()
     *                  Returns the value of status property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @name                  setTotalAmount ()
     *                                       Sets the total_amount property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $total_amount
     *
     * @return          object                $this
     */
    public function setTotalAmount($total_amount) {
        if(!$this->setModified('total_amount', $total_amount)->isModified()) {
            return $this;
        }
		$this->total_amount = $total_amount;
		return $this;
    }

    /**
     * @name            getTotalAmount ()
     *                                 Returns the value of total_amount property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->total_amount
     */
    public function getTotalAmount() {
        return $this->total_amount;
    }

    /**
     * @name                  setSubtotal ()
     *                                    Sets the subtotal property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $subtotal
     *
     * @return          object                $this
     */
    public function setSubtotal($subtotal) {
        if($this->setModified('subtotal', $subtotal)->isModified()) {
            $this->subtotal = $subtotal;
        }

        return $this;
    }

    /**
     * @name            getSubtotal ()
     *                              Returns the value of subtotal property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->subtotal
     */
    public function getSubtotal() {
        return $this->subtotal;
    }

    /**
     * @name                  setTotalDiscount ()
     *                                         Sets the total_discount property.
     *                                         Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $total_discount
     *
     * @return          object                $this
     */
    public function setTotalDiscount($total_discount) {
        if($this->setModified('total_discount', $total_discount)->isModified()) {
            $this->total_discount = $total_discount;
        }

        return $this;
    }

    /**
     * @name            getTotalDiscount ()
     *                                   Returns the value of total_discount property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->total_discount
     */
    public function getTotalDiscount() {
        return $this->total_discount;
    }

    /**
     * @name                  setTotalShipment ()
     *                                         Sets the total_shipment property.
     *                                         Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $total_shipment
     *
     * @return          object                $this
     */
    public function setTotalShipment($total_shipment) {
        if($this->setModified('total_shipment', $total_shipment)->isModified()) {
            $this->total_shipment = $total_shipment;
        }

        return $this;
    }

    /**
     * @name            getTotalShipment ()
     *                                   Returns the value of total_shipment property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->total_shipment
     */
    public function getTotalShipment() {
        return $this->total_shipment;
    }

    /**
     * @name                  setTotalTax ()
     *                                    Sets the total_tax property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $total_tax
     *
     * @return          object                $this
     */
    public function setTotalTax($total_tax) {
        if($this->setModified('total_tax', $total_tax)->isModified()) {
            $this->total_tax = $total_tax;
        }

        return $this;
    }

    /**
     * @name            getTotalTax ()
     *                              Returns the value of total_tax property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->total_tax
     */
    public function getTotalTax() {
        return $this->total_tax;
    }

}
/**
 * Change Log:
 * *************************************
 * v1.0.3                      Can Berkol
 * 01.07.2014
 * **************************************
 * A getOrderNumber()
 * A setOrderNumber()
 *
 * *************************************
 * v1.0.2                      Can Berkol
 * 14.04.2014
 * **************************************
 * A getSubtotal()
 * A getTotalDiscount()
 * A getTotalShipment()
 * A getTotalTax()
 * A setSubTotal()
 * A setTotalDiscount()
 * A setTotalShipment()
 * A setTotalTax()
 *
 * *************************************
 * v1.0.1                      Murat Ünal
 * 11.10.2013
 * **************************************
 * D get_payment_transactions()
 * D set_payment_transactions()
 * D getShoppingOrderItems()
 * D setShoppingOrderItems()
 * **************************************
 * v1.0.0                      Murat Ünal
 * 23.09.2013
 * **************************************
 * A getBilling()
 * A getCountItems()
 * A getDateCancelled()
 * A getDateCreated()
 * A get_date_purchased()
 * A getDateReturned()
 * A getDateUpdated()
 * A getFlag()
 * A getId()
 * A getInstructions()
 * A getMember()
 * A get_payment_transactions()
 * A getShipping()
 * A getCart()
 * A getShoppingOrderStatus()
 * A getShoppingOrderItems()
 * A getTotalAmount()
 *
 * A setBilling()
 * A setCountItems()
 * A setDateCancelled()
 * A setDateCreated()
 * A set_date_purchased()
 * A setDateReturned()
 * A setDateUpdated()
 * A setFlag()
 * A setInstructions()
 * A setMember()
 * A set_payment_transactions()
 * A setShipping()
 * A setCart()
 * A setShoppingOrderStatus()
 * A setShoppingOrderItems()
 * A setTotalAmount()
 *
 */