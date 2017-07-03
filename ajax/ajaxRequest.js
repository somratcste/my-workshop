$.ajax({
	type: 'POST',
	url: "<?php echo $this->Url->build(['controller'=>'Dashboard','action'=>'showList' ]) ?>",
	data: {"year" : year, "month" : month},
	success: function(data){
		$('#showlist').html(data);
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
