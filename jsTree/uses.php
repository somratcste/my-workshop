<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jstree/3.3.3/themes/default/style.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/jstree/3.3.3/jstree.min.js"></script>

<div id="jstree_demo"></div>


<!-- php controller-->

public function getTree($part_number)
{
    $table = TableRegistry::get('PartComposites');
    $lists = $table->find()
        ->where(["part_number LIKE"=>"%" . $part_number . "%"])->toArray();
    if(empty($lists)){
        return false;
    }

    $composite_part_number = [];
    $composite_part_id = [];
    foreach($lists as $list){
        $composite_part_number[] = $list->composite_part_number;
        $composite_part_id[] = $list->id;
    }
    $join_var = $table->find()->select(['part_name'=>'PartInfos.part_name','unit_price'=>'PartInfos.unit_price','on_hand_qty' => 'PartInfos.on_hand_qty',
        'PartComposites.part_number', 'PartComposites.composite_part_number','PartComposites.part_type','PartComposites.composite_part_qty',
        'PartComposites.composite_part_type','PartComposites.id'])
        ->join(
            [
                'PartInfos' => [
                    'table' => 'part_infos',
                    "conditions" => ["PartInfos.part_number = PartComposites.composite_part_number"],
                    "type" => "inner"
                ],
            ])->where(['PartInfos.part_number IN'=> $composite_part_number,'PartComposites.id IN'=>$composite_part_id])->toArray();
    return $join_var;
}

public function getJsTreeData()
{
    $data = $this->request->getData();
    $tree = [];
    if (!empty($data)) {
        $tablePartComposites = TableRegistry::get('PartComposites');
        $tablePartInfos = TableRegistry::get('PartInfos');
        $own_id = !empty($data ['id']) ? $data ['id'] : '';
        $parent_id = !empty($data ['parent_id']) ? $data ['parent_id'] : '';
        $part_number = !empty($data ['part_number']) ? $data ['part_number'] : '';

        if ((empty($parent_id) || $parent_id == '#') && !empty($own_id)) {
            $getTreeValue = $this->getTree($part_number);
        } else {
            $getTreeValue = $this->getTree($part_number);
        }

        if ($parent_id == '#' || $parent_id == '') {
            $parent_id = '#';

            if (!empty($getTreeValue)) {
                foreach ($getTreeValue as $value) {
                    if($value->composite_part_type == 'SET')
                        $icon = 'icon-plus-circle';
                    else if ($value->composite_part_type == "KIT")
                        $icon = 'icon-plus-circle2';
                    else
                        $icon = 'icon-eye';
//                        $child_unit = $this->getTree($value->composite_part_number );
//                        $children = !empty($child_unit) ? true : false;
                    $tree [] = array(
                        "id" => $value->id,
                        "text" => $value->composite_part_number.'    '.$value->part_name.'    '.$value->composite_part_type.'    '.$value->unit_price ,
                        "part_number" => $value->composite_part_number,
                        "children" => true,
                        "type" => "root",
                        "icon" => $icon
                    );
                }
            }
        } else {
            if (!empty($getTreeValue)) {
                foreach ($getTreeValue as $value) {
                    if($value->composite_part_type == 'SET')
                        $icon = 'icon-plus-circle';
                    else if ($value->composite_part_type == "KIT")
                        $icon = 'icon-plus-circle2';
                    else
                        $icon = 'icon-eye';
//                        $child_unit = $this->getTree($value->composite_part_number);
//                        $children = !empty($child_unit) ? true : false;
                    $tree [] = array(
                        "id" => $value->id,
                        "text" => $value->composite_part_number.'    '.$value->part_name.'    '.$value->composite_part_type.'    '.$value->unit_price ,
                        "part_number" => $value->composite_part_number,
                        "children" => true,
                        "icon" => $icon,
                        "type" => "root"
                    );
                }
            }
        }
    }

    $this->response->body(json_encode($tree));
    $this->response->type('json');
    return $this->response;
}


<script type="text/javascript">
    $('#jstree_demo').jstree({
        "core": {
            "themes": {
                "responsive": false
            },
            "check_callback": true,
            'data': {
                'url': function (node) {
                    return '<?= $this->Url->build(['controller' => 'PartComposites', 'action' => 'getJsTreeData']) ?>';
                },
                'method': 'post',
                'data': function (node) {
                    if (node.id == '#') {
                        return {
                            'parent_id': node.id,
                            part_number: $('#part-number').val(),
                            id: $('#part-number').val()
                        };
                    }
                    return {'parent_id': node.id, part_number: node.original.part_number};
                },
                "success" : function (response) {
                    if(response == ''){
                        notification('No Composition Found.');
                    }
                }
            }
        },
        plugins: [ "themes", "types"]
    });

    //        $('#jstree_demo').on("select_node.jstree", function (e, data) {
    //            var part_number = data.node.original.part_number;
    //        });

    $('#jstree_demo').jstree(true).refresh();
</script>