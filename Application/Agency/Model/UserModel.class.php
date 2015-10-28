<?php
namespace Agency\Model;
use Think\Model;

class UserModel extends Model{
    /**
     * 向学员用户表插入用户数据
     * @param array $data 所添加的学员信息
     */
    public function insert_user($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询学员用户表数据
     * @param int $userid 用户id
     */
    public function query_user($userid){
        if(isset($uid) && !empty($userid)){
            return $this->where('$userid='.$userid)->find();
        }else{
            return $this->field('userid,username,passwords,nickname,userpic,realname,mobile,email,regtime,logintime,loginip,loginnum,lock,locktime,jgid,source')->select();
        }
    }
    
    /**
     * 修改学员用户信息
     * @param array $data 所修改的学员用户信息，必须包含主键
     */
    public function update_user($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除学员用户信息
     * @param int $uid 要删除的学员用户id
     */
    public function delete_user($userid){
        return $this->where('$userid='.$userid)->delete();
    }
}