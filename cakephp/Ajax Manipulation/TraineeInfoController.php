<?php

App::uses('AppController', 'Controller');
App::uses('ConnectionManager', 'Model');

/**
 * TraineeInfo Controller
 *
 * @property TraineeInfo $TraineeInfo
 */
class TraineeInfoController extends AppController {

    var $uses = array(
        'TraineeInfo',
        'Entity',
        'TrainingInstitute',
        'CourseInfo',
        'BatchInfo',
        'XCity',
        'XSubDistrict',
        'XDistrict',
        'XDivision',
        'XReligion',
        'XEthnicGroup',
        'XEducationalQualification'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        //$this->Paginator->settings = array('limit' => 10000000000000000);
        $this->Auth->allow();
    }

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->_setEntitiesByRole();
        $this->_setPersonalInfoModels();
    }

    /**
     * Load Pre Registered List
     */
    public function itemList() {
        $entityId = $this->request->data['entity_id'];
        $instituteId = $this->request->data['institute_id'];
        $courseId = $this->request->data['course_id'];
        $batchId = $this->request->data['batch_id'];

        $this->TraineeInfo->recursive = 0;
        $conditions = array('TraineeInfo.entity_id' => $entityId,
            'TraineeInfo.training_institute_id' => $instituteId,
            'TraineeInfo.course_info_id' => $courseId,
            'TraineeInfo.batch_info_id' => $batchId,
            'TraineeInfo.enrollment_status' => 0);
        $this->set('traineeRegistereds', $this->Paginator->paginate(null, $conditions));
    }

    public function notEnrolledItemList() {
        $entityId = $this->request->data['entity_id'];
        $instituteId = $this->request->data['institute_id'];
        $courseId = $this->request->data['course_id'];
        $batchId = $this->request->data['batch_id'];

        $this->TraineeInfo->recursive = 0;
        $conditions = array('TraineeInfo.entity_id' => $entityId,
            'TraineeInfo.training_institute_id' => $instituteId,
            'TraineeInfo.course_info_id' => $courseId,
            'TraineeInfo.batch_info_id' => $batchId,
            'TraineeInfo.enrollment_status' => 0);
        $this->set('traineeRegistereds', $this->Paginator->paginate(null, $conditions));
        if (($this->request->query['downloadxls'])) {
            $this->set('enroll', 'No');
            $this->set('entity_name', $this->request->data['entity_name']);
            $this->set('institute_name', $this->request->data['institute_name']);
            $this->set('course_name', $this->request->data['course_name']);
            $this->set('batch_id', $this->request->data['batch_id']);
            $this->layout = false;
            $this->render('/TraineeInfo/enrolled_item_list_xl');
        }
    }

    public function enrolledItemList() {
        $entityId = $this->request->data['entity_id'];
        $instituteId = $this->request->data['institute_id'];
        $courseId = $this->request->data['course_id'];
        $batchId = $this->request->data['batch_id'];

        $this->TraineeInfo->recursive = 0;
        $conditions = array('TraineeInfo.entity_id' => $entityId,
            'TraineeInfo.training_institute_id' => $instituteId,
            'TraineeInfo.course_info_id' => $courseId,
            'TraineeInfo.batch_info_id' => $batchId,
            'TraineeInfo.enrollment_status' => 1);
        $this->set('traineeRegistereds', $this->Paginator->paginate(null, $conditions));
        $this->set('role', $this->getUserRole());

// Temporary 7 days for BACI 08-12-16
        $this->set('logged_user', $this->Auth->user());
// Temporary 7 days for BACI 08-12-16
        if (($this->request->query['downloadxls'])) {
            $this->set('enroll', 'Yes');
            $this->set('entity_name', $this->request->data['entity_name']);
            $this->set('institute_name', $this->request->data['institute_name']);
            $this->set('course_name', $this->request->data['course_name']);
            $this->set('batch_id', $this->request->data['batch_id']);
            $this->layout = false;
            $this->render('/TraineeInfo/enrolled_item_list_xl');
        }
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view() {
        $id = 0;
        if (isset($this->request->data['ShowObject'])) {
            $id = $this->request->data['ShowObject']['id'];
        } else {
            $this->Session->setFlash(__('Invalid Candidate Found!!'), 'flash_fail');
            $this->render('/Errors/custom_error');
            return false;
        }

        $this->TraineeInfo->id = $id;
        if (!$this->TraineeInfo->exists()) {
            throw new NotFoundException(__('Invalid Candidate'));
        }

        //
        $this->request->data = $this->TraineeInfo->read(null, $id);

        $this->_setEntitiesByRole();
        $this->_setLocationSelection();
        $this->_setPersonalInfoModels();
    }

    public function tabView() {
        $id = 0;
        if (isset($this->request->data['ShowObject'])) {
            $id = $this->request->data['ShowObject']['id'];
        } else {
            $this->Session->setFlash(__('Invalid Candidate Found!!'), 'flash_fail');
            $this->render('/Errors/custom_error');
            return false;
        }

        $this->TraineeInfo->id = $id;
        if (!$this->TraineeInfo->exists()) {
            throw new NotFoundException(__('Invalid Candidate'));
        }

        //
        $this->request->data = $this->TraineeInfo->read(null, $id);
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->TraineeInfo->create();
            if (isset($this->request->data['TraineeInfo']['image_file_name']) && $this->request->data['TraineeInfo']['image_file_name']['size'] == 0) {
                $this->request->data['TraineeInfo']['image_file_name'] = "";
            }

            if ($this->TraineeInfo->save($this->request->data)) {
                $this->Session->setFlash(__('The candidate has been saved'), 'flash_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Candidate could not be saved. Please, try again.'), 'flash_fail');
            }
        }

        $this->_setEntitiesByRole();
        $this->_setLocationSelection();
        $this->_setPersonalInfoModels();
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $id = 0;
        if (isset($this->request->data['EditObject'])) {
            $id = $this->request->data['EditObject']['id'];
        } else if (isset($this->request->data['TraineeInfo']['id']) && !empty($this->request->data['TraineeInfo']['id'])) {
            $id = $this->request->data['TraineeInfo']['id'];
        } else {
            $this->Session->setFlash(__('Invalid Candidate Found!!'), 'flash_fail');
            $this->render('/Errors/custom_error');
            return false;
        }

        $this->TraineeInfo->id = $id;
        if (!$this->TraineeInfo->exists()) {
            throw new NotFoundException(__('Invalid Candidate name!!'));
        }

        if (isset($this->request->data['TraineeInfo']['id']) && !empty($this->request->data['TraineeInfo']['id'])) {
            if (isset($this->request->data ['TraineeInfo'] ['image_file_name']) && $this->request->data ['TraineeInfo'] ['image_file_name'] ['size'] == 0) {
                if (isset($this->request->data['TraineeInfo']['image_file_name_prev']) && !empty($this->request->data['TraineeInfo']['image_file_name_prev'])) {
                    $this->request->data ['TraineeInfo'] ['image_file_name'] = $this->request->data['TraineeInfo']['image_file_name_prev'];
                } else {
                    $this->request->data ['TraineeInfo'] ['image_file_name'] = "";
                }
            }

            if ($this->TraineeInfo->save($this->request->data)) {
                $this->Session->setFlash(__('The Candidate has been saved.'), 'flash_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->set('error_model', 'TraineeInfo');
                $errors = json_encode($this->TraineeInfo->validationErrors);
                $this->Session->setFlash($errors, 'flash_fail');
                $this->request->data = $this->TraineeInfo->read(null, $id);
            }
        } else {
            $this->request->data = $this->TraineeInfo->read(null, $id);
        }

        $this->_setEntitiesByRole();
        $this->_setLocationSelection();
        $this->_setPersonalInfoModels();
    }

    public function tabEdit($id = null) {
        $id = 0;
        $json = array();
        if (isset($this->request->data ['EditObject'])) {
            $id = $this->request->data ['EditObject'] ['id'];
        } else if (isset($this->request->data ['TraineeInfo'] ['id']) && !empty($this->request->data ['TraineeInfo'] ['id'])) {
            $id = $this->request->data ['TraineeInfo'] ['id'];
        } else {
            $json ['status'] = 'error';
            $json ['msg'] = 'Invalid Candidate Found!!';

            echo json_encode($json);
            die();
        }

        $this->TraineeInfo->id = $id;
        if (!$this->TraineeInfo->exists()) {
            $json ['status'] = 'error';
            $json ['msg'] = 'Invalid Candidate name!!';

            echo json_encode($json);
            die();
        }

        $userProfile = $this->getUserProfile();
        $entity_shortname = $userProfile['Entity']['short_name'];
        if (isset($this->request->data ['TraineeInfo'] ['id']) && !empty($this->request->data ['TraineeInfo'] ['id'])) {
            if (!empty($this->request->data ['TraineeInfo'] ['profilephotobase64'])) {
                $img = $this->request->data ['TraineeInfo'] ['profilephotobase64'];
                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                $this->request->data['TraineeInfo']['image_file_name'] = $this->request->data['TraineeInfo']['image_file_name_prev'];
                $file = './files/trainee_info/image_file_name/' . $this->request->data['TraineeInfo']['image_file_name'];
                $success = file_put_contents($file, $data);
            } else {
                if (isset($this->request->data ['TraineeInfo'] ['image_file_name']) && $this->request->data ['TraineeInfo'] ['image_file_name'] ['size'] == 0) {
                    if (isset($this->request->data ['TraineeInfo'] ['image_file_name_prev']) && !empty($this->request->data ['TraineeInfo'] ['image_file_name_prev'])) {
                        $this->request->data ['TraineeInfo'] ['image_file_name'] = $this->request->data ['TraineeInfo'] ['image_file_name_prev'];
                    } else {
                        $this->request->data ['TraineeInfo'] ['image_file_name'] = "";
                    }
                }
            }

            if ($this->TraineeInfo->save($this->request->data)) {
                $json ['status'] = 'success';
                $json ['msg'] = 'The Candidate has been saved!!';
                echo json_encode($json);
                die();
            } else {
                $this->set('error_model', 'TraineeInfo');
                $errors = json_encode($this->TraineeInfo->validationErrors);
                $this->Session->setFlash($errors, 'flash_fail');
                $json ['status'] = 'error';
                $json ['msg'] = 'Something went wrong in submission!!';
                $json ['error'] = $this->TraineeInfo->validationErrors;

                echo json_encode($json);
                die();
            }
        } else {
            $this->request->data = $this->TraineeInfo->read(null, $id);
        }

        $this->_setEntitiesByRole();
        $this->_setLocationSelection();
        $this->_setPersonalInfoModels();
    }

    /**
     * delete method
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->TraineeInfo->id = $id;

        if (!$this->TraineeInfo->exists()) {
            throw new NotFoundException(__('Invalid Candidate'));
        }

        if ($this->TraineeInfo->delete()) {
            $this->Session->setFlash(__('Candidate deleted'), 'flash_success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Candidate was not deleted'), 'flash_fail');
        $this->redirect(array('action' => 'index'));
    }

//     /**
//      * Validate and confirm registration
//      */
//     public function registration() {
//         $db = ConnectionManager::getDataSource("default");
//         $db->begin();
//         try {
//             if ($this->request->is('post') && $this->request->data['TraineeInfo']['type'] == VALIDATION) {
//                 $entityId = $this->request->data['TraineeInfo']['entity_id'];
//                 $instituteId = $this->request->data['TraineeInfo']['training_institute_id'];
//                 $courseId = $this->request->data['TraineeInfo']['course_info_id'];
//                 $batchId = $this->request->data['TraineeInfo']['batch_info_id'];
//                 $this->loadModel('TrainingEnrollment');
//                 if ($this->TrainingEnrollment->enrolledCount($entityId, $instituteId, $courseId, $batchId) > 0) {
//                     $db->rollback();
//                     $this->_setEntitiesByRole();
//                     $this->Session->setFlash("Batch already enrolled.", 'flash_error_single_message');
//                     return;
//                 }
//                 $this->loadModel('TraineeInfoPre');
//                 $preRegistrationData = $this->TraineeInfoPre->getPreRegisteredData($entityId, $instituteId, $courseId, $batchId);
//                 $nonValidatedTrainees = array();
//                 if (count($preRegistrationData) > 0) {
//                     /* Validate National ID */
//                     $errorFlag = false;
//                     foreach ($preRegistrationData as $key => $data) {
//                         $nid = $data['TraineeInfoPre']['nid'];
//                         $bcn = $data['TraineeInfoPre']['bcn'];
//                         if (!empty($nid) && $this->TraineeInfo->checkNid($nid) > 0) {
//                             $errorFlag = true;
//                         }
//                         if (!empty($bcn) && $this->TraineeInfo->checkBcn($bcn) > 0) {
//                             $errorFlag = true;
//                         }
//                         if ($errorFlag) {
//                             $nonValidatedTrainees[] = $data;
//                             unset($preRegistrationData[$key]);
//                         }
//                         $errorFlag = false;
//                     }
//                     /* Success validation then change status to 1 */
//                     foreach ($preRegistrationData as $data) {
//                         $this->TraineeInfoPre->id = $data['TraineeInfoPre']['id'];
//                         $this->TraineeInfoPre->saveField('validation_status', 1);
//                     }
//                     /* Fail to validate then change status to 2 */
//                     foreach ($nonValidatedTrainees as $nData) {
//                         $this->TraineeInfoPre->id = $nData['TraineeInfoPre']['id'];
//                         $this->TraineeInfoPre->saveField('validation_status', 2);
//                     }
//                     $db->commit();
//                 }
//             }
//             if ($this->request->is('post') && $this->request->data['TraineeInfo']['type'] == REGISTRATION) {
//                 $entityId = $this->request->data['TraineeInfo']['entity_id'];
//                 $instituteId = $this->request->data['TraineeInfo']['training_institute_id'];
//                 $courseId = $this->request->data['TraineeInfo']['course_info_id'];
//                 $batchId = $this->request->data['TraineeInfo']['batch_info_id'];
//                 $this->loadModel('TrainingEnrollment');
//                 if ($this->TrainingEnrollment->enrolledCount($entityId, $instituteId, $courseId, $batchId) > 0) {
//                     $db->rollback();
//                     $this->_setEntitiesByRole();
//                     $this->Session->setFlash("Batch already enrolled.", 'flash_error_single_message');
//                     return;
//                 }
//                 $this->loadModel('TraineeInfoPre');
//                 $preRegistrationData = $this->TraineeInfoPre->getValidatedPreRegisteredData($entityId, $instituteId, $courseId, $batchId);
//                 $registration_number = 0;
//                 $registration_data = array();
//                 $pre_ids = array();
//                 if (count($preRegistrationData) > 0) 
//                 {
//                     foreach ($preRegistrationData as $data) {
//                         $row = $data['TraineeInfoPre'];
//                         unset($row['id']);
//                         unset($row['created']);
//                         unset($row['modified']);
//                         unset($row['created_by']);
//                         unset($row['modified_by']);
//                         $registration_number = $this->_generateRegistrationNumber($entityId, $registration_number);
//                         $row['registration_number'] = $registration_number;
//                         $row['trainee_info_pre_id'] = $data['TraineeInfoPre']['id'];
//                         $row['enrollment_status'] = 0;
//                         $registration_data[] = $row;
//                         $pre_ids[$data['TraineeInfoPre']['id']] = $row['registration_number'];
//                     }
//                 }
//                 if ($this->TraineeInfo->saveAll($registration_data)) {
//                     foreach ($pre_ids as $id => $r_number) {
//                         $this->TraineeInfoPre->id = $id;
//                         $this->TraineeInfoPre->saveField('registration_number', $r_number);
//                         $db->commit();
//                     }
//                     $this->Session->setFlash("Data has been saved", 'flash_success');
//                     $this->redirect(array('action' => 'index'));
//                 } else {
//                     $this->set('error_model', 'TraineeInfo');
//                     $errors = json_encode($this->TraineeInfo->validationErrors);
//                     $this->Session->setFlash($errors, 'flash_fail');
//                 }
//             }
//         } catch (\Exception $e) {
//             $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//         } catch (\InvalidArgumentException $e) {
//             $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//         } catch (\InvalidTypeException $e) {
//             $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//         } catch (\HttpInvalidParamException $e) {
//             $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//         }
//         $db->rollback();
//         $this->_setEntitiesByRole();
//     }

    public function traineeDeletion() {
        $userProfile = $this->getUserProfile();
        $entityId = $userProfile ['EntityResource'] ['entity_id'];
        $registration_numbers = $this->TraineeInfo->getAllRegistrationNumber($entityId);
        $this->set('registration_numbers', $registration_numbers);
    }

    public function searchTrainee() {
        $userProfile = $this->getUserProfile();
        $entityId = $userProfile ['EntityResource'] ['entity_id'];
        $registration_numbers = $this->TraineeInfo->getAllRegistrationNumber($entityId);
        $this->set('registration_numbers', $registration_numbers);
    }


    /**
     *
     */
    public function getCurrentCurrentStatus() {
        $reg_number = $this->request->data['reg_number'];
        $trainee_status = array();

        /* Registration Status */
        $reg_data = $this->TraineeInfo->isExists($reg_number);
        $trainee_status['reg_data'] = $reg_data;

        if ($reg_data > 0) {
            $reg_detail = $this->TraineeInfo->getTraineeInfoByRegistrationNumber($reg_number);

            $institute_id = $reg_detail['TraineeInfo']['training_institute_id'];
            $course_id = $reg_detail['TraineeInfo']['course_info_id'];
            $batch_id = $reg_detail['TraineeInfo']['batch_info_id'];
            $trainee_status['trainee_name'] = $reg_detail['TraineeInfo']['trainee_name'];
            $trainee_status['image_file_name'] = 'files/trainee_info/image_file_name/' . $reg_detail['TraineeInfo']['image_file_name'];
            $trainee_status['father_name'] = $reg_detail['TraineeInfo']['father_name'];
            $trainee_status['mother_name'] = $reg_detail['TraineeInfo']['mother_name'];
            $trainee_status['date_of_birth'] = $reg_detail['TraineeInfo']['date_of_birth'];
            $trainee_status['nid'] = $reg_detail['TraineeInfo']['nid'];
            $trainee_status['gender'] = $reg_detail['TraineeInfo']['gender'];
            $trainee_status['mobile'] = $reg_detail['TraineeInfo']['mobile'];
            $trainee_status['registration_number'] = $reg_detail['TraineeInfo']['registration_number'];

            /* Get Institue Information */
            $this->loadModel('TrainingInstitute');
            $institute_info = $this->TrainingInstitute->read(null, $institute_id);
            $trainee_status['institute_name'] = $institute_info['TrainingInstitute']['institute_name'];
            $trainee_status['reg_status'] = $reg_detail['TraineeInfo']['enrollment_status'];
            /* Get Course Information */
            $this->loadModel('CourseInfo');
            $course_info = $this->CourseInfo->read(null, $course_id);
            $trainee_status['course_name'] = $course_info['CourseInfo']['course_name'];
            $trainee_status['course_duration_month'] = $course_info['CourseInfo']['course_duration_month'];
            $trainee_status['course_duration_details'] = $course_info['CourseInfo']['course_duration_details'];
            $trainee_status['course_objective'] = $course_info['CourseInfo']['course_objective'];


            /* Get batch Information */
            $this->loadModel('BatchInfo');
            $batch_info = $this->BatchInfo->read(null, $batch_id);
            $trainee_status['batch_number'] = $batch_info['BatchInfo']['batch_number'];
            $trainee_status['batch_start_date'] = $batch_info['BatchInfo']['start_date'];
            $trainee_status['batch_end_date'] = $batch_info['BatchInfo']['end_date'];
            $trainee_status['batch_training_location'] = $batch_info['BatchInfo']['training_location'];

            /* Enrollemnt Status */
            $enroll_data = $this->TraineeInfo->isExists($reg_number, 1);
            $trainee_status['enrollment'] = $enroll_data;
            $this->loadModel('TrainingEnrollment');
            $enrollment_detail = $this->TrainingEnrollment->getEnrollmentInfoByRegistrationNumber($reg_number);
            $trainee_status['enrollment_start_date'] = $enrollment_detail['TrainingEnrollment']['start_date'];
            $trainee_status['enrollment_end_date'] = $enrollment_detail['TrainingEnrollment']['end_date'];

            /* Assesment Status */
            $this->loadModel('TrainingAssessment');
            $assessment_detail = $this->TrainingAssessment->getAssessmentInfoByRegistrationNumber($reg_number);
            $trainee_status['assessment_score'] = $assessment_detail['TrainingAssessment']['assessment_score'];

            /* Certification Status */
            $this->loadModel('TrainingCertificate');
            $certificate_detail = $this->TrainingCertificate->getCertificateInfoByRegistrationNumber($reg_number);
            $trainee_status['approval_status'] = $certificate_detail['TrainingCertificate']['approval_status'];

            /* Attendance Count */
            $this->loadModel('TrainingAttendanceTrainee');
            $attendance_percentage = $this->TrainingAttendanceTrainee->getAttendancePercentage($reg_number);
            $trainee_status['attendance_percentage'] = $attendance_percentage;
        }

        return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($trainee_status)));
    }

    public function getTraineeInfoByRefNmbr() {
        $ref_number = $this->request->data['ref_number'];
        $trainee_status = array();

        /* Registration Status */
        $reg_detail = $this->TraineeInfo->getTraineeInfoByRefNmbr($ref_number);
        $trainee_status['reg_data'] = $reg_detail;

        if ($reg_detail > 0) {
            $institute_id = $reg_detail['TraineeInfo']['training_institute_id'];
            $course_id = $reg_detail['TraineeInfo']['course_info_id'];
            $batch_id = $reg_detail['TraineeInfo']['batch_info_id'];
            $trainee_status['trainee_name'] = $reg_detail['TraineeInfo']['trainee_name'];
            /* Get Institue Information */
            $this->loadModel('TrainingInstitute');
            $institute_info = $this->TrainingInstitute->read(null, $institute_id);
            $trainee_status['institute_name'] = $institute_info['TrainingInstitute']['institute_name'];
            $trainee_status['reg_status'] = $reg_detail['TraineeInfo']['enrollment_status'];
            /* Get Course Information */
            $this->loadModel('CourseInfo');
            $course_info = $this->CourseInfo->read(null, $course_id);
            $trainee_status['course_name'] = $course_info['CourseInfo']['course_name'];

            /* Get batch Information */
            $this->loadModel('BatchInfo');
            $batch_info = $this->BatchInfo->read(null, $batch_id);
            $trainee_status['batch_number'] = $batch_info['BatchInfo']['batch_number'];

            /* Enrollemnt Status */
            $enroll_data = $this->TraineeInfo->isExists($reg_number, 1);
            $trainee_status['enrollment'] = $enroll_data;

            /* Attendance Count */
            $this->loadModel('TrainingAttendanceTrainee');
            $attendance_count = $this->TrainingAttendanceTrainee->attendanceDayCount($reg_number);
            $trainee_status['attendance_count'] = $attendance_count;
        }

        return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($trainee_status)));
    }

    /**
     *
     */
    public function getTraineeInfoByNID() {
        $nid = $this->request->data['nid'];
        $trainee_status = array();

        /* Registration Status */
        $reg_data = $this->TraineeInfo->isExistsNID($nid);
        $trainee_status['reg_data'] = $reg_data;

        if ($reg_data > 0) {
            $reg_detail = $this->TraineeInfo->getTraineeInfoByNID($nid);

            $institute_id = $reg_detail['TraineeInfo']['training_institute_id'];
            $course_id = $reg_detail['TraineeInfo']['course_info_id'];
            $batch_id = $reg_detail['TraineeInfo']['batch_info_id'];
            $trainee_status['trainee_name'] = $reg_detail['TraineeInfo']['trainee_name'];
            /* Get Institue Information */
            $this->loadModel('TrainingInstitute');
            $institute_info = $this->TrainingInstitute->read(null, $institute_id);
            $trainee_status['institute_name'] = $institute_info['TrainingInstitute']['institute_name'];
            $trainee_status['reg_status'] = $reg_detail['TraineeInfo']['enrollment_status'];
            /* Get Course Information */
            $this->loadModel('CourseInfo');
            $course_info = $this->CourseInfo->read(null, $course_id);
            $trainee_status['course_name'] = $course_info['CourseInfo']['course_name'];

            /* Get batch Information */
            $this->loadModel('BatchInfo');
            $batch_info = $this->BatchInfo->read(null, $batch_id);
            $trainee_status['batch_number'] = $batch_info['BatchInfo']['batch_number'];

            /* Enrollemnt Status */
            $enroll_data = $this->TraineeInfo->isExists($reg_number, 1);
            $trainee_status['enrollment'] = $enroll_data;

            /* Attendance Count */
            $this->loadModel('TrainingAttendanceTrainee');
            $attendance_count = $this->TrainingAttendanceTrainee->attendanceDayCount($reg_number);
            $trainee_status['attendance_count'] = $attendance_count;
        }

        return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($trainee_status)));
    }

    public function getTraineeInfoByBCN() {
        $bcn = $this->request->data['bcn'];
        $trainee_status = array();

        /* Registration Status */
        $reg_data = $this->TraineeInfo->isExistsBCN($bcn);
        $trainee_status['reg_data'] = $reg_data;

        if ($reg_data > 0) {
            $reg_detail = $this->TraineeInfo->getTraineeInfoByBCN($bcn);

            $institute_id = $reg_detail['TraineeInfo']['training_institute_id'];
            $course_id = $reg_detail['TraineeInfo']['course_info_id'];
            $batch_id = $reg_detail['TraineeInfo']['batch_info_id'];
            $trainee_status['trainee_name'] = $reg_detail['TraineeInfo']['trainee_name'];
            /* Get Institue Information */
            $this->loadModel('TrainingInstitute');
            $institute_info = $this->TrainingInstitute->read(null, $institute_id);
            $trainee_status['institute_name'] = $institute_info['TrainingInstitute']['institute_name'];
            $trainee_status['reg_status'] = $reg_detail['TraineeInfo']['enrollment_status'];
            /* Get Course Information */
            $this->loadModel('CourseInfo');
            $course_info = $this->CourseInfo->read(null, $course_id);
            $trainee_status['course_name'] = $course_info['CourseInfo']['course_name'];

            /* Get batch Information */
            $this->loadModel('BatchInfo');
            $batch_info = $this->BatchInfo->read(null, $batch_id);
            $trainee_status['batch_number'] = $batch_info['BatchInfo']['batch_number'];

            /* Enrollemnt Status */
            $enroll_data = $this->TraineeInfo->isExists($reg_number, 1);
            $trainee_status['enrollment'] = $enroll_data;

            /* Attendance Count */
            $this->loadModel('TrainingAttendanceTrainee');
            $attendance_count = $this->TrainingAttendanceTrainee->attendanceDayCount($reg_number);
            $trainee_status['attendance_count'] = $attendance_count;
        }

        return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($trainee_status)));
    }

//     public function deleteTrainee() {
//         if ($this->request->is('post')) {
//             $db = ConnectionManager::getDataSource("default");
//             $db->begin();
//             try {
//                 $reg_number = $this->request->data['reg_number'];
//                 /* Delete Registration */
//                 $reg_detail = $this->TraineeInfo->getTraineeInfoByRegistrationNumber($reg_number);
//                 $error = 0;
//                 // Check Enrollment Bill
//                 $this->loadModel('TrainingPaymentEnrollment');
//                 $enrollment_count = $this->TrainingPaymentEnrollment->find('count', array('conditions' => array('batch_id' => $reg_detail['TraineeInfo']['batch_info_id'])));
//                 if ($enrollment_count > 0) 
//                 {
//                     if ($error == 0) 
//                     {
//                         $this->Session->setFlash("Deletion is not allowed after bill submission. Please contact with SEIP.", 'flash_error_single_message');
//                     }
//                     $error = 1;
//                 }
//                 if ($error == 0)
//                 {
// 	                $this->loadModel('TrainingAssessment');
// 	                $assessment_count = $this->TrainingAssessment->find('count', array('conditions' => array('batch_info_id' => $reg_detail['TraineeInfo']['batch_info_id'])));
// 	                if ($assessment_count > 0) 
// 	                {
// 	                	$error = 1;
// 	                	$this->Session->setFlash("Deletion is not allowed after assessment. Please contact with SEIP.", 'flash_error_single_message');
// 	                }
//                 }
//                 /* $this->loadModel('TrainingEnrollment');
//                   $enrolled_first = $this->TrainingEnrollment->find('first', array('conditions'=>array('batch_info_id'=>$reg_detail['TraineeInfo']['batch_info_id'])));
//                   if (empty ( $enrolled_first )) {
//                   $this->loadModel ( 'BatchInfo' );
//                   $batch_data = $this->BatchInfo->read ( null, $reg_detail ['TraineeInfo'] ['batch_info_id'] );
//                   $now = time ();
//                   $batch_start_date = strtotime ( $batch_data ['BatchInfo'] ['start_date'] );
//                   $datediff = $now - $batch_start_date;
//                   $days = floor ( $datediff / (60 * 60 * 24) );
//                   } else {
//                   $now = time ();
//                   $batch_start_date = strtotime ( $enrolled_first ['TrainingEnrollment'] ['start_date'] );
//                   $datediff = $now - $batch_start_date;
//                   $days = floor ( $datediff / (60 * 60 * 24) );
//                   }
//                   if($days >= 15)
//                   {
//                   if($error == 0)
//                   {
//                   $this->Session->setFlash("Deletion session is over. Please contact with SEIP.", 'flash_error_single_message');
//                   }
//                   $error = 1;
//                   }
//                  */
//                 if ($error == 0) {
//                     $log_data['LogTraineeInfo'] = $reg_detail['TraineeInfo'];
//                     unset($log_data['LogTraineeInfo']['id']);
//                     $this->loadModel('LogTraineeInfo');
//                     $this->LogTraineeInfo->create();
//                     $this->loadModel('TrainingAttendanceTrainee');
//                     $attendanceDays = $this->TrainingAttendanceTrainee->attendanceDays($reg_number);
//                     $log_data['LogTraineeInfo']['attendance_count'] = count($attendanceDays);
//                     $log_data['LogTraineeInfo']['log_type'] = "DELETE";
//                     $log_data['LogTraineeInfo']['transaction_type'] = 0;
//                     if (!empty($reg_detail) && $this->LogTraineeInfo->save($log_data)) {
//                         //
//                         $this->TraineeInfo->id = $reg_detail['TraineeInfo']['id'];
//                         $this->TraineeInfo->delete();
//                         //
//                         foreach ($attendanceDays as $day) {
//                             $this->TrainingAttendanceTrainee->id = $day['TrainingAttendanceTrainee']['id'];
//                             $this->TrainingAttendanceTrainee->delete();
//                         }
//                         $db->commit();
//                         $this->Session->setFlash("Record has been deleted successfully", "flash_success");
//                         return $this->redirect(array('action' => 'traineeDelete'));
//                     } else {
//                         $this->Session->setFlash("Failed to remove. ", "flash_single_error_message");
//                     }
//                 }
//             } catch (\Exception $e) {
//                 $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//             } catch (\InvalidArgumentException $e) {
//                 $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//             } catch (\InvalidTypeException $e) {
//                 $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//             } catch (\HttpInvalidParamException $e) {
//                 $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//             }
//             $db->rollback();
//         }
//         return $this->redirect(array('action' => 'traineeDelete'));
//     }
//     public function traineeDelete() {
//         $userProfile = $this->getUserProfile();
//         $entityId = $userProfile ['EntityResource'] ['entity_id'];
//         $registration_numbers = $this->TraineeInfo->getAllRegistrationNumber($entityId);
//         $this->set('registration_numbers', $registration_numbers);
//     }
//     public function traineeTransfer() {
//         if ($this->request->is('post')) {
//             $db = ConnectionManager::getDataSource("default");
//             $db->begin();
//             try {
//                 $error = 0;
//                 if(empty($this->request->data['TraineeInfo']['trainee_code']))
//                 {
//                 	$this->Session->setFlash("Missing Trainee Information.", 'flash_error_single_message');
//                 	$error = 1;
//                 }
//                 if(empty($this->request->data['TraineeInfo']['course_info_id']))
//                 {
//                 	$this->Session->setFlash("Missing Course Information.", 'flash_error_single_message');
//                 	$error = 1;
//                 }
//                 if(empty($this->request->data['TraineeInfo']['batch_info_id']))
//                 {
//                 	$this->Session->setFlash("Missing Batch Information.", 'flash_error_single_message');
//                 	$error = 1;
//                 }
//                 $reg_detail = $this->TraineeInfo->getTraineeInfoByRegistrationNumber($this->request->data['TraineeInfo']['trainee_code']);
//                 // Check Enrollment Bill
//                 $this->loadModel('TrainingPaymentEnrollment');
//                 $enrollment_count = $this->TrainingPaymentEnrollment->find('count', array('conditions' => array('batch_id' => $reg_detail['TraineeInfo']['batch_info_id'])));
//                 if ($enrollment_count > 0) 
//                 {
//                     if ($error == 0) 
//                     {
//                         $this->Session->setFlash("Transfer is not allowed after bill submission. Please contact with SEIP.", 'flash_error_single_message');
//                     }
//                     $error = 1;
//                 }
//                 $this->loadModel('TrainingAssessment');
//                 $assessment_count = $this->TrainingAssessment->find('count', array('conditions' => array('batch_info_id' => $reg_detail['TraineeInfo']['batch_info_id'])));
//                 if ($assessment_count > 0) 
//                 {
//                 	if ($error == 0) 
//                 	{
//                 		$this->Session->setFlash("Transfer is not allowed after assessment. Please contact with SEIP.", 'flash_error_single_message');
//                 	}
//                 	$error = 1;
//                 }
//                 $course_info_id = $this->request->data['TraineeInfo']['course_info_id'];
//                 $this->loadModel('CourseInfo');
//                 $course = $this->CourseInfo->read(null, $course_info_id);
//                 $course_limit = $course['CourseInfo']['students_per_batch'];
//                 $enrolled_count = $this->TraineeInfo->find('count', array('conditions' => array('batch_info_id' => $this->request->data['TraineeInfo']['batch_info_id'], 'enrollment_status'=>1)));
//                 $course_limit = FLOOR($course_limit + ($course_limit * ENROLLMENT_GRACE) / 100);
//                 if ($enrolled_count > $course_limit) 
//                 {
//                     if ($error == 0) 
//                     {
//                         $this->Session->setFlash("Trainee per batch is exceeds limit. Please contact with SEIP.", 'flash_error_single_message');
//                     }
//                     $error = 1;
//                 }
//                 $enrolled_first = $this->TraineeInfo->find('first', array('conditions' => array('batch_info_id' => $reg_detail['TraineeInfo']['batch_info_id'], 'enrollment_status'=>1)));
//                 if (empty($enrolled_first)) 
//                 {
//                     $this->loadModel('BatchInfo');
//                     $batch_data = $this->BatchInfo->read(null, $reg_detail ['TraineeInfo'] ['batch_info_id']);
//                     $now = time();
//                     $batch_start_date = strtotime($batch_data ['BatchInfo'] ['start_date']);
//                     $datediff = $now - $batch_start_date;
//                     $days = floor($datediff / (60 * 60 * 24));
//                 } 
//                 else 
//                 {
//                     $now = time();
//                     $batch_start_date = strtotime($enrolled_first ['TraineeInfo'] ['start_date']);
//                     $datediff = $now - $batch_start_date;
//                     $days = floor($datediff / (60 * 60 * 24));
//                 }
//                 if ($days >= 15) 
//                 {
//                     if ($error == 0) 
//                     {
//                         $this->Session->setFlash("Transfer session is over. Please contact with SEIP.", 'flash_error_single_message');
//                     }
//                     $error = 1;
//                 }
//                 if ($error == 0) 
//                 {
//                     $log_data['LogTraineeInfo'] = $reg_detail['TraineeInfo'];
//                     unset($log_data['LogTraineeInfo']['id']);
//                     $this->loadModel('LogTraineeInfo');
//                     $this->LogTraineeInfo->create();
//                     $this->loadModel('TrainingAttendanceTrainee');
//                     $attendanceDays = $this->TrainingAttendanceTrainee->attendanceDays($this->request->data['TraineeInfo']['trainee_code']);
//                     $log_data['LogTraineeInfo']['attendance_count'] = count($attendanceDays);
//                     $log_data['LogTraineeInfo']['log_type'] = "TRANSFER";
//                     $log_data['LogTraineeInfo']['transaction_type'] = 0;
//                     if ($this->LogTraineeInfo->save($log_data)) 
//                     {
//                         /* Update Trainee Info */
//                         $trainee_info_id = $reg_detail['TraineeInfo']['id'];
//                         $this->TraineeInfo->id = $trainee_info_id;
//                         $this->TraineeInfo->set('training_institute_id', $this->request->data['TraineeInfo']['training_institute_id']);
//                         $this->TraineeInfo->set('course_info_id', $this->request->data['TraineeInfo']['course_info_id']);
//                         $this->TraineeInfo->set('batch_info_id', $this->request->data['TraineeInfo']['batch_info_id']);
//                         if ($this->TraineeInfo->save()) {
//                         	/* Update Attendance Data */
//                         	$this->loadModel('TrainingAttendanceTrainee');
//                         	$attendanceDays = $this->TrainingAttendanceTrainee->attendanceDays($this->request->data['TraineeInfo']['trainee_code']);
//                         	$result = 1;
//                         	if (count($attendanceDays) > 0) 
//                         	{
//                         		$result = 0;
//                         	}
//                         	foreach ($attendanceDays as $day) {
//                         		$result = 0;
//                         		$this->TrainingAttendanceTrainee->id = $day['TrainingAttendanceTrainee']['id'];
//                         		$this->TrainingAttendanceTrainee->set('training_institute_id', $this->request->data['TraineeInfo']['training_institute_id']);
//                         		$this->TrainingAttendanceTrainee->set('course_info_id', $this->request->data['TraineeInfo']['course_info_id']);
//                         		$this->TrainingAttendanceTrainee->set('batch_info_id', $this->request->data['TraineeInfo']['batch_info_id']);
//                         		if ($this->TrainingAttendanceTrainee->save()) 
//                         		{
//                         			$result = 1;
//                         		}
//                         	}
//                         	if ($result == 1) {
//                         		$db->commit();
//                         		$this->Session->setFlash(__('Record has been updated successfully'), 'flash_success');
//                         		$this->redirect(array('action' => 'traineeTransfer'));
//                         	} else {
//                         		$this->Session->setFlash("Failed to transfer training attendance.", 'flash_error_single_message');
//                         	}
//                         }
//                     }
//                 } else {
//                 }
//             } catch (\Exception $e) {
//                 $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//             } catch (\InvalidArgumentException $e) {
//                 $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//             } catch (\InvalidTypeException $e) {
//                 $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//             } catch (\HttpInvalidParamException $e) {
//                 $this->Session->setFlash($e->getMessage(), 'flash_error_single_message');
//             }
//             $db->rollback();
//         }
//         $userProfile = $this->getUserProfile();
//         $entityId = $userProfile ['EntityResource'] ['entity_id'];
//         $registration_numbers = $this->TraineeInfo->getAllRegistrationNumber($entityId);
//         $this->set('registration_numbers', $registration_numbers);
//         $this->_setEntitiesByRole();
//     }

    public function singlePhotoUpload() {
        $upload_status = array();
        $reg_number = $this->request->query['reg_number'];
        if (!empty($reg_number) > 0) {
            @set_time_limit(5 * 60);

            $targetDir = 'files/trainee_info/image_file_name';
            $cleanupTargetDir = true; // Remove old files
            $maxFileAge = 5 * 3600; // Temp file age in seconds
            // Create target dir
            if (!file_exists($targetDir)) {
                @mkdir($targetDir);
            }

            // Get a file name
            if (isset($_REQUEST["name"])) {
                $fileNamePre = $_REQUEST["name"];
            } elseif (!empty($_FILES)) {
                $fileNamePre = $_FILES["file"]["name"];
            } else {
                $fileNamePre = uniqid("file_");
            }
            $splitName = explode(".", $fileNamePre);
            $fileName = $fileNamePre;

            $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

            // Chunking might be enabled
            $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
            $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

            // Open temp file
            if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }

            if (!empty($_FILES)) {
                if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                    //die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
                    $upload_status = "Failed to move uploaded file.";
                    $response_data = array();
                    $response_data['error'] = 1;
                    $response_data['message'] = $upload_status;
                    return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
                    die();
                }

                // Read binary input stream and append it to temp file
                if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                    //die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    $upload_status = "Failed to open input stream.";
                    $response_data = array();
                    $response_data['error'] = 1;
                    $response_data['message'] = $upload_status;
                    return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
                    die();
                }
            } else {
                if (!$in = @fopen("php://input", "rb")) {
                    //die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    $upload_status = "Failed to open input stream.";
                    $response_data = array();
                    $response_data['error'] = 1;
                    $response_data['message'] = $upload_status;
                    return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
                    die();
                }
            }

            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }

            @fclose($out);
            @fclose($in);

            // Check if file has been uploaded
            if (!$chunks || $chunk == $chunks - 1) {
                // Strip the temp .part suffix off
                rename("{$filePath}.part", $filePath);
            }

            // Return Success JSON-RPC response
            die('{"jsonrpc" : "1.0", "result" : ' . $filePath . ', "id" : "id"}');
            $upload_status = $filePath;
            $response_data = array();
            $response_data['success'] = 1;
            $response_data['message'] = $upload_status;
            return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
            die();
        } else {
            //die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "No batch found."}, "id" : "id"}');
            $upload_status = "No Trainee found.";
            $response_data = array();
            $response_data['error'] = 1;
            $response_data['message'] = $upload_status;
            return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
            die();
        }
    }

    public function bulkPhotoUpload() {
        $upload_status = array();
        $batch_id = $this->request->query['batch_id'];
        if ($batch_id > 0) {
            $batch_photo_names = $this->TraineeInfo->find('all', array('fields' => array('image_file_name'), 'conditions' => array('batch_info_id' => $batch_id, 'transaction_type' => 0, 'enrollment_status' => 1), 'recursive' => -1));
            $batch_photos = array();
            foreach ($batch_photo_names as $photo) {
                $batch_photos[] = $photo['TraineeInfo']['image_file_name'];
            }
            if (count($batch_photos) == 0) {
                $upload_status = "Please check your batch enrollment status. Upload will be allowed only for enrolled batch with bulk upload applicants ";
                //die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "'.$upload_status.'."}, "id" : "id"}');
                $response_data = array();
                $response_data['error'] = 1;
                $response_data['message'] = $upload_status;
                return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
                die();
            }

            if (isset($_REQUEST["name"])) {
                $fname_o = $_REQUEST["name"];
            } elseif (!empty($_FILES)) {
                $fname_o = $_FILES["file"]["name"];
            }

            $str = "";
            foreach ($batch_photos as $photo) {
                $str .= $photo . "; ";
            }

            if (!in_array($fname_o, $batch_photos)) {
                $upload_status = "Invalid Photo Name " . $fname_o;
                $response_data = array();
                $response_data['error'] = 1;
                $response_data['message'] = $upload_status;
                $response_data['actual_photos'] = $str;
                return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
                //die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "'.$upload_status.'.", "actual_photos": "'.$str.'."}, "id" : "id"}');
            }

            @set_time_limit(5 * 60);

            // Settings
            //$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
            $targetDir = 'files/trainee_info/image_file_name';
            $cleanupTargetDir = true; // Remove old files
            $maxFileAge = 5 * 3600; // Temp file age in seconds
            // Create target dir
            if (!file_exists($targetDir)) {
                @mkdir($targetDir);
            }

            // Get a file name
            if (isset($_REQUEST["name"])) {
                $fileNamePre = $_REQUEST["name"];
            } elseif (!empty($_FILES)) {
                $fileNamePre = $_FILES["file"]["name"];
            } else {
                $fileNamePre = uniqid("file_");
            }
            $splitName = explode(".", $fileNamePre);
            $fileName = $fileNamePre;

            $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

            // Chunking might be enabled
            $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
            $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

            // Open temp file
            if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }

            if (!empty($_FILES)) {
                if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                    //die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
                    $upload_status = "Failed to move uploaded file.";
                    $response_data = array();
                    $response_data['error'] = 1;
                    $response_data['message'] = $upload_status;
                    return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
                    die();
                }

                // Read binary input stream and append it to temp file
                if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                    //die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    $upload_status = "Failed to open input stream.";
                    $response_data = array();
                    $response_data['error'] = 1;
                    $response_data['message'] = $upload_status;
                    return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
                    die();
                }
            } else {
                if (!$in = @fopen("php://input", "rb")) {
                    //die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    $upload_status = "Failed to open input stream.";
                    $response_data = array();
                    $response_data['error'] = 1;
                    $response_data['message'] = $upload_status;
                    return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
                    die();
                }
            }

            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }

            @fclose($out);
            @fclose($in);

            // Check if file has been uploaded
            if (!$chunks || $chunk == $chunks - 1) {
                // Strip the temp .part suffix off
                rename("{$filePath}.part", $filePath);
            }

            // Return Success JSON-RPC response
            die('{"jsonrpc" : "1.0", "result" : ' . $filePath . ', "id" : "id"}');
            $upload_status = $filePath;
            $response_data = array();
            $response_data['success'] = 1;
            $response_data['message'] = $upload_status;
            return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
            die();
        } else {
            //die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "No batch found."}, "id" : "id"}');
            $upload_status = "No batch found.";
            $response_data = array();
            $response_data['error'] = 1;
            $response_data['message'] = $upload_status;
            return new CakeResponse(array('type' => 'application/json', 'body' => json_encode($response_data)));
            die();
        }
    }

    public function showNidInformation() {
        $nid_information = array();
        if (!empty($this->request->data)) {
            $data = $this->nidCurl($this->request->data['TraineeInfoPre']);
            $nid_information = json_decode($data, true);
        }
        $this->set('nid_information', $nid_information);
    }

    public function validateAndSave() {
        if ($this->request->is('post')) {
            $this->loadModel('TrainingPaymentEnrollment');
            $this->loadModel('TraineeInfo');
            $enrollment_count = $this->TrainingPaymentEnrollment->find('count', array(
                'conditions' => array(
                    'batch_id' => $this->request->data ['batch_id']
                )
            ));

            if ($enrollment_count > 0) {
                $response = array(
                    'code' => 0,
                    'msg' => "Registration is not allowed after bill submission. Please contact with SEIP."
                );
                return new CakeResponse(array(
                    'type' => 'application/json',
                    'body' => json_encode($response)
                        ));
            }
            
            //Check enrollment date
            $this->loadModel('TraineeInfo');
            $enrolled_first = $this->TraineeInfo->find('first', array('conditions'=>array('batch_info_id'=>$this->request->data ['batch_id'], 'enrollment_status'=>1)));

            if (empty ( $enrolled_first )) {
                $this->loadModel ( 'BatchInfo' );
                $batch_data = $this->BatchInfo->read ( null, $this->request->data ['batch_id'] );
                $now = time ();
                $batch_start_date = strtotime ( $batch_data ['BatchInfo'] ['start_date'] );
                $datediff = $now - $batch_start_date;
                $days = floor ( $datediff / (60 * 60 * 24) );
            } else {
                $now = time ();
                $batch_start_date = strtotime ( $enrolled_first ['TraineeInfo'] ['start_date'] );
                $datediff = $now - $batch_start_date;
                $days = floor ( $datediff / (60 * 60 * 24) );
            }

            // Check enrollment grace count
            $course_info_id = $this->request->data['course_id'];
            $this->loadModel('CourseInfo');
            $course = $this->CourseInfo->read(null, $course_info_id);
            
            $course_duration = $course ['CourseInfo'] ['course_duration_month'];
            /*if(($course_duration >= 3 && $days > 15) || $course_duration < 3  && $days > 10){
                $response = array(
                    'code' => 0,
                    'msg' => "Enrollment session is over. Please contact with SEIP."
                );
                return new CakeResponse(array(
                    'type' => 'application/json',
                    'body' => json_encode($response)
                        ));
            }*/

            $course_limit = $course ['CourseInfo'] ['students_per_batch'];
            $enrolled_count = $this->TraineeInfo->find('count', array(
                'conditions' => array(
                    'batch_info_id' => $this->request->data['batch_id']
                )
                    ));

            $course_limit = FLOOR($course_limit + ( $course_limit * ENROLLMENT_GRACE ) / 100);
            if ($enrolled_count > $course_limit) {
                $response = array(
                    'code' => 0,
                    'msg' => "Students per batch is exceeds limit(" . $course_limit . "). Please contact with SEIP."
                );
                return new CakeResponse(array(
                    'type' => 'application/json',
                    'body' => json_encode($response)
                        ));
            }

            $nid = !empty($this->request->data['row_data']['nid']) ? $this->request->data['row_data']['nid'] : '';
            $bcn = !empty($this->request->data['row_data']['bcn']) ? $this->request->data['row_data']['bcn'] : '';
            $dob = !empty($this->request->data['row_data']['date_of_birth']) ? $this->request->data['row_data']['date_of_birth'] : '';

            $nid = trim($nid);
            $bcn = trim($bcn);

            $this->request->data ['TraineeInfo'] = $this->request->data['row_data'];

            $nid_response = $this->checkExisting($nid, $bcn, $dob, $this->request->data);
            if ($nid_response['status'] == 0) {
                return new CakeResponse(array(
                    'type' => 'application/json',
                    'body' => json_encode($nid_response)
                        ));
            } else {
                $this->request->data ['TraineeInfo']['entity_info_id'] = $this->request->data['entity_id'];
                $this->request->data ['TraineeInfo']['training_institute_id'] = $this->request->data['institute_id'];
                $this->request->data ['TraineeInfo']['course_info_id'] = $this->request->data['course_id'];
                $this->request->data ['TraineeInfo']['batch_info_id'] = $this->request->data['batch_id'];

                $this->TraineeInfo->create();
                $userProfile = $this->getUserProfile();
                $entity_shortname = $userProfile ['Entity'] ['short_name'];


                if ($this->TraineeInfo->save($this->request->data, true)) {
                    $this->Session->write('pre-registration_data', array());
                    $response = array(
                        'code' => 1,
                        'msg' => "Trainee Information has been saved."
                    );
                    return new CakeResponse(array(
                        'type' => 'application/json',
                        'body' => json_encode($response)
                            ));
                } else {
                    $errors = $this->TraineeInfo->validationErrors;
                    $html = '<ul>';
                    foreach ($errors as $error) {
                        $html .= '<li>' . $error[0] . '</li>';
                    }
                    $html .= '</ul>';
                    $response = array(
                        'code' => 0,
                        'msg' => $html
                    );
                    return new CakeResponse(array(
                        'type' => 'application/json',
                        'body' => json_encode($response)
                            ));
                }
            }
        }
    }

    private function checkExisting($nid, $bcn, $dob, $request_data) {
        $this->request->data = $request_data;
        $data = array();
        $existing = $this->checkExistingTrainee($nid, $bcn, $dob);
        if ($existing == 0) {
            if ($nid != '') {
                // check length
                if (strlen($nid) != 17) {
                    $data ['status'] = 0;
                    $data ['msg'] = 'NID length should be 17 digits';
                } else {
                    $data ['status'] = 1;
                    $nid_information = $this->checkNid($nid, $dob);
                    if (!empty($nid_information ['result'] ['name'])) {
                        $data ['data'] = $nid_information ['result'];

                        // Save data into NID log
                        $log_data ['NidLog'] = array();
                        $log_data ['NidLog'] ['entity_id'] = $this->request->data ['entity_id'];
                        $log_data ['NidLog'] ['training_institute_id'] = $this->request->data ['institute_id'];
                        $log_data ['NidLog'] ['course_info_id'] = $this->request->data ['course_id'];
                        $log_data ['NidLog'] ['batch_info_id'] = $this->request->data ['batch_id'];
                        $log_data ['NidLog'] ['dob'] = $dob;
                        $log_data ['NidLog'] ['nid'] = $nid;
                        $log_data ['NidLog'] ['request_ip'] = $_SERVER ['REMOTE_ADDR'];

                        $this->loadModel('NidLog');
                        $this->NidLog->create();
                        $this->NidLog->save($log_data);
                    } else {
                        $data ['status'] = 0;
                        $data ['msg'] = 'Server Response: Invalid NID.';
                    }
                }
            } else if ($nid == '' && $bcn != '') {
                $data ['status'] = 2;
                $data ['data'] = array();
            }
        } else {
            if ($existing == 1) {
                $data ['status'] = 0;
                $data ['msg'] = 'NID is already exists for registered qualified applicant!!!';
            }
            if ($existing == 2) {
                $data ['status'] = 0;
                $data ['msg'] = 'BCN is already exists for registered qualified applicant!!!';
            }
        }
        return $data;
    }

    private function checkExistingTrainee($nid = '', $bcn = '', $dob = '') {

        $countn = -1;
        $countb = -1;
        $this->loadModel('TraineeInfo');
        if (!empty($nid)) {
            $countn = $this->TraineeInfo->checkNid($nid);
            if (intval($countn) > 0) {
                return 1;
            }
        }
        if (!empty($bcn)) {
            $countb = $this->TraineeInfo->checkBcn($bcn);
            if (intval($countb) > 0) {
                return 2;
            }
        }
        return 0;
    }

    private function checkNid($nid = '', $dob = '') {
        $data = $this->nidCurl(array('nid' => $nid, 'dob' => $dob));
        $nid_information = json_decode($data, true);
        return $nid_information;
    }

public function traineesBankInfo() {
        $this->_setEntitiesByRole();
    }

    public function traineesBankInfoList() {
        $entityId = $this->request->data['entity_id'];
        $instituteId = $this->request->data['institute_id'];
        $courseId = $this->request->data['course_id'];
        $batchId = $this->request->data['batch_id'];

        $query = "SELECT
                                        ti.reference_number,
                                        ti.trainee_name,
                                        ti.image_file_name,
                                        ti.registration_number,
                                        ti.bank_name,
                                        ti.bank_account_number,
                                        ti.id
                            FROM
                                            trainee_infos as ti
                            WHERE
                                            ti.entity_id=$entityId and ti.training_institute_id=$instituteId and ti.course_info_id= $courseId and ti.batch_info_id= $batchId
                            ";

        $response_data = $this->TraineeInfo->query($query);
        $this->set('traineeInfos', $response_data);
    }
    public function updateTraineesBankInfo(){
        $BN = $this->request->data['BN'];
        $BAN = $this->request->data['BAN'];
        $id = $this->request->data['id'];
        if(!empty($id)){
            $data = array('id' => $id, 'bank_name' =>$BN, 'bank_account_number' => $BAN);
//            $query= "UPDATE trainee_infos set bank_name ='".$BN."' AND bank_account_number = '".$BAN."' where id = ".$id;
//            print_r($query);die;
            try{
                $this->TraineeInfo->save($data);
                echo json_encode(1 );die;
            } catch (\Exception $ex) {
                echo json_encode(array( 'status' => 0,'msg' => $ex )) ;die;
            }
            
        }
    }

    public function traineesPhotos() {
        $this->_setEntitiesByRole();
    }
    public function traineesPhotoList() {
        $entityId = $this->request->data['entity_id'];
        $instituteId = $this->request->data['institute_id'];
        $courseId = $this->request->data['course_id'];
        $batchId = $this->request->data['batch_id'];

        $query = "SELECT
                                        ti.reference_number,
                                        ti.trainee_name,
                                        ti.image_file_name,
                                        ti.registration_number,
                                        ti.id,
                                        ti.nid,
                                        ti.date_of_birth
                            FROM
                                            trainee_infos as ti
                            WHERE
                                            ti.entity_id=$entityId and ti.training_institute_id=$instituteId and ti.course_info_id= $courseId and ti.batch_info_id= $batchId
                            ";

        $response_data = $this->TraineeInfo->query($query);
        $this->set('traineeInfos', $response_data);
        $this->set(compact('entityId','instituteId','courseId','batchId'));
        $this->set('label', crypt( 'TappTmsS3!P',date('H')));
    }
    public function updateTraineePhoto(){
        $id  = isset($this->request->data['id'])?$this->request->data['id']:'';
        $img  = isset($this->request->data['img'])?$this->request->data['img']:'';
        $response = array(
            'msg' => 'Something went wrong',
            'status' => 'error'
        );
        if(empty($this->request->data['img'])){
            $response['msg'] = 'No image Given';
             goto rtn;
        }
        try{
            $this->loadModel('TraineeInfo');
            $this->loadModel('Entity');
            $trainee_info =$this->TraineeInfo->find('first', array('conditions' => array('TraineeInfo.id' => $id)));
            if(empty($trainee_info)){
                $response['msg'] ='No Trainee Record Found';
                goto rtn;
            }
            $entity_info = $this->Entity->find('first', array('conditions' => array('Entity.id' => $trainee_info['TraineeInfo']['entity_id'])));
            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $img));
             if(empty($data)){
            $response['msg'] = 'No image Given';
             goto rtn;
            }
            $filename = $entity_info['Entity']['short_name'].'-'.$trainee_info['TraineeInfo']['reference_number'].'.jpg';
            $file ='./files/trainee_info/image_file_name/' .$filename;
//            unlink($file);
            $response['status'] = ((file_put_contents($file, $data))==false?'error':'success');
           if($response['status'] == 'success'){
               $this->TraineeInfo->query('UPDATE trainee_infos set image_file_name ="'.$filename.'" where id = '.$id);
           }
        } catch (\Exception $ex) {
              $response['msg'] =$ex->getMessage();
             goto rtn;
        }
           rtn:
            $this->response->type('application/json');
            $this->response->body(json_encode($response));
            return $this->response;
        
    }
     public function traineesAddressInfo($password) {
         $this->loadModel('Users');
         print_r($this->User->returnHash('123456'));
         die;
        $this->_setEntitiesByRole();
    }
    public function traineesAddressInfoList() {
        $entityId = $this->request->data['entity_id'];
        $instituteId = $this->request->data['institute_id'];
        $courseId = $this->request->data['course_id'];
        $batchId = $this->request->data['batch_id'];
        $this->loadModel('DistrictList');
        $all_district = $this->DistrictList->getAllDistrict();
        $this->set(compact('all_district'));
        $this->loadModel('UpazilaList');
        $all_upazilla = $this->UpazilaList->getAllUpazila();
        $this->set(compact('all_upazilla'));

        $query = "SELECT
                                        ti.reference_number,
                                        ti.trainee_name,
                                        ti.image_file_name,
                                        ti.registration_number,
                                        ti.id,
                                        ti.present_district,
                                        ti.per_district,
                                        ti.home_district,
                                        ti.home_upazilla
                            FROM
                                            trainee_infos as ti
                            WHERE
                                            ti.entity_id=$entityId and ti.training_institute_id=$instituteId and ti.course_info_id= $courseId and ti.batch_info_id= $batchId
                            ";

        $response_data = $this->TraineeInfo->query($query);
        $this->set('traineeInfos', $response_data);
        $this->set(compact('entityId','instituteId','courseId','batchId'));
    }

    public function searchTraineeSummary() {
        $this->layout = 'api';
        $this->loadModel('Entity');
        $entities = $this->Entity->getAllEntityByName();
        if(!empty($entities)){
            foreach ($entities as $k => $v){
                $result[base64_encode($k)] = $v;
            }
        }
        $this->set('entities' , $result);
    }

    public function getCourseByEntityId($id)
    {
        $this->loadModel('CourseInfo');
        $all_course = $this->CourseInfo->getCourses(base64_decode($id));
        if(!empty($all_course)){
            foreach ($all_course as $k => $v){
                $result[base64_encode($k)] = $v;
            }
        }
        return new CakeResponse(array('type' => 'application/json' , 'body' => json_encode($result)));
    }

    public function getInstituteByEntityIdCourseId()
    {
        $this->loadModel('MapCourseTrainer');
        $entity_id    = empty($this->request->data['entity_id'])?'':  base64_decode($this->request->data['entity_id']);
        $course_id   = empty($this->request->data['course_id'])?'':  base64_decode($this->request->data['course_id']);
        $institutions = $this->MapCourseTrainer->getMappedInstitute($entity_id,$course_id);
        if(!empty($institutions)){
            foreach ($institutions as $k => $v){
                $result[base64_encode($k)] = $v;
            }
        }
        return new CakeResponse(array('type' => 'application/json' , 'body' => json_encode($result)));

    }

    public function getBatchByEntityIdCourseIdInstituteId()
    {
        $this->loadModel('BatchInfo');
        $entity_id    = empty($this->request->data['entity_id'])?'':  base64_decode($this->request->data['entity_id']);
        $course_id   = empty($this->request->data['course_id'])?'':  base64_decode($this->request->data['course_id']);
        $institute_id   = empty($this->request->data['institute_id'])?'':  base64_decode($this->request->data['institute_id']);
        $batches = $this->BatchInfo->nameOfBatches($entity_id,$course_id,$institute_id);
        if(!empty($batches)){
            foreach ($batches as $k => $v){
                $result[base64_encode($k)] = $v;
            }
        }
        return new CakeResponse(array('type' => 'application/json' , 'body' => json_encode($result)));

    }

    public function getCertificationByEntityIdCourseIdInstituteIdBatchId()
    {
        $this->loadModel('TrainingCertificate');
        $entity_id    = empty($this->request->data['entity_id'])?'':  base64_decode($this->request->data['entity_id']);
        $course_id   = empty($this->request->data['course_id'])?'':  base64_decode($this->request->data['course_id']);
        $institute_id   = empty($this->request->data['institute_id'])?'':  base64_decode($this->request->data['institute_id']);
        $batch_id   = empty($this->request->data['batch_id'])?'':  base64_decode($this->request->data['batch_id']);
        $all_certifications = $this->TrainingCertificate->nameOfCertification($entity_id,$course_id,$institute_id,$batch_id);
        $this->set(compact('all_certifications'));
    }

    public function getCourseInfoByCourseId($course_id)
    {
        $this->loadModel('CourseInfo');
        $result = $this->CourseInfo->getCourseInfo(base64_decode($course_id));
        $course_info = $result['CourseInfo']['course_duration_details'];
        return new CakeResponse(array('type' => 'application/json' , 'body' => json_encode($course_info)));
    }

    public function getInstituteInfoByInstituteId($institute_id)
    {
        $this->loadModel('TrainingInstitute');
        $result = $this->TrainingInstitute->getInstituteInfo(base64_decode($institute_id));
        $institute_info = $result['TrainingInstitute']['present_address'];
        return new CakeResponse(array('type' => 'application/json' , 'body' => json_encode($institute_info)));
    }

    public function getBatchInfoByBatchId($batch_id)
    {
        $this->loadModel('BatchInfo');
        $result = $this->BatchInfo->getBatchInfo(base64_decode($batch_id));
        $batch_info = $result['BatchInfo']['training_location'];
        return new CakeResponse(array('type' => 'application/json' , 'body' => json_encode($batch_info)));
    }

}
