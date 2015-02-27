<?php
/**
 * @name        ShoppingOrderStatusLocalization
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
 *     name="shopping_order_status_localization",
 *     options={"charset":"utf8","collate":"utf8_turkish_ci","engine":"innodb"},
 *     indexes={
 *         @ORM\Index(name="idx_u_shopping_order_status", columns={"status","language"}),
 *         @ORM\Index(name="idx_u_shopping_order_status_url_key", columns={"language","url_key"})
 *     }
 * )
 */
class ShoppingOrderStatusLocalization extends CoreEntity
{
    /** 
     * @ORM\Column(type="string", length=155, nullable=false)
     */
    private $name;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $url_key;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="BiberLtd\Bundle\ShoppingCartBundle\Entity\ShoppingOrderStatus",
     *     inversedBy="localizations"
     * )
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $status;


    /** 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="BiberLtd\Bundle\MultiLanguageSupportBundle\Entity\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $language;

    /**
     * @name                  setLanguage ()
     *                                    Sets the language property.
     *                                    Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $language
     *
     * @return          object                $this
     */
    public function setLanguage($language) {
        if(!$this->setModified('language', $language)->isModified()) {
            return $this;
        }
		$this->language = $language;
		return $this;
    }

    /**
     * @name            getLanguage ()
     *                              Returns the value of language property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->language
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * @name                  setName ()
     *                                Sets the name property.
     *                                Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $name
     *
     * @return          object                $this
     */
    public function setName($name) {
        if(!$this->setModified('name', $name)->isModified()) {
            return $this;
        }
		$this->name = $name;
		return $this;
    }

    /**
     * @name            getName ()
     *                          Returns the value of name property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @name            setShoppingOrderStatus ()
     *                  Sets the shopping_order_status property.
     *                  Updates the data only if stored value and value to be set are different.
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
     * @name            getShoppingOrderStatus ()
     *                  Returns the value of shopping_order_status property.
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
     * @name                  setUrlKey ()
     *                                  Sets the url_key property.
     *                                  Updates the data only if stored value and value to be set are different.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @use             $this->setModified()
     *
     * @param           mixed $url_key
     *
     * @return          object                $this
     */
    public function setUrlKey($url_key) {
        if(!$this->setModified('url_key', $url_key)->isModified()) {
            return $this;
        }
		$this->url_key = $url_key;
		return $this;
    }

    /**
     * @name            getUrlKey ()
     *                            Returns the value of url_key property.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          mixed           $this->url_key
     */
    public function getUrlKey() {
        return $this->url_key;
    }
    /******************************************************************
     * PUBLIC SET AND GET FUNCTIONS                                   *
     ******************************************************************/

}
/**
 * Change Log:
 * **************************************
 * v1.0.0                      Murat Ünal
 * 23.09.2013
 * **************************************
 * A getLanguage()
 * A getName()
 * A getShoppingOrderStatus()
 * A getUrlKey()
 *
 * A setLanguage()
 * A setName()
 * A setShoppingOrderStatus()
 * A setUrlKey()
 *
 */