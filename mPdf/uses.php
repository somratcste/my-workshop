<form target="_blank" id="ShowPdf" method="post" action="<?php echo $this->request->webroot.$this->request->params['controller'];?>/showPdf">
    <?php
    echo $this->Form->hidden('report_id', ['id' => 'report-id']);
    ?>
</form>

<script>
    $('#searchReport').on("click", function () {
        var report_id = $('#report-type').val();
        $('#report-id').val(report_id);
        $('#ShowPdf').submit();
    });
</script>

<?php
//controller
public function showPdf()
{
    date_default_timezone_set("Asia/Dhaka");
    $table = TableRegistry::get('OrganizationInfos');
    $organization_info = $table->getInfo();
    if(!empty($this->request->getData())){
        $report_type = $this->request->getData('report_id');
        $last_month = $this->request->getData('last_month_id');
        $top = $this->request->getData('top_most_id');
        if($report_type == 1) {
            $this->viewBuilder()->setTemplate('PriceListPdf');
            $lists = $this->PartInfos->getPdfData($report_type)->toArray();
            $this->set('lists',$lists);
            $this->set('organization_info',$organization_info);
        }
    }
}
?>