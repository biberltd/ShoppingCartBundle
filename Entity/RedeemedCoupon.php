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
 *     name="redeemed_coupon",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={@ORM\Index(name="idxNRedeemedCouponDateRedeemed", columns={"date_redeemed"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxURedeemedCouponId", columns={"id"})}
 * )
 */
class RedeemedCoupon extends CoreEntity
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
    private $date_redeemed;

    /** 
     * @ORM\ManyToOne(targetEntity="Coupon")
     * @ORM\JoinColumn(name="coupon", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\ShoppingCartBundle\Entity\Coupon
     */
    private $coupon;

    /** 
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MemberManagementBundle\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="ShoppingOrder")
     * @ORM\JoinColumn(name="shopping_order", referencedColumnName="id")
     * @var \BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder
     */
    private $shopping_order;


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
     * @param \BiberLtd\Bundle\ShoppingCartBundle\Entity\Coupon $coupon
     *
     * @return $this
     */
    public function setCoupon(\BiberLtd\Bundle\ShoppingCartBundle\Entity\Coupon $coupon) {
        if(!$this->setModified('coupon', $coupon)->isModified()) {
            return $this;
        }
		$this->coupon = $coupon;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ShoppingCartBundle\Entity\Coupon
     */
    public function getCoupon() {
        return $this->coupon;
    }

    /**
     * @param \DateTime $date_redeemed
     *
     * @return $this
     */
    public function setDateRedeemed(\DateTime $date_redeemed) {
        if(!$this->setModified('date_redeemed', $date_redeemed)->isModified()) {
            return $this;
        }
		$this->date_redeemed = $date_redeemed;
		return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateRedeemed() {
        return $this->date_redeemed;
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
     * @param \BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder $order
     *
     * @return $this
     */
    public function setShoppingOrder(\BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder $order) {
        if(!$this->setModified('shopping_order', $order)->isModified()) {
            return $this;
        }
		$this->shopping_order = $order;
		return $this;
    }

    /**
     * @return \BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrder
     */
    public function getShoppingOrder() {
        return $this->shopping_order;
    }
}