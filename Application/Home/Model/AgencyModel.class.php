<?php
namespace Home\Model;
use Think\Model;

class AgencyModel extends Model{
    protected $trueTableName = 'HK_JiGou';
    //添加合作机构
    public function insert_agency($data){
        return $this->add($data);
    }
    
    public function query_agency($field,$condition,$mulit){
        if($mulit){return $this->where($condition)->field($field)->select();}
        return $this->where($condition)->field($field)->find(); 
    }
    
    /**
     * 更新机构
     * @param array $data 机构数据
     */
    public function update_agency($data=array()){
        return $this->save($data);
    }
    
    //删除机构
    public function delete_agency($jgid){
        return $this->where('jgid='.$jgid)->delete();
    }

    /**
     * 机构列表数据[后期需加入分页]
     */
    public function get_agencyList($field,$condition="domain <> ''",$mulit=true){
        $where[]['_string']=$condition;
        if($mulit){
            return $this->where($where)->field($field)->
                   join('LEFT JOIN hk_admin a ON a.adminid=hk_jigou.kefuid')->
                   join('LEFT JOIN hk_admin b ON a.adminid=hk_jigou.marketid')->
                   order('create_time asc')->
                   select();
        }else{
            return $this->where($where)->field($field)->find();  
        }
    }
    
    /**
     * 添加机构用户
     * @param array $user 机构用户数据
     */
    public function insert_user($user=array()){
        $db = M('jigou_admin');
        return $db->add($user);
    }
    
    /**
     * 查询机构管理员信息
     * @param string $condition 查询条件
     * @param $mulit 返回结果集
     */
    public function query_user($condition,$mulit){
        $db = M('jigou_admin');
        $where[]['_string']=$condition;
        if($mulit){
            return $db->where($where)->select(); 
        }else{
            return $db->where($where)->find(); 
        }
    }
}