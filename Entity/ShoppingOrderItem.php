<?php
/**
 * @name        ShoppingOrderItem
 * @package		BiberLtd\Core\ShoppingCartBundle
 *
 * @author      Can Berkol
 * @author		Murat Ünal
 *
 * @version     1.0.2
 * @date        01.05.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Bundle\ShoppingCartBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="shopping_order_item",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_shopping_order_item_date_returned", columns={"date_returned"}),
 *         @ORM\Index(name="idx_n_shopping_order_item_date_added", columns={"date_added"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_shopping_order_item_id", columns={"id"})}
 * )
 */
class ShoppingOrderItem extends CoreEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=6, nullable=false)
     */
    private $quantity;

    /**
     * @ORM\Column(type="decimal", length=7, nullable=false)
     */
    private $price;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false)
     */
    private $subtotal;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_returned;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $instructions;

    /**
     * @ORM\Column(type="decimal", length=3, nullable=false)
     */
    private $tax;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false)
     */
    private $tax_amount;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false)
     */
    private $discount;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false)
     */
    private $total;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false)
     */
    private $total_with_tax;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $package_type;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder")
     * @ORM\JoinColumn(name="shopping_order", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\ProductManagementBundle\Entity\Product")
     * @ORM\JoinColumn(name="product", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $product;
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
     * @name                  setDiscount ()
     *                                    Sets the discount property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $discount
     *
     * @return          object                $this
     */
    public function setDiscount($discount) {
        if(!$this->setModified('discount', $discount)->isModified()) {
            return $this;
        }
		$this->discount = $discount;
		return $this;
    }

    /**
     * @name            getDiscount ()
     *                              Returns the value of discount property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->discount
     */
    public function getDiscount() {
        return $this->discount;
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
     * @name                  setPrice ()
     *                                 Sets the price property.
     *                                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $price
     *
     * @return          object                $this
     */
    public function setPrice($price) {
        if(!$this->setModified('price', $price)->isModified()) {
            return $this;
        }
		$this->price = $price;
		return $this;
    }

    /**
     * @name            getPrice ()
     *                           Returns the value of price property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->price
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @name                  setProduct ()
     *                                   Sets the product property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $product
     *
     * @return          object                $this
     */
    public function setProduct($product) {
        if(!$this->setModified('product', $product)->isModified()) {
            return $this;
        }
		$this->product = $product;
		return $this;
    }

    /**
     * @name            getProduct ()
     *                             Returns the value of product property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * @name                  setQuantity ()
     *                                    Sets the quantity property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $quantity
     *
     * @return          object                $this
     */
    public function setQuantity($quantity) {
        if(!$this->setModified('quantity', $quantity)->isModified()) {
            return $this;
        }
		$this->quantity = $quantity;
		return $this;
    }

    /**
     * @name            getQuantity ()
     *                              Returns the value of quantity property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->quantity
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @name            setOrder ()
     *                  Sets the shopping_order property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $shopping_order
     *
     * @return          object                $this
     */
    public function setOrder($shopping_order) {
        if(!$this->setModified('order', $shopping_order)->isModified()) {
            return $this;
        }
		$this->order = $shopping_order;
		return $this;
    }

    /**
     * @name            getShoppingOrder ()
     *                  Returns the value of shopping_order property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->shopping_order
     */
    public function getOrder() {
        return $this->order;
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
        if(!$this->setModified('subtotal', $subtotal)->isModified()) {
            return $this;
        }
		$this->subtotal = $subtotal;
		return $this;
    }

    /**
     * @name            getSubtotal ()
     *                  Returns the value of subtotal property.
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
     * @name            setTax ()
     *                  Sets the tax property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $tax
     *
     * @return          object                $this
     */
    public function setTax($tax) {
        if(!$this->setModified('tax', $tax)->isModified()) {
            return $this;
        }
		$this->tax = $tax;
		return $this;
    }

    /**
     * @name            getTax ()
     *                  Returns the value of tax property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->tax
     */
    public function getTax() {
        return $this->tax;
    }

    /**
     * @name            setTotal ()
     *                  Sets the total property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $total
     *
     * @return          object                $this
     */
    public function setTotal($total) {
        if(!$this->setModified('total', $total)->isModified()) {
            return $this;
        }
		$this->total = $total;
		return $this;
    }

    /**
     * @name            getTotal ()
     *                  Returns the value of total property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->total
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * @name            setTaxAmount ()
     *                  Sets the tax_amount property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $tax_amount
     *
     * @return          object                $this
     */
    public function setTaxAmount($tax_amount) {
        if($this->setModified('tax_amount', $tax_amount)->isModified()) {
            $this->tax_amount = $tax_amount;
        }

        return $this;
    }

    /**
     * @name            getTaxAmount ()
     *                  Returns the value of tax_amount property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->tax_amount
     */
    public function getTaxAmount() {
        return $this->tax_amount;
    }

    /**
     * @name            setTotalWithTax ()
     *                  Sets the total_with_tax property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $total_with_tax
     *
     * @return          object                $this
     */
    public function setTotalWithTax($total_with_tax) {
        if($this->setModified('total_with_tax', $total_with_tax)->isModified()) {
            $this->total_with_tax = $total_with_tax;
        }

        return $this;
    }

    /**
     * @name            getTotalWithTax ()
     *                  Returns the value of total_with_tax property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->total_with_tax
     */
    public function getTotalWithTax() {
        return $this->total_with_tax;
    }

    /**
     * @name            setPackageType ()
     *                  Sets the package_type property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.2
     * @version         1.0.2
     *
     * @use             $this->setModified()
     *
     * @param           mixed $package_type
     *
     * @return          object                $this
     */
    public function setPackageType($package_type) {
        if($this->setModified('package_type', $package_type)->isModified()) {
            $this->package_type = $package_type;
        }

        return $this;
    }

    /**
     * @name            getPackageType ()
     *                  Returns the value of package_type property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.2
     * @version         1.0.2
     *
     * @return          mixed           $this->package_type
     */
    public function getPackageType() {
        return $this->package_type;
    }

}
/**
 * Change Log:
 * **************************************
 * v1.0.2                      Can Berkol
 * 01.05.2013
 * **************************************
 * A getPackageType()
 * A setPackageType
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 14.04.2013
 * **************************************
 * A tax_amount
 * A total_with_tax
 * A getTaxAmount()
 * A getTotalWithTax()
 * A setTaxAmount()
 * A setTotalWithTax()
 *
 * **************************************
 * v1.0.0                      Murat Ünal
 * 23.09.2013
 * **************************************
 * A getDateAdded()
 * A getDateReturned()
 * A getDiscount()
 * A getId()
 * A getInstructions()
 * A getPrice()
 * A getProduct()
 * A getQuantity()
 * A getShoppingOrder()
 * A getSubtotal()
 * A getTax()
 * A getTotal()
 *
 * A setDateAdded()
 * A setDateReturned()
 * A setDiscount()
 * A setInstructions()
 * A setPrice()
 * A setProduct()
 * A setQuantity()
 * A setShoppingOrder()
 * A setSubtotal()
 * A setTax()
 * A setTotal()
 *
 */