<?php
namespace Player\Controller;
use Think\Controller;

class IndexController extends Controller{
    public function index(){
        $this->display('./Player/demo1.html');
    }
}
