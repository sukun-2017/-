<?php
namespace YueSao\Model;

use Common\Model\CommonModel;

class OrderModel extends CommonModel{

    protected $_auto = array(
        array('create_at','time',3,'function'),
    );

}