<?php
App::uses('AppController', 'Controller');
App::uses('ConnectionManager', 'Model');
App::import('Vendor', 'php-excel-reader/excel_reader2');


class TraineeInfoPreController extends AppController
{
    public function addExcelSheet()
    {
        set_time_limit(0);

        if (!empty($this->request->data ['TraineeInfoPre'] ['file'])) {

            $file = $this->request->data ['TraineeInfoPre'] ['file'] ['tmp_name'];
            try {
                $conn = ConnectionManager::getDataSource('default');

                $data     = new Spreadsheet_Excel_Reader($file, true);
                $temp     = $data->dumptoarray();
                //echo "<pre>";
                $rowCount = count($temp);
                $head     = $temp [1];

                for ($i = 2; $i <= $rowCount; $i++) {

                    for ($j = 1; $j <= count($head); $j++) {
                        //$rowData [$head [$j]] = $temp[$i][$j] ;
                        $$head[$j] = $temp[$i][$j];

                    }

                    $query = "INSERT INTO `trainee_infos_bk` (`id`, `entity_id`, `training_institute_id`, `course_info_id`, `batch_info_id`, `registration_number`, `reference_number`, `trainee_name`, `gender`, `nid`, `bcn`, `date_of_birth`, `present_address`, `present_post_code`, `present_district`, `per_address`, `per_post_code`, `per_district`, `home_district`, `home_upazilla`, `mobile`, `email`, `religion`, `ethnic_group`, `highest_class_completed`, `highest_class_completed_year`, `is_employed`, `year_of_experience`, `family_monthly_income`, `is_physically_challenged`, `challenge_remarks`, `mother_name`, `mother_education_level`, `mother_occupation`, `father_name`, `father_education_level`, `father_occupation`, `father_annual_income`, `have_family_owned_home`, `have_family_owned_land`, `number_of_siblings`, `image_file_name`, `image_dir`, `enrollment_status`, `transaction_type`, `created`, `created_by`, `start_date`, `end_date`, `modified`, `modified_by`) VALUES 
                              ('{$id}','{$entity_id}','{$training_institute_id}','{$course_info_id}','{$batch_info_id}','{$registration_number}','{$reference_number}','{$trainee_name}','{$gender}','null','null','null','null','{$present_post_code}','".str_replace("'",'"',$present_district)."','null','{$per_post_code}','".str_replace("'",'"',$per_district)."','".str_replace("'",'"',$home_district)."','".str_replace("'",'"',$home_upazilla)."','{$mobile}','null','{$religion}','{$ethnic_group}','{$highest_class_completed}','null','null','null','{$family_monthly_income}','{$is_physically_challenged}','null','null','null','null','null','null','null','null','null','null','{$number_of_siblings}','null','null','null','null','null','null','null','null','null','null')";

                    $stmt = $conn->execute($query);

                }



            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }

    }
}