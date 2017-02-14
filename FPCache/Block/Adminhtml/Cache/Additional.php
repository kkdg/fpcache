<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/19/16
 * Time: 11:12 AM
 */

class MagentoRemotely_FPCache_Block_Adminhtml_Cache_Additional extends Mage_Adminhtml_Block_Cache_Additional
{
    /**
     * @deprecated-canceled
     */
    public function checkEnabled() {

        return Mage::app()->useCache()['fpcache'];

//        if ( Mage::app()->useCache()['fpcache'] ) {
//            echo $this->getLayout()
//                      ->createBlock('magentoremotely_fpcache/adminhtml_cache_grid')
//                      ->setTemplate('magentoremotely/fpcache/cache_admin.phtml')
//                      ->toHtml();
//        }
    }

    public function getFPCImgPurgeUrl() {
        return $this->getUrl('*/remotely/cleanImagesFpc');
    }

    public function getFPCPurgeUrl() {
        return $this->getUrl('*/remotely/cleanFpc');
    }

    public function getCaches() {
        $file_cache = new MagentoRemotely_FPCache_Model_Cache();
        $frontend = $file_cache->getFrontend();

//        var_dump($sss->getOption('file_name_prefix'));

        $backend = $frontend->getBackend();

        $cache_dir = $backend->getOption('cache_dir');
//        var_dump($backend);

        $ids = $frontend->getIds();

        foreach( $ids as $id ) {
            $meta = $frontend->getMetadatas($id);
            foreach($meta['tags'] as $k => $tag) {
//                echo $tag;
//                echo gettype($k);
                if ( gettype($k) == 'string') {
                    $meta['tags']['handle'] = $k;
                    unset($meta['tags'][$k]);
                }
            }
//            $meta['tags'] = array_flip($meta['tags']);
//            var_dump($meta);
            echo date('m/d/Y', $meta['mtime']);
            echo date('m/d/Y', $meta['expire']);
        }
//        var_dump($d);
//        $meta = $frontend->getMetadatas($d[0]);
//        var_dump($meta);
//        $m = $sss->getMetadatas($d[1]);
//        var_dump($m);
//        foreach($d as $z) {
//            echo $z;
////            var_dump($file_cache->getMetadatas($z));
//        }
    }

    /**
     * @deprecated
     * @param $dir
     * @param $prefix
     * @param array $tags
     * @return array|bool
     */
    protected function _get($dir, $prefix, $tags = array()) {

        if (!is_dir($dir)) {
            return false;
        }
        $result = array();

        $glob = @glob($dir . $prefix . '--*');
        if ( $glob === false ) {
            return array();
        }
        foreach($glob as $file) {
            if (is_file($file)) {

            } elseif (is_dir($file)) {

            }
        }
    }
}