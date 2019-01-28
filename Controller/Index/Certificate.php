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

use Techspot\DocumentUpload\Helper\Data;

class Certificate extends \Magento\Framework\App\Action\Action
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
        $this->_view->getLayout()->getBlock('docupload');
        $this->_view->renderLayout();

        echo '<h1>Registo com certificado Digital</h1>';
        
        $sslClientCertificate = $this->getRequest()->getServer();
       
        if($this->hasValidCert($sslClientCertificate)){
            echo 'Seu certificado foi validado com sucesso!';
            if($this->loginWithCertificate($sslClientCertificate)){
                $now = false;
            } else {
                $this->registerWithCertificate($sslClientCertificate);
            }
            //$this->_redirect('docupload/index/index');
            $this->_redirect('docupload/index/contract');
        } else {
            echo 'Não foi possivel validar seu certificado!';
        }
    
        
    }

     /**
     * Determines if the browser provided a valid SSL client certificate
     *
     * @param array $sslClientCertificate
     * 
     * @return boolean True if the client cert is there and is valid
     */
    public function hasValidCert($sslClientCertificate)
    {
        if (!isset($sslClientCertificate['SSL_CLIENT_M_SERIAL'])
            || !isset($sslClientCertificate['SSL_CLIENT_VERIFY'])
            //|| $sslClientCertificate['SSL_CLIENT_VERIFY'] !== 'SUCCESS'
            //|| $sslClientCertificate['SSL_CLIENT_VERIFY'] !== 'GENEROUS'
            || !isset($sslClientCertificate['SSL_CLIENT_I_DN_Email'])
        ) {
            return false;
        }
 
        if ($_SERVER['SSL_CLIENT_V_REMAIN'] <= 0) {
            return false;
        }
 
        return true;
    }

    /**
     * Login customer with certificate data
     * 
     * @param array $sslClientCertificate
     * 
     * @return bool
     * */
    public function loginWithCertificate($sslClientCertificate)
    {
        $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
        $customer = $this->_customer->setWebsiteId($websiteId)
            ->loadByEmail($sslClientCertificate['SSL_CLIENT_S_DN_Email']);

        if($customer){
            $this->_customerSession->setCustomerAsLoggedIn($customer);
            return true;
        }
        return false;
    }

    /**
     * Register customer with certificate data
     * 
     * @param array $sslClientCertificate
     * */
    public function registerWithCertificate($sslClientCertificate)
    {
        
        $firstname  = $this->helper->getFirstname($sslClientCertificate['SSL_CLIENT_S_DN_CN']);
        $lastname   = $this->helper->getLastname($sslClientCertificate['SSL_CLIENT_S_DN_CN']);
        $taxvat     = $this->helper->getTaxvat($sslClientCertificate['SSL_CLIENT_S_DN_CN']);
        
        $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
        $customer   = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->setEmail($sslClientCertificate['SSL_CLIENT_S_DN_Email']); 
        $customer->setFirstname($firstname);
        $customer->setLastname($lastname);
        $customer->setTaxvat($taxvat);

        // Custom Field: Techspot BR Customer Module
        $legalType  = $this->helper->getLegalType($taxvat);
        $customer->setLegalType($legalType);

        $customer->setPassword($taxvat);

        // Save data
        $customer->save();
        $customer->sendNewAccountEmail();
        
    }
}