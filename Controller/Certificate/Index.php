<?php
/**
 *
 * The Tech Spot DocumentUpload Module for Magento 2 enable upload documents to customer account (In Brazil for PF- Individuals and PJ- Legal Entity. 
 * This require techspot/brcustomer module.
 * Copyright (C) 2018  Tech Spot 
 * 
 * This file is part of Techspot/DocumentUpload.
 */
namespace Techspot\DocumentUpload\Controller\Certificate;

use Techspot\DocumentUpload\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Customer  
     **/
    protected $_customer;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Techspot\DocumentUpload\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Framework\App\Action\Context         $context
     * @param \Magento\Store\Model\StoreManagerInterface    $storeManager
     * @param \Magento\Customer\Model\CustomerFactory       $customerFactory
     * @param \Magento\Customer\Model\Customer              $customer
     * @param \Magento\Customer\Model\Session               $customerSession
     * @param  \Techspot\DocumentUpload\Helper\Data         $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Session $customerSession,
        \Techspot\DocumentUpload\Helper\Data $helper
    ) {
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;
        $this->_customer        = $customer;
        $this->_customerSession = $customerSession;
        $this->helper           = $helper;
        parent::__construct($context);
    }

    /**
     * Show Document Upload Page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('certificate');
        $this->_view->renderLayout();
    }
}