<?php
namespace Home\Model;
use Think\Model;

class SysClassModel extends Model{
    /**
     * 向考试表插入考试数据
     * @param array $data 所添加的考试信息
     */
	protected $trueTableName = 'HK_Class_Sys';
	
    public function insert_user($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询考试班级数据
     * @param int $subid 考试科目id
     */
    public function query_user($subid,$scid){
        if(isset($scid) && !empty($scid)){
            return $this->where('scid='.$scid)->find();
        }
		else{
			$TempStr = $this->join('LEFT JOIN HK_Subjects ON HK_Class_Sys.SubID = HK_Subjects.SubID');
			$TempStr = $TempStr->join('LEFT JOIN HK_Class_Type ON HK_Class_Sys.CTID = HK_Class_Type.CTID');
			$TempStr = $TempStr->field('scid,scname,subname,classtypename,statetf,price,classnum,year,listenid,HK_Class_Sys.create_time,HK_Class_Sys.last_time,HK_Class_Sys.sortid,HK_Class_Sys.examid,HK_Class_Sys.subid');
			return $TempStr->where('HK_Class_Sys.subid='.$subid)->select();
        }
    }
    public function query_examsub($subid){
        return $this->table('HK_Subjects')->join('LEFT JOIN HK_ExamClass ON HK_Subjects.ExamID = HK_ExamClass.ExamID')->field('CnName,SubName,ExamTime,EndDate')->where('subid='.$subid)->find();
    }
	public function query_classtype(){
        return $this->table('HK_Class_Type')->field('CTID,ClassTypeName')->order('SortID')->select();
    }
	public function query_teacher($examid){
        return $this->table('HK_Class_Teacher')->field('TID,TeachName')->where('concat(","+AllExam+",") like concat("%,"+'.$examid.'+",%")')->select();
    }
	public function query_province(){
        return $this->table('HK_Citys')->field('CityID,CityName')->where('ParentID=0 and level=1')->order('SortID')->select();
    }
	public function query_freelisten($scid){
        return $this->table('HK_Class_Resources')->field('LessonID,CnName')->where('scid='.$scid.' and FreeTF=1')->order('SortID')->select();
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
    public function delete_user($scid){
        return $this->where('scid='.$scid)->delete();
    }
}