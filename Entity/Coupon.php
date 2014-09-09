<?php

/**
 * @name        Coupon
 * @package		BiberLtd\Bundle\CoreBundle\ShoppingCartBundle
 *
 * @author      Can Berkol
 * @author		Murat Ünal
 *
 * @version     1.0.5
 * @date        09.05.2014
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com)
 * @license     GPL v3.0
 *
 * @description Model / Entity class.
 *
 */

namespace BiberLtd\Bundle\ShoppingCartBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Bundle\CoreBundle\CoreLocalizableEntity;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="coupon",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_n_coupon_date_published", columns={"date_published"}),
 *         @ORM\Index(name="idx_n_coupon_date_unpublished", columns={"date_unpublished"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idx_u_coupon_id", columns={"id"})}
 * )
 */
class Coupon extends CoreLocalizableEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, length=155, nullable=false)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="decimal", unique=true, length=10, nullable=false)
     */
    private $discount;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    private $limit_redeem;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=true)
     */
    private $limit_order_total;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=true)
     */
    private $limit_discount;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_published;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_unpublished;

    /**
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $type_usage;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $total_discount_redeemed;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $validity;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $total_order_amount;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    private $count_redeemed;

    /**
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Bundle\ShoppingCartBundle\Entity\CouponLocalization",
     *     mappedBy="coupon"
     * )
     */
    protected $localizations;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     */
    private $site;

    /** ****************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                    *
     * *****************************************************************/

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
    public function getId() {
        return $this->id;
    }

    /**
     * @name            setCode ()
     *                  Sets the code property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $code
     *
     * @return          object                $this
     */
    public function setCode($code) {
        if (!$this->setModified('code', $code)->isModified()) {
            return $this;
        }
        $this->code = $code;
        return $this;
    }

    /**
     * @name            getCode ()
     *                  Returns the value of code property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->code
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @name            setDatePublished ()
     *                  Sets the date_published property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_published
     *
     * @return          object                $this
     */
    public function setDatePublished($date_published) {
        if (!$this->setModified('date_published', $date_published)->isModified()) {
            return $this;
        }
        $this->date_published = $date_published;
        return $this;
    }

    /**
     * @name            getDatePublished ()
     *                  Returns the value of date_published property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_published
     */
    public function getDatePublished() {
        return $this->date_published;
    }

    /**
     * @name            setDateUnpublished ()
     *                  Sets the date_unpublished property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $date_unpublished
     *
     * @return          object                $this
     */
    public function setDateUnpublished($date_unpublished) {
        if (!$this->setModified('date_unpublished', $date_unpublished)->isModified()) {
            return $this;
        }
        $this->date_unpublished = $date_unpublished;
        return $this;
    }

    /**
     * @name            getDateUnpublished ()
     *                  Returns the value of date_unpublished property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->date_unpublished
     */
    public function getDateUnpublished() {
        return $this->date_unpublished;
    }

    /**
     * @name            setDiscount ()
     *                  Sets the discount property.
     *                  Updates the data only if stored value and value to be set are different.
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
        if (!$this->setModified('discount', $discount)->isModified()) {
            return $this;
        }
        $this->discount = floatval($discount);
        return $this;
    }

    /**
     * @name            getDiscount ()
     *                  Returns the value of discount property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->discount
     */
    public function getDiscount() {
        return floatval($this->discount);
    }

    /**
     * @name            setLimitDiscount ()
     *                  Sets the limit_discount property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $limit_discount
     *
     * @return          object                $this
     */
    public function setLimitDiscount($limit_discount) {
        if (!$this->setModified('limit_discount', $limit_discount)->isModified()) {
            return $this;
        }
        $this->limit_discount = floatval($limit_discount);
        return $this;
    }

    /**
     * @name            getLimitDiscount ()
     *                                   Returns the value of limit_discount property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->limit_discount
     */
    public function getLimitDiscount() {
        return floatval($this->limit_discount);
    }

    /**
     * @name            setLimitOrderTotal ()
     *                  Sets the limit_order_total property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $limit_order_total
     *
     * @return          object                $this
     */
    public function setLimitOrderTotal($limit_order_total) {
        if (!$this->setModified('limit_order_total', $limit_order_total)->isModified()) {
            return $this;
        }
        $this->limit_order_total = floatval($limit_order_total);
        return $this;
    }

    /**
     * @name            getLimitOrderTotal ()
     *                                     Returns the value of limit_order_total property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->limit_order_total
     */
    public function getLimitOrderTotal() {
        return floatval($this->limit_order_total);
    }

    /**
     * @name            setLimitRedeem ()
     *                  Sets the limit_redeem property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $limit_redeem
     *
     * @return          object                $this
     */
    public function setLimitRedeem($limit_redeem) {
        if (!$this->setModified('limit_redeem', $limit_redeem)->isModified()) {
            return $this;
        }
        $this->limit_redeem = $limit_redeem;
        return $this;
    }

    /**
     * @name            getLimitRedeem ()
     *                  Returns the value of limit_redeem property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->limit_redeem
     */
    public function getLimitRedeem() {
        return $this->limit_redeem;
    }

    /**
     * @name            setValidity()
     *                  Sets the validty property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.5
     * @version         1.0.5
     *
     * @use             $this->setModified()
     *
     * @param           string                $json             json string
     *
     * @return          object                $this
     */
    public function setValidity($json) {
        if (!$this->setModified('validity', $json)->isModified()) {
            return $this;
        }
        $this->validity = $json;
        return $this;
    }

    /**
     * @name            getValidity()
     *                  Returns the value of validity property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.5
     * @version         1.0.5
     *
     * @return          mixed           $this->validity
     */
    public function getValidity() {
        return $this->validity;
    }

    /**
     * @name            setSite ()
     *                  Sets the site property.
     *                  Updates the data only if stored value and value to be set are different.
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
        if (!$this->setModified('site', $site)->isModified()) {
            return $this;
        }
        $this->site = $site;
        return $this;
    }

    /**
     * @name            getSite ()
     *                  Returns the value of site property.
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
     * @name            setType ()
     *                  Sets the type property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $type
     *
     * @return          object                $this
     */
    public function setType($type) {
        if (!$this->setModified('type', $type)->isModified()) {
            return $this;
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @name            getType ()
     *                  Returns the value of type property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @name            setTypeUsage ()
     *                  Sets the type_usage property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $type_usage
     *
     * @return          object                $this
     */
    public function setTypeUsage($type_usage) {
        if (!$this->setModified('type_usage', $type_usage)->isModified()) {
            return $this;
        }
        $this->type_usage = $type_usage;
        return $this;
    }

    /**
     * @name            getTypeUsage ()
     *                  Returns the value of type_usage property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->type_usage
     */
    public function getTypeUsage() {
        return $this->type_usage;
    }

    /**
     * @name            setTotalDiscountRedeemed ()
     *                  Sets the total_discount_redeemed property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.3
     * @version         1.0.3
     *
     * @use             $this->setModified()
     *
     * @param           mixed $total_discount_redeemed
     *
     * @return          object                $this
     */
    public function setTotalDiscountRedeemed($total_discount_redeemed) {
        if ($this->setModified('total_discount_redeemed', $total_discount_redeemed)->isModified()) {
            $this->total_discount_redeemed = $total_discount_redeemed;
        }

        return $this;
    }

    /**
     * @name            getTotalDiscountRedeemed ()
     *                  Returns the value of total_discount_redeemed property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.3
     * @version         1.0.3
     *
     * @return          mixed           $this->total_discount_redeemed
     */
    public function getTotalDiscountRedeemed() {
        return $this->total_discount_redeemed;
    }

    /**
     * @name            setTotalOrderAmount ()
     *                  Sets the total_order_amount property.
     *                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.3
     * @version         1.0.3
     *
     * @use             $this->setModified()
     *
     * @param           mixed $total_order_amount
     *
     * @return          object                $this
     */
    public function setTotalOrderAmount($total_order_amount) {
        if ($this->setModified('total_order_amount', $total_order_amount)->isModified()) {
            $this->total_order_amount = floatval($total_order_amount);
        }

        return $this;
    }

    /**
     * @name            getTotalOrderAmount ()
     *                  Returns the value of total_order_amount property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.3
     * @version         1.0.3
     *
     * @return          mixed           $this->total_order_amount
     */
    public function getTotalOrderAmount() {
        return floatval($this->total_order_amount);
    }

    /**
     * @name           setCountRedeemed ()
     *                 Sets the count_redeemed property.
     *                 Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.3
     * @version         1.0.3
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_redeemed
     *
     * @return          object                $this
     */
    public function setCountRedeemed($count_redeemed) {
        if ($this->setModified('count_redeemed', $count_redeemed)->isModified()) {
            $this->count_redeemed = $count_redeemed;
        }

        return $this;
    }

    /**
     * @name            getCountRedeemed ()
     *                  Returns the value of count_redeemed property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.3
     * @version         1.0.3
     *
     * @return          mixed           $this->count_redeemed
     */
    public function getCountRedeemed() {
        return $this->count_redeemed;
    }

}

/**
 * Change Log:
 * **************************************
 * v1.0.5                      Can Berkol
 * 09.05.2014
 * **************************************
 * A getValidity()
 * A setValidity()
 * D getMember()
 * D getMemberGroup()
 * D getProduct()
 * D getProductCategory()
 * D setMember()
 * D setMemberGroup()
 * D setProduct()
 * D setProductCategory()
 *
 * **************************************
 * v1.0.4                      Can Berkol
 * 30.01.2014
 * **************************************
 * A getCountRedeemed()
 * A getTotalDiscountRedeemed()
 * A getTotalOrderAmount()
 * A setCountRedeemed()
 * A setTotalDiscountRedeemed()
 * A setTotalOrderAmount()
 *
 * * * **********************************
 * v1.0.2                      Murat Ünal
 * 11.10.2013
 * **************************************
 * A getLocalizations()
 * A setLocalizations()
 * * ************************************
 * v1.0.1                      Murat Ünal
 * 11.10.2013
 * **************************************
 * D get_redeemed_coupons()
 * D set_redeemed_coupons()
 * D getCoupon_localizations()
 * D setCoupon_localizations()
 * **************************************
 * v1.0.0                      Murat Ünal
 * 23.09.2013
 * **************************************
 * A getCode()
 * A getCoupon_localizations()
 * A getDatePublished()
 * A getDateUnpublished()
 * A getDiscount()
 * A getId()
 * A getLimitDiscount()
 * A getLimitOrderTotal()
 * A getLimitRedeem()
 * A getMember()
 * A getMemberGroup()
 * A getProduct()
 * A getProductCategory()
 * A get_redeemed_coupons()
 * A getSite()
 * A getType()
 * A getTypeUsage()
 *
 * A setCode()
 * A setCoupon_localizations()
 * A setDatePublished()
 * A setDateUnpublished()
 * A setDiscount()
 * A setLimitDiscount()
 * A setLimitOrderTotal()
 * A setLimitRedeem()
 * A setMember()
 * A setMemberGroup()
 * A setProduct()
 * A setProductCategory()
 * A setSite()
 * A setType()
 * A setTypeUsage()
 *
 */