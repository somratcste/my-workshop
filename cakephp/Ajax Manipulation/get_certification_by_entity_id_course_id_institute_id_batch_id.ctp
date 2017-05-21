<div class="panel panel-default" style="margin-top: 10px;">
    <div class="panel-heading">
        <h6 class="panel-title">
            <i class="icon-paragraph-right2"></i> SEIP Certification List
        </h6>
    </div>
    <div class="row">
        <div class="col-sm-12" style="font-size:15px;">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tbody>
                    <tr>
                        <th>Serial No</th>
                        <th>Name</th>
                        <th>Registration No.</th>
                        <th>Mobile Number</th>
                        <th>View</th>
                    </tr>
                    <?php $i=1; ?>
                    <?php foreach ($all_certifications as $all_certification): ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo h($all_certification['trainee_name']); ?>&nbsp;</td>
                            <td><?php echo h($all_certification['registration_number']); ?>&nbsp;</td>
                            <td><?php echo h($all_certification['mobile']); ?>&nbsp;</td>
                            <td style="cursor:pointer" onclick="TRAINEE_SEARCH.getTraineeCurrentStatus('<?php echo $all_certification['registration_number']?>')"><i class="icon-eye3"></i></td>
                        </tr>

                    <?php $i++; endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
