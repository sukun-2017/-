<?php
namespace Front\Controller;

use Common\Controller\HomebaseController;

class FrontController extends HomebaseController {

    public function _initialize(){
        parent::_initialize();
    }

    public function index(){
        $this->display(':index');
    }

}
