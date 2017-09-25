<?php
include APP.'/Vendor/mpdf/mpdf.php';

$contribution_amount =(($std_bill_amount*$contribution_percentage)/100);
$students_data= '';
$sl=1;
foreach($all_data as $data){
    $students_data.='<div class="clearfix">
    <h2 align="right" style="padding-top: -15pt;">TRAINEE LIST OF '.urldecode($data['bill_no']).'</h2>
      <div class="head">
      <div style="float: left; width: 95%"> '.$image_file_url.'<b>'.$entity_name.'</b></div>
      <div style="float: right; text-align: right">'.$sl++.'</div>
      </div>
      
      <div class="company clearfix" style="width: 75%;  padding: 0px; margin: 0px;float: left; font-size: 8.25pt;" >
        <div><strong>Institute : </strong>'.$data["institute_name"].'</div>
        <div><strong>Course : </strong>'.$data["course_name"].'</div>
        <div><strong>Batch : </strong>'.$data["batch_number"].'</div>
      </div>
      <div class="project clearfix" style=" padding: 0px; margin: 0px; float: right; font-size: 8.25pt;">
        <div><span>SUBMITTED DATE</span> '.$submitted_date.'</div>
        <div><span>SUBMITTED BY</span> '.$submitted_by.'</div>
        <div><span>SYSTEM REF</span> '.$entity_id.'-'.$bill_sequence_no.'-'.$claim_no.'-'.date("Ymd").'</div>
      </div>
    </div>';
    $students_data.='<table style="margin-bottom: 7.5pt">
    <thead>
    <tr>
    <th>Total Student</th>
    <th>Female</th>
    <th>Male</th>
    </tr>
</thead>
<tbody>
<tr>
<td align="center">'.$data['total_student'].'</td>
<td align="center">'.$data['female'].'</td>
<td align="center">'.($data['total_student']-$data['female']).'</td>
</tr>
</tbody>
</table>';

    $students_data.="<table align='center'>
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Photo</th>
                    <th>Reference Number</th>
                    <th>Registration Number</th>
                    <th>Trainee Name</th>
                    <th>Mobile</th>
                    <th>DoB</th>
                    <th>Gender</th>
                </tr>
            </thead>
            <tbody>";
    $i=1;
    foreach ($data['bill_students'] as $traineeRegistered){
    $students_data .= "<tr>";
    $students_data .= "<td align='left'>" . $i++ . "</td>";
    $students_data .= "<td align='center'>" .((strlen ( trim ( $traineeRegistered['TraineeInfo'] ['image_file_name'] ) ) > 0 )?('<img src="'.$this->request->webroot.'files/trainee_info/image_file_name/'. $traineeRegistered['TraineeInfo'] ['image_file_name'].'" height="17pt" width="17pt" style="vertical-align: middle;">'):('<img src="'.$this->request->webroot.'images/entity_logo.png" alt="Trainee Photo" height="17pt" width="17pt" style="vertical-align: middle;">') ) . "</td>";
    $students_data .= "<td align='left'>" . h($traineeRegistered['TraineeInfo']['reference_number']) . "</td>";
    $students_data .= "<td align='left'>" . h($traineeRegistered['TraineeInfo']['registration_number']) . "</td>";
    $students_data .= "<td align='left'>" . h($traineeRegistered['TraineeInfo']['trainee_name']) . "</td>";
    $students_data .= "<td align='left'>" . h($traineeRegistered['TraineeInfo']['mobile']) . "</td>";
    $students_data .= "<td align='left'>" . h($traineeRegistered['TraineeInfo']['date_of_birth']) . "</td>";
    $students_data .= "<td align='left'>" . h($traineeRegistered['TraineeInfo']['gender']) . "</td>";
    $students_data .= "</tr>";
    }
    $students_data.="<tr>
<td colspan='4' align='left'><b>Total Student: ".$data['total_student']."</b></td>
<td colspan='2' align='left'>Female: ".$data['female']."</td>
<td colspan='2' align='right'>Male: ".($data['total_student']-$data['female'])."</td>
</tr>";
    $students_data.= " </tbody></table><pagebreak>";
}

$html='<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Trainee Management System</title>
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
  border-bottom: 1px solid  #5D6975;
  color: #5D6975;
  font-size: 13.5pt;
  line-height: 1.4em;
  font-weight: normal;
  margin: 0 0 7.5pt 0;
}
.project  span {
  color: #5D6975;
  text-align: right;
  width: 39pt;
  margin-right: 7.5pt;
  display: inline-block;
  font-size: 0.8em;
}
.company span {
  color: #5D6975;
  text-align: right;
  width: 39pt;
  margin-right: 7.5pt;
  display: inline-block;
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

footer {
  color: #5D6975;
  width: 100%;
  height: 22.5pt;
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
    <h1 align="right" style="padding-top: -14.5pt;">INVOICE</h1>
      <div class="head"> '.$image_file_url.'<b>'.$entity_name.'</b></div>
      
      <div class="company clearfix" style="width: 75%;  padding: 0px; margin: 0px;float: left; font-size: 8pt;" >
        <div><span>INVOICE SUBMITTED TO:</span></div>
        <div>Skills for Employment Investment Program</div>
        <div>Probashi Kalyan Bhaban (Level-16),<br /> 71-72 Old Elephant Road,<br /> Eskaton Road, Dhaka-1000.</div>
      </div>
      <div class="project clearfix" style=" padding: 0px; margin: 0px; float: right; font-size: 8pt;" >
        <div><span>BILL NO</span> Bill-'.$bill_sequence_no.'</div>
        <div><span>CLAIM NO</span> Claim-'.$claim_no.'</div>
        <div><span>SUBMITTED DATE</span> '.$submitted_date.'</div>
        <div><span>SUBMITTED BY</span> '.$submitted_by.'</div>
        <div><span>SYSTEM REF</span> '.$entity_id.'-'.$bill_sequence_no.'-'.$claim_no.'-'.date("Ymd").'</div>
      </div>
    </header>
    <main>
    <table style="margin-bottom: 7.5pt">
    <thead>
    <tr>
    <th>Total Batch</th>
    <th>Total Student</th>
    <th>Gross Amount</th>
    <th>Contribution Amount</th>
    <th>Net Payable</th>
    </tr>
</thead>
<tbody>
<tr>
<td align="center">'.$bill_count.'</td>
<td align="center">'.$std_count.'</td>
<td align="center">'.number_format($std_bill_amount, 2).'</td>
<td align="center">'.number_format($contribution_amount).'</td>
<td align="center">'.number_format($std_bill_amount-$contribution_amount).'</td>
</tr>
</tbody>
</table>
      <table>
        <thead>
        <tr>
                    <th >Sl</th>
                    <th >TI</th>
                    <th >Course</th>
                    <th>BN</th>
                    <th>Trainee Count</th>
                    <th>Unit Cost</th>
                    <th>Pay %</th>
                    <th>Payment Amount</th>
                    <th>Bill Amount</th>
                </tr>
        </thead>
        <tbody>
        '.$trs.' 
        <tr>
        <td colspan="3" align="left"> <b> Total</b></td>
        <td>'.$bill_count.'</td>
        <td align="center">'.$std_count.'</td>
        <td colspan="3" align="right"><b>A) Gross Amount</b></td>
        <td>'.number_format($std_bill_amount, 2).'</td>
</tr>
        <tr>
        <td colspan="8" align="right"><b>B) Contribution Percentage</b></td>
        <td>'.$contribution_percentage.'% </td>
</tr>
        <tr>
        <td colspan="8" align="right"><b>C) Contribution Amount (A*B)</b></td>
        <td>'.number_format($contribution_amount, 2).'</td>
</tr>
        <tr>
        <td colspan="8" align="right"><b>D) Net Payable (A - C)</b></td>
        <td>'.number_format(($std_bill_amount-$contribution_amount), 2).'</td>
</tr>
        </tbody>
      </table>
      <pagebreak>'.$students_data.'
    </main>
  </body>
</html>';

$mpdf = new mPDF();
$mpdf->setFooter('<table width="100%" style="margin-top: auto;vertical-align: bottom; font-family: serif; font-size: 4.5pt; color: #000000;"><tr>
`
<td width="33.33%" style="text-align: left; "><span style=" font-style: italic;"> © 2017. Powered by Tappware</a></td>

<td width="33.33%" align="center" style=" font-style: italic;">TMS '.date('jS M Y g:i A').'</td>

<td width="33.33%" style="text-align: right; ">{PAGENO}/{nbpg}</td>

</tr></table>');
$mpdf->autoLangToFont;
$mpdf->WriteHTML($html);
ob_end_clean();
$mpdf->Output('Invoice '.$entity_id.'-'.$bill_sequence_no.'-'.$claim_no.'-'.date("Ymd").'.pdf', 'I');
exit;

