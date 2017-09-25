<?php
include APP.'/Vendor/mpdf/mpdf.php';

$trs='';
$sl=1;
foreach ( $lists as $list ) {
    $trs .="<tr>";
    $trs .="<td>".$sl++."</td>";
    $trs .="<td align='left'>". $list['part_number']."</td>";
    $trs .="<td align='left'>".$list['part_name']."</td>";
    $trs .="<td align='right'>".number_format($list['unit_price'], 2)."</td>";
    $trs .="<td align='center'>".$list['part_size_unit']."</td>";
    $trs .="<td align='center'>".$list['location']."</td>";
    $trs .="</tr>";
}
$html='<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Price List</title>
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
  color: #5D6975;
  border-bottom: .75pt solid #C1CED9;
  white-space: nowrap;        
  font-weight: normal;
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
  <htmlpageheader name="otherpages" style="display:none">
    <div style="text-align:center">
    <div class="headOther" align="center"><b>Price List</b></div> 
    </div>
   </htmlpageheader>
<sethtmlpageheader name="otherpages" value="on" />
    <header class="clearfix">
      <div class="company clearfix" style="width: 75%;  padding: 0px; margin: 0px;float: left; font-size: 8pt;" >
         <p align="left" style="width: 20%;float: left;"><img style="padding-top: -20.5pt;" src="'.$organization_info->image_dir.$organization_info->image_file_name.'" width="80px"></p>
         <h1 align="left" style="width:80%;float:left;">'.$organization_info->name.'</h1>
      </div>
      <div class="project clearfix" style=" padding: 0px; margin: 0px; float: right; font-size: 8pt;" >
        <h2 align="right" style="padding-top: -14.5pt;">PRICE LIST</h2>
        <p align="right" style="padding-top: -10.5pt;">'. date("Y/m/d").'</p>
      </div>
    </header>
    <main>   
    <div class="head" align="center"><b>Price List</b></div> 
   <table>
        <thead>
        <tr>
            <th >Sl</th>
            <th >Part ID</th>
            <th >Description</th>
            <th>Sale Price</th>
            <th>Unit</th>
            <th>Location</th>    
        </tr>
        </thead>
        <tbody>
        '.$trs.'
        </tbody>
      </table>
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
$mpdf->Output('Price List.pdf', 'I');
exit;

