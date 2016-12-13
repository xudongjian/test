<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
abstract class WebsiteController extends Controller {

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/layoutMain';
    public $defaultAction = 'index';
    public $cotroller_name;

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    public $pageTitle = '名医主刀_三甲医院名医,专家,主任医生手术,床位预约,网上挂号,手机APP';
    public $htmlMetaKeywords = '名医主刀,三甲医院,名医,专家,主任医生,手术预约,网上挂号,手机APP';
    public $htmlMetaDescription = '名医随时有,手术不再难！【名医主刀】汇聚国内外顶级名医资源和床位资源，利用互联网技术实现医患精准匹配，帮助广大患者得以在第一时间预约到名医专家进行主刀治疗。www.mingyizhudao.com';

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();
    public $content_container = 'container page-container';
    public $site_menu = null;
    public $header_menu = null;
    public $show_header = true;
    public $show_header_navbar = true;
    public $show_footer = true;
    public $show_traffic_script = true;
    public $show_baidushangqiao = true;

    public function init() {
        if (isset(Yii::app()->theme)) {
            Yii::app()->clientScript->scriptMap = array(
                //'jquery.js' => Yii::app()->theme->baseUrl . '/js/jquery-1.8.3.min.js',            
//                'jquery.js' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/static/web/js/jquery-1.9.1.min.js',
//                'jquery.min.js' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/static/web/js/jquery-1.9.1.min.js',
                 'jquery.js' => 'http://static.mingyizhudao.com/pc/jquery-1.9.1.min.js',
                'jquery.min.js' => 'http://static.mingyizhudao.com/pc/jquery-1.9.1.min.js',
//                'jquery.yiiactiveform.js' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/static/web/js/jquery.yiiactiveform.js',
                'jquery.yiiactiveform.js' =>'http://static.mingyizhudao.com/pc/jquery.yiiactiveform.js',
            );
        }

        Yii::app()->clientScript->registerCoreScript('jquery');

        // show header.
        if (isset($_GET['header']) && $_GET['header'] != 1) {
            $this->show_header = false;
        }
        // show footer.
        if (isset($_GET['footer']) && $_GET['footer'] != 1) {
            $this->show_footer = false;
        }
        $this->storeAppAccessInfo();
        return parent::init();
    }

    /**
     * Performs the AJAX validation.
     * @param User $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        //if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-form') {
        if (isset($_POST['ajax'])) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function showHeader() {
        return $this->show_header;
    }

    public function showFooter() {
        return $this->show_footer;
    }

    public function getHomeUrl() {
        return Yii::app()->homeUrl;
    }

    public function setPageTitle($title, $siteName = true) {
        if ($siteName) {
            $this->pageTitle = Yii::app()->name . ' - ' . $title;
        } else {
            $this->pageTitle = $title;
        }
    }

    public function getFlashMessage($key) {
        if (Yii::app()->user->hasFlash($key))
            return Yii::app()->user->getFlash($key);
        else
            return null;
    }

    public function setFlashMessage($key, $msg) {
        Yii::app()->user->setFlash($key, $msg);
    }

    public function hasFlashMessage($key) {
        return Yii::app()->user->hasFlash($key);
    }

    public function getSession($key, $unset = false) {
        $value = Yii::app()->session[$key];
        if ($unset) {
            unset(Yii::app()->session[$key]);
        }
        return $value;
    }

    public function setSession($key, $value) {
        Yii::app()->session[$key] = $value;
    }

    public function clearSession($key) {
        unset(Yii::app()->session[$key]);
    }

    public function addHttpSession($key, $value) {
        $session = new CHttpSession;
        $session->open();
        $session[$key] = $value;
    }

    /*
      public function renderJsonOutput($data) {
      header('Content-Type: application/json; charset=utf-8');
      echo CJSON::encode($data);
      foreach (Yii::app()->log->routes as $route) {
      if ($route instanceof CWebLogRoute) {
      $route->enabled = false; // disable any weblogroutes
      }
      }
      Yii::app()->end();
      }
     */

    public function isAjaxRequest() {
        if (Yii::app()->request->isAjaxRequest) {
            return true;
        } else {
            return ((isset($_GET['ajax']) && $_GET['ajax'] == 1) || (isset($_POST['ajax']) && $_POST['ajax'] == 1));
        }
    }

    public function throwPageNotFoundException($code = 404) {
        throw new CHttpException($code, 'The requested page does not exist.');
    }

    public function loadTrafficAnalysisScript($filterDomain = true) {
        $show = true;
        if ($filterDomain) {
            $baseUrl = Yii::app()->getBaseUrl(true);
            if (strStartsWith($baseUrl, 'http://localhost') || strStartsWith($baseUrl, 'http://127.0.0.1')) {
                $show = false;
            }
        }
        if ($show) {
            $this->renderPartial('//layouts/_scriptTrafficAnalysis');
        }
    }

    public function handleMobileBrowserRedirect($defaultUrl = null) {
        $detect = Yii::app()->mobileDetect;
        // client is mobile and url is not mobile.
        if ($detect->isMobile()) {
            $this->redirect(Yii::app()->params['baseUrlMobile']);
            /*
              $cookieName = "client.browsermode";
              if (isset(Yii::app()->request->cookies[$cookieName]) == false || Yii::app()->request->cookies[$cookieName] != "pc") {
              $this->redirect(Yii::app()->params['baseUrlMobile']);
              }
             * 
             */
        }
    }

    public function isBaseUrlMobile() {
        // get rule like 'http://m.example.com'.
        $baseUrl = Yii::app()->getBaseUrl(true);
        /*
          // remove 'http://' in url.
          $baseUrl = str_ireplace('http://', '', $baseUrl);
          // if starts with 'm.'.
          return (strpos($baseUrl, 'm.') === 0);
         */
        return $baseUrl == Yii::app()->params['baseUrlMobile'];
    }

    public function setBrowserInSession($browser) {
        Yii::app()->session['client.browser'] = $browser;
    }

    /**
     * Stores user's access info for every request.
     */
    public function storeUserAccessInfo() {
        $coreAccess = new CoreAccess();
        $coreAccess->user_host_ip = Yii::app()->request->getUserHostAddress();
        $coreAccess->url = Yii::app()->request->getUrl();
        $coreAccess->url_referrer = Yii::app()->request->getUrlReferrer();
        $coreAccess->user_agent = Yii::app()->request->getUserAgent();
        $coreAccess->user_host = Yii::app()->request->getUserHost();
        $coreAccess->save();
    }

    /**
     * Stores user's access info for every request.
     */
    public function  storeAppAccessInfo($vendorId=0, $site=0) {
//        $coreAccess = new AppLog();
//        if($vendorId > 0){
//            $coreAccess->vendor_id = $vendorId;
//        }
//        if($site > 0){
//            $coreAccess->site = $site;
//        }
//        $coreAccess->user_host_ip = Yii::app()->request->getUserHostAddress();
//        $coreAccess->url = Yii::app()->request->getUrl();
//        $coreAccess->url_referrer = Yii::app()->request->getUrlReferrer();
//        $coreAccess->user_agent = Yii::app()->request->getUserAgent();
//        $coreAccess->user_host = Yii::app()->request->getUserHost();
//
//        $coreAccess->save();

    }

    //验证第三方
    public function checkVendor($site=0){
        if (isset($_GET['appId']) && isset($_GET['timestamp']) && isset($_GET['sign'])) {
            $now = time();
            $oneDay = 3600 * 24;
            $timestamp = $_GET['timestamp'];
            if ($timestamp <= ($now + $oneDay) && $timestamp >= ($now - $oneDay)) {
                $appVendor = new AppVendor();
                $appKey = $appVendor->getByAppId($_GET['appId']);

                if (isset($appKey)) {
                    if (checkSignature(array('appId' => $_GET['appId'], 'timestamp' => $_GET['timestamp']), $appKey->app_secret, $_GET['sign'])) {

                        Yii::app()->session['vendorId'] = $appKey->id;
                        $this->storeAppAccessInfo($appKey->id, $site);
                    } else {
                        unset(Yii::app()->session['vendorId']);
                        $this->renderJsonOutput(array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => 3, 'errorMsg' => 'sign error'));
                    }
                } else {
                    unset(Yii::app()->session['vendorId']);
                    $this->renderJsonOutput(array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => 2, 'errorMsg' => 'vendor error'));
                }
            } else {
                unset(Yii::app()->session['vendorId']);
                $this->renderJsonOutput(array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => 1, 'errorMsg' => 'the request has expired'));
            }
        }
    }
    /*
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
     * 
     */

    public function loadSiteMenu() {
        if (is_null($this->site_menu)) {
            $urlFacultyView = $this->createUrl("faculty/view", array("name" => ""));
            $this->site_menu = array(
                'site' => array(
                    "aboutus" => array("label" => "简介", "url" => $this->createUrl('site/page', array('view' => 'aboutus'))),
                    "terms" => array("label" => "免责声明", "url" => $this->createUrl('site/page', array('view' => 'terms'))),
                    "contactus" => array("label" => "联系我们", "url" => $this->createUrl('site/contactus')),
                    "quickbook" => array("label" => "快速预约", "url" => $this->createUrl('site/enquiry')),
                ),
                'auth' => array(
                    "userRegister" => array("label" => "用户注册", "url" => $this->createUrl("user/register")),
                    "userLogin" => array("label" => "用户登录", "url" => $this->createUrl("user/login")),
                    "doctorRegister" => array("label" => "医生注册", "url" => $this->createUrl("doctor/register"))
                ),
                'serviceflow' => array(
                    '国内服务流程' => $this->createUrl('service/domestic'),
                    '海外服务流程' => $this->createUrl('service/overseas')
                ),
                'overseas' => array(
                    '新加坡' => $this->createUrl('overseas/view', array('page' => 'sg')),
                    '美国' => $this->createUrl('overseas/view', array('page' => 'usa')),
                    '韩国' => $this->createUrl('overseas/view', array('page' => 'korea')),
                    '日本' => $this->createUrl('overseas/view', array('page' => 'japan')),
                    'divider' => '',
                    '特色手术' => $this->createUrl('overseas/surgery'),
                ),
                'event' => array(
                    '上海胆道疾病会诊中心' => $this->createUrl('event/view', array('page' => 'dandao')),
                    '肝病新疗法' => $this->createUrl('event/view', array('page' => 'liubaochi'))
                ),
                'faculty' => array(
                    "xinxueguan" => array("label" => "心血管科", "url" => $urlFacultyView . "心血管科"),
                    "jiazhuangxianke" => array("label" => "甲状腺科", "url" => $urlFacultyView . "甲状腺科"),
                    "yanke" => array("label" => "眼科", "url" => $urlFacultyView . "眼科"),
                    "baineizhangke" => array("label" => "白内障科", "url" => $urlFacultyView . "白内障科"),
                    "shenjingke" => array("label" => "神经外科", "url" => $urlFacultyView . "神经外科"),
                    "jiezhichangaike" => array("label" => "结直肠癌科", "url" => $urlFacultyView . "结直肠癌科"),
                    "gandan" => array("label" => "肝胆外科", "url" => $urlFacultyView . "肝胆外科"),
                    "yixianwaike" => array("label" => "胰腺外科", "url" => $urlFacultyView . "胰腺外科"),
                    "shengzhiyixueke" => array("label" => "生殖医学科", "url" => $urlFacultyView . "生殖医学科"),
                    "zhongliu" => array("label" => "肿瘤外科", "url" => $urlFacultyView . "肿瘤外科"),
                )
            );
        }
        return $this->site_menu;
    }

    public function getHeaderMenu() {
        if ($this->header_menu === null) {
            $this->header_menu = array(
                'doctor' => array('label' => '找名医', 'url' => array('/doctor/top/disease_sub_category/101/disease/0/city/0/mtitle/0/page/1/getcount/1')),
                'hospital' => array('label' => '找医院', 'url' => array('hospital/department')),
                'zhitongche' => array('label' => '患者故事', 'url' => array('site/page', 'view' => 'zhitongche')),
                'mygy' => array('label' => '公益手术', 'url' => array('site/page', 'view' => 'mygy')),
                'aboutus' => array('label' => '关于我们', 'url' => array('site/page', 'view' => 'aboutus')),
            );
        }
        return $this->header_menu;
    }

    public function actionValiCaptcha() {
        $output = array('status' => 'no');
        if (strcmp($_REQUEST['co_code'], Yii::app()->session['code']) != 0) {
            $output['status'] = 'no';
            $output['error'] = '验证码错误';
        } else {
            $output['status'] = 'ok';
        }
        $this->renderJsonOutput($output);
    }

    //获取验证码
    public function actionGetCaptcha() {
        $captcha = new CaptchaManage;
        $captcha->showImg();
    }
    
    //分頁    
    public function page($parmes,$pagesize,$nums)
    {
        if(array_key_exists("page",$parmes)){
            $currentPage=$parmes['page'];
        }else{
            $currentPage=1;
        }
        unset($parmes['page']);
        $parmesStr="";
        foreach($parmes as $k=>$v){
           if($parmesStr==""){
               $parmesStr.= "?".$k."=".$v;
           }else{
               $parmesStr.= "&".$k."=".$v;
           }
        }
        
        $uriArr=explode('-',str_replace(".html", "", $_SERVER['REQUEST_URI']));
        $key=array_search('page', $uriArr);
        
        
        $pages=ceil($nums/$pagesize);
        if($currentPage>$pages){
            $currentPage=$pages;
        }
        if($currentPage < 1){
            $currentPage=1;
        }
        $beginNum = ($currentPage-1)*$pagesize;
        $limit="$beginNum,$pagesize";
        $pre = $currentPage-1;
        if($pre<1)
        {
            $pre=1;
        }
        $next = $currentPage +1;
        if($next>$pages)
        {
            $next=$pages;
        }
        $preUriArray=$uriArr;
        $preUriArray[$key+1]=$pre;
        $nextUriArray=$uriArr;
        $nextUriArray[$key+1]=$next;
        $forUriArray=$uriArr;
        
        $preUrl=  implode("/", $preUriArray);
        $nextUrl=  implode("/", $nextUriArray);
        
        $show ="<ul class='pagination'>";
        $show.= "<li><a aria-label='Previous' href='$preUrl' class='pagePre'><span aria-hidden='true'>«</span></a></li>";
        for($i=1;$i<=$pages;$i++)
       {
            $forUriArray[$key+1]=$i;
            $forUrl=  implode("/", $forUriArray);
            if($i>$currentPage+2 || $i<$currentPage-2)
                continue;
            if($i==$currentPage)
                $show.="<li class='page-item active'><a href='javascript:;' data-page='$i'>$i</a></li>";
            else   
                $show.= "<li class='page-item'><a href='$forUrl' data-page='$i'>$i</a></li>";
        }
        $show.= "<li><a aria-label='Next' href='$nextUrl' class='pageNext'><span aria-hidden='true'>»</span></a></li>";
        if($pagesize>=$nums){
           $showpage['show']="";
        }else{
           $showpage['show']=$show;
        }
        return $showpage;
       }
       
       function array2object($array) {
           if (is_array($array)) {
               $obj = new StdClass();
               foreach ($array as $key => $val){
                   $obj->$key = $val;
               }
           }
           else { $obj = $array; }
           return $obj;
       }
}
