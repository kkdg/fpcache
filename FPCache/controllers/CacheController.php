<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/20/16
 * Time: 2:30 PM
 */

class MagentoRemotely_FPCache_CacheController extends Mage_Core_Controller_Front_Action
{
    const path = "design/theme/";

    public function signingAction() {
        echo Mage::getSingleton('customer/session')->isLoggedIn() ? 1 : 0;

    }

    public function cAction() {
        $theme = Mage::helper('magentoremotely_fpcache')->themeCheck();
var_dump($theme);
        $dir = Mage::getBaseDir('etc');
//echo $dir;
//        var_dump($theme);

 $dir = Mage::getBaseDir('etc');

        $file = $dir . DS . "degi.xml";

//echo $file;
        $xml = simplexml_load_file($file);
//        header("Content-Type: text/xml");
//        echo $xml->children('remotely')->asXML();
//        echo $xml->remotely->theme->template;
//
//        echo $xml->asXML();
//        print_r($xml->asXML());
//        var_dump(get_class_methods($xml));


        $col = Mage::getSingleton('core/config_data')->getCollection();

        $cols = $col->addPathFilter('design/theme');

        $dir = Mage::getBaseDir('etc');

        $file = $dir . DS . "degi.xml";
        $xml = simplexml_load_file($file);
//        print_r($xml);
//        echo $xml->asXML();
        $b = $xml->children();
//        echo $b;
        $xmlTheme =  $xml->remotely->theme;

//        foreach($c as $y => $z) {
//            var_dump($z);
//            if($y == )
//        }
        $x=$xml->remotely->theme;
//        echo $x;
//        echo 'ssibal';
//        die();
        foreach($cols as $key => $col) {
//            echo $key;
//            echo "<br />";
//            var_dump($col->getData());
//            if( $col->getPath() == 'design/theme/' . $xmlTheme
            $len = strlen(self::path);
//            echo $len;
            $path = substr($col->getPath(),$len);
//            echo $path;
//            echo $xmlTheme->$path;
            $xmlTheme->$path = $col->getValue();
//            echo "<br />";

        }

//        echo $xml->asXml($file);
        var_dump($xml);
        echo $xml->saveXML($file);

    }

    public function sAction() {
       echo md5("/index.php/?template=ddd&skin=Genius");
        echo "<br />";
       echo md5("/index.php/?template=Genius&skin=Genius");
    }

    public function dAction() {
        $helper = Mage::helper('magentoremotely_fpcache');
        $this->loadLayout();
//        $data = $helper->runAppBackground('cms_index_index',array('welcome'));
        $data = $helper->test('cms_index_index',array('welcome'));
//        var_dump($data);
        $area = 'frontend';
        $package = 'rwd';
        $theme = 'special';
        $c = Mage::getSingleton('core/design_config')->getNode("$area/$package/$theme/layout/updates");
        $c = Mage::getConfig()->getModelClassName('core/layout_element');
        // ("$area/$package/$theme");
//        var_dump($c);
        $d = Mage::getSingleton('core/design_config')->getNode('frontend/rwd');
//        var_dump($d);
        $time = microtime();
        $updatesRoot = Mage::app()->getConfig()->getNode($area.'/layout/updates');
//        var_dump($updatesRoot);
        $updates = $updatesRoot->asArray();
        $themeUpdates = Mage::getSingleton('core/design_config')->getNode("$area/$package/$theme/layout/updates");
        if ($themeUpdates && is_array($themeUpdates->asArray())) {
            //array_values() to ensure that theme-specific layouts don't override, but add to module layouts
            $updates = array_merge($updates, array_values($themeUpdates->asArray()));
        }
        $updateFiles = array();
        foreach ($updates as $updateNode) {
            if (!empty($updateNode['file'])) {
                $module = isset($updateNode['@']['module']) ? $updateNode['@']['module'] : false;
                if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module, $storeId)) {
                    continue;
                }
                $updateFiles[] = $updateNode['file'];
            }
        }
        // custom local layout updates file - load always last
        $updateFiles[] = 'local.xml';
        $layoutStr = '';
//        var_dump($updateFiles);
//        $fileStr = file_get_contents($filename);
        $design = Mage::getSingleton('core/design_package');

        foreach ($updateFiles as $file) {
            $filename = $design->getLayoutFilename($file, array(
                '_area'    => $area,
                '_package' => $package,
                '_theme'   => $theme
            ));
            if (!is_readable($filename)) {
                continue;
            }
            $fileStr = file_get_contents($filename);
            $fileStr = str_replace($this->_subst['from'], $this->_subst['to'], $fileStr);
            $fileXml = simplexml_load_string($fileStr, $c);
            if (!$fileXml instanceof SimpleXMLElement) {
                continue;
            }
            $layoutStr .= $fileXml->innerXml();
        }
        header("Content-Type: text/xml");
        echo "<layout>";
            echo $layoutStr;
            echo "<time>";
                echo microtime() - $time;
            echo "</time>";
        echo "</layout>";
    }

    public function eAction() {
        $a = microtime();
//        $this->loadLayout();
//echo 1,2,3;
//        echo "1"."2"."3";
//        $this->_cacheId = 'LAYOUT_'.Mage::app()->getStore()->getId().md5(join('__', $this->getHandles()));


        $layout = $this->getLayout();
        $layout->getUpdate()
            ->loadCache();
//               ->addHandle('default')
//               ->addHandle('catalog_product_view')
//            ->load('cms_index_index');
//                ->load();
        $layout->generateXml()
            ->generateBlocks();

//        echo $layout->getBlock('welcome')->toHtml();
        echo microtime() - $a;

    }

    public function zAction() {
        $helper = Mage::helper('magentoremotely_fpcache/update');
//        var_dump($helper->getCache());
        $cache = $helper->getCache();

        $f = $cache->getFrontend();

        $ids = $f;
        $idsb = $f->getBackend();
//        $ids = $f->getIds();
        var_dump($ids);
        var_dump($idsb);
    }

    public function xAction() {
        echo 'x';
        $cache = Mage::getSingleton('core/cache');

        $front = Mage::getSingleton('core/cache')->getFrontend();

        $frontend = Zend_Cache::factory('Varien_Cache_Core', 'Cm_Cache_Backend_Redis', array(
                    'caching' => 1,
                    'lifetime' => 7200,
                    'automatic_cleaning_factor' => 0,
                    'cache_id_prefix' => '2d9_'
                ), array(
                    'server' => '127.0.0.1',
                    'port'  => "6379",
                    'database' => 0,
                    'password' => '',
                    'force_standalone' => 0,
                    'connect_retries' => 1,
                    'read_timeout' => 10,
                    'automatic_cleaning_factor' => 0,
                    'compress_data' => 1,
                    "compress_tags" => 1,
                    "compress_threshold" => 20480,
                    "compression_lib" => 'gzip',
                    'use_lua'   => 0
                ),
            true, true, true
        );
//        $front->setBackend('cm_redis');
//        $redis = $front->getBackend();
//
//        var_dump($redis);
//        var_dump($frontend);

        $ids = $frontend->getIds();

        foreach( $ids as $id ) {
//            var_dump($id);
            $meta = $frontend->getMetadatas($id);
            var_dump($meta);
        }
    }

    public function vAction() {
        echo 'v';
        $a = [1,2,3];



        $a = ['a','b','c'];
        var_dump($a);

        echo array_search('c',$a);
        echo array_search('d',$a);








    }

}