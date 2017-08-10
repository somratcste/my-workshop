<?php
$showError = function ($errors){
    $error = [];
    if(is_array($errors)){
        foreach($errors as $key=>$errorType){
            if(is_array($errorType)){
                foreach($errorType as $k=>$msg){
                    $error[]  =  $key . ':' .$msg;
                }
            }else{
                $error[]  =  $key . ':' .$errorType;
            }
        }
    }else{
        $error[]  = $errors;
    }

    return $error;
};
$error = $showError($message);
?>
<script>
    $('.alert').remove();
    $.jGrowl('<?php echo implode('<br>' , $error) ?>', { sticky: true, theme: 'growl-error', header: 'Error!', life: 1000  });
</script>