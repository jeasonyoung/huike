<?php
namespace Agency\Controller;
use Agency\Controller\AdminController;

class JigouController extends AdminController{
    
    /**
     * 修改当前机构信息
     * @param int $jgid 用户ID
     */
    public function edit_jigou(){
        $model = D('Agency/Jigou');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交     
            $data = $model->create();
            if($model->update_jigou($data)){
                $this->success('修改成功',U('jigou/query_jigou'));
            }else{
                $this->error('修改失败或未做修改',U('jigou/query_jigou'));
           }
        }else{
            $data = $model->query_jigou(session('JGID'));
            $this->assign('info',$data);
            $this->display('query_jigou');
        }
    }
    
    /*查询机构用户登陆的机构信息*/
    public function query_jigou(){
        $model = D('Agency/Jigou');
        $data = $model->query_jigou(session('JGID'));
        $this->assign('info',$data);
        $this->display();
    }
    /*添加当前机构的用户*/
    public function add_jigou_user(){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        if(IS_POST){
            $model = D('Agency/JigouAdmin');
            $data = array();
            $data['UserName'] = I('UserName');
            //密码加上配置文件中的key
            $data['PassWords'] = md5(C('psw_key').I('PassWords'));
            $data['RePassWords'] = md5(C('psw_key').I('RePassWords'));
            $data['Lock'] = I('Lock');
            $data['JGID'] = session('JGID');
            $data['GroupID'] = I('GroupID');
            $data['RegTime'] = date('Y-m-d H:i:s',time());
            $data['RealName'] = I('RealName');
            $data['LoginNum'] = 0;
            if(empty($data['RealName']) || empty($data['UserName']) || empty($data['PassWords'])){
                $this->error('用户名、真实姓名、密码必须填写!');
            }
            if($data['PassWords']!==$data['RePassWords']){
                $this->error('两次输入密码不相同!');
                //exit("alert('两次输入密码不相同');history.go(-1)");
            }
            if($model->insert_jigou_user($data)){
                $this->success('新增系统用户成功!',U('jigou/list_jigou_user'));
            }else{
                $this->error('新增系统用户失败!');
            }
        }else{
            $this->display();
        }
    }
    /**
     * 修改当前机构下的用户信息
     * @param int $jgid 用户ID
     */
    public function edit_jigou_user($uid,$username){
        $model = D('Agency/JigouAdmin');
        
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
            $data = array();
            $psw = I('PassWords');
            $repsw = I('RePassWords');
            if(!empty($psw)){
                if($psw!==$repsw){$this->error('两次输入密码不一样');}
                $data['PassWords'] = md5(C('md5_key').$psw);
            }
            
            $data['UID'] = I('uid');
            $data['UserName'] = I('UserName');
            $data['RealName'] = I('RealName');
            $data['Lock'] = I('Lock');
           
            if($model->update_jigou_user($data)){
                $this->success('用户修改成功',U('jigou/list_jigou_user'));
            }else{
                $this->error('修改失败或未做修改',U('jigou/edit_jigou_user',array('uid' => $uid,'username' => $username)));
            }
        }else{
            $data = array();
            $data['UID'] = $uid;
            $data['UserName'] = $username;
            $info = $model->query_jigou_user($data);  
            $this->assign('info',$info);
            $this->display();
        }
    }
    /**
     * 删除系统用户
     * @param int $uid 用户ID
     * @return int 影响行数
     */
    public function del_jigou_user($uid){
        $model = D('Agency/JigouAdmin');
        if($model->delete_jigou_user($uid)){
            $this->success('删除用户成功',U('jigou/list_jigou_user'));
        }else{
            $this->error('删除失败,请联系技术人员');
        }
    }
    /*机构管理用户列表*/
    public function list_jigou_user(){
        $model = D('Agency/JigouAdmin');
        $data['JGID'] = session('JGID');
        $users = $model->query_jigou_user($data);
        $this->assign('users',$users);
        $this->display();
    }
}