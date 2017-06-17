<?php
/**
 * 后台首页
 */
namespace YueSaoads\Controller;

use Common\Controller\AdminbaseController;

class AdsController extends AdminbaseController {

    public function add(){
        if(IS_POST){
            $map = I('post.photos_url',array());
            $user = D('Ads');

            foreach($map as $v){
                $pic = array(
                    'URL.class' =>$v,
                );
                if($user->create($pic)){
                    if($user->add($pic)){
                        $data = array('status'=>1,'message'=>'图片添加成功！', 'URL.class' =>U('YueSaoAds/add'));
                    }else{
                        $data = array('status'=>0,'message'=>'图片添加失败！', 'URL.class' =>U('YueSaoAds/add'));
                    }
                }else{
                    $data = array('status'=>0,'message'=>'数据异常！请重试！', 'URL.class' =>U('YueSaoAds/add'));
                }
            }
            $this->ajaxReturn($data);
        }
        $this->display();
    }

}

