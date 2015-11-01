<?php
namespace Agency\Model;
use Think\Model;

class JigouClassModel extends Model{
    /**
     * 添加机构班级表
     * @param array $data 所添加的班级信息
     */
    public function insert_class($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询机构班级表数据
     * @param int $jgcid 班级id,$examid考试id
     */
    public function query_class($jgcid,$examid){
        if(isset($jgcid) && !empty($jgcid)&&isset($examid) && !empty($examid)){
            return $this->field('t2.cnname as ecnname,t3.price as sys_price,hk_jigou_class.*')
                    ->where('hk_jigou_class.jgcid='.$jgcid.' and hk_jigou_class.jgid='.session('JGID').' and hk_jigou_class.examid='.$examid)
                    ->join('left join hk_examclass t2 ON t2.examid = hk_jigou_class.examid')
                    ->join('left join hk_class_sys t3 ON t3.scid = hk_jigou_class.scid')
                    ->find();
        }else if(isset($examid) && !empty($examid)){
            return $this->where('jgid='.session('JGID').' and examid='.$examid)->order('SortID')->select();
        }   
    }
    /**
     * 查询机构班级表数据
     * @param int $jgcid 机构班级id
     */
    public function query_details_class($jgcid){
        if(isset($jgcid) && !empty($jgcid)){
            return $this->where('jgcid='.$jgcid)
                  ->field('t1.cnname as ecnname,t2.validity,t3.teachname,t4.subname,t5.classtypename,hk_jigou_class.*')
                  ->join('left join hk_examclass t1 ON t1.examid = hk_jigou_class.examid')
                  ->join('left join hk_class_sys t2 ON t2.scid = hk_jigou_class.scid')
                  ->join('left join hk_class_teacher t3 ON t3.tid=t2.teacherid')                  
                  ->join('left join hk_subjects t4 ON t4.subid = t2.subid')
                  ->join('left join hk_class_type t5 ON t5.ctid = t2.ctid')
                  ->select();
        }else{
            echo '班级id不能为空！';
            exit();
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