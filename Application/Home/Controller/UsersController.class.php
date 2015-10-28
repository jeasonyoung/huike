<?php
namespace Home\Controller;
use Think\Controller;
class UsersController extends Controller{
    private $Model;
    /*
     * Regist 用户注册
     * 必要参数 $name,$account,$passowrd
     * 返回值：1注册成功;parameter error参数错误;username exists用户名已存在
     */
    public function Regist(){
        $name = I('name');
        $account = I('account');
        $password = md5(I('password'));
        
        if($name == '' || $account == '' || $password == ''){
            $res = 'parameter error';
            exit($this->ajaxReturn($res));
        }
        
        $data = compact('name','account','password');   //组装用户信息
        $result = $this->Model->Regist($data);
        $this->ajaxReturn($result);
    }
    
    public function _initialize(){
        $this->Model = D('Home/Users');
    }
    
    public function _empty(){
        echo '您访问的页面不存在';
    }
}

