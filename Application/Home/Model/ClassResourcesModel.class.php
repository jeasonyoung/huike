<?php
namespace Home\Model;
use Think\Model;

class ClassResourcesModel extends Model{
    /**
     * 向考试表插入考试数据
     * @param array $data 所添加的考试信息
     */
	protected $trueTableName = 'HK_Class_Resources';
	
    public function insert_user($data=array()){
        return $this->add($data);
    }
    
    /**
     * 查询考试班级列表数据
     * @param int $scid 班级列表id
     */
    public function query_user($scid,$yearid){
        if(isset($yearid) && !empty($yearid)){
            return $this->where('scid='.$scid.' and year='.$yearid)->select();
        }
		else{
            echo '未选择年份！';
			exit();
        }
    }
	//获取具体课时内容
	public function query_lessoninfo($lessonid){
        return $this->where('lessonid='.$lessonid)->find();
    }
	//获取班级名称
	public function query_scname($scid){
        return $this->table('HK_Class_Sys')->field('scname,examid,subid')->where('scid='.$scid)->find();
    }
	public function query_year($scid){
        return $this->Distinct(true)->field('year')->where('scid='.$scid)->order('year')->select();
    }
    public function get_defclassnum($scid,$yearid){
        if(isset($yearid) && !empty($yearid)){
            return $this->field('sortid')->where('scid='.$scid.' and year='.$yearid)->order('sortid desc')->limit(1)->find();
        }
		else{
			echo '未选择年份！';
			exit();
		}
    }
    public function get_examclassname($scid){
		return $this->table('HK_Class_Sys')
					->join('LEFT JOIN HK_ExamClass ON HK_Class_Sys.ExamID = HK_ExamClass.ExamID')
					->field('CnName,EnName,SCName,HK_Class_Sys.subid,HK_Class_Sys.examid')
					->where('scid='.$scid)->find();
	}
    /**
     * 修改考试科目信息
     * @param array $data 所修改的考试信息，必须包含主键
     */
    public function update_user($data = array()){
        return $this->save($data);
    }
    
    /**
     * 删除班级课时信息
     * @param int $lessonid 要删除的班级课时id
     */
    public function delete_user($lessonid){
        return $this->where('lessonid='.$lessonid)->delete();
    }
}