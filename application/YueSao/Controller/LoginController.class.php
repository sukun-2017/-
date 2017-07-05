<?php
namespace  YueSao\Controller;
use Common\Controller\HomebaseController;
use Think\Verify;

class LoginController extends HomebaseController{

    /*第三方登录*/
    public function callback(){
        $code = I('get.code');
        /*获取access_token*/
        import('Vendor.Github_login.ThinkSDK.ThinkOauth');
        $gitHub_token = \ThinkOauth::getInstance(Github);
        $access_token = $gitHub_token->getAccessToken($code);
        $api = $access_token['access_token'];
        $data = $gitHub_token->call('user?access_token='.$api);
        $this->otherLogin($data);
    }

    /*获取code*/
    public function hitHub_code(){
        import('Vendor.Github_login.ThinkSDK.ThinkOauth');
        $gitHub_code = \ThinkOauth::getInstance('Github');
        redirect($gitHub_code->getRequestCodeURL());
    }

    /*将第三方登录的信息保存到数据库*/
    public function otherLogin($data){
        $temp['name'] = $data['login'];

        $user = D('user');
        if(!($user->where("name = '$temp[name]'")->find())) {
            if ($user->create($temp)) {
                $user->add();
            } else {
                $this->error("<script>alert('登录失败！');</script>");
                return;
            }
        }
        session('username',$temp['name']);
        $this->redirect('Index/index');
    }

    /*用户名登录页面*/
    public function login(){
        if(IS_POST){
            $temp['username'] = I('post.username');
            $temp['password'] = I('post.password');
            session('username',$temp['username']);
            $user = D('user');
            if($meg = $user->where("name = '$temp[username]'")->find()){
                if(sha1($temp['password']) == $meg['password']){

                    $data = array('status'=>1,'message'=>'登录成功！', 'URL.class' =>U('Index/index'));
                }else{
                    $data = array('status'=>0,'message'=>'用户名或密码不正确！');
                }
            }else{
                $data = array('status'=>0,'message'=>'用户名或密码不正确！');
            }
            $this->ajaxReturn($data);
        }else{
            $this->display();
        }

    }

    /*手机号登陆页面获取手机验证码*/
    public function get_code(){
        if(IS_POST){
            $data['phone'] = I('post.phone');
            $user = D('user');
            if($user->where("phone = '$data[phone]'")->find()){
                $code = '';
                for($i=0;$i<6;$i++){
                    $code .= rand(0,9);
                }
                session('code',$code);
                vendor('SMS.SendSMS');  /*引入第三方文件*/
                $send = new \SendSMS();  /*实例化*/
                $send->sendTemplateSMS($data['phone'],array($code,'5'),1);  /*调用方法*/
            }else{
                $data = array('status'=>0,'message'=>'手机号不存在！');
            }
            $this->ajaxReturn($data);
        }
    }

    /*手机号登陆页面*/
    public function doLogin(){
            if(IS_POST){
                $temp['phone'] = I('post.phone');
                $map['code'] = I('post.code');
                if(!empty($map['code'])){
                    if($map['code'] == $_SESSION['code']){
                        session('username',$temp['phone']);
                        session('code',null);
                        $data = array('status'=>1,'message'=>'恭喜您，登陆成功！', 'URL.class' =>U('Index/index'));
                    }else{
                        $data = array('status'=>0,'message'=>'验证码输入有误！');
                    }
                }else{
                    $data = array('status'=>0,'message'=>'验证码不得为空！');
                }
                $this->ajaxReturn($data);
            }
    }

    /*退出登录*/
    public function logout(){
        session_destroy();
        $this->redirect('Index/index');
    }

    /*找回密码*/
    public function findcode(){
            if(!empty($_SESSION['flag'])){
                $flag = $_SESSION['flag'];
                session('flag',null);
            }else{
                $flag =1;
            }
            $this->assign('cat',$flag);
            $this->display();
    }

    /*找回密码--验证手机,获取验证码*/
    public function check_phone(){
        if(IS_POST){
            $data['phone'] = I('post.phone');
            $user = D('user');
            if($user->where("phone = '$data[phone]'")->find()){
                $code = '';
                for($i=0;$i<6;$i++){
                    $code .= rand(0,9);
                }
                session('code',$code);
                vendor('SMS.SendSMS');  /*引入第三方文件*/
                $send = new \SendSMS();  /*实例化*/
                $send->sendTemplateSMS($data['phone'],array($code,'5'),1);  /*调用方法*/
            }else{
                $data = array('status'=>0,'message'=>'手机号不存在！');
            }
            $this->ajaxReturn($data);
        }
        $this->display();
    }

    /*找回密码--验证手机验证码*/
    public function check_phonecode(){
        if(IS_POST){
            $temp['code'] = I('post.code');
            if(!empty($_SESSION['code'])){
                if($temp['code'] == $_SESSION['code']){
                    session('code',null);
                    session('flag',2);
                    $data = array('status'=>1, 'URL.class' =>U('Index/findcode'));
                }else{
                    $data = array('status'=>0,'message'=>'手机验证码不正确！');
                }
            }else{
                $data = array('status'=>0,'message'=>'请先获取验证码！');
            }
            $this->ajaxReturn($data);
        }
    }

    /*找回密码--验证验证码*/
    public function check_code(){
        if(IS_POST){
            $temp['phone'] = I('post.phone');
            $temp['code'] = I('post.code');
            $user = D('user');
            if($user->where("phone = '$temp[phone]'")->find()){
                $verify = new Verify();
                if($verify->check($temp['code'])){
                    session('code',null);
                    session('phone',$temp['phone']);
                    session('flag',3);
                    $data = array('status'=>1, 'URL.class' =>U('Index/findcode'));
                }else{
                    $data = array('status'=>0,'message'=>'验证码输入有误！');
                }
            }else{
                $data = array('status'=>0,'message'=>'手机号不正确，请重新输入手机号！');
            }
            $this->ajaxReturn($data);
        }
    }

    /*找回密码--设置新密码*/
    public function check_password(){
        if(IS_POST){
            $temp['password'] = sha1(I('post.password'));
            $temp['repassword'] = sha1(I('post.repassword'));
            $map['phone'] = $_SESSION['phone'];
            $user = D('user');
            if($temp['password'] == $temp['repassword']){
                if($user->where($map)->find()){
                    if($user->where($map)->save($temp)){
                        $data = array('status'=>1,'message'=>'密码重置成功！', 'URL.class' =>U('Index/login'));
                    }else{
                        $data = array('status'=>0,'message'=>'密码重置失败！');
                    }
                }else{
                    $data = array('status'=>0,'message'=>'找不到此用户！');
                }
            }else{
                $data = array('status'=>0,'message'=>'密码确认不一致！');
            }
            $this->ajaxReturn($data);
        }
    }

}