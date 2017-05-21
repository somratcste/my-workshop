<?php
App::uses('AppModel', 'Model');
/**
 * MapCourseTrainer Model
 *
 */
class MapCourseTrainer extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'map_course_trainers';
	
	public function getMappedTrainersFormatted($entityId = 0, $instituteId = 0, $courseId = 0)
	{
		$query = "SELECT
					ti.id,
					ti.trainer_name,
					ti.mobile,
					ti.email
				FROM
					map_course_trainers mcr join trainer_infos as ti on ti.id = mcr.trainer_info_id
				WHERE 
					mcr.entity_id = '$entityId' and mcr.training_institute_id = '$instituteId' and mcr.course_info_id = '$courseId' ";
	
		$raw_data = $this->query($query);
		$updated_data = array();
		foreach($raw_data as $raw)
		{
		$updated_data[$raw['ti']['id']] = $raw['ti']['trainer_name'] .' | '.$raw['ti']['mobile'] .' | '.$raw['ti']['email'];
		}
		return $updated_data;
	}
	
	//
	public function getMappedTrainersForDeletion($entityId, $instituteId, $courseId)
	{
		return $this->find('list', array('conditions'=>array('entity_id'=>$entityId, 'training_institute_id'=>$instituteId, 'course_info_id'=>$courseId), 'recursive'=>-1));
	}
	
	public function getMappedTrainers($entityId = 0, $instituteId = 0, $courseId = 0)
	{
		$query = "SELECT
					ti.id,
					ti.trainer_name,
					ti.mobile,
					ti.email
				FROM
					map_course_trainers mcr join trainer_infos as ti on ti.id = mcr.trainer_info_id
				WHERE
					mcr.entity_id = '$entityId' and mcr.training_institute_id = '$instituteId' and mcr.course_info_id = '$courseId' ";
				
		$raw_data = $this->query($query);
		$updated_data = array();
		foreach($raw_data as $raw)
		{
			$updated_data[$raw['ti']['id']] = $raw['ti']['trainer_name'];
		}
		return $updated_data;
	}

	public function getMappedInstitute($entity_id =0, $course_id = 0)
    {
        $query = "SELECT
                    ti.id,
					ti.institute_name
				FROM
					map_institute_courses mcr join training_institutes as ti on ti.id = mcr.training_institute_id
				WHERE 
					mcr.entity_id = '$entity_id' and mcr.course_info_id = '$course_id' ";

        $raw_data = $this->query($query);
        $updated_data = array();
        foreach($raw_data as $raw)
        {
            $updated_data[$raw['ti']['id']] = $raw['ti']['institute_name'] ;
        }
        return $updated_data;
    }

}
