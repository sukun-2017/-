<?php
namespace  YueSao\Controller;
use Common\Controller\HomebaseController;
use Guzzle\Http\Message\Response;
use Think\Verify;

class UserController extends HomebaseController{

    /*个人中心*/
    public function profile(){
        if(is_numeric(session('username'))){
            $map['phone'] = session('username');
        }else{
            $map['name'] = session('username');
        }
        $user = D('user');
        $data = $user->where($map)->find();
        $this->assign('list',$data);
        $this->display();
    }

    /*修改个人信息*/
    public function edit(){
        $data = I('request.');
        if(is_numeric($_SESSION['username'])){
            $map['phone'] = $_SESSION['username'];
        }else{
            $map['name'] = $_SESSION['username'];
        }

        if(!(empty($data['password']) && empty($data['repassword']))){
            $data['password'] = sha1(I('request.password'));
            $data['repassword'] = sha1(I('request.repassword'));
        }else{
            $data['password'] = $data['repassword'] = D('user')->field('password')->where($map)->find();
        }

        if($_FILES['avatar']['name']){
            $res = upload_img($_FILES[avatar][name],3145728,array('jpg', 'gif', 'png', 'jpeg'),'./Uploads/','');
        }
        if($res['status'] == 1 ){
            $data['avatar'] = $res['picture'];
//            $temp['thumb_picture'] = $res['thumb_picture'];
        }

        $user = D('user');
        if($data['password'] == $data['repassword']){
            if($user->create($data)){
                if(is_numeric($_SESSION['username'])){
                    if($user->where($map)->save()){
                        $this->redirect('User/profile');
                    }else{
                        $this->error("<script>alert('更新数据失败！');</script>");
                    }
                }else{
                    if($user->where($map)->save()){
                        $this->redirect('User/profile');
                    }else{
                        $this->error("<script>alert('更新数据失败！');</script>");
                    }
                }

            }else{
                $this->error("<script>alert('保存失败！');</script>");
            }
        }else{
            $this->error("<script>alert('密码输入不一致！');</script>");
        }
    }

    /*我的订单*/
    public function myOrder(){
        $map['name'] = session('username');
        $user = D('user');
        $data = $user->where($map)->find();
        $count = D('order')->where("user_id = '$data[id]'")->join('as o left join cmf_yuesao as y on o.yuesao_id = y.id')->count();
        $page = new \Think\Page($count,4);
        $show = $page->show();
        $temp = D('order')->field('o.*,y.avatar,y.name,y.price')
            ->where("user_id = '$data[id]'")->join('as o left join cmf_yuesao as y on o.yuesao_id = y.id')
            ->limit($page->firstRow.','.$page->listRows)->select();
        $money = 0.1;
        $this->assign('money',$money);
        $this->assign('page',$show);
        $this->assign('cat',$temp);
        $this->assign('list',$data);
        $this->display();
    }

    /*待付订单*/
    public function no_pay(){
        $map['name'] = $_SESSION['username'];
        $user = D('user');
        $data = $user->where($map)->find();
        $count = D('order')->where("user_id = '$data[id]' and o.status = 1")->join('as o left join cmf_yuesao as y on o.yuesao_id = y.id')->count();
        $page = new \Think\Page($count,1);
        $show = $page->show();
        $temp = D('order')->field('o.*,y.avatar,y.name,y.price')->where("user_id = '$data[id]' and o.status = 1")
            ->limit($page->firstRow.','.$page->listRows)
            ->join('as o left join cmf_yuesao as y on o.yuesao_id = y.id')->select();
        $money = 0.1;
        $this->assign('money',$money);
        $this->assign('page',$show);
        $this->assign('cat',$temp);
        $this->assign('list',$data);
        $this->display();
    }

    /*待付余款*/
    public function leave_money(){
        $map['name'] = $_SESSION['username'];
        $user = D('user');
        $data = $user->where($map)->find();
        $count = D('order')->where("user_id = '$data[id]' and o.status = 2")->join('as o left join cmf_yuesao as y on o.yuesao_id = y.id')->count();
        $page = new \Think\Page($count,1);
        $show = $page->show();
        $temp = D('order')->field('o.*,y.avatar,y.name,y.price')->where("user_id = '$data[id]' and o.status = 2")->join('as o left join cmf_yuesao as y on o.yuesao_id = y.id')->select();
        $money = 0.1;
        $this->assign('money',$money);
        $this->assign('page',$show);
        $this->assign('cat',$temp);
        $this->assign('list',$data);
        $this->display();
    }

    /*没有评价的订单*/
    public function no_comment(){
        $map['name'] = $_SESSION['username'];
        $user = D('user');
        $data = $user->where($map)->find();
        $count = D('order')->where("user_id = '$data[id]' and o.status = 3")->join('as o left join cmf_yuesao as y on o.yuesao_id = y.id')->count();
        $page = new \Think\Page($count,1);
        $show = $page->show();
        $temp = D('order')->field('o.*,y.avatar,y.name,y.price')->where("user_id = '$data[id]' and o.status = 3")
            ->limit($page->firstRow.','.$page->listRows)
            ->join('as o left join cmf_yuesao as y on o.yuesao_id = y.id')->select();
        $money = 0.1;
        $this->assign('money',$money);
        $this->assign('page',$show);
        $this->assign('cat',$temp);
        $this->assign('list',$data);
        $this->display();
    }

    /*我的收藏*/
    public function my_mark(){
        $map['name'] = session('username');
        $user = D('user');
        $temp = $user->field('id as user_id')->where($map)->find();
        $count = D('favorite')->where($temp)->count();
        $page = new \Think\Page($count,2);
        $show = $page->show();
        $data = D('favorite')->where($temp)->field('y.*,f.id as favorite_id,f.yuesao_id')
            ->limit($page->firstRow.','.$page->listRows)
            ->join('as f left join cmf_yuesao as y on f.yuesao_id = y.id')->select();
        $this->assign('page',$show);
        $this->assign('list',$data);
        $this->display();
    }

    /*收藏页面的去看看操作*/
    public function yuesao_details(){
        if(isset($_SESSION['username'])){
            $id = I('get.id');
            $user = D('yuesao');
            $data = $user->where("id = '$id'")->find();
            $this->assign('list',$data);
            $this->display();
        }else{
            $this->redirect('Login/login');
        }
    }

    /*收藏里的删除操作*/
    public function delete(){
        $id = I('get.id');
        $user = D('favorite');
        if($user->where("id = '$id'")->delete()){
            $this->redirect('Index/my_mark');
        }else{
            $this->error("<script>alert('删除失败，请重新操作！');</script>");
        }
    }

    /*浏览历史*/
    public function footprint(){
        $map['name'] = $_SESSION['username'];
        $temp = D('user')->where($map)->find();
        $data = D('browsing_log')->field('y.*,b.browsing_time,b.id as b_id')->where("user_id = '$temp[id]'")->join('as b left join cmf_yuesao as y on y.id = b.yuesao_id')->select();
        $this->assign('list',$data);
        $this->display();
    }

    /*浏览记录里的删除操作*/
    public function b_delete(){
        $id = I('get.id');
        if(D('browsing_log')->where("id = '$id'")->delete()){
            $this->redirect('Index/footprint');
        }else{
            $this->error("<script>alert('删除失败，请重新操作！');</script>");
        }
    }

    /*订单详情*/
    public function order_detail(){
        $id = I('get.id');
        $user = D('order');
        $temp = $user->field('u.*,o.*,y.*,u.phone as user_phone,y.phone as yuesao_phone,u.name as user_name,y.name as yuesao_name,o.status as order_status,y.status as yuesao_status,o.id as order_id,u.id as user_id,y.id as yuesao_id,u.avatar as user_avatar,y.avatar as yuesao_avatar')
            ->join('as o left join cmf_user as u on o.user_id = u.id left join cmf_yuesao as y on o.yuesao_id = y.id')
            ->where("o.id = '$id'")->find();
        $this->assign('list',$temp);
        $this->display();
    }

    /*付款*/
    public function pay(){
        $id = I('get.id');
        $user = D('order');
        $data = $user->where("o.id = '$id'")
                     ->field('o.*,y.name as yuesao_name,u.name as user_name,y.price')
                     ->join('as o left join cmf_yuesao as y on o.yuesao_id = y.id left join cmf_user as u on o.user_id = u.id')->find();
        $temp = 0.1;
        $this->assign('cat',$temp);
        $this->assign('list',$data);
        $this->display();
    }

    /*取消订单*/
    public function cancel(){
        $id = I('get.id');
        $user = D('order');
        if($user->where("id = '$id'")->delete()){
            $this->redirect('Index/myOrder');
        }else{
            $this->error("<script>alert('取消失败，请重新操作！');</script>");
        }
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

    /*月嫂展示里的去看看--预约月嫂页面*/
    public function yuesao_appointment(){
        if(isset($_SESSION['username'])){
            $id = I('get.id');
            $data = D('yuesao')->where("id = '$id'")->find();
            $this->assign('list',$data);
            $this->display();
        }else{
            $this->redirect('Login/login');
        }
    }

    /*用户约定月嫂*/
    public function make_order(){
        if(IS_POST){
            $map['name'] = I('post.name');
            $temp = D('user')->field('id as user_id')->where($map)->find();
            $temp['yuesao_id'] = I('post.yuesao_id');
            $temp['phone'] = I('post.phone');
            $temp['serve_time'] = I('post.serve_time');
            $temp['serve_address'] = I('post.serve_address');
            $temp['leave_message'] = I('post.leave_message');
            $temp['create_number'] = date('Ymd',time()).rand(100000,999999);
            $user = D('order');
            if($user->create($temp)){
                if($user->add()){
                    $data = array('status'=>0,'message'=>'数据添加成功！', 'URL.class' =>U('Index/myOrder'));
                }else{
                    $data = array('status'=>0,'message'=>'数据添加失败！');
                }
            }else{
                $data = array('status'=>0,'message'=>'创建数据失败！');
            }
            $this->ajaxReturn($data);
        }
    }

    /*用户评价页面*/
    public function comment(){
        $id = I('get.id');
        $data = D('order')->field('y.*,o.id as order_id,o.create_at,o.create_number')->where("o.id = '$id'")->join('as o left join cmf_yuesao as y on o.yuesao_id = y.id')->find();
        $this->assign('list',$data);
        $this->display();
    }

    /*对月嫂的评价*/
    public function docomment(){
        $map['name'] = $_SESSION['username'];
        $temp = D('user')->field('id')->where($map)->find();
        $temp['yuesao_id'] = I('post.yuesao_id');
        $temp['order_id'] = I('post.order_id');
        $temp['comprehensive_score'] = I('post.comprehensive_score');
        $temp['skill_score'] = I('post.skill_score');
        $temp['attitude_score'] = I('post.attitude_score');
        $wa['status'] = 4;
        $user = D('comment');
        if($user->create($temp)){
            if($user->add()){
                D('order')->where("id = '$temp[order_id]'")->save($wa);
               $data = array('status'=>1,'message'=>'评论成功！', 'URL.class' =>U('User/myOrder'));
            }else{
                $data = array('status'=>0,'message'=>'添加评论失败！');
            }
        }else{
            $data = array('status'=>0,'message'=>'数据创建失败！');
        }
        return $data;
    }

}