<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 10/6/16
 * Time: 2:51 PM
 */


/**
 * @deprecated
 * Class MagentoRemotely_FPCache_Model_Cookie_Welcome
 */
class MagentoRemotely_FPCache_Model_Cookie_Welcome extends Mage_Core_Model_Cookie
{

    protected function _getSession() {
        return $session = Mage::getSingleton('customer/session');
    }

    public function getDynamicContents() {
        if ( $this->_getSession()->isLoggedIn() ) {
            $welcome = "Welcome" . Mage::helper('core')->escapeHtml($this->_getSession()->getCustomer()->getName());
        } else {
            $welcome = Mage::getStoreConfig('design/header/welcome');
        }
        return $welcome;
    }
}