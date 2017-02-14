<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/19/16
 * Time: 4:06 PM
 */

class MagentoRemotely_FPCache_Block_Adminhtml_Cache extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        if ( ! Mage::app()->useCache()['fpcache'] ) return false;

        $this->_blockGroup = 'magentoremotely_fpcache/adminhtml';
        $this->_headerText =  'Fullpage Cache Pages';
        $this->_controller = 'cache';
        parent::__construct();
        $this->_removeButton('add');
    }

    protected function _prepareLayout()
    {
        return Mage_Adminhtml_Block_Widget_Container::_prepareLayout();
    }
}