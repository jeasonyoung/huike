<?php
namespace Agency\Model;
use Think\Model;

class ExamclassModel extends Model{
    /**
     * 向机构班级表插入数据
     * @param array $data 所添加的班级信息
     */
    public function insert_Class($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询考试类目数据
     * @param int $jgcid 班级id
     */
    public function query_exam_class(){
        return $this->select();
    }
    
    /**
     * 修改机构班级信息
     * @param array $data 所修改的班级信息，必须包含主键
     */
    public function update_Class($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除机构班级信息
     * @param int $jgcid 要删除的班级id
     */
    public function delete_user($uid){
        return $this->where('uid='.$uid)->delete();
    }
}