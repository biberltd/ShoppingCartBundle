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
 *     name="payment_transaction",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxNPaymentTransactionDateAdded", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUPaymentTransactionId", columns={"id"})}
 * )
 */
class PaymentTransaction extends CoreEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $transaction_id;

    /**
     * @ORM\Column(type="decimal", length=7, nullable=false, options={"default":0})
     * @var float
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=155, nullable=false)
     * @var string
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @var string
     */
    private $response;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    public $date_added;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="\BiberLtd\Bundle\PaymentGatewayBundle\Entity\PaymentGateway")
     * @ORM\JoinColumn(name="gateway", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\PaymentGatewayBundle\Entity\PaymentGateway
     */
    private $gateway;

    /**
     * @ORM\ManyToOne(targetEntity="ShoppingOrder")
     * @ORM\JoinColumn(name="shopping_order", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder
     */
    private $shopping_order;

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }
    /**
     * @param float $amount
     *
     * @return $this
     */
    public function setAmount(float $amount) {
        if(!$this->setModified('amount', $amount)->isModified()) {
            return $this;
        }
		$this->amount = $amount;
		return $this;
    }

    /**
     * @return float
     */
    public function getAmount() {
        return $this->amount;
    }
    /**
     * @param \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
     *
     * @return $this
     */
    public function setMember(\BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member) {
        if(!$this->setModified('member', $member)->isModified()) {
            return $this;
        }
		$this->member = $member;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    public function getMember() {
        return $this->member;
    }

    /**
     * @param \BiberLtd\Bundle\PaymentGatewayBundle\Entity\PaymentGateway $gateway
     *
     * @return $this
     */
    public function setGateway(\BiberLtd\Bundle\PaymentGatewayBundle\Entity\PaymentGateway $gateway) {
        if(!$this->setModified('gateway', $gateway)->isModified()) {
            return $this;
        }
		$this->gateway = $gateway;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\PaymentGatewayBundle\Entity\PaymentGateway
     */
    public function getGateway() {
        return $this->gateway;
    }

    /**
     * @param string $response
     *
     * @return $this
     */
    public function setResponse(string $response) {
        if(!$this->setModified('response', $response)->isModified()) {
            return $this;
        }
		$this->response = $response;
		return $this;
    }

    /**
     * @return string
     */
    public function getResponse() {
        return $this->response;
    }
        /**
     * @param \BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder $shopping_order
     *
     * @return $this
     */
    public function setShoppingOrder(\BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder $shopping_order) {
        if(!$this->setModified('shopping_order', $shopping_order)->isModified()) {
            return $this;
        }
		$this->shopping_order = $shopping_order;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder
     */
    public function getShoppingOrder() {
        return $this->shopping_order;
    }

    /**
     * @param \BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site
     *
     * @return $this
     */
    public function setSite(\BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site) {
        if(!$this->setModified('site', $site)->isModified()) {
            return $this;
        }
		$this->site = $site;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
     */
    public function getSite() {
        return $this->site;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(string $status) {
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
     * */
    public function setTransactionId(string $transaction_id) {
        if(!$this->setModified('transaction_id', $transaction_id)->isModified()) {
            return $this;
        }
		$this->transaction_id = $transaction_id;
		return $this;
    }

    /**
     * @return string
     */
    public function getTransactionId() {
        return $this->transaction_id;
    }
}