<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/21/16
 * Time: 4:08 PM
 */

class MagentoRemotely_FPCache_Helper_Update extends Mage_Core_Helper_Abstract
{

    protected $_cache;

    protected $_engine;

    protected $_designTheme = array('template','layout','skin');

    protected function _getSession() {
        return Mage::getSingleton('adminhtml/session');
    }

    public function __construct() {
        $this->_cache = Mage::getSingleton("magentoremotely_fpcache/cache");
    }

    public function getKey($uri) {
        $ptn = Mage::helper('magentoremotely_fpcache')->patternQuery();

        return md5($uri . $ptn);
    }

    public function setLifetime($key, $createdTime, $oldLifeTime, $newLifetime) {
        $time = $newLifetime - $oldLifeTime + time() - $createdTime;

        $this->_cache->getFrontend()->touch($key,$time);
    }

    /**
     * @todo : find header and attach it to file_get_contents
     * @param $handle
     */
    public function refreshCache($handle,$uri) {
//
        $uri = Mage::getUrl() . $uri;
        $uri = preg_replace('/\/index.php\//','',$uri); // when URL is not


        // sweetened by web server rewrite rule
//echo $uri;
//        $uri = 'homesale';
//        echo "<br>";
        $frontend = $this->_cache->getFrontend();
        $metas = $frontend->getMetadatas($handle);
        $prefix = $frontend->getOption('cache_id_prefix');
//        echo $prefix;
        $template = $prefix . "PTN";
        $new_handle = array_search($template, $metas['tags']);
        $new_handle = $uri.$new_handle;
//        $uri = Mage::getUrl();
//        var_dump($new_handle);
//        echo "<br />";
//        echo $handle;
//        $url = Mage::getUrl($uri);
//        echo $url;die();
                $this->_cache->clean($handle);
//echo $new_handle;
//echo $uri;

        Mage::log(file_get_contents($new_handle),null,'filegetcontents');
        Mage::log($new_handle,null,'filegetcontents');

//        $this->setLifetime($handle, 0, 0, 7200);
//        $this->_cache->getFrontend()->touch($handle,7200);
//        var_dump($this->_cache->getFrontend()->getMetadatas($handle));
//
//        $metas = $this->_cache->getFrontend()->getMetadatas($handle);
//        $metas['expire'] = $metas['mtime'] + 7200 + time()

//        die();

    }

    public function purgeAll() {
//        $tag = MagentoRemotely_FPCache_Model_Observer::CACHE_TAG;
        try {
            $this->_cache->flush();
            $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('Full Page Cache was cleared.')
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

    }

    public function purgeImgAndFpc() {
        try {
            Mage::getModel('catalog/product_image')->clearCache();
            $this->_cache->flush();
            $this->reindexElastic();
            $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('The image cache, Full Page Cache and Elastic Search Index were cleared.')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while clearing the image cache.'));
        }
    }

    public function purgeCache($key) {
        $this->_cache->remove($key);

    }

    public function getCacheContent($id) {
        return $this->_cache->getFrontend()->load($id);
    }

    /**
     * @return array
     */
    public function getCacheMetas() {
        $frontend = $this->_cache->getFrontend();

        $ids = $frontend->getIds();

        $prefix = $frontend->getOption('cache_id_prefix');
//echo $prefix;
//var_dump($ids);
        foreach( $ids as $id ) {
            $meta = $frontend->getMetadatas($id);
	        $filter = array_search($prefix. 'NRS_FPC',$meta['tags']);
            if ( $filter === null || $filter === false ) continue;  // need to array_flip elements whose key is string.


            $k = array_search($prefix. 'URI',$meta['tags']);  // need to array_flip elements whose key is string.
            $meta['tags']['handle'] = $k;
            unset($meta['tags'][$k]);

            $meta['key'] = $id;
            $metas[] = $meta;
        }

        return $metas;
    }

    public function getThemes($key) {

        $frontend = $this->_cache->getFrontend();

        $meta = $frontend->getMetadatas($key);
        $prefix = $frontend->getOption("cache_id_prefix");

        $pattern = array_search($prefix.'PTN', $meta['tags']);
        $pattern = trim($pattern,"?");
        if ( strpos($pattern,'&') !== false ) {
            $pattern = explode('&',$pattern);

            foreach($pattern as $elem) {
                $kv = explode('=',$elem);
                $list[$kv[0]] = $kv[1];
            }
        } else {
            $kv = explode('=',$pattern);
            $list[$kv[0]] = $kv[1];
        }

        return $list;
    }

    protected function reindexElastic() {

        if ( Mage::helper('core')->isModuleEnabled('Bubble_Elasticsearch') ) {
            Mage::getSingleton('elasticsearch/indexer_category')->reindexAll();
            Mage::getSingleton('elasticsearch/indexer_cms')->reindexAll();
            Mage::getSingleton('elasticsearch/indexer_fulltext')->reindexAll();
        }
    }


    ///////////////////////////////////////

    public function getCache() {
        return $this->_cache;
    }
}
