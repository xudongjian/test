<?php

class UserManager {
    public function saveUser($values) {
        $model = new User();
        $model->setAttributes(array('id'=>$values['id'],'name'=>$values['name'], 'age'=>$values['age']), true);
        if ($model->save()) {
            print_r($model);die;
           $output['status'] = 'ok';
           $output['errorCode'] = 0;
           $output['errorMsg'] = 'success';
           $output['results'] = array();
           return $output;
        }else{
           $output['status'] = 'no';
           $output['errorCode'] = 400;
           $output['errorMsg'] = $model->getFirstErrors();
           return $output;
        }
    }

}

