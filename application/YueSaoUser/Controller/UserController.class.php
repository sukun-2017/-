<?php
namespace YueSaoUser\Controller;

use Common\Controller\AdminbaseController;

class UserController extends AdminbaseController {

    public function show(){


        if(IS_POST){
            /*对关键字进行筛选*/
            $where = "1=1";
            $keyword = I('post.keyword','','strip_tags');
            if($keyword){
                if(is_numeric($keyword)){
                    $where .= " and phone =".$keyword;
                }else{
                    $where .= " and name like '%".$keyword."%'";
                }
            }

            /*对预产期进行筛选*/
            $map['start_childbirth_date'] = strtotime(I('post.start_childbirth_date'));
            $map['end_childbirth_date'] = strtotime(I('post.end_childbirth_date'));
            if($map['start_childbirth_date'] != '' && $map['end_childbirth_date'] != ''){
                $where .= " and childbirth_date between $map[start_childbirth_date] and $map[end_childbirth_date]";
            }elseif($map['start_childbirth_date'] != '' && $map['end_childbirth_date'] == ''){
                $where .= " and childbirth_date gt $map[start_childbirth_date]";
            }elseif($map['start_childbirth_date'] == '' && $map['end_childbirth_date'] != ''){
                $where .= " and childbirth_date lt $map[end_childbirth_date]";
            }

            $count = D('user')->where($where)->count();
            $page = new \Think\Page($count,5);
            $show = $page->show();
            $list = D('user')->where($where)->limit($page->firstRow.','.$page->listRows)->select();
            $this->assign('page',$show);
            $this->assign('list',$list);
            $this->display();

        }else{
            $count = D('user')->count();
            $page = new \Think\Page($count,5);
            $show = $page->show();
            $list = D('user')->where($where)->limit($page->firstRow.','.$page->listRows)->select();
            $this->assign('page',$show);
            $this->assign('list',$list);

            $this->display();
        }
    }

    public function details(){
        if(IS_GET){
            $temp['u.id'] = I('get.id');
            $data = D('Order')->alias('o')->field('o.*,y.name,u.avatar')->join('left join cmf_user as u on o.user_id=u.id left join cmf_yuesao as y on o.yuesao_id=y.id')->where($temp)->find();

            $this->assign('list',$data);

            $this->display();
        }
    }

    public function delete(){
        $map['id'] = I('get.id','','int');

        if(D('user')->where($map)->delete()){
            $this->redirect('User/show');
        }else{
            $this->error('<sctipt>alert(数据删除失败！);</sctipt>');
        }
    }

    public function member(){
        $map['id'] = I('get.id');
        $data = D('user')->where($map)->find();
        $this->assign('list',$data);

        $this->display();
    }

}
