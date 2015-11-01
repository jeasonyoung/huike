<?php
namespace Agency\Model;
use Think\Model;

class ExamclassModel extends Model{

    /**
     * 查询机构包含考试
     * @param int $allexams 包含考试id字符串(1,2,3)
     */
    public function query_exam_class($allexams){
        if( isset($allexams) && !empty($allexams)){
            return $this->where('examid in('.$allexams.')')->field('ExamID,CnName')->select();        
        }else{
            return $this->select();
        }
        
    }
     /**
     * 获取考试
     * @param int $examid 考试id
     */
    public function query_select_exam($examid){
         if(isset($examid) && !empty($examid)){
             return $this->where('examid='.$examid)->field('ExamID,CnName')->select();
         }else{
              return $this->field('ExamID,CnName')->select();
         }
    }
}