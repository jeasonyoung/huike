<?php
namespace Agency\Model;
use Think\Model;

class ClassSysModel extends Model{
    /**
     * 向机构班级表插入数据
     * @param array $data 所添加的班级信息
     */
    public function insert_Class($data=array()){
        return $this->add($data);
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
            return $this->where('statetf=1')->select();
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
    public function delete_class($jgcid = array()){
        $condition['jgcid']=array('in',$jgcid);
        return $result=$this->where($condition)->delete();
    }
}