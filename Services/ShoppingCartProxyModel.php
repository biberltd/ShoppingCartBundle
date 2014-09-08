<?php
/**
 * ShoppingCartProxyModel Class
 *
 * This class acts as a database proxy model for ShoppingCartBundle functionality.
 *
 * @vendor      BiberLtd
 * @package        Core\Bundles\ShoppingCartBundle
 * @subpackage    Services
 * @name        ShoppingCartProxyModel
 *
 * @author        Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.8
 * @date        08.07.2014
 *
 */
namespace BiberLtd\Bundle\ShoppingCartBundle\Services;

/** Extends CoreModel */
use BiberLtd\Core\CoreModel;

/** Entities to be used */
use BiberLtd\Bundle\ShoppingCartBundle\Entity as BundleEntity;

/** Helper Service*/
use Doctrine\Common\Collections\ArrayCollection;

class ShoppingCartProxyModel extends CoreModel
{
    /**
     * @name            $cart
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    private $cart;
    /**
     * @name            $date
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.3
     * @version         1.0.3
     *
     */
    private $date;
    /**
     * @name            $cartModel
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.5
     * @version         1.0.5
     *
     */
    private $cartModel;
    /**
     * @name            $memberId
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.5
     * @version         1.0.5
     *
     */
    private $memberId;
    /**
     * @name            $debug
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.6
     * @version         1.0.6
     *
     */
    private $debug;
    /**
     * @name            $cartUpdated
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.6
     * @version         1.0.6
     *
     */
    private $cartUpdated;

    /**
     * @name            __construct ()
     *                  Constructor.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.5
     *
     * @param           object $kernel
     * @param           string $db_connection Database connection key as set in app/config.yml
     * @param           string $orm ORM that is used.
     */
    public function __construct($kernel, $db_connection = 'default', $orm = 'doctrine')
    {
        parent::__construct($kernel, $db_connection, $orm);
        /** INITIALIZING SOME USEFUL STAFF */
        $this->cartModel = $this->kernel->getContainer()->get('shoppingcart.model');
        $this->date = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        $this->request = $this->kernel->getContainer()->get('request');
        $this->sessionManager = $this->kernel->getContainer()->get('session_manager');
        $this->session = $this->kernel->getContainer()->get('session');
        $this->cookies = $this->request->cookies;
        $this->memberId = $this->sessionManager->get_detail('id');
//        $this->session->set('bbr_cart', false);
        $this->debug = false;
        /**
         * INITIALIZE CART
         */
        $debugStr = '';
        $cart = $this->initCart();
        /** If cookie has no cart then write it to cookie.  */
        if ($this->doesCookiesHasCart()) {
            $debugStr .= 'cookie has cart id<br>';
            /** If cookie cart id and id of cart not equal then write new cart id to cookie. */
            if ($cart->getId() != $this->getCartFromCookie()) {
                /** Save cart to cookie */
                $debugStr .= 'cart id in cookie and cart id in db are not same, cookie will be updated.<br>';
                $cart->setSaveCookie(true);
            }
        } else {
            $debugStr .= 'cookie has not and cart id so bbr_cart will be added to cookie.<br>';
            $cart->setSaveCookie(true);
        }
        /** Set cart */
        $this->setCart($cart);
        if ($cart->getSaveCookie()) {
            setcookie('bbr_cart', $this->encryptCookie($cart->getId()), time() + 60 * 60 * 24 * 30, '/', "kullanatpazari.biberltd.com", false, false);
        }
        if ($this->debug) {
            echo $debugStr;
        }

    }

    /**
     * @name            initCart ()
     *                  Initializes cart
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.5
     * @version         1.0.6
     *
     * @return BundleEntity\Proxy\CartProxyEntity
     */
    public function initCart()
    {
        $debugStr = '';
        $debugStr = 'cart initialization started<br>';
        $checkCookieForCart = false;
        $memberHasCart = false;
        /** Does session has cart object? */
        if ($this->doesSessionHasCart()) {
            $debugStr .= 'session has cart<br>';
            $cart = $this->getCartFromSession();
        } else {
            $debugStr .= 'session has not cart<br>';
            $debugStr .= 'getting cart from db with session id<br>';
            $response = $this->getCartFromDbBySessionId();
            if (!$response['error']) {
                $debugStr .= 'cart found from db with session id<br>';
                $cart = $response['result']['set'];
            }else{
                /** Is user logged in? */
                if ($this->isUserLoggedIn()) {
                    $debugStr .= 'user logged in<br>';
                    /** Does member has available cart in database? */
                    $debugStr .= 'getting cart from db with member id(' . $this->memberId . ')<br>';
                    $response = $this->getCartFromDbByMember($this->memberId);
                    if (!$response['error']) {
                        $debugStr .= 'member has cart in db<br>';
                        $cart = $response['result']['set'];
                        $memberHasCart = true;
                    } else {
                        /** Does cookie has cart? */
                        $debugStr .= 'member has not cart in db<br>';
                        $checkCookieForCart = true;
                    }
                    unset($response);

                } else {
                    /** Does cookie has cart? */
                    $checkCookieForCart = true;
                }
            }

            /** If member has not available cart in db */
            if (!isset($cart)) {
                /** Does cookie exist in cart */
                $debugStr .= 'cart not found from database with session id. <br> checking cookie for cart<br>';
                if ($checkCookieForCart && $this->doesCookiesHasCart()) {
                    $cartId = $this->getCartFromCookie();
                    $response = $this->getCartFromDbByCartId($cartId);
                    if (!$response['error']) {
                        $debugStr .= 'cart found from database with cookie<br>';
                        $cart = $response['result']['set'];
                        if (!is_null($cart->getMember())) {
                            if ($this->isUserLoggedIn()) {
                                if ($cart->getMember()->getId() != $this->sessionManager->get_detail('id')) {
                                    $debugStr .= 'cart owner is different. destroying cart<br>';
                                    unset($cart);
                                }
                            }else{
                                $debugStr .= 'cart owner is different. destroying cart<br>';
                                unset($cart);
                            }
                        }

                    } else {
                        $debugStr .= 'cart not found from database with member or cookie cart id<br>';
                    }
                    unset($response);
                } else {
                    $debugStr .= 'cookie has not cart<br>';
                }
            }
        }
        /** If $cart is not set */
        if (!isset($cart)) {
            $debugStr .= 'cart is not created yet,creating cart<br>';
            /** Create new cart */
            $cart = $this->createCart();
        }
        /** If user logged in update member and session of  cart */
        if ($this->isUserLoggedIn() && $cart instanceof BundleEntity\ShoppingCart) {
            $updateCart = false;
            if (!is_null($cart->getMember())) {
                if ($cart->getMember()->getId() != $this->sessionManager->get_detail('id')) {
                    $debugStr .= 'cart assigned to member(' . $this->memberId . ')<br>';
                    $memberModel = $this->kernel->getContainer()->get('membermanagement.model');
                    $response = $memberModel->getMember($this->memberId, 'id');
                    if (!$response['error']) {
                        $memberEntity = $response['result']['set'];
                        $cart->setMember($memberEntity);
                    }
                    $updateCart = true;
                    unset($response);
                }

            }

            if ($cart->getSession()->getSessionId() != $this->session->getId()) {
                $debugStr .= 'cart assigned to this session<br>';
                $logModel = $this->kernel->getContainer()->get('logbundle.model');
                $response = $logModel->getSession($this->session->getId(), 'session_id');
                if (!$response['error']) {
                    $cart->setSession($response['result']['set']);
                }
                unset($response);
                $updateCart = true;
            }
            if ($updateCart) {
                $this->cartModel->updateShoppingCart($cart);
                /** After updating cart we need to change member property to integer value of member id */
                $cart->setMember($this->memberId);
            }
        }
        /** If cart is not instance of CartProxyEntity then convert it  */
        if (!$cart instanceof BundleEntity\Proxy\CartProxyEntity && $cart instanceof BundleEntity\ShoppingCart) {
            $debugStr .= 'cart is converting to proxy <br>';
            $cart = $this->convertToProxyCart($cart);
        }
        $debugStr .= 'cart is set succesfully<br>';
        $debugStr .= 'cart initialization finished<br>';
        if ($this->debug) {
            echo $debugStr;
        }
        return $cart;
    }

    /**
     * @name            saveCartToCookie ()
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.5
     * @version         1.0.5
     *
     * @return BundleEntity\Proxy\CartProxyEntity
     */
    public function saveCartToCookie()
    {
        $cart = $this->session->get('bbr_cart');
        if ($cart instanceof BundleEntity\Proxy\CartProxyEntity) {
            return true;
        }
        return false;
    }

    /**
     * @name            doesSessionHasCart ()
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.5
     * @version         1.0.5
     *
     * @return BundleEntity\Proxy\CartProxyEntity
     */
    public function doesSessionHasCart()
    {
        $cart = $this->session->get('bbr_cart');
        if ($cart instanceof BundleEntity\Proxy\CartProxyEntity) {
            return true;
        }
        return false;
    }

    /**
     * @name            getCartFromSession ()
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.5
     * @version         1.0.5
     *
     * @return BundleEntity\Proxy\CartProxyEntity
     */
    public function getCartFromSession()
    {
        return $this->session->get('bbr_cart');
    }

    /**
     * @name            isUserLoggedIn ()
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.5
     * @version         1.0.5
     *
     * @return BundleEntity\Proxy\CartProxyEntity
     */
    public function isUserLoggedIn()
    {
        if ($this->session->get('is_logged_in') === true) {
            return true;
        }
        return false;
    }

    /**
     * @name            doesCookiesHasCart ()
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.5
     * @version         1.0.5
     *
     * @return mixed
     */
    public function doesCookiesHasCart()
    {
        /** If cookie has then set $cartFromCookie */
        $cartFromCookie = (int)$this->decryptCookie($this->cookies->get('bbr_cart'));
        if (isset($cartFromCookie) && is_int($cartFromCookie) && $cartFromCookie > 0) {
            return $cartFromCookie;
        }
        return false;
    }

    /**
     * @name            getCartFromCookie ()
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.5
     * @version         1.0.5
     *
     * @return int
     */
    public function getCartFromCookie()
    {
        return (int)$this->decryptCookie($this->cookies->get('bbr_cart'));
    }

    /**
     * @name            createCart ()
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return BundleEntity\Proxy\CartProxyEntity
     */
    public function createCart()
    {
        $cart = new \stdClass();
        if ($this->isUserLoggedIn()) {
            $cart->member = $this->sessionManager->get_detail('id');
        }
        $sessionId = $this->sessionManager->getId();
        if (!$sessionId) {
            $sessionId = $this->kernel->getContainer()->get('session')->getId();
        }
        /** Get session from database with $sessionId */
        $logModel = $this->kernel->getContainer()->get('logbundle.model');
        $response = $logModel->getSession($sessionId, 'session_id');
        if ($response['error']) {
            return $response;
        }
        $cart->session = $response['result']['set']->getId();
        $cart->total_amount = 0;
        $cart->count_items = 0;
        $response = $this->cartModel->insertShoppingCart($cart);
        return $response['result']['set'][0];
    }

    /**
     * @name            getCart ()
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param   bool $convertItemsToArray
     * @return BundleEntity\Proxy\CartProxyEntity
     */
    public function getCart($convertItemsToArray = false)
    {
        if ($convertItemsToArray) {
            $newCollection = array();
            foreach ($this->listCartItems(true) as $item) {
                $newCollection[] = $item;
            }
            $this->cart->setItems($newCollection);
            unset($newCollection);
        }
        return $this->cart;

    }

    /**
     * @name            setCart ()
     *                  Sets $cart property
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.1
     * @version         1.0.5
     *
     * @return  $this
     */
    public function setCart($cart)
    {
        $this->saveToSession($cart);
        $this->cart = $cart;
        $debugStr = '';
        if ($this->cartUpdated != true) {
            /** Calculate date difference and if interval higher than 5 minute then write cart to database */
            $interval = date_diff($this->date, $cart->getDateUpdated())->format('%R%i');
            $debugStr .= 'interval is ' . $interval . ' <br>';
            if ($interval <= -1) {
                $debugStr .= 'cart date is older than 5mins so cart will be updated in db.<br>';
                $response = $this->saveToDb($cart);
                if (isset($response['error']) && $response['error']) {
                    return $response;
                }
                unset($response);
            }
        }
        if ($this->debug) {
            echo $debugStr;
        }

        return $this;
    }

    /**
     * @name            setCartAfterSaveToDb ()
     *                  Sets $cart property
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.6
     * @version         1.0.6
     *
     * @return  $this
     */
    public function setCartAfterSaveToDb($cart)
    {
        $this->saveToSession($cart);
        $this->cart = $cart;
        return $this;
    }

    /**
     * @name            update ()
     *
     * @author          Said İmamoğlu
     *
     * @param           $cart
     * @since           1.0.1
     * @version         1.0.1
     *
     */
    public function updateCart($cart)
    {
        $this->setCart($cart);
        if ($cart instanceof BundleEntity\ShoppingCart) {
            $this->setCart($this->convertToSessionObject());
        }
    }

    /**
     * @name            __destruct ()
     *                  Destructor.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.
     * @version         1.0.0
     *
     */
    public function __destruct()
    {
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }

    /**
     * @name            convertToDatabaseObject ()
     *                  Convert given object to shopping cart proxy entity
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.6
     *
     * @param       BundleEntity\Proxy\CartProxyEntity $cart
     *
     * @return array
     */
    public function convertToDatabaseObject($cart)
    {
        /** Find cart */
        $response = $this->cartModel->getShoppingCart($cart->getId());
        if ($response['error']) {
            return $response;
        }
        $cartEntity = $response['result']['set'];
        unset($response);
        /** Get member entity */
        if (!is_null($cartEntity->getMember()) && ($cart->getOwnerId() != $cartEntity->getMember()->getId())) {
            $memberModel = $this->kernel->getContainer()->get('membermanagement.model');
            $response = $memberModel->getMember($this->sessionManager->get_detail('id'));
            if ($response['error']) {
                return $response;
            }
            $member = $response['result']['set'];
            unset($response);
        }

        /** Get session entity */
        if ($cart->getInitializedSession() != $cartEntity->getSession()->getId()) {
            $logBundle = $this->kernel->getContainer()->get('logbundle.model');
            $response = $logBundle->getSession($cart->getInitializedSession());
            if ($response['error']) {
                return $response;
            }
            $session = $response['result']['set'];
            unset($response);
        }
        if (isset($member)) {
            $cartEntity->setMember($member);
        }
        if (isset($session)) {
            $cartEntity->setSession($session);
        }
        $cartEntity->setDateCreated($cart->getDateCreated());
        $cartEntity->setDateOrdered($cart->getCartToOrderDate());
        $cartEntity->setDateUpdated($cart->getDateUpdated());
        $cartEntity->setDateCancelled($cart->getDateCancelled());
        $cartEntity->setTotalAmount($cart->getTotalAmount());
        $cartEntity->setCountItems(count($cart->getItems()));
        /** Converting items */
        $items = array();
        foreach ($cart->getItems() as $item) {
            /** Get shopping cart item entity */
            $cartItemEntity = new BundleEntity\ShoppingCartItem();
            if (!is_null($item->getId())) {
                $response = $this->cartModel->getShoppingCartItem($item->getId());
                if (!$response['error']) {
                    $cartItemEntity = $response['result']['set'];
                }
            }
            /** Get product entity */
            if (!is_null($item->getProduct())) {
                $productModel = $this->kernel->getContainer()->get('productmanagement.model');
                $response = $productModel->getProduct($item->getProduct());
                if ($response['error']) {
                    return $response;
                }
                $cartItemEntity->setProduct($response['result']['set']);
                unset($response);
            }
            $cartItemEntity->setCart($cartEntity);
            $cartItemEntity->setQuantity($item->getQuantity());
            $cartItemEntity->setPrice($item->getPrice());
            /** If discounted price of product is set then update product price */
            if ($item->getDiscountedPrice() > 0) {
                $cartItemEntity->setPrice($item->getDiscountedPrice());
            }
            $cartItemEntity->setSubtotal($item->getTotalAmount() - $item->getTaxAmount());
            /** @todo   Said İmamoğlu : date added and date updated properties will be added to cartItemProxy entity */
            $cartItemEntity->setDateAdded($this->date);
            $cartItemEntity->setDateUpdated($this->date);
            $cartItemEntity->setTax($item->getTaxRate());
            $cartItemEntity->setDiscount($item->getDiscount());
            $cartItemEntity->setTotal($item->getTotalAmount());
            $cartItemEntity->setPackageType($item->getBoxType());
            $items[] = $cartItemEntity;
            unset($response);
        }
        $currentCart['entity'] = $cartEntity;
        $currentCart['items'] = $items;
        return $currentCart;
    }

    /**
     * @name            convertToProxyCart ()
     *                  Convert given object to shopping cart proxy entity
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.5
     *
     * @param       BundleEntity\ShoppingCart $cart
     *
     * @return mixed
     */
    public function convertToProxyCart($cart)
    {
        if ($cart instanceof BundleEntity\ShoppingCart) {
            $newCart = new BundleEntity\Proxy\CartProxyEntity($this->kernel);
            $fields = array(
                'id' => 'id',
                'date_created' => 'dateCreated',
                'date_updated' => 'dateUpdated',
                'date_ordered' => 'cartToOrderDate',
                'member' => 'ownerId',
                'total_amount' => 'totalAmount',
                'session' => 'initializedSession',
            );

            /** Columns which use getId() method */
            $fieldsWhichUseId = array('member', 'session');

            foreach ($fields as $db => $proxy) {
                $proxy = 'set' . ucfirst($proxy);
                if (property_exists($cart, $db)) {
                    $dbMethod = 'get' . ucfirst($this->translateColumnName($db));
                    if (in_array($db, $fieldsWhichUseId) && is_object($cart->{$dbMethod}())) {
                        $newCart->$proxy($cart->$dbMethod()->getId());
                    } else {
                        $newCart->$proxy($cart->$dbMethod());
                    }

                }
            }

            /**
             * Convert items to proxy cart item object and add them to proxy cart object.
             */
            $this->cartModel = $this->kernel->getContainer()->get('shoppingcart.model');
            $response = $this->cartModel->listItemsOfShoppingCart($cart->getId());
            (float)$totalPrice = 0;
            if (!$response['error']) {
                foreach ($response['result']['set'] as $cartItem) {
                    /**
                     * Get categories of product as array
                     */
                    $productModel = $this->kernel->getContainer()->get('productmanagement.model');
                    $response = $productModel->listCategoriesOfProduct($cartItem->getProduct());
                    $categories = array();
                    if (!$response['error']) {
                        foreach ($response['result']['set'] as $cat) {
                            $categories[] = $cat->getId();
                        }
                    }
                    unset($response);
                    $newCartItem = new BundleEntity\Proxy\CartItemProxyEntity();
                    $newCartItem->setId($cartItem->getId());
                    $newCartItem->setProduct($cartItem->getProduct()->getId());
                    $newCartItem->setProductCategories($categories);
                    $price = $cartItem->getPrice();
                    $discountedPrice = $cartItem->getProduct()->getDiscountPrice();
                    $newCartItem->setTotalAmount($cartItem->getTotal());
                    $newCartItem->setQuantity($cartItem->getQuantity());
                    $newCartItem->setBoxType($cartItem->getPackageType());
                    if ($cartItem->getPackageType() == 'b') {
                        $responseVP = $productModel->getVolumePricingOfProductWithMaximumQuantity($cartItem->getProduct()->getId());
                        if (!$responseVP['error']) {
                            $price = $responseVP['result']['set']->getPrice();
                            $discountedPrice = $responseVP['result']['set']->getDiscountedPrice();
                        }
                        unset($responseVP);
                    }
                    $newCartItem->setPrice($price);
                    $newCartItem->setDiscountedPrice($discountedPrice);
                    /** Setting boxCount */
                    $boxCount = json_decode(stripslashes($cartItem->getProduct()->getExtraInfo()));
                    $boxCount = isset($boxCount->box) ? $boxCount->box : 1;
                    $newCartItem->setBoxCount($boxCount);
                    $newCartItem->setTaxRate($cartItem->getTax());
                    $taxAmount = $cartItem->getTax() * $cartItem->getPrice() / 100;
                    $newCartItem->setTaxAmount($taxAmount);
                    $newCartItem->setDiscount($cartItem->getDiscount());
                    $newCart->addItem($newCartItem);
                    $totalPrice += $newCartItem->getTotalAmount();
                }
                unset($response);
            }
            /** Unsetting kernel property because of doctrine recursive structure. */
            unset($newCart->kernel);
            $newCart->setDateUpdated($cart->getDateUpdated());
            return $newCart;
        }
    }

    /**
     * @name            saveToSession ()
     *                  Saves cart to session.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.5
     *
     * @param   BundleEntity\Proxy\CartProxyEntity $cart
     * @return  bool
     */
    public function saveToSession($cart)
    {
        if (!$cart instanceof BundleEntity\Proxy\CartProxyEntity) {
            return false;
        }
        $this->session->set('bbr_cart', $cart);
        return true;
    }

    /**
     * @name            saveToDb ()
     *                  Saves cart to database.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.6
     * @version         1.0.6
     *
     * @param   BundleEntity\Proxy\CartProxyEntity $currentCart
     * @return  mixed
     */
    public function saveToDb($currentCart)
    {
        /** Convert proxy object to ShoppingCart and ShoppingCartItem entities */
        $response = $this->convertToDatabaseObject($currentCart);
        if (isset($response['error']) && $response['error'] == true) {
            return $response;
        }
        $cart = $response;
        unset($response);

        /** Update shopping cart */
        $response = $this->cartModel->updateShoppingCart($cart['entity']);
        if ($response['error']) {
            return $response;
        }
        unset($response);

        /** Update shopping cart items */
        $proxyCart = $this->getCart();
        $items = $proxyCart->getItems();
        $c = 0;
        foreach ($cart['items'] as $item) {
            $action = 'updateShoppingCartItem';
            if (is_null($item->getId())) {
                $action = 'insertShoppingCartItem';
            }
            $response = $this->cartModel->$action($item);
            if ($response['error']) {
                return $response;
            }
            $newCartItem = $response['result']['set'][0];
            if (isset($items[$c])) {
                $items[$c]->setId($newCartItem->getId());
            }
            $c++;
        }
        $proxyCart->setItems($items);
        $this->cartUpdated = true;
        $this->setCartAfterSaveToDb($proxyCart);
        return true;
    }

    /**
     * @name            loadFromDb ()
     *                  Loads cart from database with cart id taken from cookie and member id
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.5
     *
     * @param   int $cart
     * @param   int $member
     *
     * @return mixed
     */
    public function getCartFromDbByCartIdAndMember($cart, $member)
    {
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.id', 'comparison' => '=', 'value' => $cart),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.member', 'comparison' => '=', 'value' => $member),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.date_ordered', 'comparison' => 'null', 'value' => null),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.date_cancelled', 'comparison' => 'null', 'value' => null),
                ),
            )
        );
        $response = $this->cartModel->listShoppingCarts($filter, array('date_updated' => 'desc'));
        if (!$response['error']) {
            $response['result']['set'] = $response['result']['set'][0];
        }
        return $response;
    }

    /**
     * @name            getCartFromDbByMember ()
     *                  Loads cart from database with member id
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.5
     *
     * @param   int $member
     *
     * @return mixed
     */
    public function getCartFromDbByMember($member)
    {
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.member', 'comparison' => '=', 'value' => $member),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.date_ordered', 'comparison' => 'null', 'value' => null),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.date_cancelled', 'comparison' => 'null', 'value' => null),
                ),
            )
        );
        $response = $this->cartModel->listShoppingCarts($filter, array('date_updated' => 'desc'));
        if (!$response['error']) {
            $response['result']['set'] = $response['result']['set'][0];
        }
        return $response;
    }

    /**
     * @name            getCartFromDbBySessionId ()
     *                  Loads cart from database with PHPSESSID
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.5
     *
     *
     * @return mixed
     */
    public function getCartFromDbBySessionId()
    {
        /** Find id of session with PHPSESSID */
        $logModel = $this->kernel->getContainer()->get('logbundle.model');
        $response = $logModel->getSession($this->session->getId(), 'session_id');
        if ($response['error']) {
            return $response;
        }
        $sessionId = $response['result']['set']->getId();
        unset($response, $logModel);
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.session', 'comparison' => '=', 'value' => $sessionId),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.date_ordered', 'comparison' => 'null', 'value' => null),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.date_cancelled', 'comparison' => 'null', 'value' => null),
                ),
            )
        );
        $response = $this->cartModel->listShoppingCarts($filter, array('date_updated' => 'desc'));
        if (!$response['error']) {
            $response['result']['set'] = $response['result']['set'][0];
        }
        return $response;
    }

    /**
     * @name            getCartFromDbByCartId ()
     *                  Loads cart from database with cart id from cookie
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.0.5
     *
     * @return mixed
     */
    public function getCartFromDbByCartId($cart)
    {
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.id', 'comparison' => '=', 'value' => $cart),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.date_ordered', 'comparison' => 'null', 'value' => null),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_cart', 'alias') . '.date_cancelled', 'comparison' => 'null', 'value' => null),
                ),
            )
        );
        $response = $this->cartModel->listShoppingCarts($filter, array('date_updated' => 'desc'));
        if (!$response['error']) {
            $response['result']['set'] = $response['result']['set'][0];
        }
        return $response;
    }

    /**
     * @name            encryptCookie ()
     *
     *                  Encrypts cookie value
     *
     * @author          Said İmamoğlu
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           string $cookie
     *
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function encryptCookie($cookie)
    {
        $container = $this->kernel->getContainer();
        $encryption = $container->get('encryption');
        return $encryption->input($cookie)->key($container->getParameter('app_key'))->encrypt()->output();
    }

    /**
     * @name            decryptCookie ()
     *
     *                  Decrypts cookie value
     *
     * @author          Said İmamoğlu
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           string $cookie
     *
     * @return          string
     */
    public function decryptCookie($cookie)
    {
        $container = $this->kernel->getContainer();
        $encryption = $container->get('encryption');
        return $encryption->input($cookie)->key($container->getParameter('app_key'))->decrypt()->output();
    }

    /**
     * @name            deleteShoppingCartItemByColumn ()
     *
     *                  processes all actions
     *
     * @author          Said İmamoğlu
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           int $product
     * @param           string $by
     * @param           string $type
     *
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function removeShoppingCartItemFromCart($product, $by = 'product', $type = 'b')
    {
        $cart = $this->getCart();
        $items = $cart->getItems();
        foreach ($items as $key => $item) {
            switch ($by) {
                case 'product':
                    if ($item->getProduct() == $product && $item->getBoxType() == $type) {
                        $cart->getItems()->removeElement($items[$key]);
                    }
                    break;
                default:
                    break;
            }
        }
        $cart->setDateUpdated($this->date);
        $this->updateCart($cart);
        $responseJson = $this->convertToJsonObject();
        $response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $responseJson,
                'total_rows' => 0,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'success.item.removed',
        );
        return $response;
    }

    /**
     * @name            deleteShoppingCartItemByColumn ()
     *
     *                  processes all actions
     *
     * @author          Said İmamoğlu
     * @since           1.0.0
     * @version         1.0.7
     *
     * @param           int $product
     * @param           int $quantity
     * @param           int $type
     *
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function addShoppingCartItemToCart($product, $quantity, $type)
    {
        $cart = $this->getCart();
        $items = $cart->getItems();
        /** Get product  */
        $productModel = $this->kernel->getContainer()->get('productmanagement.model');
        $response = $productModel->getProduct($product);
        if ($response['error']) {
            return $response;
        }
        $product = $response['result']['set'];
        unset($response);
        /** Calculate Tax */
        $taxRate = 18;
        $taxModel = $this->kernel->getContainer()->get('taxmanagement.model');
        $responseTax = $taxModel->listTaxRatesOfProduct($product->getId());
        if ($responseTax['error']) {
            $responseCategories = $productModel->listCategoriesOfProduct($product);
            if (!$responseCategories['error']) {
                $categories = $responseCategories['result']['set'];
                $cats = array();
                foreach ($categories as $cat) {
                    $cats[] = $cat->getId();
                }
                unset($categories);

                $filter = array();
                $filter[] = array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $taxModel->getEntityDefinition('tax_rate', 'alias') . '.product_category', 'comparison' => 'in', 'value' => $cats),
                        )
                    )
                );
                $responseListTax = $taxModel->listTaxRates($filter);
                if ($responseListTax['error']) {
                    $taxRate = 18;
                } else {
                    $taxRate = $responseListTax['result']['set'][0]->getRate();
                }
                unset($responseListTax);
            }
            unset($responseCategories);
        } else {
            $taxRate = $responseTax['result']['set'][0]->getRate();
        }

        unset($responseTax);
        /** Box Count */
        $boxCount = 1;
        if ($type == 'b') {
            $boxCount = json_decode(stripslashes($product->getExtraInfo()));
            $boxCount = isset($boxCount->box) ? $boxCount->box : 1;
        }
        $newCartItem = new BundleEntity\Proxy\CartItemProxyEntity();
        $newCartItem->setTaxRate($taxRate);
        unset($taxRate);
        $newCartItem->setBoxCount($boxCount);
        $newCartItem->setQuantity($quantity);
        $newCartItem->setProduct($product->getId());
        $newCartItem->setPrice($product->getPrice());
        $newCartItem->setDiscountedPrice($product->getDiscountPrice());
        $newCartItem->setTaxAmount($newCartItem->getBoxCount() * $newCartItem->getTaxRate() * $newCartItem->getPrice() * $newCartItem->getQuantity() / 100);
        if ($type == 'b') {
            $responseVP = $productModel->getVolumePricingOfProductWithMaximumQuantity($product);
            if (!$responseVP['error']) {
                $volumePricing = $responseVP['result']['set'];
                $newCartItem->setPrice($volumePricing->getPrice());
                $newCartItem->setDiscountedPrice($volumePricing->getDiscountedPrice());
            }
            unset($responseVP);
        }
        $newCartItem->setTotalAmount(($newCartItem->getBoxCount() * $newCartItem->getPrice() * $newCartItem->getQuantity()) + $newCartItem->getTaxAmount());
        if ($newCartItem->getDiscountedPrice() > 0 && $newCartItem->getDiscountedPrice() < $newCartItem->getPrice()) {
            $newCartItem->setTaxAmount($newCartItem->getBoxCount() * $newCartItem->getTaxRate() * $newCartItem->getDiscountedPrice() * $newCartItem->getQuantity() / 100);
            $newCartItem->setTotalAmount(($newCartItem->getBoxCount() * $newCartItem->getDiscountedPrice() * $newCartItem->getQuantity()) + $newCartItem->getTaxAmount());
        }
        $newCartItem->setBoxType($type);
        $currentCartItem = $this->doesCartHasItem($newCartItem);
        if ($currentCartItem) {
            $currentCartItem->setQuantity($newCartItem->getQuantity() + $currentCartItem->getQuantity());
            $currentCartItem->setTotalAmount($newCartItem->getTotalAmount() + $currentCartItem->getTotalAmount());
            $currentCartItem->setTaxAmount($newCartItem->getTaxAmount() + $currentCartItem->getTaxAmount());
            $response = $this->updateCartItem($currentCartItem);
            if ($response['error']) {
                return $response;
            }
        } else {
            if ($newCartItem->getQuantity() > 0) {
                $items->add($newCartItem);
                $cart->setItems($items);
                $cart->setDateUpdated($this->date);
                $this->updateCart($cart);
            }
        }
        $responseJson = $this->convertToJsonObject($this->getCart());
        $response = array(
            'result' => array(
                'set' => $responseJson,
                'total_rows' => 0,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => is_array($responseJson) ? (isset($responseJson['code']) ? $responseJson['code'] : '"success.item.added"') : "success.item.added",
        );

        return $response;
    }

    /**
     * @name            updateProductQuantity ()
     * Updates product quantity
     *
     * @author          Said İmamoğlu
     * @since           1.0.0
     * @version         1.0.7
     *
     * @param   int $item
     * @param   string $type
     * @param   int $quantity
     *
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function updateProductQuantity($item, $type, $quantity)
    {
        $response = $this->getCartItem($item, 'product', $type);
        if ($response['error']) {
            return $response;
        }
        $item = $response['result']['set'];
        /** If product quantity lower then 0 drop them or remove them from cart */
        if ($quantity < 1) {
            $quantity = $item->getQuantity() + $quantity;
            if ($quantity <= 0) {
                return $this->removeShoppingCartItemFromCart($item, null, $type);
            }
        }
        $item->setQuantity($quantity);
        $item->setTaxAmount($item->getBoxCount() * $item->getTaxRate() * $item->getPrice() * $item->getQuantity() / 100);
        $item->setTotalAmount(($item->getBoxCount() * $item->getQuantity() * $item->getPrice()) + $item->getTaxAmount());
        if ($item->getDiscountedPrice() > 0) {
            $item->setTaxAmount($item->getBoxCount() * $item->getTaxRate() * $item->getDiscountedPrice() * $item->getQuantity() / 100);
            $item->setTotalAmount(($item->getBoxCount() * $item->getQuantity() * $item->getDiscountedPrice()) + $item->getTaxAmount());
        }
        $response = $this->updateCartItem($item);
        $responseJson = $this->convertToJsonObject($this->getCart());
        $response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $responseJson,
                'total_rows' => 0,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => is_array($response['code']) ? $$response['code'] : "success.item.updated",
        );

        return $response;
    }

    /**
     * @name            viewCart ()
     *
     *                  processes all actions
     *
     * @author          Said İmamoğlu
     * @since           1.0.0
     * @version         1.0.0
     *
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function viewCart()
    {
        $response = $this->getCart();
        $responseJson = $this->convertToJsonObject($response);
        $response = array(
            'result' => array(
                'set' => $responseJson,
                'total_rows' => 0,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => "success.cart",
        );

        return $response;
    }

    /**
     * @name            convertToJsonObject ()
     *                  Convert current cart to json
     *
     * @author          Said İmamoğlu
     * @since           1.0.2
     * @version         1.0.2
     *
     * @return          bool
     */
    public function convertToJsonObject()
    {
        $cart = $this->getCart();
        $response = new \stdClass();
        $response->currency = "TL";
        $products = array();
        $totalTax = 0;
        $totalAmount = 0;
        $totalDiscount = 0;
        $totalQuantity = 0;
        $response->products = array();
        foreach ($cart->getItems() as $item) {
            if (!in_array($item->getProduct(), $products)) {
                $products[] = $item->getProduct();
            }
        }
        if (count($products) > 0) {
            $productModel = $this->kernel->getContainer()->get('productmanagement.model');
            $filter = array();
            $filter[] = array(
                'glue' => 'and',
                'condition' => array(
                    array(
                        'glue' => 'and',
                        'condition' => array('column' => $productModel->getEntityDefinition('product', 'alias') . '.id', 'comparison' => 'in', 'value' => $products),
                    )
                )
            );
            unset($products);
            $productCollection = array();
            $responseObj = $productModel->listProducts($filter);
            if (!$responseObj['error']) {
                foreach ($responseObj['result']['set'] as $product) {
                    $productCollection[$product->getId()] = $product;

                }
            }
            unset($responseObj);
            foreach ($cart->getItems() as $item) {
                if (!isset($productCollection[$item->getProduct()])) {
                    return $response;
                }
                $product = $productCollection[$item->getProduct()];
                $newProductItem = new \stdClass();
                $newProductItem->id = $product->getId();
                /** Photos */
                $file = $product->getPreviewFile();
                $newProductItem->small_photo = '';
                $newProductItem->small_photo = '';
                if (!is_null($file)) {
                    $folder = $file->getFolder()->getPathAbsolute();
                    $newProductItem->small_photo = $folder . '/' . $file->getSourcePreview();
                    $newProductItem->large_photo = $folder . '/' . $file->getSourceOriginal();
                }

                /** Box count */
                $boxCount = json_decode(stripslashes($product->getExtraInfo()));
                $boxCount = isset($boxCount->box) && !empty($boxCount->box) ? $boxCount->box : 1;
                /** Quantity and Box Type */
                $newProductItem->quantity = $item->getQuantity();
                $totalQuantity += $item->getQuantity();
                $newProductItem->quantity_type = $item->getBoxType();
                $newProductItem->box_count = $boxCount;
                $locale = $this->sessionManager->get_detail('_locale');
                $newProductItem->name = $product->getLocalization($locale)->getName();
                $newProductItem->description = $product->getLocalization($locale)->getDescription();
                $newProductItem->item_price = $this->_numberFormat($item->getPrice());
                $newProductItem->item_discounted_price = $this->_numberFormat($item->getDiscountedPrice());
                if ($item->getBoxType() == 'b') {
                    $responseVP = $productModel->getVolumePricingOfProductWithMaximumQuantity($product);
                    if (!$responseVP['error']) {
                        $newProductItem->item_price = $this->_numberFormat($responseVP['result']['set']->getPrice());
                        $newProductItem->item_discounted_price = $this->_numberFormat($responseVP['result']['set']->getDiscountedPrice());
                    }
                    unset($responseVP);
                }
                $newProductItem->total_price = $this->_numberFormat($item->getTotalAmount());
                $newProductItem->tax_rate = $item->getTaxRate();
                $newProductItem->tax = $this->_numberFormat($item->getTaxAmount());
                $newProductItem->discount = $this->_numberFormat($item->getDiscount());
                $totalTax += $item->getTaxAmount();
                $totalAmount += $item->getTotalAmount();
                $totalDiscount += $item->getDiscount();
                $response->products[] = $newProductItem;
            }

        }
        $cart->setQuantity($totalQuantity);
        $cart->setTotalAmount($this->_numberFormat($totalAmount));
        $cart->setTotalTax($this->_numberFormat($totalTax));
        $cart->setTotalDiscount($this->_numberFormat($totalDiscount));
        $this->updateCart($cart);
        $response->quantity = $totalQuantity;
        $response->tax = $this->_numberFormat($totalTax);
        $response->discount = $this->_numberFormat($totalDiscount);
        $response->total = $this->_numberFormat($totalAmount);
        $response->subtotal = $this->_numberFormat($totalAmount - $totalTax);

        return $response;
    }

    /**
     * @name            doesCartHasItem ()
     * Checks item exist in cart items
     *
     * @author          Said İmamoğlu
     * @since           1.0.2
     * @version         1.0.2
     *
     * @param           BundleEntity\Proxy\CartItemProxyEntity $currentItem
     *
     * @return          bool
     */
    public function doesCartHasItem(BundleEntity\Proxy\CartItemProxyEntity $currentItem)
    {
        $items = $this->listCartItems(true);
        $found = false;
        foreach ($items as $item) {
            if (($item->getProduct() == $currentItem->getProduct()) && ($item->getBoxType() === $currentItem->getBoxType())) {
                $found = true;
//                $currentItem->setId($item->getId());
                $currentItem = $item;
            }
        }
        return $found ? $currentItem : false;
    }

    /**
     * @name            convertObjectToArray ()
     *
     *                  Converts given object to array
     *
     * @author          Said İmamoğlu
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           object $object
     *
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function convertObjectToArray($object)
    {
        $vars = get_object_vars($object);
        foreach ($vars as &$value) {
            if (is_object($value) && method_exists($value, 'convertObjectToArray')) {
                $value = $value->convertObjectToArray();
            } else {
                $value = (array)$value;
            }
        }
        return $vars;
    }

    /**
     * @name            getCartItem ()
     *
     *                  processes all actions
     *
     * @author          Said İmamoğlu
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           mixed $cartItem
     * @param           string $by
     * @param           string $type
     *
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function getCartItem($cartItem, $by = 'product', $type = null)
    {
        $cart = $this->getCart();
        $items = $cart->getItems();
        $count = 0;
        $result = false;

        foreach ($items as $item) {
            switch ($by) {
                case 'id':
                    if ($item->getId() == $cartItem) {
                        $result = $items[$count];
                    }
                    break;
                case 'product':
                    if ($item->getProduct() == $cartItem && $item->getBoxType() == $type) {
                        $result = $items[$count];
                    }
                    break;
                default:
                    break;
            }

            $count++;
        }
        $error = false;
        if (!$result instanceof BundleEntity\Proxy\CartItemProxyEntity) {
            $error = true;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $result,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => 'error.item.notfound',
        );
        return $this->response;
    }

    /**
     * @name            listCartItems ()
     * Listing all items in cart.
     *
     * @author          Said İmamoğlu
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param   bool $skip
     *
     * @return          array
     */
    public function listCartItems($skip)
    {
        $cart = $this->getCart();
        $items = $cart->getItems();
        if ($skip) {
            return $items;
        }
        $error = count($items) > 0 ? false : true;

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $items,
                'total_rows' => count($items),
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name            getCartItem ()
     *
     *                  processes all actions
     *
     * @author          Said İmamoğlu
     * @since           1.0.0
     * @version         1.0.7
     *
     * @param           mixed $cartItem
     *
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function updateCartItem($cartItem)
    {
        $cart = $this->getCart();
        $items = $cart->getItems();
        if ($cartItem instanceof BundleEntity\Proxy\CartItemProxyEntity) {
            $cartItemEntity = $cartItem;
        } elseif ($cartItem instanceof \stdClass) {
            if (isset($cartItem->id)) {
                $cartItemEntity = $this->getCartItem($cartItem->id, 'id', $cartItem->type);
            } else {
                if (isset($cartItem->product)) {
                    $cartItemEntity = $this->getCartItem($cartItem->product, 'product', $cartItem->type);
                } else {
                    if (isset($cartItem->sku)) {
                        $cartItemEntity = $this->getCartItem($cartItem->sku, 'sku', $cartItem->type);
                    } else {
                        return $this->createException('InvalidParameterException', 'object', 'err.invalid.parameter.collection');
                    }
                }
            }
            foreach ($cartItem as $key => $value) {
                $methodName = 'set' . $this->translateColumnName($key);
                $cartItemEntity->$methodName($cartItem->id, 'id');
            }

        }
        $count = 0;
        foreach ($items as $key => $item) {
            if (($item->getProduct() == $cartItemEntity->getProduct()) && ($item->getBoxType() == $cartItemEntity->getBoxType())) {
                $quantity = $cartItemEntity->getQuantity();
                if ($quantity <= 0) {
                    return $this->removeShoppingCartItemFromCart($item->getProduct(), 'product', $item->getBoxType());
                }
                $item->setQuantity($quantity);
                $item->setTaxAmount($cartItemEntity->getTaxAmount());
                $item->setTotalAmount($cartItemEntity->getTotalAmount());
                $items[$count] = $item;
            }
            $count++;
        }
        $cart = $this->getCart();
        $cart->setItems($items);
        $cart->setDateUpdated($this->date);
        $this->setCart($cart);

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $cart,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name            isCouponValid ()
     *                  Checks given coupon validity with rules in database.
     *
     * @author          Said İmamoğlu
     * @since           1.0.0
     * @version         1.0.7
     *
     * @param           BundleEntity\Coupon $coupon
     *
     * @return          array
     */
    private function isCouponValid(BundleEntity\Coupon $coupon)
    {
        /**
         * Check coupon exist from database.
         */
        $shoppingCartModel = $this->kernel->getContainer()->get('shoppingcart.model');
        /**
         * Cases
         *
         * 1. Check type usage:
         *  List redeemed coupons and get total rows.
         */
        $currentCart = $this->getCart();
        if ($currentCart->hasCoupon($coupon->getId())) {
//            $currentCart->removeCoupon($coupon->getId());
            return $this->response = array('rowCount' => $this->response['rowCount'], 'result' => array(), 'error' => true, 'code' => 'msg.error.coupon.usage.limit.exceeded');
        }
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $shoppingCartModel->getEntityDefinition('redeemed_coupon', 'alias') . '.coupon', 'comparison' => '=', 'value' => $coupon->getId()),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $shoppingCartModel->getEntityDefinition('redeemed_coupon', 'alias') . '.member', 'comparison' => '=', 'value' => $this->memberId),
                ),
            )
        );
        /** Is coupon reached to limit of redeem*/
        $response = $shoppingCartModel->listRedeemedCoupons($filter);
        if (!$response['error']) {
            $redeemedCoupons = $response['result']['set'];
            $totalRedeemedCoupons = count($redeemedCoupons);
            if ($coupon->getTypeUsage() == 's' && $totalRedeemedCoupons > 1) {
                return $this->response = array('rowCount' => $this->response['rowCount'], 'result' => array(), 'error' => true, 'code' => 'msg.error.coupon.limit.reached');
            } elseif ($coupon->getTypeUsage() == 'm') {
                if ($coupon->getLimitRedeem() <= $totalRedeemedCoupons && $totalRedeemedCoupons > 2) {
                    return $this->response = array('rowCount' => $this->response['rowCount'], 'result' => array(), 'error' => true, 'code' => 'msg.error.coupon.limit.reached');
                }
            }
        }
        unset($response);
        $totalAmountOfCart = $this->calculateTotal();
        /** If coupon only available to carts which total is higher than limit_order_total */
        if (!is_null($coupon->getLimitOrderTotal()) && $totalAmountOfCart < $coupon->getLimitOrderTotal()) {
            return $this->response = array('rowCount' => $this->response['rowCount'], 'result' => array(), 'error' => true, 'code' => 'msg.error.coupon.not.reached.to.limit');
        }
        $rules = json_decode(stripslashes($coupon->getValidity()));
        $availableItems = array();
        (float)$totalAmountOfAvailableItems = 0.00;
        $cartItems = $this->listCartItems(true);
        if (!is_null($rules)) {
            /** Check if product of item available for this coupon */
            if (isset($rules->product)) {
                foreach ($cartItems as $item) {
                    $discountOfCoupon = $coupon->getDiscount();
                    if (in_array($item->getProduct(), $rules->product)) {
                        /** Apply coupon for this item */
                        $item->setCoupon($coupon->getId());
                        if ($coupon->getType() == 'p') {
                            $discountOfCoupon = $item->getPrice() * $coupon->getDiscount() / 100;
                        }
                        $item->setDiscount($discountOfCoupon * $item->getQuantity());
                        $availableItems[$item->getId()] = $item;
                        /** Add item to collection of coupon applied items */
                        $currentCart->addItemToCouponAppliedItem($item->getId());
                        $totalAmountOfAvailableItems += $item->getPrice();
                    }
                }
            }
            /** Check if product categories of item available for this coupon */
            if (isset($rules->productCategory)) {
                foreach ($cartItems as $item) {
                    if (!isset($availableItems[$item->getId()])) {
                        foreach ($item->getProductCategories() as $c) {
                            $discountOfCoupon = $coupon->getDiscount();
                            if (in_array($c, $rules->productCategory)) {
                                if (!isset($availableItems[$item->getId()])) {
                                    /** Apply coupon for this item */
                                    $item->setCoupon($coupon->getId());
                                    if ($coupon->getType() == 'p') {
                                        $discountOfCoupon = $item->getPrice() * $coupon->getDiscount() / 100;
                                    }
                                    $item->setDiscount($discountOfCoupon * $item->getQuantity());
                                    $availableItems[$item->getId()] = $item;
                                    $totalAmountOfAvailableItems += $item->getPrice();
                                }
                            }
                        }
                    }
                }
            }
            /** Check if member available for this coupon */
            $canMemberUse = true;
            if (isset($rules->member)) {
                if (!in_array($this->memberId, $rules->member)) {
                    $canMemberUse = false;
                }
            }
            /** Check if member groups available for this coupon */
            if (isset($rules->memberGroup)) {
                /** Get groups of member */
                $memberModel = $this->kernel->getContainer()->get('membermanagement.model');
                $response = $memberModel->listGroupsOfMember($this->memberId);
                $groupsOfMember = array();
                if (!$response['error']) {
                    foreach ($response['result']['set'] as $group) {
                        $groupsOfMember[] = $group->getId();
                    }

                }
                unset($memberModel, $response);
                foreach ($groupsOfMember as $group) {
                    if (in_array($group, $rules->memberGroup)) {
                        $canMemberUse = true;
                    }
                }
            }
        }
        if ($canMemberUse == false) {
            return $this->response = array('rowCount' => $this->response['rowCount'], 'result' => array(), 'error' => true, 'code' => 'msg.error.coupon.denied');
        }
        if ($coupon->getType() == 'p') {
            if ((($totalAmountOfAvailableItems * $coupon->getLimitDiscount() / 100)) > $coupon->getDiscount()) {
                return $this->response = array('rowCount' => $this->response['rowCount'], 'result' => array(), 'error' => true, 'code' => 'msg.error.coupon.discount.limit.exceeded');
            }
        }
        if (count($this->listCartItems(true)) > 0 && count($availableItems) < 1) {
            return $this->response = array('rowCount' => $this->response['rowCount'], 'result' => array(), 'error' => true, 'code' => 'msg.error.coupon.invalid.products');
        }

        return $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $currentCart,
            ),
            'error' => false,
            'code' => 'success.coupon.valid'
        );
    }

    /**
     * @name            getCartTotal ()
     *
     *                  Calculates total of cart
     *
     * @author          Said İmamoğlu
     * @since           1.0.1
     * @version         1.0.1
     *
     *
     * @return          \Symfony\Component\HttpFoundation\Response
     */
    public function getCartTotal()
    {
        return $this->calculateTotal();
    }

    /**
     * @name            getCartTotal ()
     *
     *                  Calculates total of cart
     *
     * @author          Said İmamoğlu
     * @since           1.0.1
     * @version         1.0.1
     *
     *
     * @return          float
     */
    private function calculateTotal(float $total = null)
    {
        $response = $this->listCartItems(false);
        if ($response['error']) {
            return $total;
        }
        $items = $response['result']['set'];
        unset($response);
        (float)$total = 0.00;
        foreach ($items as $item) {
            $total += $item->getTotalAmount();
        }
        return $total;
    }

    /**
     * @name            checkCouponValidty ()
     *
     * Applies coupon to cart.
     *
     * @author          Said İmamoğlu
     * @since           1.0.1
     * @version         1.0.7
     *
     * @param           mixed $coupon
     *
     * @use             $this->isCouponValid()
     *
     * @return          array
     */
    public function applyCoupon($coupon)
    {
        $response = $this->cartModel->getCoupon($coupon, 'code');
        if ($response['error']) {
            return $this->createException('EntityDoesNotExist', 'Coupon not found', 'msg.error.notfound.coupon');
        }
        $coupon = $response['result']['set'];
        unset($response);
        $response = $this->isCouponValid($coupon);
        if ($response['error']) {
            return $response;
        }
        $currentCart = $response['result']['set'];

        /** Calculate total discount */
        $currentCart->setTotalAmount($currentCart->calculateTotalAmount());
        $currentCart->setTotalDiscount($currentCart->calculateTotalDiscount());
        $currentCart->setItems($currentCart->getItems());
        $currentCart->addCoupon($coupon->getId());
        $currentCart->setDateUpdated($this->date);
        $this->updateCart($currentCart);
        $responseJson = $this->convertToJsonObject();
        $response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $responseJson,
                'total_rows' => 0,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'success.coupon.applied',
        );
        return $response;
    }

    /**
     * @name            emptyCart ()
     * Empties cart
     *
     * @author          Said İmamoğlu
     * @since           1.0.3
     * @version         1.0.3
     *
     * @return          array
     */
    public function emptyCart()
    {
        $cart = $this->getCart();
        $cart->setItems(new ArrayCollection());
        $cart->setDateUpdated($this->date);
        $this->setCart($cart);
        return $this->convertToJsonObject($cart);
    }

    /**
     * @name            cartToOrder ()
     *                  Creates new order from shopping car
     *
     * @author          Said İmamoğlu
     * @since           1.0.5
     * @version         1.0.5
     *
     * @return          array
     */
    public function cartToOrder()
    {
        $cart = $this->getCart();
        /** Find cart */
        $response = $this->cartModel->getShoppingCart($cart->getId());
        if ($response['error']) {
            return $response;
        }
        $cartEntity = $response['result']['set'];
        unset($response);
        $order = new \stdClass();
        $order->count_items = $cart->getQuantity();
        $order->total_amount = $cart->calculateTotalAmount();
        $order->total_tax = $cart->calculateTotalTax();
        $order->total_discount = $cart->getTotalDiscount();
        $order->sub_total = $order->total_amount;
        $order->flag = 'o';
        $order->status = 1;
        $order->purchaser = $this->sessionManager->get_detail('id');
        $order->cart = $cartEntity->getId();
        unset($cartEntity);
        /** Check if an order exist with this cart */
        $response = $this->cartModel->getShoppingOrder($order->cart, 'cart');
        if (!$response['error']) {
            $order->id = $response['result']['set']->getId();
            $action = 'updateShoppingOrder';
        } else {
            $action = 'insertShoppingOrder';
        }
        unset($response);

        $response = $this->cartModel->$action($order);
        if (!$response['error']) {
            /** Insert shopping order items */
            $newItems = array();
            $existItems = array();
            foreach ($cart->getItems() as $item) {
                $itemEntity = new \stdClass();
                $itemEntity->product = (int)$item->getProduct();
                $itemEntity->order = $response['result']['set'][0]->getId();
                $itemEntity->quantity = $item->getQuantity();
                $itemEntity->price = $item->getPrice();
                $itemEntity->sub_total = $itemEntity->price * $itemEntity->quantity;
                $itemEntity->tax = (float)$item->getTaxRate();
                $itemEntity->discount = $item->getDiscount();
                $itemEntity->total = $item->getTotalAmount();
                $itemEntity->tax_amount = $item->getTaxAmount();
                $itemEntity->package_type = $item->getBoxType();
                /** Check if order item exist */
                $filter = array();
                $filter[] = array(
                    'glue' => 'and',
                    'condition' => array(
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_order_item', 'alias') . '.product', 'comparison' => '=', 'value' => $itemEntity->product),
                        ),
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_order_item', 'alias') . '.package_type', 'comparison' => '=', 'value' => $itemEntity->package_type),
                        ),
                        array(
                            'glue' => 'and',
                            'condition' => array('column' => $this->cartModel->getEntityDefinition('shopping_order_item', 'alias') . '.order', 'comparison' => '=', 'value' => $itemEntity->order),
                        ),
                    )
                );
                $responseItem = $this->cartModel->listShoppingOrderItems($filter);
                if ($responseItem['error']) {
                    $newItems[] = $itemEntity;
                } else {
                    $itemEntity->id = $responseItem['result']['set'][0]->getId();
                    $existItems[] = $itemEntity;
                }
                unset($responseItem);
            }
            /** Insert and update actions */
            $collection['order'] = $response['result']['set'][0];
            $insertedItems = $this->cartModel->insertShoppingOrderItems($newItems);
            $updatedItems = $this->cartModel->updateShoppingOrderItems($existItems);
            if (!$insertedItems['error'] && !$updatedItems['error']) {
                $collection['items'] = array_merge($insertedItems, $updatedItems);
            }
        }
        $response = array(
            'error' => false,
            'code' => 'success.cart.converted.to.order',
            'result' => array(
                'set' => $collection,
                'total_rows' => count($collection),
            ),
        );
        return $response;
    }
    /**
     * @name    numberFormat()
     *
     * @author  Said İmamoğlu
     * @since   1.0.8
     * @version 1.0.8
     *
     * @param   int $number
     * @return  float
     */
    private function _numberFormat($number){
        $decimal = 2;
        if ($this->kernel->getContainer()->hasParameter('currency_decimal')) {
            $decimal = $this->kernel->getContainer()->getParameter('currency_decimal');
        }
        return number_format($number,$decimal);
    }

}


/**
 * Change Log
 * **************************************
 * v1.0.8                     Said İmamoğlu
 * 08.07.2014
 * **************************************
 * A numberFormat()
 * **************************************
 * v1.0.7                     Said İmamoğlu
 * 04.07.2014
 * **************************************
 * A doesCookiesHasMember()
 * **************************************
 * v1.0.7                     Said İmamoğlu
 * 30.06.2014
 * **************************************
 * U addShoppingCartItemToCart()
 * U updateProductQuantity()
 * U updateCartItem()
 * D checkCouponValidty
 * A isCouponValid()
 * **************************************
 * v1.0.6                     Said İmamoğlu
 * 23.06.2014
 * **************************************
 * A convertToDatabaseObject()
 * A saveToDb()
 * **************************************
 * v1.0.5                     Said İmamoğlu
 * 20.06.2014
 * **************************************
 * A $date
 * A $memberId
 * A doesSessionHasCart()
 * A getCartFromSession()
 * A isUserLoggedIn()
 * A loadMemberCartFromDb()
 * A doesCookiesHasCart()
 * A getCartFromCookie()
 * A getCartFromDbByCartIdAndMember()
 * A getCartFromDbByCartId()
 * A convertToProxyCart()
 * A getCartFromDbBySessionId()
 * D convertToSessionObject()
 * D loadFromSession()
 * D loadMemberCartFromDb()
 * D loadLastFromDb()
 * D loadFromDbByDate()
 * D loadFromDbByColumn()
 * U __construct()
 *
 * **************************************
 * v1.0.4                     Said İmamoğlu
 * 19.06.2014
 * **************************************
 * A emptyCart()
 * **************************************
 * v1.0.3                     Said İmamoğlu
 * 25.05.2014
 * **************************************
 * A emptyCart()
 * **************************************
 * v1.0.2                     Said İmamoğlu
 * 21.05.2014
 * **************************************
 * A doesCartHasItem()
 * **************************************
 * v1.0.1                     Said İmamoğlu
 * 14.05.2014
 * **************************************
 * A getCartItem()
 * A updateCart()
 * U setCart()
 * A processValidation()
 * **************************************
 * v1.0.0                     Said İmamoğlu
 * 30.04.2013
 * **************************************
 * A deleteShoppingCartItemByColumn()
 * A saveToSession()
 * A saveToDb()
 * A loadFromSession()
 * A loadFromDb()
 * A loadLastFromDb()
 * A loadFromDbByDate()
 * A loadFromDbByCode ()
 */