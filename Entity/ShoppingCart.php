<?php
/**
 * @name        ShoppingCart
 * @package		BiberLtd\Core\ShoppingCartBundle
 *
 * @author		Murat Ünal
 *
 * @version     1.0.1
 * @date        11.10.2013
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
 *     name="shopping_cart",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_shopping_cart_date_created", columns={"date_created"}),
 *         @ORM\Index(name="idx_n_shopping_cart_date_cancelled", columns={"date_cancelled"}),
 *         @ORM\Index(name="idx_n_shopping_cart_date_ordered", columns={"date_ordered"}),
 *         @ORM\Index(name="idx_n_shopping_cart_date_updated", columns={"date_updated"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_shopping_cart_id", columns={"id"})}
 * )
 */
class ShoppingCart extends CoreEntity
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_cancelled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_ordered;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_updated;

    /**
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_items;

    /**
     * @ORM\Column(type="decimal", length=7, nullable=false)
     */
    private $total_amount;

    /**
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingCartItem",
     *     mappedBy="shopping_cart"
     * )
     */
    private $shopping_cart_items;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\LogBundle\Entity\Session")
     * @ORM\JoinColumn(name="session", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $session;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $member;
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
     * @return          integer          $this->id
     */
    public function getId(){
        return $this->id;
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
     * @name                  setDateOrdered ()
     *                                       Sets the date_ordered property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_ordered
     *
     * @return          object                $this
     */
    public function setDateOrdered($date_ordered) {
        if(!$this->setModified('date_ordered', $date_ordered)->isModified()) {
            return $this;
        }
		$this->date_ordered = $date_ordered;
		return $this;
    }

    /**
     * @name            getDateOrdered ()
     *                                 Returns the value of date_ordered property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_ordered
     */
    public function getDateOrdered() {
        return $this->date_ordered;
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
     * @name                  setSession ()
     *                                   Sets the session property.
     *                                   Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $session
     *
     * @return          object                $this
     */
    public function setSession($session) {
        if(!$this->setModified('session', $session)->isModified()) {
            return $this;
        }
		$this->session = $session;
		return $this;
    }

    /**
     * @name            getSession ()
     *                             Returns the value of session property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->session
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * @name                  setShoppingCartItems()
     *                                        Sets the shopping_cart_items property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $shopping_cart_items
     *
     * @return          object                $this
     */
    public function setShoppingCartItems($shopping_cart_items) {
        if(!$this->setModified('shopping_cart_items', $shopping_cart_items)->isModified()) {
            return $this;
        }
		$this->shopping_cart_items = $shopping_cart_items;
		return $this;
    }

    /**
     * @name            getShoppingCartItems()
     *                                  Returns the value of shopping_cart_items property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->shopping_cart_items
     */
    public function getShoppingCartItems() {
        return $this->shopping_cart_items;
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
}
/**
 * Change Log:
 * * ************************************
 * v1.0.1                      Murat Ünal
 * 11.10.2013
 * **************************************
 * D getShoppingOrders()
 * D setShoppingOrders()
 * D get_redeemed_coupons()
 * D set_redeemed_coupons()
 * **************************************
 * v1.0.0                      Murat Ünal
 * 23.09.2013
 * **************************************
 * A getCountItems()
 * A getDateCancelled()
 * A getDateCreated()
 * A getDateOrdered()
 * A getDateUpdated()
 * A getId()
 * A getMember()
 * A get_redeemed_coupons()
 * A getSession()
 * A getShoppingCartItems()
 * A getShoppingOrders()
 * A getTotalAmount()
 *
 * A setCountItems()
 * A setDateCancelled()
 * A setDateCreated()
 * A setDateOrdered()
 * A setDateUpdated()
 * A setMember()
 * A set_redeemed_coupons()
 * A setSession()
 * A setShoppingCartItems()
 * A setShoppingOrders()
 * A setTotalAmount()
 *
 */