<?php

/**
 * MLSListener Class
 *
 * This class provides MultiLanguage Support - language detection and redirection mechanisms.
 *
 * @vendor      BiberLtd
 * @package		Core
 * @subpackage	Services
 * @name	    MLSListener
 *
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.3.2
 * @date        22.11.2013
 *
 */

namespace BiberLtd\Core\Bundles\ShoppingCartBundle\Listeners;

use BiberLtd\Core\Core as Core;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use \Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Requires MultiLanguageSupportBundle
 */
use BiberLtd\Core\Bundles\MultiLanguageSupportBundle\Services as MLSServices;

class SCListener extends Core {

    /** @var $container             Service container */
    private $container;

    /** @var $languages             Available languages. */
    private $languages;

    /**
     *
     * @var type 
     */
    private $cookies;

    /**
     * @name            __construct()
     *                  Constructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           Kernel          $kernel
     *
     */
    public function __construct($container, $kernel) {
        parent::__construct($kernel);
    }

    /**
     * @name            __destruct()
     *                  Destructor.
     *
     * @author          Said İmamoğlu
     *
     * @since           1.0.0
     * @version         1.3.0
     *
     */
    public function __destruct() {
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }

    /**
     * @name 			onKernelRequest()
     *  				Called onKernelRequest event and handles browser language detection.
     *
     * @author          Said İmamoğlu
     *
     * @since			1.0.0
     * @version         1.3.1
     *
     * @param 			GetResponseEvent 	        $e
     *
     */
    public function onKernelRequest(Event\GetResponseEvent $e) {
        $request = $e->getRequest();
        $response = $e->getResponse();
        $session = $this->kernel->getContainer()->get('session');
        $cookies = $request->cookies;

        $sessionCart = $this->kernel->getContainer()->get('session')->get('bbr_cart');
        $enc = $this->kernel->getContainer()->get('encryption');

        $encryptedCookie = $cookies->get('bbr_cart');
        
        $cookie = array();
        $cartCollection = array();
        if (!is_null($encryptedCookie) && isset($encryptedCookie) && !$encryptedCookie) {
            $cookie = $enc->input($encryptedCookie)->key($this->kernel->getContainer()->getParameter('app_key'))->decrypt('enc_reversible_pkey')->output();
            $cookie = unserialize(base64_decode($cookie));
            $cartCollection['cookie'] = $cookie;
            $cartUpdatedTime = $cookie->getDateUpdated()->format('d.m.Y h.i.s');
        }
        unset($encryptedCookie,$cookie);
        if (!is_null($sessionCart) && isset($sessionCart) && !$sessionCart) {
            $cartCollection['session'] = $session;
            $cartUpdatedTime = $sessionCart->getDateUpdated()->format('d.m.Y h.i.s');
            
            $SCB = $this->get('shoppingcart.model');
            $response = $SCB->getCart();
            if (!$response['error']) {
                $db =$response['result']['set']; 
                $cartCollection['db'] = $db;
                $cartUpdatedTime = $db->getDateUpdated()->format('d.m.Y h.i.s');
            }
        }
        
        $currentCart = array();
        foreach ($cartCollection as $k=>$cart) {
            if (strtotime($cartUpdatedTime) < strtotime($cart->getDateUpdated()->format('d.m.Y h.i.s'))) {
                $currentCart = $cartCollection[$k];
            }
        }
        $session->set('bbr_cart',  json_encode($currentCart));
        unset($currentCart,$session);


        //$response->headers->setCookie(new Cookie('asli','anam'));
        //$cookies = $this->cookies->get('bbr_cart');
        //var_dump($cookies); 358691051799413
    }

}

/**
 * Change Log
 * **************************************
 * v1.3.2                      Said İmamoğlu
 * 22.11.2013
 * **************************************
 * U onKernelRequest() Fixed for MLS v1.0.5
 *
 * **************************************
 * v1.3.1                      Said İmamoğlu
 * 11.08.2013
 * **************************************
 * U onKernelRequest() checks for cookie locale.
 *
 * **************************************
 * v1.3.0                      Said İmamoğlu
 * 03.08.2013
 * **************************************
 * B __destruct() foreach loop bug fixed.
 * U __construct() now uses database to get a list of languages.
 *
 * **************************************
 * v1.0.0                      Said İmamoğlu
 * 03.08.2013
 * **************************************
 * A __construct()
 * A __destruct()
 * A onKernelRequest()
 */