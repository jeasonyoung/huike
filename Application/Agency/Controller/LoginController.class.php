<?php
namespace Agency\Controller;
use Think\Controller;

class LoginController extends Controller{
	
	/*用户登陆控制器*/
	public function login(){
            session('[destroy]'); 
            $this->display();
	}
        public function verify_c(){
        ob_clean();
        $Verify = new \Think\Verify();  
        $Verify->fontSize = 18;  
        $Verify->length   = 4;  
        $Verify->useNoise = false;  
        $Verify->codeSet = '0123456789';  
        $Verify->imageW = 120;  
        $Verify->imageH = 46;   
        $Verify->entry();  
    } 
    private function check_verify($code, $id = ""){  
        $verify = new \Think\Verify();  
        return $verify->check($code, $id);  
    }
    /*用户登陆验证控制器*/
   function LoginCheck(){
        $model = D('Agency/JigouAdmin');
        $verify = I('verify_c');
        $data = array();
        $data['UserName'] = I('UserName');
        $pwd = I('PassWords');
        $data['PassWords'] = md5(I('PassWords'));
        
        if(empty($data['UserName']) || empty($pwd)){
            $this->error('用户名或密码不能为空!',U('Login/login'));
        }
        if(empty($verify)){
            $this->error('验证码不能为空!',U('Login/login'));
        }else if(!$this->check_verify($verify)){
            $this->error('对不起，验证码错误!',U('Login/login'));
        }
        $result = $model->query_jigou_user($data);
        if($result){
            if($result['lock']==0){
                $this->error('对不起无法登陆！用户已经被锁定，请联系管理员',U('Login/login'));
            }
            session('JGUID',$result['uid']);
            session('JGID',$result['jgid']);
            session('UserName',$result['username']);
            session('LastLoginTime',$result['groupid']);
            session('LoginNum',$result['loginnum']);
            session('LastLoginTime',$result['lastlogintime']);            
            $this->success('登陆成功',U('Index/Index'));
        }else{
          $this->error('用户名或密码错误!',U('Login/login'));
        }
    }
}