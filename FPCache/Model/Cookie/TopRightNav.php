<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 10/6/16
 * Time: 4:17 PM
 */


/**
 * @deprecated
 * Class MagentoRemotely_FPCache_Model_Cookie_TopRightNav
 */
class MagentoRemotely_FPCache_Model_Cookie_TopRightNav extends Mage_Core_Model_Cookie
{

    public function getDynamicContents() {

        $cartQty = Mage::getSingleton('checkout/cart')->getSummaryQty();

        return $cartQty;
    }
}