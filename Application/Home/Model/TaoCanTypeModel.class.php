<?php
namespace Home\Model;
use Think\Model;

class TaoCanTypeModel extends Model{
    /**
     * 向系统套餐类型表插入用户数据
     * @param array $data 所添加的用户信息
     */
	protected $trueTableName = 'HK_Class_TaoCanType';
	 
    public function insert_class_taocantype($data=array()){
        return $this->add($data);
    }
    
    /*
     * 查询系统套餐类型表数据
     * @param int $tctypeid 套餐类型id
     */
    public function query_class_taocantype($tctypeid){
        if(isset($tctypeid) && !empty($tctypeid)){
            return $this->where('tctypeid='.$tctypeid)->find();
        }else{
            return $this->field('tctypeid,tctypename,discount,create_time,last_time,sortid')->select();
        }
    }
    
    /**
     * 修改系统套餐类型表信息
     * @param array $data 所修改的套餐类型信息，必须包含主键
     */
    public function update_class_taocantype($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除系统套餐类型信息
     * @param int $tctypeid 要删除的套餐类型id
     */
    public function delete_class_taocantype($tctypeid){
        return $this->where('tctypeid='.$tctypeid)->delete();
    }
}