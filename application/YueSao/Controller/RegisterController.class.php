<?php
namespace  YueSao\Controller;
use Common\Controller\HomebaseController;
use Think\Verify;

class RegisterController extends HomebaseController{

    /*用户注册页面,发送验证短信*/
    public function register(){
        if(IS_POST){
            $data['phone'] = I('post.phone');
            $user = D('user');
            if(!$user->where("phone = '$data[phone]'")->find()){
                $code = '';
                for($i=0;$i<6;$i++){
                    $code .= rand(0,9);
                }
                session('code',$code);
                vendor('SMS.SendSMS');  /*引入第三方文件*/
                $send = new \SendSMS();  /*实例化*/
                $send->sendTemplateSMS($data['phone'],array($code,'5'),1);  /*调用方法*/
            }else{
                $data = array('status'=>0,'message'=>'手机号已被注册！');
            }
            $this->ajaxReturn($data);
        }
        $this->display();
    }

    /*注册用户*/
    public function doregister(){
        if(IS_POST){
            $temp['phone'] = I('post.phone');
            $temp['password'] = sha1(I('post.password'));
            $map['code'] = I('post.code');
            if($map['code'] == $_SESSION['code']){
                session('code',null);
                session('phone',$temp['phone']);
                $user = D('user');
                if($user->create($temp)){
                    $user->add();
                    $data = array('status'=>1,'message'=>'恭喜您，注册成功！', 'URL.class' =>U('Index/login'));
                }else{
                    $data = array('status'=>0,'message'=>'数据创建失败！');
                }
            }else{
                $data = array('status'=>0,'message'=>'验证码输入有误！');
            }
            $this->ajaxReturn($data);
        }
    }

}