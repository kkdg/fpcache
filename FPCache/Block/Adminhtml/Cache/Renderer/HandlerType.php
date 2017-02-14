<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/21/16
 * Time: 7:47 AM
 */

class MagentoRemotely_FPCache_Block_Adminhtml_Cache_Renderer_HandlerType extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
//        var_dump($row);
        $handle = $row->getData('uri');
        $key = $row->getData('key');

//        return $key;
        $themes = Mage::helper('magentoremotely_fpcache/update')->getThemes($key);

        $tag = '';
        foreach( $themes as $k => $theme ) {
            if($theme != '') {
                $tag .= "<span style='float:right; margin-right:1em;'><span style='font-weight:700; color: orangered'>" . strtoupper(substr($k,0,1)) . '</span> : ' . $theme . "</span>";

            }
        }

        return $handle . $tag;
    }
}