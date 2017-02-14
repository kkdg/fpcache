<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/20/16
 * Time: 3:37 PM
 */

class MagentoRemotely_FPCache_Adminhtml_RemotelyController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return MagentoRemotely_FPCache_Helper_Update
     */
    protected function _helper() {
        return Mage::helper('magentoremotely_fpcache/update');
    }

    /**
     * @param $param
     */
    protected function _params($param) {


    }

    public function cleanFpcAction() {
        $this->_helper()->purgeAll();

        $this->_redirectReferer();
    }

    public function cleanImagesFpcAction() {
        $this->_helper()->purgeImgAndFpc();

        $this->_redirectReferer();
    }

    public function refreshAction() {
        $params = $this->getRequest()->getParams();
        $handle = $params['cache_handle'];
        $uri = $params['uri'];

        $this->_helper()->refreshCache($handle,$uri);

        $this->_redirectReferer();
    }

    public function purgeAction() {
        $params = $this->getRequest()->getParams();
//        $key = strtoupper($this->_helper()->getKey($params['cache_handle']));
        $key = strtoupper($params['cache_handle']);

        $this->_helper()->purgeCache($key);

        $this->_redirectReferer();
    }

    public function changeTTLAction() {
        $params = $this->getRequest()->getParams();
        $key = strtoupper($params['cache_handle']);

        $this->_helper()->setLifetime($key, $params['created'], $params['old_ttl'], $params['ttl']);

        $this->_redirectReferer();
    }

    public function contentPageAction() {
        $params = $this->getRequest()->getParams();
        $key = $params['cacheId'];
        $uri = $params['uri'];

        echo $this->_helper()->getCacheContent($key);

/* Diff tester */
//        $a = $this->_helper()->getCacheContent($key);
//        $b = file_get_contents(Mage::getUrl($uri,array('fpc'=>'')));

//
//        $a1 = explode(" " , $a);
//        $a2 = explode(" ", $b);
//
//        echo join(' ', array_diff($a1, $a2));
#######################################################
//        $diff = xdiff_string_diff($a, $b, 1);
//        if (is_string($diff)) {
//            echo "Differences between two articles:\n";
//            echo $diff;
//        }
#######################################################
//        echo $a;
//        echo $b;
//        if ( $a == $b ) echo 1;
//        else echo 2;
    }
}