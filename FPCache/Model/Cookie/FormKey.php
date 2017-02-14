<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 10/6/16
 * Time: 5:18 PM
 */

/**
 * @deprecated
 * Class MagentoRemotely_FPCache_Model_Cookie_FormKey
 */
class MagentoRemotely_FPCache_Model_Cookie_FormKey extends Mage_Core_Model_Cookie
{

    public function getDynamicContents() {

        $formkey = Mage::getSingleton('core/session')->getFormKey();

        return $formkey;
    }
}