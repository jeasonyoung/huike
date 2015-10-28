<?php
namespace Agency\Controller;
use Think\Controller;

class AdminController extends Controller{
    public function _initialize(){
        if(!session('?UserName')){
            $this->error('未登陆，请登陆！',U('login/login'));
        }
    }
}

