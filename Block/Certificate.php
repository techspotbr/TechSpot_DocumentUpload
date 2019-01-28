<?php
/**
 *
 * The Tech Spot DocumentUpload Module for Magento 2 enable upload documents to customer account (In Brazil for PF- Individuals and PJ- Legal Entity. 
 * This require techspot/brcustomer module.
 * Copyright (C) 2018  Tech Spot 
 * 
 * This file is part of Techspot/DocumentUpload.
 */
namespace Techspot\DocumentUpload\Block;

use Magento\Framework\View\Element\Template;
/**
 * Main Document Certificate Form Block
 */
class Certificate extends Template
{
    protected $customerSession;
    protected $_objectManager;

    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Template\Context $context, 
        array $data = [])
    {
        $this->customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Get the Digital Certificate record
     * 
     * @return \Techspot\DocumentUpload\Model\Certificate
     * */
    public function getCustomerCertificate()
    {
        $model = $this->_objectManager->create('Techspot\DocumentUpload\Model\Certificate');

        $row  = $model->load($this->customerSession->getId(), 'customer_id');

        return $row;
    }

    /**
     * Check if customer has a certificate record
     * 
     * @return bool
     * */
    public function hasValidCertificate()
    {
        $row = $this->getCustomerCertificate();

        if ($row->getId()) {
            return true;
        }
        return false;
    }
}