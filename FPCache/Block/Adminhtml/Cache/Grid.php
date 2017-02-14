<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/19/16
 * Time: 2:57 PM
 */

class MagentoRemotely_FPCache_Block_Adminhtml_Cache_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct() {
        parent::__construct();
        $this->setId('cache_row_grid');
        $this->_filterVisibility = false;
        $this->_pagerVisibility = true;
    }

    /**
     * @return array
     */
    protected function _getRowsObject() {
        $objects = array();
        $pages = $this->getCaches();
        if ( $pages ) {
            $i = 0;
            foreach($pages as $page) {
                $i++;
                $handle = $page['tags']['handle'];
//                echo $handle;
                $objects[$page['key']] = new Varien_Object(
                    array(
                        'cache_row_grid' => $i,
                        'key'         => $page['key'],
                        'uri'         => $handle,
                        'handler'     => $handle,
                        'created_at'  => date('Y/m/d H:i:s',$page['mtime']),
                        'expire'      => date('Y/m/d H:i:s',$page['expire']),
                        'expire_raw'  => $page['expire'],
                        'created'     => $page['mtime'],
                        //                      'expire_raw'     => $page['expire'],
                    )
                );
            }
        }
        return $objects;
    }

    protected function _prepareCollection() {
        $collection = new Varien_Data_Collection();
        foreach ($this->_getRowsObject() as $row) {
            $collection->addItem($row);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('cache_row_grid', array(
                'header'       => 'No.',
                'width'        => '20',
                'align'        => 'left',
                'index'        => 'cache_row_grid',
                'sortable'     => false,
            )
        );
        $this->addColumn('uri', array(
                'header'       => 'Handle Name',
                'width'        => '170',
                'align'        => 'left',
                'index'        => 'uri',
                'sortable'     => false,
                'renderer'     => 'MagentoRemotely_FPCache_Block_Adminhtml_Cache_Renderer_HandlerType',
            )
        );
        $this->addColumn('content', array(
                'header'       => 'Content',
                'width'        => '30',
                'sortable'     => false,
                'type'         => 'text',
                'filter'       => false,
                'renderer'     => 'MagentoRemotely_FPCache_Block_Adminhtml_Cache_Renderer_PageType',
            )
        );
        $this->addColumn('created_at', array(
                'header'       => 'Created At',
                'width'        => '100',
                'align'        => 'left',
                'index'        => 'created_at',
                'filter'       => false,
                'sortable'     => false,
            )
        );
        $this->addColumn('expire', array(
                'header'       => 'Expire At',
                'width'        => '100',
                'align'        => 'left',
                'index'        => 'expire',
                'filter'       => false,
                'sortable'     => false,
            )
        );
        $this->addColumn('expire_row', array(
                'header'       => 'TTL',
                'width'        => '100',
                'sortable'     => false,
                'type'         => 'text',
                'filter'       => false,
                'renderer'       => 'MagentoRemotely_FPCache_Block_Adminhtml_Cache_Renderer_ExtendType',
            )
        );
        $this->addColumn('refresh', array(
                'header'       => 'Refresh',
                'type'         => 'text',
                'width'        => '30',
                'sortable'     => false,
                'filter'       => false,
                'renderer'     => 'MagentoRemotely_FPCache_Block_Adminhtml_Cache_Renderer_Type'
            )
        );
        $this->addColumn('purge', array(
                'header'       => 'Purge',
                'type'         => 'text',
                'width'        => '30',
                'sortable'     => false,
                'filter'       => false,
                'renderer'     => 'MagentoRemotely_FPCache_Block_Adminhtml_Cache_Renderer_PurgeType'
            )
        );
        return parent::_prepareColumns();
    }

    public function getCaches() {
        $caches = Mage::helper('magentoremotely_fpcache/update')->getCacheMetas();

        return $caches;
    }
}