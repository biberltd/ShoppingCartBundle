<?php
/**
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        27.12.2015
 */
namespace BiberLtd\Bundle\ShoppingCartBundle\Services;

use BiberLtd\Bundle\CoreBundle\Responses\ModelResponse;
use BiberLtd\Bundle\CoreBundle\CoreModel;
use BiberLtd\Bundle\ShoppingCartBundle\Entity as BundleEntity;
use BiberLtd\Bundle\MemberManagementBundle\Entity as MMBEntity;
use BiberLtd\Bundle\MultiLanguageSupportBundle\Entity as MLSEntity;
use BiberLtd\Bundle\PaymentGatewayBundle\Entity as PGBEntity;
use BiberLtd\Bundle\ProductManagementBundle\Entity as PMBEntity;
use BiberLtd\Bundle\SiteManagementBundle\Entity as SMBEntity;
use BiberLtd\Bundle\LogBundle\Services as LBService;
use BiberLtd\Bundle\MemberManagementBundle\Services as MMBService;
use BiberLtd\Bundle\SiteManagementBundle\Services as SMMService;
use BiberLtd\Bundle\PaymentGatewayBundle\Services as PGBService;
use BiberLtd\Bundle\ProductManagementBundle\Services as PMBService;
use BiberLtd\Bundle\CoreBundle\Services as CoreServices;
use BiberLtd\Bundle\CoreBundle\Exceptions as CoreExceptions;

class ShoppingCartModel extends CoreModel {

	/**
	 * ShoppingCartModel constructor.
	 *
	 * @param object $kernel
	 * @param string $db_connection
	 * @param string $orm
	 */
	public function __construct($kernel, string $db_connection = 'default', string $orm = 'doctrine') {
		parent::__construct($kernel, $db_connection, $orm);

		/**
		 * Register entity names for easy reference.
		 */
		$this->entity = array(
			'c' => array('name' => 'ShoppingCartBundle:Coupon', 'alias' => 'c'),
			'cl' => array('name' => 'ShoppingCartBundle:CouponLocalization', 'alias' => 'cl'),
			'pt' => array('name' => 'ShoppingCartBundle:PaymentTransaction', 'alias' => 'pt'),
			'rc' => array('name' => 'ShoppingCartBundle:RedeemedCoupon', 'alias' => 'rc'),
			'so' => array('name' => 'ShoppingCartBundle:ShoppingOrder', 'alias' => 'so'),
			'soi' => array('name' => 'ShoppingCartBundle:ShoppingOrderItem', 'alias' => 'soi'),
		);
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		foreach ($this as $property => $value) {
			$this->$property = null;
		}
	}

	/**
	 * @param string $flag
	 * @param array|null $filter
	 * @return ModelResponse
	 */
	public function countOrdersWithFlag(string $flag, array $filter = null){
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.flag', 'comparison' => '=', 'value' => $flag),
				),
			)
		);
		$response = $this->listShoppingOrders($filter);
		if($response->error->exist){
			return $response;
		}
		$response->result->set = count($response->result->set);
		$response->result->count->set = 1;
		$response->result->count->total = 1;
		return $response;
	}
	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function countCompletedOrders(array $filter = null, array $sortOrder = null, array $limit = null){
		return $this->countOrdersWithFlag('c', $filter, $sortOrder, $limit);
	}

	/**
	 * @param mixed $member
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|mixed
	 */
	public function countOrdersOfMember($member, array $filter = null, array $sortOrder = null, array $limit = null){
		/**
		 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
		 */
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		/**
		 * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
		 */
		$member = $response->result->set;
		unset($response);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.purchaser', 'comparison' => '=', 'value' => $member->getId()),
				),
			)
		);
		$response = $this->listShoppingOrders($filter, $sortOrder, $limit);
		if($response->error->exist){
			return $response;
		}
		$response->result->set = count($response->result->set);
		$response->result->count->set = 1;
		$response->result->count->total = 1;
		return $response;
	}
	/**
	 * @param mixed $member
	 * @param string     $flag
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|mixed
	 */
	public function countOrdersOfMemberWithFlag($member, string $flag, array $filter = null, array $sortOrder = null, array $limit = null){
		/**
		 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
		 */
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		/**
		 * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
		 */
		$member = $response->result->set;
		unset($response);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.purchaser', 'comparison' => '=', 'value' => $member->getId()),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.flag', 'comparison' => '=', 'value' => $flag),
				),
			)
		);
		$response = $this->listShoppingOrders($filter, $sortOrder, $limit);
		if($response->error->exist){
			return $response;
		}
		$response->result->set = count($response->result->set);
		$response->result->count->set = 1;
		$response->result->count->total = 1;
		return $response;
	}
	/**
	 * @param mixed $member
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|mixed
	 */
	public function countCompletedOrdersOfMember($member, array $filter = null, array $sortOrder = null, array $limit = null){
		return $this->countOrdersOfMemberWithFlag($member, 'c', $filter, $sortOrder, $limit);
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function countNoneOrderedCarts(array $filter = null, array $sortOrder = null, array $limit = null){
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.status', 'comparison' => '=', 'value' => 't'),
				),
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.date_puschased', 'comparison' => 'isnull', 'value' => null),
				),
			)
		);
		$response = $this->listShoppingOrders($filter, $sortOrder, $limit);
		if($response->error->exist){
			return $response;
		}
		$response->result->set = count($response->result->set);
		$response->result->count->set = 1;
		$response->result->count->total = 1;
		return $response;
	}

	/**
	 * @param mixed $data
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deletePaymentTransaction($data) {
		return $this->deletePaymentTransactions(array($data));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deletePaymentTransactions(array $collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach ($collection as $entry) {
			if ($entry instanceof BundleEntity\PaymentTransaction) {
				$this->em->remove($entry);
				$countDeleted++;
			} else {
				$response = $this->getPaymentTransaction($entry);
				if (!$response->error->exist) {
					$this->em->remove($response->result->set);
					$countDeleted++;
				}
			}
		}
		if ($countDeleted < 0) {
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
		}
		$this->em->flush();
		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param $order
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteShoppingOrder($order) {
		return $this->deleteShoppingOrders(array($order));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteShoppingOrders(array $collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach ($collection as $entry) {
			if ($entry instanceof BundleEntity\ShoppingOrder) {
				$this->em->remove($entry);
				$countDeleted++;
			} else {
				$response = $this->getShoppingOrder($entry);
				if (!$response->error->exist) {
					$this->em->remove($response->result->set);
					$countDeleted++;
				}
			}
		}
		if ($countDeleted < 0) {
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
		}
		$this->em->flush();
		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $item
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteShoppingOrderItem($item) {
		return $this->deleteShoppingOrderItems(array($item));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteShoppingOrderItems(array $collection) {
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach ($collection as $entry) {
			if ($entry instanceof BundleEntity\ShoppingOrderItem) {
				$this->em->remove($entry);
				$countDeleted++;
			} else {
				$response = $this->getShoppingOrderItem($entry);
				if (!$response->error->exist) {
					$this->em->remove($response->result->set);
					$countDeleted++;
				}
			}
		}
		if ($countDeleted < 0) {
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
		}
		$this->em->flush();
		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $transaction
	 * @param bool $bypass
	 *
	 * @return bool
	 */
	public function doesPaymentTransationExist($transaction, bool $bypass = false)
	{
		$response = $this->getPaymentTransaction($transaction);
		$exist = true;
		if ($response->error->exist) {
			$exist = false;
			$response->result->set = false;
		}
		if ($bypass) {
			return $exist;
		}
		return $response;
	}

	/**
	 * @param mixed $order
	 * @param bool $bypass
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
	 */
	public function doesShoppingOrderExist($order, bool $bypass = false)
	{
		$response = $this->getShoppingOrder($order);
		$exist = true;
		if ($response->error->exist) {
			$exist = false;
			$response->result->set = false;
		}
		if ($bypass) {
			return $exist;
		}
		return $response;
	}

	/**
	 * @param mixed $item
	 * @param bool $bypass
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
	 */
	public function doesShoppingOrderItemExist($item, bool $bypass = false)
	{
		$response = $this->getShoppingOrderItem($item);
		$exist = true;
		if ($response->error->exist) {
			$exist = false;
			$response->result->set = false;
		}
		if ($bypass) {
			return $exist;
		}
		return $response;
	}

	/**
	 * @param mixed $transaction
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function  getPaymentTransaction($transaction)
	{
		$timeStamp = microtime(true);
		if ($transaction instanceof BundleEntity\PaymentTransaction) {
			return new ModelResponse($transaction, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
		}
		$result = null;
		switch ($transaction) {
			case is_numeric($transaction):
				$result = $this->em->getRepository($this->entity['pt']['name'])->findOneBy(array('id' => $transaction));
				break;
		}
		if (is_null($result)) {
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
		}
		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $order
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getShoppingOrder($order) {
		$timeStamp = microtime(true);
		if ($order instanceof BundleEntity\ShoppingOrder) {
			return new ModelResponse($order, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
		}
		$result = null;
		switch ($order) {
			case is_numeric($order):
				$result = $this->em->getRepository($this->entity['so']['name'])->findOneBy(array('id' => $order));
				break;
			case is_string($order):
				$result = $this->em->getRepository($this->entity['so']['name'])->findOneBy(array('order_number' => $order));
				break;
		}
		if (is_null($result)) {
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
		}
		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param $item
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getShoppingOrderItem($item) {
		$timeStamp = microtime(true);
		if ($item instanceof BundleEntity\ShoppingOrderItem) {
			return new ModelResponse($item, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
		}
		$result = null;
		switch ($item) {
			case is_numeric($item):
				$result = $this->em->getRepository($this->entity['soi']['name'])->findOneBy(array('id' => $item));
				break;
		}
		if (is_null($result)) {
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
		}
		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $transaction
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertPaymentTransaction($transaction) {
		return $this->insertPaymentTransactions(array($transaction));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertPaymentTransactions(array $collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = [];
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\PaymentTransaction) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else if (is_object($data)) {
				$entity = new BundleEntity\PaymentTransaction();
				if (!property_exists($data, 'date_added')) {
					$data->date_added = $now;
				}
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'site':
							/**
							 * @var \BiberLtd\Bundle\SiteManagementBundle\Services\SiteManagementModel $sModel
							 */
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $sModel);
							break;
						case 'member':
							/**
							 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
							 */
							$mModel = $this->kernel->getContainer()->get('membermanagement.model');
							$response = $mModel->getMember($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $mModel);
							break;
						case 'gateway':
							/**
							 * @var \BiberLtd\Bundle\PaymentGatewayBundle\Services\PaymentGatewayModel $pModel
							 */
							$pModel = $this->kernel->getContainer()->get('paymentgateway.model');
							$response = $pModel->getPaymentGateway($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $pModel);
							break;
						case 'order':
						case 'shopping_order':
							$response = $this->getShoppingOrder($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->setShoppingOrder($response->result->set);
							unset($response, $pModel);
							break;
						default:
							$entity->$set($value);
							break;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;

				$countInserts++;
			}
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $order
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertShoppingOrder($order) {
		return $this->insertShoppingOrders(array($order));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertShoppingOrders(array $collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = [];
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\ShoppingOrder) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else if (is_object($data)) {
				$entity = new BundleEntity\ShoppingOrder();
				if (!property_exists($data, 'date_added')) {
					$data->date_added = $now;
				}
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'purchaser':
							/**
							 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
							 */
							$mModel = $this->kernel->getContainer()->get('membermanagement.model');
							$response = $mModel->getMember($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $mModel);
							break;
						default:
							$entity->$set($value);
							break;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;

				$countInserts++;
			}
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param $item
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertShoppingOrderItem($item) {
		return $this->insertShoppingOrderItems(array($item));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertShoppingOrderItems(array $collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = [];
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\ShoppingOrderItem) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else if (is_object($data)) {
				$entity = new BundleEntity\ShoppingOrderItem();
				if (!property_exists($data, 'date_added')) {
					$data->date_added = $now;
				}
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'product':
							/**
							 * @var \BiberLtd\Bundle\ProductManagementBundle\Services\ProductManagementModel $pModel
							 */
							$pModel = $this->kernel->getContainer()->get('productmanagement.model');
							$response = $pModel->getProduct($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $pModel);
							break;
						case 'shopping_order':
							$response = $this->getShoppingOrder($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response);
							break;
						default:
							$entity->$set($value);
							break;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;

				$countInserts++;
			}
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listCancelledShoppingOrders(array $filter = null, array $sortOrder = null, array $limit = null) {
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['shopping_order']['alias'] . '.date_cancelled', 'comparison' => 'notnull', 'value' => null),
				)
			)
		);
		return $this->listShoppingOrders($filter, $sortOrder, $limit);
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortorder
	 * @param array|null $limit
	 *
	 * @return array
	 */
	public function listCompletedShoppingOrders(array $filter = null, array $sortorder = null, array $limit = null) {
		return $this->listShoppingOrdersWithFlag('c', $filter, $sortorder, $limit);
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listOpenShoppingOrders(array $filter = null, array $sortOrder = null, array $limit = null) {
		return $this->listShoppingOrdersWithFlag('o', $filter, $sortOrder, $limit);
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listPaymentTransactions(array $filter = null, array $sortOrder = null, array $limit = null) {
		$timeStamp = microtime(true);
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT ' . $this->entity['so']['alias']
			. ' FROM ' . $this->entity['so']['name'] . ' ' . $this->entity['so']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'id':
					case 'transaction_id':
					case 'amount':
					case 'status':
						$column = $this->entity['pt']['alias'] . '.' . $column;
						break;
				}
				$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY ' . $oStr . ' ';
		}

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr . $gStr . $oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$totalRows = count($result);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}
		return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $member
	 * @param array|null $filter
	 * @param array|null $sortorder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|mixed
	 */
	public function listPaymentTransactionsOfMember($member, array $filter = null, array $sortorder = null, array $limit = null) {
		/**
		 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
		 */
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		$member = $response->result->set;

		$column = $this->entity['pt']['alias'] . '.member';
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
	}

	/**
	 * @param mixed $order
	 * @param array|null $filter
	 * @param array|null $sortorder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|mixed
	 */
	public function listPaymentTransactionsOfOrder($order, array $filter = null, array $sortorder = null, array $limit = null) {
		$response = $this->getShoppingOrder($order);
		if($response->error->exist){
			return $response;
		}
		$order = $response->result->set;

		$column = $this->entity['pt']['alias'] . '.shopping_order';
		$condition = array('column' => $column, 'comparison' => '=', 'value' => $order->getId());
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

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return array|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listReturnedShoppingOrders(array $filter = null, array $sortOrder = null, array $limit = null) {
		$column = $this->entity['so']['alias'] . '.date_returned';
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
		return $this->listShoppingOrders($filter, $sortOrder, $limit);
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortorder
	 * @param array|null $limit
	 *
	 * @return array|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listPurchasedShoppingOrders(array $filter = null, array $sortorder = null, array $limit = null) {
		$this->resetResponse();
		/**
		 * Prepare $filter
		 */
		$column = $this->entity['so']['alias'] . '.date_purchased';
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
		return $this->listShoppingOrders($filter, $sortorder, $limit);
	}

	/**
	 * @param mixed $member
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return array|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|mixed
	 */
	public function listShoppingCartsOfMember($member, array $filter = null, array $sortOrder = null, array $limit = null) {
		/**
		 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
		 */
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		/**
		 * @var \BiberLtd\Bundle\MemberManagementBundle\Entity\Member $member
		 */
		$member = $response->result->set;
		unset($response);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' =>  array('column' => $this->entity['so']['alias'] . '.status', 'comparison' => '=', 'value' => 't'),
				),
				array(
					'glue' => 'and',
					'condition' =>  array('column' => $this->entity['so']['alias'] . '.member', 'comparison' => '=', 'value' => $member->getId()),
				)
			)
		);
		return $this->listShoppingOrders($filter, $sortOrder, $limit);
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listShoppingOrderItems(array $filter = null, array $sortOrder = null, array $limit = null) {
		$timeStamp = microtime(true);
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT ' . $this->entity['so']['alias']
			. ' FROM ' . $this->entity['so']['name'] . ' ' . $this->entity['so']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
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
						$column = $this->entity['soi']['alias'] . '.' . $column;
						break;
				}
				$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY ' . $oStr . ' ';
		}

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr . $gStr . $oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$totalRows = count($result);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}
		return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listShoppingOrders(array $filter = null, array $sortOrder = null, array $limit = null) {
		$timeStamp = microtime(true);
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT ' . $this->entity['so']['alias']
			. ' FROM ' . $this->entity['so']['name'] . ' ' . $this->entity['so']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'id':
					case 'order_numder':
					case 'date_added':
					case 'date_created':
					case 'date_updated':
					case 'date_purchased':
					case 'date_cancelled':
					case 'date_returned':
					case 'count_items':
					case 'total_amount':
					case 'flag':
					case 'status':
					case 'subtotal':
					case 'total_shipment':
					case 'total_tax':
					case 'total_discount':
					case 'installment_fee':
						$column = $this->entity['so']['alias'] . '.' . $column;
						break;
				}
				$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY ' . $oStr . ' ';
		}

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr . $gStr . $oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$totalRows = count($result);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}
		return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $member
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return array|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|mixed
	 */
	public function listShoppingOrdersOfMember($member, array $filter = null, array $sortOrder = null, array $limit = null) {
		/**
		 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
		 */
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		$member = $response->result->set;
		unset($response);
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' =>  array('column' => $this->entity['so']['alias'] . '.status', 'comparison' => '!=', 'value' => 't'),
				),
				array(
					'glue' => 'and',
					'condition' =>  array('column' => $this->entity['so']['alias'] . '.purchaser', 'comparison' => '=', 'value' => $member->getId()),
				)
			)
		);
		return $this->listShoppingOrders($filter, $sortOrder, $limit);
	}

	/**
	 * @param string     $flag
	 * @param array|null $filter
	 * @param array|null $sortorder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listShoppingOrdersWithFlag(string $flag, array $filter = null, array $sortorder = null, array $limit = null) {
		$column = $this->entity['so']['alias'] . '.flag';
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
		return $this->listShoppingOrders($filter, $sortorder, $limit);
	}

	/**
	 * @param mixed $transaction
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updatePaymentTransaction($transaction) {
		return $this->updatePaymentTransactions(array($transaction));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updatePaymentTransactions(array $collection)
	{
		$timeStamp = microtime(true);
		$countUpdates = 0;
		$updatedItems = [];
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\PaymentTransaction) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			} else if (is_object($data)) {
				if (!property_exists($data, 'id') || !is_numeric($data->id)) {
					return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
				}
				if (property_exists($data, 'date_added')) {
					unset($data->date_added);
				}
				$response = $this->getPaymentTransaction($data->id);
				if ($response->error->exist) {
					return $this->createException('EntityDoesNotExist', 'Brand with id ' . $data->id, 'err.invalid.entity');
				}
				$oldEntity = $response->result->set;
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'id':
							break;
						case 'site':
							/**
							 * @var \BiberLtd\Bundle\SiteManagementBundle\Services\SiteManagementModel $sModel
							 */
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $sModel);
							break;
						case 'member':
							/**
							 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
							 */
							$mModel = $this->kernel->getContainer()->get('membermanagement.model');
							$response = $mModel->getMember($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $mModel);
							break;
						case 'gateway':
							/**
							 * @var \BiberLtd\Bundle\PaymentGatewayBundle\Services\PaymentGatewayModel $pModel
							 */
							$pModel = $this->kernel->getContainer()->get('membermanagement.model');
							$response = $pModel->getPaymentGateway($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $pModel);
							break;
						case 'order':
						case 'shopping_order':
							$response = $this->getShoppingOrder($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->setShoppingOrder($response->result->set);
							unset($response, $pModel);
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
			}
		}
		if ($countUpdates > 0) {
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
	}

	/**
	 * @param $order
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateShoppingOrder($order) {
		return $this->updateShoppingOrders(array($order));
	}

	/**
	 * @param $item
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateShoppingOrderItem($item) {
		return $this->updateShoppingOrderItems(array($item));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateShoppingOrderItems(array $collection)
	{
		$timeStamp = microtime(true);
		$countUpdates = 0;
		$updatedItems = [];
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\PaymentTransaction) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			} else if (is_object($data)) {
				if (!property_exists($data, 'id') || !is_numeric($data->id)) {
					return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
				}
				if (property_exists($data, 'date_added')) {
					unset($data->date_added);
				}
				$response = $this->getPaymentTransaction($data->id);
				if ($response->error->exist) {
					return $this->createException('EntityDoesNotExist', 'Brand with id ' . $data->id, 'err.invalid.entity');
				}
				$oldEntity = $response->result->set;
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'id':
							break;
						case 'product':
							/**
							 * @var \BiberLtd\Bundle\ProductManagementBundle\Services\ProductManagementModel $pModel
							 */
							$pModel = $this->kernel->getContainer()->get('productmanagement.model');
							$response = $pModel->getProduct($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $pModel);
							break;
						case 'shopping_order':
							$response = $this->getShoppingOrder($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response);
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
			}
		}
		if ($countUpdates > 0) {
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateShoppingOrders(array $collection)
	{
		$timeStamp = microtime(true);
		$countUpdates = 0;
		$updatedItems = [];
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\ShoppingOrder) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			} else if (is_object($data)) {
				if (!property_exists($data, 'id') || !is_numeric($data->id)) {
					return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
				}
				if (property_exists($data, 'date_added')) {
					unset($data->date_added);
				}
				$response = $this->getShoppingOrder($data->id);
				if ($response->error->exist) {
					return $this->createException('EntityDoesNotExist', 'Brand with id ' . $data->id, 'err.invalid.entity');
				}
				$oldEntity = $response->result->set;
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'id':
							break;
						case 'purchaser':
							/**
							 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
							 */
							$mModel = $this->kernel->getContainer()->get('membermanagement.model');
							$response = $mModel->getMember($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $mModel);
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
			}
		}
		if ($countUpdates > 0) {
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
	}

	/**
	 * @param $coupon
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteCoupon($coupon) {
		return $this->deleteCoupons(array($coupon));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function deleteCoupons(array $collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countDeleted = 0;
		foreach ($collection as $entry) {
			if ($entry instanceof BundleEntity\Coupon) {
				$this->em->remove($entry);
				$countDeleted++;
			} else {
				$response = $this->getCoupon($entry);
				if (!$response->error->exist) {
					$this->em->remove($response->result->set);
					$countDeleted++;
				}
			}
		}
		if ($countDeleted < 0) {
			return new ModelResponse(null, 0, 0, null, true, 'E:E:001', 'Unable to delete all or some of the selected entries.', $timeStamp, microtime(true));
		}
		$this->em->flush();
		return new ModelResponse(null, 0, 0, null, false, 'S:D:001', 'Selected entries have been successfully removed from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listCoupons(array  $filter = null, array $sortOrder = null,array  $limit = null)
	{
		$timeStamp = microtime(true);
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT ' . $this->entity['c']['alias'] . ', ' . $this->entity['cl']['alias']
			. ' FROM ' . $this->entity['cl']['name'] . ' ' . $this->entity['cl']['alias']
			. ' JOIN ' . $this->entity['cl']['alias'] . '.coupon ' . $this->entity['c']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					case 'id':
					case 'code':
					case 'type':
					case 'discount':
					case 'date_published':
					case 'date_unpublished':
					case 'total_discount_redeemed':
					case 'type_usage':
					case 'limit_discount':
					case 'limit_discount':
					case 'limit_order_total':
					case 'count_redeemed':
					case 'site':
						$column = $this->entity['c']['alias'] . '.' . $column;
						break;
					case 'name':
					case 'description':
						$column = $this->entity['cl']['alias'] . '.' . $column;
						break;
				}
				$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY ' . $oStr . ' ';
		}

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr . $gStr . $oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$entities = [];
		foreach ($result as $entry) {
			$id = $entry->getCoupon()->getId();
			if (!isset($unique[$id])) {
				$unique[$id] = '';
				$entities[] = $entry->getCoupon();
			}
		}
		$totalRows = count($entities);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}
		return new ModelResponse($entities, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $coupon
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getCoupon($coupon) {
		$timeStamp = microtime(true);
		if ($coupon instanceof BundleEntity\Coupon) {
			return new ModelResponse($coupon, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
		}
		$result = null;
		switch ($coupon) {
			case is_numeric($coupon):
				$result = $this->em->getRepository($this->entity['c']['name'])->findOneBy(array('id' => $coupon));
				break;
			case is_string($coupon):
				$result = $this->em->getRepository($this->entity['c']['name'])->findOneBy(array('code' => $coupon));
				break;
		}
		if (is_null($result)) {
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
		}
		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $coupon
	 * @param bool $bypass
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse|bool
	 */
	public function doesCouponExist($coupon, bool $bypass = null)
	{
		$bypass = $bypass ?? false;
		$response = $this->getCoupon($coupon);
		$exist = true;
		if ($response->error->exist) {
			$exist = false;
			$response->result->set = false;
		}
		if ($bypass) {
			return $exist;
		}
		return $response;
	}

	/**
	 * @param $coupon
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertCoupon($coupon) {
		return $this->insertCoupons(array($coupon));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertCoupons(array $collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$countLocalizations = 0;
		$insertedItems = [];
		$localizations = [];
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\Coupon) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else if (is_object($data)) {
				unset($data->id);
				$entity = new BundleEntity\Coupon();
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
							/**
							 * @var \BiberLtd\Bundle\SiteManagementBundle\Services\SiteManagementModel $sModel
							 */
							$sModel = $this->kernel->getContainer()->get('sitemanagement.model');
							$response = $sModel->getSite($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
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
		}
		/** Now handle localizations */
		if ($countInserts > 0 && $countLocalizations > 0) {
			$this->em->flush();
			$this->insertCouponLocalizations($localizations);
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertCouponLocalizations(array $collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = [];
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\CouponLocalization) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else {
				$coupon = $data['entity'];
				foreach ($data['localizations'] as $locale => $translation) {
					$entity = new BundleEntity\CouponLocalization();
					/**
					 * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Services\MultiLanguageSupportModel $lModel
					 */
					$lModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
					$response = $lModel->getLanguage($locale);
					if ($response->error->exist) {
						return $response;
					}
					$entity->setLanguage($response->result->set);
					unset($response);
					$entity->setCoupon($coupon);
					foreach ($translation as $column => $value) {
						$set = 'set' . $this->translateColumnName($column);
						switch ($column) {
							default:
								$entity->$set($value);
								break;
						}
					}
					$this->em->persist($entity);
					$insertedItems[] = $entity;
					$countInserts++;
				}
			}
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}

	/**
	 * @param $coupon
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateCoupon($coupon) {
		return $this->updateCoupons(array($coupon));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateCoupons(array $collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countUpdates = 0;
		$updatedItems = [];
		$localizations = [];
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\Coupon) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			} else if (is_object($data)) {
				if (!property_exists($data, 'id') || !is_numeric($data->id)) {
					return $this->createException('InvalidParameterException', 'Parameter must be an object with the "id" property and id property ​must have an integer value.', 'E:S:003');
				}
				if (!property_exists($data, 'site')) {
					$data->site = 1;
				}
				$response = $this->getCoupon($data->id);
				if ($response->error->exist) {
					return $this->createException('EntityDoesNotExist', 'Product with id / url_key / sku  ' . $data->id . ' does not exist in database.', 'E:D:002');
				}
				$oldEntity = $response->result->set;
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'local':
							foreach ($value as $langCode => $translation) {
								$localization = $oldEntity->getLocalization($langCode, true);
								$newLocalization = false;
								if (!$localization) {
									$newLocalization = true;
									$localization = new BundleEntity\Coupon();
									/**
									 * @var \BiberLtd\Bundle\MultiLanguageSupportBundle\Services\MultiLanguageSupportModel $mlsModel
									 */
									$mlsModel = $this->kernel->getContainer()->get('multilanguagesupport.model');
									$response = $mlsModel->getLanguage($langCode);
									$localization->setLanguage($response->result->set);
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
							$response = $sModel->getSite($value);
							if ($response->error->exist) {
								return $response;
							}
							$oldEntity->$set($response->result->set);
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
			}
		}
		if ($countUpdates > 0) {
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
	}

	/**
	 * @param bool       $returned
	 * @param bool       $cancelled
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listPurchasedOrders(bool $returned = null, bool $cancelled = null, array $sortOrder = null, array $limit = null) {
		$filter = [];
		$returned = $returned ?? null;
		$cancelled = $cancelled ?? null;
		if ($returned) {
			$column = $this->entity['so']['alias'] . '.date_returned';
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
			$column = $this->entity['so']['alias'] . '.date_cancelled';
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
		$column = $this->entity['so']['alias'] . '.date_purchased';
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
		return $this->listShoppingOrders($filter, $sortOrder, $limit);
	}

	/**
	 * @param \DateTime  $dateStart
	 * @param \DateTime  $dateEnd
	 * @param bool       $returned
	 * @param bool       $cancelled
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listPurchasedOrdersBetween(\DateTime $dateStart, \DateTime $dateEnd, bool $returned = null, bool $cancelled = null, array $sortOrder = null, array $limit = null) {
		$filter = [];
		$returned = $returned ?? false;
		$cancelled = $cancelled ?? false;
		if ($returned) {
			$column = $this->entity['so']['alias'] . '.date_returned';
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
			$column = $this->entity['so']['alias'] . '.date_cancelled';
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
		$column = $this->entity['so']['alias'] . '.date_purchased';
		$condition = array('column' => $column, 'comparison' => 'between', 'value' => array($dateStart, $dateEnd));
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => $condition,
				)
			)
		);
		return $this->listShoppingOrders($filter, $sortOrder, $limit);

	}

	/**
	 * @param mixed $member
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listPurchasedOrdersOfMember($member, array $filter = null, array $sortOrder = null, array $limit = null) {
		/**
		 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
		 */
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		$member = $response->result->set;
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
		return $this->listShoppingOrders($filter, $sortOrder, $limit);

	}

	/**
	 * @param mixed $member
	 * @param \DateTime  $dateStart
	 * @param \DateTime  $dateEnd
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return array|\BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function getTotalSalesVolumeOfMember($member, \DateTime $dateStart = null, \DateTime $dateEnd = null, array $sortOrder = null, array $limit = null) {
		/**
		 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
		 */
		$mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		$member = $response->result->set;
		$filter = [];
		$column = $this->entity['so']['alias'] . '.purchaser';
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
		if ($dateEnd != null && $dateStart != null) {
			$column = $this->entity['so']['alias'] . '.date_purchased';
			$condition = array('column' => $column, 'comparison' => 'between', 'value' => array($dateStart, $dateEnd));
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
		$response = $this->listShoppingOrders($filter, $sortOrder, $limit);
		if ($response['error']) {
			return $response;
		}
		(float) $total = 0;
		foreach ($response['result']['set'] as $order) {
			$total += $order->getTotalAmount();
		}

		$response->result->set = $total;
		$response->result->count->set = 1;
		$response->result->count->total = 1;
		return $response;
	}

	/**
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listRedeemedCoupons(array $filter = null, array $sortOrder = null, array $limit = null) {
		$timeStamp = microtime(true);
		if (!is_array($sortOrder) && !is_null($sortOrder)) {
			return $this->createException('InvalidSortOrderException', '$sortOrder must be an array with key => value pairs where value can only be "asc" or "desc".', 'E:S:002');
		}
		$oStr = $wStr = $gStr = $fStr = '';

		$qStr = 'SELECT ' . $this->entity['rc']['alias']
			. ' FROM ' . $this->entity['rc']['name'] . ' ' . $this->entity['rc']['alias'];

		if (!is_null($sortOrder)) {
			foreach ($sortOrder as $column => $direction) {
				switch ($column) {
					default:
						$column = $this->entity['rc']['alias'] . '.' . $column;
						break;
				}
				$oStr .= ' ' . $column . ' ' . strtoupper($direction) . ', ';
			}
			$oStr = rtrim($oStr, ', ');
			$oStr = ' ORDER BY ' . $oStr . ' ';
		}

		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr . $gStr . $oStr;
		$q = $this->em->createQuery($qStr);
		$q = $this->addLimit($q, $limit);

		$result = $q->getResult();

		$totalRows = count($result);
		if ($totalRows < 1) {
			return new ModelResponse(null, 0, 0, null, true, 'E:D:002', 'No entries found in database that matches to your criterion.', $timeStamp, microtime(true));
		}
		return new ModelResponse($result, $totalRows, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $order
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listItemsOfShoppingOrder($order, array $filter = null, array $sortOrder = null, array $limit = null) {
		$response = $this->getShoppingOrder($order);
		if($response->error->exist){
			return $response;
		}
		$order = $response->result->set;
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['soi']['alias'] . '.order', 'comparison' => '=', 'value' => $order->getId()),
				)
			)
		);
		return $this->listShoppingOrderItems($filter, $sortOrder, $limit);
	}
	/**
	 * @param mixed $coupon
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertRedeemedCoupon($coupon) {
		return $this->insertRedeemedCoupons(array($coupon));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function insertRedeemedCoupons(array $collection)
	{
		$timeStamp = microtime(true);
		if (!is_array($collection)) {
			return $this->createException('InvalidParameterValueException', 'Invalid parameter value. Parameter must be an array collection', 'E:S:001');
		}
		$countInserts = 0;
		$insertedItems = [];
		$now = new \DateTime('now', new \DateTimeZone($this->kernel->getContainer()->getParameter('app_timezone')));
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\RedeemedCoupon) {
				$entity = $data;
				$this->em->persist($entity);
				$insertedItems[] = $entity;
				$countInserts++;
			} else if (is_object($data)) {
				$entity = new BundleEntity\RedeemedCoupon();
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'coupon':
							$response = $this->getCoupon($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $sModel);
							break;
						case 'member':
							/**
							 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
							 */
							$mModel = $this->kernel->getContainer()->get('membermanagement.model');
							$response = $mModel->getMember($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $mModel);
							break;
						case 'shopping_order':
							$response = $this->getShoppingOrder($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->setShoppingOrder($response->result->set);
							unset($response, $pModel);
							break;
						default:
							$entity->$set($value);
							break;
					}
				}
				$this->em->persist($entity);
				$insertedItems[] = $entity;

				$countInserts++;
			}
		}
		if ($countInserts > 0) {
			$this->em->flush();
			return new ModelResponse($insertedItems, $countInserts, 0, null, false, 'S:D:003', 'Selected entries have been successfully inserted into database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:003', 'One or more entities cannot be inserted into database.', $timeStamp, microtime(true));
	}
	/**
	 * @param mixed $coupon
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateRedeemedCoupon($coupon) {
		return $this->updateRedeemedCoupons(array($coupon));
	}

	/**
	 * @param array $collection
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function updateRedeemedCoupons(array $collection)
	{
		$timeStamp = microtime(true);
		$countUpdates = 0;
		$updatedItems = [];
		foreach ($collection as $data) {
			if ($data instanceof BundleEntity\RedeemedCoupon) {
				$entity = $data;
				$this->em->persist($entity);
				$updatedItems[] = $entity;
				$countUpdates++;
			} else if (is_object($data)) {
				if (!property_exists($data, 'id') || !is_numeric($data->id)) {
					return $this->createException('InvalidParameter', 'Each data must contain a valid identifier id, integer', 'err.invalid.parameter.collection');
				}
				$response = $this->getRedeemedCoupon($data->id);
				if ($response->error->exist) {
					return $this->createException('EntityDoesNotExist', 'Brand with id ' . $data->id, 'err.invalid.entity');
				}
				$oldEntity = $response->result->set;
				foreach ($data as $column => $value) {
					$set = 'set' . $this->translateColumnName($column);
					switch ($column) {
						case 'id':
							break;
						case 'coupon':
							$response = $this->getCoupon($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $sModel);
							break;
						case 'member':
							/**
							 * @var \BiberLtd\Bundle\MemberManagementBundle\Services\MemberManagementModel $mModel
							 */
							$mModel = $this->kernel->getContainer()->get('membermanagement.model');
							$response = $mModel->getMember($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->$set($response->result->set);
							unset($response, $mModel);
							break;
						case 'order':
						case 'shopping_order':
							$response = $this->getShoppingOrder($value);
							if ($response->error->exist) {
								return $response;
							}
							$entity->setShoppingOrder($response->result->set);
							unset($response, $pModel);
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
			}
		}
		if ($countUpdates > 0) {
			$this->em->flush();
			return new ModelResponse($updatedItems, $countUpdates, 0, null, false, 'S:D:004', 'Selected entries have been successfully updated within database.', $timeStamp, microtime(true));
		}
		return new ModelResponse(null, 0, 0, null, true, 'E:D:004', 'One or more entities cannot be updated within database.', $timeStamp, microtime(true));
	}
	/**
	 * @param mixed $coupon
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function  getRedeemedCoupon($coupon)
	{
		$timeStamp = microtime(true);
		if ($coupon instanceof BundleEntity\RedeemedCoupon) {
			return new ModelResponse($coupon, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
		}
		$result = null;
		switch ($coupon) {
			case is_numeric($coupon):
				$result = $this->em->getRepository($this->entity['rc']['name'])->findOneBy(array('id' => $coupon));
				break;
		}
		if (is_null($result)) {
			return new ModelResponse($result, 0, 0, null, true, 'E:D:002', 'Unable to find request entry in database.', $timeStamp, microtime(true));
		}
		return new ModelResponse($result, 1, 0, null, false, 'S:D:002', 'Entries successfully fetched from database.', $timeStamp, microtime(true));
	}

	/**
	 * @param mixed $member
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 *
	 * @return \BiberLtd\Bundle\CoreBundle\Responses\ModelResponse
	 */
	public function listRedeemedCouponsOfMember($member, array $sortOrder = null, array $limit = null) {
		$timeStamp = microtime(true);
		$mModel = $mModel = $this->kernel->getContainer()->get('membermanagement.model');
		$response = $mModel->getMember($member);
		if($response->error->exist){
			return $response;
		}
		$member = $response->result->set;
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['rc']['alias'] . '.member', 'comparison' => '=', 'value' => $member->getId()),
				)
			)
		);
		return $this->listRedeemedCoupons($filter, $sortOrder, $limit);
	}

	/**
	 * @param \DateTime $startDate
	 * @param \DateTime $endDate
	 * @param bool|null $inclusive
	 * @param array|null $filter
	 * @return ModelResponse
	 */
	public function getSumOfCompletedSalesBetween(\DateTime $startDate, \DateTime $endDate, bool $inclusive = null, array $filter =  null){
		$timeStamp = microtime(true);
		$inclusive = $inclusive ?? true;
		$statuses = ['d', 'p'];
		$gt = '>=';
		$lt = '<=';

		if(!$inclusive){
			$gt = '>';
			$lt = '<';
		}

		$filter[] = [
			'glue' => 'and',
			'condition' => [
				[
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.date_returned', 'comparison' => 'isnull', 'value' => null),
				],
				[
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.date_purchased', 'comparison' => $gt, 'value' => $startDate->format('Y-m-d H:i:s')),
				],
				[
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.date_purchased', 'comparison' => $lt, 'value' => $endDate->format('Y-m-d H:i:s')),
				],
			]
		];

		$qStr = 'SELECT SUM('.$this->entity['so']['alias'].'.total_amount)'
			.' FROM '.$this->entity['so']['name'].' '.$this->entity['so']['alias'];

		$wStr = '';
		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr;
		$q = $this->em->createQuery($qStr);

		$result = $q->getSingleScalarResult() ?? 0;

		return new ModelResponse($result, 1, 0, null, false, 'S:D:003', 'Calculation sucessfuly completed.', $timeStamp, microtime(true));
	}

	/**
	 * @param \DateTime $startDate
	 * @param \DateTime $endDate
	 * @param array $statuses
	 * @param bool|null $inclusive
	 * @param array|null $filter
	 * @return ModelResponse
	 */
	public function getCountOfOrdersWithStatusBetween(\DateTime $startDate, \DateTime $endDate, array $statuses = ['d', 'p'], bool $inclusive = null, array $filter =  null){
		$timeStamp = microtime(true);
		$inclusive = $inclusive ?? true;

		$gt = '>=';
		$lt = '<=';

		if(!$inclusive){
			$gt = '>';
			$lt = '<';
		}

		$filter[] = [
			'glue' => 'and',
			'condition' => [
				[
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.status', 'comparison' => 'in', 'value' => $statuses),
				],
				[
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.date_returned', 'comparison' => 'isnull', 'value' => null),
				],
				[
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.date_purchased', 'comparison' => $gt, 'value' => $startDate->format('Y-m-d H:i:s')),
				],
				[
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] .'.date_purchased', 'comparison' => $lt, 'value' => $endDate->format('Y-m-d H:i:s')),
				],
			]
		];

		$qStr = 'SELECT COUNT('.$this->entity['so']['alias'].'.id)'
			.' FROM '.$this->entity['so']['name'].' '.$this->entity['so']['alias'];

		$wStr = '';
		if (!is_null($filter)) {
			$fStr = $this->prepareWhere($filter);
			$wStr .= ' WHERE ' . $fStr;
		}

		$qStr .= $wStr;
		$q = $this->em->createQuery($qStr);

		$result = $q->getSingleScalarResult() ?? 0;

		return new ModelResponse($result, 1, 0, null, false, 'S:D:003', 'Calculation sucessfuly completed.', $timeStamp, microtime(true));

	}

	/**
	 * @param $status
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 * @return ModelResponse
	 */
	public function listOrdersOfStatus($status, array $filter = null, array $sortOrder = null, array $limit = null){
		return $this->listOrdersOfStatutes((array)$status, $filter, $filter, $sortOrder, $limit);
	}

	/**
	 * @param array $statuses
	 * @param array|null $filter
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 * @return ModelResponse
	 */
	public function listOrdersOfStatutes(array $statuses, array $filter = null, array $sortOrder = null, array $limit = null){
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' => array('column' => $this->entity['so']['alias'] . '.status', 'comparison' => 'in', 'value' => $statuses),
				)
			)
		);
		return $this->listShoppingOrders($filter, $sortOrder, $limit);
	}

	/**
	 * @param array $ids
	 * @param array|null $sortOrder
	 * @param array|null $limit
	 * @return ModelResponse
	 */
	public function listShoppingOrdersWithIds(array $ids, array $sortOrder = null, array $limit = null){
		$filter[] = array(
			'glue' => 'and',
			'condition' => array(
				array(
					'glue' => 'and',
					'condition' =>  array('column' => $this->entity['so']['alias'] . '.id', 'comparison' => 'in', 'value' => $ids),
				)
			)
		);
		return $this->listShoppingOrders($filter, $sortOrder, $limit);

	}
}