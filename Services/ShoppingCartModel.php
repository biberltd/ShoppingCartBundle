<?php
/**
 * ShoppingCartModel Class
 *
 * This class acts as a database proxy model for ShoppingCartBundle functionalities.
 *
 * @vendor      BiberLtd
 * @package		Core\Bundles\ShoppingCartBundle
 * @subpackage	Services
 * @name	    ProductManagementModel
 *
 * @author		Can Berkol
 * @author      Said Imamoglu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.1.4
 * @date        23.06.2014
 *
 * =============================================================================================================
 * !! INSTRUCTIONS ON IMPORTANT ASPECTS OF MODEL METHODS !!!
 *
 * Each model function must return a $response ARRAY.
 * The array must contain the following keys and corresponding values.
 *
 * $response = array(
 *              'result'    =>   An array that contains the following keys:
 *                               'set'         Actual result set returned from ORM or null
 *                               'total_rows'  0 or number of total rows
 *                               'last_insert_id' The id of the item that is added last (if insert action)
 *              'error'     =>   true if there is an error; false if there is none.
 *              'code'      =>   null or a semantic and short English string that defines the error concanated
 *                               with dots, prefixed with err and the initials of the name of model class.
 *                               EXAMPLE: err.amm.action.not.found success messages have a prefix called scc..
 *
 *                               NOTE: DO NOT FORGET TO ADD AN ENTRY FOR ERROR CODE IN BUNDLE'S
 *                               RESOURCES/TRANSLATIONS FOLDER FOR EACH LANGUAGE.
 * =============================================================================================================
 * TODOs:
 *
 */

namespace BiberLtd\Bundle\ShoppingCartBundle\Services;

/** Extends CoreModel */
use BiberLtd\Bundle\MultiLanguageSupportBundle\Services\MultiLanguageSupportModel;
use BiberLtd\Bundle\CoreBundle\CoreModel;
/** Entities to be used */
use BiberLtd\Bundle\ShoppingCartBundle\Entity as BundleEntity;
use BiberLtd\Bundle\MemberManagementBundle\Entity as MMBEntity;
use BiberLtd\Bundle\MultiLanguageSupportBundle\Entity as MLSEntity;
use BiberLtd\Bundle\ProductManagementBundle\Entity as PMBEntity;
use BiberLtd\Bundle\SiteManagementBundle\Entity as SMBEntity;

/** Helper Models */
use BiberLtd\Bundle\LogBundle\Services as LBService;
use BiberLtd\Bundle\MemberManagementBundle\Services as MMBService;
use BiberLtd\Bundle\SiteManagementBundle\Services as SMMService;
use BiberLtd\Bundle\PaymentGatewayBundle\Services as PGBService;
use BiberLtd\Bundle\ProductManagementBundle\Services as PMBService;
/** Core Service */
use BiberLtd\Bundle\CoreBundle\Services as CoreServices;
use BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;

class ShoppingCartModel extends CoreModel {

    /**
     * @name            __construct()
     *                  Constructor.
     *ıııııııııııı
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     * @param           object          $kernel
     * @param           string          $db_connection  Database connection key as set in app/config.yml
     * @param           string          $orm            ORM that is used.
     */
    public function __construct($kernel, $db_connection = 'default', $orm = 'doctrine') {
        parent::__construct($kernel, $db_connection, $orm);

        /**
         * Register entity names for easy reference.
         */
        $this->entity = array(
            'coupon' => array('name' => 'ShoppingCartBundle:Coupon', 'alias' => 'c'),
            'coupon_localization' => array('name' => 'ShoppingCartBundle:CouponLocalization', 'alias' => 'cl'),
            'redeemed_coupon' => array('name' => 'ShoppingCartBundle:RedeemedCoupon', 'alias' => 'rc'),
            'shopping_cart' => array('name' => 'ShoppingCartBundle:ShoppingCart', 'alias' => 'sc'),
            'shopping_cart_item' => array('name' => 'ShoppingCartBundle:ShoppingCartItem', 'alias' => 'sci'),
            'shopping_order' => array('name' => 'ShoppingCartBundle:ShoppingOrder', 'alias' => 'so'),
            'shopping_order_item' => array('name' => 'ShoppingCartBundle:ShoppingOrderItem', 'alias' => 'soi'),
            'shopping_order_status' => array('name' => 'ShoppingCartBundle:ShoppingOrderStatus', 'alias' => 'sos'),
            'shopping_order_status_localization' => array('name' => 'ShoppingCartBundle:ShoppingOrderStatusLocalization', 'alias' => 'sosl'),
        );
    }
    /**
     * @name            __destruct()
     *                  Destructor.
     *
     * @author          Can Berkol
     *
     * @since           1.0.0
     * @version         1.0.0
     *
     */
    public function __destruct() {
        foreach ($this as $property => $value) {
            $this->$property = null;
        }
    }
    /**
     * @name            countCompletedOrders()
     *                  Get the total count of completed orders.
     *
     * @since           1.0.4
     * @version         1.0.4
     * @author          Can Berkol
     *
     * @param           string      $queryStr   Custom query
     *
     * @return          array       $response
     */
    public function countCompletedOrders($queryStr = null){
        $this->resetResponse();
        $whereStr = '';
        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($queryStr)) {
            $queryStr = 'SELECT COUNT('.$this->entity['shopping_order']['alias'].')'
                . ' FROM '.$this->entity['shopping_order']['name'].' '.$this->entity['shopping_order']['alias'];
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_order']['alias'] .'.flag', 'comparison' => '=', 'value' => 'c'),
                ),
            )
        );

        $filterStr = $this->prepareWhere($filter);
        $whereStr .= ' WHERE '.$filterStr;

        $queryStr .= $whereStr;

        $query = $this->em->createQuery($queryStr);

        /**
         * Prepare & Return Response
         */
        $result = $query->getSingleScalarResult();

        $this->response = array(
            'result' => array(
                'set' => $result,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name            countAllOrders()
     *                  Get the total count of completed orders.
     *
     * @since           1.1.2
     * @version         1.1.2
     * @author          Said İmamoğlu
     *
     * @param           mixed   $member
     *
     * @return          array       $response
     */
    public function countAllOrdersOfMember($member){
        $this->resetResponse();
        $whereStr = '';
        $memberModel = new MMBService\MemberManagementModel($this->kernel);
        if($member instanceof MMBService\MemberManagementModel){
            $member = $member->getId();
        }else{
            if (is_int($member)) {
                $response = $memberModel->getMember($member,'id');
            }elseif(is_string($member)){
                $response = $memberModel->getMember($member,'username');
            }else{
                return $this->createException('EntityDoesNotExist','Invalid Member Entity','notfound.member',true);
            }
            $member = $response['result']['set']->getId();
        }

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        $queryStr = 'SELECT COUNT('.$this->entity['shopping_order']['alias'].')'
            . ' FROM '.$this->entity['shopping_order']['name'].' '.$this->entity['shopping_order']['alias'];

        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_order']['alias'] .'.purchaser', 'comparison' => '=', 'value' => $member),
                ),
            )
        );

        $filterStr = $this->prepareWhere($filter);
        $whereStr .= ' WHERE '.$filterStr;

        $queryStr .= $whereStr;

        $query = $this->em->createQuery($queryStr);

        /**
         * Prepare & Return Response
         */
        $result = $query->getSingleScalarResult();

        $this->response = array(
            'result' => array(
                'set' => $result,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name            countNoneOrderedCarts()
     *                  Get the total count of none ordered shopping carts.
     *
     * @since           1.0.4
     * @version         1.0.4
     * @author          Can Berkol
     *
     * @param           string      $queryStr   Custom query
     *
     * @return          array       $response
     */
    public function countNoneOrderedCarts($queryStr = null){
        $this->resetResponse();
        $whereStr = '';
        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($queryStr)) {
            $queryStr = 'SELECT COUNT('.$this->entity['shopping_cart']['alias'].')'
                . ' FROM '.$this->entity['shopping_cart']['name'].' '.$this->entity['shopping_cart']['alias'];
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_cart']['alias'] .'.date_ordered', 'comparison' => 'isnull', 'value' => null),
                ),
            )
        );

        $filterStr = $this->prepareWhere($filter);
        $whereStr .= ' WHERE '.$filterStr;

        $queryStr .= $whereStr;

        $query = $this->em->createQuery($queryStr);

        /**
         * Prepare & Return Response
         */
        $result = $query->getSingleScalarResult();

        $this->response = array(
            'result' => array(
                'set' => $result,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }
    /**
     * @name 			deleteCouponsOfMember()
     *  				Deletes all coupons that belong to a member.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->deleteCoupons()
     *
     * @param           mixed           $member          entity or id.
     *
     * @return          mixed           $response
     */
    public function deleteCouponsOfMember($member) {
        $this->resetResponse();
        /**
         * Parameter check
         */
        if (!$member instanceof MMBEntity\Member && !is_integer($member)) {
            return $this->createException('InvalidParameterException', 'Member entity or integer', 'invalid.parameter.member');
        }
        $by = 'member';
        if ($member instanceof MMBEntity\Member) {
            $member = $member->getId();
        }

        return $this->deleteCoupons(array($member), $by);
    }

    /**
     * @name 			deleteCouponsOfMemberGroup()
     *  				Deletes all coupons that belong to a member group.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->deleteCoupons()
     *
     * @param           mixed           $group          entity or id.
     *
     * @return          mixed           $response
     */
    public function deleteCouponsOfMemberGroup($group) {
        $this->resetResponse();
        /**
         * Parameter check
         */
        if (!$group instanceof MMBEntity\MemberGroup && !is_integer($group)) {
            return $this->createException('InvalidParameterException', 'MemberGroup entity or integer', 'invalid.parameter.member_group');
        }
        $by = 'member_group';
        if ($group instanceof MMBEntity\MemberGroup) {
            $group = $group->getId();
        }

        return $this->deleteCoupons(array($group), $by);
    }

    /**
     * @name 			deleteCouponsOfProduct()
     *  				Deletes all coupons that belong to a product.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->deleteCoupons()
     *
     * @param           mixed           $product          entity or id.
     *
     * @return          mixed           $response
     */
    public function deleteCouponsOfProduct($product) {
        $this->resetResponse();
        /**
         * Parameter check
         */
        if (!$product instanceof PMBEntity\Product && !is_integer($product)) {
            return $this->createException('InvalidParameterException', 'Product entity or integer', 'invalid.parameter.product');
        }
        $by = 'product';
        if ($product instanceof PMBEntity\Product) {
            $product = $product->getId();
        }

        return $this->deleteCoupons(array($product), $by);
    }

    /**
     * @name 			deleteCouponsOfSite()
     *  				Deletes all coupons that belong to a site.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->deleteCoupons()
     *
     * @param           mixed           $site             entity or id.
     *
     * @return          mixed           $response
     */
    public function deleteCouponsOfSite($site) {
        $this->resetResponse();
        /**
         * Parameter check
         */
        if (!$site instanceof SMBEntity\Site && !is_integer($site)) {
            return $this->createException('InvalidParameterException', 'Site entity or integer', 'invalid.parameter.site');
        }
        $by = 'site';
        if ($site instanceof SMBEntity\Site) {
            $site = $site->getId();
        }

        return $this->deleteCoupons(array($site), $by);
    }

    /**
     * @name 			deleteCouponsWityThype()
     *  				Deletes all coupons that are of a certain type..
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->deleteCoupons()
     *
     * @param           string          $type            a or p.
     *
     * @return          mixed           $response
     */
    public function deleteCouponsWityThype($type) {
        $this->resetResponse();
        /**
         * Parameter check
         */
        $type_opts = array('a', 'p');
        if (!is_string($type) && !in_array($type, $type_opts)) {
            return $this->createException('InvalidParameterException', 'a or p', 'invalid.parameter.type');
        }
        $by = 'type';

        return $this->deleteCoupons(array($type), $by);
    }

    /**
     * @name 			deleteShoppingCart()
     *  				Deletes an existing cart from database.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->deleteShoppingCarts()
     *
     * @param           mixed           $data             a single value of
     *                                                              id
     *                                                              code
     *                                                              type
     *                                                              Coupon entity
     *                                                              Member entity
     *                                                              MemberGroup entity
     *                                                              Product entity
     *                                                              Site entity
     * @param           string          $by               'id', 'code', 'coupon', 'member', 'member_group', 'product', 'site'
     *
     * @return          mixed           $response
     */
    public function deleteShoppingCart($data, $by = 'id') {
        return $this->deleteShoppingCarts(array($data), $by);
    }

    /**
     * @name 			deleteShoppingCartItem()
     *  				Deletes an existing shopping cart item from database.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->deleteShoppingCartItems()
     *
     * @param           mixed           $data             a single value of
     *                                                              id
     * @param           string          $by               'id'
     *
     * @return          mixed           $response
     */
    public function deleteShoppingCartItem($data, $by = 'id') {
        return $this->deleteShoppingCartItems(array($data), $by);
    }

    /**
     * @name 			deleteShoppingCartItems()
     *  				Deletes provided shopping order items from database.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->delete_entities()
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection consists one of the following:
     *                                                              id
     * @param           string          $by             Accepts the following options: 'id'
     *
     * @return          array           $response
     */
    public function deleteShoppingCartItems($collection, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id','product');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Array', 'err.invalid.parameter.collection');
        }
        /** If COLLECTION is ENTITYs then USE ENTITY MANAGER */
        if ($by == 'id') {
            $sub_response = $this->delete_entities($collection, 'BundleEntity\ShoppingCartItem');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );

                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        }

        /**
         * If COLLECTION is NOT Entitys OR MORE COMPLEX DELETION NEEDED
         * CREATE CUSTOM SQL / DQL
         *
         * If you need custom DELETE, you need to assign $q_str to well formed DQL string; otherwise use
         * $this>prepare_delete.
         */
        $table = $this->entity['shopping_cart_item']['name'] . ' ' . $this->entity['shopping_cart_item']['alias'];
        $q_str = $this->prepare_delete($table, $this->entity['shopping_cart_item']['alias'] . '.' . $by, $collection);

        $query = $this->em->createQuery($q_str);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $collection_count = count($collection);
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => $collection_count,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        return $this->response;
    }

    /**
     * @name 			deleteShoppingCarts()
     *  				Deletes provided carts from database.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->delete_entities()
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection consists one of the following:
     *                                                              id
     *                                                              Member entity
     *                                                              Session entity
     *
     * @param           string          $by             Accepts the following options: 'id', 'member', 'session',
     *
     * @return          array           $response
     */
    public function deleteShoppingCarts($collection, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id', 'member', 'session');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Array', 'err.invalid.parameter.collection');
        }
        /** If COLLECTION is ENTITYs then USE ENTITY MANAGER */
        if ($by == 'id') {
            $sub_response = $this->deleteEntities($collection, 'BundleEntity\\ShoppingCart');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );

                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        }
        /**
         * If COLLECTION is NOT Entitys OR MORE COMPLEX DELETION NEEDED
         * CREATE CUSTOM SQL / DQL
         *
         * If you need custom DELETE, you need to assign $q_str to well formed DQL string; otherwise use
         * $this>prepare_delete.
         */
        $table = $this->entity['shopping_cart']['name'] . ' ' . $this->entity['shopping_cart']['alias'];
        $q_str = $this->prepareDelete($table, $this->entity['shopping_cart']['alias'] . '.' . $by, $collection);

        $query = $this->em->createQuery($q_str);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $collection_count = count($collection);
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => $collection_count,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        return $this->response;
    }

    /**
     * @name 			deleteShoppingOrder()
     *  				Deletes an existing shopping order  from database.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->deleteShoppingOrders()
     *
     * @param           mixed           $data             a single value of
     *                                                              id
     * @param           string          $by               'id'
     *
     * @return          mixed           $response
     */
    public function deleteShoppingOrder($data, $by = 'id') {
        return $this->deleteShoppingOrders(array($data), $by);
    }

    /**
     * @name 			deleteShoppingOrderItem()
     *  				Deletes an existing shopping order item from database.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->deleteShoppingOrderItems()
     *
     * @param           mixed           $data             a single value of
     *                                                              id
     * @param           string          $by               'id'
     *
     * @return          mixed           $response
     */
    public function deleteShoppingOrderItem($data, $by = 'id') {
        return $this->deleteShoppingOrderItems(array($data), $by);
    }

    /**
     * @name 			deleteShoppingOrders()
     *  				Deletes provided shopping orders from database.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->delete_entities()
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection consists one of the following:
     *                                                              id
     *                                                              code
     *                                                              type
     *                                                              Coupon entity
     *                                                              Member entity
     *                                                              MemberGroup entity
     *                                                              Product entity
     *                                                              Site entity
     * @param           string          $by             Accepts the following options: 'id', 'code', 'site', 'type', 'coupon', 'member', 'member_group', 'site'
     *
     * @return          array           $response
     */
    public function deleteShoppingOrders($collection, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Array', 'err.invalid.parameter.collection');
        }
        /** If COLLECTION is ENTITYs then USE ENTITY MANAGER */
        if ($by == 'id') {
            $sub_response = $this->delete_entities($collection, 'BundleEntity\\ShoppingOrder');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );

                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        }

        /**
         * If COLLECTION is NOT Entitys OR MORE COMPLEX DELETION NEEDED
         * CREATE CUSTOM SQL / DQL
         *
         * If you need custom DELETE, you need to assign $q_str to well formed DQL string; otherwise use
         * $this>prepare_delete.
         */
        $table = $this->entity['shopping_order']['name'] . ' ' . $this->entity['shopping_order']['alias'];
        $q_str = $this->prepareDelete($table, $this->entity['shopping_order']['alias'] . '.' . $by, $collection);

        $query = $this->em->createQuery($q_str);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $collection_count = count($collection);
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => $collection_count,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        return $this->response;
    }

    /**
     * @name 			deleteShoppingOrderStatus()
     *  				Deletes an existing shopping order status from database.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->deleteShoppingOrderStatus()
     *
     * @param           mixed           $data             a single value of
     *                                                              id
     * @param           string          $by               'id'
     *
     * @return          mixed           $response
     */
    public function deleteShoppingOrderStatus($data, $by = 'id') {
        return $this->deleteShoppingOrderStatuses(array($data), $by);
    }

    /**
     * @name 			deleteShoppingOrderStatuses()
     *  				Deletes provided shopping orders from database.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->delete_entities()
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection consists one of the following:
     *                                                              id
     *                                                              code
     *                                                              type
     *                                                              Coupon entity
     *                                                              Member entity
     *                                                              MemberGroup entity
     *                                                              Product entity
     *                                                              Site entity
     * @param           string          $by             Accepts the following options: 'id', 'code', 'site', 'type', 'coupon', 'member', 'member_group', 'site'
     *
     * @return          array           $response
     */
    public function deleteShoppingOrderStatuses($collection, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Array', 'err.invalid.parameter.collection');
        }
        /** If COLLECTION is ENTITYs then USE ENTITY MANAGER */
        if ($by == 'id') {
            $sub_response = $this->deleteEntities($collection, 'BundleEntity\\ShoppingOrder');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );

                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        }

        /**
         * If COLLECTION is NOT Entitys OR MORE COMPLEX DELETION NEEDED
         * CREATE CUSTOM SQL / DQL
         *
         * If you need custom DELETE, you need to assign $q_str to well formed DQL string; otherwise use
         * $this>prepare_delete.
         */
        $table = $this->entity['shopping_order_status']['name'] . ' ' . $this->entity['shopping_order_status']['alias'];
        $q_str = $this->prepareDelete($table, $this->entity['shopping_order_status']['alias'] . '.' . $by, $collection);

        $query = $this->em->createQuery($q_str);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $collection_count = count($collection);
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => $collection_count,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        return $this->response;
    }

    /**
     * @name 			deleteShoppingOrderItems()
     *  				Deletes provided shopping order items from database.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->delete_entities()
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection consists one of the following:
     *                                                              id
     *                                                              code
     *                                                              type
     *                                                              Coupon entity
     *                                                              Member entity
     *                                                              MemberGroup entity
     *                                                              Product entity
     *                                                              Site entity
     * @param           string          $by             Accepts the following options: 'id', 'code', 'site', 'type', 'coupon', 'member', 'member_group', 'site'
     *
     * @return          array           $response
     */
    public function deleteShoppingOrderItems($collection, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id','product');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValueException', 'Array', 'err.invalid.parameter.collection');
        }
        /** If COLLECTION is ENTITYs then USE ENTITY MANAGER */
        if ($by == 'id') {
            $sub_response = $this->deleteEntities($collection, 'BundleEntity\ShoppingOrderItem');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );

                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        }

        /**
         * If COLLECTION is NOT Entitys OR MORE COMPLEX DELETION NEEDED
         * CREATE CUSTOM SQL / DQL
         *
         * If you need custom DELETE, you need to assign $q_str to well formed DQL string; otherwise use
         * $this>prepare_delete.
         */
        $table = $this->entity['shopping_order_item']['name'] . ' ' . $this->entity['shopping_order_item']['alias'];
        $q_str = $this->prepare_delete($table, $this->entity['shopping_order_item']['alias'] . '.' . $by, $collection);

        $query = $this->em->createQuery($q_str);
        /**
         * 6. Run query
         */
        $query->getResult();
        /**
         * Prepare & Return Response
         */
        $collection_count = count($collection);
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => $collection_count,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.delete.done',
        );
        return $this->response;
    }

    /**
     * @name 			doesShoppingCartExist()
     *  				Checks if entry exists in database.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->getShoppingCart()
     *
     * @param           mixed           $cart           entity, id
     * @param           string          $by             all, entity, id
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesShoppingCartExist($cart, $by = 'id', $bypass = false) {
        $this->resetResponse();
        $exist = false;

        $response = $this->getShoppingCart($cart, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = true;
        }
        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			doesShoppingCartItemExist()
     *  				Checks if entry exists in database.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->getShoppingCartItem()
     *
     * @param           mixed           $item           entity, id
     * @param           string          $by             all, entity, id
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesShoppingCartItemExist($item, $by = 'id', $bypass = false) {
        $this->resetResponse();
        $exist = false;

        $response = $this->getShoppingCartItem($item, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = true;
        }
        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			doesShoppingOrderExist()
     *  				Checks if entry exists in database.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->getShoppingOrder()
     *
     * @param           mixed           $cart           entity, id
     * @param           string          $by             all, entity, id
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesShoppingOrderExist($cart, $by = 'id', $bypass = false) {
        $this->resetResponse();
        $exist = false;

        $response = $this->getShoppingOrder($cart, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = true;
        }
        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			doesShoppingOrderItemExist()
     *  				Checks if entry exists in database.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->getShoppingOrderItem()
     *
     * @param           mixed           $item           entity, id
     * @param           string          $by             all, entity, id
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesShoppingOrderItemExist($item, $by = 'id', $bypass = false) {
        $this->resetResponse();
        $exist = false;

        $response = $this->getShoppingOrderItem($item, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = true;
        }
        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			getShoppingCart()
     *  				Returns details of a shopping cart.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listShoppingCarts()
     *
     * @param           mixed           $cart               id
     * @param           string          $by                 id
     *
     * @return          mixed           $response
     */
    public function getShoppingCart($cart, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($cart) && !is_numeric($cart)) {
            return $this->createException('InvalidParameterException', 'ShoppingCart', 'err.invalid.parameter.cart');
        }
        if (is_object($cart)) {
            if (!$cart instanceof BundleEntity\ShoppingCart) {
                return $this->createException('InvalidParameterException', 'ShoppingCart', 'err.invalid.parameter.cart');
            }
            /**
             * Prepare & Return Response
             */
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
            return $this->resetResponse();
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_cart']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $cart),
                )
            )
        );

        $response = $this->listShoppingCarts($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			getShoppingCartItem()
     *  				Returns details of a shopping cart item.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listShoppingCartItems()
     *
     * @param           mixed           $cart               id
     * @param           string          $by                 id
     *
     * @return          mixed           $response
     */
    public function getShoppingCartItem($item, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($item) && !is_numeric($item)) {
            return $this->createException('InvalidParameterException', 'ShoppingCartItem', 'err.invalid.parameter.cart_item');
        }
        if (is_object($item)) {
            if (!$item instanceof BundleEntity\ShoppingCartItem) {
                return $this->createException('InvalidParameterException', 'ShoppingCartItem', 'err.invalid.parameter.cart_item');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $item,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->resetResponse();
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_cart_item']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $item),
                )
            )
        );

        $response = $this->listShoppingCartItems($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			getShoppingOrder()
     *  				Returns details of a shopping order.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listShoppingOrders()
     *
     * @param           mixed           $order              id
     * @param           string          $by                 id
     *
     * @return          mixed           $response
     */
    public function getShoppingOrder($order, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id','cart');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($order) && !is_numeric($order)) {
            return $this->createException('InvalidParameterException', 'ShoppingOrder', 'err.invalid.parameter.order');
        }
        if (is_object($order)) {
            if (!$order instanceof BundleEntity\ShoppingOrder) {
                return $this->createException('InvalidParameterException', 'ShoppingOrder', 'err.invalid.parameter.order');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $order,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->resetResponse();
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_order']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $order),
                )
            )
        );

        $response = $this->listShoppingOrders($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			getIncompleteShoppingOrder()
     *  				Returns details of a shopping order.
     *
     * @since			1.0.2
     * @version         1.1.4
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listShoppingOrders()
     *
     * @param           mixed           $order
     * @param           mixed           $member
     *
     * @return          mixed           $response
     */
    public function getIncompleteShoppingOrderOfMember($order,$member) {
        $this->resetResponse();
        if (!$order instanceof \stdClass && !is_numeric($order) && !$order instanceof BundleEntity\ShoppingOrder) {
            return $this->createException('InvalidParameterException', 'ShoppingOrder', 'err.invalid.parameter.order');
        }
        if (!$member instanceof \stdClass && !is_numeric($member) && !$member instanceof MMBEntity\Member) {
            return $this->createException('InvalidParameterException', 'Member', 'err.invalid.parameter.order');
        }
        if ($order instanceof \stdClass) {
            $order = $order->id;
        }
        if ($order instanceof BundleEntity\ShoppingOrder) {
            $order = $order->getId();
        }
        if ($member instanceof \stdClass) {
            $member = $member->id;
        }
        if ($member instanceof MMBEntity\Member) {
            $member = $member->getId();
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_order']['alias'] . '.id', 'comparison' => '=', 'value' => $order),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_order']['alias'] . '.purchaser', 'comparison' => '=', 'value' => $member),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_order']['alias'] . '.date_purchased', 'comparison' => 'null', 'value' => $order),
                )
            )
        );

        $response = $this->listShoppingOrders($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			getShoppingOrderItem()
     *  				Returns details of a shopping order item.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listShoppingOrderItems()
     *
     * @param           mixed           $cart               id
     * @param           string          $by                 id
     *
     * @return          mixed           $response
     */
    public function getShoppingOrderItem($item, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($item) && !is_numeric($item)) {
            return $this->createException('InvalidParameterException', 'ShoppingOrderItem', 'err.invalid.parameter.order_item');
        }
        if (is_object($item)) {
            if (!$item instanceof BundleEntity\ShoppingOrderItem) {
                return $this->createException('InvalidParameterException', 'ShoppingOrderItem', 'err.invalid.parameter.order_item');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $item,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->resetResponse();
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_order_item']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $item),
                )
            )
        );

        $response = $this->listShoppingOrderItems($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			getShoppingOrderStatus()
     *  				Returns details of a shopping order status.
     *
     * @since			1.0.2
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     * @use             $this->listShoppingOrderStatus()
     *
     * @param           mixed           $status             id
     * @param           string          $by                 id
     *
     * @return          mixed           $response
     */
    public function getShoppingOrderStatus($status, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id','url_key');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($status) && !is_numeric($status) && !is_string($status)) {
            return $this->createException('InvalidParameterException', 'ShoppingOrderStatus', 'err.invalid.parameter.order_status');
        }
        if (is_object($status)) {
            if (!$status instanceof BundleEntity\ShoppingOrderStatus) {
                return $this->createException('InvalidParameterException', 'ShoppingOrderStatus', 'err.invalid.parameter.order_status');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $status,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->resetResponse();
        }
        switch($by){
            case 'id':
                $column = $this->getEntityDefinition('shopping_order_status','alias').'.'.$by;
                break;
            case 'url_key':
                $column = $this->getEntityDefinition('shopping_order_status_localization','alias').'.'.$by;
                break;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $column, 'comparison' => '=', 'value' => $status),
                )
            )
        );

        $response = $this->listShoppingOrderStatuses($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			getShoppingOrderStatusLocalization()
     *  				Gets a specific shopping order status localization values from database.
     *
     * @since			1.0.3
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           BundleEntity\ShoppingOrderStatus            $status
     * @param           MLSEntity\Language                          $language
     *
     * @return          array           $response
     */
    public function getShoppingOrderStatusLocalization($status, $language) {
        $this->resetResponse();
        if (!$status instanceof BundleEntity\ShoppingOrderStatus) {
            return $this->createException('InvalidParameterException', 'ShoppingOrderStatus', 'err.invalid.parameter.shopping_order_status');
        }
        /** Parameter must be an array */
        if (!$language instanceof MLSEntity\Language) {
            return $this->createException('InvalidParameterException', 'Language', 'err.invalid.parameter.language');
        }
        $q_str = 'SELECT ' . $this->entity['shopping_order_status_localization']['alias'] . ' FROM ' . $this->entity['shopping_order_status_localization']['name'] . ' ' . $this->entity['shopping_order_status_localization']['alias']
            . ' WHERE ' . $this->entity['shopping_order_status_localization']['alias'] . '.status = ' . $status->getId()
            . ' AND ' . $this->entity['shopping_order_status_localization']['alias'] . '.language = ' . $language->getId();

        $query = $this->em->createQuery($q_str);
        /**
         * 6. Run query
         */
        $result = $query->getResult();
        /**
         * Prepare & Return Response
         */
        $total_rows = count($result);

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $result,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist.',
        );
        return $this->response;
    }
    
    /**
     * @name 			insertShoppingCart()
     *  				Inserts one shopping cart into database.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->insertShoppingCarts()
     *
     * @param           mixed           $cart                  Entity or post
     *
     * @return          array           $response
     */
    public function insertShoppingCart($cart) {
        $this->resetResponse();
        return $this->insertShoppingCarts(array($cart));
    }

    /**
     * @name 			insertShoppingCartItem()
     *  				Inserts one shopping cart into into database.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->insertShoppingCartItems()
     *
     * @param           mixed           $item                  Entity or post
     *
     * @return          array           $response
     */
    public function insertShoppingCartItem($item) {
        $this->resetResponse();
        return $this->insertShoppingCartItems(array($item));
    }

    /**
     * @name 			insertShoppingCartItems()
     *  				Inserts one or more shopping cart items into database.
     *
     * @since			1.0.2
     * @version         1.1.0
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertShoppingCartItems($collection) {

        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShoppingCartItem) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $entity = new BundleEntity\ShoppingCartItem;
                if (isset($data->id)) {
                    unset($data->id);
                }
                if (!property_exists($data, 'date_added')) {
                    $data->date_created = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'product':
                            $productModel = $this->kernel->getContainer()->get('productmanagement.model');
                            $response = $productModel->getProduct($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        case 'cart':
                            $response = $this->getShoppingCart($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name 			insertShoppingCarts()
     *  				Inserts one or more shopping carts into database.
     *
     * @since			1.0.2
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertShoppingCarts($collection) {

        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShoppingCart) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $entity = new BundleEntity\ShoppingCart;
                if (isset($data->id)) {
                    unset($data->id);
                }
                if (!property_exists($data, 'date_created')) {
                    $data->date_created = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'member':
                            $memberModel = $this->kernel->getContainer()->get('membermanagement.model');
                            $response = $memberModel->getMember($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        case 'session':
                            $sessionModel = $this->kernel->getContainer()->get('logbundle.model');
                            $response = $sessionModel->getSession($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'success.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name 			insertShoppingOrder()
     *  				Inserts one shopping order  into database.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->insertShoppingOrders()
     *
     * @param           mixed           $order                 Entity or post
     *
     * @return          array           $response
     */
    public function insertShoppingOrder($order) {
        $this->resetResponse();
        return $this->insertShoppingOrders(array($order));
    }

    /**
     * @name 			insertShoppingOrders()
     *  				Inserts one or more shopping orders into database.
     *
     * @since			1.0.2
     * @version         1.0.6
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertShoppingOrders($collection) {

        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShoppingOrder) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $entity = new BundleEntity\ShoppingOrder;
                if (isset($data->id)) {
                    unset($data->id);
                }
                if (!property_exists($data, 'status')) {
                    $data->status = 1;
                }
                if (!property_exists($data, 'date_created')) {
                    $data->date_created = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'cart':
                            $response = $this->getShoppingCart($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        case 'purchaser':
                            $memberModel = $this->kernel->getContainer()->get('membermanagement.model');
                            $response = $memberModel->getMember($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        case 'status':
                            $response = $this->getShoppingOrderStatus($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name 			insertShoppingOrderItem()
     *  				Inserts one shopping order item into database.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->insertShoppingOrderItems()
     *
     * @param           mixed           $item                  Entity or post
     * @param           mixed           $by                    entity, or, post
     *
     * @return          array           $response
     */
    public function insertShoppingOrderItem($item) {
        $this->resetResponse();
        return $this->insertShoppingOrderItems(array($item));
    }

    /**
     * @name 			insertShoppingOrderItems()
     *  				Inserts one or more shopping order items into database.
     *
     * @since			1.0.2
     * @version         1.1.4
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertShoppingOrderItems($collection) {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShoppingOrderItem) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else if (is_object($data)) {
                $entity = new BundleEntity\ShoppingOrderItem;
                if (isset($data->id)) {
                    unset($data->id);
                }
                if (!property_exists($data, 'date_created')) {
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'order':
                            $response = $this->getShoppingOrder($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        case 'product':
                            $productModel = $this->kernel->getContainer()->get('productmanagement.model');
                            $response = $productModel->getProduct($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $productModel);
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        $lastId = null;
        if ($countInserts > 0) {
            $this->em->flush();
            $lastId = $insertedItems[$countInserts-1]->getId();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $lastId,
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name 			insertShoppingOrderStatus()
     *  				Inserts one shopping order status into database.
     *
     * @since			1.0.3
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->insertShoppingOrderStatuses()
     *
     * @param           mixed           $status                Entity or post
     * @param           mixed           $by                    entity, or, post
     *
     * @return          array           $response
     */
    public function insertShoppingOrderStatus($status) {
        $this->resetResponse();
        return $this->insertShoppingOrderStatuses(array($status));
    }

    /**
     * @name 			insertShoppingOrderStatuses()
     *  				Inserts one or more shopping order statuses into database.
     *
     * @since			1.0.3
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection        Collection of entities or post data.
     * @param           string          $by                entity, post
     *
     * @return          array           $response
     */
    public function insertShoppingCartStatuses($collection, $by = 'post') {
        $this->resetResponse();

        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
        }
        if ($by == 'id') {
            $sub_response = $this->insert_entities($collection, 'BundleEntity\\ShoppingOrderStatus');
            /**
             * If there are items that cannot be deleted in the collection then $sub_response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.insert.done.',
                );

                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        }
        /**
         * If by post
         */
        $l_collection = array();
        $to_insert = 0;
        foreach ($collection as $item) {
            $localizations = array();
            if (isset($item['localizations'])) {
                $localizations = $item['localizations'];
                unset($item['localizations']);
            }
            $entity = new BundleEntity\ShoppingOrderStatus();
            foreach ($item as $column => $value) {
                $method = 'set_' . $column;
                if (method_exists($entity, $method)) {
                    $entity->$method($value);
                }
            }
            /** HANDLE FOREIGN DATA :: LOCALIZATIONS */
            if (count($localizations) > 0) {
                $l_collection[] = $localizations;
            }

            $this->insert_entities(array($entity), 'BundleEntity\\ShoppingOrderStatus');

            $entity_localizations = array();
            foreach ($l_collection as $localization) {
                if ($localization instanceof BundleEntity\ShoppingOrderStatusLocalization) {
                    $entity_localizations[] = $localization;
                } else {
                    $localization_entity = new BundleEntity\ShoppingOrderStatusLocalization();
                    $localization_entity->setProduct($entity);
                    foreach ($localization as $key => $value) {
                        $l_method = 'set_' . $key;
                        switch ($key) {
                            case 'language';
                                $MLSModel = new MLSService\MultiLanguageSupportModel($this->kernel, $this->db_connection, $this->orm);
                                $response = $MLSModel->getLanguage($value, 'id');
                                if ($response['error']) {
                                    new CoreExceptions\InvalidLanguageException($this->kernel, $value);
                                    break;
                                }
                                $language = $response['result']['set'];
                                $localization_entity->setLanguage($language);
                                unset($response, $MLSModel);
                                break;
                            default:
                                if (method_exists($localization_entity, $l_method)) {
                                    $localization_entity->$l_method($value);
                                } else {
                                    new CoreExceptions\InvalidMethodException($this->kernel, $method);
                                }
                                break;
                        }
                        $collection[] = $localization_entity;
                    }
                }
            }
            $this->insert_entities($collection, 'BundleEntity\\ShoppingOrderStatusLocalization');
            $entity->setLocalizations($entity_localizations);
            $this->em->persist($entity);
            $to_insert++;
            /** Free some memory */
            unset($entity_localizations);
        }
        $this->em->flush();
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection,
                'total_rows' => $to_insert,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name 			listCancelledShoppingCarts()
     *  				Lists cancelled shopping carts.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @uses            $this->listShoppingCarts()
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listCancelledShoppingCarts($sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        $column = $this->entity['shopping_cart']['alias'] . '.date_cancelled';
        $condition = array('column' => $column, 'comparison' => 'notnull', 'value' => null);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listShoppingCarts($filter, $sortorder, $limit);
        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }

    /**
     * @name 			listCancelledShoppingOrders()
     *  				Lists cancelled shopping orders.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @uses            $this->listShoppingOrders()
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listCancelledShoppingOrders($sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        $column = $this->entity['shopping_order']['alias'] . '.date_cancelled';
        $condition = array('column' => $column, 'comparison' => 'notnull', 'value' => null);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listShoppingOrders($filter, $sortorder, $limit);
        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }

    /**
     * @name 			listCompletedShoppingOrders()
     *  				Lists shopping orders with given flag.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @uses            $this->listShoppingOrdersWithFlag()
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listCompletedShoppingOrders($sortorder = null, $limit = null) {
        return $this->listShoppingOrdersWithFlag('c', $sortorder, $limit);
    }

    /**
     * @name 			listItemsOfShoppingCart()
     *  				List items of shopping cart.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->listShoppingCartItems()
     *
     * @param           mixed           $cart                   Cart entity, id, e-mail, or username.
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listItemsOfShoppingCart($cart, $sortorder = null, $limit = null) {
        $this->resetResponse();
        if (!$cart instanceof BundleEntity\ShoppingCart && !is_numeric($cart) && !is_string($cart)) {
            return $this->createException('InvalidParameterException', 'Member entity', 'err.invalid.parameter.cart');
        }
        if (!is_object($cart)) {
            switch ($cart) {
                case is_numeric($cart):
                    $response = $this->getShoppingCart($cart, 'id');
                    break;
                case is_string($cart):
                    $response = $this->getShoppingCart($cart, 'username');
                    if ($response['error']) {
                        $response = $this->getShoppingCart($cart, 'email');
                    }
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameterException', 'Member entity', 'err.invalid.parameter.cart');
            }
            $cart = $response['result']['set'];
        }
        /**
         * Prepare $filter
         */
        $column = $this->entity['shopping_cart_item']['alias'] . '.cart';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $cart->getId());
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listShoppingCartItems($filter, $sortorder, $limit);

        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }

    /**
     * @name 			listNoneOrderedShoppingCartsOfMember()
     *  				Lists ordered shopping carts.
     *
     * @since			1.1.1
     * @version         1.1.1
     * @author          Can Berkol
     *
     * @uses            $this->listShoppingCarts()
     *
     * @param           mixed           $member
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listNoneOrderedShoppingCartsOfMember($member, $sortorder = null, $limit = null) {
        $this->resetResponse();
        if (!$member instanceof MMBEntity\Member && !is_numeric($member) && !is_string($member)) {
            return $this->createException('InvalidParameterException', 'Member entity', 'err.invalid.parameter.member');
        }
        if (!is_object($member)) {
            $MMModel = new MMBService\MemberManagementModel($this->kernel, $this->db_connection, $this->orm);
            switch ($member) {
                case is_numeric($member):
                    $response = $MMModel->getMember($member, 'id');
                    break;
                case is_string($member):
                    $response = $MMModel->getMember($member, 'username');
                    if ($response['error']) {
                        $response = $MMModel->getMember($member, 'email');
                    }
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameterException', 'Member entity', 'err.invalid.parameter.member');
            }
            $member = $response['result']['set'];
        }
        /**
         * Prepare $filter
         */
        $column = $this->entity['shopping_cart']['alias'] . '.date_ordered';
        $condition = array('column' => $column, 'comparison' => 'null', 'value' => null);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $column = $this->entity['shopping_cart']['alias'] . '.member';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $member->getId());
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listShoppingCarts($filter, $sortorder, $limit);
        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }
    /**
     * @name 			listOpenShoppingOrders()
     *  				Lists open / incomplete shopping orders.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @uses            $this->listShoppingOrdersWithFlag()
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listOpenShoppingOrders($sortorder = null, $limit = null) {
        return $this->listShoppingOrdersWithFlag('o', $sortorder, $limit);
    }

    /**
     * @name 			listOrderedShoppingCarts()
     *  				Lists ordered shopping carts.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @uses            $this->listShoppingCarts()
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listOrderedShoppingCarts($sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        $column = $this->entity['shopping_cart']['alias'] . '.date_ordered';
        $condition = array('column' => $column, 'comparison' => 'notnull', 'value' => null);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listShoppingCarts($filter, $sortorder, $limit);
        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }

    /**
     * @name 			listReturnedShoppingOrders()
     *  				Lists cancelled shopping orders.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @uses            $this->listShoppingOrders()
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listReturnedShoppingOrders($sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        $column = $this->entity['shopping_order']['alias'] . '.date_returned';
        $condition = array('column' => $column, 'comparison' => 'notnull', 'value' => null);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listShoppingOrders($filter, $sortorder, $limit);
        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }

    /**
     * @name 			listPurchasedShoppingOrders()
     *  				Lists purchased shopping orders.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @uses            $this->listShoppingOrders()
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listPurchasedShoppingOrders($sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        $column = $this->entity['shopping_order']['alias'] . '.date_purchased';
        $condition = array('column' => $column, 'comparison' => 'notnull', 'value' => null);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listShoppingOrders($filter, $sortorder, $limit);
        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }

    /**
     * @name 			listShoppingCartItems()
     *  				List shopping cart items from database based on a variety of conditions.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter             Multi-dimensional array
     *
     *                                  Example:
     *                                  $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                               array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.id', 'comparison' => 'in', 'value' => array(3,4,5,6)),
     *                                                                  )
     *                                                  )
     *                                              );
     *                                 $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                              array(
     *                                                                      'glue' => 'or',
     *                                                                      'condition' => array('column' => 'p.status', 'comparison' => 'eq', 'value' => 'a'),
     *                                                              ),
     *                                                              array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.price', 'comparison' => '<', 'value' => 500),
     *                                                              ),
     *                                                             )
     *                                           );
     *
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    private function listShoppingCartItems($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */
        /**         * ************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($query_str)) {
            $query_str = 'SELECT ' . $this->entity['shopping_cart_item']['alias']
                . ' FROM ' . $this->entity['shopping_cart_item']['name'] . ' ' . $this->entity['shopping_cart_item']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if ($sortorder != null) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'quantity':
                    case 'price':
                    case 'subtotal':
                    case 'date_updated':
                    case 'date_added':
                    case 'date_removed':
                    case 'tax':
                    case 'discount':
                    case 'total':
                        $column = $this->entity['shopping_cart_item']['alias'] . '.' . $column;
                        break;
                    default:
                        return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepare_where($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }

        $query_str .= $where_str . $group_str . $order_str;

        $query = $this->em->createQuery($query_str);

        /**
         * Prepare LIMIT section of query
         */
        if ($limit != null) {
            if (isset($limit['start']) && isset($limit['count'])) {
                /** If limit is set */
                $query->setFirstResult($limit['start']);
                $query->setMaxResults($limit['count']);
            } else {
                new CoreExceptions\InvalidLimitException($this->kernel, '');
            }
        }
        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();

        $total_rows = count($result);
        if ($total_rows < 1) {
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $result,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			listShoppingCarts()
     *  				List shopping carts from database based on a variety of conditions.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter             Multi-dimensional array
     *
     *                                  Example:
     *                                  $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                               array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.id', 'comparison' => 'in', 'value' => array(3,4,5,6)),
     *                                                                  )
     *                                                  )
     *                                              );
     *                                 $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                              array(
     *                                                                      'glue' => 'or',
     *                                                                      'condition' => array('column' => 'p.status', 'comparison' => 'eq', 'value' => 'a'),
     *                                                              ),
     *                                                              array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.price', 'comparison' => '<', 'value' => 500),
     *                                                              ),
     *                                                             )
     *                                           );
     *
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listShoppingCarts($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */
        /**         * ************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($query_str)) {
            $query_str = 'SELECT ' . $this->entity['shopping_cart']['alias']
                . ' FROM ' . $this->entity['shopping_cart']['name'] . ' ' . $this->entity['shopping_cart']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if ($sortorder != null) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'date_created':
                    case 'date_cancelled':
                    case 'date_ordered':
                    case 'date_updated':
                    case 'total_amount':
                    case 'count_items':
                        $column = $this->entity['shopping_cart']['alias'] . '.' . $column;
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepare_where($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }

        $query_str .= $where_str . $group_str . $order_str;

        $query = $this->em->createQuery($query_str);

        /**
         * Prepare LIMIT section of query
         */
        if ($limit != null) {
            if (isset($limit['start']) && isset($limit['count'])) {
                /** If limit is set */
                $query->setFirstResult($limit['start']);
                $query->setMaxResults($limit['count']);
            } else {
                new CoreExceptions\InvalidLimitException($this->kernel, '');
            }
        }
        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();

        $total_rows = count($result);
        if ($total_rows < 1) {
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $result,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			listShoppingCartsOfMember()
     *  				List shopping carts that belong to a specific member.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->listShoppingCarts()
     *
     * @param           mixed           $member                 Member entity, id, e-mail, or username.
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listShoppingCartsOfMember($member, $sortorder = null, $limit = null) {
        $this->resetResponse();
        if (!$member instanceof MMBEntity\Member && !is_numeric($member) && !is_string($member)) {
            return $this->createException('InvalidParameterException', 'Member entity', 'err.invalid.parameter.member');
        }
        if (!is_object($member)) {
            $MMModel = new MMBService\MemberManagementModel($this->kernel, $this->db_connection, $this->orm);
            switch ($member) {
                case is_numeric($member):
                    $response = $MMModel->getMember($member, 'id');
                    break;
                case is_string($member):
                    $response = $MMModel->getMember($member, 'username');
                    if ($response['error']) {
                        $response = $MMModel->getMember($member, 'email');
                    }
                    break;
            }
            if ($response['error']) {
                return $this->createException('InvalidParameterException', 'Member entity', 'err.invalid.parameter.member');
            }
            $member = $response['result']['set'];
        }
        /**
         * Prepare $filter
         */
        $column = $this->entity['shopping_cart']['alias'] . '.member';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $member->getId());
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listShoppingCarts($filter, $sortorder, $limit);

        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }

    /**
     * @name 			listShoppingOrderItems()
     *  				List shopping order items from database based on a variety of conditions.
     *
     * @since			1.0.1
     * @version         1.0.1
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter             Multi-dimensional array
     *
     *                                  Example:
     *                                  $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                               array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.id', 'comparison' => 'in', 'value' => array(3,4,5,6)),
     *                                                                  )
     *                                                  )
     *                                              );
     *                                 $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                              array(
     *                                                                      'glue' => 'or',
     *                                                                      'condition' => array('column' => 'p.status', 'comparison' => 'eq', 'value' => 'a'),
     *                                                              ),
     *                                                              array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.price', 'comparison' => '<', 'value' => 500),
     *                                                              ),
     *                                                             )
     *                                           );
     *
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listShoppingOrderItems($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */
        /**         * ************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($query_str)) {
            $query_str = 'SELECT ' . $this->entity['shopping_order_item']['alias']
                . ' FROM ' . $this->entity['shopping_order_item']['name'] . ' ' . $this->entity['shopping_order_item']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if ($sortorder != null) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'quantity':
                    case 'price':
                    case 'subtotal':
                    case 'date_updated':
                    case 'date_added':
                    case 'date_returned':
                    case 'tax':
                    case 'discount':
                    case 'total':
                        $column = $this->entity['shopping_cart_item']['alias'] . '.' . $column;
                        break;
                    default:
                        return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepare_where($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }

        $query_str .= $where_str . $group_str . $order_str;

        $query = $this->em->createQuery($query_str);

        /**
         * Prepare LIMIT section of query
         */
        if ($limit != null) {
            if (isset($limit['start']) && isset($limit['count'])) {
                /** If limit is set */
                $query->setFirstResult($limit['start']);
                $query->setMaxResults($limit['count']);
            } else {
                new CoreExceptions\InvalidLimitException($this->kernel, '');
            }
        }
        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();

        $total_rows = count($result);
        if ($total_rows < 1) {
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $result,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			listShoppingOrder()
     *  				List shopping orders from database based on a variety of conditions.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter             Multi-dimensional array
     *
     *                                  Example:
     *                                  $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                               array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.id', 'comparison' => 'in', 'value' => array(3,4,5,6)),
     *                                                                  )
     *                                                  )
     *                                              );
     *                                 $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                              array(
     *                                                                      'glue' => 'or',
     *                                                                      'condition' => array('column' => 'p.status', 'comparison' => 'eq', 'value' => 'a'),
     *                                                              ),
     *                                                              array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.price', 'comparison' => '<', 'value' => 500),
     *                                                              ),
     *                                                             )
     *                                           );
     *
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listShoppingOrders($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */
        /**         * ************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($query_str)) {
            $query_str = 'SELECT ' . $this->entity['shopping_order']['alias']
                . ' FROM ' . $this->entity['shopping_order']['name'] . ' ' . $this->entity['shopping_order']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if ($sortorder != null) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'date_created':
                    case 'date_cancelled':
                    case 'date_purchased':
                    case 'date_updated':
                    case 'date_returned':
                    case 'total_amount':
                    case 'count_items':
                        $column = $this->entity['shopping_order']['alias'] . '.' . $column;
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepare_where($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }

        $query_str .= $where_str . $group_str . $order_str;

        $query = $this->em->createQuery($query_str);

        /**
         * Prepare LIMIT section of query
         */
        if ($limit != null) {
            if (isset($limit['start']) && isset($limit['count'])) {
                /** If limit is set */
                $query->setFirstResult($limit['start']);
                $query->setMaxResults($limit['count']);
            } else {
                new CoreExceptions\InvalidLimitException($this->kernel, '');
            }
        }
        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();

        $total_rows = count($result);
        if ($total_rows < 1) {
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $result,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			listShoppingOrdersOfCart()
     *  				List shopping orders that belong to a certain cart.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @uses            $this->listShoppingOrders()
     *
     * @param           mixed           $cart                   entity, id
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listShoppingOrdersOfCart($cart, $sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        if (!$cart instanceof BundleEntity\ShoppingCart && !is_integerr($cart)) {
            return $this->createException('InvalidParameterException', 'BundleEntity\\ShoppingCart', 'invalid.parameter.cart');
        }
        if (is_integer($cart)) {
            $response = $this->getShoppingCart($cart, 'id');
            if ($response['error']) {
                return $response;
            }
            $cart = $response['result']['set'];
        }
        $column = $this->entity['shopping_order']['alias'] . '.cart';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $cart->getId());
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listShoppingOrders($filter, $sortorder, $limit);
        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }

    /**
     * @name 			listShoppingOrdersOfMember()
     *  				List shopping orders that belong to a certain member.
     *
     * @since			1.0.2
     * @version         1.1.1
     * @author          Can Berkol
     *
     * @uses            $this->listShoppingOrders()
     *
     * @param           mixed           $member                 entity, id
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    public function listShoppingOrdersOfMember($member, $sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        if (!$member instanceof MMBEntity\Member && !is_int($member)) {
            return $this->createException('InvalidParameterException', 'MMBEntity\\Member', 'invalid.parameter.member');
        }

        if (is_integer($member)) {
            $memberModel = new MMBService\MemberManagementModel($this->kernel, $this->db_connection, $this->orm);
            $response = $memberModel->getMember($member, 'id');
            if ($response['error']) {
                return $response;
            }
            $member = $response['result']['set'];
        }
        $column = $this->entity['shopping_order']['alias'] . '.purchaser';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $member->getId());
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listShoppingOrders($filter, $sortorder, $limit);
        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }

    /**
     * @name 			listShoppingOrderStatuses()
     *  				List shopping order statuses from database based on a variety of conditions.
     *
     * @since			1.0.3
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $filter             Multi-dimensional array
     *
     *                                  Example:
     *                                  $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                               array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.id', 'comparison' => 'in', 'value' => array(3,4,5,6)),
     *                                                                  )
     *                                                  )
     *                                              );
     *                                 $filter[] = array(
     *                                              'glue' => 'and',
     *                                              'condition' => array(
     *                                                              array(
     *                                                                      'glue' => 'or',
     *                                                                      'condition' => array('column' => 'p.status', 'comparison' => 'eq', 'value' => 'a'),
     *                                                              ),
     *                                                              array(
     *                                                                      'glue' => 'and',
     *                                                                      'condition' => array('column' => 'p.price', 'comparison' => '<', 'value' => 500),
     *                                                              ),
     *                                                             )
     *                                           );
     *
     *
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @param           string           $query_str             If a custom query string needs to be defined.
     * @param           bool              $returnLocales
     * @return          array           $response
     */
    public function listShoppingOrderStatuses($filter = null, $sortorder = null, $limit = null, $query_str = null, $returnLocales = false) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrder', '', 'err.invalid.parameter.sortorder');
        }
        /**
         * Add filter checks to below to set join_needed to true.
         */
        /**         * ************************************************** */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';

        /**
         * Start creating the query.
         *
         * Note that if no custom select query is provided we will use the below query as a start.
         */
        if (is_null($query_str)) {
            $query_str = 'SELECT ' . $this->entity['shopping_order_status_localization']['alias'] . ', ' . $this->entity['shopping_order_status']['alias']
                . ' FROM ' . $this->entity['shopping_order_status_localization']['name'] . ' ' . $this->entity['shopping_order_status_localization']['alias']
                . ' JOIN ' . $this->entity['shopping_order_status_localization']['alias'] . '.status ' . $this->entity['shopping_order_status']['alias'];
        }
        /**
         * Prepare ORDER BY section of query.
         */
        if ($sortorder != null) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'date_added':
                    case 'date_updated':
                        $column = $this->entity['shopping_order_status']['alias'] . '.' . $column;
                        break;
                    case 'url_key':
                        $column = $this->entity['shopping_order_status_localization']['alias'] . '.' . $column;
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /**
         * Prepare WHERE section of query.
         */
        if ($filter != null) {
            $filter_str = $this->prepareWhere($filter);
            $where_str .= ' WHERE ' . $filter_str;
        }
        if(!is_null($limit)){
            $lqStr = 'SELECT ' . $this->entity['shopping_order_status_localization']['alias'] . ', ' . $this->entity['shopping_order_status']['alias']
                . ' FROM ' . $this->entity['shopping_order_status_localization']['name'] . ' ' . $this->entity['shopping_order_status_localization']['alias']
                . ' JOIN ' . $this->entity['shopping_order_status_localization']['alias'] . '.status ' . $this->entity['shopping_order_status']['alias'];
            $lqStr .= $where_str.$group_str.$order_str;
            $lQuery = $this->em->createQuery($lqStr);
            $lQuery = $this->addLimit($lQuery, $limit);
            $result = $lQuery->getResult();
            $selectedIds = array();

            foreach($result as $entry){
                $selectedIds[] = $entry->getStatus()->getId();
            }
            if (count($selectedIds)>0){
                $where_str .= ' AND '.$this->entity['shopping_order_status_localization']['alias'].'.status IN('.implode(',', $selectedIds).')';
            }

        }

        $query_str .= $where_str . $group_str . $order_str;
        $query = $this->em->createQuery($query_str);

        /**
         * Prepare & Return Response
         */
        $result = $query->getResult();
        $categories = array();
        $unique = array();
        foreach ($result as $entry) {
            $id = $entry->getStatus()->getId();
            if (!isset($unique[$id])) {
                $categories[$id] = $entry->getStatus();
                $unique[$id] = $entry->getStatus();
            }
            $localizations[$id][] = $entry;
        }
        $total_rows = count($categories);
        $responseSet = array();
        if ($returnLocales) {
            foreach ($categories as $key => $category) {
                $responseSet[$key]['entity'] = $category;
                $responseSet[$key]['localizations'] = $localizations[$key];
            }
        } else {
            $responseSet = $categories;
        }
        $newCollection = array();
        foreach ($responseSet as $item) {
            $newCollection[] = $item;
        }
        unset($responseSet,$categories);

        if ($total_rows < 1) {
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $newCollection,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 			listShoppingOrdersWithFlag()
     *  				Lists shopping orders with given flag.
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @uses            $this->listShoppingOrders()
     *
     * @param           string          $flag
     * @param           array           $sortorder              Array
     *                                      'column'            => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     *
     * @return          array           $response
     */
    private function listShoppingOrdersWithFlag($flag, $sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        $column = $this->entity['shopping_order']['alias'] . '.flag';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $flag);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        $response = $this->listShoppingOrders($filter, $sortorder, $limit);
        if (!$response['error']) {
            return $response;
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $response['result']['set'],
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => true,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }

    /**
     * @name 			updateShoppingCart()
     *  				Updates single shopping cart. The data must be either a post data (array) or an entity
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->updateShoppingCarts()
     *
     * @param           mixed           $data           entity or post data
     * @param           string          $by             entity, post
     *
     * @return          mixed           $response
     */
    public function updateShoppingCart($data, $by = 'post') {
        return $this->updateShoppingCarts(array($data), $by);
    }

    /**
     * @name 			updateShoppingCartItem()
     *  				Updates single shopping cart item. The data must be either a post data (array) or an entity
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->updateShoppingCartItems()
     *
     * @param           mixed           $data           entity or post data
     * @param           string          $by             entity, post
     *
     * @return          mixed           $response
     */
    public function updateShoppingCartItem($data, $by = 'post') {
        return $this->updateShoppingCartItems(array($data), $by);
    }

    /**
     * @name 			updateShoppingCartItems()
     *  				Updates one or more shopping cart items details in database.
     *
     * @since			1.0.2
     * @version         1.1.4
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection      Collection of Product entities or array of entity details.
     *
     * @return          array           $response
     */
    public function updateShoppingCartItems($collection) {

        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countUpdates = 0;
        $updatedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShoppingCartItem) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            } else if (is_object($data)) {
                if (!property_exists($data, 'id') || !is_numeric($data->id)) {
                    return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (property_exists($data, 'date_added')) {
                    unset($data->date_added);
                }
                $response = $this->getShoppingCartItem($data->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'ShoppingCartItem with id ' . $data->id, 'err.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'cart':
                            $response = $this->getShoppingCart($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, $value);
                            }
                            unset($response, $lModel);
                            break;
                        case 'product':
                            $pModel = $this->kernel->getContainer()->get('productManagement.model');
                            $response = $pModel->getProduct($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExistException($this->kernel, $value);
                            }
                            unset($response, $pModel);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if ($oldEntity->isModified()) {
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $updatedItems,
                'total_rows' => $countUpdates,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.update.done',
        );
        return $this->response;
    }

    /**
     * @name 			updateShoppingCarts()
     *  				Updates one or more shopping carts details in database.
     *
     * @since			1.0.2
     * @version         1.1.0
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection      Collection of Product entities or array of entity details.
     *
     * @return          array           $response
     */
    public function updateShoppingCarts($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countUpdates = 0;
        $updatedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShoppingCart) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            } else if (is_object($data)) {
                if (!property_exists($data, 'id') || !is_numeric($data->id)) {
                    return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (property_exists($data, 'date_created')) {
                    unset($data->date_created);
                }
                $response = $this->getShoppingCart($data->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'ProductAttribute with id ' . $data->id, 'err.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'member':
                            $memberModel = $this->kernel->getContainer()->get('membermanagement.model');
                            $response = $memberModel->getMember($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        case 'session':
                            $sessionModel = $this->kernel->getContainer()->get('logbundle.model');
                            $response = $sessionModel->getSession($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if ($oldEntity->isModified()) {
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $updatedItems,
                'total_rows' => $countUpdates,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.update.done',
        );
        return $this->response;
    }

    /**
     * @name 			updateShoppingOrder()
     *  				Updates single shopping order. The data must be either a post data (array) or an entity
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->updateShoppingOrders()
     *
     * @param           mixed           $data           entity or post data
     *
     * @return          mixed           $response
     */
    public function updateShoppingOrder($data) {
        return $this->updateShoppingOrders(array($data));
    }

    /**
     * @name 			updateShoppingOrderItem()
     *  				Updates single shopping order item. The data must be either a post data (array) or an entity
     *
     * @since			1.0.2
     * @version         1.0.2
     * @author          Can Berkol
     *
     * @use             $this->updateShoppingOrderItems()
     *
     * @param           mixed           $data           entity or post data
     * @param           string          $by             entity, post
     *
     * @return          mixed           $response
     */
    public function updateShoppingOrderItem($data, $by = 'post') {
        return $this->updateShoppingOrderItems(array($data), $by);
    }

    /**
     * @name 			updateShoppingOrderItems()
     *  				Updates one or more shopping order items details in database.
     *
     * @since			1.0.2
     * @version         1.1.4
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection      Collection of Product entities or array of entity details.
     *
     * @return          array           $response
     */
    public function updateShoppingOrderItems($collection) {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countUpdates = 0;
        $updatedItems = array();

        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShoppingOrder) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            }
            else if (is_object($data)) {
                if (!property_exists($data, 'id') || !is_numeric($data->id)) {
                    return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (property_exists($data, 'date_created')) {
                    unset($data->date_added);
                }
                $response = $this->getShoppingOrderItem($data->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'ShoppingOrder with id ' . $data->id, 'err.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                foreach ($data as $column => $value) {
                    $set = 'set'.$this->translateColumnName($column);
                    switch ($column) {
                        case 'order':
                            $response = $this->getShoppingOrder($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        case 'product':
                            $productModel = $this->kernel->getContainer()->get('productmanagement.model');
                            $response = $productModel->getProduct($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\EntityDoesNotExist($this->kernel, $value);
                            }
                            unset($response, $productModel);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if ($oldEntity->isModified()) {
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $updatedItems,
                'total_rows' => $countUpdates,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.update.done',
        );
        return $this->response;
    }


    /**
     * @name 			updateShoppingOrders()
     *  				Updates one or more shopping orders details in database.
     *
     * @since			1.1.4
     * @version         1.1.4
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection      Collection of Product entities or array of entity details.
     *
     * @return          array           $response
     */
    public function updateShoppingOrders($collection)
    {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countUpdates = 0;
        $updatedItems = array();

        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\ShoppingOrder) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            }
            else if (is_object($data)) {
                if (!property_exists($data, 'id') || !is_numeric($data->id)) {
                    return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
                }
                if (!property_exists($data, 'date_updated')) {
                    $data->date_updated = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (property_exists($data, 'date_created')) {
                    unset($data->date_added);
                }
                $response = $this->getShoppingOrder($data->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'ShoppingOrderItem with id ' . $data->id, 'err.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                foreach ($data as $column => $value) {
                    $set = 'set'.$this->translateColumnName($column);
                    switch ($column) {
                        case 'purchaser':
                            $memberModel = $this->kernel->getContainer()->get('membermanagement.model');
                            $response = $memberModel->getMember($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            }
                            else {
                                new CoreExceptions\SiteDoesNotExistException($this->kernel, 'member' . $value);
                            }
                            unset($response, $fModel);
                            break;
                        case 'cart':
                            $response = $this->getShoppingCart($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\SiteDoesNotExistException($this->kernel, $value);
                            }
                            unset($response, $fModel);
                            break;
                        case 'status':
                            $response = $this->getShoppingOrderStatus($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\SiteDoesNotExistException($this->kernel, $value);
                            }
                            unset($response, $fModel);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if ($oldEntity->isModified()) {
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $updatedItems,
                'total_rows' => $countUpdates,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.update.done',
        );
        return $this->response;
    }

    /**
     * @name 			updateShoppingOrderStatus()
     *  				Updates single shopping order status. The data must be either a post data (array) or an entity
     *
     * @since			1.0.3
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->updateShoppingOrderStatuses()
     *
     * @param           mixed           $data           entity or post data
     * @param           string          $by             entity, post
     *
     * @return          mixed           $response
     */
    public function updateShoppingOrderStatus($data, $by = 'post') {
        return $this->updateShoppingOrderStatuses(array($data), $by);
    }

    /**
     * @name 			updateShoppingOrderStatuses()
     *  				Updates one or more shopping order statuses details in database.
     *
     * @since			1.0.3
     * @version         1.0.3
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array           $collection      Collection of Product entities or array of entity details.
     * @param           array           $by              entity, post
     *
     * @return          array           $response
     */
    public function updateShoppingOrderStatuses($collection, $by = 'post') {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
        }
        $by_opts = array('id', 'post');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if ($by == 'id') {
            $sub_response = $this->update_entities($collection, 'BundleEntity\\ShoppingOrderStatuses');
            /**
             * If there are items that cannot be deleted in the collection then $sub_Response['process']
             * will be equal to continue and we need to continue process; otherwise we can return response.
             */
            if ($sub_response['process'] == 'stop') {
                $this->response = array(
                    'rowCount' => $this->response['rowCount'],
                    'result' => array(
                        'set' => $sub_response['entries']['valid'],
                        'total_rows' => $sub_response['item_count'],
                        'last_insert_id' => null,
                    ),
                    'error' => false,
                    'code' => 'scc.db.delete.done',
                );
                return $this->response;
            } else {
                $collection = $sub_response['entries']['invalid'];
            }
        }
        /**
         * If by post
         */
        $to_update = array();
        $count = 0;
        $collection_by_id = array();
        foreach ($collection as $item) {
            if (!isset($item['id'])) {
                unset($collection[$count]);
            }
            $to_update[] = $item['id'];
            $collection_by_id[$item['id']] = $item;
            $count++;
        }
        unset($collection);
        $filter = array(
            array(
                'glue' => 'and',
                'condition' => array(
                    array(
                        'glue' => 'and',
                        'condition' => array('column' => $this->entity['shopping_order_status']['alias'] . '.id', 'comparison' => 'in', 'value' => $to_update),
                    )
                )
            )
        );
        $response = $this->listShoppingOrderStatuses($filter);
        if ($response['error']) {
            return $this->createException('InvalidParameterException', 'Array', 'err.invalid.parameter.collection');
        }
        $entities = $response['result']['set'];
        foreach ($entities as $entity) {
            $data = $collection_by_id[$entity->getId()];
            /** Prepare foreign key data for process */
            $localizations = array();
            if (isset($data['localizations'])) {
                $localizations = $data['localizations'];
            }
            unset($data['localizations']);

            foreach ($data as $column => $value) {
                $method_set = 'set_' . $column;
                $method_get = 'get_' . $column;
                /**
                 * Set the value only if there is a corresponding value in collection and if that value is different
                 * from the one set in database
                 */
                if (isset($collection_by_id[$entity->getId()][$column]) && $collection_by_id[$entity->getId()][$column] != $entity->$method_get()) {
                    $entity->$method_set($value);
                }
                /** HANDLE FOREIGN DATA :: LOCALIZATIONS */
                $l_collection = array();
                foreach ($localizations as $lang => $localization) {
                    $MLSModel = new MultiLanguageSupportModel($this->kernel, $this->db_connection, $this->orm);
                    $response = $MLSModel->getLanguage($lang, 'iso_code');
                    if ($response['error']) {
                        new CoreExceptions\InvalidLanguageException($this->kernel, $value);
                        break;
                    }
                    $language = $response['result']['set'];
                    $translation_exists = true;
                    $response = $this->getShoppingOrderStatusLocalization($entity, $language);
                    if ($response['error']) {
                        $localization_entity = new BundleEntity\ShoppingOrderStatusLocalization;
                        $translation_exists = false;
                    } else {
                        $localization_entity = $response['result']['set'];
                    }
                    foreach ($localization as $key => $value) {
                        $l_method = 'set_' . $key;
                        switch ($key) {
                            case 'language';
                                $language = $response['result']['set'];
                                $localization_entity->setLanguage($language);
                                unset($language, $response, $MLSModel);
                                break;
                            default:
                                $localization_entity->$l_method($value);
                                break;
                        }
                    }
                    $l_collection[] = $localization_entity;
                    if (!$translation_exists) {
                        $this->em->persists($localization_entity);
                    }
                }
                $entity->setLocalizations($l_collection);
                $this->em->persist($entity);
            }
        }
        $this->em->flush();

        $total_rows = count($to_update);
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $to_update,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.update.done',
        );
        return $this->response;
    }

    /**
     * @name 		    deleteCoupon()
     *                  Deletes an existing item from database.
     *
     * @since		    1.0.2
     * @version         1.0.8
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->deleteCoupons()
     *
     * @param           mixed           $coupon           Entity, id or url key of item
     *
     * @return          mixed           $response
     */
    public function deleteCoupon($coupon) {
        return $this->deleteCoupons(array($coupon));
    }

    /**
     * @name            deleteCoupons()
     *                  Deletes provided items from database.
     *
     * @since		    1.0.2
     * @version         1.0.8
     * @author          Can Berkol
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           array           $collection     Collection of Coupon entities, ids, or codes or url keys
     * @param           string          $by             Accepts the following options: entity, id, code, url_key
     *
     * @return          array           $response
     */
    public function deleteCoupons($collection){
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameterValue', 'Array', 'err.invalid.parameter.collection');
        }
        $countDeleted = 0;
        foreach ($collection as $entry) {
            if ($entry instanceof BundleEntity\Coupon) {
                $this->em->remove($entry);
                $countDeleted++;
            }
            else {
                switch ($entry) {
                    case is_numeric($entry):
                        $response = $this->getCoupon($entry, 'id');
                        break;
                    case is_string($entry):
                        $response = $this->getCoupon($entry, 'sku');
                        break;
                }
                if ($response['error']) {
                    $this->createException('EntryDoesNotExist', $entry, 'err.invalid.entry');
                }
                $entry = $response['result']['set'];
                $this->em->remove($entry);
                $countDeleted++;
            }
        }
        if ($countDeleted < 0) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.fail.delete';

            return $this->response;
        }
        $this->em->flush();
        $this->response = array(
            'rowCount' => 0,
            'result' => array(
                'set' => null,
                'total_rows' => $countDeleted,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.deleted',
        );
        return $this->response;
    }

    /**
     * @name            listCoupons()
     * List items of a given collection.
     *
     * @since		1.0.2
     * @version         1.0.2
     * @author          Said Imamoglu
     *
     * @use             $this->resetResponse()
     * @use             $this->createException()
     * @use             $this->prepare_where()
     * @use             $this->createQuery()
     * @use             $this->getResult()
     *
     * @throws          InvalidSortOrderException
     * @throws          InvalidLimitException
     *
     *
     * @param           mixed           $filter                Multi dimensional array
     * @param           array           $sortorder              Array
     *                                                              'column'    => 'asc|desc'
     * @param           array           $limit
     *                                      start
     *                                      count
     * @param           string           $query_str             If a custom query string needs to be defined.
     *
     * @return          array           $response
     */
    public function listCoupons($filter = null, $sortorder = null, $limit = null, $query_str = null) {
        $this->resetResponse();
        if (!is_array($sortorder) && !is_null($sortorder)) {
            return $this->createException('InvalidSortOrderException', '', 'err.invalid.parameter.sortorder');
        }

        /**
         * Add filter check to below to set join_needed to true
         */
        $order_str = '';
        $where_str = '';
        $group_str = '';
        $filter_str = '';


        /**
         * Start creating the query
         *
         * Note that if no custom select query is provided we will use the below query as a start
         */
        $localizable = false;
        if (is_null($query_str)) {
            if ($localizable) {
                $query_str = 'SELECT ' . $this->entity['coupon_localization']['alias']
                    . ' FROM ' . $this->entity['coupon_localization']['name'] . ' ' . $this->entity['coupon_localization']['alias']
                    . ' JOIN ' . $this->entity['coupon_localization']['alias'] . '.coupon ' . $this->entity['coupon']['alias'];
            } else {
                $query_str = 'SELECT ' . $this->entity['coupon']['alias']
                    . ' FROM ' . $this->entity['coupon']['name'] . ' ' . $this->entity['coupon']['alias'];
            }
        }
        /*
         * Prepare ORDER BY section of query
         */
        if (!is_null($sortorder)) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'id':
                    case 'name':
                    case 'url_key':
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        /*
         * Prepare WHERE section of query
         */

        if (!is_null($filter)) {
            $filter_str = $this->prepare_where($filter);
            $where_str = ' WHERE ' . $filter_str;
        }



        $query_str .= $where_str . $group_str . $order_str;


        $query = $this->em->createQuery($query_str);
        /**
         * Prepare LIMIT section of query
         */

        if (!is_null($limit) && is_numeric($limit)) {
            /*
             * if limit is set
             */
            if (isset($limit['start']) && isset($limit['count'])) {
                $query = $this->addLimit($query, $limit);
            } else {
                $this->createException('InvalidLimitException', '', 'err.invalid.limit');
            }
        }
        /**
         * Prepare and Return Response
         */

        $files = $query->getResult();


        $total_rows = count($files);
        if ($total_rows < 1) {
            $this->response['error'] = true;
            $this->response['code'] = 'err.db.entry.notexist';
            return $this->response;
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $files,
                'total_rows' => $total_rows,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );

        return $this->response;
    }

    /**
     * @name 		getCoupon()
     * Returns details of a gallery.
     *
     * @since		1.0.2
     * @version         1.0.2
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listCoupons()
     *
     * @param           mixed           $coupon               id, url_key
     * @param           string          $by                 entity, id, url_key
     *
     * @return          mixed           $response
     */
    public function getCoupon($coupon, $by = 'id') {
        $this->resetResponse();
        $by_opts = array('id', 'url_key','code');
        if (!in_array($by, $by_opts)) {
            return $this->createException('InvalidParameterValueException', implode(',', $by_opts), 'err.invalid.parameter.by');
        }
        if (!is_object($coupon) && !is_numeric($coupon) && !is_string($coupon)) {
            return $this->createException('InvalidParameterException', 'Coupon', 'err.invalid.parameter');
        }
        if (is_object($coupon)) {
            if (!$coupon instanceof BundleEntity\Coupon) {
                return $this->createException('InvalidParameterException', 'Coupon', 'err.invalid.parameter');
            }
            /**
             * Prepare & Return Response
             */
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => $coupon,
                    'total_rows' => 1,
                    'last_insert_id' => null,
                ),
                'error' => false,
                'code' => 'scc.db.entry.exist',
            );
            return $this->response;
        }
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['coupon']['alias'] . '.' . $by, 'comparison' => '=', 'value' => $coupon),
                )
            )
        );

        $response = $this->listCoupons($filter, null, array('start' => 0, 'count' => 1));
        if ($response['error']) {
            return $response;
        }
        $collection = $response['result']['set'];
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $collection[0],
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 		doesCouponExist()
     * Checks if entry exists in database.
     *
     * @since		1.0.2
     * @version         1.0.2
     * @author          Said İmamoğlu
     *
     * @use             $this->getCoupon()
     *
     * @param           mixed           $coupon           id, url_key
     * @param           string          $by             id, url_key
     *
     * @param           bool            $bypass         If set to true does not return response but only the result.
     *
     * @return          mixed           $response
     */
    public function doesCouponExist($coupon, $by = 'id', $bypass = false) {
        $this->resetResponse();
        $exist = false;

        $response = $this->getCoupon($coupon, $by);

        if (!$response['error'] && $response['result']['total_rows'] > 0) {
            $exist = $response['result']['set'];
            $error = false;
        } else {
            $exist = false;
            $error = true;
        }

        if ($bypass) {
            return $exist;
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $exist,
                'total_rows' => 1,
                'last_insert_id' => null,
            ),
            'error' => $error,
            'code' => 'scc.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name 		    insertCoupon()
     *                  Inserts one or more item into database.
     *
     * @since		    1.0.1
     * @version         1.0.3
     * @author          Said İmamoğlu
     *
     * @use             $this->insertCoupons()
     *
     * @param           mixed           $coupon        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertCoupon($coupon) {
        return $this->insertCoupons(array($coupon));
    }

    /**
     * @name            insertCoupons()
     *                  Inserts one or more items into database.
     *
     * @since           1.0.1
     * @version         1.0.6
     *
     * @author          Can Berkol
     * @author          Said Imamoglu
     *
     * @use             $this->createException()
     *
     * @throws          InvalidParameterException
     * @throws          InvalidMethodException
     *
     * @param           array           $collection        Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertCoupons($collection) {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $countLocalizations = 0;
        $insertedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\Coupon) {
                $entity = $data;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            }
            else if (is_object($data)) {
                $localizations = array();
                $entity = new BundleEntity\Coupon;
                if (!property_exists($data, 'date_published')) {
                    $data->date_published = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
                }
                if (!property_exists($data, 'type')) {
                    $data->type = 'a';
                }
                if (!property_exists($data, 'discount')) {
                    $data->discount = 5;
                }
                if (!property_exists($data, 'site')) {
                    $data->site = 1;
                }
                if (!property_exists($data, 'type_usage')) {
                    $data->type_usage = 's';
                }
                foreach ($data as $column => $value) {
                    $localeSet = false;
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations[$countInserts]['localizations'] = $value;
                            $localeSet = true;
                            $countLocalizations++;
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value, 'id');
                            if (!$response['error']) {
                                $entity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\SiteDoesNotExistException($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        default:
                            $entity->$set($value);
                            break;
                    }
                    if ($localeSet) {
                        $localizations[$countInserts]['entity'] = $entity;
                    }
                }
                $this->em->persist($entity);
                $insertedItems[] = $entity;

                $countInserts++;
            }
            else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        /** Now handle localizations */
        if ($countInserts > 0 && $countLocalizations > 0) {
            $this->insertCouponLocalizations($localizations);
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => $entity->getId(),
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }
    /**
     * @name            insertCouponLocalizations()
     *                  Inserts one or more coupon localizations into database.
     *
     * @since           1.0.6
     * @version         1.0.6
     * @author          Can Berkol
     *
     * @use             $this->createException()
     *
     * @param           array $collection Collection of entities or post data.
     *
     * @return          array           $response
     */
    public function insertCouponLocalizations($collection){
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countInserts = 0;
        $insertedItems = array();
        foreach ($collection as $item) {
            if ($item instanceof BundleEntity\CouponLocalization) {
                $entity = $item;
                $this->em->persist($entity);
                $insertedItems[] = $entity;
                $countInserts++;
            } else {
                foreach ($item['localizations'] as $language => $data) {
                    $entity = new BundleEntity\CouponLocalization;
                    $entity->setCoupon($item['entity']);
                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                    $response = $mlsModel->getLanguage($language, 'iso_code');
                    if (!$response['error']) {
                        $entity->setLanguage($response['result']['set']);
                    }
                    else {
                        break 1;
                    }
                    foreach ($data as $column => $value) {
                        $set = 'set' . $this->translateColumnName($column);
                        $entity->$set($value);
                    }
                    $this->em->persist($entity);
                }
                $insertedItems[] = $entity;
                $countInserts++;
            }
        }
        if ($countInserts > 0) {
            $this->em->flush();
        }
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $insertedItems,
                'total_rows' => $countInserts,
                'last_insert_id' => -1,
            ),
            'error' => false,
            'code' => 'scc.db.insert.done',
        );
        return $this->response;
    }

    /**
     * @name            updateCoupon()
     * Updates single item. The item must be either a post data (array) or an entity
     *
     * @since           1.0.2
     * @version         1.0.7
     * @author          Said Imamoglu
     *
     * @use             $this->resetResponse()
     * @use             $this->updateCoupons()
     *
     * @param           mixed   $coupon     Entity or Entity id of a folder
     *
     * @return          array   $response
     *
     */

    public function updateCoupon($coupon) {
        return $this->updateCoupons(array($coupon));
    }

    /**
     * @name            updateCoupons()
     *                  Updates one or more item details in database.
     *
     * @since           1.0.2
     * @version         1.0.7
     * @author          Can Berkol
     * @author          Said Imamoglu
     *
     * @use             $this->update_entities()
     * @use             $this->createException()
     * @use             $this->listCoupons()
     *
     *
     * @throws          InvalidParameterException
     *
     * @param           array   $collection     Collection of item's entities or array of entity details.
     *
     * @return          array   $response
     *
     */
    public function updateCoupons($collection) {
        $this->resetResponse();
        /** Parameter must be an array */
        if (!is_array($collection)) {
            return $this->createException('InvalidParameter', 'Array', 'err.invalid.parameter.collection');
        }
        $countUpdates = 0;
        $updatedItems = array();
        foreach ($collection as $data) {
            if ($data instanceof BundleEntity\Coupon) {
                $entity = $data;
                $this->em->persist($entity);
                $updatedItems[] = $entity;
                $countUpdates++;
            }
            else if (is_object($data)) {
                if (!property_exists($data, 'id') || !is_numeric($data->id)) {
                    return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
                }
                if (!property_exists($data, 'site')) {
                    $data->site = 1;
                }
                $response = $this->getCoupon($data->id, 'id');
                if ($response['error']) {
                    return $this->createException('EntityDoesNotExist', 'Coupon with id ' . $data->id, 'err.invalid.entity');
                }
                $oldEntity = $response['result']['set'];
                foreach ($data as $column => $value) {
                    $set = 'set' . $this->translateColumnName($column);
                    switch ($column) {
                        case 'local':
                            $localizations = array();
                            foreach ($value as $langCode => $translation) {
                                $localization = $oldEntity->getLocalization($langCode, true);
                                $newLocalization = false;
                                if (!$localization) {
                                    $newLocalization = true;
                                    $localization = new BundleEntity\CouponLocalization();
                                    $mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
                                    $response = $mlsModel->getLanguage($langCode, 'iso_code');
                                    $localization->setLanguage($response['result']['set']);
                                    $localization->setCoupon($oldEntity);
                                }
                                foreach ($translation as $transCol => $transVal) {
                                    $transSet = 'set' . $this->translateColumnName($transCol);
                                    $localization->$transSet($transVal);
                                }
                                if ($newLocalization) {
                                    $this->em->persist($localization);
                                }
                                $localizations[] = $localization;
                            }
                            $oldEntity->setLocalizations($localizations);
                            break;
                        case 'site':
                            $sModel = $this->kernel->getContainer()->get('sitemanagement.model');
                            $response = $sModel->getSite($value, 'id');
                            if (!$response['error']) {
                                $oldEntity->$set($response['result']['set']);
                            } else {
                                new CoreExceptions\SiteDoesNotExistException($this->kernel, $value);
                            }
                            unset($response, $sModel);
                            break;
                        case 'id':
                            break;
                        default:
                            $oldEntity->$set($value);
                            break;
                    }
                    if ($oldEntity->isModified()) {
                        $this->em->persist($oldEntity);
                        $countUpdates++;
                        $updatedItems[] = $oldEntity;
                    }
                }
            } else {
                new CoreExceptions\InvalidDataException($this->kernel);
            }
        }
        if ($countUpdates > 0) {
            $this->em->flush();
        }
        /**
         * Prepare & Return Response
         */
        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $updatedItems,
                'total_rows' => $countUpdates,
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'scc.db.update.done',
        );
        return $this->response;
    }
    public function listItemsOfShoppingOrder($id){
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_order_item']['alias'].'.order', 'comparison' => '=', 'value' => $id),
                )
            )
        );
        return $this->listShoppingOrderItems($filter);

    }


    /**
     * @name    listPurchasedOrders()
     * @author  Said İmamoğlu
     *
     * @version 1.0.5
     * @since   1.0.5
     *
     *
     * @param bool $returned
     * @param bool $cancelled
     * @param null $sortorder
     * @param null $limit
     *
     *
     * @return array $response
     */
    public function listPurchasedOrders($returned = false, $cancelled = false, $sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        $filter = array();
        if ($returned) {
            $column = $this->entity['shopping_order']['alias'] . '.date_returned';
            $condition = array('column' => $column, 'comparison' => '!=', 'value' => null);
            $filter[] = array(
                'glue' => 'and',
                'condition' => array(
                    array(
                        'glue' => 'and',
                        'condition' => $condition,
                    )
                )
            );
        }
        if ($cancelled) {
            $column = $this->entity['shopping_order']['alias'] . '.date_cancelled';
            $condition = array('column' => $column, 'comparison' => '!=', 'value' => null);
            $filter[] = array(
                'glue' => 'and',
                'condition' => array(
                    array(
                        'glue' => 'and',
                        'condition' => $condition,
                    )
                )
            );
        }
        $column = $this->entity['shopping_order']['alias'] . '.date_purchased';
        $condition = array('column' => $column, 'comparison' => '!=', 'value' => null);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        return $this->listShoppingOrders($filter, $sortorder, $limit);
    }

    /**
     * @name    listPurchasedOrders()
     * @author  Said İmamoğlu
     *
     * @version 1.0.5
     * @since   1.0.5
     *
     *
     * @param bool $dateStart
     * @param bool $dateEnd
     * @param bool $returned
     * @param bool $cancelled
     * @param null $sortorder
     * @param null $limit
     *
     *
     * @return array $response
     */
    public function listPurchasedOrdersBetween($dateStart, $dateEnd, $returned = false, $cancelled = false, $sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        $filter = array();
        if ($returned) {
            $column = $this->entity['shopping_order']['alias'] . '.date_returned';
            $condition = array('column' => $column, 'comparison' => '!=', 'value' => null);
            $filter[] = array(
                'glue' => 'and',
                'condition' => array(
                    array(
                        'glue' => 'and',
                        'condition' => $condition,
                    )
                )
            );
        }
        if ($cancelled) {
            $column = $this->entity['shopping_order']['alias'] . '.date_cancelled';
            $condition = array('column' => $column, 'comparison' => '!=', 'value' => null);
            $filter[] = array(
                'glue' => 'and',
                'condition' => array(
                    array(
                        'glue' => 'and',
                        'condition' => $condition,
                    )
                )
            );
        }
        $column = $this->entity['shopping_order']['alias'] . '.date_purchased';
        $condition = array('column' => $column, 'comparison' => 'between', 'value' => array($dateStart,$dateEnd));
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        return $this->listShoppingOrders($filter, $sortorder, $limit);

    }

    /**
     * @name    listPurchasedOrdersOfMember()
     * @author  Said İmamoğlu
     *
     * @version 1.0.5
     * @since   1.0.5
     *
     *
     * @param bool $member
     * @param null $sortorder
     * @param null $limit
     *
     *
     * @return array $response
     */
    public function listPurchasedOrdersOfMember($member, $sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        $filter = array();
        $column = $this->entity['shopping_order']['alias'] . '.purchaser';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $member);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        return $this->listShoppingOrders($filter, $sortorder, $limit);

    }

    /**
     * @name    listPurchasedOrdersOfMember()
     * @author  Said İmamoğlu
     *
     * @version 1.0.5
     * @since   1.0.5
     *
     *
     * @param bool $member
     * @param   $dateStart
     * @param   $dateEnd
     * @param null $sortorder
     * @param null $limit
     *
     *
     * @return array $response
     */
    public function getTotalSalesVolumeOfMember($member, $dateStart = false, $dateEnd = false, $sortorder = null, $limit = null) {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        $filter = array();
        $column = $this->entity['shopping_order']['alias'] . '.purchaser';
        $condition = array('column' => $column, 'comparison' => '=', 'value' => $member);
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => $condition,
                )
            )
        );
        if ($dateEnd != false && $dateStart != false) {
            $column = $this->entity['shopping_order']['alias'] . '.date_purchased';
            $condition = array('column' => $column, 'comparison' => 'between', 'value' => array($dateStart,$dateEnd));
            $filter[] = array(
                'glue' => 'and',
                'condition' => array(
                    array(
                        'glue' => 'and',
                        'condition' => $condition,
                    )
                )
            );
        }
        $response = $this->listShoppingOrders($filter, $sortorder, $limit);
        if ($response['error']) {
            return $response;
        }
        (float) $total = 0;
        foreach ($response['result']['set'] as $order) {
            $total += $order->getTotalAmount();
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $total,
                'total_rows' => $response['result']['total_rows'],
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'err.db.entry.notexist',
        );
        return $this->response;
    }
    /**
     * @name            listRedeemedCoupons()
     *                  Lists redeemed coupons
     *
     * @since           1.0.9
     * @version         1.0.9
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     *
     * @param           mixed               $filter           ProductCategoru entity, id
     * @param           array               $sortorder          Array
     *                                                          'column'            => 'asc|desc'
     * @param           array               $limit
     * @param           array               $query_str
     *
     *
     * @return          array           $response
     */
    public function listRedeemedCoupons($filter = null, $sortorder = null, $limit = null, $query_str = null)
    {
        $this->resetResponse();
        /**
         * Prepare $filter
         */
        $q_str = 'SELECT ' . $this->entity['redeemed_coupon']['alias']
            . ' FROM ' . $this->entity['redeemed_coupon']['name'] . ' ' . $this->entity['redeemed_coupon']['alias'];
        /**
         * Prepare ORDER BY section of query.
         */
        $order_str = '';
        if ($sortorder != null) {
            foreach ($sortorder as $column => $direction) {
                switch ($column) {
                    case 'sort_order':
                        $column = $this->entity['redeemed_coupon']['alias'] . '.' . $column;
                        break;
                    default:
                        $column = $this->entity['redeemed_coupon']['alias'] . '.' . $column;
                        break;
                }
                $order_str .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
            }
            $order_str = rtrim($order_str, ', ');
            $order_str = ' ORDER BY ' . $order_str . ' ';
        }

        $q_str .= $order_str;

        $query = $this->em->createQuery($q_str);

        /**
         * Prepare LIMIT section of query
         */
        if ($limit != null) {
            if (isset($limit['start']) && isset($limit['count'])) {
                /** If limit is set */
                $query->setFirstResult($limit['start']);
                $query->setMaxResults($limit['count']);
            } else {
                new CoreExceptions\InvalidLimitException($this->kernel, '');
            }
        }

        $result = $query->getResult();

        $total_rows = count($result);
        if ($total_rows == 0) {
            $this->response = array(
                'rowCount' => $this->response['rowCount'],
                'result' => array(
                    'set' => null,
                    'total_rows' => $total_rows,
                    'last_insert_id' => null,
                ),
                'error' => true,
                'code' => 'err.db.entry.notexist',
            );
            return $this->response;
        }

        $this->response = array(
            'rowCount' => $this->response['rowCount'],
            'result' => array(
                'set' => $result,
                'total_rows' => count($result),
                'last_insert_id' => null,
            ),
            'error' => false,
            'code' => 'err.db.entry.exist',
        );
        return $this->response;
    }

    /**
     * @name    getShoppingOrderOfMember()
     *          Gets shopping order of member
     *
     * @since   1.1.3
     * @version 1.1.3
     *
     * @param   mixed   $order
     * @param   mixed   $member
     *
     * @return  Response
     */
    public function getShoppingOrderOfMember($order,$member){
        if ((!is_int($order) && !$order instanceof \stdClass && !$order instanceof BundleEntity\ShoppingOrder) || (!is_int($member) && !$member instanceof \stdClass && !$member instanceof MMBEntity\Member)) {
            return $this->createException('InvalidParameter', 'ShoppingOrder or Member', 'err.invalid.parameter');
        }
        if ($member instanceof \stdClass) {
            $member = $member->id;
        }
        if ($member instanceof MMBEntity\Member) {
            $member = $member->getId;
        }
        if ($order instanceof \stdClass) {
            $order = $order->id;
        }
        if ($order instanceof BundleEntity\ShoppingOrder) {
            $order = $order->getId();
        }

        /** Prepare filter */
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_order']['alias'] . '.purchaser', 'comparison' => '=', 'value' => $member),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_order']['alias'] . '.id', 'comparison' => '=', 'value' => $order),
                ),
            )
        );
        $response  = $this->listShoppingOrders($filter,null , array('start'=>0,'count'=>1));
        if ($response['error']) {
            return $response;
        }
        $response['result']['set'] = $response['result']['set'][0];
        return $response;
    }

    /**
     * @name    completeOrder()
     *          Complete an order and create a payment transaction
     *
     * @since   1.1.4
     * @version 1.1.5
     *
     * @param   mixed   $order
     * @param   mixed   $member
     * @param   array   $params
     *
     * @return  Response
     */
    public function completeOrder($order,$member,$params = array()){
        if ((!is_int($order) && !$order instanceof \stdClass && !$order instanceof BundleEntity\ShoppingOrder) || (!is_int($member) && !$member instanceof \stdClass && !$member instanceof MMBEntity\Member)) {
            return $this->createException('InvalidParameter', 'ShoppingOrder or Member', 'err.invalid.parameter');
        }
        if ($member instanceof \stdClass) {
            $member = $member->id;
        }
        if ($member instanceof MMBEntity\Member) {
            $member = $member->getId;
        }
        if ($order instanceof \stdClass) {
            $order = $order->id;
        }
        if (!$order instanceof BundleEntity\ShoppingOrder) {
            $response = $this->getShoppingOrderOfMember($order,$member);
            if ($response['error']) {
                return $response;
            }
            $orderEntity = $response['result']['set'];
            unset($response);
        }else{
            $orderEntity = $order;
        }

        $date =new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
        $transactionData = !isset($params['transaction'])?array():$params['transaction'];
        /**
         * Calculate installment fee
         */
        $installmentFee = 0;
        if (isset($transactionData['response']) && $transactionData['gateway'] != 2) {
            $transactionResponse = json_decode($params['transaction']['response']);
            if (property_exists($transactionResponse, 'AMOUNT')) {
                $installmentFee = $transactionResponse->AMOUNT - $orderEntity->getTotalAmount();
                if (($transactionResponse->AMOUNT - $orderEntity->getTotalAmount() < 0)) {
                    $installmentFee = 0;
                }
            }
        }
        /** Update Order */
        $orderClass = new \stdClass();
        $orderClass->id = $order;
        $orderClass->order_number =isset($transactionData['id']) ? $transactionData['id'] : ($date->format('YmdHis').$order);
        $orderClass->status = isset($params['orderStatus']) ? $params['orderStatus'] : 2;
        $orderClass->flag = 'c';
        $orderClass->date_purchased = $date;
        $orderClass->installment_fee = $installmentFee;
        $orderClass->total_amount = $orderEntity->getTotalAmount() + $installmentFee;

        $response = $this->updateShoppingOrder($orderClass);
        $orderEntity = $response['result']['set'][0];
        unset($response);
        /** Completing cart*/
        $cart = new \stdClass();
        $cart->id = $orderEntity->getCart()->getId();
        $cart->date_ordered = $date;
        $this->updateShoppingCart($cart);

        /** Create transaction **/
        $transaction = new \stdClass();
        $transaction->transaction_id = isset($transactionData['id']) ? $transactionData['id'] : ($date->format('YmdHis').$order);
        $transaction->shopping_order = $orderClass->id;
        $transaction->gateway = isset($transactionData['gateway']) ? $transactionData['gateway'] : 1;
        $transaction->amount = $orderEntity->getTotalAmount();
        $transaction->status = isset($transactionData['status']) ? $transactionData['status'] : 'FAILED';
        $transaction->response = isset($transactionData['response']) ? $transactionData['response'] : '[]';
        $transaction->date_added = $date;
        $transaction->site = 1;
        $transaction->member = $member;


        $pgModel =  new PGBService\PaymentGatewayModel($this->kernel, $this->dbConnection, $this->orm);
        $response = $pgModel->insertPaymentTransaction($transaction);
        //$response = $this->insertPaymentTransaction($transaction);
        if ($response['error']) {
            return $response;
        }
        $response['result']['set'] =$response['result']['set'][0];
        return $response;

    }

    /**
     * @name    cancelUncompletedCartsOfMember()
     *
     * @since   1.1.6
     * @version 1.1.6
     *
     * @param   mixed   $member
     * @param   mixed   $sessionId
     *
     * @return  bool
     */
    public  function cancelUncompletedCartsOfMember($member,$sessionId){
        if ((!is_int($member) && !$member instanceof \stdClass && !$member instanceof MMBEntity\Member)) {
            return $this->createException('InvalidParameter', 'Member', 'err.invalid.parameter');
        }
        if ($member instanceof \stdClass) {
            $member = $member->id;
        }
        if ($member instanceof MMBEntity\Member) {
            $member = $member->getId;
        }

        /** Get session id with PHPSESSID */
        $logModel = $this->kernel->getContainer()->get('logbundle.model');
        $response = $logModel->getSession($sessionId,'session_id');
        if ($response['error']) {
            return $this->createException('InvalidParameter', 'Session', 'err.invalid.parameter');
        }
        $session = $response['result']['set']->getId();
        unset($response);

        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'or',
                    'condition' => array('column' => $this->entity['shopping_cart']['alias'] . '.member', 'comparison' => '=', 'value' => $member),
                ),
                array(
                    'glue' => 'or',
                    'condition' => array('column' => $this->entity['shopping_cart']['alias'] . '.session', 'comparison' => '=', 'value' => $session),
                ),
            )
        );
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_cart']['alias'] . '.date_ordered', 'comparison' => 'null', 'value' => ''),
                ),
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_cart']['alias'] . '.date_cancelled', 'comparison' => 'null', 'value' => $session),
                ),
            )
        );
        $carts = array();
        $response = $this->listShoppingCarts($filter);
        if (!$response['error']) {
            $date = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));;
            foreach ($response['result']['set'] as $cart) {
                $cart->setDateCancelled($date);
                $carts[] = $cart;
            }
            $response = $this->updateShoppingCarts($carts);
            if ($response['error']) {
                return $response;
            }
            $carts = $response['result']['set'];
        }
        unset($response);
        $this->response['code'] = 'success.all.carts.cancelled';
        $this->response['result']['set'] = $carts;
        $this->response['error'] = false;

        return $this->response;
    }


    /**
     * @name 			listShoppingOrderItemsOfOrder()
     *  				List shopping order items of given order from database based on a variety of conditions.
     *
     * @since			1.1.6
     * @version         1.1.6
     * @author          Said İmamoğlu
     *
     * @use             $this->createException()
     * @use             $this->listShoppingOrderItems()
     *
     * @param           array           $order
     * @param           array           $filter
     * @param           array           $sortorder
     * @param           array           $limit
     * @param           string          $query_str
     *
     * @return          array           $response
     */
    public function listShoppingOrderItemsOfOrder($order,$filter = null, $sortorder = null, $limit = null, $query_str = null) {
        if ($order instanceof BundleEntity\ShoppingOrder) {
            $order = $order->getId();
        }elseif($order instanceof \stdClass){
            $order = $order->id;
        }elseif(is_int($order)){
            $order = $order;
        }else{
            return $this->createException('InvalidParameter', 'Order', 'err.invalid.parameter');
        }
        $filter = array();
        $filter[] = array(
            'glue' => 'and',
            'condition' => array(
                array(
                    'glue' => 'and',
                    'condition' => array('column' => $this->entity['shopping_order_item']['alias'] . '.order', 'comparison' => '=', 'value' => $order),
                )
            )
        );
        return $this->listShoppingOrderItems($filter);
    }
}

/**
 * Change Log
 * **************************************
 * v1.1.6                      Said İmamoğlu
 * 01.07.2014
 * **************************************
 * U completeOrder()
 * A cancelUncompletedCartsOfMember()
 * A listShoppingOrderItemsOfOrder()
 * **************************************
 * v1.1.5                      Said İmamoğlu
 * 24.06.2014
 * **************************************
 * U completeOrder()
 * **************************************
 * v1.1.4                      Said İmamoğlu
 * 23.06.2014
 * **************************************
 * A updateShoppingCartItems()
 * U insertShoppingOrders()
 * U updateShoppingOrders()
 * U updateShoppingOrderItems()
 * U insertShoppingOrderItems()
 * U getIncompleteShoppingOrder()
 * **************************************
 * v1.1.3                      Said İmamoğlu
 * 06.06.2014
 * **************************************
 * A getShoppingOrderOfMember()
 * **************************************
 * v1.1.2                      Said İmamoğlu
 * 30.05.2014
 * **************************************
 * A listNoneOrderedShoppingCartsOfMember()
 * **************************************
 * v1.1.1                      Can Berkol
 * 26.05.2014
 * **************************************
 * A listNoneOrderedShoppingCartsOfMember()
 * A listPaymentTransactionsOfOrder()
 * U listShoppingOrdersOfMember
 *
 * **************************************
 * v1.1.0                   Said İmamoğlu
 * 21.05.2014
 * **************************************
 * U insertShoppingCarts()
 * U insertShoppingCartItems()
 *
 * **************************************
 * v1.0.9                   Said İmamoğlu
 * 14.05.2014
 * **************************************
 * A listRedeemedCoupons()
 * 
 * **************************************
 * v1.0.8                      Can Berkol
 * 13.05.2014
 * **************************************
 * U deleteCoupon()
 * U deleteCoupons()
 *
 * **************************************
 * v1.0.7                      Can Berkol
 * 12.05.2014
 * **************************************
 * U updateCoupon()
 * U updateCoupons()
 *
 * **************************************
 * v1.0.6                      Can Berkol
 * 11.05.2014
 * **************************************
 * A insertCouponLocalizations()
 * A insertCoupons()
 *
 * **************************************
 * v1.0.5                   Said İmamoğlu
 * 23.04.2014
 * **************************************
 * A listPurchasedOrders()
 * A listPurchasedOrdersBetween()
 * A listPurchasedOrdersOfMember()
 * A getTotalSalesVolumeOfMember()
 *
 * **************************************
 * v1.0.4                   Said İmamoğlu
 * 16.04.2014
 * **************************************
 * U updateShoppingOrders()
 * **************************************
 * v1.0.3                      Can Berkol
 * 16.11.2013
 * **************************************
 * A getShoppingOrderStatus()
 * A getShoppingOrderStatusLocalization()
 * A insertShoppingOrderStatus()
 * A insertShoppingOrderStatuses()
 * A listShoppingOrderStatuses()
 * A updateShoppingOrderStatus()
 * A updateShoppingOrderStatuses()
 *
 * **************************************
 * v1.0.2                      Can Berkol
 * 15.11.2013
 * **************************************
 * A deleteShoppingOrder()
 * A deleteShoppingOrders()
 * A deleteShoppingOrderStatus()
 * A deleteShoppingOrderStatuses()
 * A doesShoppingCartItemExist()
 * A doesShoppingOrderExist()
 * A getShoppingOrder()
 * A insertShoppingCart()
 * A insertShoppingCartItem()
 * A insertShoppingCartItems()
 * A insertShoppingCarts()
 * A insertShoppingOrder()
 * A insertShoppingOrderItem()
 * A insertShoppingOrderItems()
 * A insertShoppingOrders()
 * A listCancelledShoppingOrders()
 * A listCompletedShoppingOrders()
 * A listItemsOfShoppingCart()
 * A listOpenShoppingOrders()
 * A listOrderedShoppingCarts()
 * A listPaymentTransactionsOfMember()
 * A listPurchasedShoppingOrders()
 * A listReturnedShoppingOrders()
 * A listShoppingCartsOfMember()
 * A listShoppingOrders()
 * A listShoppingOrdersOfCart()
 * A listShoppingOrdersOfMember()
 * A listShoppingOrdersWithFlag()
 * A updatePaymentTransaction()
 * A updatePaymentTransactions()
 * A updateShoppingCart()
 * A updateShoppingCartItem()
 * A updateShoppingCartItems()
 * A updateShoppingCarts()
 * A updateShoppingOrder
 * A updateShoppingOrderItem()
 * A updateShoppingOrderItems()
 * A updateShoppingOrders
 *
 * **************************************
 * v1.0.1                      Can Berkol
 * 14.11.2013
 * **************************************
 * A deleteCoupon()
 * A deleteCoupons()
 * A deleteCouponsOfMember()
 * A deleteCouponsOfMemberGroup()
 * A deleteCouponsOfProduct()
 * A deleteCouponsOfSite()
 * A deleteCouponsWithType()
 * A deletePaymentTransaction()
 * A deletePaymentTransactions()
 * A deletePaymentTransactionsOfGateway()
 * A deletePaymentTransactionsOfMember()
 * A deletePaymentTransactionsOfOrder()
 * A deletePaymentTransactionsOfSite()
 * A deleteShoppingCart()
 * A deleteShoppingCartItem()
 * A deleteShoppingCartItems()
 * A deleteShoppingCarts()
 * A deleteShoppingOrderItem()
 * A deleteShoppingOrderItems()
 * A doesPaymentTransactionExist()
 * A doesShoppingCartExist()
 * A doesShoppingOrderItemExist()
 * A getPaymentTransaction()
 * A getShoppingCart()
 * A getShoppingCartItem()
 * A getShoppingOrderItem()
 * A insertPaymentTransaction()
 * A insertPaymentTransactions()
 * A listCancelledShoppingCarts()
 * A listPaymentTransactions()
 * A listShoppingCartItems()
 * A listShoppingCarts()
 * A listShoppingOrderItems()
 *
 * **************************************
 * v1.0.0                      Can Berkol
 * 18.10.2013
 * **************************************
 * Initial setup of class has been added.
 */