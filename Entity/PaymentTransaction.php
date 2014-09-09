<?php
/**
 * @name        PaymentTransaction
 * @package		BiberLtd\Core\ShoppingCartBundle
 *
 * @author		Murat Ünal
 *
 * @version     1.0.0
 * @date        23.09.2013
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */
namespace BiberLtd\Core\Bundles\ShoppingCartBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreEntity;
/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="payment_transaction",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idx_n_payment_transaction_date_added", columns={"date_added"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_payment_transaction_id", columns={"id"})}
 * )
 */
class PaymentTransaction extends CoreEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $transaction_id;

    /**
     * @ORM\Column(type="decimal", length=7, nullable=false)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=155, nullable=false)
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $response;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\PaymentGatewayBundle\Entity\PaymentGateway")
     * @ORM\JoinColumn(name="gateway", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $gateway;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Core\Bundles\ShoppingCartBundle\Entity\ShoppingOrder")
     * @ORM\JoinColumn(name="order", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $shopping_order;
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
     * @name                  setAmount ()
     *                                  Sets the amount property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $amount
     *
     * @return          object                $this
     */
    public function setAmount($amount) {
        if(!$this->setModified('amount', $amount)->isModified()) {
            return $this;
        }
		$this->amount = $amount;
		return $this;
    }

    /**
     * @name            getAmount ()
     *                            Returns the value of amount property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->amount
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * @name                  setMember ()
     *                                  Sets the member property.
     *                                  Updates the data only if stored value and value to be set are different.
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
    public function setMember($member) {
        if(!$this->setModified('member', $member)->isModified()) {
            return $this;
        }
		$this->member = $member;
		return $this;
    }

    /**
     * @name            getMember ()
     *                            Returns the value of member property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->member
     */
    public function getMember() {
        return $this->member;
    }

    /**
     * @name                  setGateway ()
     *                                          Sets the gateway property.
     *                                          Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $gateway
     *
     * @return          object                $this
     */
    public function setGateway($gateway) {
        if(!$this->setModified('gateway', $gateway)->isModified()) {
            return $this;
        }
		$this->gateway = $gateway;
		return $this;
    }

    /**
     * @name            getGateway ()
     *                                    Returns the value of gateway property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->gateway
     */
    public function getGateway() {
        return $this->gateway;
    }

    /**
     * @name                  setResponse ()
     *                                    Sets the response property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $response
     *
     * @return          object                $this
     */
    public function setResponse($response) {
        if(!$this->setModified('response', $response)->isModified()) {
            return $this;
        }
		$this->response = $response;
		return $this;
    }

    /**
     * @name            getResponse ()
     *                              Returns the value of response property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * @name                  setShoppingOrder ()
     *                                         Sets the shopping_order property.
     *                                         Updates the data only if stored value and value to be set are different.
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
    public function setShoppingOrder($shopping_order) {
        if(!$this->setModified('shopping_order', $shopping_order)->isModified()) {
            return $this;
        }
		$this->shopping_order = $shopping_order;
		return $this;
    }

    /**
     * @name            getShoppingOrder ()
     *                                   Returns the value of shopping_order property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->shopping_order
     */
    public function getShoppingOrder() {
        return $this->shopping_order;
    }

    /**
     * @name                  setSite ()
     *                                Sets the site property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $site
     *
     * @return          object                $this
     */
    public function setSite($site) {
        if(!$this->setModified('site', $site)->isModified()) {
            return $this;
        }
		$this->site = $site;
		return $this;
    }

    /**
     * @name            getSite ()
     *                          Returns the value of site property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->site
     */
    public function getSite() {
        return $this->site;
    }

    /**
     * @name                  setStatus ()
     *                                  Sets the status property.
     *                                  Updates the data only if stored value and value to be set are different.
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
     * @name            getStatus ()
     *                            Returns the value of status property.
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
     * @name            setTransactionId()
     *                                       Sets the transaction_id property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $transaction_id
     *
     * @return          object                $this
     */
    public function setTransactionId($transaction_id) {
        if(!$this->setModified('transaction_id', $transaction_id)->isModified()) {
            return $this;
        }
		$this->transaction_id = $transaction_id;
		return $this;
    }

    /**
     * @name            getTransactionId()
     *                                 Returns the value of transaction_id property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->transaction_id
     */
    public function getTransactionId() {
        return $this->transaction_id;
    }
}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 23.09.2013
 * **************************************
 * A get_amount()
 * A getDateAdded()
 * A getId()
 * A getMember()
 * A getGateway()
 * A getResponse()
 * A getShoppingOrder()
 * A getSite()
 * A getStatus()
 * A get_transaction_id()
 *
 * A set_amount()
 * A setDateAdded()
 * A setMember()
 * A etGateway()
 * A setResponse()
 * A setShoppingOrder()
 * A setSite()
 * A setStatus()
 * A set_transaction_id()
 *
 */