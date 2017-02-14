<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/2/16
 * Time: 9:04 AM
 */

class MagentoRemotely_FPCache_Model_Observer
{

    const CACHE_TAG = 'NRS_FPC';

    const COOKIE_LIFETIME = MagentoRemotely_FPCache_Model_Cache::DEFAULT_LIFETIME;

    /**
     * @param $event
     */
    public function cacheStatus($event) {
//        var_dump($event->getEvent()->getData());

//        $node = Mage::getConfig()->getNode('global/cache');
//        var_dump($node);

//        $block = Mage::app()->getLayout()->createBlock('magentoremotely_fpcache/ajax');
//        $cookies = $block->getCookies();
//        var_dump($cookies);
    }

    /**
     * @var bool
     */
    protected $_cacheSaveOffSwitch = false;

    /**
     * @var array
     */
    protected $_placeholders = array();

    /**
     * @var string
     */
    protected $_requestUri = '';

    /**
     * @var string
     */
    protected $_actionName = '';

    /**
     * @var array
     */
    protected $_dynamicBlockNames = array();

    protected $_ajaxBlockNames = array();

    protected $_cookieBlockNames = array();

    /**
     * @var array
     */
    protected $_html = array();

    /**
     * @var
     */
    protected $_recoverBlock;

    /**
     * @var bool
     */
    protected $_blockNameShow = false;

    /**
     * @param $observer
     */
    public function httpResponseSendBefore($observer)
    {
        $helper = Mage::helper('magentoremotely_fpcache');

        /*
         * bypass saving cache when agent is app
         */
        if ( $helper->isApp() ) return;

        if ($helper->isCacheUsed()) {
            $request =  Mage::app()->getRequest();
            $requestUri = $this->_requestUri;
            $this->_actionName = $helper->getFullActionName($request);

//            var_dump($patternQuery);
//            die();
//            $theme = unserialize($theme);
//            var_dump($theme);die();
            $patternQuery = $helper->patternQuery();

            if (in_array($this->_actionName, $helper->getCacheHandles())) {
                $key = $helper->buildKey($requestUri);
                $cache = Mage::getSingleton('magentoremotely_fpcache/cache');
                $body = $observer->getEvent()->getResponse()->getBody();
                $this->_cacheSaveOffSwitch = $helper->fpcCheck($request);
//                die(var_dump($this->_cacheSaveOffSwitch));
                if ( ! $this->_blockNameShow && ! $this->_cacheSaveOffSwitch ) {
                    $handles = Mage::app()->getLayout()->getUpdate()->getHandles();
//                    $layout_cache_id = 'LAYOUT_'.Mage::app()->getStore()->getId().md5(join('__', $handles));
                    $tags = array();
//                    echo "Save : ", $key;
                    $tags[$requestUri] = 'URI';
                    $tags[$patternQuery] = 'PTN';
                    $tags[] = self::CACHE_TAG;
                    $tags[] = $key;
                    $tags[$this->_actionName] = 'handle';
                    foreach ( $this->_dynamicBlockNames as $blocks ) {
                        $tags[$blocks] = 'blocks';
                    }
                    $cache->save($body, $key, $tags, Mage_Core_Model_Cache::DEFAULT_LIFETIME);
                }
                $observer->getEvent()->getResponse()->setBody($body);
            }
        }
    }

    /**
     *
     * @param $observer
     */
    public function coreBlockAbstractToHtmlAfter($observer)
    {
        if ( strpos($this->_requestUri,'fpcache') !== false ) return;

        $helper = Mage::helper('magentoremotely_fpcache');

        /*
         * bypass saving cache when agent is app
         */
        if ( $helper->isApp() ) return;

        $request = Mage::app()->getRequest();
        if ( $this->_actionName == '' ) {
            $this->_actionName = $helper->getFullActionName($request);
        }
        if ( ! in_array($this->_actionName,$helper->getCacheHandles()) ) return;

        $block = $observer->getEvent()->getBlock();
        $nameInLayout = $block->getNameInLayout();
        $this->_cacheSaveOffSwitch = isset($request->getParams()['fpc']);
        if ($helper->isCacheUsed() && ! $this->_cacheSaveOffSwitch ) {

            if (in_array($nameInLayout, $helper->getDynamicBlocks())) {
                if ( in_array($nameInLayout, $helper->getCookieBlocks()) ) {
                    $this->_cookieBlockNames[] = $nameInLayout;
                } else {
                    $this->_ajaxBlockNames[] = $nameInLayout;
                }
                $this->_dynamicBlockNames[] = $nameInLayout;

                $key = $helper->renderPlaceholder($nameInLayout);
                if ( $this->_blockNameShow ) {
                    $key = "<div style='border: 1px solid #fff'><span style='color: blue; font-weight:800'>" . $nameInLayout . "" . $key ."</span></div>";
                }
                $this->_placeholders[] = $key;
                $this->_html[] = $observer->getTransport()->getHtml();
                $observer->getTransport()->setHtml($key);
            } elseif ( $this->_blockNameShow ) {
                $blockHtml = $observer->getTransport()->getHtml();
                $blockName = "<div><span style='color: red; font-weight:800'>" . $nameInLayout . "</span></div>";
                $observer->getTransport()->setHtml($blockName . $blockHtml);
            }
        }
    }

    /**
     * @param $observer
     */
    public function coreAbstractLoadAfter($observer) {
        $this->_requestUri = Mage::app()->getRequest()->getRequestUri();
    }

    /**
     * @return array
     */
    public function getDynamicBlockForThePage() {
        return $this->_dynamicBlockNames;
    }

    /**
     * @return array
     */
    public function getAjaxBlockForThePage() {
        return $this->_ajaxBlockNames;
    }

    /**
     * @return array
     */
    public function getCookieBlockForThePage() {
        return $this->_cookieBlockNames;
    }

    /**
     * @forsaken
     * @param $observer
     */
    public function adminSystemConfigSectionSaveAfter($observer) {

    }

    /**
     * 1. This observer captures Admin > System > Design save action event.
     * 2. Fetch from saved data and save them to an app/etc .xml file.
     *
     * @param $observer
     *
     */
    public function adminSystemConfigChangedSectionDesign($observer) {

        $collection = Mage::getSingleton('core/config_data')->getCollection();
        $cols = $collection->addPathFilter('design/theme');

        $dir = Mage::getBaseDir('etc');
        $file = $dir . DS . "fpc.xml";
        $xml = simplexml_load_file($file);
        $xmlTheme =  $xml->remotely->theme;

        if( $xmlTheme ) {
            foreach($cols as $col) {
                $len = strlen(MagentoRemotely_FPCache_Helper_Data::ThemeConfigPath);

                $path = substr($col->getPath(),$len);

                $xmlTheme->$path = $col->getValue();
            }
            $xml->saveXML($file);
        }
    }

    /**
     * @param $observer
     */
    public function customerLogin($observer) {
        Mage::helper('magentoremotely_fpcache')->purgeCache();

        setCookie('fpcache.login',1,time()+ self::COOKIE_LIFETIME,'/');

    }

    /**
     * @param $observer
     */
    public function customerLogout($observer) {
        Mage::helper('magentoremotely_fpcache')->purgeCache();

        setCookie('fpcache.login',0,time()+ self::COOKIE_LIFETIME,'/');

    }

    /**
     * @param $obs
     * @var $obs
     */
    public function checkoutCartSaveAfter($obs) {
        $params = $obs->getEvent()->getData();
        $qty = $params['cart']['quote']->getData()['items_qty'];
        $qty = "(" . $qty . ")";
        setCookie('fpcache.cartqty',$qty,time()+ self::COOKIE_LIFETIME,'/');
        setCookie('fpcache.minicart.qty',$qty,time()+ self::COOKIE_LIFETIME,'/');
    }

    /**
     * @deprecated
     */
    protected function _purgeBlockCookies() {

        $helper = Mage::helper('magentoremotely_fpcache');
        $blocks = $helper->getDynamicBlocks();

        foreach($blocks as $block) {
            $block = str_replace(".","_",$block);
            $this->deleteCookie($block);
        }

    }

    public function getCookies() {

        $helper = Mage::helper('magentoremotely_fpcache');
        $helper->getCookie();
    }

    public function deleteCookie($cookie) {
        if ( isset($_COOKIE[$cookie]) ) {
            unset($_COOKIE[$cookie]);
            setcookie($cookie,null,time()-1,'/');

        }
    }

    /**
     *
     */
    public function process() {

        $helper = Mage::helper('magentoremotely_fpcache/update');

        $helper->purgeImgAndFpc();
    }
}
