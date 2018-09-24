<?php
/**
 *
 * The Tech Spot DocumentUpload Module for Magento 2 enable upload documents to customer account (In Brazil for PF- Individuals and PJ- Legal Entity. 
 * This require techspot/brcustomer module.
 * Copyright (C) 2018  Tech Spot 
 * 
 * This file is part of Techspot/DocumentUpload.
 */
namespace Techspot\DocumentUpload\Controller\Index;
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Show Document Upload Page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('docupload');
        $this->_view->renderLayout();
    }
}