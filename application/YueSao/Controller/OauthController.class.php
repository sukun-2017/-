<?php
namespace  YueSao\Controller;

use Common\Controller\HomebaseController;


class OauthController extends HomebaseController{

    public function index(){

        import('Vendor.QQConnect.API.class.Oauth');
//        vendor('QQConnect.API.class.Oauth',' ','.class.php');
        $login = new \Oauth();//这个也没错啊.....
        $login->qq_login();
        $this->display();
    }

}