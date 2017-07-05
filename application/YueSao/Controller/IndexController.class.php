<?php
namespace  YueSao\Controller;
use Common\Controller\HomebaseController;
use Think\Verify;

class IndexController extends HomebaseController{



    public function  index(){
        $user = D('ads');
        $data = $user->limit('4')->select();
        $this->assign('list',$data);
        $this->display();
    }

    /*用户服务使用协议*/
    public function agreement(){
        $this->display();
    }

    /*月嫂展示*/
    public function yuesao_show(){
        $user = D('yuesao');
        $data = $user->select();
        $this->assign('list',$data);
        $temp = D('comment')->field('c.comment,c.create_at,u.avatar,u.name,u.phone')->join('as c left join cmf_user as u on c.user_id = u.id')->select();
        $this->assign('cat',$temp);
        $this->display();
    }

    /*智能匹配月嫂*/
    public function matching(){
        $flag = I('get.flag');
        if($flag > 1){
            if($flag == 2){

            }elseif ($flag == 3){
                $map = I('post.value');
                $where = cookie('where')." and hobby like '%".$map."%'";
                cookie('where',$where);
            }elseif($flag == 4){

            }elseif($flag == 5){
                $flag = 1;
            }
        }else{
            $flag = 1;
            $where = '1=1';
            cookie('where',$where);
        }
        $count = D('yuesao')->where($where)->count();
        $page = new \Think\Page($count,6);
        $show = $page->show();
        $data = D('yuesao')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('list',$data);
        $this->assign('flag',$flag);
        $this->display();
    }

    /*关于我们*/
    public function about(){
        $this->display();
    }

    /*联系我们*/
    public function contact(){
        $this->display();
    }

}