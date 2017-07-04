{{ stylesheet_link('css/croppie.css') }}
{{ javascript_include('js/croppie.js') }}
<style type="text/css">
	.modal {
		background-clip: padding-box;
		background-color: #fff;
		border: 1px solid rgba(0, 0, 0, 0.3);
		border-radius: 6px;
		box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
		left: 35%;
		margin: -250px 0 0 -280px;
		outline: 0 none;
		position: fixed;
		top: 50%;
		width: 900px;
		z-index: 1050;
	}
	.modal.fade.in {
	  top: 40%;
	}
</style>

<!-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Upload Image</button> -->
<!-- Modal -->
<div id="myModal" class="modal fade modal-lg" role="dialog">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header btn-info">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Upload Your Image </h4>
      </div>
      <div class="modal-body">
        <form>
	        <input type="file" id="upload" value="Choose a file">
	        <div id="upload-demo"></div>
	        <input type="hidden" id="imagebase64" name="imagebase64">
      </div>
      <div class="modal-footer btn-info">
      	<button type="button" class="upload-result btn btn-success">Upload</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
        </form>
    </div>

  </div>
</div>
<!--End Modal -->


<table id="tbl-{{ fldname }}">
    <thead>
        <tr>
            <th>File Name</th>
            <th>Caption (bn)</th>
            <th>Caption (en)</th>
            <th>Link</th>
        </tr>
    </thead>
    <tbody>
    <?php if(isset($fldval)){
        ?>
        <tr>
            <td>
                <div class="input-append">
                    {{ text_field(fldname~"[0][name]","id":fldname~"_0_name","class":"input-medium","readonly":"readonly","value":fldval['name']) }}
                    {{ hidden_field(fldname~"[0][path]","value":fldval['path']) }}
                    <a class="btn" id="showModal" data-toggle="modal" data-target="#myModal"><i class="icon-folder-open"></i></a>
                    <a id="imageRemove" role="button" class="btn"><i class="icon-remove"></i></a>
                </div>
            </td>
            <td>{{ text_field(fldname~"[0][caption_bn]","value":fldval['caption_bn']) }}</td>
            <td>{{ text_field(fldname~"[0][caption_en]","value":fldval['caption_en']) }}</td>
            <td>
                <div class="input-append">
                    {{ text_field(fldname~"[0][link]","value":fldval['link']) }}
                    <a id="btn-contentref" href="javascript:;" role="button" class="btn" onclick="showSelectContent(this,''); return false;"><i class="icon-search"></i></a>
                </div>
            </td>
        </tr>
    <?php }else{ ?>
        <tr>
        </tr>
        <tr>
            <td>
                <div class="input-append">
                    {{ text_field(fldname~"[0][name]","id":fldname~"_0_name", "class":"input-medium","readonly":"readonly") }}
                    {{ hidden_field(fldname~"[0][path]","value":"") }}
                    <a class="btn" id="showModal" data-toggle="modal" data-target="#myModal"><i class="icon-folder-open"></i></a>
                    <a id="imageRemove" role="button" class="btn"><i class="icon-remove"></i></a>
                </div>
            </td>
            <td>{{ text_field(fldname~"[0][caption_bn]") }}</td>
            <td>{{ text_field(fldname~"[0][caption_en]") }}</td>
            <td>
                <div class="input-append">
                    {{ text_field(fldname~"[0][link]") }}
                    <a id="btn-contentref" href="javascript:;" role="button" class="btn" onclick="showSelectContent(this,''); return false;"><i class="icon-search"></i></a>
                </div>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<script type="text/javascript">
    $( document ).ready(function() {
        var $uploadCrop;
        $('#upload-demo').hide();
		$('input[type=file]').change(function() {
			$("#upload-demo").show();
		});

        function readFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $uploadCrop.croppie('bind', {
                        url: e.target.result
                    });
                    $('.upload-demo').addClass('ready');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $uploadCrop = $('#upload-demo').croppie({
            viewport: {
                width: {{ width }},
                height: {{ height }}
            },
            boundary: {
                width: 800,
                height: 300
            }
        });

        $('#upload').on('change', function () { readFile(this); });
        $('.upload-result').on('click', function (ev) {
            $uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'original'
            }).then(function (resp) {
                $('#imagebase64').val(resp);
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
            });
        })

        $('#imageRemove').on('click', function(){
        	if($('#imageID').attr('src')){
	        	var url = '/npfadmin/contenttype/croppieCancel';
	        	$.ajax({
			        	url: url,
						type: 'POST',
						dataType : 'json',
						data: {previousImage : $('#imageID').attr('src')},
						success: function(response){
							$('#_0_name').val('');
	        				$('.upload_image').hide();
							alert(response.result);
						},
						error: function(response){
							console.log(response);
							alert("Image can not deleted.");
						}
					});
        	} else {
        		return false;
        	}
        });

        $('#showModal').on('click', function(){
        	$('#upload-demo').hide();
            $("#upload").val('');
        });
    });
</script>