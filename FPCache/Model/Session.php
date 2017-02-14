<?php
/**
 *
 */


class MagentoRemotely_FPCache_Model_Session extends Zend_Session_Namespace
{
    const BLOCK_POSTFIX = 'Block';

    protected $_store = array();

    public function __construct()
    {
        parent::__construct('fpcache');
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getBlockHtml($key)
    {

        return $this->$key;
    }

    /**
     * @param $key
     * @param $html
     * @return $this
     */
    public function setBlockHtml($key, $html)
    {
        $key .= self::BLOCK_POSTFIX;
        $this->$key = $html;
        return $this;
    }
}
