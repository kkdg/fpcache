<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/2/16
 * Time: 8:56 AM
 */

class MagentoRemotely_FPCache_Model_Cache extends Mage_Core_Model_Cache
{

    const CACHE_TAG = 'NRS_FPC';
    const DEFAULT_LIFETIME  = 7200;

    /**
     * Default options for default backend
     *
     * @var array
     */
    protected $_defaultBackendOptions = array(
        'hashed_directory_level'    => 3,
        'hashed_directory_perm'    => 0777,
        'file_name_prefix'          => 'degi_fpc',
    );

    public function __construct()
    {
        $node = Mage::getConfig()->getNode('global/cache');
        $options = array();
        if($node) {
            $options = $node->asArray();
        }
        parent::__construct($options);
    }
}