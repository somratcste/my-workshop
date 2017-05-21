$.ajax({
	type: 'POST',
	url: "<?php echo $this->Url->build(['controller'=>'Dashboard','action'=>'showList' ]) ?>",
	data: {"year" : year, "month" : month},
	success: function(data){
		$('#showlist').html(data);
	}
});
