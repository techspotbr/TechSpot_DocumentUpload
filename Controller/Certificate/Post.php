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

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Post extends \Magento\Framework\App\Action\Action
{
    const REDIRECT_PAGE_DOC = 'docupload/certificate/index';

    protected $customerSession;
    protected $_objectManager;
    protected $_storeManager;
    protected $_filesystem;
    protected $_fileUploaderFactory;
    
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context, 
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory) 
    {
        $this->customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($context);    
    }

    /**
     * Show Document Upload Page
     *
     * @return void
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
       
        if(!$post){
            throw new StateException(__('Problem in POST - please contact the site administrator.'));
        }

        $files = $this->getRequest()->getFiles();
        $pathurl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'docupload/';
        $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $mediapath = $this->_mediaBaseDirectory = rtrim($mediaDir, '/');

        $model = $this->_objectManager->create('Techspot\DocumentUpload\Model\Certificate');

        $model->setData('customer_id', $this->customerSession->getId());

        $row  = $model->load($this->customerSession->getId(), 'customer_id');
        
        $createNew = false;
        if (!$row->getId()) {
            $createNew = true;
        }
    
        $currentTarget = $files->{'certificate_file'}['name'];

        if(null !==  $currentTarget && $currentTarget !== ''){
            // Save File
            $current = $files->{'certificate_file'};
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'certificate_file']);
            $uploader->setAllowedExtensions(['crt', 'pfx', 'p12']);
            $uploader->setAllowRenameFiles(true);
            $path = $mediapath . '/docupload/certificate/';
            $result = $uploader->save($path);

             // Save Data
             if($createNew){
                $model->setData('certificate_type', 'Certificado');
                $model->setData('certificate_file', $currentTarget);
                $model->setData('certificate_pin', $post['certificate_pin']);
            } else {
                $row->setData('certificate_type', 'Certificado');
                $row->setData('certificate_file', $currentTarget);
                $row->setData('certificate_pin', $post['certificate_pin']);
            }
        }

        $currenttime = date('Y-m-d H:i:s');
        if($createNew){
            $model->setData('created_at', $currenttime);
            $model->save();
        } else {
            $row->setData('created_at', $currenttime);
            $row->save();
        }

        $this->messageManager->addSuccess(__('The file of Digital Certificate have been uploaded successfully.'));
        $this->getRedirectPage($post['customer_type']);
    }

    /**
     * Redirect Page
     * 
     * @param int $customerType
     * 
     * @return string Redirect Page
     */
    public function getRedirectPage($customerType)
    {
        $redirectPage = self::REDIRECT_PAGE_DOC;
        return $this->_redirect($redirectPage);
    }
}