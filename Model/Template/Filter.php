<?php
/**
 * The Tech Spot DocumentUpload Module for Magento 2 enable upload documents to customer account (In Brazil for PF- Individuals and PJ- Legal Entity. 
 * This require techspot/brcustomer module.
 * Copyright (C) 2018  Tech Spot 
 * 
 * Techspot/Brcustomer is free software: you can redistribute it and/or modify
 * it under the terms of the MTI License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Techspot\DocumentUpload\Model\Template;

use \Magento\Framework\App\ObjectManager;

class Filter extends \Magento\Email\Model\Template\Filter
{
    // Basic initialization of preset filters 
    public function initDefaultFilters()
    {
        $this->_modifiers = [
            'value' => [$this, 'value'],
            'dateFormat' => [$this, 'dateFormat'],
            'priceFormat' => [$this, 'priceFormat']
        ];
        return $this;
    }
 
    // Flexible abilities for filter usage, and ability of rapid adding
    public function addModifier($name, $data = ['this', 'value'])
    {
        if ($data[0] == 'this') {
            $data[0] = $this;
        }
 
        $this->_modifiers[$name] = $data;
    }
 
    public function value($value)
    {
        return $value;
    }
 
    // Random date format
    public function dateFormat($value, $format)
    {
        $time = strtotime($value);
        return date($format, $time);
    }
 
    // Price format
    public function priceFormat($value, $clean = true)
    {
        $priceFilter = ObjectManager::getInstance()
            ->create('\Magento\Framework\Pricing\PriceCurrencyInterface');
        $value = $priceFilter->format($value, false);
        if ($clean) {
            $value = str_replace($priceFilter->getCurrencySymbol(), '', $value);
        }
        
        return $value;
    }
}