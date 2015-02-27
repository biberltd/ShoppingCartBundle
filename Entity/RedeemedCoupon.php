<?php
/**
 * @name        redeemedCoupon
 * @package		BiberLtd\Bundle\CoreBundle\ShoppingCartBundle
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
namespace BiberLtd\Bundle\ShoppingCartBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreEntity;
/** 
 * @ORM\Entity
 * @ORM\Table(
 *     name="redeemed_coupon",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idx_n_redeemed_coupon_date_redeemed", columns={"date_redeemed"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_redeemed_coupon_id", columns={"id"})}
 * )
 */
class RedeemedCoupon extends CoreEntity
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
    private $date_redeemed;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\ShoppingCartBundle\Entity\Coupon")
     * @ORM\JoinColumn(name="coupon", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $coupon;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $member;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingCart")
     * @ORM\JoinColumn(name="cart", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $shopping_cart;
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
     * @name                  setCoupon ()
     *                                  Sets the coupon property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $coupon
     *
     * @return          object                $this
     */
    public function setCoupon($coupon) {
        if(!$this->setModified('coupon', $coupon)->isModified()) {
            return $this;
        }
		$this->coupon = $coupon;
		return $this;
    }

    /**
     * @name            getCoupon ()
     *                            Returns the value of coupon property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->coupon
     */
    public function getCoupon() {
        return $this->coupon;
    }

    /**
     * @name                  setDateRedeemed ()
     *                                        Sets the date_redeemed property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_redeemed
     *
     * @return          object                $this
     */
    public function setDateRedeemed($date_redeemed) {
        if(!$this->setModified('date_redeemed', $date_redeemed)->isModified()) {
            return $this;
        }
		$this->date_redeemed = $date_redeemed;
		return $this;
    }

    /**
     * @name            getDateRedeemed ()
     *                                  Returns the value of date_redeemed property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_redeemed
     */
    public function getDateRedeemed() {
        return $this->date_redeemed;
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
     * @name                  setShoppingCart ()
     *                                        Sets the shopping_cart property.
     *                                        Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $shopping_cart
     *
     * @return          object                $this
     */
    public function setShoppingCart($shopping_cart) {
        if(!$this->setModified('shopping_cart', $shopping_cart)->isModified()) {
            return $this;
        }
		$this->shopping_cart = $shopping_cart;
		return $this;
    }

    /**
     * @name            getShoppingCart ()
     *                                  Returns the value of shopping_cart property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->shopping_cart
     */
    public function getShoppingCart() {
        return $this->shopping_cart;
    }

}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 23.09.2013
 * **************************************
 * A getCoupon()
 * A getDateRedeemed()
 * A getId()
 * A getMember()
 * A getShoppingCart()
 *
 * A setCoupon()
 * A setDateRedeemed()
 * A setMember()
 * A shopping_cart()
 *
 */