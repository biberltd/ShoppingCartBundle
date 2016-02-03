<?php
/**
 * @author		Can Berkol
 * @author		Sid İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        27.12.2015
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
 *         @ORM\Index(name="idxNCouponDatePublished", columns={"date_published"}),
 *         @ORM\Index(name="idxNCouponDateUnpublished", columns={"date_unpublished"})
 *     },
 *     uniqueConstraints={@ORM\UniqueConstraint(name="idxUCouponId", columns={"id"})}
 * )
 */
class Coupon extends CoreLocalizableEntity {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, length=155, nullable=false)
     * @var string
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"a"})
     * @var string
     */
    private $type;

    /**
     * @ORM\Column(type="decimal", unique=true, length=10, nullable=false, options={"default":0})
     * @var float
     */
    private $discount;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     * @var int
     */
    private $limit_redeem;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=true)
     * @var float
     */
    private $limit_order_total;

    /**
     * @ORM\Column(type="decimal", length=10, nullable=true)
     * @var float
     */
    private $limit_discount;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $date_published;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $date_unpublished;

    /**
     * @ORM\Column(type="string", length=1, nullable=false, options={"default":"s"})
     * @var string
     */
    private $type_usage;

    /**
     * @ORM\Column(type="decimal", nullable=true, options={"default":0})
     * @var float
     */
    private $total_discount_redeemed;

    /**
     * @ORM\Column(type="text", nullable=false, options={"default":"unlimited"})
     * @var string
     */
    private $validity;

    /**
     * @ORM\Column(type="decimal", nullable=true, options={"default":0})
     * @var float
     */
    private $total_order_amount;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true, options={"default":0})
     * @var int
     */
    private $count_redeemed;

    /**
     * @ORM\OneToMany(targetEntity="CouponLocalization", mappedBy="coupon")
     * @var array
     */
    protected $localizations;

    /**
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\SiteManagementBundle\Entity\Site")
     * @ORM\JoinColumn(name="site", referencedColumnName="id", onDelete="CASCADE")
     * @var \BiberLtd\Bundle\SiteManagementBundle\Entity\Site
     */
    private $site;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode(string $code) {
        if (!$this->setModified('code', $code)->isModified()) {
            return $this;
        }
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param \DateTime $date_published
     *
     * @return $this
     */
    public function setDatePublished(\DateTime $date_published) {
        if (!$this->setModified('date_published', $date_published)->isModified()) {
            return $this;
        }
        $this->date_published = $date_published;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDatePublished() {
        return $this->date_published;
    }

    /**
     * @param \DateTime $date_unpublished
     *
     * @return $this
     */
    public function setDateUnpublished(\DateTime $date_unpublished) {
        if (!$this->setModified('date_unpublished', $date_unpublished)->isModified()) {
            return $this;
        }
        $this->date_unpublished = $date_unpublished;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateUnpublished() {
        return $this->date_unpublished;
    }

    /**
     * @param float $discount
     *
     * @return $this
     */
    public function setDiscount(float $discount) {
        if (!$this->setModified('discount', $discount)->isModified()) {
            return $this;
        }
        $this->discount = floatval($discount);
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscount() {
        return floatval($this->discount);
    }

    /**
     * @param float $limit_discount
     *
     * @return $this
     */
    public function setLimitDiscount(float $limit_discount) {
        if (!$this->setModified('limit_discount', $limit_discount)->isModified()) {
            return $this;
        }
        $this->limit_discount = floatval($limit_discount);
        return $this;
    }

    /**
     * @return float
     */
    public function getLimitDiscount() {
        return floatval($this->limit_discount);
    }

    /**
     *     object                $this
     */
    public function setLimitOrderTotal(float $limit_order_total) {
        if (!$this->setModified('limit_order_total', $limit_order_total)->isModified()) {
            return $this;
        }
        $this->limit_order_total = floatval($limit_order_total);
        return $this;
    }

    /**
     * @return float
     */
    public function getLimitOrderTotal() {
        return floatval($this->limit_order_total);
    }

    /**
     * @param int $limit_redeem
     *
     * @return $this
     */
    public function setLimitRedeem(int $limit_redeem) {
        if (!$this->setModified('limit_redeem', $limit_redeem)->isModified()) {
            return $this;
        }
        $this->limit_redeem = $limit_redeem;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimitRedeem() {
        return $this->limit_redeem;
    }

    /**
     * @param string $validity
     *
     * @return $this
     */
    public function setValidity(string $validity) {
        if (!$this->setModified('validity', $validity)->isModified()) {
            return $this;
        }
        $this->validity = $validity;
        return $this;
    }

    /**
     * @return string
     */
    public function getValidity() {
        return $this->validity;
    }

    /**
     * @param \BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site
     *
     * @return $this
     */
    public function setSite(\BiberLtd\Bundle\SiteManagementBundle\Entity\Site $site) {
        if (!$this->setModified('site', $site)->isModified()) {
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
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type) {
        if (!$this->setModified('type', $type)->isModified()) {
            return $this;
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $type_usage
     *
     * @return $this
     */
    public function setTypeUsage(string $type_usage) {
        if (!$this->setModified('type_usage', $type_usage)->isModified()) {
            return $this;
        }
        $this->type_usage = $type_usage;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeUsage() {
        return $this->type_usage;
    }

    /**
     * @param float $total_discount_redeemed
     *
     * @return $this
     */
    public function setTotalDiscountRedeemed(float $total_discount_redeemed) {
        if ($this->setModified('total_discount_redeemed', $total_discount_redeemed)->isModified()) {
            $this->total_discount_redeemed = $total_discount_redeemed;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalDiscountRedeemed() {
        return $this->total_discount_redeemed;
    }

    /**
     * @param float $total_order_amount
     *
     * @return $this
     */
    public function setTotalOrderAmount(float $total_order_amount) {
        if ($this->setModified('total_order_amount', $total_order_amount)->isModified()) {
            $this->total_order_amount = floatval($total_order_amount);
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalOrderAmount() {
        return floatval($this->total_order_amount);
    }

    /**
     * @param int $count_redeemed
     *
     * @return $this
     */
    public function setCountRedeemed(int $count_redeemed) {
        if ($this->setModified('count_redeemed', $count_redeemed)->isModified()) {
            $this->count_redeemed = $count_redeemed;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getCountRedeemed() {
        return $this->count_redeemed;
    }

}