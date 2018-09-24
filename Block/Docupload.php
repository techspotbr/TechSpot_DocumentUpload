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
use Techspot\DocumentUpload\Model\DocumentsFactory;

/**
 * Main Document Upload Form Block
 */
class Docupload extends Template
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    protected $customerSession;
    protected $_objectManager;
    protected $_documentsFactory;

    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        DocumentsFactory $documentsFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        Template\Context $context, array $data = [])
    {
        $this->_storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        $this->_documentsFactory = $documentsFactory;
        $this->_urlInterface = $urlInterface;
        $this->_responseFactory = $responseFactory;
        
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    public function getMyDocuments()
    {
        $documentModel = $this->_documentsFactory->create();
        $collection = $documentModel->getCollection()
            ->addFieldToFilter('customer_id', ['eq' => $this->customerSession->getId()]);
        return $collection;
    }

    public function getDocuploadPageLink()
    {
        $customerLegalType = $this->customerSession->getCustomer()->getData('legal_type');

        $redirectPage = false;
        if($customerLegalType === '1'){
            $redirectPage = \Techspot\DocumentUpload\Controller\Index\Post::REDIRECT_PAGE_PF;
        } else if($customerLegalType === '2'){
            $redirectPage = \Techspot\DocumentUpload\Controller\Index\Post::REDIRECT_PAGE_PJ;
        }

        $url = $this->_urlInterface->getUrl($redirectPage); 
        return $url;
    }

    /**
     * Retrive thumbnail URL
     *
     * @return string
     */
    public function getThumbnailUrl($thumbnail)
    {
        $url = false;
        if ($thumbnail) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) .'docupload/'.$thumbnail;
        } else {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) .'images/user.png';
        }
        return $url;
    }
}