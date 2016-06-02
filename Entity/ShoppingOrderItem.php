<?php
/**
 * @author		Can Berkol
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        27.12.2015
 */
namespace BiberLtd\Bundle\ShoppingCartBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="shopping_order_item",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNShopppingOrderDateReturned", columns={"date_returned"}),
 *         @ORM\Index(name="idxNShopppingOrderDateAdded", columns={"date_added"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUShoppingOrderItemId", columns={"id"})}
 * )
 */
class ShoppingOrderItem extends CoreEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=6, nullable=false, options={"default":0})
     * @var int
     */
    private $quantity;

    /**
     * @ORM\Column(type="decimal", length=7, nullable=false, options={"default":0})
     * @var float
     */
    private $price;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false, options={"default":0})
     * @var float
     */
    private $subtotal;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $date_returned;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $instructions;

    /**
     * @ORM\Column(type="decimal", length=3, nullable=false, options={"default":0})
     * @var float
     */
    private $tax;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false, options={"default":0})
     * @var float
     */
    private $tax_amount;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false, options={"default":0})
     * @var float
     */
    private $discount;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false, options={"default":0})
     * @var float
     */
    private $total;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false, options={"default":0})
     * @var float
     */
    private $total_with_tax;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, options={"default":"p"})
     * @var string
     */
    private $package_type;

    /**
     * @ORM\ManyToOne(targetEntity="ShoppingOrder")
     * @ORM\JoinColumn(name="shopping_order", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder
     */
    private $shopping_order;

    /**
     * @ORM\ManyToOne(targetEntity="\BiberLtd\Bundle\ProductManagementBundle\Entity\Product")
     * @ORM\JoinColumn(name="product", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\ProductManagementBundle\Entity\Product
     */
    private $product;

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param \DateTime $date_returned
     *
     * @return $this
     */
    public function setDateReturned(\DateTime $date_returned) {
        if(!$this->setModified('date_returned', $date_returned)->isModified()) {
            return $this;
        }
		$this->date_returned = $date_returned;
		return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateReturned() {
        return $this->date_returned;
    }

    /**
     * @param float $discount
     *
     * @return $this
     */
    public function setDiscount(float $discount) {
        if(!$this->setModified('discount', $discount)->isModified()) {
            return $this;
        }
		$this->discount = $discount;
		return $this;
    }

    /**
     * @return float
     */
    public function getDiscount() {
        return $this->discount;
    }

    /**
     * @param string $instructions
     *
     * @return $this
     */
    public function setInstructions(string $instructions) {
        if(!$this->setModified('instructions', $instructions)->isModified()) {
            return $this;
        }
		$this->instructions = $instructions;
		return $this;
    }

    /**
     * @return string
     */
    public function getInstructions() {
        return $this->instructions;
    }

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice(float $price) {
        if(!$this->setModified('price', $price)->isModified()) {
            return $this;
        }
		$this->price = $price;
		return $this;
    }

    /**
     * @return float
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param \BiberLtd\Bundle\ProductManagementBundle\Entity\Product $product
     *
     * @return $this
     */
    public function setProduct(\BiberLtd\Bundle\ProductManagementBundle\Entity\Product $product) {
        if(!$this->setModified('product', $product)->isModified()) {
            return $this;
        }
		$this->product = $product;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ProductManagementBundle\Entity\Product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity(int $quantity) {
        if(!$this->setModified('quantity', $quantity)->isModified()) {
            return $this;
        }
		$this->quantity = $quantity;
		return $this;
    }

    /**
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }
    /**
     * @param float $subtotal
     *
     * @return $this
     */
    public function setSubtotal(float $subtotal) {
        if(!$this->setModified('subtotal', $subtotal)->isModified()) {
            return $this;
        }
		$this->subtotal = $subtotal;
		return $this;
    }

    /**
     * @return float
     */
    public function getSubtotal() {
        return $this->subtotal;
    }

    /**
     * @param float $tax
     *
     * @return $this
     */
    public function setTax(float $tax) {
        if(!$this->setModified('tax', $tax)->isModified()) {
            return $this;
        }
		$this->tax = $tax;
		return $this;
    }

    /**
     * @return float
     */
    public function getTax() {
        return $this->tax;
    }

    /**
     * @param float $total
     *
     * @return $this
     */
    public function setTotal(float $total) {
        if(!$this->setModified('total', $total)->isModified()) {
            return $this;
        }
		$this->total = $total;
		return $this;
    }

    /**
     * @return float
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * @param float $tax_amount
     *
     * @return $this
     */
    public function setTaxAmount(float $tax_amount) {
        if($this->setModified('tax_amount', $tax_amount)->isModified()) {
            $this->tax_amount = $tax_amount;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxAmount() {
        return $this->tax_amount;
    }

    /**
     * @param float $total_with_tax
     *
     * @return $this
     */
    public function setTotalWithTax(float $total_with_tax) {
        if($this->setModified('total_with_tax', $total_with_tax)->isModified()) {
            $this->total_with_tax = $total_with_tax;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalWithTax() {
        return $this->total_with_tax;
    }

    /**
     * @param string $package_type
     *
     * @return $this
     */
    public function setPackageType(string $package_type) {
        if($this->setModified('package_type', $package_type)->isModified()) {
            $this->package_type = $package_type;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPackageType() {
        return $this->package_type;
    }

    /**
     * @return \BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder
     */
    public function getShoppingOrder(){
        return $this->shopping_order;
    }

    /**
     * @param \BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder $shopping_order
     *
     * @return $this
     */
    public function setShoppingOrder(\BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder $shopping_order){
        if(!$this->setModified('shopping_order', $shopping_order)->isModified()){
            return $this;
        }
        $this->shopping_order = $shopping_order;

        return $this;
    }

}