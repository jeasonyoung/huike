<?php
namespace Home\Controller;
use Think\Controller;

class BaseController extends Controller{
    public function _initialize(){
        $this->ChkLogin();
    }
    
    private function ChkLogin(){
        if(empty(session('username'))){
            $this->error('您没有登陆!',U('Login/index'));
        }
    }
}

