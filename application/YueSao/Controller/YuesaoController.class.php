<?php
/**
 * 后台首页
 */
namespace YueSao\Controller;

use Common\Controller\AdminbaseController;

class YuesaoController extends AdminbaseController {

    /**
     * 添加月嫂
     */
    public function add_yuesao(){
        if(IS_POST){
            if($_FILES['avatar']['name']){
                $res = upload_img($_FILES[avatar][name],3145728,array('jpg', 'gif', 'png', 'jpeg'),'./Uploads/','');
            }
            if($res['status'] == 1 ){
                $temp['avatar'] = $res['picture'];
                $temp['thumb_picture'] = $res['thumb_picture'];
            }
            $temp['name'] = I('post.username','','strip_tags');
            $temp['level'] = I('post.level');
            $temp['age'] = I('post.age','','int');
            $temp['experience'] = I('post.experience','','strip_tags');
            $temp['baby_num'] = I('post.baby_num','','int');
            $temp['address'] = I('post.address','','strip_tags');
            $temp['certificate'] = I('post.option','','int');
            $temp['skill'] = I('post.skill','','strip_tags');
            $temp['price'] = I('post.price','','int');
            $temp['hobby'] = I('post.hobby','','strip_tags');
            $temp['phone'] = I('post.phone','','int');
            $temp['status'] = I('post.status','','int');
            $temp['introduce'] = I('post.content','','strip_tags');

            $user = D('Yuesao');
            if($user->create($temp)){
                $data = $user->addData();
            }else{
                $data = array('status'=>0,'message'=>'数据创建失败！', 'URL.class' =>U('Yuesao/add_yuesao'));
            }

            if($data['status'] == 1){
                /*上传图片*/
                $pics = I('post.photos_url',array());

                if(!empty($pics)){
                    $images =  D('Images');
                    /*插入图片*/
                    foreach($pics as $v){
                        $pic = array(
                            'yuesao_id' => $data['yuesao_id'],
                            'image_url' => $v,
                        );
                        if($images->create($pic)){
                            if(!$images->add()){
                                $data = array('status'=>0,'message'=>'图片添加失败！', 'URL.class' =>U('Yuesao/addPicture'));
                            }
                        }
                    }
                }
            }
            $this->ajaxReturn($data);
        }else{
            $this->display();
        }
    }

//    /*ajax验证用户名是否已存在*/
//    public function checkUsername(){
//        if(IS_AJAX){
//            $data['name'] = I('post.username');
//            $user = D('Yuesao');
//            echo $user->create($data) ? true : false;
//        }
//    }

    /*修改月嫂信息*/
    public function edit(){
        if(IS_POST){
            if($_FILES['avatar']['name']){
                $res = upload_img($_FILES[avatar][name],3145728,array('jpg', 'gif', 'png', 'jpeg'),'./Uploads/','');
            }
            if($res['status'] == 1 ){
                $temp['avatar'] = $res['picture'];
                $temp['thumb_picture'] = $res['thumb_picture'];
            }
            $temp['id'] = I('post.id');
            $temp['name'] = I('post.username','','strip_tags');
            $temp['level'] = I('post.level');
            $temp['age'] = I('post.age','','int');
            $temp['experience'] = I('post.experience','','strip_tags');
            $temp['baby_num'] = I('post.baby_num','','int');
            $temp['address'] = I('post.address','','strip_tags');
            $temp['certificate'] = I('post.option','','int');
            $temp['skill'] = I('post.skill','','strip_tags');
            $temp['price'] = I('post.price','','int');
            $temp['hobby'] = I('post.hobby','','strip_tags');
            $temp['phone'] = I('post.phone','','int');
            $temp['status'] = I('post.status','','int');
            $temp['introduce'] = I('post.content','','strip_tags');
            $user = D('Yuesao');
            $data = $user->editData($temp);

            $this->ajaxReturn($data);
        }else{
            $map['id'] = I('get.id','','int');
            $cat = D('Yuesao')->where($map)->find();
            $this->assign('cat',$cat);
            $this->display();
        }
    }

    /*删除月嫂信息*/
    public function delete(){
        $id = I('get.id');
        $user = D('Yuesao');
        $data = $user->deleteData($id);
        if($data){
            $this->redirect('Yuesao/show');
        }else{
            $this->error("<script>alert('删除失败！');</script>");
        }
    }

    /**
     * 展示列表页
     */
    public function show(){
        $where = '1=1';
        $order = array();

        /*对星级进行删选*/
        $map = I('get.map');
        $data = I('get.data');
        if($map == 'asc'){
            $order[$data] = 'asc';
        }elseif($map == 'desc'){
            $order[$data] = 'desc';
        }

        /*对年龄进行删选*/
        $age = I('post.term','-1','int');
        if(!empty($age)){
            if($age == 0 || $age == 1 || $age == 2 || $age == 3){
                /*年龄分段数组*/
                $age_arr = array(0=>'20-30',1=>'30-40',2=>'40-50',3=>'50-60');
                /*所选年龄的区间*/
                $temp_age = explode('-',$age_arr[$age]);
                $where .= " and age between $temp_age[0] and $temp_age[1] ";
                $this->assign('age',$age);
            }
        }

        /*对等级进行删选*/
        $temp['start_level'] =I('post.start_level','','int');
        $temp['end_level'] =I('post.end_level','','int');
        if(!empty($temp['start_level'] && $temp['end_level'])){
            if($temp['start_level'] != '' && $temp['end_level'] != ''){
                $where .= " and level between $temp[start_level] and $temp[end_level]";
            }elseif($temp['start_level'] != '' and $temp['end_level'] == ''){
                $where .= " and level > $temp[start_level]";
            }elseif($temp['end_level'] != '' and $temp['start_level'] == ''){
                $where .= " and level < $temp[end_level]";
            }
        }

        /*对宝宝数量进行删选*/
        $temp['baby_min_num'] =I('post.baby_min_num','','int');
        $temp['baby_max_num'] =I('post.baby_max_num','','int');
        if(!empty($temp['baby_min_num'] && $temp['baby_max_num'])){
            if($temp['baby_min_num'] != '' && $temp['baby_max_num'] != ''){
                $where .= " and level between $temp[baby_min_num] and $temp[baby_max_num]";
            }elseif($temp['baby_min_num'] != '' and $temp['baby_max_num'] == ''){
                $where .= " and level > $temp[baby_min_num]";
            }elseif($temp['baby_max_num'] != '' and $temp['baby_min_num'] == ''){
                $where .= " and level < $temp[baby_max_num]";
            }
        }

        /*对关键字进行搜索*/
        $keyword = I('post.keyword','','strip_tags');
        if($keyword != ''){
            if(is_numeric($keyword)){
                $where .= " and phone =".$keyword;
            }else{
                $where .= " and name like '%".$keyword."%'";
            }
        }
        $count = D('Yuesao')->where($where)->count();
        $page = new \Think\Page($count,5);
        $show = $page->show();
        $list = D('Yuesao')->where($where)->limit($page->firstRow.','.$page->listRows)->order($order)->select();
        $this->assign('page',$show);
        $this->assign('list',$list);

        $this->display();
    }

    /*展示大图*/
    public function bigPicture(){
        $map['id'] = I('get.id');
        $data = D('Yuesao')->field('avatar')->where($map)->find();
        if(strpos($data['avatar'],';')){  /*对多张图片进行分隔*/
            $data = explode(';',$data['avatar']);
        }
        $this->assign('data',$data);
        $this->display();
    }

    /*快速修改*/
    public function quick_modify(){
        if(IS_POST){
            $id = I('post.id');
            $filedName =I('post.filedName');
            $filedValue = I('post.text');
            $temp[$filedName] = $filedValue;
            $data = D('Yuesao')->quick_modify_data($id,$temp);

            $this->ajaxReturn($data);
        }
    }

    public function addPicture(){
        if(IS_POST){
            $pics = I('post.photos_url',array());
            if(!empty($pics)){
                $images =  D('Images');
                /*插入图片*/
                foreach($pics as $v){
                    $data['yuesao_id'] = I('post.yuesao_id');
                    $pic = array(
                        'yuesao_id' => $data['yuesao_id'],
                        'image_url' => $v,
                    );
                    if($images->create($pic)){
                        if($images->add()){
                            $data = array('status'=>1,'message'=>'图片添加成功！', 'URL.class' =>U('Yuesao/addPicture'));
                        }else{
                            $data = array('status'=>0,'message'=>'图片添加失败！', 'URL.class' =>U('Yuesao/addPicture'));
                        }
                    }
                }
            }
            $this->ajaxReturn($data);
        }else{
            $map['id'] = I('get.id');
            $this->assign('list',$map);
            $this->display();
        }
    }

}

