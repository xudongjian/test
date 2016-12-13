<?php

abstract class Controller extends CController {

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout;
    public $defaultAction;
    public $returnUrl = '';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu;
    public $pageTitle;

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();
    public $current_user = null;

    /*
     * In-class defined filter method, configured for use in the above filters() method.
     * It is called before the actionCreate() action method is run in order to ensure a proper user content.
     */

    public function filterUserContext($filterChain) {
        // Load User data model from session.
        $this->loadUser();

        //complete the running of other filters and execute the requested action.
        $filterChain->run();
    }

    public function filterUserDoctorContext($filterChain) {
        // Load User data model from session.
        $user = $this->loadUser(array('userDoctorProfile'));
        if ($user->isDoctor(false) === false) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        //complete the running of other filters and execute the requested action.
        $filterChain->run();
    }

    /**
     * Protected method to load the associated User model class from login session.
     * @return object the User data model based on the primary key stored in session.
     */
    public function loadUser($with = null) {
        if (is_null($this->current_user)) {
            //var_dump(Yii::app()->user->id);exit;
            if (isset(Yii::app()->user->id)) {
                $this->current_user = User::model()->getById(Yii::app()->user->id, $with);
                if (is_null($this->current_user)) {
                    // 有session但存的user.id不存在，所以logout user.
                    Yii::app()->user->logout();
                    // 跳转到登录页面.
                    $this->redirect(Yii::app()->user->loginUrl);
                    //throw new CHttpException(404, 'The requested page does not exist.');
                }
            }
        }
        return $this->current_user;
    }

    public function getCurrentUser() {
        try {
            return $this->loadUser();
        } catch (CException $cex) {
            return null;
        }
    }

    //TODO: reimplement this method, remove throw exceptions in loadUser().
    public function getCurrentUserId() {
        return Yii::app()->user->id;
    }

    public function getCurrentUserRole() {
        return (Yii::app()->user->getRole());
    }

    public function headerUTF8() {
        header('Content-Type: text/html; charset=utf-8');
    }

    public function renderJsonOutput($data, $exit = true, $httpStatus = 200) {
        header('Content-Type: application/json; charset=utf-8');
        echo CJSON::encode($data);
        foreach (Yii::app()->log->routes as $route) {
            if ($route instanceof CWebLogRoute || $route instanceof XWebDebugRouter) {
                $route->enabled = false; // disable any weblogroutes
            }
        }
        if ($exit) {
            Yii::app()->end($httpStatus, true);
        }
    }

    public function renderImageOutput($imageUrl, $type = 'jpg') {
        $imginfo = getimagesize($imageUrl);
        header("Content-type: {$imginfo['mime']}");
        readfile($imageUrl);
        /* header('Content-type: image/' . $type);
          $content = file_get_contents($imageUrl);
          echo $content;
         */
    }

    public function isUserAgentApp() {
        return ( (isset($_GET['appv']) && $_GET['appv'] > 1) || (isset($_GET['agent']) && $_GET['agent'] == 'app') || (isset($_POST['agent']) && $_POST['agent'] == 'app') || (isset($_GET['os']) && in_array($_GET['os'], array('android', 'ios'))) || (isset($_POST['os']) && in_array($_POST['os'], array('android', 'ios'))));
    }

    public function isUserAgentWeixin() {
        $userAgent = Yii::app()->request->getUserAgent();
        return (strContains($userAgent, 'MicroMessenger'));
    }

    public function isUserAgentIOS() {
        $userAgent = strtolower(Yii::app()->request->getUserAgent());
        return strContains($userAgent, 'iphone') || strContains($userAgent, 'ipad');
    }

    public function isUserAgentAndroid() {
        $userAgent = strtolower(Yii::app()->request->getUserAgent());
        return strContains($userAgent, 'android');
    }

    public function getUserIp() {
        return Yii::app()->request->userHostAddress;
    }

    public function getPostData() {
        return file_get_contents('php://input');
    }

    /**
     * @return string return url. 
     */
    public function getReturnUrl($defaultUrl = '') {
        if (strIsEmpty($this->returnUrl)) {
            $this->returnUrl = trim(Yii::app()->request->getParam('returnUrl', $defaultUrl));
        }
        return $this->returnUrl;
    }

    public function setReturnUrl($url) {
        $this->returnUrl = $url;
    }

    public function getCurrentRequestUrl() {
        $request = Yii::app()->request;
        return $request->hostInfo . $request->url;
    }

    /**
     * 模拟get进行url请求
     * @param string $url
     */
    function send_get($url) {
         $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }  

}
