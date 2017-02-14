<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/21/16
 * Time: 7:47 AM
 */

class MagentoRemotely_FPCache_Block_Adminhtml_Cache_Renderer_ExtendType extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {

        $expire = urlencode($row->getData('expire_raw'));
        $value = $expire - time();
        $handle = urlencode($row->getData('key'));
        $created = urlencode($row->getData('created'));
        $input = "<input class='ttl-input' type=text name=extend value=" . $value . " />";

        $change_handle = 'adminhtml/remotely/changeTTL/';
        $link = Mage::helper('adminhtml')->getUrl($change_handle, array('created' => $created, 'old_ttl' => $value, 'cache_handle' => $handle));

        $btn = "<a class='send-ttl' onclick='sendTTL(this);' style='margin-left: 1em;' href=" . $link . ">Change</a>";

        return $input . $btn;
    }
}