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

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Post extends \Magento\Framework\App\Action\Action
{
    const REDIRECT_PAGE_PF = 'docupload/index/docpf';
    const REDIRECT_PAGE_PJ = 'docupload/index/docpj';

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
        $pathurl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'customer/docupload/';
        $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $mediapath = $this->_mediaBaseDirectory = rtrim($mediaDir, '/');
        
        $model = $this->_objectManager->create('Techspot\DocumentUpload\Model\Documents');
        
        $model->setData('customer_id', $this->customerSession->getId());

        $row  = $model->load(1);
        $createNew = false;
        if (!$row->getId()) {
            $createNew = true;
        }

        for($i = 1; $i <= 6; $i++){
            $currentType = $post['document'.$i.'_type'];
            $currentTarget = $files->{'document'.$i.'_file'}['name'];

            if(null !==  $currentTarget && $currentTarget !== ''){
                // Save File
                $current = $files->{'document'.$i.'_file'};
                $uploader = $this->_fileUploaderFactory->create(['fileId' => 'document'.$i.'_file']);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'pdf']);
                $uploader->setAllowRenameFiles(true);
                $path = $mediapath . '/customer/docupload/';
                $result = $uploader->save($path);

                // Save Data
                if($createNew){
                    $model->setData('document'.$i.'_type', $currentType);
                    $model->setData('document'.$i.'_file', $currentTarget);
                } else {
                    $row->setData('document'.$i.'_type', $currentType);
                    $row->setData('document'.$i.'_file', $currentTarget);
                }
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

        $this->getRedirectPage($post['customer_type']);
        $this->messageManager->addSuccess(__('The files have been uploaded successfully.'));
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
        $redirectPage = false;
        if($customerType === '1'){
            $redirectPage = self::REDIRECT_PAGE_PF;
        } else if($customerType === '2'){
            $redirectPage = self::REDIRECT_PAGE_PJ;
        }
        return $this->_redirect($redirectPage);
    }
}