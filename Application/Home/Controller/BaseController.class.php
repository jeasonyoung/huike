<?php
namespace Home\Controller;
use Think\Controller;

class BaseController extends Controller{
    /*登陆判定,权限验证*/
    public function _initialize(){
        if(empty(session('adminid'))){
            $this->error('您没有登陆!',U('login/index'));
        }
        $AUTH = new \Think\Auth();
        if(!in_array(session('adminid'), C('administrator'))){
            if(!$AUTH->check(MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME, session('adminid'))){
                $this->error('你没有权限');
            }
        }
    }
    
}
