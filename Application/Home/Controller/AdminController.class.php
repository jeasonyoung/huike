<?php
namespace Home\Controller;
use Think\Controller;
//use Home\Controller\BaseController;

class AdminController extends Controller{
    /*添加系统用户*/
    public function add_user(){
        //IS_POST 表示当前请求为POST方式 即表单产生提交
        if(IS_POST){
            $model = D('Home/Admin');
            $data = array();
            $groupid = I('GroupID');  //用户组ID
            $data['UserName'] = I('UserName');
            //密码加上配置文件中的key
            $data['PassWords'] = md5(C('md5_key').I('PassWords'));
            $data['RePassWords'] = md5(C('md5_key').I('RePassWords'));
            $data['Lock'] = I('Lock');
            $data['RealName'] = I('RealName');
            $data['RegTime'] = date('Y-m-d',time());
            $data['LoginNum'] = 0;
            $data['GroupID'] = $groupid;
            if(empty($data['RealName']) || empty($data['UserName']) || empty($data['PassWords'])){
                $this->error('用户名、真实姓名、密码必须填写!');
            }
            if($data['PassWords']!==$data['RePassWords']){
                $this->error('两次输入密码不相同!');
                //exit("alert('两次输入密码不相同');history.go(-1)");
            }
            if($uid=$model->insert_user($data)){
                /*操作权限验证表*/
                $auth = M('admin_group_access');
                $in_data['uid'] = $uid;
                $in_data['group_id'] = $groupid;
                if($auth->add($in_data)){
                    $this->success('新增系统用户成功!',U('admin/add_user'));  
                }else{
                    $this->error('设置权限出现错误');
                }
            }else{
                $this->error('新增系统用户失败!',U('admin/add_user'));
            }
        }else{
            $group = M('admin_group');
            $groupList = $group->field('id,title')->select();
            $this->assign('group',$groupList);
            $this->display();
        }
    }
    
    /**
     * 删除系统用户
     * @param int $AdminId 用户ID
     * @return int 影响行数
     */
    public function del_user($uid){
        $model = D('Home/Admin');
        if($model->delete_user($uid)){
            $this->success('删除用户成功',U('admin/list_user'));
        }else{
            $this->error('删除失败,请联系技术人员');
        }
    }
    
    /**
     * 修改系统用户
     * @param int $AdminId 用户ID
     */
    public function edit_user($uid){
        $model = D('Home/Admin');
        if(IS_POST){    //IS_POST 表示当前请求为POST方式 即表单产生提交
            $data = array();
            $psw = I('PassWords');
            $repsw = I('RePassWords');
            $adminid = I('uid');
            $groupid = I('GroupID');
            if(!empty($psw)){
                if($psw!==$repsw){$this->error('两次输入密码不一样',U('admin/edit_user',array('uid' => $uid)));}
                $data['PassWords'] = md5(C('md5_key').$psw);
            }
            $data['AdminID'] = $adminid;
            $data['UserName'] = I('UserName');
            $data['RealName'] = I('RealName');
            $data['Lock'] = I('Lock');
            $data['GroupID'] = $groupid;
            if($model->update_user($data)){
                //更新权限验证表
                $auth = M('admin_group_access');
                if($auth->where('uid='.$adminid)->setField('group_id',$groupid)){
                    $this->success('用户修改成功',U('admin/list_user'));
                }else{
                    $this->error('更新权限时出现错误!');
                }
            }else{
                $this->error('修改失败或未做修改',U('admin/edit_user',array('uid' => $uid)));
            }
        }else{
            $group = M('admin_group');
            $groupList = $group->field('id,title')->select();
            $data = $model->query_user($uid);
            $this->assign('info',$data);
            $this->assign('group',$groupList);
            $this->display('edit_user');
        }
    }
    
    /*系统用户列表*/
    public function list_user(){
        $model = D('Home/Admin');
        $data = $model->query_user();
        $this->assign('users',$data);
        $this->display();
    }
    
    /**
     * 返回查询结果，供其他控制器调用
     * @param int $adminid 管理员ID
     */
    public function get_data(){
        $adminid = I('aid');
        $model = D('Home/Admin');
        return $model->query_user();
    }
    
    /**
     * 根据用户名，密码得到用户
     * @param string $username 用户名
     * @param string $psw 用户密码
     */
    public function getUser($username,$psw){
        $model = D('Home/Admin');
        return $model->queryCondition("username='$username' and passwords='$psw'",FALSE);
    }
}