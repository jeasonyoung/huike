<?php
namespace Agency\Model;
use Think\Model;

class JigouAdminModel extends Model{
    /**
     * 查询机构用户信息
     * @param  array $data 用户信息
     */
    public function query_jigou_user($data){
        if(!empty($data['UserName'])){
            return $this->where($data)->find();
        }
        return $this->where($data)->select();
    }
    /**
     * 添加机构用户信息
     * @param  array $data 要添加用户信息
     */
    public function insert_jigou_user($data=array()){
        return $this->add($data);
    }
    /**
     * 修改机构用户信息
     * @param array $data 所修改的用户信息
     */
    public function update_jigou_user($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除机构用户信息
     * @param int $uid 要删除的用户id
     */
    public function delete_jigou_user($uid){
        return $this->where('uid='.$uid)->delete();
    }
}