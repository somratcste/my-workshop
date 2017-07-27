<?php
echo $this->Session->flash();
?>
<div class="row" style="margin-top: 10px;font-size: 13px;">
    <div class="col-sm-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h6 class="panel-title">
                    <i class="icon-paragraph-right2"></i> Search SEIP Graduate Information
                </h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div>
                            <?php
                            echo $this->Form->create ( 'TraineeInfo', array ('inputDefaults' => array ('label' => false, 'div' => false, 'error' => false), 'class' => 'form-horizontal','type'=>'file') );
                            ?>
                            <label>Assocation </label><br>
                            <?php
                            echo $this->Form->input('entity_id', array('id'=>'entity_id','class'=>'col-sm-12', 'empty'=>'-- Select Association --'));
                            ?>
                        </div>
                        <div>
                            <label  style="margin-top: 20px;">Course</label><br>
                            <?php
                            echo $this->Form->input('course_id', array('id'=>'course_id','class'=>'col-sm-12', 'empty'=>'-- Select Course --'));
                            ?>
                        </div>
                        <div>
                            <label  style="margin-top: 20px;">Institute </label><br>
                            <?php
                            echo $this->Form->input('institute_id', array('id'=>'institute_id','class'=>'col-sm-12', 'empty'=>'-- Select Institute --'));
                            ?>
                        </div>
                        <div>
                            <label  style="margin-top: 20px;">Batch</label><br>
                            <?php
                            echo $this->Form->input('batch_id', array('id'=>'batch_id','class'=>'col-sm-12', 'empty'=>'-- Select Batch --'));
                            ?>
                        </div>
                        <button type="button" class="btn btn-danger" style="margin-top: 15px;margin-left:15px;" onclick="getCertificationList()">Search</button>
                        <?php echo $this->Form->end (); ?>
                    </div>
                    <div class="col-sm-6">
                        <div class="alert alert-success" style="margin-top: 95px;" id="course_lebel_id">

                        </div>
                        <div class="alert alert-info" style="margin-top: 30px;" id="institute_lebel_id">
                            <strong>Success!</strong> The page has been added.
                        </div>
                        <div class="alert alert-warning" style="margin-top: 30px;" id="batch_lebel_id">
                            <strong>Success!</strong> The page has been added.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h6 class="panel-title">
                    <i class="icon-paragraph-right2"></i> Search SEIP Graduate Information
                </h6>
            </div>
            <div class="panel-body" style="padding-top: 132px;padding-bottom: 132px;">
                <label class="control-label">Search By Registration Number</label>
                <?php
                echo $this->Form->create ( 'TraineeInfo', array ('inputDefaults' => array ('label' => false, 'div' => false, 'error' => false), 'class' => 'form-horizontal','type'=>'file') );
                echo $this->Form->input('trainee_code', array('id'=>'trainee_code','class'=>'input-large form-control'));
                echo $this->Form->end ();
                ?>
                <button type="button" class="btn btn-danger" style="margin-top: 10px" onclick="TRAINEE_SEARCH.getTraineeCurrentStatus()">Search</button>
            </div>
        </div>
    </div>
</div>

<div id="certification_list">

</div>

<!-- Modal -->
<div class="modal fade" id="profileModal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">SEIP Graduate</h4>
            </div>
            <div class="modal-body">
                <div class="content_part">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><i class="icon-table2"></i> Trinee Information</h6>
                                </div>
                                <div class="panel-body" style="padding: 20px 20px">
                                    <div class="col-sm-6">
                                        <span class="img-responsive pull-right" id="image"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <h6>Name  <span id="name" style="padding-left: 76px;"></span></h6>
                                        <h6>Gender  <span id="gender" style="padding-left: 67px;"></span></h6>
                                        <h6>Mobile  <span id="mobile" style="padding-left: 68px;"></span></h6>
                                        <h6>Birth Date  <span id="date_of_birth" style="padding-left: 46px;"></span></h6>
                                        <h6>Registration No.  <span id="registration_number" style="padding-left: 8px;"></span></h6>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><i class="icon-table2"></i> Course Details</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <th width="26%">Course Name</th>
                                            <td id="course"></td>
                                        </tr>
                                        <tr>
                                            <th>Institute Name</th>
                                            <td id="institute"></td>
                                        </tr>
                                        <tr>
                                            <th>Course Duration Details</th>
                                            <td><span id="course_duration_details"></span> . (<span id="course_duration_month"></span>)</td>
                                        </tr>
                                        <tr>
                                            <th>Course Info</th>
                                            <td style="text-align: justify;" id="course_objective"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><i class="icon-table2"></i> Trainee Status</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <th>Enrollment</th>
                                            <td id="enrollment"></td>
                                        </tr>
                                        <tr>
                                            <th>Enrollment Start Date</th>
                                            <td id="enrollment_start_date"></td>
                                        </tr>
                                        <tr>
                                            <th>Enrollment End Date</th>
                                            <td id="enrollment_end_date"></td>
                                        </tr>
                                        <tr>
                                            <th>Assesment Status</th>
                                            <td id="assessment"></td>
                                        </tr>
                                        <tr>
                                            <th>Certification Status</th>
                                            <td id="certification"></td>
                                        </tr>
                                        <tr>
                                            <th>Attendance Percentage</th>
                                            <td id="attendance_percentage"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo $this->Form->end (); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<!--End Modal -->

<script>
$("select").select2();
$("#course_lebel_id").hide();
$("#institute_lebel_id").hide();
$("#batch_lebel_id").hide();
function loadCourseInfo() {
    var entity_id = $("#entity_id").val();
    if(entity_id==''){
        var dataOptions = '<option value="0">-- Select Course --</option>';
        $("#course_id").html(dataOptions);
        $("#institute_id").html(dataOptions);
        return false;
    }
    $.ajax({
        type: 'POST',
        url: "<?php echo $this->request->webroot;?>" + 'traineeInfo/getCourseByEntityId/' + entity_id,
        data: {},
        success: function (data) {
            $("#course_id").empty();
            $("#institute_id").empty();
            $("#batch_id").empty();
            $('#course_id').select2('data', null);
            $('#institute_id').select2('data', null);
            $('#batch_id').select2('data', null);
            var dataOptions = '<option value="0">-- Select Course --</option>';
            $.each(data, function (i, v) {
                dataOptions += '<option value="' + i + '">' + v + '</option>';
            });
            $("#course_id").html(dataOptions);
        }
    });
}

function loadInstituteInfo() {
    var entity_id = $("#entity_id").val();
    var course_id = $("#course_id").val();
    if(entity_id=='' || entity_id == '0' || course_id == '' || course_id == '0'){
        var dataOptions = '<option value="0">-- Select Institute --</option>';
        $("#institute_id").html(dataOptions);
        return false;
    }
    $.ajax({
        type: 'POST',
        url: "<?php echo $this->request->webroot;?>" + 'traineeInfo/getInstituteByEntityIdCourseId',
        data: {'entity_id':entity_id,'course_id':course_id },
        success: function (data) {
            $("#institute_id").empty();
            $("#batch_id").empty();
            $('#institute_id').select2('data', null);
            $('#batch_id').select2('data', null);
            var dataOptions = '<option value="0">-- Select Institute --</option>';
            $.each(data, function (i, v) {
                dataOptions += '<option value="' + i + '">' + v + '</option>';
            });
            $("#institute_id").html(dataOptions);

        }
    });
}

function loadBatchInfo() {
    var entity_id = $("#entity_id").val();
    var course_id = $("#course_id").val();
    var institute_id = $("#institute_id").val();
    if(entity_id=='' || entity_id == '0' || course_id == '' || course_id == '0' || institute_id == '0' || institute_id == ''){
        var dataOptions = '<option value="0">-- Select Batch --</option>';
        $("#batch_id").html(dataOptions);
        return false;
    }
    $.ajax({
        type: 'POST',
        url: "<?php echo $this->request->webroot;?>" + 'traineeInfo/getBatchByEntityIdCourseIdInstituteId',
        data: {'entity_id':entity_id,'course_id':course_id,'institute_id':institute_id },
        success: function (data) {
            $("#batch_id").empty();
            $('#batch_id').select2('data', null);
            var dataOptions = '<option value="0">-- Select Batch --</option>';
            $.each(data, function (i, v) {
                dataOptions += '<option value="' + i + '">' + v + '</option>';
            });
            $("#batch_id").html(dataOptions);
        }
    });
}

function getCertificationList()
{
    var entity_id = $("#entity_id").val();
    var course_id = $("#course_id").val();
    var institute_id = $("#institute_id").val();
    var batch_id = $("#batch_id").val();
    if(entity_id=='' || entity_id == '0' || course_id == '' || course_id == '0' || institute_id == '' || institute_id == '0' || batch_id =='' || batch_id == '0'){
        $("#freeow").freeow("Error", 'Please Select All Option', { classes: ["smokey", "error"] });
        return;
    }
    $.ajax({
        type: 'POST',
        url: "<?php echo $this->request->webroot;?>" + 'traineeInfo/getCertificationByEntityIdCourseIdInstituteIdBatchId',
        data: {'entity_id':entity_id,'course_id':course_id,'institute_id':institute_id,'batch_id':batch_id },
        success: function (data) {
            $("#certification_list").html(data);
        }
    });

}

    $("#TraineeInfoSearchTraineeSummaryForm").submit(function(e){
        TRAINEE_SEARCH.getTraineeCurrentStatus();
        return false;
    });
    var TRAINEE_SEARCH = {
        deleteTrainee:function(){
            var reg_number = $("#trainee_code").val();
            if(reg_number.length == 0)
            {
                alert("Please input a valid registration number");
                return;
            }
            APP_HELPER.ajaxDeleteRecordAction('traineeInfo/deleteTrainee/'+reg_number);
        },
        getTraineeCurrentStatus:function(reg_number_from_row = '')
        {
            $("#name").html("");
            $("#institute").html("");
            $("#course").html("");
            $("#batch").html("");
            $("#image").html("");
            $("#date_of_birth").html("");
            $("#nid").html("");
            $("#father_name").html("");
            $("#mother_name").html("");
            $("#certification").html('<i class="icon-close"></i>');
            $("#assessment").html('<i class="icon-close"></i>');
            $("#enrollment").html('<i class="icon-close"></i>');
            $("#attendance_percentage").html("0");
            if(reg_number_from_row == ''|| reg_number_from_row == null || typeof (reg_number_from_row) =='undefined'){
                var reg_number = $("#trainee_code").val();
            }
           else{
                var reg_number = reg_number_from_row;
            }
            if(reg_number.length == 0)
            {
                $("#freeow").freeow("Error", 'Please input a valid registration number', { classes: ["smokey", "error"] });
                return;
            }
            APP_HELPER.ajaxSubmitDataCallback('traineeInfo/getCurrentCurrentStatus', {'reg_number': reg_number}, function(response){
                if(response.reg_count == 0 || response.reg_data == 0)
                {
                    $("#freeow").freeow("Error", 'Registered Number Does not Exits.', { classes: ["smokey", "error"] });
                    $(".content_part").hide();
                    $("#name").html("");
                    $("#institute").html("");
                    $("#course").html("");
                    $("#batch").html("");
                    $("#image").html("");
                    $("#certification").html('');
                    $("#assessment").html('');
                    $("#enrollment").html('');
                    $("#attendance_percentage").html("");
                }
                else
                {
                    $("#profileModal").modal("show");
                    $("#name").html(response.trainee_name);
                    $("#institute").html(response.institute_name);
                    $("#image").html('<img style="max-width:250px" src="<?php echo $this->request->webroot?>'+response.image_file_name+'" alt="No Image Found">');
                    $("#mobile").html('+880'+response.mobile);
                    $("#gender").html(response.gender);
                    $("#date_of_birth").html(response.date_of_birth);
                    $("#nid").html(response.nid);
                    $("#course_objective").html(response.course_objective);
                    $("#registration_number").html(response.registration_number);


                    //get course info
                    $("#course").html(response.course_name);
                    $("#course_duration_month").html(response.course_duration_month);
                    $("#course_duration_details").html(response.course_duration_details);

                    //get batch info
                    $("#batch").html(response.batch_number);
                    $("#batch_start_date").html(response.batch_start_date);
                    $("#batch_end_date").html(response.batch_end_date);
                    $("#batch_training_location").html(response.batch_training_location);



                    if(parseInt(response.approval_status) == 1 || response.approval_status == true)
                    {
                        $("#certification").html('<i class="icon-checkmark-circle2" style="color:green"></i>');
                    }

                    if(parseInt(response.assessment_score) == 100)
                    {
                        $("#assessment").html('<i class="icon-checkmark-circle2" style="color:green"></i>&nbsp; Competent');
                    } else if (parseInt(response.assessment_score) == 50)
                    {
                        $("#assessment").html('<i class="icon-checkmark-circle2" style="color:green"></i>&nbsp; Not Competent');
                    }

                    if(parseInt(response.enrollment) == 1)
                    {
                        $("#enrollment").html('<i class="icon-checkmark-circle2" style="color:green"></i>');
                        $('#enrollment_start_date').html(response.enrollment_start_date);
                        $('#enrollment_end_date').html(response.enrollment_end_date);
                    }
                    $("#attendance_percentage").html((response.attendance_percentage).toFixed(2));
                }
            });
        }
    };

    $(function(){
        $("#entity_id").bind('change',function(){
            $("#course_lebel_id").hide();
            $("#institute_lebel_id").hide();
            $("#batch_lebel_id").hide();
            loadCourseInfo();
            loadInstituteInfo();
            loadBatchInfo();
        });
        $("#course_id").bind('change',function(){
            var course_id = $("#course_id").val();
            $.ajax({
                type: 'POST',
                url: "<?php echo $this->request->webroot;?>" + 'traineeInfo/getCourseInfoByCourseId/' + course_id,
                data: { },
                success: function (data) {
                    $("#course_lebel_id").show();
                    $("#institute_lebel_id").hide();
                    $("#batch_lebel_id").hide();
                    $("#course_lebel_id").html('<p>' + data + '</p>');
                }
            });
            loadInstituteInfo();
            loadBatchInfo();
        });
        $("#institute_id").bind('change',function(){
            var institute_id = $("#institute_id").val();
            $.ajax({
                type: 'POST',
                url: "<?php echo $this->request->webroot;?>" + 'traineeInfo/getInstituteInfoByInstituteId/' + institute_id,
                data: { },
                success: function (data) {
                    $("#institute_lebel_id").show();
                    $("#batch_lebel_id").hide();
                    $("#institute_lebel_id").html('<p>' + data + '</p>');
                }
            });
            loadBatchInfo();
        });
        $("#batch_id").bind('change',function(){
            var batch_id = $("#batch_id").val();
            $.ajax({
                type: 'POST',
                url: "<?php echo $this->request->webroot;?>" + 'traineeInfo/getBatchInfoByBatchId/' + batch_id,
                data: { },
                success: function (data) {
                    $("#batch_lebel_id").show();
                    $("#batch_lebel_id").html('<p>' + data + '</p>');
                }
            });
        });
    });

</script>

