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

use Magento\Framework\App\ObjectManager;
/**
 * Main Document Upload Form Block
 */
class Contract extends \Magento\Cms\Block\Block
{
    protected $customerSession;

    protected $countryFactory;

    /**
     * Get logo image URL
     *
     * @return string
     */
    public function getLogoSrc()
    {    
        $logo = ObjectManager::getInstance()->get('\Magento\Theme\Block\Html\Header\Logo');
        return $logo->getLogoSrc();
    }

    protected function getDefaultData()
    {
        if (!isset($this->customerSession)) {
            $this->customerSession = ObjectManager::getInstance()->get('\Magento\Customer\Model\Session');
        }

        $customer = $this->customerSession->getCustomer();
        if ($customer) {
            $billingAddress = $customer->getDefaultBillingAddress();
            if ($billingAddress) {
                $this->setData('customer_name', $customer->getName());
                $this->setData('customer_email', $customer->getEmail());
                $this->setData('customer_created_at', $customer->getCreatedAt());
                
                $customerLegalType = $customer->getData('legal_type');
                
                if($customerLegalType == 1){
                    $this->setData('customer_legal_type_code', 'CPF');
                    $this->setData('customer_rg', $customer->getData('document'));
                    $this->setData('customer_rg_emissor', $customer->getData('document_emitter'));
                } else if($customerLegalType === '2'){
                    $this->setData('customer_legal_type_code', 'CNPJ');
                    $this->setData('customer_state_inscription', $customer->getData('state_inscription'));
                    $this->setData('customer_county_inscription', $customer->getData('county_inscription'));
                }

                $this->setData('customer_taxvat', $customer->getTaxvat());
                
                $this->setData('customer_address_street1', $billingAddress->getStreet()[0]);
                $this->setData('customer_address_street2', $billingAddress->getStreet()[1]);
                $this->setData('customer_address_street3', $billingAddress->getStreet()[2]);
                $this->setData('customer_address_street4', $billingAddress->getStreet()[3]);
                $this->setData('customer_address_postcode', $billingAddress->getPostcode());
                $this->setData('customer_address_city', $billingAddress->getCity());
                $this->setData('customer_address_state', $billingAddress->getState());

                $this->setData('customer_address_country', $billingAddress->getCountryModel()->getName());

                return $this->getData();
            }
        }
        return false;
    }

    protected function getBlockContent()
    {
        //$blockId = $this->getBlockId();
        $blockId = 'contrato-participacao-leilao';
        $html = '';
        if ($blockId) {
            $storeId = $this->_storeManager->getStore()->getId();
            $block = $this->_blockFactory->create();
            $block->setStoreId($storeId)->load($blockId);
            if ($block->isActive()) {
                if (!isset($this->_filter)) {
                    $this->_filter = ObjectManager::getInstance()->get('\Techspot\DocumentUpload\Model\Template\Filter');
                }
                
                $defaultData = $this->getDefaultData();
                if($defaultData){
                    $this->_filter->initDefaultFilters();
                    $this->_filter->setVariables($this->getDefaultData());
                    
                    $html = $this->_filter->setStoreId($storeId)->filter($block->getContent());
                    
                } else {
                    $addressNotFound = "<p>".__("To view/print the Contract Participation in Judicial Auction, Extrajudicial Auction and/or Direct Online Sale, it is necessary to register your legal address.")."</p>";
                    $addressNotFound.= "<h3>".__("<a href='%1'> Click here </a> to register your address.", $this->getUrl('customer/address/new'))."</h3>";
                    $addressNotFound.= "<p>".__("Then click on the CONTRACT link in the side menu to view and print your contract.")."</p>";
                    $html = $this->_filter->setStoreId($storeId)->filter($addressNotFound);
                }
            }
        }
        return $html;
    }

    /**
     * Prepare Content to PDF
     * @return string
     */
    public function _toPdf()
    {
        return $this->getBlockContent();
    }

    /**
     * Prepare Content HTML
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        $html.= '<div class="contract-area" style="height: 300px; overflow-y: scroll; border: 1px solid #ccc;">';
        $html.= '"'.$this->getBlockContent().'"';
        $html.= '</div>';
        $html.= '<p><input type="checkbox" style="float:left" name="agree-contract"/>'.__('I read, and agree to the terms of the contract.').'</p>';

        $signUrl = $this->getUrl('docupload/certificate/sign', ['_current' => true, '_use_rewrite' => true]);
        $html.= '<button><a href="'.$signUrl.'"><span>'.__('Sign with Digital Certificate').'</span></a></button>';
        return $html;
    }

    protected function getBlock($blockId)
    {
        $objectManager = ObjectManager::getInstance();
        $block = $objectManager->get('\Techspot\DocumentUpload\Block\Contract');
        $block->setBlockId('contrato-participacao-leilao');
        return $block->toHtml();
    }
}