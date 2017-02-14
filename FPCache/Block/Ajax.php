<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/6/16
 * Time: 5:37 PM
 */

class MagentoRemotely_FPCache_Block_Ajax extends Mage_Core_Block_Template
{

    public function helper() {
        return Mage::helper('magentoremotely_fpcache');
    }
    /**
     * @return array
     */
    public function catchBlockName() {

        return Mage::getSingleton('magentoremotely_fpcache/observer')->getDynamicBlockForThePage();
    }

    public function catchCookieBlockName() {

        return Mage::getSingleton('magentoremotely_fpcache/observer')->getCookieBlockForThePage();
    }

    public function catchAjaxBlockName() {
        return Mage::getSingleton('magentoremotely_fpcache/observer')->getAjaxBlockForThePage();
    }


    public function getBlockKeys() {
        $blocks = array();
        foreach ( $this->catchCookieBlockName() as $block ) {
            $blocks[$block] = $this->helper()->buildBlockKey($block);
        }

        return $blocks;
    }

    public function getAjaxBlocks() {
        return $this->helper()->getAjaxBlocks();
    }

    public function getRequestHandle() {
        return Mage::helper('magentoremotely_fpcache')->getFullActionName($this->getRequest());
    }

    /**
     * @return bool
     */
    public function loadAjaxScript() {
        $request = Mage::app()->getRequest();
        $helper = Mage::helper('magentoremotely_fpcache');
        $this->_cacheSaveOffSwitch = $helper->fpcCheck($request);
        $this->_actionName = $helper->getFullActionName($request);
        if ( in_array($this->_actionName, $helper->getCacheHandles()) && ! $this->_cacheSaveOffSwitch ) {
            return true;
        }
    }

    /**
     *
     * @deprecated
     * @return array
     */
    public function getCookies() {
        $cookie = Mage::getSingleton('core/cookie');
        if ( ! $cookie->get(MagentoRemotely_FPCache_Model_Cookie::prefix) ) {

//            $cookie->set(MagentoRemotely_FPCache_Model_Cookie::prefix,1,7200,$request->getBasePath(),$request->getHttpHost(),$request->isSecure(),false);
            if ( Mage::getSingleton('customer/session')->isLoggedIn() ) {
                $cookies = Mage::getSingleton('magentoremotely_fpcache/cookie')->checkBlocks();
            } else {
                $cookies = Mage::getSingleton('magentoremotely_fpcache/cookie')->checkBlocksLoggedOut();
            }

            return $cookies;

        }

    }

    public function getSigninUrl() {
        return Mage::getUrl('fpcache/cookie/signin',array('_secure'=>true));
    }

    public function getAjaxUrl() {
        return Mage::getUrl('fpcache/ajax/personal',array('_secure'=>true));
    }

    public function getCookiesUrl() {
        return Mage::getUrl('fpcache/cookie/personal',array('_secure'=>true));
    }

    public function getSignin() {
        return Mage::getSingleton('customer/session')->isLoggedIn() ? 1 : 0;
    }
}
