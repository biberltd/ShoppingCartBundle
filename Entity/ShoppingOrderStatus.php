<?php
/**
 * @name        ShoppingOrderStatus
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
namespace BiberLtd\Core\Bundles\ShoppingCartBundle\Entity;
use Doctrine\ORM\Mapping AS ORM;
use BiberLtd\Core\CoreLocalizableEntity;
/** 
 * @ORM\Entity
 * @ORM\Table(name="shopping_order_status", options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"})
 */
class ShoppingOrderStatus extends CoreLocalizableEntity
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer", length=5)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $date_added;

    /** 
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $count_orders;

    /** 
     * @ORM\OneToMany(
     *     targetEntity="BiberLtd\Core\Bundles\ShoppingCartBundle\Entity\ShoppingOrderStatusLocalization",
     *     mappedBy="status"
     * )
     */
    protected $localizations;

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
     * @name                  setCountOrders ()
     *                                       Sets the count_orders property.
     *                                       Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $count_orders
     *
     * @return          object                $this
     */
    public function setCountOrders($count_orders) {
        if(!$this->setModified('count_orders', $count_orders)->isModified()) {
            return $this;
        }
		$this->count_orders = $count_orders;
		return $this;
    }

    /**
     * @name            getCountOrders ()
     *                                 Returns the value of count_orders property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->count_orders
     */
    public function getCountOrders() {
        return $this->count_orders;
    }
}
/**
 * Change Log:
 * * * **********************************
 * v1.0.1                      Murat Ünal
 * 11.10.2013
 * **************************************
 * D getShoppingOrderStatus_localizations()
 * D setShoppingOrderStatus_localizations()
 * D getShoppingOrders()
 * D setShoppingOrders()
 * * ************************************
 * v1.0.1                      Murat Ünal
 * 11.10.2013
 * **************************************
 * A getLocalizations()
 * A setLocalizations()
 * **************************************
 * v1.0.0                      Murat Ünal
 * 23.09.2013
 * **************************************
 * A getCountOrders()
 * A getDateAdded()
 * A getId()
 * A getShoppingOrderStatus_localizations()
 * A getShoppingOrders()
 *
 * A setCountOrders()
 * A setDateAdded()
 * A setShoppingOrderStatus_localizations()
 * A setShoppingOrders()
 *
 */