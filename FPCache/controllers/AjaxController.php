<?php
/**
 * Created by PhpStorm.
 * User: dex
 * Date: 9/7/16
 * Time: 10:46 AM
 */

class MagentoRemotely_FPCache_AjaxController extends Mage_Core_Controller_Front_Action
{

    protected function _checkNode() {

    }

    public function personalAction() {

        $params = $this->getRequest()->getParam('degi-data');

        $request = $this->getRequest()->getParam('degi-request');

        $helper = Mage::helper('magentoremotely_fpcache');
        $this->loadLayout();
//
        Mage::log($request,null,'handle',true);
        $data = $helper->runAppBackground($request,$params);
//
//        $data = '';
//        $data['a'] =1;

        $this->getResponse()
             ->clearHeaders()
             ->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($data));
    }

    public function drawAction() {

        $handle = 'cms_index_index';
        $layout = Mage::app()->getLayout();
        $update = $layout->getUpdate()
//            ->addHandle($handle);
            ->addHandle($handle)
            ->load(array($handle));
//        var_dump($update);


            /*   ->addHandle('default')
               ->addHandle($handle)
            ->load();

        $layout->generateXml()
            ->generateBlocks();
        $this->loadLayout();
        $this->renderLayout() */
    }

    public function aAction() {
        echo Mage::helper('core')->isModuleEnabled('Bubble_Elasticsearch');

    }
}