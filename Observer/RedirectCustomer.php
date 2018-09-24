<?php
/**
 *
 * The Tech Spot DocumentUpload Module for Magento 2 enable upload documents to customer account (In Brazil for PF- Individuals and PJ- Legal Entity. 
 * This require techspot/brcustomer module.
 * Copyright (C) 2018  Tech Spot 
 * 
 * This file is part of Techspot/DocumentUpload.
 */
namespace Techspot\DocumentUpload\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class RedirectCustomer implements ObserverInterface
{
    const CUSTOMER_GROUP_ID = 2;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Uri Validator
     *
     * @var \Zend\Validator\Uri
     */
    protected $uri;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $_responseFactory;

    /**
     * [__construct ]
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
     * @param \Zend\Validator\Uri $uri
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface 
     * @param \Magento\Framework\UrlInterface           $urlInterface
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
    */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Zend\Validator\Uri $uri,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\ResponseFactory $responseFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->uri = $uri;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_urlInterface = $urlInterface;
        $this->_responseFactory = $responseFactory;

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        
        if($customer->getId()){
            $customerLegalType = $customer->getData('legal_type');

            $redirectPage = false;
            if($customerLegalType === '1'){
                $redirectPage = \Techspot\DocumentUpload\Controller\Index\Post::REDIRECT_PAGE_PF;
            } else if($customerLegalType === '2'){
                $redirectPage = \Techspot\DocumentUpload\Controller\Index\Post::REDIRECT_PAGE_PJ;
            }

            $url = $this->_urlInterface->getUrl($redirectPage); 
            $this->_responseFactory->create()->setRedirect($url)->sendResponse();
            exit();
        }
    }
}