<?php
namespace Agency\Model;
use Think\Model;

class JigouClassModel extends Model{
    /**
     * 向机构班级表插入数据
     * @param array $data 所添加的班级信息
     */
    public function insert_Class($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询机构班级表数据
     * @param int $jgcid 班级id
     */
    public function query_class($jgcid){
        if(isset($jgcid) && !empty($jgcid)){
            return $this->where('jgcid='.$jgcid)->find();
        }else{
            return $this->field('jgcid,scid,cnname,desc,picpath,jgid,usryear,sale_price,price,statetf,classnum,listenid,create_time,last_time,sortid')->select();
        }
    }
    
    /**
     * 修改机构班级信息
     * @param array $data 所修改的班级信息，必须包含主键
     */
    public function update_class($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除机构班级信息
     * @param int $jgcid 要删除的班级id
     */
    public function delete_class($jgcid){
        return $this->delete($jgcid);
    }
}