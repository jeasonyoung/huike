<?php
namespace Home\Model;
use Think\Model;

class ClassTypeModel extends Model{
    /**
     * 向系统班级类型表插入用户数据
     * @param array $data 所添加的用户信息
     */
	protected $trueTableName = 'hk_Class_Type';
	 
    public function insert_class_type($data=array()){
        return $this->add($data);
    }
    
    /*
     * 查询系统班级类型表数据
     * @param int $ctid 班级类型id
     */
    public function query_class_type($ctid){
        if(isset($ctid) && !empty($ctid)){
            return $this->where('ctid='.$ctid)->find();
        }else{
            return $this->field('ctid,classtypename,sortid')->select();
        }
    }
    
    /**
     * 修改系统班级类型表信息
     * @param array $data 所修改的班级类型信息，必须包含主键
     */
    public function update_class_type($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除系统班级类型信息
     * @param int $ctid 要删除的班级类型id
     */
    public function delete_class_type($ctid){
        return $this->where('ctid='.$ctid)->delete();
    }
}