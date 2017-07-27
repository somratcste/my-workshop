<?php
App::uses('AppModel', 'Model');
/**
 * TrainingCertificate Model
 *
 */
class TrainingCertificate extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'training_certificates';

    public function getCertificateInfoByRegistrationNumber($regNumber)
    {
            return $this->find('first', array('conditions'=>array('registration_number'=>$regNumber)));

    }

    public function removeCertificateInformation($conditions)
    {
        return $this->deleteAll($conditions);
    }
    
    public function getTraineesCertificateInfo($batch_id = 0)
    {
    	$query = "SELECT
    				ti.reference_number,
			    	ti.registration_number,
			    	ti.trainee_name,
			    	ti.gender,
			    	ti.mobile,
			    	ti.image_file_name,
			    	ta.assessment_title,
			    	ta.assessment_date,
			    	ta.total_marks,
			    	ta.assessment_score,
			    	tc.final_grade,
			    	tc.approval_status,
			    	tc.certification_date,
			    	tc.pass_percentage,
			    	tc.id
			    FROM trainee_infos as ti
			    JOIN training_assessments as ta ON ti.registration_number = ta.registration_number
    			LEFT JOIN training_certificates as tc ON ti.registration_number = tc.registration_number
    			WHERE ti.batch_info_id = '$batch_id' and ti.enrollment_status = 1 and ta.assessment_score in (100, 50) order by ti.registration_number";
    	return $this->query($query);
    }
    
    public function getPendingCertificateList()
    {
    	$query = "SELECT
					cd.certification_date,    			
    				et.name,
					ti.institute_name,
					ci.course_name,
					bi.batch_number,
    				bi.id,
    				count(cd.registration_number) as total_trainees 
    			FROM
    				training_certificates as cd
    			JOIN 
    				entity as et on cd.entity_id = et.id
				JOIN 
    				training_institutes as ti on cd.training_institute_id = ti.id
				JOIN 
    				course_info as ci on cd.course_info_id = ci.id
				JOIN 
    				batch_info as bi on cd.batch_info_id = bi.id
    			WHERE
    				 cd.approval_status = 0 
    			GROUP BY 
    				cd.batch_info_id 
    			ORDER BY 
    				cd.entity_id";
    	return $this->query($query);
    }
    
    public function getCertifiedTraineesRegistrationNumbers($entityId = 0, $trainingInstituteId = 0)
    {
    	$ti_condition = "";
    	if($trainingInstituteId > 0)
    	{
    		$ti_condition = " AND training_institute_id = '$trainingInstituteId'";
    	}
    	$query = "SELECT registration_number
			    	FROM training_certificates
			    	WHERE final_grade = 'Yes'
			    	AND entity_id = '$entityId'" . $ti_condition;
    	//
    	return $this->query($query);
    }
    
    public function certifiedCount($entityId = 0, $instituteId = 0, $courseId = 0, $batchId = 0)
    {
    	$certificate_count = $this->find('count', array('conditions'=>array('entity_id'=>$entityId, 'training_institute_id'=>$instituteId, 'course_info_id'=>$courseId, 'batch_info_id'=>$batchId, 'final_grade'=>'Yes')));
    	return $certificate_count;
    }
    
    public function certifiedTraineeForBill($entityId = 0, $instituteId = 0, $courseId = 0, $batchId = 0)
    {
    	$certificate_trainees = $this->find('all', array('conditions'=>array('entity_id'=>$entityId, 'training_institute_id'=>$instituteId, 'course_info_id'=>$courseId, 'batch_info_id'=>$batchId)));
    	return $certificate_trainees;
    }

    public function getBatchWiseCertificateData()
    {
        $query="SELECT
                        entity.id,
                        entity.name,
                        ti.id,
                        ti.institute_name,
                        ci.id,
                        ci.course_name,
                        bi.id,
                        bi.batch_number,
                        tc.certification_date,
                        count(tc.id) as certification_count
                 FROM training_certificates as tc
                 join entity on tc.entity_id=entity.id
                 join training_institutes as ti on tc.training_institute_id = ti.id
                 join course_info as ci on tc.course_info_id = ci.id
                join batch_info as bi on tc.batch_info_id = bi.id
                group by tc.batch_info_id";

        return $this->query($query);
    }

    public function getCertificationTraineeList($batch_id = 0)
    {
        $query = "SELECT
        			ti.reference_number,
			    	ti.registration_number,
			    	ti.trainee_name,
			    	ti.date_of_birth,
			    	ti.gender,
			    	ti.mobile,
			    	ti.image_file_name,
			    	tc.id,
			    	tc.approval_status
			    FROM trainee_infos as ti
			    LEFT JOIN training_certificates as tc ON ti.registration_number = tc.registration_number
			    WHERE ti.batch_info_id = '$batch_id' and ti.enrollment_status = 1 and ti.registration_number = tc.registration_number order by ti.registration_number";
        return $this->query($query);
    }
    
    //
    public function getCertifiedTrainees($entityId = 0, $instituteId = 0, $courseId = 0, $batchId = 0)
    {
    	$query= "SELECT
    				ti.reference_number,
    				ti.registration_number,
    				ti.trainee_name,
			    	ti.gender,
			    	ti.mobile,
			    	ti.image_file_name,
    				tc.certification_date,
			    	tc.final_grade,
			    	tc.approval_status
    			FROM
    				training_certificates as tc
    			JOIN trainee_infos as ti ON ti.registration_number = tc.registration_number
    			WHERE ti.entity_id = $entityId AND 
    				  ti.training_institute_id = $instituteId AND 
    				  ti.course_info_id = $courseId AND 
    				  ti.batch_info_id = $batchId
    			ORDER BY ti.reference_number";
        return $this->query($query);
    }

    //
    public function getCertifiedTraineesRegistrationNumberByBatch($entityId = 0, $instituteId = 0, $courseId = 0, $batchId = 0)
    {
    	$query = "SELECT registration_number
    			  FROM training_certificates
    			  WHERE final_grade = 'Yes' AND
						entity_id = $entityId AND
    					training_institute_id = $instituteId AND
    					course_info_id = $courseId AND
    					batch_info_id = $batchId
    			  ORDER BY registration_number";
    	
    	$result = $this->query($query);
    	$trainee_list = array();
    	foreach($result as $trainee)
    	{
    		$trainee_list[] = $trainee['training_certificates']['registration_number'];
    	}
    	return $trainee_list;
    }
    
    //
    public function getCertifiedTraineeCount($entityId = 0, $entityLevel = 0)
    {
    	if($entityId != 0 && $entityLevel > 6)
    	{
    		return $this->find('count', array(
    				'fields' => 'DISTINCT registration_number',
    				'conditions'=>array('entity_id'=>$entityId),
    				'recursive'=>-1));
    	}
    	else
    	{
    		return $this->find('count', array(
    				'fields' => 'DISTINCT registration_number',
    				'recursive'=>-1));
    	}
    }
    
    public function getCertifiedTraineeCountTI($entityId = 0, $instituteId = 0)
    {
    	return $this->find('count', array(
    				'fields' => 'DISTINCT registration_number',
    				'conditions'=>array('entity_id'=>$entityId, 'training_institute_id'=>$instituteId),
    				'recursive'=>-1));
    }
    
    //
    public function getTraineeCountByEntityType($entityType, $dateStart, $dateEnd)
    {
    	$countQuery = "SELECT count(tc.registration_number) AS trainee_count 
    	 			   FROM `training_certificates` AS tc 
					   LEFT JOIN entity AS e ON tc.entity_id = e.id
					   WHERE e.type_id = '$entityType' AND tc.final_grade = 'Yes' AND (tc.certification_date BETWEEN '$dateStart 00:00:00' AND '$dateEnd 23:59:59')";
    
    	return $this->query($countQuery);
    }
    
    //
    public function getFemaleTraineeCountByEntityType($entityType, $dateStart, $dateEnd)
    {
    	$countQuery = "SELECT count(ti.registration_number) AS female_trainee_count
				    	FROM `training_certificates` AS tc
				    	LEFT JOIN trainee_infos AS ti ON tc.registration_number = ti.registration_number
				    	LEFT JOIN entity AS e ON tc.entity_id = e.id
				    	WHERE e.type_id = '$entityType' AND 
				    	 	  tc.final_grade = 'Yes' AND 
				    	 	  ti.gender LIKE 'f%' AND 
				    	 	  (tc.certification_date BETWEEN '$dateStart 00:00:00' AND '$dateEnd 23:59:59')";
    	
    	return $this->query($countQuery);
    }
    
	//
    public function getTraineeCountByEntities($dateStart, $dateEnd)
    {
    	$countQuery = "SELECT e.short_name, 
    						  COUNT(ti.registration_number) as total_trainee,  
    						  COUNT(case when TRIM(ti.gender) LIKE 'M%' then 1 end) as male, 
    						  COUNT(case when TRIM(ti.gender) LIKE 'F%' then 1 end) as female
    					FROM training_certificates as tc
    					LEFT JOIN trainee_infos AS ti ON tc.registration_number = ti.registration_number
    					LEFT JOIN `entity` AS e ON e.id = tc.entity_id
    					WHERE tc.final_grade = 'Yes' AND 
    						  (tc.certification_date BETWEEN '$dateStart 00:00:00' AND '$dateEnd 23:59:59')
    					GROUP BY e.id
    					ORDER BY e.type_id";
    
    	return $this->query($countQuery);
    }
    
    public function getPaymentCertifiedTraineeCount($entityId = 0, $instituteId = 0, $courseId = 0, $batchId = 0)
    {
    	$query = "SELECT count(tc.registration_number) as certifiedTrainees, tc.final_grade
    	FROM training_certificates as tc
    	JOIN trainee_infos as ti ON tc.registration_number = ti.registration_number
    	WHERE tc.entity_id = $entityId AND
    	tc.training_institute_id = $instituteId AND
    	tc.course_info_id = $courseId AND
    	tc.batch_info_id = $batchId";
    	return $this->query($query);
    }

    public function nameOfCertification($entityId = 0,  $courseId = 0, $instituteId = 0, $batchId = 0)
    {
        $query= "SELECT
    				ti.registration_number,
    				ti.trainee_name,
			    	ti.mobile
    			FROM
    				training_certificates as tc
    			JOIN trainee_infos as ti ON (ti.entity_id = tc.entity_id AND ti.training_institute_id = tc.training_institute_id AND ti.course_info_id = tc.course_info_id AND ti.batch_info_id = tc.batch_info_id AND ti.registration_number = tc.registration_number)
    			WHERE ti.entity_id = $entityId AND 
    				  ti.training_institute_id = $instituteId AND 
    				  ti.course_info_id = $courseId AND 
    				  ti.batch_info_id = $batchId AND 
    				  tc.approval_status = 1
    			ORDER BY ti.trainee_name";
        $raw_data =  $this->query($query);
        $data = array();
        $i = 0;
        foreach($raw_data as $raw){
            $data[$i]['registration_number'] = $raw['ti']['registration_number'];
            $data[$i]['mobile'] = $raw['ti']['mobile'];
            $data[$i]['trainee_name'] = $raw['ti']['trainee_name'];
            $i++;
        }
        return $data;

    }

}
