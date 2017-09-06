<div class="col-md-2 col-sm-2 col-xs-2" style="margin-top: 20px;">
    <input type="button" onclick="downloadXL()" value="Export" class="btn btn-primary btn-sm">
</div>

<form id="form_submit" action="<?= $this->request->webroot ?>assessorInfos/itemList?downloadxls=true" method="post">
    <input type="hidden" id="entity_id_xl" name='entity_id' >
    <input type="hidden" id="training_institute_id_xl" name="training_institute_id" >
</form>
<script>
    function downloadXL() {
        var entityId = $("#entity_id").val();
        var institute_id = $("#institute_id").val();
        $("#entity_id_xl").val(entityId);
        $("#training_institute_id_xl").val(institute_id);
        $("#form_submit").submit();
    }
</script>
<!--controller Cakephp 2-->
<?php
public function itemList()
{

    $assessors = $this->AssessorInfo->getAssessorList($entity_id, $institute_assessor_ids);
    $xcel_data = array();
    $ind = 0;
    $is_xcel = (isset($this->request->query['downloadxls'])) ? true : false;
    if(!empty($assessors)){
        foreach ($assessors as $assessor) {
            if ($assessor['AssessorInfo']['is_active'] == 1) {
                $total_active++;
            }
            else {
                $total_inactive++;
            }
            if ($is_xcel) {
                $xcel_data[$ind] = $assessor['AssessorInfo'];
                $xcel_data[$ind]['active_status'] = ($assessor['AssessorInfo']['is_active'] == 1)?'Active':'Inactive';
                $ind++;
            }
        }
    }
    if (isset($this->request->query['downloadxls'])) {
    //            pr(($xcel_data));die;
        $this->printExcel($xcel_data,array(
        array('key'=>'si','title'=>'SN.'),
        array('key'=>'assessor_name','title'=>'Assessor Name'),
        array('key'=>'gender','title'=>'Gender'),
        array('key'=>'mobile','title'=>'Phone'),
        array('key'=>'email','title'=>'Emails'),
        array('key'=>'area_of_expertise','title'=>'Area Of Expertise'),
        array('key'=>'active_status','title'=>'Status'),
        ),array('name' => 'Assessor List','title' => ['Nazmul','Tushar']));
    //            exit;
    }
}


// Controller

public function printExcel($data = array(), $header = array(), $options = array())
{
    try{
        set_time_limit('0');
        ini_set('memory_limit', '-1');
        include APP.'/Vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
        include APP.'/Vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php';

        $book  = new PHPExcel();
        $title = isset($options['name']) ? $options['name'] : 'excel';
        $book->getActiveSheet()->setTitle('Sheet 1');
        $sheet = $book->getActiveSheet();

        $style     = array(
            'font' => array('bold' => true, 'size' => 12),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        );
        $cur_index = 1;
        if (!empty($header)) {
            $t_header = count($header);
            if (!empty($options['title'])) {
                foreach ($options['title'] as $titles) {
                    $sheet->setCellValueByColumnAndRow(0, $cur_index, $titles);
                    $sheet->mergeCellsByColumnAndRow(0, $cur_index, $t_header, $cur_index);
                    $sheet->getStyleByColumnAndRow(0, $cur_index)->applyFromArray($style);
                    $cur_index++;
                }
            }
            foreach ($header as $key => $headTitle) {
                $sheet->setCellValueByColumnAndRow($key, $cur_index, $headTitle['title']);
            }
            $cur_index++;
        }

        if (!empty($data)) {
            foreach ($data as $row => $value) {
                foreach ($header as $col => $headTitle) {
                    $sheet->setCellValueByColumnAndRow($col, $cur_index,
                        ($headTitle['key'] == 'si' ? ($row + 1) : (isset($value[$headTitle['key']])
                            ? $value[$headTitle['key']] : '')));
                }
                $cur_index++;
            }
        }

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\".$title.xls\"");
        header("Cache-Control: max-age=0");
        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');

        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    catch (Exception $ex) {

    }
}



// Cakephp 3 Controller

public function partInfoExcelExport()
{
    $excel_data = array();
    $part_number = $this->request->getData('part_number');
    $table = TableRegistry::get('PartInfos');
    $excel_data = $table->getList($part_number)->toArray();

    $this->printExcel($excel_data,array(
        array('key'=>'si','title'=>'SL.'),
        array('key'=>'part_number','title'=>'Part Number'),
        array('key'=>'part_name','title'=>'Part Name'),
        array('key'=>'part_size','title'=>'Part Size'),
        array('key'=>'unit_price','title'=>'Unit Price'),
        array('key'=>'on_hand_qty','title'=>'On Hand Qty'),
        array('key'=>'on_sale_qty','title'=>'On Sale Qty'),
        array('key'=>'on_order_qty','title'=>'On Order Qty'),
        array('key'=>'supplier_code','title'=>'Supplier Code'),
    ),array('name' => 'Product Ledger List','title' => ['Product Ledger List']));

}

private function printExcel($data = array(), $header = array(), $options = array())
{
    try{
        set_time_limit('0');
        ini_set('memory_limit', '-1');
        require ROOT .DS. 'vendor' . DS . 'phpoffice'.DS .'phpexcel' . DS .'Classes'. DS . 'PHPExcel.php';
        require ROOT .DS. 'vendor' . DS . 'phpoffice'.DS .'phpexcel' . DS .'Classes'.DS. 'PHPExcel'. DS . 'IOFactory.php';
        $book  = new \PHPExcel();

        $title = isset($options['name']) ? $options['name'] : 'excel';
        $book->getActiveSheet()->setTitle('Sheet 1');
        $sheet = $book->getActiveSheet();
        $style     = array(
            'font' => array('bold' => true, 'size' => 12),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        );
        $cur_index = 1;
        if (!empty($header)) {
            $t_header = count($header);
            if (!empty($options['title'])) {
                foreach ($options['title'] as $titles) {
                    $sheet->setCellValueByColumnAndRow(0, $cur_index, $titles);
                    $sheet->mergeCellsByColumnAndRow(0, $cur_index, $t_header, $cur_index);
                    $sheet->getStyleByColumnAndRow(0, $cur_index)->applyFromArray($style);
                    $cur_index++;
                }
            }
            foreach ($header as $key => $headTitle) {
                $sheet->setCellValueByColumnAndRow($key, $cur_index, $headTitle['title']);
            }
            $cur_index++;
        }

        if (!empty($data)) {
            foreach ($data as $row => $value) {
                foreach ($header as $col => $headTitle) {
                    $sheet->setCellValueByColumnAndRow($col, $cur_index,
                        ($headTitle['key'] == 'si' ? ($row + 1) : (isset($value[$headTitle['key']])
                            ? $value[$headTitle['key']] : '')));
                }
                $cur_index++;
            }
        }

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\".$title.xls\"");
        header("Cache-Control: max-age=0");
        $writer = \PHPExcel_IOFactory::createWriter($book, 'Excel2007');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    catch (\Exception $ex) {

    }
}

?>
