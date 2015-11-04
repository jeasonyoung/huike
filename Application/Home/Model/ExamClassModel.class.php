<?php
namespace Home\Model;
use Think\Model;

class ExamClassModel extends Model{
    /**
     * 向考试表插入考试数据
     * @param array $data 所添加的考试信息
     */
	protected $trueTableName = 'HK_ExamClass';
	
    public function insert_user($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询系统考试表数据
     * @param int $examid 考试id
     */
    public function query_user($examid){
        if(isset($examid) && !empty($examid)){
            return $this->where('examid='.$examid)->find();
        }else{
            return $this->field('examid,EnName,CnName,ExamTime,EndDate,create_time,last_time,SortID')->select();
        }
    }
    
    /**
     * 修改系统考试信息
     * @param array $data 所修改的考试信息，必须包含主键
     */
    public function update_user($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除系统考试信息
     * @param int $examid 要删除的考试id
     */
    public function delete_user($examid){
        return $this->where('examid='.$examid)->delete();
    }
}