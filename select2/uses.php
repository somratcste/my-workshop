<div class="col-sm-2">
    <?= $this->Form->input('part_number', ['type' => 'select', 'class' => 'searchbox']) ?>
</div>

<?= $this->Html->css('css/select2.min') ?>
<?= $this->Html->script('js/select2.min') ?>

<script>
    $(function(){
        $('.searchbox').select2({
            width: '100%',
            placeholder:'Search Part Number',
            ajax: {
                url : '<?= $this->Url->build(["_name"=>'partinfos']) ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        'keyField' : 'part_number',
                        'key': 'part_number',
                        'part_number': params.term, // search term
                        page: params.page,
                    };
                },
                method: 'post',
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
    });
</script>

<?php
//controller
public function searchPartInfos(){
    $table = TableRegistry::get('PartInfos');
    $keyField = $this->request->getData('keyField');
    if(empty($keyField))
        $keyField = 'id';

    $part_type = $this->request->getData('part_type');
    $ignore_part_number = $this->request->getData('ignore_part_number');


    $key = $this->request->getData('key');
    $response = $table->find('list',['keyField'=>$keyField ,'valueField'=>$key])
        ->where(["$key LIKE"=>"%" . $this->request->getData($key) . "%"]);
    if(!empty($part_type) && $part_type != '')
        $response= $response->where(['part_type' => $part_type]);

    if(!empty($ignore_part_number))
        $response = $response->where(['part_number !=' => $ignore_part_number]);

    $response->limit(20)->toArray();

    $options = [];
    if(!empty($response)){
        foreach($response as $key=>$value){
            $options[] = ['id'=>$key,'text' => $value];
        }
    }
    $this->response->body(json_encode($options));
    $this->response->type('json');
    return $this->response;
}
?>
