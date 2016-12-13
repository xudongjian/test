<?php

class SiteController extends WebsiteController {

    private $model = null;

    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('ajaxform'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('ajaxform'),
                'users' => array('@'),
            ),
//            array('deny', // deny all users
//                'users' => array('*'),
//            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        $users = User::model()->findAll();
        $this->render("index", array('users' => $users
        ));
    }

    public function actionAjaxForm() {  
        if (isset($_POST['user'])) {
            $values = $_POST['user'];
            $usermar = new UserManager();
            $output = $usermar->saveUser($values);
            return $this->renderJsonOutput($output);
        } else {
            $output['status'] = 'no';
            $output['errorCode'] = 401;
            $output['errorMsg'] = "非法请求";
            return $this->renderJsonOutput($output);
        }
    }

}
