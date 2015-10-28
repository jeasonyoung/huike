<?php
namespace Home\Model;
use Think\Model;

class SubjectsModel extends Model{
    /**
     * 向考试表插入考试数据
     * @param array $data 所添加的考试信息
     */
    protected $trueTableName = 'HK_Subjects';
	
    public function insert_user($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询考试科目表数据
     * @param int $examid 考试id
     */
    public function query_user($examid){
        if(isset($examid) && !empty($examid)){
            return $this->where('examid='.$examid)->select();
        }
		else{
            return $this->table('HK_ExamClass')->field('examid,CnName')->order('SortID')->select();
        }
    }
    
    public function query_usersub($subid){
        if(isset($subid) && !empty($subid)){
            return $this->where('subid='.$subid)->find();
        }else{
            return null;
	}
    }
    
    public function get_examname($examid){
        return $this->table('HK_ExamClass')->field('CnName')->where('examid='.$examid)->find();
    }
    /**
     * 修改考试科目信息
     * @param array $data 所修改的考试信息，必须包含主键
     */
    public function update_user($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除考试科目信息
     * @param int $subid 要删除的考试科目id
     */
    public function delete_user($subid){
        return $this->where('subid='.$subid)->delete();
    }
}