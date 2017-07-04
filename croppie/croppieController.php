public function croppieAction()
    {
        $response = new Response();
        if(!empty($this->request->getPost('imagebase64'))){
            if($this->request->getPost('previousImage')){
                unlink('/var/www/html/bangladesh.gov.bd-mig'.$this->request->getPost('previousImage'));
            }
            
            $domain = $this->getDomainName();
            $contenttype = $this->request->getPost('contenttype');
            $uploadPath = $this->request->getPost('uploadPath');
            $uploadPath = str_replace("-","_", $uploadPath);
            
            if (!file_exists($this->imageUploadUri.$domain)) {
                if(mkdir($this->imageUploadUri.$domain, 0777, true)){

                }else{
                    $response->setContentType('application/json', 'UTF-8');
                    $response->setContent(json_encode(array("result" => "You haven't any permission to create directory.")));
                    return $response;
                }
            } 
            if (!file_exists($this->imageUploadUri.$domain.'/'.$contenttype)){
                if(mkdir($this->imageUploadUri.$domain.'/'.$contenttype, 0777, ture)){

                } else {
                    $response->setContentType('application/json', 'UTF-8');
                    $response->setContent(json_encode(array("result" => "You haven't any permission to create directory.")));
                    return $response;
                }
            } 
            if(!file_exists($this->imageUploadUri.$domain.'/'.$contenttype.'/'.$uploadPath)){
                if(mkdir($this->imageUploadUri.$domain.'/'.$contenttype.'/'.$uploadPath, 0777, true)){

                } else {
                    $response->setContentType('application/json', 'UTF-8');
                    $response->setContent(json_encode(array("result" => "You haven't any permission to create directory.")));
                    return $response;
                }
            }
        
            $data = $this->request->getPost('imagebase64');
            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
            $folder_path = $this->imageUploadUri.$domain.'/'.$contenttype.'/'.$uploadPath.'/';
            $filename = md5(date('Y:m:d H:i:s:u')).'.png';
            $file = $folder_path.$filename;
            file_put_contents($file, $data);
            if(is_writable($folder_path)){

            } else {
                $response->setContentType('application/json', 'UTF-8');
                $response->setContent(json_encode(array("result" => "You haven't any permission to write file.")));
                return $response;
            }
            $file = str_replace('/var/www/html/bangladesh.gov.bd-mig', '', $file);
            $response->setContentType('application/json', 'UTF-8');
            $response->setContent(json_encode(array("result" => $file,"filename" => $filename)));
            return $response;
        }
    }

    public function croppiecancelAction()
    {
        $response = new Response();
        if($this->request->getPost('previousImage')){
            unlink('/var/www/html/bangladesh.gov.bd-mig'.$this->request->getPost('previousImage'));
        }
        $response->setContentType('application/json', 'UTF-8');
        $response->setContent(json_encode(array("result" => "Image Deleted successfully.")));
        return $response;
    }