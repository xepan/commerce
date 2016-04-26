<?php

namespace xepan\commerce;

class page_test extends \Page{
	
	function init(){
		parent::init();

		// $this->add('xepan\commerce\View_QSP')->generatePDF();

		// create new PDF document
		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Rakesh Sinha');
		$pdf->SetTitle('PDF Example');
		$pdf->SetSubject('PDF Subject');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set font
		$pdf->SetFont('dejavusans', '', 10);

		// add a page
		$pdf->AddPage();

		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)


		// create some HTML content
		$html = '
<table id="{$_name}">
  <tbody>
    <tr >
      <td > </td>
      <td > </td>
      <td > </td>
      <td ><a class="new-quotation btn btn-primary"><i class="fa fa-edit fa-lg"></i> New {type} DocType {/}</a></td>
      <td ><a href="#" class="btn btn-primary"><i class="fa fa-mail-forward fa-lg"></i> Send {type} DocType {/}</a></td>
      <td ><a href="#" class="btn btn-primary pull-right xepan-print-qsp"><i class="fa fa-print fa-lg"></i> Print {type} DocType {/}</a></td>
    </tr>
  </tbody>
</table>
<table >
  <tbody>
    <tr>
      <td style="width: 424px; height: 13px; text-align: left;"><span style="font-size: 14px;"><strong>{$contact}{contact_id}{/}</strong></span></td>
      <td style="width: 460.867px; height: 13px;"> </td>
      <td style="width: 198.133px; height: 13px;"> </td>
    </tr>
    <tr style="height: 13px;">
      <td style="width: 424px; height: 13px;">{$billing_address},</td>
      <td style="width: 460.867px; height: 13px;">{$shipping_address},</td>
      <td style="width: 198.133px; height: 13px; text-align: left;"><span style="font-size: 14px;"><strong>{type} DocType {/} No:</strong></span></td>
    </tr>
    <tr style="height: 13px;">
      <td style="width: 424px; height: 13px;">{$billing_city},</td>
      <td style="width: 460.867px; height: 13px;">{$shipping_city},</td>
      <td style="width: 198.133px; height: 13px; text-align: left;"> {$document_no}</td>
    </tr>
    <tr style="height: 13px;">
      <td style="width: 424px; height: 13px;">{$billing_state},</td>
      <td style="width: 460.867px; height: 13px;">{$shipping_state},</td>
      <td style="width: 198.133px; height: 13px; text-align: left;"><span style="font-size: 12px;"><strong>Created At :</strong></span></td>
    </tr>
    <tr style="height: 13px;">
      <td style="width: 424px; height: 13px;">{$billing_pincode}.</td>
      <td style="width: 460.867px; height: 13px;">{$shipping_pincode}.</td>
      <td style="width: 198.133px; height: 13px; text-align: left;"> {$created_at}</td>
    </tr>
    <tr style="height: 13px;">
      <td style="width: 424px; height: 13px;">{$billing_country}.</td>
      <td style="width: 460.867px; height: 13px;">{$shipping_country}.</td>
      <td style="width: 198.133px; height: 13px; text-align: left;"><span style="font-size: 12px;"><strong>Updated At:</strong></span></td>
    </tr>
    <tr style="height: 13px;">
      <td style="width: 424px; height: 13px;">{$billing_contact} {$billing_email}</td>
      <td style="width: 460.867px; height: 13px;"> {$shipping_contact} {$shipping_email}</td>
      <td style="width: 198.133px; height: 13px; text-align: left;">{$due_date}</td>
    </tr>
  </tbody>
</table>{$item_info}
<table style="height: 138px; width: 1104px;">
  <tbody>
    <tr style="height: 12px;">
      <td style="width: 198px; height: 12px;"><span style="font-size: 14px;"><strong> Narration :</strong></span></td>
      <td style="width: 270.8px; height: 12px;"> </td>
      <td style="width: 182.2px; height: 12px; text-align: left;"><span style="font-size: 18px;">Gross Amount</span></td>
      <td style="width: 279px; height: 12px; text-align: right;">{gross_amount} $ 00000000 {/}</td>
    </tr>
    <tr style="height: 23px;">
      <td style="width: 198px; height: 23px; text-align: left;" colspan="2" rowspan="2">{narration} This Item to be sold and replaced and Reproduction and wastage. {/}  </td>
      <td style="width: 182.2px; height: 23px; text-align: left;"><span style="font-size: 18px;">Discount</span></td>
      <td style="width: 279px; height: 23px; text-align: right;">{discount_amount} $ 00000000 {/}</td>
    </tr>
    <tr style="height: 11.8px;">
      <td style="width: 182.2px; height: 11.8px; text-align: left;"><strong><span style="font-size: 24px;">GRAND TOTAL</span></strong></td>
      <td style="width: 279px; height: 11.8px; text-align: right;">{net_amount} $ 0000000{/}</td>
    </tr>
  </tbody>
</table>
<table style="height: 167px; width: 1093px;">
  <tbody>
    <tr style="height: 157px;">
      <td style="width: 786px; height: 157px;">
        <table style="height: 85px;" width="786">
          <tbody>
            <tr style="height: 16.5px;">
              <td style="width: 776px; height: 16.5px;"><span style="font-size: 14px;"><strong>Terms & Conditions:</strong></span></td>
            </tr>
            <tr style="height: 13px;">
              <td style="width: 776px; height: 13px;">
                 
                {$tnc_id}
                {$tnc_text}  
              </td>
            </tr>
            <tr style="height: 16px;">
              <td style="width: 776px; height: 16px;"><span style="font-size: 14px;"><strong>Item Specific:</strong></span></td>
            </tr>
            <tr style="height: 13px;">
              <td style="width: 776px; height: 13px;">
                 
                {terms_and_conditions}
                {rows}{row}
                {terms_and_conditions}Not Specified{/} 
                {/}{/}
                {/}  
              </td>
            </tr>
          </tbody>
        </table>
      </td>
      <td style="width: 304px; height: 157px;">
        <table style="height: 108px;" width="304">
          <tbody>
            <tr style="height: 16px;">
              <td style="width: 294px; background-color: #2a3deb; text-align: center; height: 16px;"><span style="font-size: 14px;"><strong>Vat Sum</strong></span></td>
            </tr>
            <tr style="height: 13px;">
              <td style="width: 294px; background-color: #2a3deb; text-align: center; height: 13px;">
                 
                {common_vat}
                <div>{$id} = {$name}</div>{/}
              </td>
            </tr>
            <tr style="height: 16px;">
              <td style="width: 294px; background-color: #2a3deb; text-align: center; height: 16px;"><strong><span style="font-size: 14px;"> {$currency} {$currency_id} @</span></strong></td>
            </tr>
            <tr style="height: 13px;">
              <td style="width: 294px; background-color: #2a3deb; text-align: center; height: 13px;">{$exchange_rate}</td>
            </tr>
            <tr style="height: 16px;">
              <td style="width: 294px; background-color: #2a3deb; text-align: center; height: 16px;"><strong><span style="font-size: 14px;">Nominal</span></strong></td>
            </tr>
            <tr style="height: 13px;">
              <td style="width: 294px; height: 13px; background-color: #2a3deb; text-align: center;">
                 
                {$nominal} {$nominal_id}
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>';

		// $html = "<style>".file_get_contents("../bootstrap.min.css")."</style>";
		// $html = file_get_contents('../text.txt');
		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
		// set default form properties
		$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
		// reset pointer to the last page
		$pdf->lastPage();
		//Close and output PDF document
		$pdf->Output(null, 'I');

	}

	
	// function defaultTemplate(){
	// 	return ['view/table'];
	// }

}