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
    const CONTRACT_CMS_BLOCK_IDENTIFIER = 'contrato-participacao-leilao';

    /**
     * @var \Magento\Customer\Model\Session
     **/
    protected $customerSession;

    /**
     * @var \Techspot\DocumentUpload\Model\Template\Filter
     */
    protected $_filter;

  
    
    /**
     * Get Store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

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

    /**
     * Return the Customer Default Data or false
     * 
     * @return object|false
     **/
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
                
                if(null !== $customerLegalType || null !== $customer->getData('taxvat')){
                    if($customerLegalType == \Techspot\Brcustomer\Model\Config\Source\Legaltype::LEGAL_TYPE_PHYSICAL_PERSON){
                        $this->setData('customer_legal_type_code', 'CPF');
                        $this->setData('customer_rg', $customer->getData('document'));
                        $this->setData('customer_rg_emissor', $customer->getData('document_emitter'));
                    } else if($customerLegalType === \Techspot\Brcustomer\Model\Config\Source\Legaltype::LEGAL_TYPE_LEGAL_PERSON){
                        $this->setData('customer_legal_type_code', 'CNPJ');
                        $this->setData('customer_state_inscription', $customer->getData('state_inscription'));
                        $this->setData('customer_county_inscription', $customer->getData('county_inscription'));
                    }
                } else {
                    return false;
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
            return false;
        }
        return false;
    }

    /**
     * Return the cms Block content 
     * 
     * @return string
     **/
    protected function getBlockContent()
    {
        $blockId = self::CONTRACT_CMS_BLOCK_IDENTIFIER;
        $html = '';
        if ($blockId) {
            $block = $this->_blockFactory->create();
            $block->setStoreId($this->getStoreId())->load($blockId);
            if ($block->isActive()) {
                if (!isset($this->_filter)) {
                    $this->_filter = ObjectManager::getInstance()->get('\Techspot\DocumentUpload\Model\Template\Filter');
                }
                
                $this->_filter->initDefaultFilters();
                $this->_filter->setVariables($this->getDefaultData());
                
                $html = $this->_filter->setStoreId($this->getStoreId())->filter($block->getContent());
            }
        }
        return $html;
    }

    /**
     * Prepare Content to PDF
     * 
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

        if(!$this->getDefaultData()){
            $addressNotFound = "<div><p>".__("To view/print the Contract Participation in Judicial Auction, Extrajudicial Auction and/or Direct Online Sale, it is necessary to register your legal address.")."</p>";
            $addressNotFound.= "<h3>".__("<a href='%1'> Click here </a> to register your address.", $this->getUrl('customer/address/new'))."</h3>";
            $addressNotFound.= "<p>".__("Then click on the CONTRACT link in the side menu to view and print your contract.")."</p></div>";
            $html.= $addressNotFound;
        } else {
            $html.= '<div class="contract-area" style="height: 300px; overflow-y: scroll; border: 1px solid #ccc;">';
            $html.= $this->getBlockContent();
            $html.= '</div>';
            $html.= '<p><input type="checkbox" style="float:left" name="agree-contract"/>'.__('I read, and agree to the terms of the contract.').'</p>';

            $signUrl = $this->getUrl('docupload/certificate/sign', ['_current' => true, '_use_rewrite' => true]);
            $html.= '<button><a href="'.$signUrl.'"><span>'.__('Sign with Digital Certificate').'</span></a></button>';
        }
        return $html;
    }

}