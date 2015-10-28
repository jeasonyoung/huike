<?php
namespace Agency\Model;
use Think\Model;

class JigouModel extends Model{
    /**
     * 查询机构用户数据
     * @param int $jgid 用户id
     */
    public function query_jigou($jgid){
        if(is_numeric($jgid)){
            return $this->where('jgid='.$jgid)->find();
        }
    }
    
    /**
     * 修改机构用户信息
     * @param array $data 所修改的用户信息，必须包含主键
     */
    public function update_jigou($data = array()){
        return $this->save($data);
    }
}