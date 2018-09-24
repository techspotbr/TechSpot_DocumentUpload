<?php
namespace Techspot\DocumentUpload\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Techspot\DocumentUpload\Model\DocumentsFactory;

class Documents  extends \Magento\Framework\View\Element\Template implements TabInterface
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    protected $_documentsFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
 
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        DocumentsFactory $documentsFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_documentsFactory = $documentsFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Documents');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Documents');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }
 
    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }
    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }
    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('docupload/*/documents', ['_current' => true]);
    }
    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }

    public function getCustomerDocuments()
    {
        $documentModel = $this->_documentsFactory->create();
        $collection = $documentModel->getCollection()
            ->addFieldToFilter('customer_id', ['eq' => $this->getCustomerId()]);
        return $collection;
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