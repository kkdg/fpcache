<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 8/29/16
 * Time: 1:03 PM
 */ 
class MagentoRemotely_FPCache_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     *
     */
    const ThemeConfigPath = "design/theme/";

    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    protected $_designTheme = array('template','layout','skin');

    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout = array();

    /**
     * @return array
     */
    public function getCacheHandles() {
        return array(
            'cms_index_index',
//            'catalog_product_view',
            'catalog_category_view',
            'cms_page_view',
        );
    }

    /**
     * @return array
     */
    public function getDynamicBlocks() {
        return array_merge($this->getCookieBlocks(), $this->getAjaxBlocks());
    }

    /**
     * @return array
     */
    public function getCookieBlocks() {
        return array(
            'fpcache.welcome',
            'formkey',
            'fpcache.cartqty',
            'fpcache.mobile.welcome',
            'fpcache.minicart.qty',
        );
    }

    /**
     * @return array
     */
    public function getAjaxBlocks() {
        return array(
            'fpcache.wowpoint',
            'fpcache.wowpoint.detail',
        );
    }

    /**
     * @deprecated
     * @param $nameInLayout
     * @return string
     */
    public function getPlaceholder($nameInLayout)
    {
        return md5($nameInLayout);
    }

    /**
     * @param $nameInLayout
     * @return string
     */
    public function renderPlaceholder($nameInLayout)
    {
        return '<span class="degi-fpcache" id="' . $this->buildBlockKey($nameInLayout) . '"></span>';
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @return string
     */
    public function getFullActionName(Mage_Core_Controller_Request_Http $request)
    {
        return $request->getModuleName() . '_' .$request->getControllerName() . '_' .$request->getActionName();
    }

    /**
     * @param $uri
     * @return string
     */
    public function buildKey($uri)
    {
        $patternQuery = $this->patternQuery();
//        Mage::log($uri.' : '.$patternQuery);
        return md5($uri . $patternQuery);
    }

    /**
     * @param $nameInLayout
     * @return string
     */
    public function buildBlockKey($nameInLayout) {
        return md5($nameInLayout);
    }

    /**
     * @return string
     */
    public function patternQuery() {

        $themeMap = $this->themeCheck();

        $exceptionThemeAppend = "?";
        foreach($themeMap as $key => $exception) {
            if ( $exception != ""  ) {
                $exceptionThemeAppend .= $key . "=" . $exception . "&";
            }
        }
        if ( $this->isApp() ) {
            if ( $this->isAndroid() ) {
                $exceptionThemeAppend .= 'App' . "=" . "Android";
            } else if ( $this->isIPhone() ) {
                $exceptionThemeAppend .= 'App' . "=" . "iPhone";
            }
        }
        if (substr($exceptionThemeAppend,-1) == "&") {
            $exceptionThemeAppend = rtrim($exceptionThemeAppend,"&");
        } else {
            $exceptionThemeAppend = trim($exceptionThemeAppend,"?");
        }


        return $exceptionThemeAppend;
    }


    public function isApp()
    {
        $userAgent = strtolower(Mage::helper('core/http')->getHttpUserAgent());

        if(strpos($userAgent,'isapp=i') || strpos($userAgent,'isapp=a'))
        {
            return true;
        }
        return false;
    }

    public function isAndroid() {
        $userAgent = strtolower(Mage::helper('core/http')->getHttpUserAgent());

        if(strpos($userAgent,'isapp=a'))
        {
            return true;
        }
        return false;
    }

    public function isIPhone() {
        $userAgent = strtolower(Mage::helper('core/http')->getHttpUserAgent());

        if(strpos($userAgent,'isapp=i'))
        {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isCacheUsed()
    {
        return Mage::app()->useCache('fpcache');
    }

    /**
     * @deprecated
     * @return array
     */
    public function getCaches() {
        $file_cache = Mage::getSingleton("magentoremotely_fpcache/cache"); //MagentoRemotely_FPCache_Model_Cache();
        $frontend = $file_cache->getFrontend();

//        $backend = $frontend->getBackend();
//        $cache_dir = $backend->getOption('cache_dir');

        $ids = $frontend->getIds();

        foreach( $ids as $id ) {
            $meta = $frontend->getMetadatas($id);
            foreach($meta['tags'] as $k => $tag) {
                if ( gettype($k) == 'string' && $k[0] != "?") { // need to array_flip elements whose key is string.
                    $meta['tags']['handle'] = $k;
                    unset($meta['tags'][$k]);
                }
            }
            $meta['key'] = $id;
            $metas[] = $meta;

        }

        return $metas;
    }

    /**
     *
     */
    public function runAppBackground($handle, $blocks) {

        $layout = Mage::app()->getLayout();
        $layout->getUpdate()
//               ->addHandle('default')
//               ->addHandle($handle)
               ->load();
//echo $handle;
        $layout->generateXml()
               ->generateBlocks();

        $generated = array();
        foreach( $blocks as $block ) {

            $key = $this->buildBlockKey($block);
            $generated[$key] = $layout->getBlock($block)->toHtml();
        }


        return $generated;

    }

    public function test($handle,$blocks) {
        $layout = Mage::app()->getLayout();
        $a = $layout->getUpdate()->getHandles();
        return $a;
    }

    public function fpcCheck($request) {
        return isset($request->getParams()['fpc']) || isset($request->getParams()['SID']);
    }

    public function themeCheck() {

        $theme =  Mage::app()->getConfig()->getNode('remotely/theme')->asArray();

        $pkg = Mage::getSingleton('core/design_package');
//MAge::log($pkg->_checkUserAgentAgainstRegexps())
        foreach($this->_designTheme as $designPart) {
            $path = $designPart."_ua_regexp";
            $package = $pkg->getPackageByUserAgent(unserialize($theme[$path]),self::ThemeConfigPath.$path);
            $themeMap[$designPart] = $package;
        }

        return $themeMap;
    }

    public function formatCookie($block) {

        $block = str_replace(".","_",$block);

        return $block;
    }

    public function getCookie() {
        $blocks = $this->getCookieBlocks();
        $cook = Mage::getSingleton('core/cookie');
        $bool = true;
        foreach( $blocks as $block ) {

            $block = str_replace(".","_",$block);
            $cookie = $cook->get($block);
            $bool = $bool && $cookie;
        }

        return $bool;
    }

    /**
     * @return bool
     */
    public function loadAjaxScript() {
        $request = Mage::app()->getRequest();
        $cacheSaveOffSwitch = $this->fpcCheck($request);
        $actionName = $this->getFullActionName($request);
        if ( in_array($actionName, $this->getCacheHandles()) && ! $cacheSaveOffSwitch ) {
            return true;
        }
    }

    public function purgeCache() {
        $blocks = $this->getCookieBlocks();

        foreach( $blocks as $cookie ) {
            setcookie($cookie,null,time()-1,'/');
        }
    }
}
