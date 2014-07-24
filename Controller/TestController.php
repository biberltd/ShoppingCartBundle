<?php

/**
 * TestController
 *
 * This controller is used to install default / test values to the system.
 * The controller can only be accessed from allowed IP address.
 *
 * @package		ShoppingCartBundle
 * @subpackage	Controller
 * @name	    TestController
 *
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.0
 *
 */

namespace BiberLtd\Core\Bundles\ShoppingCartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception,
    Symfony\Component\HttpFoundation\Response,
    BiberLtd\Core\CoreController,
    BiberLtd\Core\Bundles\ShoppingCartBundle\Entity as BundleEntity;

class TestController extends CoreController {

    /**
     * @author Said İmamoğlu
     * 
     * 
     */
    public function testAction() {
        //$SCB= SCBService\ShoppingCartModel();
        $SCB = $this->get('shoppingcart.model');
        $response = $SCB->getCoupon(1, 'id');
        if ($response['error']) {
            exit('kupon bulunamadı');
        }
        $coupon = $response['result']['set'];
        unset($response);
        //echo $coupon->getCode(); die;

        $shipping = new BundleEntity\Proxy\CartItemShippingProxyEntity();
        $shipping->setPrice((float) 15.00);
        $shipping->setDiscount((float) 5.00);


        $TMB = $this->get('taxmanagement.model');
        $response = $TMB->getTaxRate(1, 'id');
        if ($response['error']) {
            exit('tax bulunamadı');
        }
        $tax = $response['result']['set'];
        unset($response);
        $PMB = $this->get('productmanagement.model');
        $response = $PMB->getProduct(1, 'id');
        if ($response['error']) {
            exit('ürün bulunamadı');
        }
        $product = $response['result']['set'];
        unset($response);
        $response = $PMB->listCategoriesOfProduct(1);
        if ($response['error']) {
            exit('ürün kategori bulunamadı');
        }
        $productCategory = $response['result']['set'][0];
        unset($response);

        $cartItem_ = new BundleEntity\Proxy\CartItemProxyEntity();
        $cartItem_->setProduct($product);
        $cartItem_->setProductCategory($productCategory);
        $cartItem_->setShipping($shipping);
        $cartItem_->setTax($tax);

        $cart = new BundleEntity\Proxy\CartProxyEntity();
        $cart->addItem($cartItem_);
        $cart->addCoupon($coupon);
        $discount = $cart->applyCoupons($cart->calculateTotal());
        if ($discount['err']) {
            echo $discount['err'];
            exit();
        }





        /**
          echo "Ürün Adı : \t" . $cartItem->getProductName().'<br>';
          echo "Ürün Fiyatı : \t" . $cartItem->getProductPrice().'<br>';
          echo "Vergi (%".$cartItem->getTax()->getCategory()->getRate() ."): \t" . $cartItem->calculateTotalTax().'<br>';
          echo "Kargo : \t" . $cartItem->calculateTotalShipping().'<br>';
          echo "Toplam  : \t" . $cartItem->calculateTotal();
         * 
         */
        echo "<style>";
        echo "table tr td { width:200px; text-align:center;} ";
        echo "</style>";
        echo "<table border=\"1\">"
        . "<tr>"
        . "<td>Ürün Adı</td>"
        . "<td>Ürün Fiyatı</td>"
        . "<td>KDV</td>"
        . "<td>Kargo</td>"
        . "<td>Toplam</td>"
        . "</tr>";
        foreach ($cart->getItems() as $item) {
            echo "<table>"
            . "<tr>"
            . "<td>" . $item->getProduct()->getLocalization('tr')->getName() . "</td>"
            . "<td>" . $item->getProduct()->getPrice() . "</td>"
            . "<td>" . $item->calculateTotalTax() . " (%" . $item->getTax()->getRate() . ")</td>"
            . "<td>" . number_format($item->calculateTotalShipping(), 2) . "</td>"
            . "<td>" . $item->calculateTotal() . "</td>"
            . "</tr>";
        }
        foreach ($cart->getCoupons() as $coupon) {
            echo "<table>"
            . "<tr>"
            . "<td>" . $coupon->getLocalization('tr')->getName() . "</td>"
            . "<td>&nbsp</td>"
            . "<td>&nbsp</td>"
            . "<td>&nbsp</td>"
            . "<td> -" . $coupon->getDiscount() . "</td>"
            . "</tr>";
        }
        echo "<tr>"
        . "<td>ARA TOPLAM</td>"
        . "<td>&nbsp</td>"
        . "<td>&nbsp</td>"
        . "<td>&nbsp</td>"
        . "<td>" . ($item->calculateTotal() - $discount) . "</td>"
        . "</td>";
        echo "<table>";
        die;
    }

}
