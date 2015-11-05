<?php
namespace Home\Model;
use Think\Model;

class AdminModel extends Model{
    /**
     * 向系统用户表插入用户数据
     * @param array $data 所添加的用户信息
     */
    public function insert_user($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询系统用户表数据
     * @param int $uid 用户id
     */
    public function query_user($uid){
        if(isset($uid) && !empty($uid)){
            return $this->where('adminid='.$uid)->find();
        }else{
            return $this->field('adminid,username,lock,realname,regtime,lastlogintime,auth.title as groupname')
                        ->join('left join hk_admin_group auth ON auth.id=hk_admin.groupid')
                        ->order('adminid desc')
                        ->select();
        }
    }
    
    /**
     * 条件查询用户数据
     * @param string $condition 条件
     * @param string $mulit 返回结果集 true 二维 false一维
     */
    public function queryCondition($condition='',$mulit){
        if($mulit){
            return $this->where($condition)->select();
        }else{
            return $this->where($condition)->find();
        }
    }
    
    /**
     * 修改系统用户信息
     * @param array $data 所修改的用户信息，必须包含主键
     */
    public function update_user($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除系统用户信息
     * @param int $uid 要删除的用户id
     */
    public function delete_user($uid){
        return $this->where('adminid='.$uid)->delete();
    }
    
}