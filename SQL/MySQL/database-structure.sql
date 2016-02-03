/**
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        23.12.2015
 */
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for coupon
-- ----------------------------
DROP TABLE IF EXISTS `coupon`;
CREATE TABLE `coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `code` varchar(155) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Coupon code.',
  `type` char(1) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'a' COMMENT 'a:amount;p:percentage',
  `discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Discount amount / percentage.',
  `limit_redeem` int(10) unsigned DEFAULT NULL COMMENT 'Number of times this coupon can be redeemed.',
  `limit_order_total` double(10,2) DEFAULT NULL COMMENT 'For this coupon to be redeemed the order must be at least this much.',
  `limit_discount` decimal(10,2) unsigned DEFAULT NULL COMMENT 'If coupon type is percentage this limits the maximimum amount of the promotion.',
  `date_published` datetime NOT NULL COMMENT 'Date when the coupon is published.',
  `date_unpublished` datetime DEFAULT NULL COMMENT 'Date when the coupon is unpublished.',
  `type_usage` char(1) COLLATE utf8_turkish_ci NOT NULL COMMENT 's:single;m:multiple',
  `site` int(10) unsigned DEFAULT NULL COMMENT 'Site that coupon belongs to.',
  `total_discount_redeemed` decimal(10,0) DEFAULT NULL,
  `total_order_amount` decimal(10,0) DEFAULT NULL,
  `count_redeemed` int(5) DEFAULT NULL,
  `validity` text COLLATE utf8_turkish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for coupon_localization
-- ----------------------------
DROP TABLE IF EXISTS `coupon_localization`;
CREATE TABLE `coupon_localization` (
  `coupon` int(10) unsigned NOT NULL COMMENT 'Localized coupon.',
  `language` int(5) unsigned NOT NULL COMMENT 'Localization language.',
  `name` varchar(155) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Localized name of coupon.',
  `description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Localized description.',
  PRIMARY KEY (`coupon`,`language`),
  KEY `idxFCouponLocalizationLanguage` (`language`),
  CONSTRAINT `idxFCouponLocalizationLanguage` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFLocalizedCoupon` FOREIGN KEY (`coupon`) REFERENCES `coupon` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for payment_transaction
-- ----------------------------
DROP TABLE IF EXISTS `payment_transaction`;
CREATE TABLE `payment_transaction` (
  `id` int(10) unsigned NOT NULL COMMENT 'Ssytem giiven id.',
  `transaction_id` varchar(255) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Transaction id generated by the gateway.',
  `shopping_order` int(15) unsigned NOT NULL COMMENT 'Order that transaciton is generated for.',
  `gateway` int(10) unsigned DEFAULT NULL COMMENT 'Payment gateway.',
  `amount` decimal(7,2) unsigned NOT NULL COMMENT 'Amount of transaction.',
  `status` varchar(155) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Gateway status code.',
  `response` longtext COLLATE utf8_turkish_ci NOT NULL COMMENT 'Response returned from gatway.',
  `date_added` datetime NOT NULL COMMENT 'Date when the transaction is added.',
  `site` int(10) unsigned NOT NULL COMMENT 'ite that transaction belongs to.',
  `member` int(10) unsigned NOT NULL COMMENT 'Member who owns the transaction.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUPaymentTransactionId` (`id`),
  KEY `idxFMemberOfPaymentTransaction` (`member`),
  KEY `idxFSiteOfPaymentTransaction` (`site`),
  KEY `idxFOrderOfPaymentTransaction` (`shopping_order`),
  KEY `idxFGatewayOfPaymentTransaction` (`gateway`),
  CONSTRAINT `idxFGatewayOfPaymentTransaction` FOREIGN KEY (`gateway`) REFERENCES `payment_gateway` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFMemberOfPaymentTransaction` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFOrderOfPaymentTransaction` FOREIGN KEY (`shopping_order`) REFERENCES `shopping_order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFSiteOfPaymentTransaction` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for redeemed_coupon
-- ----------------------------
DROP TABLE IF EXISTS `redeemed_coupon`;
CREATE TABLE `redeemed_coupon` (
  `id` int(15) unsigned NOT NULL COMMENT 'System given id.',
  `coupon` int(10) unsigned NOT NULL COMMENT 'Coupon that is redeemed.',
  `member` int(10) unsigned NOT NULL COMMENT 'Member who redeemed the coupon.',
  `shopping_order` int(10) unsigned NOT NULL COMMENT 'Cart where coupon is used.',
  `date_redeemed` datetime NOT NULL COMMENT 'Date when the coupon is redeemed.',
  PRIMARY KEY (`id`),
  KEY `idxFMemberWhoRedeemedCoupon` (`member`),
  KEY `idxFShoppingOrderOfRedeemedCoupon` (`shopping_order`),
  KEY `idxFRedeemedCoupon` (`coupon`),
  CONSTRAINT `idxFMemberWhoRedeemedCoupon` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFRedeemedCoupon` FOREIGN KEY (`coupon`) REFERENCES `coupon` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFShoppingOrderOfRedeemedCoupon` FOREIGN KEY (`shopping_order`) REFERENCES `shopping_order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for shopping_order
-- ----------------------------
DROP TABLE IF EXISTS `shopping_order`;
CREATE TABLE `shopping_order` (
  `id` int(15) unsigned NOT NULL COMMENT 'System given id.',
  `order_number` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL COMMENT 'Date when the order is created.',
  `date_updated` datetime NOT NULL COMMENT 'Date when the order is last updated.',
  `date_purchased` datetime DEFAULT NULL COMMENT 'Date when the purchase has been completed.',
  `date_cancelled` datetime DEFAULT NULL COMMENT 'Date when the order is cancelled.',
  `date_returned` datetime DEFAULT NULL COMMENT 'Date when the order is returned by the customer.',
  `count_items` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of items in order.',
  `total_amount` decimal(7,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Total amount to be paid.',
  `billing_information` longtext COLLATE utf8_turkish_ci COMMENT 'Billing information',
  `shipping_information` longtext COLLATE utf8_turkish_ci COMMENT 'Shipping information.',
  `instructions` longtext COLLATE utf8_turkish_ci COMMENT 'Extra instructions.',
  `flag` char(1) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'o' COMMENT 'o:open;r:returned;c:completed',
  `status` char(1) COLLATE utf8_turkish_ci NOT NULL COMMENT 't:cart,p:purchased,c:cancelled,r:in preperation,s:shipped,r:returned,d:completed',
  `purchaser` int(10) unsigned NOT NULL COMMENT 'Member who made the purchase',
  `subtotal` decimal(7,2) DEFAULT NULL,
  `total_shipment` decimal(7,2) DEFAULT NULL,
  `total_tax` decimal(7,2) DEFAULT NULL,
  `total_discount` decimal(7,2) DEFAULT NULL,
  `installment_fee` decimal(7,2) DEFAULT '0.00' COMMENT 'Installment fee of payment gateway',
  `content` longtext COLLATE utf8_turkish_ci COMMENT 'Shopping cart / order content. Json or serialized data.',
  `transaction_info` longtext COLLATE utf8_turkish_ci COMMENT 'Recorded transaction information. Json or serialized string.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUShoppingOrderNumber` (`order_number`),
  KEY `idxNShoppingOrderDateCreated` (`date_created`),
  KEY `idxNShoppingOrderDateUpdated` (`date_updated`),
  KEY `idxNShoppingOrderDateCancelled` (`date_cancelled`),
  KEY `idxNShoppingOrderDateReturned` (`date_returned`),
  KEY `idxNShoppingOrderStatus` (`status`),
  KEY `idxFShoppingOrderPurchases` (`purchaser`),
  CONSTRAINT `idxFShoppingOrderPurchases` FOREIGN KEY (`purchaser`) REFERENCES `member` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for shopping_order_item
-- ----------------------------
DROP TABLE IF EXISTS `shopping_order_item`;
CREATE TABLE `shopping_order_item` (
  `id` int(20) unsigned NOT NULL COMMENT 'System given id.',
  `product` int(15) unsigned NOT NULL COMMENT 'Product oredered.',
  `shopping_order` int(15) unsigned NOT NULL COMMENT 'Order that owns the product.',
  `quantity` int(6) unsigned NOT NULL DEFAULT '0' COMMENT 'Quantity ordered.',
  `price` decimal(7,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Item price.',
  `subtotal` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Quantity * price',
  `date_added` datetime NOT NULL COMMENT 'Date when the item is added.',
  `date_returned` date DEFAULT NULL COMMENT 'Date when the item is returned.',
  `instructions` longtext COLLATE utf8_turkish_ci COMMENT 'Extra instrucitons.',
  `tax` decimal(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Tax percentage.',
  `discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Total discount to be applied.',
  `total` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '(Subtotal - Discount) * (1 + tax)',
  `tax_amount` decimal(7,2) DEFAULT NULL,
  `total_with_tax` decimal(7,2) DEFAULT NULL,
  `package_type` char(1) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'b:box;p:package',
  PRIMARY KEY (`id`),
  KEY `idxFProductOfShoppingOrderItem` (`product`),
  KEY `idxFOrderOfShoppingOrder` (`shopping_order`),
  CONSTRAINT `idxFOrderOfShoppingOrder` FOREIGN KEY (`shopping_order`) REFERENCES `shopping_order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFProductOfShoppingOrderItem` FOREIGN KEY (`product`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;