<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/2/16
 * Time: 1:41 PM
 */

class MagentoRemotely_FPCache_Model_Processor
{
    /**
     * @var MagentoRemotely_FPCache_Model_Cache
     */
    protected $_cache;

    /**
     * @var bool
     */
    protected $_cacheLoadOffSwitch = false;

    /**
     * @var array
     */
    protected $_html = array();

    /**
     * @var array
     */
    protected $_placeholder = array();


    protected $_helper = '';


    /**
     * @param bool $content
     */
    public function extractContent($content)
    {
        $request = Mage::app()->getRequest();

        return $this->_processRequest($request);
    }

    protected function _processRequest(Mage_Core_Controller_Request_Http $request)
    {

        $this->_helper = new MagentoRemotely_FPCache_Helper_Data();

        $key = $this->_helper->buildKey($request->getRequestUri());
        $cache = $this->_getCache();
        $this->_cacheLoadOffSwitch = $this->_helper->fpcCheck($request);
        $canuse = Mage::app()->getCacheInstance()->canUse('fpcache');
        
        if (!$this->_cacheLoadOffSwitch && $canuse) {

            $body = $cache->load($key); // Mage_Core_Model_Cache::OPTIONS_CACHE_ID;


            if ($body) {

//                $body = $this->_cacheFromCookies($cache,$body,$key);

//                $cookie->set(MagentoRemotely_FPCache_Model_Cookie::prefix.'_loaded',1,7200,$request->getBasePath(),$request->getHttpHost(),$request->isSecure(),false);

                return $body;
            }
        }
    }

    protected function _cacheFromCookies($cache,$body,$key) {
        $cookie = Mage::getSingleton('core/cookie');
        $frontend = $cache->getFrontend();
//                $ids = $frontend->getIds();
//                var_dump($ids);
//                echo $key;
        $metadatas = $frontend->getMetadatas(strtoupper($key));
//                var_dump($metadatas);
        $tags = $metadatas['tags'];
        $prefix = $frontend->getOption('cache_id_prefix');
        $blocks = array_keys($tags,$prefix."BLOCKS");

        $keys = array();
        $cookies = array();
//                var_dump($prefix);
//                var_dump($blocks);
//        var_dump($_COOKIE);
        foreach($blocks as $block) {
            $keyblock = $block;
//            $block = str_replace(".","_",$block);

            $block = $this->formatCookie($block);

//            var_dump($block);
            $cookies[$block] = rawurldecode($cookie->get($block));
            $keys[$block] = $this->_helper->buildBlockKey($keyblock);
//                    Mage::log($keys[$block] . " : " . $cookies[$block],null,'keysblock',true);
            $span = $this->getSpan($keys[$block]);
            $body = str_replace($span,$cookies[$block],$body);
//                    Mage::log($cookies[$block],null,'moyaa',true);
//                    Mage::log($cookie->get('top_links'),null,'welcomei',true);
//                    Mage::log($_COOKIE,null,'welcomei',true);
//                    Mage::log($span,null,'moyaa',true);

//                    Mage::log($body,null,'znagarehosini',true);
        }

        return $body;
    }

    public function getSpan($block){
        return '<span class="degi-fpcache" id="' . $block . '"></span>';
    }

    public function formatCookie($block) {
        return $this->_helper->formatCookie($block);
    }

    protected function _getCache()
    {
        if (is_null($this->_cache)) {
            $this->_cache = new MagentoRemotely_FPCache_Model_Cache();
        }
        return $this->_cache;
    }
}