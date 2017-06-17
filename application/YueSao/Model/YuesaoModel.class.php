<?php
namespace YueSao\Model;

use Common\Model\CommonModel;

class YueSaoModel extends CommonModel{

    protected $_validate = array(
        array('name','2,20','账号长度不正确！',self::EXISTS_VALIDATE,'length'),
        array('level','require','等级不得为空！'),
        array('age','require','年龄不得为空！'),
        array('price','require','价格不得为空！'),
        array('phone','require','电话不得为空！'),

        array('name','','用户名已存在！',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
    );

    protected $_auto = array(
        array('create_time','time',1,'function'),
        array('update_time','time',3,'function'),
    );

    public function addData($temp){
        /*添加月嫂*/
        if($yuesao_id = $this->add($temp)){
            $data = array('status'=>1,'message'=>'数据添加成功！', 'URL.class' =>U('Yuesao/show'),'yuesao_id'=>$yuesao_id);
        }else{
            $data = array('status'=>0,'message'=>'数据添加失败！', 'URL.class' =>U('Yuesao/show'));
        }
        return $data;
    }

    public function editData($temp){
        /*修改数据*/
        $map['id'] = $temp['id'];
        if($this->where($map)->save($temp)){
            $data = array('status'=>1,'message'=>'数据修改成功！', 'URL.class' =>U('Yuesao/show'));
        }else{
            $data = array('status'=>0,'message'=>'数据修改失败！', 'URL.class' =>U('Yuesao/edit'));
        }
        return $data;
    }

    public function deleteData($id){
        /*删除数据*/
        $map['id'] = $id;
        if($this->where($map)->delete()){
            return true;
        }else{
            return false;
        }
    }

    public function addPicture($temp){
        $map['id'] = $temp['id'];
        if($this->where($map)->save($temp)){
            $data = array('status'=>1,'message'=>'图片添加成功！', 'URL.class' =>U('Yuesao/show'));
        }else{
            $data = array('status'=>0,'message'=>'图片添加失败！');
        }
        session('id',null);
        return $data;
    }

    public function quick_modify_data($id,$temp){
        $map['id'] = $id;
        if($this->where($map)->save($temp)){
            $data = array('status'=>1,'message'=>'数据修改成功！', 'URL.class' =>U('Yuesao/show'));
        }else{
            $data = array('status'=>0,'message'=>'数据修改失败！');
        }
        return $data;
    }

}