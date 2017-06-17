<?php
/**
 * 后台首页
 */
namespace YueSaoOrder\Controller;

use Common\Controller\AdminbaseController;

class OrderController extends AdminbaseController {
    /*展示所有订单*/
    public function all_order(){
        $user = D('Order');
        $data = $user->field('o.*,u.name as username,y.name as yuesaoname')
                     ->where('is_del=1')
                     ->join('as o left join cmf_user as u on o.user_id = u.id left join cmf_yuesao as y on o.yuesao_id = y.id')
                     ->select();
        $this->assign('list',$data);

        $this->display();
    }

    /*订单详情*/
    public function details(){
        $map['o.id'] = I('get.id');
        $user = D('Order');
        $data = $user->field('o.*,u.name as username,y.name as yuesaoname')
                     ->where($map)
                     ->join('as o left join cmf_user as u on o.user_id = u.id left join cmf_yuesao as y on o.yuesao_id = y.id')
                     ->find();
        $this->assign('list',$data);

        $this->display();
    }

    /*展示未付定金订单*/
    public function front_money(){
        $user = D('Order');
        $data = $user->field('o.*,u.name as username,y.name as yuesaoname')->where('is_del=1 and o.status=1')->join('as o left join cmf_user as u on o.user_id = u.id left join cmf_yuesao as y on o.yuesao_id = y.id')->select();
        $this->assign('list',$data);

        $this->display();
    }

    /*展示未付余款订单*/
    public function spare_money(){
        $user = D('Order');
        $data = $user->field('o.*,u.name as username,y.name as yuesaoname')->where('is_del=1 and o.status=2')->join('as o left join cmf_user as u on o.user_id = u.id left join cmf_yuesao as y on o.yuesao_id = y.id')->select();
        $this->assign('list',$data);

        $this->display();
    }

    /*展示未评价的订单*/
    public function no_evaluate(){
        $user = D('Order');
        $data = $user->field('o.*,u.name as username,y.name as yuesaoname')->where('is_del=1 and o.status=3')->join('as o left join cmf_user as u on o.user_id = u.id left join cmf_yuesao as y on o.yuesao_id = y.id')->select();
        $this->assign('list',$data);

        $this->display();
    }

    /*展示已完成的订单*/
    public function is_done(){
        $user = D('Order');
        $data = $user->field('o.*,u.name as username,y.name as yuesaoname')->where('is_del=1 and o.status=4')->join('as o left join cmf_user as u on o.user_id = u.id left join cmf_yuesao as y on o.yuesao_id = y.id')->select();
        $this->assign('list',$data);

        $this->display();
    }

    /*展示已关闭的订单*/
    public function is_close(){
        $user = D('Order');
        $data = $user->field('o.*,u.name as username,y.name as yuesaoname')->where('is_del=1 and o.status=0')->join('as o left join cmf_user as u on o.user_id = u.id left join cmf_yuesao as y on o.yuesao_id = y.id')->select();
        $this->assign('list',$data);

        $this->display();
    }

    /*删除单条记录*/
    public function delete_one(){
        $map['id'] = I('get.id');
        if(D('Order')->where($map)->delete()){
            $this->redirect('Order/all_order');
        }else{
            $this->getError("<script>alert('删除失败！');</script>");
        }
    }

    /*批量删除*/
    public function delete(){
        $id = I('post.id');
        $map = substr($id,1);
        if(D('Order')->where('id in ('.$map.')')->delete()){
            $data = array('status'=>1,'message'=>'删除成功！');
        }else{
            $data = array('status'=>0,'message'=>'删除失败！');
        }
        $this->ajaxReturn($data);
    }

    /*修改顾客信息*/
    public function modify(){
        $key = I('post.field_key');
        if($key == 'serve_time'){
            $map['serve_time'] = strtotime(I('post.field_value'));
            $id = I('post.id');
            if(D('Order')->where("id = '$id'")->save($map)){
                $data = array('status'=>1,'message'=>'服务时间更改成功！');
            }else{
                $data = array('status'=>0,'message'=>'服务时间更改失败，请重试！');
            }
        }else{
            $id = I('post.id');
            $map['name'] = I('post.field_value');

            /*修改余款*/
            $temp['deposit'] = I('post.deposit');
            $data = D('Yuesao')->field('price')->where($map)->find();
            $m = D('Order')->field('status')->where("id = '$id'")->find();
            if($m['status'] == 2){
                $yk['balance'] = $data['price'] - $temp['deposit'];
                D('Order')->where("id = '$id'")->save($yk);
            }

            if($map = D('Yuesao')->field('id')->where($map)->find()){
                $meg = array(
                    'user_id'=>$id,
                    'yuesao_id'=>$map['id'],
                );
                if(D('Order')->where("id = '$id'")->save($meg)){
                    $data = array('status'=>1,'message'=>'月嫂更改成功！');
                }else{
                    $data = array('status'=>0,'message'=>'月嫂更改失败，请重试！');
                }
            }else{
                $data = array('status'=>0,'message'=>'未找到该月嫂或月嫂不存在！');
            }
        }
        $this->ajaxReturn($data);
    }



}

