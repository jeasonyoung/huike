<?php
namespace Agency\Model;
use Think\Model;

class ClassSysModel extends Model{
 
    /**
     * 查询考试下所有系统班级
     * @param int $examid 考试id
     */
    public function query_exam_sysclass($examid){
        if(isset($examid) && !empty($examid)){
            return $this->where('examid='.$examid.' and statetf=1')
                  ->field('SCID,SCName')->order('SortID')->select();
        }else{
            echo '考试id不能为空！';
            die();
        }
    }
    
    /**
     * 查询系统班级表数据
     * @param int $scid 系统班级id
     */
    public function query_sysclass($scid){
        if(isset($scid) && !empty($scid)){
            return $this->where('scid='.$scid.' and statetf=1')
                  ->field('t2.teachname,t3.cnname as ecnname,t4.subname,t5.classtypename,hk_class_sys.*')
                  ->join('left join hk_class_teacher t2 ON t2.tid=hk_class_sys.teacherid')
                  ->join('left join hk_examclass t3 ON t3.examid = hk_class_sys.examid')
                  ->join('left join hk_subjects t4 ON t4.subid = hk_class_sys.subid')
                  ->join('left join hk_class_type t5 ON t5.ctid = hk_class_sys.ctid')
                  ->select();
        }else{
            return $this->where('statetf=1')
                    ->field('t2.teachname,t3.cnname as ecnname,t4.subname,t5.classtypename,hk_class_sys.*')
                    ->join('left join hk_class_teacher t2 ON t2.tid=hk_class_sys.teacherid')
                    ->join('left join hk_examclass t3 ON t3.examid = hk_class_sys.examid')
                    ->join('left join hk_subjects t4 ON t4.subid = hk_class_sys.subid')
                    ->join('left join hk_class_type t5 ON t5.ctid = hk_class_sys.ctid')
                    ->order('SortID')->select();
        }
    }    
}