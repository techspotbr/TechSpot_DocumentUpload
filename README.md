# Techspot - DocumentUpload - Magento 2 Module

The Tech Spot DocumentUpload Module for Magento 2 enable upload documents to customer account (In Brazil for PF- Individuals and PJ- Legal Entity. This require techspot/brcustomer module.


### Install

Installe via composer:

```
cd <your Magento install dir>
composer require techspot/documentupload
php bin/magento module:enable --clear-static-content Techspot_DocumentUpload
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy //ou php bin/magento setup:static-content:deploy pt_BR
```

## Authors

* **Bruno Monteiro** - *Initial work* - [TechSpot](https://github.com/techspotbr)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

