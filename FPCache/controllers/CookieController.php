<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/7/16
 * Time: 10:46 AM
 */

class MagentoRemotely_FPCache_CookieController extends Mage_Core_Controller_Front_Action
{

//    const COOKIE_BLOCKS = array('formkey', '')

    protected $_helper = '';

    protected function _helper() {
        if ( $this->_helper == '' ) {
            $helper = $this->_helper = Mage::helper('magentoremotely_fpcache');
        } else {
            $helper = $this->_helper;
        }
        return $helper;
    }

    protected function _checkNode() {

    }

    protected function _getCookies($blocks) {
        if ( Mage::getSingleton('customer/session')->isLoggedIn() ) {
            $cookies = Mage::getSingleton('magentoremotely_fpcache/cookie')->checkBlocks($blocks);
        } else {
            $cookies = Mage::getSingleton('magentoremotely_fpcache/cookie')->checkBlocksLoggedOut($blocks);
        }
        return $cookies;

    }

    public function signinAction() {
        $param = $this->getRequest()->getParam('cookie');
        Mage::log($param,null,'paraparapa');
        $signin = !! Mage::getSingleton('customer/session')->isLoggedIn();

        if ( $param != $signin || $param == '' ) {
            $this->_helper()->purgeCache();
        }

        setcookie('fpcache.login',$signin,time()+ MagentoRemotely_FPCache_Model_Observer::COOKIE_LIFETIME,'/');

        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($signin));
    }

    public function personalAction() {

        $params = $this->getRequest()->getParam('degi-data');

        $request = $this->getRequest()->getParam('degi-request');

        $helper = $this->_helper();
        $this->loadLayout();
//
//        Mage::log($request,null,'handle',true);
        $blocks = $helper->runAppBackground($request,$params);
//
//        $data = '';
//        $data['a'] =1;

//        $cookies = $this->_getCookies($params);
        Mage::log($blocks,null,'zdzd');

        foreach($params as $param) {
            if ( in_array($param,$helper->getCookieBlocks()) ) {
                $block = str_replace(array("\n", "\t", "\r"), '', $blocks[$helper->buildBlockKey($param)]);
                $cookies[$param] = $block;
            }
        }

        $data['blocks'] = $blocks;
        $data['cookies'] = $cookies;

        Mage::log($data,null,'dakura-ajax-cookie');

        $this->getResponse()
             ->clearHeaders()
             ->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($data));
    }

}
