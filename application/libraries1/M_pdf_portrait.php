<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
include_once APPPATH.'/third_party/mpdf/mpdf.php';
 
class M_pdf_portrait {
 
    public $param;
    public $pdf;

  /*
  $mpdf = new mPDF('',    // mode - default ''
 '',    // format - A4, for example, default ''
 0,     // font size - default 0
 '',    // default font family
 15,    // margin_left
 15,    // margin right
 16,     // margin top
 16,    // margin bottom
 9,     // margin header
 9,     // margin footer
 'L');  // L - landscape, P - portrait
 */
 
    //public function __construct($param = '"","A4","0","",5,5,10,10,6,3')
    public function __construct($param = '"en-GB-x","A4","0","",10,10,10,10,6,3,"L"')
    {
        //$this->param =$param;
        $this->pdf = new mPDF('',    
                                 'A4-P',   
                                 0,     
                                 '', 
                                 10,  
                                 10,   
                                 10,    
                                 10,   
                                 5,    
                                 5);
       //$this->pdf = new mPDF('C', 'A4-L');
    }
}