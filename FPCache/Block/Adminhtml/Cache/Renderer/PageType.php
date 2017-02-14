<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/21/16
 * Time: 2:51 PM
 */

class MagentoRemotely_FPCache_Block_Adminhtml_Cache_Renderer_PageType extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $key = urlencode($row->getData('key'));
        $uri = urlencode($row->getData('uri'));

        $content_handle = 'adminhtml/remotely/contentPage/cacheId/' . $key . '/uri/' . $uri;
        $link = Mage::helper('adminhtml')->getUrl($content_handle);

        $url = "<a target='_blank' href='" . $link . "'>View</a>";

        return $url;

    }
}