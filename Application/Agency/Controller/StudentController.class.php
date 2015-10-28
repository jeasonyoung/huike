<?php
namespace Agency\Controller;
use Think\Controller;

class StudentController extends Controller{
   /*添加学员用户*/
    public function add_user(){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        if(IS_POST){
            $model = D('Agency/User');
            $data = array();
            $data['UserName'] = I('UserName');
            $data['PassWords'] = md5(I('PassWords'));
            $data['NickName'] = I('NickName');
            $data['UserPic'] = I('UserPic');
            $data['Mobile'] = I('Mobile');
            $data['Email'] = I('Email');
            $data['RegTime'] = date('Y-m-d',time());
            $data['LoginTime'] = I('LoginTime');
            $data['LoginIP'] = I('LoginIP');        
            $data['LoginNum'] = 0;
            $data['Lock'] = I('Lock');
            $data['LockTime'] = I('LockTime');
            $data['JGID'] = I('JGID');
            $data['Source'] = I('Source');
            
            if(empty($data['RealName']) || empty($data['UserName']) || empty($data['PassWords'])){
                $this->error('用户名、密码必须填写!');
            }
            if($model->insert_user($data)){
                $this->success('新增系统用户成功!',U('jigou/add_user'));
            }else{
                $this->error('新增系统用户失败!',U('jigou/add_user'));
            }
        }else{
            $this->display();
        }
    }
    
    /**
     * 锁定机构用户
     * @param int $uid 用户ID
     * @return int 影响行数
     */
    public function lock_user($uid){
        $model = D('Agency/JigouAdmin');
        if($model->lock_user($uid)){
            $this->success('锁定成功',U('jigou/list_user'));
        }else{
            $this->error('锁定失败,请联系技术人员');
        }
    }
    
    /**
     * 修改机构用户
     * @param int $AdminId 用户ID
     */
    public function edit_user($uid){
        $model = D('Agency/JigouAdmin');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
            $data = array();
            $psw = I('PassWords');
            $repsw = I('RePassWords');
            if(!empty($psw)){
                if($psw!==$repsw){$this->error('两次输入密码不一样',U('jigou/edit_user',array('uid' => $uid)));}
                $data['PassWords'] = md5(C('psw_key').$psw);
            }
            $data['UID'] = I('uid');
            $data['UserName'] = I('UserName');
            $data['RealName'] = I('RealName');
            $data['Lock'] = I('Lock');
            if($model->update_user($data)){
                $this->success('用户修改成功',U('jigou/list_user'));
            }else{
                $this->error('修改失败或未做修改',U('jigou/edit_user',array('uid' => $uid)));
            }
        }else{
            $data = $model->query_user($uid);
            $this->assign('info',$data);
            $this->display('edit_user');
        }
    }
    
    /*机构用户列表*/
    public function list_user(){
        $model = D('Agency/JigouAdmin');
        $data = $model->query_user();
        $this->assign('users',$data);
        $this->display();
    }
}