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

use Techspot\DocumentUpload\Helper\Data;

class Sign extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;
    
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;


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
        \Techspot\DocumentUpload\Helper\Data $helper,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem\DirectoryList $dir
    ) {
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;
        $this->_customer        = $customer;
        $this->_customerSession = $customerSession;
        $this->helper           = $helper;
        $this->fileFactory      = $fileFactory;
        $this->_dir             = $dir;
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
        $block = $this->_view->getLayout()->getBlock('contract');
        $text = $block->_toPdf();

         // create new PDF document
         $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
         // set document information
         $pdf->SetCreator(PDF_CREATOR);
         $pdf->SetAuthor('VIRTU LEILÕES');
         $pdf->SetTitle('VIRTU LEILÕES');
         $pdf->SetSubject('CONTRATO PARA PARTICIPAÇÃO EM LEILÃO JUDICIAL, EXTRAJUDICIAL E/OU VENDA DIRETA ON-LINE');
         $pdf->SetKeywords('contrato leilão, pdf, virtuleiloes.com.br');
 
         // set default header data
         $logoHeader = $block->getLogoSrc();
         //die($logoHeader);
         $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 052', PDF_HEADER_STRING);
 
         // set header and footer fonts
         $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
         $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
 
         // set default monospaced font
         $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
 
         // set margins
         $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
         $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
         $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
 
         // set auto page breaks
         $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
 
         // set image scale factor
         $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
 
         // set certificate file
         $certificate =  'file://'.realpath($this->_dir->getPath('var').'/certificates/tcpdf.crt');
         $key = 'file://'.realpath($this->_dir->getPath('var').'/certificates/tcpdf.key');
         
 
         // set additional information
         $info = array(
             'Name' => 'TCPDF',
             'Location' => 'Office',
             'Reason' => 'Testing TCPDF',
             'ContactInfo' => 'http://www.tcpdf.org',
             );
         
         // set document signature
         $pdf->setSignature($certificate, $key, 'tcpdfdemo', '', 2, $info);
             
         // set font
         $pdf->SetFont('helvetica', '', 12);
 
         // add a page
         $pdf->AddPage();
 
         // print a line of text
         $text.= 'Documento assinado eletronicamente por XXX, Título, conforme ar. 1, III, "b", da Lei 11.419/2006<br/>.
         Data:';
         $pdf->writeHTML($text, true, 0, true, 0);
 
         //Close and output PDF document
     
         $this->fileFactory->create(
             'example_052.pdf',
             $pdf->Output('example_052.pdf', 'D'),
             \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
             'application/pdf'
         );
         
     }

}