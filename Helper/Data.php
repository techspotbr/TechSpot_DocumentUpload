<?php
 
namespace Techspot\DocumentUpload\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Techspot\Brcustomer\Model\Config\Source\Legaltype;
class Data extends AbstractHelper
{
    protected function getFormattedName($unformattedName){
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