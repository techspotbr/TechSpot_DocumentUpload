<?php
 
namespace Techspot\DocumentUpload\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Techspot\Brcustomer\Model\Config\Source\Legaltype;
class Data extends AbstractHelper
{
    const DOCUPLOAD_CONFIG_ENABLE_PATH = 'documentupload/config/enable';
    const DOCUPLOAD_CONFIG_REDIRECT_PATH = 'documentupload/config/redirect';
    const DOCUPLOAD_CONFIG_ENABLE_LOGIN_CERTIFICATE = 'documentupload/config/enable_login_certificate';
    const DOCUPLOAD_CONFIG_CMS_BLOCK_CONTRACT = 'documentupload/config/cms_block_contract';


    /**
     * Check if module config is enable in admin
     * 
     * @param ScopeConfigInterface $scope
     * 
     * @return bool
     */
    public function isEnable($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $enable = $this->scopeConfig->getValue(self::DOCUPLOAD_CONFIG_ENABLE_PATH, $scope);

        if($enable){
            return true;
        }
        return false;
    }

    /**
     * Get cms block contract identifier
     * 
     * @param ScopeConfigInterface $scope
     * 
     * @return string | bool
     */
    public function getContractIdentifier($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $enable = $this->scopeConfig->getValue(self::DOCUPLOAD_CONFIG_CMS_BLOCK_CONTRACT, $scope);

        if($enable){
            return $enable;
        }
        return false;
    }

    /**
     * Check if can redirect do document upload page after login
     * 
     * @param ScopeConfigInterface $scope
     * 
     * @return bool
     */
    public function redirectAfterLogin($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $redirect = $this->scopeConfig->getValue(self::DOCUPLOAD_CONFIG_REDIRECT_PATH, $scope);

        if($redirect){
            return true;
        }
        return false;
    }

    /**
     * Check if can login with certificate
     * 
     * @param ScopeConfigInterface $scope
     * 
     * @return bool
     */
    public function canLoginWithCertificate($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $enableLoginCert = $this->scopeConfig->getValue(self::DOCUPLOAD_CONFIG_ENABLE_LOGIN_CERTIFICATE, $scope);

        if($enableLoginCert){
            return true;
        }
        return false;
    }

    protected function getFormattedName($unformattedName)
    {
        $pos = strpos($unformattedName, ' ');
        $customerName = array();
        if($pos !== FALSE){
            $customerName['Firstname'] = substr($unformattedName, 0, $pos);
            $customerName['Lastname'] = substr($unformattedName, $pos+1, strlen($unformattedName));
        }

        return $customerName;
    }

    public function getFirstname($unformattedName)
    {
        list ($fullName, $taxvat) = explode(":", $unformattedName);
        $customerName = $this->getFormattedName($fullName);

        return $customerName['Firstname'];
    }

    public function getLastname($unformattedName)
    {
        list ($fullName, $taxvat) = explode(":", $unformattedName);
        $customerName = $this->getFormattedName($fullName);

        return $customerName['Lastname'];
    }

    public function getTaxvat($unformattedName)
    {
        list ($fullName, $taxvat) = explode(":", $unformattedName);

        return $taxvat;
    }

    public function getLegalType($taxvat)
    {
        // Legal Type: PF (Individuals)
		if ( strlen( $taxvat ) === 11 ) {
			return Legaltype::LEGAL_TYPE_PHYSICAL_PERSON;
		} 
		// Legal Type: PJ (Companies)
		elseif ( strlen( $taxvat ) === 14 ) {
			return Legaltype::LEGAL_TYPE_LEGAL_PERSON;
		} 
		// Legal Type: Invalid
		else {
			return false;
		}
    }
}