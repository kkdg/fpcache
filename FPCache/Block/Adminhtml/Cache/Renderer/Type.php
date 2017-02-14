<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/20/16
 * Time: 4:20 PM
 */

class MagentoRemotely_FPCache_Block_Adminhtml_Cache_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $uri = urlencode($row->getData('uri'));
        $value = urlencode($row->getData('key'));

        $refresh_handle = 'adminhtml/remotely/refresh/';
        $link = Mage::helper('adminhtml')->getUrl($refresh_handle, array('cache_handle' => $value, 'uri' => $uri));

        $url = "<a href='" . $link . "'>Refresh</a>";

        return $url;
    }
}