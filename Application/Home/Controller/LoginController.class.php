<?php
namespace Home\Controller;
use Think\Controller;

class LoginController extends Controller{
    /*用户登陆控制器*/
    public function index(){
        $this->display();
    }
    
    //验证码配置
    public function verify_c(){
        ob_clean();
        $Verify = new \Think\Verify();  
        $Verify->fontSize = 18; 
        $Verify->length   = 4;  
        $Verify->useNoise = false;  
        $Verify->codeSet = '0123456789';  
        $Verify->imageW = 114;  
        $Verify->imageH = 46;   
        $Verify->bg = array(241, 88, 12);
        $Verify->entry();  
    } 
    
    //验证码核对
    private function check_verify($code, $id = ""){  
        $verify = new \Think\Verify();  
        return $verify->check($code, $id);  
    }
    
    //登陆验证
    public function loginCheck(){
        //$user = A('Admin');    //实例化Admin控制器
        $user=M('admin');
        $username = I('UserName');
        $psw = md5(C('md5_key').I('PassWords'));
        $verify = I('verifycode');
        if(empty($username) || empty($psw)){
           $this->error('用户名或者密码为空!'); 
        }
        if(!$this->check_verify($verify)){
            $this->error('对不起，验证码错误!');
        }
        $userinfo = $user->where("username='".$username."' and passwords='".$psw."'")->find();
        if($userinfo['lock'] == 1){
            $this->error('对不起，您的管理帐号已被锁定!');
            die();
        }
        if(!$userinfo){
            $this->error('用户名或密码错误，请重试');
        }else{
            session('username',$userinfo['username']);
            session('adminid',$userinfo['adminid']);
            session('groupid',$userinfo['groupid']);
            session('lastlogintime',$userinfo['lastlogintime']);
            /*记录登陆信息开始*/
            $data = array();
            $db = M('admin');
            $data['AdminID'] = $userinfo['adminid'];
            $data['LoginNum'] = $userinfo['loginnum'] + 1;
            $data['LastLoginTime'] = date('Y-m-d:H:i:s',time());
            $data['LastLoginIP'] = get_client_ip();
            $db->save($data);
            /*记录登陆信息结束*/
            $this->success('登陆成功',U('index/index'));
        }
    }
    
    //退出登陆
    public function logOut(){
        session('username',null);
        session('groupid',null);
        session('adminid',null);
        session('lastlogintime',null);
        $this->success('您已退出登陆',U('Login/Index'));
    }
}