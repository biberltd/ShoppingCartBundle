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
use BiberLtd\Bundle\PhpOrientBundle\Odm\Types\DateTime;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="shopping_order",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idxNShoppingOrderDateCreated", columns={"date_created"}),
 *         @ORM\Index(name="idxNShoppingOrderDateUpdated", columns={"date_updated"}),
 *         @ORM\Index(name="idxNShoppingOrderDatePurchased", columns={"date_purchased"}),
 *         @ORM\Index(name="idxNShoppingOrderDateCancelled", columns={"date_cancelled"}),
 *         @ORM\Index(name="idxNShoppingOrderDateReturned", columns={"date_returned"}),
 *         @ORM\Index(name="idxNShoppingOrderStatus", columns={"status"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idxUShoppingOrderId", columns={"id"}),
 *         @ORM\UniqueConstraint(name="idxUShoppingOrderNumber", columns={"order_number"})
 *     }
 * )
 */
class ShoppingOrder extends CoreEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=15)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $date_created;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_updated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $date_purchased;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $date_cancelled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $date_returned;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":0})
     * @var int
     */
    private $count_items;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false, options={"default":0})
     * @var float
     */
    private $subtotal;
    /**
     * @ORM\Column(type="decimal", length=10, nullable=true)
     * @var float
     */
    private $installment_fee;

    /**
     * @ORM\Column(type="decimal", length=7, nullable=false, options={"default":0})
     * @var float
     */
    private $total_amount;

    /**
     * @ORM\Column(type="decimal", nullable=false, options={"default":0})
     * @var float
     */
    private $total_shipment;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @var string
     */
    private $billing_information;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $shipping_information;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false, options={"default":","})
     * @var float
     */
    private $total_tax;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=false, options={"default":0})
     * @var float
     */
    private $total_discount;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $instructions;

    /**
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"o"})
     * @var string
     */
    private $flag;

    /**
     * @ORM\Column(type="integer", unique=true, length=20, nullable=false)
     * @var string
     */
    private $order_number;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $transaction_info;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="purchaser", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $purchaser;

    /**
     * 
     * @ORM\Column(type="string", length=1, nullable=false)
     * @var string
     */
    private $status;

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param string $order_number
     *
     * @return $this
     */
    public function setOrderNumber(\string $order_number) {
        if($this->setModified('order_number', $order_number)->isModified()) {
            $this->order_number = $order_number;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderNumber() {
        return $this->order_number;
    }

    /**
     * @param int $count_items
     *
     * @return $this
     */
    public function setCountItems(\integer $count_items) {
        if(!$this->setModified('count_items', $count_items)->isModified()) {
            return $this;
        }
        $this->count_items = $count_items;
        return $this;
    }

    /**
     * @return int
     */
    public function getCountItems() {
        return $this->count_items;
    }

    /**+
     * @param \DateTime $date_cancelled
     *
     * @return $this
     */
    public function setDateCancelled(\DateTime $date_cancelled) {
        if(!$this->setModified('date_cancelled', $date_cancelled)->isModified()) {
            return $this;
        }
        $this->date_cancelled = $date_cancelled;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCancelled() {
        return $this->date_cancelled;
    }

    /**
     * @param \DateTime $date_created
     *
     * @return $this
     */
    public function setDateCreated(\DateTime $date_created) {
        if(!$this->setModified('date_created', $date_created)->isModified()) {
            return $this;
        }
        $this->date_created = $date_created;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated() {
        return $this->date_created;
    }

    /**
     * @param \DateTime $date_purchased
     *
     * @return $this
     */
    public function setDatePurchased(\DateTime $date_purchased) {
        if(!$this->setModified('date_purchased', $date_purchased)->isModified()) {
            return $this;
        }
        $this->date_purchased = $date_purchased;
        return $this;
    }

    /**
     * @return \BiberLtd\Bundle\PhpOrientBundle\Odm\Types\DateTime
     */
    public function getDatePurchased() {
        return $this->date_purchased;
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
     * @param string $flag
     *
     * @return $this
     */
    public function setFlag(\string $flag) {
        if(!$this->setModified('flag', $flag)->isModified()) {
            return $this;
        }
        $this->flag = $flag;
        return $this;
    }

    /**
     * @return string
     */
    public function getFlag() {
        return $this->flag;
    }

    /**
     * @return float
     */
    public function getInstallmentFee()
    {
        return $this->installment_fee;
    }

    /**
     * @param float $installment_fee
     *
     * @return $this
     */
    public function setInstallmentFee(\float $installment_fee)
    {
        if (!$this->setModified('installment_fee', $installment_fee)->isModified()) {
            return $this;
        }
        $this->installment_fee = $installment_fee;
        return $this;
    }

    /**
     * @param string $instructions
     *
     * @return $this
     */
    public function setInstructions(\string $instructions) {
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
     * @param \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
     *
     * @return $this
     */
    public function setPurchaser(\BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member) {
        if(!$this->setModified('purchaser', $member)->isModified()) {
            return $this;
        }
        $this->purchaser = $member;
        return $this;
    }

    /**
     * @return \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    public function getPurchaser() {
        return $this->purchaser;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(\string $status) {
        if(!$this->setModified('status', $status)->isModified()) {
            return $this;
        }
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param float $total_amount
     *
     * @return $this
     */
    public function setTotalAmount(\float $total_amount) {
        if(!$this->setModified('total_amount', $total_amount)->isModified()) {
            return $this;
        }
        $this->total_amount = $total_amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalAmount() {
        return $this->total_amount;
    }

    /**
     * @param float $subtotal
     *
     * @return $this
     */
    public function setSubtotal(\float $subtotal) {
        if($this->setModified('subtotal', $subtotal)->isModified()) {
            $this->subtotal = $subtotal;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getSubtotal() {
        return $this->subtotal;
    }

    /**
     * @param float $total_discount
     *
     * @return $this
     */
    public function setTotalDiscount(\float $total_discount) {
        if($this->setModified('total_discount', $total_discount)->isModified()) {
            $this->total_discount = $total_discount;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalDiscount() {
        return $this->total_discount;
    }

    /**
     * @param float $total_shipment
     *
     * @return $this
     */
    public function setTotalShipment(\float $total_shipment) {
        if($this->setModified('total_shipment', $total_shipment)->isModified()) {
            $this->total_shipment = $total_shipment;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalShipment() {
        return $this->total_shipment;
    }

    /**
     * @param float $total_tax
     *
     * @return $this
     */
    public function setTotalTax(\float $total_tax) {
        if($this->setModified('total_tax', $total_tax)->isModified()) {
            $this->total_tax = $total_tax;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalTax() {
        return $this->total_tax;
    }

    /**
     * @return string
     */
    public function getBillingInformation(){
        return $this->billing_information;
    }

    /**
     * @param float $billing_information
     *
     * @return $this
     */
    public function setBillingInformation(\float $billing_information){
        if(!$this->setModified('billing_information', $billing_information)->isModified()){
            return $this;
        }
        $this->billing_information = $billing_information;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingInformation(){
        return $this->shipping_information;
    }

    /**
     * @param string $shipping_information
     *
     * @return $this
     */
    public function setShippingInformation(\string $shipping_information){
        if(!$this->setModified('shipping_information', $shipping_information)->isModified()){
            return $this;
        }
        $this->shipping_information = $shipping_information;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(){
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent(\string $content){
        if(!$this->setModified('content', $content)->isModified()){
            return $this;
        }
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionInfo(){
        return $this->transaction_info;
    }

    /**
     * @param string $transaction_info
     *
     * @return $this
     */
    public function setTransactionInfo(\string $transaction_info){
        if(!$this->setModified('transaction_info', $transaction_info)->isModified()){
            return $this;
        }
        $this->transaction_info = $transaction_info;

        return $this;
    }
}