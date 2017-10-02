<?php
include APP.'/Vendor/mpdf/mpdf.php';


$html='<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
    .clearfix:after {
  content: "";
  display: table;
  clear: both;
}

a {
  color: #5D6975;
  text-decoration: underline;
}

body {
  position: relative;
  width: 21cm;  
  height: 29.7cm; 
  margin: 0 auto; 
  color: #001028;
  background: #FFFFFF; 
  font-family: Arial, sans-serif; 
  font-size: 7.5pt; 
  font-family: Arial;
}

header {
  padding: 0px 0;
  margin-bottom: 3.75pt;
}

.headerClass {
  padding: 0px 0;
  margin-bottom: 3.75pt;
}

.head {
  border-top: 1px solid  #5D6975;
  color: #5D6975;
  font-size: 13.5pt;
  line-height: 1.4em;
  font-weight: normal;
  margin: 0 0 7.5pt 0;
}
.headOther {
  color: #5D6975;
  font-size: 13.5pt;
  line-height: 1.4em;
  font-weight: normal;
  margin: 0 0 7.5pt 0;
}
.project  span {
  color: #5D6975;
  text-align: right;in-right: 7.5pt;
  displa
  width: 39pt;
  margin-right: 7.5pt;
  display: inline-block;
  font-size: 0.8em;
}
.company span {
  color: #5D6975;
  text-align: right;
  width: 39pt;
  margy: inline-block;
  font-size: 9pt;
}


.project div,
.company div {
  white-space: nowrap;        
}

table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 14.5pt;
}

table tr:nth-child(2n-1) td {
  background: #F5F5F5;
}

table th,
table td {
  text-align: center;
  font-size: 7.5pt;
}

table th {
  padding: 3.75pt 3.75pt;
  border-bottom: .75pt solid #C1CED9;
  white-space: nowrap;        
  font-weight: bold;
}

table td {
  padding: 3.75pt;
  text-align: right;
}

table td.service,
table td.desc {
  vertical-align: top;
}

table td.unit,
table td.qty,
table td.total {
  font-size: 7.5pt;
}

table td.grand {
  border-top: 1px solid #5D6975;;
}

 th, td {
    border: 1px solid #C1CED9;
    padding: 10px;
  }
td {
vertical-align: top
}

footer {
  clear: both;
  color: #5D6975;
  width: 100%;
  height: 21.7pt;
  position: absolute;
  bottom: 0;
  border-top: 1px solid #C1CED9;
  padding: 6pt 0;
  text-align: center;
}

</style>
  </head>
  <body>
    <header class="clearfix">
      <div class="company clearfix" style="width: 75%;  padding: 0px; margin: 0px;float: left; font-size: 8pt;" >
         <p align="left" style="width: 20%;float: left;"><img style="padding-top: -20.5pt;" src="'.$organization_info->image_dir.$organization_info->image_file_name.'" width="80px"></p>
         <h1 align="left" style="width:80%;float:left;">'.$organization_info->name.'</h1>
      </div>
      <div class="project clearfix" style=" padding: 0px; margin: 0px; float: right; font-size: 8pt;" >
        <h2 align="right" style="padding-top: -14.5pt;">Invoice</h2>
      </div>
      
      <div class="company clearfix" style="width:50%;float:left;font-size:8pt;">
         <p style="float: left;">
            Ref : '.$invoice_master->reference.'
        </p>  
     </div>
     <div class="company clearfix" style="width:50%;float:right;font-size:8pt;">
         <p align="right" style="float:right">
            Date : '.$invoice_master->invoice_date.'
        </p>  
     </div>
      <div class="company clearfix" style="height:80px; width: 35%;  padding: 10px; margin: 0px;float: left; font-size: 8pt;border:1px solid #000">
        <div>TO</div>
        <div>'.$party_info["name"].'</div>
        <div>'.$party_info["address"].'</div>
        <div>'.$party_info["mobile"].'</div>
        <div>'.$party_info["fax"].'</div>
      </div>
      <div class="project clearfix" style="height:80px; padding: 10px; margin-left: 15px; float: right; font-size: 8pt;border:1px solid #000">
         '.$invoice_master->reference_detail.'
      </div>
    </header>
    <main>   
   <table style="margin-top:10px">
        <thead>
        <tr>
            <th >Sl</th>
            <th >Part No</th>
            <th >Description</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Total Price</th>
            <th>Delivery</th>
            <th>Remarks</th>
        </tr>
        </thead>
        <tbody>
        '.$trs.'
         <tr>
            <td colspan="3" align="left"> <b> Total</b></td>
            <td>'.str_replace('0','',$total_quantity).'</td>
            <td colspan="1"></td>
            <td>'.str_replace('0.00','',number_format($total_price,2)).'</td>
            <td colspan="2"></td>
        </tr>
        </tbody>
      </table>
      
      <div style="font-size:10pt">
          <div class="" style="width: 20%;  padding: 0px; margin: 0px;float: left;">
                Offer Validity 
            </div>
          <div class="" style="padding: 0px; margin: 0px; float: right;">
             '.str_replace('<p>','',$invoice_master->offer_validity).'
          </div>
      </div>
      
      <div style="font-size:10pt;margin-top:5px;">
        <div class="" style="width: 20%;  padding: 0px; margin: 0px;float: left;">
            Payment Terms  
        </div>
          <div class="" style="padding: 0px; margin: 0px; float: right;">
             '.str_replace('<p>','',$invoice_master->payment_terms).'
          </div>
      </div>
      
      <div style="font-size:10pt;margin-top:15px;">
        <div class="" style="width: 20%;  padding: 0px; margin: 0px;float: left;">
            Note 
        </div>
          <div class="" style="padding: 0px; margin: 0px; float: right;">
             '.str_replace('<p>','',$invoice_master->note).'
          </div>
      </div>
      
    </main>
  </body>
</html>';

$mpdf = new mPDF();
$mpdf->setFooter('<div style="clear:both;margin-top:-10pt;"></div><table width="100%" style="margin-top: -10pt;vertical-align: bottom; font-family: serif; font-size: 4.5pt; color: #000000;"><tr>
<td width="40%" style="text-align: left;border: none;"><span style="border:none; font-style: italic;"> © 2017. Powered by Tappware Solution Limited</a></td>

<td width="30%" align="center" style="border: none; font-style: italic;">MPMS '.date('jS M Y g:i A').'</td>

<td width="30%" style="border:none;text-align: right; ">{PAGENO}/{nbpg}</td>

</tr></table>');
$mpdf->autoLangToFont;
$mpdf->WriteHTML($html);
ob_end_clean();
$mpdf->Output('C:\xampp\htdocs\mpms_new\webroot\files\invoice\invoice.pdf', 'F');
$mpdf->Output('Invoice.pdf', 'I');
exit;

