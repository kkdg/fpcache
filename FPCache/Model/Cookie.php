<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 10/6/16
 * Time: 11:16 AM
 */

class MagentoRemotely_FPCache_Model_Cookie extends Mage_Core_Model_Cookie
{
    const prefix = 'degi_fpc';

    const LOGGED_IN_FLAG = 1;

    const LOGGED_OUT_FLAG = 2;

    protected function _checkBlocks($blocks) {
        $request = Mage::app()->getRequest();

        $cookies = array();
        if ( $blocks == null ) {
            foreach(Mage::helper('magentoremotely_fpcache')->getCookieBlocks() as $block) {
                $cookies[$block] = $this->checkBlock($block,$request);
            }
        } else {
            foreach($blocks as $block) {
                $cookies[$block] = $this->checkBlock($block,$request);
            }
        }

        return $cookies;
    }

    public function checkBlocks($blocks = null) {

        $cookies = $this->_checkBlocks($blocks);

        $cookies['login_flag'] = self::LOGGED_IN_FLAG;

        return $cookies;
    }

    public function checkBlocksLoggedOut($blocks = null) {

        $session = Mage::getSingleton('customer/session');
        $session->setId(null);
        $session->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        $session->getCookie()->delete($session->getSessionName());

        $cookies = $this->_checkBlocks($blocks);

        $cookies['login_flag'] = self::LOGGED_OUT_FLAG;

        return $cookies;
    }

    public function checkBlock($block_name,$request) {
//            'top.links',
//            'welcome',
//            'formkey',
//            'top.right.nav',

        switch($block_name) {
            case 'top.links':
                $class = Mage::getSingleton('magentoremotely_fpcache/cookie_topLinks');
                $key = 'top.links';
                break;
            case 'welcome':
                $class = Mage::getSingleton('magentoremotely_fpcache/cookie_welcome');
                $key = 'welcome';
                break;
            case 'formkey':
                $class = Mage::getSingleton('magentoremotely_fpcache/cookie_formKey');
                $key = 'formkey';
                break;
            case 'top.right.nav':
                $class = Mage::getSingleton('magentoremotely_fpcache/cookie_topRightNav');
                $key = 'top.right.nav';
            default;
        }

        if ( $class == '' ) return null;

        $check = $this->checkIfCookieAlreadyExists($key);
        if ( ! $check ) {
            $value[$key] = $class->getDynamicContents();
        }

        return $value;
//        $this->set(self::prefix .'_'. $key, $value,self::prefix,1,7200,$request->getBasePath(),$request->getHttpHost(),$request->isSecure(),false);

    }

    public function checkIfCookieAlreadyExists($key) {
        return $this->get('degi-fpc-'.$key);
    }


}