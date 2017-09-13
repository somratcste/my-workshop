$.ajax({
	type: 'POST',
	url: "<?php echo $this->Url->build(['controller'=>'Dashboard','action'=>'showList' ]) ?>",
	data: {"year" : year, "month" : month},
	success: function(data){
		$('#showlist').html(data);
	}
});

$.ajax({
            url : '<?= $this->Url->build(["_name"=>(!empty($options['masterListUrl'])?$options['masterListUrl']:'')]) ?>',
            data: {},
            type: 'post',
            dataType: 'html',
            success: function(response){
                $('.showDetails').html(response);
            }
        });
//phalcon 
var url = '/npfadmin/contenttype/croppie';
$.ajax({
	url: url,
	type: 'POST',
	dataType : 'json',
	data: {imagebase64 : resp, contenttype : '<?php echo $contenttype; ?>', uploadPath : $('#uploadpath').val(), previousImage : $('#imageID').attr('src')},
	success: function(response){
		$('#myModal').modal('toggle');
		$('.upload_image').show();
		$('#imageID').attr('src',response.result);
		$('#_0_name').val(response.filename);
		alert("Image Upload Successfully");
	},
	error: function(response){
		$('#myModal').modal('toggle');
		alert(response);
	}
});


// cakephp 3 ajax with cache 

$(function () {
        $('[name=part_number]').on("change", function () {
            var val = $(this).val();
            if (val.length > 0) {
                $.ajax({
                    cache: true,
                    url: '<?= $this->Url->build(["_name" => 'partDetail']) ?>',
                    method: 'post',
                    dataType: 'json',
                    data: {'part_number': val},
                    success: function (response) {
                        if (typeof response.part != 'undefined' && Object.keys(response.part).length != 0) {
                            $.each(response.part, function (i, v) {
                                $('[name=' + i + ']').val(v)
                            });
                            $('.alert-info').show();
                            $('.show-alert').append("The Part Already Exist . <a href='/mpms_new/part_infos/view/"+response.part.id+"' class='icon-eye'> | </a><a href='/mpms_new/part_infos/edit/"+response.part.id+"' class='icon-pencil3'>");
                        } else {
                            $('[name=id]').val(0);
                            $('.form').find('input[type=text]').not('[name=part_number]').val('');
                            $('.form').find('select').val('');
                            $('.form').find('textarea').val('');
                        }
                    }
                });
            }

        })
    });