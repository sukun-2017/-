<?php
namespace YueSao\Model;

use Common\Model\CommonModel;

class ImagesModel extends CommonModel{

    protected $_auto = array(
        array('createtime', 'time', 1, 'function'),
        array('updatetime', 'time', 3, 'function'),
    );

}