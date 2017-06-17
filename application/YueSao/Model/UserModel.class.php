<?php
namespace YueSao\Model;

use Common\Model\CommonModel;

class UserModel extends CommonModel{

    protected $_auto = array(
        array('create_time','time',3,'function'),
        array('update_time','time',3,'function'),
    );

}